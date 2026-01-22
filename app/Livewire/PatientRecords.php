<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Facade;


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

    public function updatedSearch()
    {
        $this->resetPage();
        $this->fetchFirstPatient(); 
    }
    
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

        if (!empty($this->search)) {
            $query->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $this->search . '%');;
        }

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
        $patient = DB::table('patients')->where('id', $id)->first();

        if ($patient) {
            DB::table('patients')->where('id', $id)->delete();

            // Reset selection if the deleted patient was currently being viewed
            if ($this->selectedPatient && $this->selectedPatient->id == $id) {
                $this->selectedPatient = null;
                $this->lastVisit = null;
            }
            session()->flash('error', 'Patient deleted successfully.');
        }
    }

    public function render()
    {
        return view('livewire.patient-records', [
            'patients' => $this->getPatientsQuery()->paginate(10),
        ]);
    }
}