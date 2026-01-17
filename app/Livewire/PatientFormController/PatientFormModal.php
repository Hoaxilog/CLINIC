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

    #[On('openAddPatientModal')]
    public function openForCreate()
    {
        $this->resetState();
        $this->showModal = true;
        $this->chartKey = uniqid();
        $this->checkAdminRole();
    }

    #[On('editPatient')]
    public function openForEdit($id)
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

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetState();
    }

    private function resetState()
    {
        $this->reset(); 
        $this->currentStep = 1;
    }

    private function checkAdminRole()
    {
        $user = Auth::user();
        $this->isAdmin = ($user && $user->role === 1);
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
            3 => $this->dispatch('requestDentalChartData')->to('PatientFormController.dental-chart'), // Chart requests data instead of validating
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

    #[On('dentalChartDataProvided')]
    public function handleDentalChart($data)
    {
        $this->dentalChartData = $data;

        if ($this->isSaving) {
            if ($this->isEditing && $this->isAdmin) {
                $this->updateDentalChart(); 
                $this->dispatch('patient-added');
                $this->isReadOnly = true;
            }
        } else {
            $this->currentStep = 4;
        }
    }

    #[On('treatmentRecordValidated')]
    public function handleTreatmentRecord($data)
    {
        $this->treatmentRecordData = $data;

        if ($this->isSaving && $this->isEditing && $this->isAdmin) {
            $this->updateTreatmentRecord();
            $this->dispatch('patient-added');
            $this->isReadOnly = true;
        }
        $this->isSaving = false;
    }
    private function createFullPatientRecord()
    {
        DB::transaction(function () {
            $this->newPatientId = $this->addBasicInfo($this->basicInfoData);

            if (!empty($this->healthHistoryData)) {
                $this->addHealthHistory($this->newPatientId, $this->healthHistoryData);
            }
        });

        $this->dispatch('patient-added');
        $this->closeModal();
    }

    private function addBasicInfo($data)
    {
        $data['modified_by'] = $this->getModifier();
        $newId = DB::table('patients')->insertGetId($data);
        
        $subject = new \App\Models\Patient(); // Fake Model
        $subject->id = $newId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('patient_created') // Specific Event
            ->withProperties([
                'attributes' => $data
            ])
            ->log('Created New Patient Record'); // Specific Description
        // ========================

        return $newId;

    }

    private function addHealthHistory($patientId, $data)
    {
        $data['patient_id'] = $patientId;
        $data['modified_by'] = $this->getModifier();
        DB::table('health_histories')->insert($data);
    }

    private function updateBasicInfo()
    {
        $this->basicInfoData['modified_by'] = $this->getModifier();
        
        // 1. Fetch Old Data
        $oldDataObj = DB::table('patients')->where('id', $this->newPatientId)->first();
        $oldDataArray = (array) $oldDataObj; // Convert to array for easy comparison

        // 2. === SMART DIFF CHECK (The Fix) ===
        // We loop through the NEW data and check if it's different from the OLD data.
        $changedAttributes = [];
        $oldAttributes = [];

        foreach ($this->basicInfoData as $key => $newValue) {
            // Skip keys that shouldn't be logged (like timestamps or unmodified fields)
            if (in_array($key, ['updated_at', 'modified_by'])) continue;

            // Check if value actually changed
            if (array_key_exists($key, $oldDataArray) && $oldDataArray[$key] != $newValue) {
                $changedAttributes[$key] = $newValue;       // Save new value
                $oldAttributes[$key] = $oldDataArray[$key]; // Save old value
            }
        }

        // 3. Update the Database
        DB::table('patients')
            ->where('id', $this->newPatientId)
            ->update($this->basicInfoData);

        // 4. Log ONLY if something actually changed
        if (!empty($changedAttributes)) {
            $subject = new \App\Models\Patient();
            $subject->id = $this->newPatientId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->event('patient_updated')
                ->withProperties([
                    'old' => $oldAttributes,       // Only the fields that changed (e.g. "John")
                    'attributes' => $changedAttributes // Only the new values (e.g. "Jane")
                ])
                ->log('Updated Patient Demographics');
        }

        $this->dispatch('patient-added');
        $this->isReadOnly = true; 
        $this->isSaving = false;
    }

    private function updateHealthHistory()
    {
        $this->healthHistoryData['modified_by'] = $this->getModifier();
        unset($this->healthHistoryData['id']); 
        
        DB::table('health_histories')
            ->updateOrInsert(
                ['patient_id' => $this->newPatientId], 
                $this->healthHistoryData
            );

        $this->dispatch('patient-added');
        $this->isReadOnly = true; 
        $this->isSaving = false;
    }

    private function updateDentalChart()
    {
        if (empty($this->dentalChartData)) return null;

        $modifier = $this->getModifier();
        $chartId = null;
        $wasUpdated = false; // Flag to track if we touched the DB

        // 1. Logic to Find or Create Chart (Existing Code)
        if (!$this->forceNewRecord) {
            $existingToday = DB::table('dental_charts')
                ->where('patient_id', $this->newPatientId)
                ->whereDate('created_at', Carbon::today())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingToday) {
                // Check if it actually changed to avoid spam
                if ($existingToday->chart_data !== json_encode($this->dentalChartData)) {
                    DB::table('dental_charts')->where('id', $existingToday->id)->update([
                        'chart_data' => json_encode($this->dentalChartData),
                        'modified_by' => $modifier,
                        'updated_at' => now()
                    ]);
                    $wasUpdated = true;
                }
                $chartId = $existingToday->id;
            }
        }

        if (!$chartId) {
            $chartId = DB::table('dental_charts')->insertGetId([
                'patient_id' => $this->newPatientId,
                'chart_data' => json_encode($this->dentalChartData),
                'modified_by' => $modifier,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $wasUpdated = true;
            $this->forceNewRecord = false;
        }

        // 2. === LOGGING BLOCK ===
        if ($wasUpdated) {
            $subject = new \App\Models\DentalChart();
            $subject->id = $chartId;

            // Note: We use the Patient as the 'Subject' usually, but here we link the specific Chart 
            // OR we can link the Patient so it shows on their timeline. Let's link the Patient.
            $patientSubject = new \App\Models\Patient();
            $patientSubject->id = $this->newPatientId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($patientSubject)
                ->event('dental_chart_updated')
                ->withProperties([
                    'attributes' => ['Dental Chart' => 'Visual Chart Updated'] 
                    // We hardcode this message to keep the log clean
                ])
                ->log('Updated Dental Chart');
        }
        // ========================

        $this->currentDentalChartId = $chartId;
        $this->loadDentalChartHistory($this->newPatientId); 
        return $chartId;
    }

    private function updateTreatmentRecord()
    {
        $chartId = $this->updateDentalChart();

        if ($chartId && !empty($this->treatmentRecordData)) {
            DB::table('treatment_records')->updateOrInsert(
                ['dental_chart_id' => $chartId], 
                [
                    'patient_id' => $this->newPatientId,
                    'dmd' => $this->treatmentRecordData['dmd'] ?? null,
                    'treatment' => $this->treatmentRecordData['treatment'] ?? null,
                    'cost_of_treatment' => $this->treatmentRecordData['cost_of_treatment'] ?? null,
                    'amount_charged' => $this->treatmentRecordData['amount_charged'] ?? null,
                    'remarks' => $this->treatmentRecordData['remarks'] ?? null,
                    'image' => $this->treatmentRecordData['image'] ?? null,
                    'modified_by' => $this->getModifier(),
                    'updated_at' => now(),
                ]
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

        $history = DB::table('health_histories')->where('patient_id', $id)->first();
        $this->healthHistoryData = $history ? (array) $history : [];
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
        $this->treatmentRecordData = $record ? $record : [];
    }

    private function syncDataToSteps()
    {
        // Pass gender to Step 2
        if ($this->currentStep == 2 && isset($this->basicInfoData['gender'])) {
            $this->dispatch('setGender', gender: $this->basicInfoData['gender'])
                 ->to('PatientFormController.health-history');
        }
    }

    private function getMaxStep()
    {
        if (!$this->isEditing) return 2;
        return $this->isAdmin ? 4 : 2;
    }

    public function enableEditMode()
    {
        $this->isReadOnly = false;
    }
    
    public function cancelEdit()
    {
        if ($this->isEditing && !$this->isReadOnly) {
            $this->isReadOnly = true;
            $this->forceNewRecord = false;
            $this->loadLatestDentalChart($this->newPatientId);
        } else {
            $this->closeModal();
        }
    }

    // Chart Events
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