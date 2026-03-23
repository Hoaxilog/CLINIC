<?php

namespace App\Livewire\Patient\Form;

use App\Services\DentalChartService;
use App\Services\HealthHistoryService;
use App\Support\PatientFormDraftService;
use App\Services\PatientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class PatientFormModal extends Component
{
    // ── UI State ─────────────────────────────────────────────────────────────
    public $showModal = false;
    public $currentStep = 1;
    public $isEditing = false;
    public $isAdmin = false;
    public $isReadOnly = false;
    public $isSaving = false;
    public $forceNewRecord = false;

    // ── Form Data ─────────────────────────────────────────────────────────────
    public $basicInfoData = [];
    public $healthHistoryData = [];
    public $dentalChartData = [];
    public $treatmentRecordData = [];

    // ── Patient / History Context ─────────────────────────────────────────────
    public $newPatientId;
    public $currentDentalChartId = null;
    public $selectedHistoryId = '';
    public $chartHistory = [];
    public $chartKey = 'initial';
    public $healthHistoryList = [];
    public $selectedHealthHistoryId = '';
    public $patientAge = null;

    // ── Dental Draft ──────────────────────────────────────────────────────────
    public $dentalDataLoaded = false;
    public $pendingDentalDraft = null;
    public $hasPendingDentalDraft = false;

    // ── Consent ───────────────────────────────────────────────────────────────
    public $consentAuthorizationAccepted = false;
    public $consentTruthfulnessAccepted = false;

    // ─────────────────────────────────────────────────────────────────────────
    // Open / Close
    // ─────────────────────────────────────────────────────────────────────────

    #[On('openAddPatientModal')]
    public function openForCreate(): void
    {
        $this->resetState();
        $this->showModal = true;
        $this->chartKey = uniqid();
        $this->checkAdminRole();
        $this->dispatch('patient-form-opened');
    }

    #[On('editPatient')]
    public function openForEdit($id, $startStep = 1): void
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

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetState();
        $this->dispatch('patient-form-closed');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step Navigation
    // ─────────────────────────────────────────────────────────────────────────

    public function nextStep(): void
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

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        // Free tab navigation is only allowed in edit/view mode.
        // Create mode stays sequential to enforce validation order.
        if (! $this->isEditing) {
            return;
        }

        $maxStep = $this->getMaxStep();
        if ($step >= 1 && $step <= $maxStep) {
            $this->currentStep = $step;
            $this->syncDataToSteps();
        }
    }


    public function save(): void
    {
        if ($this->isReadOnly) {
            return;
        }
        if (! $this->validateConsentForUpdate()) {
            return;
        }
        $this->isSaving = true;
        $this->triggerStepValidation($this->currentStep);
    }

    public function cancelEdit(): void
    {
        $this->clearCurrentDraftAfterSuccessfulSave();

        if ($this->isEditing && ! $this->isReadOnly) {
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

    // ─────────────────────────────────────────────────────────────────────────
    // Step Event Handlers  (#[On] listeners from child components)
    // ─────────────────────────────────────────────────────────────────────────

    #[On('basicInfoValidated')]
    public function handleBasicInfo($data): void
    {
        $this->basicInfoData = $data;

        if ($this->isSaving) {
            $this->isEditing ? $this->updateBasicInfo() : $this->createFullPatientRecord();
        } elseif (! $this->isEditing) {
            // Create mode: move to step 2 (health history) after basic info is filled
            $this->currentStep = 2;
        }
    }

    #[On('healthHistoryValidated')]
    public function handleHealthHistory($data): void
    {
        $this->healthHistoryData = $data;

        if ($this->isSaving && $this->isEditing) {
            // Save health history
            $hService = app(HealthHistoryService::class);
            $modifier = $this->modifier();
            $selectedId = $this->healthHistoryData['selectedHistoryId'] ?? $this->selectedHealthHistoryId;

            if ($selectedId && is_numeric($selectedId) && $selectedId !== 'new') {
                $hService->update((int) $selectedId, $this->newPatientId, $this->healthHistoryData, $modifier);
            } else {
                $hService->create($this->newPatientId, $this->healthHistoryData, $modifier);
            }

            $this->dispatch('patient-added');
            $this->isReadOnly = true;
            $this->isSaving   = false;
            $this->clearCurrentDraftAfterSuccessfulSave();
            session()->flash('info', 'Health records saved.');
        } elseif ($this->isSaving && ! $this->isEditing) {
            // Create flow: advance to step 3 if admin, otherwise done via createFullPatientRecord
            $this->currentStep = 3;
            $this->syncDataToSteps();
        }
    }

    #[On('dentalChartDataProvided')]
    public function handleDentalChart($data): void
    {
        $this->dentalChartData = $data;

        if ($this->isSaving) {
            $this->dispatch('validateTreatmentRecord')->to('patient.form.treatment-record');
        } else {
            $this->isSaving = false;
        }
    }

    #[On('treatmentRecordValidated')]
    public function handleTreatmentRecord($data): void
    {
        $this->treatmentRecordData = $data;

        if ($this->isSaving && $this->isEditing && $this->isAdmin) {
            $this->saveDentalAndTreatment();
            $this->dispatch('patient-added');
            $this->isReadOnly = true;
            $this->isSaving   = false;
            $this->clearCurrentDraftAfterSuccessfulSave();
            session()->flash('success', 'Dental chart and treatment record saved.');
        }

        $this->isSaving = false;
    }

    #[On('switchHealthHistory')]
    public function switchHealthHistory($historyId): void
    {
        if ($historyId === 'new') {
            $latest = DB::table('health_histories')
                ->where('patient_id', $this->newPatientId)
                ->orderBy('created_at', 'desc')
                ->first();
            $this->healthHistoryData = $latest ? (array) $latest : [];
            $this->selectedHealthHistoryId = 'new';
            $this->isReadOnly = false;
        } else {
            $record = DB::table('health_histories')->where('id', $historyId)->first();
            if ($record) {
                $this->healthHistoryData = (array) $record;
                $this->selectedHealthHistoryId = $historyId;
                $this->isReadOnly = true;
            }
        }

        $this->dispatch('fillHealthHistory',
            data: $this->healthHistoryData,
            gender: $this->basicInfoData['gender'] ?? null
        )->to('patient.form.health-history');
    }

    #[On('switchChartHistory')]
    public function switchChartHistory($chartId): void
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
            $this->dentalChartData = ! empty($chart->chart_data)
                ? json_decode($chart->chart_data, true)
                : [];
            $this->treatmentRecordData = app(DentalChartService::class)->getTreatmentRecord($chartId);
            $this->chartKey = uniqid();
        }
    }

    #[On('startNewChartSession')]
    public function startNewChartSession(): void
    {
        $this->isReadOnly = false;
        $this->forceNewRecord = true;
        $this->dentalChartData = [];
        $this->treatmentRecordData = [];
        $this->currentDentalChartId = null;
        $this->selectedHistoryId = '';
        $this->chartKey = uniqid();
    }

    #[On('enableEditMode')]
    public function enableEditMode(): void
    {
        $this->isReadOnly = false;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Consent
    // ─────────────────────────────────────────────────────────────────────────

    public function updatedConsentAuthorizationAccepted(): void
    {
        $this->resetErrorBag('consentAuthorizationAccepted');
    }

    public function updatedConsentTruthfulnessAccepted(): void
    {
        $this->resetErrorBag('consentTruthfulnessAccepted');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Draft Management
    // ─────────────────────────────────────────────────────────────────────────

    public function fetchServerDraft($mode, $patientId = 0)
    {
        $userId = Auth::id();
        if (! $userId) {
            return null;
        }

        $safeMode = $mode === 'edit' ? 'edit' : 'create';
        $safePatientId = $safeMode === 'edit' ? (int) $patientId : 0;

        $draft = app(PatientFormDraftService::class)->getDraft($userId, $safeMode, $safePatientId);
        if (! $draft) {
            return null;
        }

        $payload = json_decode($draft->payload_json, true);
        if (! is_array($payload)) {
            return null;
        }

        return [
            'mode'      => $draft->mode,
            'patientId' => (int) $draft->patient_id,
            'step'      => (int) $draft->step,
            'payload'   => $payload,
            'updatedAt' => optional($draft->updated_at)->toIso8601String(),
        ];
    }

    public function saveDraftFromClient($payload)
    {
        $userId = Auth::id();
        if (! $userId) {
            return ['ok' => false, 'message' => 'Unauthorized'];
        }

        $normalized = $this->normalizeDraftPayload($payload);
        if (! $normalized) {
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

        return ['ok' => true, 'updatedAt' => optional($record->updated_at)->toIso8601String()];
    }

    public function discardDraft($mode, $patientId = 0)
    {
        $userId = Auth::id();
        if (! $userId) {
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
        if (! $normalized) {
            return false;
        }

        [$contextMode, $contextPatientId] = $this->resolveDraftContext();
        if ($normalized['mode'] !== $contextMode || (int) $normalized['patientId'] !== (int) $contextPatientId) {
            return false;
        }

        $targetStep = max(1, min($this->getMaxStep(), (int) $normalized['currentStep']));
        $safePayload = $normalized['payload'];

        $this->isReadOnly = false;
        $this->basicInfoData = $safePayload['basicInfo'];
        $this->healthHistoryData = $safePayload['healthHistory'];
        $this->treatmentRecordData = $safePayload['treatmentRecord'];

        $restoredDental = $safePayload['dentalChart'] ?? [];
        if (! empty($restoredDental) && $this->isAdmin && $this->isEditing) {
            $this->pendingDentalDraft = [
                'teeth'     => is_array($restoredDental['teeth'] ?? null) ? $restoredDental['teeth'] : [],
                'oral_exam' => is_array($restoredDental['oralExam'] ?? null) ? $restoredDental['oralExam'] : [],
                'comments'  => is_array($restoredDental['chartComments'] ?? null) ? $restoredDental['chartComments'] : [],
                'meta'      => [
                    'dentition_type'   => ($restoredDental['dentitionType'] ?? 'adult') === 'child' ? 'child' : 'adult',
                    'numbering_system' => is_string($restoredDental['numberingSystem'] ?? null) ? $restoredDental['numberingSystem'] : 'FDI',
                ],
            ];
            $this->hasPendingDentalDraft = true;
            $this->forceNewRecord = true;
        }

        $this->currentStep = $targetStep;
        $this->syncDataToSteps();
        $this->dispatch('fillBasicInfo', data: $this->basicInfoData)->to('patient.form.basic-info');
        $this->dispatch('fillHealthHistory', data: $this->healthHistoryData, gender: $this->basicInfoData['gender'] ?? null)
            ->to('patient.form.health-history');

        return true;
    }

    public function render()
    {
        return view('livewire.patient.form.patient-form-modal');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private — DB Operations (use services)
    // ─────────────────────────────────────────────────────────────────────────

    private function createFullPatientRecord(): void
    {
        $patientService = app(PatientService::class);
        $hService       = app(HealthHistoryService::class);
        $modifier       = $this->modifier();

        $createdPatientId = null;
        $createdHealthId  = null;

        DB::transaction(function () use ($patientService, $hService, $modifier, &$createdPatientId, &$createdHealthId) {
            $createdPatientId  = $patientService->create($this->basicInfoData, $modifier);
            $this->newPatientId = $createdPatientId;

            if (! empty($this->healthHistoryData)) {
                $createdHealthId = $hService->create($createdPatientId, $this->healthHistoryData, $modifier);
            }
        });

        if ($createdPatientId) {
            $patientService->logCreated($createdPatientId, $this->basicInfoData);
        }

        $this->dispatch('patient-added');
        $this->clearCurrentDraftAfterSuccessfulSave();
        $this->closeModal();
        session()->flash('success', 'New patient record created successfully!');
    }

    private function updateBasicInfo(): void
    {
        app(PatientService::class)->update($this->newPatientId, $this->basicInfoData, $this->modifier());

        $this->dispatch('patient-added');
        $this->isReadOnly = true;
        $this->isSaving   = false;
        $this->clearCurrentDraftAfterSuccessfulSave();
        session()->flash('info', 'Patient information updated.');
    }

    private function saveHealthHistoryRecord(): void
    {
        $hService   = app(HealthHistoryService::class);
        $modifier   = $this->modifier();
        $selectedId = $this->healthHistoryData['selectedHistoryId'] ?? $this->selectedHealthHistoryId;

        if ($selectedId && is_numeric($selectedId) && $selectedId !== 'new') {
            $hService->update((int) $selectedId, $this->newPatientId, $this->healthHistoryData, $modifier);
        } else {
            $hService->create($this->newPatientId, $this->healthHistoryData, $modifier);
        }

        $this->loadPatientData($this->newPatientId);
        $this->dispatch('setHealthHistoryContext',
            gender: $this->basicInfoData['gender'] ?? null,
            historyList: $this->healthHistoryList,
            selectedId: $this->selectedHealthHistoryId
        )->to('patient.form.health-history');
    }

    private function completeSave(string $message = 'Changes saved.'): void
    {
        $this->dispatch('patient-added');
        $this->isReadOnly = true;
        $this->isSaving   = false;
        $this->clearCurrentDraftAfterSuccessfulSave();
        session()->flash('success', $message);
    }

    private function saveDentalAndTreatment(): void
    {
        $dcService = app(DentalChartService::class);
        $modifier  = $this->modifier();

        $chartId = $dcService->save($this->newPatientId, $this->dentalChartData, $modifier, $this->forceNewRecord);
        $this->forceNewRecord = false;

        if ($chartId) {
            $this->currentDentalChartId = $chartId;
            $dcService->saveTreatmentRecord($chartId, $this->newPatientId, $this->treatmentRecordData, $modifier);
            $this->chartHistory       = $dcService->getHistory($this->newPatientId);
            $this->treatmentRecordData = $dcService->getTreatmentRecord($chartId);
        }
    }

    private function loadPatientData(int $id): void
    {
        $result = app(PatientService::class)->loadForForm($id);

        $this->basicInfoData            = $result['basicInfo'];
        $this->healthHistoryList        = $result['healthHistoryList'];
        $this->healthHistoryData        = $result['latestHealthHistory'];
        $this->selectedHealthHistoryId  = $result['selectedHealthHistoryId'];
        $this->patientAge               = $result['age'];
    }

    private function loadLatestDentalChart(int $patientId): void
    {
        $result = app(DentalChartService::class)->getLatest($patientId);

        $this->dentalChartData        = $result['chartData'];
        $this->currentDentalChartId   = $result['chartId'];
        $this->treatmentRecordData    = $result['treatmentRecord'];
        $this->selectedHistoryId      = '';
        $this->chartKey               = uniqid();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private — Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function resetState(): void
    {
        $this->reset();
        $this->currentStep     = 1;
        $this->dentalDataLoaded = false;
        $this->healthHistoryList = [];
    }

    private function checkAdminRole(): void
    {
        $this->isAdmin = Auth::user()?->canAccessOperationalPages() ?? false;
    }

    private function modifier(): string
    {
        return Auth::check() ? (Auth::user()->username ?? 'USER') : 'SYSTEM';
    }

    private function getMaxStep(): int
    {
        if (! $this->isEditing) {
            // Create mode flows: Basic Info → Health History (→ Dental → Treatment for admin)
            return $this->isAdmin ? 4 : 2;
        }

        // Edit/view mode: 4 tabs for admin, 2 for staff
        return $this->isAdmin ? 4 : 2;
    }

    private function triggerStepValidation(int $step): void
    {
        match ($step) {
            1 => $this->dispatch('validateBasicInfo')->to('patient.form.basic-info'),
            2 => $this->dispatch('validateHealthHistory')->to('patient.form.health-history'),
            3 => $this->dispatch('requestDentalChartData')->to('patient.form.dental-chart'),
            4 => $this->dispatch('validateTreatmentRecord')->to('patient.form.treatment-record'),
            default => null,
        };
    }

    private function validateConsentForUpdate(): bool
    {
        if (! $this->isEditing) {
            return true;
        }

        $this->resetErrorBag(['consentAuthorizationAccepted', 'consentTruthfulnessAccepted']);
        $valid = true;

        if (! $this->consentAuthorizationAccepted) {
            $this->addError('consentAuthorizationAccepted', 'Please authorize processing of personal information under the Data Privacy Act of 2012.');
            $valid = false;
        }
        if (! $this->consentTruthfulnessAccepted) {
            $this->addError('consentTruthfulnessAccepted', 'Please confirm that the information provided is true and correct.');
            $valid = false;
        }

        return $valid;
    }

    private function syncDataToSteps(): void
    {
        if ($this->isEditing) {
            if ($this->currentStep == 2) {
                $this->dispatch('setHealthHistoryContext',
                    gender: $this->basicInfoData['gender'] ?? null,
                    historyList: $this->healthHistoryList,
                    selectedId: $this->selectedHealthHistoryId
                )->to('patient.form.health-history');
            }

            if (($this->currentStep == 3 || $this->currentStep == 4) && $this->isAdmin) {
                $this->ensureDentalDataLoaded();

                if ($this->hasPendingDentalDraft && is_array($this->pendingDentalDraft)) {
                    $this->dentalChartData      = $this->pendingDentalDraft;
                    $this->pendingDentalDraft   = null;
                    $this->hasPendingDentalDraft = false;
                    $this->chartKey = uniqid();
                }
            }
        }
    }

    private function ensureDentalDataLoaded(): void
    {
        if ($this->dentalDataLoaded || ! $this->isAdmin || ! $this->isEditing || ! $this->newPatientId) {
            return;
        }

        $dcService = app(DentalChartService::class);
        $this->chartHistory = $dcService->getHistory($this->newPatientId);
        $this->loadLatestDentalChart($this->newPatientId);
        $this->dentalDataLoaded = true;
    }

    private function resolveDraftContext(): array
    {
        $mode = $this->isEditing ? 'edit' : 'create';
        return [$mode, $mode === 'edit' ? (int) ($this->newPatientId ?? 0) : 0];
    }

    private function clearCurrentDraftAfterSuccessfulSave(): void
    {
        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        [$mode, $patientId] = $this->resolveDraftContext();
        app(PatientFormDraftService::class)->discardDraft($userId, $mode, $patientId);
        $this->dispatch('patient-form-draft-cleared', userId: $userId, mode: $mode, patientId: $patientId);
    }

    private function normalizeDraftPayload($payload): ?array
    {
        if (! is_array($payload)) {
            return null;
        }

        $allowedTopLevel = ['currentStep', 'basicInfo', 'healthHistory', 'dentalChart', 'treatmentRecord', 'updatedAt', 'mode', 'patientId'];
        foreach (array_keys($payload) as $key) {
            if (! in_array($key, $allowedTopLevel, true)) {
                return null;
            }
        }

        $mode      = ($payload['mode'] ?? ($this->isEditing ? 'edit' : 'create')) === 'edit' ? 'edit' : 'create';
        $patientId = $mode === 'edit' ? (int) ($payload['patientId'] ?? (int) ($this->newPatientId ?? 0)) : 0;
        $step      = max(1, min(4, (int) ($payload['currentStep'] ?? 1)));

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

        $rawDental    = is_array($payload['dentalChart'] ?? null) ? $payload['dentalChart'] : [];
        $oralExam     = $this->pickAllowed($rawDental['oralExam'] ?? [], ['oral_hygiene_status', 'gingiva', 'calcular_deposits', 'stains', 'complete_denture', 'partial_denture']);
        $chartComments = $this->pickAllowed($rawDental['chartComments'] ?? [], ['notes', 'treatment_plan']);
        $teeth        = is_array($rawDental['teeth'] ?? null) ? $rawDental['teeth'] : [];

        $normalizedPayload = [
            'currentStep'    => $step,
            'basicInfo'      => $this->pickAllowed($payload['basicInfo'] ?? [], $basicAllowed),
            'healthHistory'  => $this->pickAllowed($payload['healthHistory'] ?? [], $healthAllowed),
            'treatmentRecord' => $this->pickAllowed($payload['treatmentRecord'] ?? [], $treatmentAllowed),
            'dentalChart'    => [
                'teeth'           => $teeth,
                'oralExam'        => $oralExam,
                'chartComments'   => $chartComments,
                'dentitionType'   => ($rawDental['dentitionType'] ?? 'adult') === 'child' ? 'child' : 'adult',
                'numberingSystem' => is_string($rawDental['numberingSystem'] ?? null) ? $rawDental['numberingSystem'] : 'FDI',
            ],
            'updatedAt' => is_string($payload['updatedAt'] ?? null) ? $payload['updatedAt'] : now()->toIso8601String(),
            'mode'      => $mode,
            'patientId' => $patientId,
        ];

        return ['mode' => $mode, 'patientId' => $patientId, 'currentStep' => $step, 'payload' => $normalizedPayload];
    }

    private function pickAllowed($input, array $allowed): array
    {
        if (! is_array($input)) {
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
