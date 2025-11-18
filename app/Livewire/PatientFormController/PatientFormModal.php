<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\On;

class PatientFormModal extends Component
{
    public $showModal = false;
    public $currentStep = 1;
    public $basicInfoData = [];
    public $healthHistoryData = [];

    // This will hold the ID of the new patient after saving
    public $newPatientId;


    #[On('basicInfoValidated')]
    public function handleBasicInfoValidated($data)
    {
        $this->basicInfoData = $data;
        $this->currentStep = 2;
        
        $this->dispatch('setGender', gender: $this->basicInfoData['gender'])->to('PatientFormController.health-history');
    }

    
    #[On('healthHistoryValidated')]
    public function handleHealthHistoryValidated($data)
    {
        // Data came back from the child, store it
        $this->healthHistoryData = $data;
        
        // Now that we have all data, run the save
        $this->savePatientData();
    }


    #[On('openAddPatientModal')]
    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(); // Reset all properties
        $this->currentStep = 1;
    }

    public function save()
    {
        // MODIFIED: This is the correct logic for the parent 'save' button
        // It should tell the current step's component to validate and send its data
        if ($this->currentStep == 2) { 
            // MODIFIED: Added 'Controller' to the alias
            $this->dispatch('validateHealthHistory')->to('PatientFormController.health-history');
        }

        // The old save logic below is removed, as it's now handled by savePatientData()
    }

    public function nextStep()
    {
        if($this->currentStep == 1) {
            $this->dispatch('validateBasicInfo')->to('PatientFormController.basic-info');
        }

    }
    
    public function previousStep()
    {
        $this->currentStep--;
    }

    public function savePatientData()
    {
        try {
            DB::transaction(function () {
                
                // 1. Add the extra data that wasn't on the form
                $this->basicInfoData['modified_by'] = 'SYSTEM'; // You can change this

                // 2. Create the Patient and get the new ID by inserting the whole array
                $this->newPatientId = DB::table('patients')->insertGetId($this->basicInfoData);

                // 3. Add the new patient_id to the health history data
                $this->healthHistoryData['patient_id'] = $this->newPatientId;
                $this->healthHistoryData['modified_by'] = 'SYSTEM';


                // 4. Create the Health History record
                DB::table('health_histories')->insert($this->healthHistoryData);
            });

            // 5. If all good, dispatch event to refresh the patient list and close
            $this->dispatch('patient-added');
            $this->closeModal();

        } catch (\Exception $e) {
            // Handle error, e.g., show a notification
            // \Log::error('Error adding patient: ' . $e->getMessage());
            // You can add a toast notification here
        }
    }

    public function render()
    {
        return view('livewire.PatientFormViews.patient-form-modal');
    }
}