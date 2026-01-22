<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\On;

class PatientFormModal extends Component
{
    public $showModal = false;
    public $currentStep = 1;
    public $isEditing = false;
    public $isAdmin = false;
    public $isReadOnly = false;
    public $isSaving = false; 
    public $forceNewRecord = false; 

    public $basicInfoData = [];
    public $healthHistoryData = [];
    public $dentalChartData = [];
    public $treatmentRecordData = [];
    
    public $newPatientId;
    public $currentDentalChartId = null;
    public $selectedHistoryId = '';
    public $chartHistory = [];
    public $chartKey = 'initial'; 
    public $healthHistoryList = []; 
    public $selectedHealthHistoryId = '';

    #[On('openAddPatientModal')]
    public function openForCreate()
    {
        $this->resetState();
        $this->showModal = true;
        $this->chartKey = uniqid();
        $this->checkAdminRole();

        $this->dispatch('patient-form-opened');
    }

    #[On('editPatient')]
    public function openForEdit($id, $startStep = 1)
    {
        $this->resetState();
        $this->isEditing = true;
        $this->isReadOnly = true;
        $this->newPatientId = $id;
        $this->checkAdminRole();

        $this->loadPatientData($id);
        
        if ($this->isAdmin) {
            $this->loadDentalChartHistory($id);
            $this->loadLatestDentalChart($id);
        }

        $this->currentStep = $startStep;
        $this->showModal = true;
        $this->dispatch('patient-form-opened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetState();

        $this->dispatch('patient-form-opened');
    }

    private function resetState()
    {
        $this->reset(); 
        $this->currentStep = 1;
        // Ensure "Add Patient" button logic resets correctly
        $this->healthHistoryList = [];
    }

    private function checkAdminRole()
    {
        $user = Auth::user();
        // Allow Role 1 (Admin) AND Role 2 (Dentist) to save charts
        $this->isAdmin = ($user && in_array($user->role, [1, 2])); 
    }

    public function nextStep()
    {
        $this->isSaving = false;
        
        if ($this->isReadOnly) {
            if ($this->currentStep < $this->getMaxStep()) {
                $this->currentStep++;
                $this->syncDataToSteps();
            }
            return;
        }
        $this->triggerStepValidation($this->currentStep);
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function save()
    {
        if ($this->isReadOnly) return;
        
        $this->isSaving = true;
        $this->triggerStepValidation($this->currentStep);
    }

    private function triggerStepValidation($step)
    {
        match ($step) {
            1 => $this->dispatch('validateBasicInfo')->to('PatientFormController.basic-info'),
            2 => $this->dispatch('validateHealthHistory')->to('PatientFormController.health-history'),
            3 => $this->dispatch('requestDentalChartData')->to('PatientFormController.dental-chart'),
            4 => $this->dispatch('validateTreatmentRecord')->to('PatientFormController.treatment-record'),
        };
    }

    #[On('basicInfoValidated')]
    public function handleBasicInfo($data)
    {
        $this->basicInfoData = $data;

        if ($this->isSaving) {
            if ($this->isEditing) {
                $this->updateBasicInfo();
            } else {
                $this->createFullPatientRecord(); 
            }
        } else {
            $this->currentStep = 2;
            $this->syncDataToSteps();
        }
    }

    #[On('healthHistoryValidated')]
    public function handleHealthHistory($data)
    {
        $this->healthHistoryData = $data;

        if ($this->isSaving) {
            if ($this->isEditing) {
                $this->updateHealthHistory();
            } else {
                $this->createFullPatientRecord();
            }
        } else {
            if ($this->isAdmin && $this->isEditing && $this->currentStep < $this->getMaxStep()) {
                $this->currentStep = 3;
            }
        }
    }

    // === STEP 3 HANDLER: DO NOT SAVE, JUST MOVE TO STEP 4 ===
    #[On('dentalChartDataProvided')]
    public function handleDentalChart($data)
    {
        $this->dentalChartData = $data;
        
        // Force navigation to Step 4 so user can fill Treatment Record
        $this->currentStep = 4;
        
        // Reset saving flag because we haven't saved to DB yet
        $this->isSaving = false; 
    }

    // === STEP 4 HANDLER: SAVE BOTH CHART AND TREATMENT ===
    #[On('treatmentRecordValidated')]
    public function handleTreatmentRecord($data)
    {
        $this->treatmentRecordData = $data;

        if ($this->isSaving) {
            if ($this->isEditing && $this->isAdmin) {
                
                // 1. Unified Save Function
                $this->updateTreatmentRecord(); 
                
                $this->dispatch('patient-added');
                $this->isReadOnly = true;
                session()->flash('success', 'Dental chart & treatment record saved successfully.');
            } else {
                if (!$this->isEditing) session()->flash('error', 'Error: Must be in Edit Mode.');
                if (!$this->isAdmin) session()->flash('error', 'Access Denied: Permission missing.');
            }
        }
        $this->isSaving = false; // Always unlock the button
    }

    private function createFullPatientRecord()
    {
        DB::transaction(function () {
            $this->newPatientId = $this->addBasicInfo($this->basicInfoData);
            if (!empty($this->healthHistoryData)) {
                $this->addHealthHistory($this->newPatientId, $this->healthHistoryData);
            }
            // Walk-In Logic (Waiting Room)
            $defaultService = DB::table('services')->first(); 
            if ($defaultService) {
                DB::table('appointments')->insert([
                    'patient_id' => $this->newPatientId,
                    'service_id' => $defaultService->id,
                    'appointment_date' => now(),
                    'status' => 'Waiting',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'modified_by' => $this->getModifier()
                ]);
            }
        });

        $this->dispatch('patient-added');
        $this->closeModal();
        session()->flash('success', 'New patient record created successfully!');
    }

    private function addBasicInfo($data)
    {
        $data['modified_by'] = $this->getModifier();
        $newId = DB::table('patients')->insertGetId($data);
        return $newId;
    }

    private function addHealthHistory($patientId, $data)
    {
        if(isset($data['selectedHistoryId'])) unset($data['selectedHistoryId']);
        $data['patient_id'] = $patientId;
        $data['modified_by'] = $this->getModifier();
        DB::table('health_histories')->insert($data);
    }

    private function updateBasicInfo()
    {
        $this->basicInfoData['modified_by'] = $this->getModifier();
        DB::table('patients')->where('id', $this->newPatientId)->update($this->basicInfoData);
        $this->dispatch('patient-added');
        $this->isReadOnly = true; 
        $this->isSaving = false;
        session()->flash('info', 'Patient information updated.');
    }

    private function updateHealthHistory()
    {
        $this->healthHistoryData['modified_by'] = $this->getModifier();
        $selectedId = $this->healthHistoryData['selectedHistoryId'] ?? $this->selectedHealthHistoryId;
        unset($this->healthHistoryData['id']); 
        unset($this->healthHistoryData['selectedHistoryId']);

        if ($selectedId && is_numeric($selectedId) && $selectedId !== 'new') {
            DB::table('health_histories')->where('id', $selectedId)->update($this->healthHistoryData);
            session()->flash('success', 'Health history updated.');
        } else {
            $this->healthHistoryData['patient_id'] = $this->newPatientId;
            $this->healthHistoryData['created_at'] = now();
            $this->healthHistoryData['updated_at'] = now();
            DB::table('health_histories')->insert($this->healthHistoryData);
            session()->flash('success', 'New health history added.');
        }

        $this->loadPatientData($this->newPatientId);
        $this->dispatch('setHealthHistoryContext', 
            gender: $this->basicInfoData['gender'] ?? null,
            historyList: $this->healthHistoryList,
            selectedId: $this->selectedHealthHistoryId
        )->to('PatientFormController.health-history');
        
        $this->dispatch('patient-added');
        $this->isReadOnly = true; 
        $this->isSaving = false;
    }

    private function updateDentalChart()
    {
        if (empty($this->dentalChartData)) return null;

        $modifier = $this->getModifier();
        $chartId = null;

        // 1. Try to Update Existing (if not forced new)
        if (!$this->forceNewRecord) {
            $existingToday = DB::table('dental_charts')
                ->where('patient_id', $this->newPatientId)
                ->whereDate('created_at', Carbon::today())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingToday) {
                DB::table('dental_charts')->where('id', $existingToday->id)->update([
                    'chart_data' => json_encode($this->dentalChartData),
                    'modified_by' => $modifier,
                    'updated_at' => now()
                ]);
                $chartId = $existingToday->id;
            }
        }

        // 2. Create New (if forced or not found)
        if (!$chartId) {
            $chartId = DB::table('dental_charts')->insertGetId([
                'patient_id' => $this->newPatientId,
                'chart_data' => json_encode($this->dentalChartData),
                'modified_by' => $modifier,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->forceNewRecord = false; // Reset flag
        }

        $this->currentDentalChartId = $chartId;
        $this->loadDentalChartHistory($this->newPatientId); 
        return $chartId;
    }

    private function updateTreatmentRecord()
    {
        // 1. Save Chart First
        $chartId = $this->updateDentalChart();

        if ($chartId && !empty($this->treatmentRecordData)) {
            $dataToUpdate = [
                'patient_id' => $this->newPatientId,
                'dmd' => $this->treatmentRecordData['dmd'] ?? null,
                'treatment' => $this->treatmentRecordData['treatment'] ?? null,
                'cost_of_treatment' => $this->treatmentRecordData['cost_of_treatment'] ?? null,
                'amount_charged' => $this->treatmentRecordData['amount_charged'] ?? null,
                'remarks' => $this->treatmentRecordData['remarks'] ?? null,
                'image' => $this->treatmentRecordData['image'] ?? null,
                'modified_by' => $this->getModifier(),
                'updated_at' => now(),
            ];

            DB::table('treatment_records')->updateOrInsert(
                ['dental_chart_id' => $chartId], 
                $dataToUpdate
            );
        }
        
        if ($this->currentDentalChartId) {
            $this->loadTreatmentRecordForChart($this->currentDentalChartId);
        }
    }

    private function getModifier()
    {
        return Auth::check() ? (Auth::user()->username ?? 'USER') : 'SYSTEM';
    }

    private function loadPatientData($id)
    {
        $patient = DB::table('patients')->where('id', $id)->first();
        $this->basicInfoData = (array) $patient;

        $this->healthHistoryList = DB::table('health_histories')
            ->where('patient_id', $id)
            ->orderBy('created_at', 'desc')
            ->select('id', 'created_at')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'label' => Carbon::parse($item->created_at)->format('F j, Y')
            ])->toArray();

        $latest = DB::table('health_histories')
            ->where('patient_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();
            
        $this->healthHistoryData = $latest ? (array) $latest : [];
        $this->selectedHealthHistoryId = $latest ? $latest->id : '';
    }

    #[On('switchHealthHistory')]
    public function switchHealthHistory($historyId)
    {
        if ($historyId === 'new') {
            $latest = DB::table('health_histories')
                ->where('patient_id', $this->newPatientId)
                ->orderBy('created_at', 'desc')
                ->first();
            $this->healthHistoryData = $latest ? (array)$latest : [];
            $this->selectedHealthHistoryId = 'new';
            $this->isReadOnly = false; 
        } else {
            $record = DB::table('health_histories')->where('id', $historyId)->first();
            if ($record) {
                $this->healthHistoryData = (array)$record;
                $this->selectedHealthHistoryId = $historyId;
                $this->isReadOnly = true; 
            }
        }
        $this->dispatch('fillHealthHistory', 
            data: $this->healthHistoryData, 
            gender: $this->basicInfoData['gender'] ?? null
        )->to('PatientFormController.health-history');
    }

    private function loadDentalChartHistory($patientId)
    {
        $this->chartHistory = DB::table('dental_charts')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->select('id', 'created_at')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'date' => Carbon::parse($item->created_at)->format('F j, Y - h:i A')
            ])
            ->toArray();
    }

    private function loadLatestDentalChart($patientId)
    {
        $latestChart = DB::table('dental_charts')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestChart && !empty($latestChart->chart_data)) {
            $this->dentalChartData = json_decode($latestChart->chart_data, true);
            $this->currentDentalChartId = $latestChart->id;
            $this->loadTreatmentRecordForChart($latestChart->id);
        } else {
            $this->dentalChartData = [];
            $this->currentDentalChartId = null;
            $this->treatmentRecordData = [];
        }
        $this->selectedHistoryId = '';
        $this->chartKey = uniqid();
    }

    private function loadTreatmentRecordForChart($chartId)
    {
        $record = DB::table('treatment_records')->where('dental_chart_id', $chartId)->first();
        $this->treatmentRecordData = $record ? (array)$record : [];
    }

    private function syncDataToSteps()
    {
        if ($this->currentStep == 2) {
            $this->dispatch('setHealthHistoryContext', 
                gender: $this->basicInfoData['gender'] ?? null,
                historyList: $this->healthHistoryList,
                selectedId: $this->selectedHealthHistoryId
            )->to('PatientFormController.health-history');
        }
    }

    private function getMaxStep()
    {
        if (!$this->isEditing) return 2;
        return $this->isAdmin ? 4 : 2;
    }

    #[On('enableEditMode')]
    public function enableEditMode()
    {
        $this->isReadOnly = false;
    }
    
    public function cancelEdit()
    {
        if ($this->isEditing && !$this->isReadOnly) {
            $this->isReadOnly = true;
            $this->forceNewRecord = false;
            
            $this->loadPatientData($this->newPatientId);
            $this->dispatch('resetForm'); 
            $this->dispatch('fillBasicInfo', data: $this->basicInfoData);
            $this->dispatch('setHealthHistoryContext', 
                gender: $this->basicInfoData['gender'] ?? null,
                historyList: $this->healthHistoryList,
                selectedId: $this->selectedHealthHistoryId
            );
            $this->dispatch('fillHealthHistory', 
                data: $this->healthHistoryData, 
                gender: $this->basicInfoData['gender'] ?? null
            );
            if ($this->isAdmin) {
                $this->loadLatestDentalChart($this->newPatientId);
            }
        } else {
            $this->closeModal();
        }
    }

    #[On('switchChartHistory')]
    public function switchChartHistory($chartId)
    {
        if (empty($chartId)) {
            $this->loadLatestDentalChart($this->newPatientId);
            return;
        }

        $chart = DB::table('dental_charts')->where('id', $chartId)->first();
        if ($chart) {
            $this->isReadOnly = true; 
            $this->selectedHistoryId = $chartId;
            $this->currentDentalChartId = $chartId;
            $this->dentalChartData = !empty($chart->chart_data) ? json_decode($chart->chart_data, true) : [];
            $this->loadTreatmentRecordForChart($chartId);
            $this->chartKey = uniqid(); 
        }
    }

    #[On('startNewChartSession')]
    public function startNewChartSession()
    {
        $this->isReadOnly = false;
        $this->forceNewRecord = true;
        $this->dentalChartData = [];
        $this->treatmentRecordData = [];
        $this->currentDentalChartId = null;
        $this->selectedHistoryId = '';
        $this->chartKey = uniqid();
    }

    public function render()
    {
        return view('livewire.PatientFormViews.patient-form-modal');
    }
}   