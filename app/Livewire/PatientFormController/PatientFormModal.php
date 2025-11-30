<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // [ADDED] Import Auth facade
use Carbon\Carbon;
use Livewire\Attributes\On;

class PatientFormModal extends Component
{
    public $showModal = false;
    public $currentStep = 1;
    public $isEditing = false;
    public $isAdmin = false; // [ADDED] Property to store admin status

    public $basicInfoData = [];
    public $healthHistoryData = [];
    public $newPatientId;
    
    #[On('openAddPatientModal')]
    public function openModal()
    {
        $this->reset(); 
        $this->showModal = true;
        $this->isEditing = false;
        
        // [ADDED] Check if user is admin using the Auth facade
        $user = Auth::user();
        if ($user) {
            // REPLACE 'role' with your actual database column name (e.g., 'usertype', 'is_admin')
            // REPLACE 'admin' with the value that represents an admin
            $this->isAdmin = ($user->role === 1); 
        }
    }

    // --- UPDATED: Store data locally instead of dispatching ---
    #[On('editPatient')]
    public function editPatient($id)
    {
        $this->reset(); 
        $this->isEditing = true;
        $this->newPatientId = $id;

        // [ADDED] Re-check admin status when editing
        $user = Auth::user();
        if ($user) {
            $this->isAdmin = ($user->role === 'admin'); 
        }

        // 1. Fetch Basic Info & Store in Parent Property
        $patient = DB::table('patients')->where('id', $id)->first();
        $this->basicInfoData = (array) $patient;

        // 2. Fetch Health History & Store in Parent Property
        $history = DB::table('health_histories')->where('patient_id', $id)->first();
        $this->healthHistoryData = $history ? (array) $history : [];

        // 3. Open Modal
        $this->showModal = true;
        $this->currentStep = 1;
    }

    #[On('basicInfoValidated')]
    public function handleBasicInfoValidated($data)
    {
        $this->basicInfoData = $data;
        $this->currentStep = 2;
        
        $this->dispatch('setGender', gender: $this->basicInfoData['gender'])
             ->to('PatientFormController.health-history');
    }

    #[On('healthHistoryValidated')]
    public function handleHealthHistoryValidated($data)
    {
        $this->healthHistoryData = $data;
        
        // [MODIFIED] Logic Split: Admin goes to next step, Staff saves immediately
        if ($this->isAdmin) {
            // Admin Flow: Move to Step 3 (Dental Chart)
            $this->currentStep = 3;
        } else {
            // Staff Flow: Save/Update immediately
            if ($this->isEditing) {
                $this->updatePatientData();
            } else {
                $this->savePatientData();
            }
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
        // [ADDED] Step 2 logic for Admin
        elseif ($this->currentStep == 2 && $this->isAdmin) {
            $this->dispatch('validateHealthHistory')->to('PatientFormController.health-history');
        }
        // [ADDED] Step 3 logic (Dental Chart)
        elseif ($this->currentStep == 3) {
            // For now, we just move to step 4. 
            // If you need validation for the chart, you'd dispatch an event here.
            $this->currentStep = 4;
        }
    }
    
    public function previousStep()
    {
        $this->currentStep--;
    }

    public function save()
    {
        // [MODIFIED] Save logic based on step and role
        if ($this->currentStep == 2 && !$this->isAdmin) { 
            // Staff on Step 2: Validate and Save
            $this->dispatch('validateHealthHistory')->to('PatientFormController.health-history');
        } elseif ($this->currentStep >= 3) {
            // Admin finishing the form (Step 3 or 4)
            if ($this->isEditing) {
                $this->updatePatientData();
            } else {
                $this->savePatientData();
            }
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
                
                // Note: If you have dental chart data to save, add it here in the future
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