<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class PatientRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $sortOption = 'recent';
    public $selectedPatient;
    public $lastVisit;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->fetchFirstPatient();
    }

    public function selectPatient($patientId)
    {
        $this->selectedPatient = DB::table('patients')->where('id', $patientId)->first();

        $this->lastVisit = DB::table('appointments')
                            ->where('patient_id', $patientId)
                            ->where('status', 'Completed')
                            ->orderBy('appointment_date', 'desc')
                            ->first();
    }

    // MODIFIED: Changed to 'updatedSearch' so it runs AFTER the search text updates
    public function updatedSearch()
    {
        $this->resetPage();
        $this->fetchFirstPatient(); // Auto-select top result when searching
    }
    
    // MODIFIED: Auto-select top result when sorting changes
    public function setSort($option)
    {
        $this->sortOption = $option;
        $this->resetPage();
        $this->fetchFirstPatient(); 
        $this->dispatch('closeSortDropdown');

    }

    protected function getPatientsQuery()
    {
        $query = DB::table('patients');

        // 1. Apply Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Apply Sort
        switch ($this->sortOption) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'a_z':
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                break;
            case 'z_a':
                $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    /**
     * Helper to fetch the first patient for the initial view state
     */
    protected function fetchFirstPatient()
    {
        $patient = $this->getPatientsQuery()->first();
        
        if ($patient) {
            $this->selectPatient($patient->id);
        } else {
            $this->selectedPatient = null;
            $this->lastVisit = null;
        }
    }

    public function deletePatient($id)
    {
        // 1. Find the patient
        $patient = \Illuminate\Support\Facades\DB::table('patients')->where('id', $id)->first();

        if ($patient) {
            // 2. Delete the patient
            // Your DB schema is set to ON DELETE CASCADE, so this will automatically
            // delete linked appointments, health history, etc.
            \Illuminate\Support\Facades\DB::table('patients')->where('id', $id)->delete();

            // 3. Reset selection if the deleted patient was currently being viewed
            if ($this->selectedPatient && $this->selectedPatient->id == $id) {
                $this->selectedPatient = null;
                $this->lastVisit = null;
            }

            // 4. (Optional) Show a success message (if using a notification component)
            // session()->flash('message', 'Patient record deleted successfully.');
        }
    }

    public function render()
    {
        return view('livewire.patient-records', [
            'patients' => $this->getPatientsQuery()->paginate(10),
        ]);
    }
}