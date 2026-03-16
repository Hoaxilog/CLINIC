<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Livewire\Attributes\On;
use App\Support\PatientFormDraftService;
use App\Models\Patient;
use App\Models\DentalChart;
use App\Models\TreatmentRecord;
use App\Models\Appointment;

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
    public $patientAge = null;
    public $dentalDataLoaded = false;
    public $pendingDentalDraft = null;
    public $hasPendingDentalDraft = false;
    public $consentAuthorizationAccepted = false;
    public $consentTruthfulnessAccepted = false;

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

        $this->currentStep = $startStep;
        if ($this->isAdmin && $this->currentStep >= 3) {
            $this->ensureDentalDataLoaded();
        }
        $this->showModal = true;
        $this->dispatch('patient-form-opened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetState();

        $this->dispatch('patient-form-closed');
    }

    private function resetState()
    {
        $this->reset(); 
        $this->currentStep = 1;
        $this->dentalDataLoaded = false;
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

        if (!$this->validateConsentForUpdate()) {
            return;
        }
        
        $this->isSaving = true;
        $this->triggerStepValidation($this->currentStep);
    }

    private function validateConsentForUpdate(): bool
    {
        // Consent/signature is required for patient record updates.
        if (!$this->isEditing) {
            return true;
        }

        $this->resetErrorBag([
            'consentAuthorizationAccepted',
            'consentTruthfulnessAccepted',
        ]);

        $isValid = true;

        if (!$this->consentAuthorizationAccepted) {
            $this->addError('consentAuthorizationAccepted', 'Please authorize processing of personal information under the Data Privacy Act of 2012.');
            $isValid = false;
        }

        if (!$this->consentTruthfulnessAccepted) {
            $this->addError('consentTruthfulnessAccepted', 'Please confirm that the information provided is true and correct.');
            $isValid = false;
        }

        return $isValid;
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
                $this->syncDataToSteps();
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
                $this->clearCurrentDraftAfterSuccessfulSave();
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
        $createdPatientId = null;
        $createdHealthHistoryId = null;
        $createdAppointmentId = null;

        DB::transaction(function () use (&$createdPatientId, &$createdHealthHistoryId, &$createdAppointmentId) {
            $createdPatientId = $this->addBasicInfo($this->basicInfoData);
            $this->newPatientId = $createdPatientId;

            if (!empty($this->healthHistoryData)) {
                $createdHealthHistoryId = $this->addHealthHistory($this->newPatientId, $this->healthHistoryData);
            }
            // Walk-In Logic (Waiting Room)
            $defaultService = DB::table('services')->first(); 
            if ($defaultService) {
                $appointmentPayload = [
                    'patient_id' => $this->newPatientId,
                    'service_id' => $defaultService->id,
                    'appointment_date' => now(),
                    'status' => 'Waiting',
                    'created_at' => now(),
                    'updated_at' => now(),
                    'modified_by' => $this->getModifier()
                ];

                if (Schema::hasColumn('appointments', 'booking_type')) {
                    $appointmentPayload['booking_type'] = 'walk_in';
                }

                $createdAppointmentId = DB::table('appointments')->insertGetId($appointmentPayload);
            }
        });

        if ($createdPatientId) {
            $patientSubject = new Patient();
            $patientSubject->id = $createdPatientId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($patientSubject)
                ->event('patient_created')
                ->withProperties([
                    'attributes' => $this->basicInfoData,
                ])
                ->log('Created Patient');
        }

        if ($createdHealthHistoryId) {
            $patientSubject = new Patient();
            $patientSubject->id = $createdPatientId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($patientSubject)
                ->event('health_history_created')
                ->withProperties([
                    'health_history_id' => $createdHealthHistoryId,
                    'attributes' => $this->healthHistoryData,
                ])
                ->log('Created Health History');
        }

        if ($createdAppointmentId) {
            $appointmentSubject = new Appointment();
            $appointmentSubject->id = $createdAppointmentId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($appointmentSubject)
                ->event('appointment_created')
                ->withProperties([
                    'attributes' => [
                        'patient_id' => $createdPatientId,
                        'patient_name' => trim(
                            ($this->basicInfoData['last_name'] ?? '') . ', ' .
                            ($this->basicInfoData['first_name'] ?? '') . ' ' .
                            ($this->basicInfoData['middle_name'] ?? '')
                        ),
                        'status' => 'Waiting',
                    ],
                ])
                ->log('Created Walk-in Appointment');
        }

        $this->dispatch('patient-added');
        $this->clearCurrentDraftAfterSuccessfulSave();
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
        return DB::table('health_histories')->insertGetId($data);
    }

    private function updateBasicInfo()
    {
        $oldPatient = DB::table('patients')->where('id', $this->newPatientId)->first();
        $this->basicInfoData['modified_by'] = $this->getModifier();
        DB::table('patients')->where('id', $this->newPatientId)->update($this->basicInfoData);

        if ($oldPatient) {
            $patientSubject = new Patient();
            $patientSubject->id = $this->newPatientId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($patientSubject)
                ->event('patient_updated')
                ->withProperties([
                    'old' => (array) $oldPatient,
                    'attributes' => $this->basicInfoData,
                ])
                ->log('Updated Patient');
        }

        $this->dispatch('patient-added');
        $this->isReadOnly = true; 
        $this->isSaving = false;
        $this->clearCurrentDraftAfterSuccessfulSave();
        session()->flash('info', 'Patient information updated.');
    }

    private function updateHealthHistory()
    {
        $this->healthHistoryData['modified_by'] = $this->getModifier();
        $selectedId = $this->healthHistoryData['selectedHistoryId'] ?? $this->selectedHealthHistoryId;
        unset($this->healthHistoryData['id']); 
        unset($this->healthHistoryData['selectedHistoryId']);

        if ($selectedId && is_numeric($selectedId) && $selectedId !== 'new') {
            $oldHistory = DB::table('health_histories')->where('id', $selectedId)->first();
            DB::table('health_histories')->where('id', $selectedId)->update($this->healthHistoryData);

            if ($oldHistory) {
                $patientSubject = new Patient();
                $patientSubject->id = $this->newPatientId;

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($patientSubject)
                    ->event('health_history_updated')
                    ->withProperties([
                        'health_history_id' => $selectedId,
                        'old' => (array) $oldHistory,
                        'attributes' => $this->healthHistoryData,
                    ])
                    ->log('Updated Health History');
            }

            session()->flash('success', 'Health history updated.');
        } else {
            $this->healthHistoryData['patient_id'] = $this->newPatientId;
            $this->healthHistoryData['created_at'] = now();
            $this->healthHistoryData['updated_at'] = now();
            $newHistoryId = DB::table('health_histories')->insertGetId($this->healthHistoryData);

            $patientSubject = new Patient();
            $patientSubject->id = $this->newPatientId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($patientSubject)
                ->event('health_history_created')
                ->withProperties([
                    'health_history_id' => $newHistoryId,
                    'attributes' => $this->healthHistoryData,
                ])
                ->log('Created Health History');

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
        $this->clearCurrentDraftAfterSuccessfulSave();
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
                $oldChart = DB::table('dental_charts')->where('id', $existingToday->id)->first();
                DB::table('dental_charts')->where('id', $existingToday->id)->update([
                    'chart_data' => json_encode($this->dentalChartData),
                    'modified_by' => $modifier,
                    'updated_at' => now()
                ]);
                $chartId = $existingToday->id;

                if ($oldChart) {
                    $chartSubject = new DentalChart();
                    $chartSubject->id = $chartId;

                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($chartSubject)
                        ->event('dental_chart_updated')
                        ->withProperties([
                            'old' => [
                                'chart_data' => $oldChart->chart_data,
                            ],
                            'attributes' => [
                                'chart_data' => json_encode($this->dentalChartData),
                            ],
                        ])
                        ->log('Updated Dental Chart');
                }
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

            $chartSubject = new DentalChart();
            $chartSubject->id = $chartId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($chartSubject)
                ->event('dental_chart_created')
                ->withProperties([
                    'attributes' => [
                        'patient_id' => $this->newPatientId,
                        'chart_data' => json_encode($this->dentalChartData),
                    ],
                ])
                ->log('Created Dental Chart');
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
            $existingRecord = DB::table('treatment_records')->where('dental_chart_id', $chartId)->first();
            $dataToUpdate = [
                'patient_id' => $this->newPatientId,
                'dmd' => $this->treatmentRecordData['dmd'] ?? null,
                'treatment' => $this->treatmentRecordData['treatment'] ?? null,
                'cost_of_treatment' => $this->treatmentRecordData['cost_of_treatment'] ?? null,
                'amount_charged' => $this->treatmentRecordData['amount_charged'] ?? null,
                'remarks' => $this->treatmentRecordData['remarks'] ?? null,
                'modified_by' => $this->getModifier(),
                'updated_at' => now(),
            ];

            DB::table('treatment_records')->updateOrInsert(
                ['dental_chart_id' => $chartId], 
                $dataToUpdate
            );

            $savedRecord = DB::table('treatment_records')->where('dental_chart_id', $chartId)->first();
            if ($savedRecord) {
                $recordSubject = new TreatmentRecord();
                $recordSubject->id = $savedRecord->id;
                $this->saveTreatmentRecordImages($savedRecord->id, $this->treatmentRecordData['image_payloads'] ?? []);

                if ($existingRecord) {
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($recordSubject)
                        ->event('treatment_record_updated')
                        ->withProperties([
                            'old' => (array) $existingRecord,
                            'attributes' => $dataToUpdate,
                        ])
                        ->log('Updated Treatment Record');
                } else {
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($recordSubject)
                        ->event('treatment_record_created')
                        ->withProperties([
                            'attributes' => $dataToUpdate,
                        ])
                        ->log('Created Treatment Record');
                }
            }
        }
        
        if ($this->currentDentalChartId) {
            $this->loadTreatmentRecordForChart($this->currentDentalChartId);
        }
    }

    private function saveTreatmentRecordImages($recordId, $imagePayloads)
    {
        if (empty($imagePayloads)) {
            return;
        }

        $currentMax = DB::table('treatment_record_images')
            ->where('treatment_record_id', $recordId)
            ->max('sort_order');

        $nextOrder = is_null($currentMax) ? 0 : ($currentMax + 1);
        $now = now();
        $rows = [];

        foreach ($imagePayloads as $index => $payload) {
            $path = $payload['path'] ?? null;
            if (empty($path)) {
                continue;
            }
            $rows[] = [
                'treatment_record_id' => $recordId,
                'image_path' => $path,
                'image_type' => $payload['type'] ?? 'other',
                'sort_order' => $nextOrder + $index,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('treatment_record_images')->insert($rows);
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
        $this->patientAge = $this->calculateAge($this->basicInfoData['birth_date'] ?? null);

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

        if ($record) {
            $images = DB::table('treatment_record_images')
                ->where('treatment_record_id', $record->id)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($img) => (array) $img)
                ->toArray();
            $this->treatmentRecordData['image_list'] = $images;
        }
    }

    private function syncDataToSteps()
    {
        if ($this->currentStep == 2) {
            $this->dispatch('setHealthHistoryContext', 
                gender: $this->basicInfoData['gender'] ?? null,
                historyList: $this->healthHistoryList,
                selectedId: $this->selectedHealthHistoryId
            )->to('PatientFormController.health-history');
            return;
        }

        if ($this->isAdmin && $this->isEditing && $this->currentStep >= 3) {
            $this->ensureDentalDataLoaded();
            if ($this->hasPendingDentalDraft && is_array($this->pendingDentalDraft)) {
                $this->dentalChartData = $this->pendingDentalDraft;
                $this->pendingDentalDraft = null;
                $this->hasPendingDentalDraft = false;
                $this->chartKey = uniqid();
            }
        }
    }

    private function ensureDentalDataLoaded(): void
    {
        if ($this->dentalDataLoaded || !$this->isAdmin || !$this->isEditing || !$this->newPatientId) {
            return;
        }

        $this->loadDentalChartHistory($this->newPatientId);
        $this->loadLatestDentalChart($this->newPatientId);
        $this->dentalDataLoaded = true;
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

    public function updatedConsentAuthorizationAccepted(): void
    {
        $this->resetErrorBag('consentAuthorizationAccepted');
    }

    public function updatedConsentTruthfulnessAccepted(): void
    {
        $this->resetErrorBag('consentTruthfulnessAccepted');
    }
    
    public function cancelEdit()
    {
        $this->clearCurrentDraftAfterSuccessfulSave();

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
                $this->ensureDentalDataLoaded();
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

    public function fetchServerDraft($mode, $patientId = 0)
    {
        $userId = Auth::id();
        if (!$userId) {
            return null;
        }

        $safeMode = $mode === 'edit' ? 'edit' : 'create';
        $safePatientId = $safeMode === 'edit' ? (int) $patientId : 0;

        $draft = app(PatientFormDraftService::class)->getDraft($userId, $safeMode, $safePatientId);
        if (!$draft) {
            return null;
        }

        $payload = json_decode($draft->payload_json, true);
        if (!is_array($payload)) {
            return null;
        }

        return [
            'mode' => $draft->mode,
            'patientId' => (int) $draft->patient_id,
            'step' => (int) $draft->step,
            'payload' => $payload,
            'updatedAt' => optional($draft->updated_at)->toIso8601String(),
        ];
    }

    public function saveDraftFromClient($payload)
    {
        $userId = Auth::id();
        if (!$userId) {
            return ['ok' => false, 'message' => 'Unauthorized'];
        }

        $normalized = $this->normalizeDraftPayload($payload);
        if (!$normalized) {
            return ['ok' => false, 'message' => 'Invalid draft payload'];
        }

        $encoded = json_encode($normalized['payload']);
        if ($encoded === false || strlen($encoded) > 300000) {
            return ['ok' => false, 'message' => 'Draft payload too large'];
        }

        $record = app(PatientFormDraftService::class)->upsertDraft(
            $userId,
            $normalized['mode'],
            $normalized['patientId'],
            $normalized['currentStep'],
            $normalized['payload']
        );

        return [
            'ok' => true,
            'updatedAt' => optional($record->updated_at)->toIso8601String(),
        ];
    }

    public function discardDraft($mode, $patientId = 0)
    {
        $userId = Auth::id();
        if (!$userId) {
            return ['ok' => false, 'deleted' => 0];
        }

        $safeMode = $mode === 'edit' ? 'edit' : 'create';
        $safePatientId = $safeMode === 'edit' ? (int) $patientId : 0;
        $deleted = app(PatientFormDraftService::class)->discardDraft($userId, $safeMode, $safePatientId);

        return ['ok' => true, 'deleted' => $deleted];
    }

    public function applyDraftPayload($payload): bool
    {
        $normalized = $this->normalizeDraftPayload($payload);
        if (!$normalized) {
            return false;
        }

        [$contextMode, $contextPatientId] = $this->resolveDraftContext();
        if ($normalized['mode'] !== $contextMode || (int) $normalized['patientId'] !== (int) $contextPatientId) {
            return false;
        }

        $safeMaxStep = $this->getMaxStep();
        $targetStep = max(1, min($safeMaxStep, (int) $normalized['currentStep']));
        $safePayload = $normalized['payload'];

        // Restored drafts should continue in editable mode.
        $this->isReadOnly = false;

        $this->basicInfoData = $safePayload['basicInfo'];
        $this->healthHistoryData = $safePayload['healthHistory'];
        $this->treatmentRecordData = $safePayload['treatmentRecord'];

        $restoredDental = $safePayload['dentalChart'] ?? [];
        if (!empty($restoredDental) && $this->isAdmin && $this->isEditing) {
            $this->pendingDentalDraft = [
                'teeth' => is_array($restoredDental['teeth'] ?? null) ? $restoredDental['teeth'] : [],
                'oral_exam' => is_array($restoredDental['oralExam'] ?? null) ? $restoredDental['oralExam'] : [],
                'comments' => is_array($restoredDental['chartComments'] ?? null) ? $restoredDental['chartComments'] : [],
                'meta' => [
                    'dentition_type' => ($restoredDental['dentitionType'] ?? 'adult') === 'child' ? 'child' : 'adult',
                    'numbering_system' => is_string($restoredDental['numberingSystem'] ?? null)
                        ? $restoredDental['numberingSystem']
                        : 'FDI',
                ],
            ];
            $this->hasPendingDentalDraft = true;
            // Make Step 3 render the chart form even with no existing history rows.
            $this->forceNewRecord = true;
        }

        $this->currentStep = $targetStep;
        $this->syncDataToSteps();
        $this->dispatch('fillBasicInfo', data: $this->basicInfoData)->to('PatientFormController.basic-info');
        $this->dispatch('fillHealthHistory', data: $this->healthHistoryData, gender: $this->basicInfoData['gender'] ?? null)
            ->to('PatientFormController.health-history');

        return true;
    }

    public function render()
    {
        return view('livewire.PatientFormViews.patient-form-modal');
    }

    private function calculateAge($birthDate): ?int
    {
        if (empty($birthDate)) {
            return null;
        }

        try {
            return Carbon::parse($birthDate)->age;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolveDraftContext(): array
    {
        $mode = $this->isEditing ? 'edit' : 'create';
        $patientId = $mode === 'edit' ? (int) ($this->newPatientId ?? 0) : 0;
        return [$mode, $patientId];
    }

    private function clearCurrentDraftAfterSuccessfulSave(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            return;
        }

        [$mode, $patientId] = $this->resolveDraftContext();
        app(PatientFormDraftService::class)->discardDraft($userId, $mode, $patientId);
        $this->dispatch('patient-form-draft-cleared', userId: $userId, mode: $mode, patientId: $patientId);
    }

    private function normalizeDraftPayload($payload): ?array
    {
        if (!is_array($payload)) {
            return null;
        }

        $allowedTopLevel = ['currentStep', 'basicInfo', 'healthHistory', 'dentalChart', 'treatmentRecord', 'updatedAt', 'mode', 'patientId'];
        foreach (array_keys($payload) as $key) {
            if (!in_array($key, $allowedTopLevel, true)) {
                return null;
            }
        }

        $mode = ($payload['mode'] ?? ($this->isEditing ? 'edit' : 'create')) === 'edit' ? 'edit' : 'create';
        $patientId = (int) ($payload['patientId'] ?? ($mode === 'edit' ? (int) ($this->newPatientId ?? 0) : 0));
        if ($mode !== 'edit') {
            $patientId = 0;
        }
        $currentStep = max(1, min(4, (int) ($payload['currentStep'] ?? 1)));

        $basicAllowed = [
            'last_name', 'first_name', 'middle_name', 'nickname', 'occupation', 'birth_date', 'gender', 'civil_status',
            'home_address', 'office_address', 'home_number', 'office_number', 'mobile_number', 'email_address', 'referral',
            'emergency_contact_name', 'emergency_contact_number', 'relationship', 'who_answering', 'relationship_to_patient',
            'father_name', 'father_number', 'mother_name', 'mother_number', 'guardian_name', 'guardian_number',
        ];
        $healthAllowed = [
            'when_last_visit_q1', 'what_last_visit_reason_q1', 'what_seeing_dentist_reason_q2',
            'is_clicking_jaw_q3a', 'is_pain_jaw_q3b', 'is_difficulty_opening_closing_q3c', 'is_locking_jaw_q3d',
            'is_clench_grind_q4', 'is_bad_experience_q5', 'is_nervous_q6', 'what_nervous_concern_q6',
            'is_condition_q1', 'what_condition_reason_q1', 'is_hospitalized_q2', 'what_hospitalized_reason_q2',
            'is_serious_illness_operation_q3', 'what_serious_illness_operation_reason_q3', 'is_taking_medications_q4',
            'what_medications_list_q4', 'is_allergic_medications_q5', 'what_allergies_list_q5',
            'is_allergic_latex_rubber_metals_q6', 'is_pregnant_q7', 'is_breast_feeding_q8',
        ];
        $treatmentAllowed = ['dmd', 'treatment', 'cost_of_treatment', 'amount_charged', 'remarks'];

        $basicInfo = $this->pickAllowedAssoc($payload['basicInfo'] ?? [], $basicAllowed);
        $healthHistory = $this->pickAllowedAssoc($payload['healthHistory'] ?? [], $healthAllowed);
        $treatmentRecord = $this->pickAllowedAssoc($payload['treatmentRecord'] ?? [], $treatmentAllowed);

        $rawDental = is_array($payload['dentalChart'] ?? null) ? $payload['dentalChart'] : [];
        $oralExam = $this->pickAllowedAssoc($rawDental['oralExam'] ?? [], [
            'oral_hygiene_status', 'gingiva', 'calcular_deposits', 'stains', 'complete_denture', 'partial_denture',
        ]);
        $chartComments = $this->pickAllowedAssoc($rawDental['chartComments'] ?? [], ['notes', 'treatment_plan']);
        $teeth = is_array($rawDental['teeth'] ?? null) ? $rawDental['teeth'] : [];

        $dentalChart = [
            'teeth' => $teeth,
            'oralExam' => $oralExam,
            'chartComments' => $chartComments,
            'dentitionType' => ($rawDental['dentitionType'] ?? 'adult') === 'child' ? 'child' : 'adult',
            'numberingSystem' => is_string($rawDental['numberingSystem'] ?? null) ? $rawDental['numberingSystem'] : 'FDI',
        ];

        $normalizedPayload = [
            'currentStep' => $currentStep,
            'basicInfo' => $basicInfo,
            'healthHistory' => $healthHistory,
            'dentalChart' => $dentalChart,
            'treatmentRecord' => $treatmentRecord,
            'updatedAt' => is_string($payload['updatedAt'] ?? null) ? $payload['updatedAt'] : now()->toIso8601String(),
            'mode' => $mode,
            'patientId' => $patientId,
        ];

        return [
            'mode' => $mode,
            'patientId' => $patientId,
            'currentStep' => $currentStep,
            'payload' => $normalizedPayload,
        ];
    }

    private function pickAllowedAssoc($input, array $allowed): array
    {
        if (!is_array($input)) {
            return [];
        }

        $output = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $input)) {
                $output[$key] = $input[$key];
            }
        }

        return $output;
    }
}   
