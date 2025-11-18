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
    public $isEditing = false;

    public $basicInfoData = [];
    public $healthHistoryData = [];
    public $newPatientId;

    #[On('openAddPatientModal')]
    public function openModal()
    {
        $this->reset(); 
        $this->showModal = true;
        $this->isEditing = false;
        
        // We don't need to dispatch resetForm anymore because 
        // destroying the modal (showModal=false) resets the children automatically.
    }

    // --- UPDATED: Store data locally instead of dispatching ---
    #[On('editPatient')]
    public function editPatient($id)
    {
        $this->reset(); 
        $this->isEditing = true;
        $this->newPatientId = $id;

        // 1. Fetch Basic Info & Store in Parent Property
        $patient = DB::table('patients')->where('id', $id)->first();
        $this->basicInfoData = (array) $patient;

        // 2. Fetch Health History & Store in Parent Property
        $history = DB::table('health_histories')->where('patient_id', $id)->first();
        $this->healthHistoryData = $history ? (array) $history : [];

        // 3. Open Modal
        // When this becomes true, the child components render and read the data above
        $this->showModal = true;
        $this->currentStep = 1;
    }

    #[On('basicInfoValidated')]
    public function handleBasicInfoValidated($data)
    {
        $this->basicInfoData = $data;
        $this->currentStep = 2;
        
        // We still use dispatch here because the HealthHistory component ALREADY exists
        // and we need to update it dynamically while the modal is open.
        $this->dispatch('setGender', gender: $this->basicInfoData['gender'])
             ->to('PatientFormController.health-history');
    }

    #[On('healthHistoryValidated')]
    public function handleHealthHistoryValidated($data)
    {
        $this->healthHistoryData = $data;
        
        if ($this->isEditing) {
            $this->updatePatientData();
        } else {
            $this->savePatientData();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(); 
        $this->currentStep = 1;
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

    public function save()
    {
        if ($this->currentStep == 2) { 
            $this->dispatch('validateHealthHistory')->to('PatientFormController.health-history');
        }
    }

    public function savePatientData()
    {
        try {
            DB::transaction(function () {
                $this->basicInfoData['modified_by'] = 'SYSTEM';
                $this->newPatientId = DB::table('patients')->insertGetId($this->basicInfoData);
                
                $this->healthHistoryData['patient_id'] = $this->newPatientId;
                DB::table('health_histories')->insert($this->healthHistoryData);
            });

            $this->dispatch('patient-added');
            $this->closeModal();
        } catch (\Exception $e) {
            // handle error
        }
    }

    public function updatePatientData()
    {
        try {
            DB::transaction(function () {
                $this->basicInfoData['modified_by'] = 'SYSTEM';
                
                DB::table('patients')
                    ->where('id', $this->newPatientId)
                    ->update($this->basicInfoData);

                $this->healthHistoryData['patient_id'] = $this->newPatientId;
                
                DB::table('health_histories')->updateOrInsert(
                    ['patient_id' => $this->newPatientId],
                    $this->healthHistoryData
                );
            });

            $this->dispatch('patient-added');
            $this->closeModal();
        } catch (\Exception $e) {
            // handle error
        }
    }

    public function render()
    {
        return view('livewire.PatientFormViews.patient-form-modal');
    }
}