<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination; // <-- 1. IMPORT (uncommented)

class PatientRecords extends Component
{
    use WithPagination; // <-- 2. USE THE TRAIT

    /**
     * @var string
     * The search term bound to the input box.
     */
    public $search = '';

    /**
     * @var \stdClass|null
     * Holds the full details of the patient clicked on.
     */
    public $selectedPatient;

    /**
     * @var \stdClass|null
     * Holds the last appointment details for the selected patient.
     */
    public $lastVisit;

    /**
     * @var string
     * Tell Livewire to use Tailwind for pagination styling.
     */
    protected $paginationTheme = 'tailwind'; // <-- 3. ADD THIS FOR TAILWIND STYLING

    /**
     * mount() is Livewire's constructor.
     * It runs once when the component is first loaded.
     */
    public function mount()
    {
        // Load the very first patient by default so the right side isn't empty
        $firstPatient = DB::table('patients')
                            ->orderBy('last_name')
                            ->orderBy('first_name')
                            ->first();

        if ($firstPatient) {
            $this->selectPatient($firstPatient->id);
        }
    }

    /**
     * This is the key function. It's triggered when a user
     * clicks on a patient from the list.
     *
     * @param int $patientId
     */
    public function selectPatient($patientId)
    {
        // 1. Fetch the patient's main details
        $this->selectedPatient = DB::table('patients')
                                    ->where('id', $patientId)
                                    ->first();

        // 2. Fetch their last completed appointment
        $this->lastVisit = DB::table('appointments')
                            ->where('patient_id', $patientId)
                            ->where('status', 'Completed') // As per your schema
                            ->orderBy('appointment_date', 'desc')
                            ->first();
    }
                    
                        
    public function updatingSearch()
    {
        $this->resetPage(); // <-- 5. ADD THIS METHOD
    }

    /**
     * render() is called every time a public property (like $search) changes.
     * It re-fetches the patient list and re-renders the view.
     */
    public function render()
    {
        // Base query for the patient list
        $query = DB::table('patients')
                    ->select('id', 'first_name', 'last_name', 'mobile_number', 'home_address')
                    ->orderBy('last_name')
                    ->orderBy('first_name');

        // Apply the search filter if $search is not empty
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.patient-records', [
            'patients' => $query-> paginate(15)
        ]);
    }

}