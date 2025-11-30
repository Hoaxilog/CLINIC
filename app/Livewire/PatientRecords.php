<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class PatientRecords extends Component
{
    use WithPagination;

    public $search = '';
    
    // Add this property to track the current sort order
    // Options: 'recent', 'oldest', 'a_z', 'z_a'
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
    }

    /**
     * NEW HELPER: Centralized query logic to avoid code duplication.
     * This ensures 'render' and 'fetchFirstPatient' always use the same filters.
     */
    protected function getPatientsQuery()
    {
        $query = DB::table('patients')
                    ->select('id', 'first_name', 'last_name', 'mobile_number', 'home_address');

        // 1. Apply Search
        if (!empty($this->search)) {
            $query->where(function($q) {
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
        // Use the centralized query so it respects Search AND Sort
        $firstPatient = $this->getPatientsQuery()->first();

        if ($firstPatient) {
            $this->selectPatient($firstPatient->id);
        } else {
            // If search result is empty, clear the selection
            $this->selectedPatient = null;
            $this->lastVisit = null;
        }
    }

    public function render()
    {
        return view('livewire.patient-records', [
            'patients' => $this->getPatientsQuery()->paginate(15)
        ]);
    }
}