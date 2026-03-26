<?php

namespace Tests\Feature;

use App\Livewire\Patient\Form\PatientFormModal;
use App\Models\User;
use App\Services\HealthHistoryService;
use App\Services\PatientService;
use App\Support\PatientFormDraftService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class PatientFormModalWorkflowTest extends TestCase
{
    public function test_basic_info_edit_blocks_navigation_to_other_sections_until_saved(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_STAFF,
        ]);
        $user->id = 101;

        $this->actingAs($user);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('currentStep', 1)
            ->set('isReadOnly', false)
            ->call('goToStep', 2)
            ->assertSet('currentStep', 1)
            ->assertSet('pendingNavigationStep', null)
            ->assertDispatched('flash-message')
            ->assertDispatched('patient-form-navigation-finished');
    }

    public function test_editable_draft_blocks_navigation_back_to_basic_information_until_saved(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_ADMIN,
        ]);
        $user->id = 107;

        $this->actingAs($user);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('isReadOnly', false)
            ->set('forceNewRecord', true)
            ->set('currentStep', 3)
            ->call('goToStep', 1)
            ->assertSet('currentStep', 3)
            ->assertSet('pendingNavigationStep', null)
            ->assertDispatched('flash-message')
            ->assertDispatched('patient-form-navigation-finished');
    }

    public function test_staff_can_save_from_basic_info_step(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_STAFF,
        ]);
        $user->id = 102;

        $this->actingAs($user);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('isReadOnly', false)
            ->set('currentStep', 1)
            ->assertSee('Loading section...')
            ->assertSee('Save All');
    }

    public function test_admin_can_start_new_connected_record_from_treatment_step(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_ADMIN,
        ]);
        $user->id = 104;

        $this->actingAs($user);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('isAdmin', true)
            ->set('canViewClinicalRecords', true)
            ->set('isReadOnly', true)
            ->set('currentStep', 4)
            ->assertSee('New Record')
            ->call('startNewVisitRecord')
            ->assertSet('currentStep', 2)
            ->assertSet('isReadOnly', false)
            ->assertSet('selectedHealthHistoryId', 'new')
            ->assertSet('forceNewRecord', true)
            ->assertSet('selectedHistoryId', '');
    }

    public function test_new_visit_flow_can_progress_from_health_history_to_dental_and_treatment_steps(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_ADMIN,
        ]);
        $user->id = 106;

        $this->actingAs($user);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('isAdmin', true)
            ->set('canViewClinicalRecords', true)
            ->set('isReadOnly', false)
            ->set('forceNewRecord', true)
            ->set('currentStep', 2)
            ->call('goToStep', 3)
            ->assertSet('pendingNavigationStep', 3)
            ->assertDispatched('validateHealthHistory')
            ->dispatch('healthHistoryValidated', data: [
                'what_seeing_dentist_reason_q2' => 'Follow-up',
                'is_clicking_jaw_q3a' => 0,
                'is_pain_jaw_q3b' => 0,
                'is_difficulty_opening_closing_q3c' => 0,
                'is_locking_jaw_q3d' => 0,
                'is_clench_grind_q4' => 0,
                'is_bad_experience_q5' => 0,
                'is_nervous_q6' => 0,
                'is_condition_q1' => 0,
                'is_hospitalized_q2' => 0,
                'is_serious_illness_operation_q3' => 0,
                'is_taking_medications_q4' => 0,
                'is_allergic_medications_q5' => 0,
                'is_allergic_latex_rubber_metals_q6' => 0,
                'is_pregnant_q7' => 0,
                'is_breast_feeding_q8' => 0,
                'selectedHistoryId' => 'new',
            ])
            ->assertSet('currentStep', 3)
            ->call('goToStep', 4)
            ->assertSet('pendingNavigationStep', 4)
            ->assertDispatched('requestDentalChartData')
            ->dispatch('dentalChartDataProvided', data: [
                'teeth' => [],
                'oral_exam' => [
                    'oral_hygiene_status' => 'Good',
                    'gingiva' => 'Healthy',
                    'calcular_deposits' => 'None',
                    'stains' => 'None',
                    'complete_denture' => 'None',
                    'partial_denture' => 'None',
                ],
                'comments' => [
                    'notes' => '',
                    'treatment_plan' => '',
                ],
                'meta' => [
                    'dentition_type' => 'adult',
                    'numbering_system' => 'FDI',
                ],
            ])
            ->assertSet('currentStep', 4);
    }

    public function test_backward_navigation_does_not_require_validation(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_ADMIN,
        ]);
        $user->id = 105;

        $this->actingAs($user);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('isAdmin', true)
            ->set('canViewClinicalRecords', true)
            ->set('isReadOnly', true)
            ->set('currentStep', 4)
            ->call('goToStep', 3)
            ->assertSet('currentStep', 3)
            ->assertSet('pendingNavigationStep', null);
    }

    public function test_editable_backward_navigation_collects_treatment_without_validation(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_ADMIN,
        ]);
        $user->id = 108;

        $this->actingAs($user);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('isAdmin', true)
            ->set('canViewClinicalRecords', true)
            ->set('isReadOnly', false)
            ->set('forceNewRecord', true)
            ->set('currentStep', 4)
            ->call('goToStep', 3)
            ->assertSet('pendingNavigationStep', 3)
            ->assertDispatched('requestTreatmentRecordData')
            ->dispatch('treatmentRecordDataProvided', data: [
                'dmd' => 'Dr. Test',
                'treatment' => 'Oral Prophylaxis',
                'cost_of_treatment' => '1000',
                'amount_charged' => '1000',
                'remarks' => 'draft',
            ])
            ->assertSet('currentStep', 3)
            ->assertSet('pendingNavigationStep', null)
            ->assertSet('treatmentRecordData.dmd', 'Dr. Test');
    }

    public function test_editable_backward_navigation_collects_dental_without_validation(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_ADMIN,
        ]);
        $user->id = 109;

        $this->actingAs($user);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('isAdmin', true)
            ->set('canViewClinicalRecords', true)
            ->set('isReadOnly', false)
            ->set('forceNewRecord', true)
            ->set('currentStep', 3)
            ->call('goToStep', 2)
            ->assertSet('pendingNavigationStep', 2)
            ->assertDispatched('requestDentalChartDataWithoutValidation')
            ->dispatch('dentalChartDataProvided', data: [
                'teeth' => ['11' => ['surface' => 'O']],
                'oral_exam' => [
                    'oral_hygiene_status' => 'Good',
                    'gingiva' => 'Healthy',
                    'calcular_deposits' => 'None',
                    'stains' => 'None',
                    'complete_denture' => 'None',
                    'partial_denture' => 'None',
                ],
                'comments' => [
                    'notes' => 'chart note',
                    'treatment_plan' => 'plan',
                ],
                'meta' => [
                    'dentition_type' => 'adult',
                    'numbering_system' => 'FDI',
                ],
            ])
            ->assertSet('currentStep', 2)
            ->assertSet('pendingNavigationStep', null)
            ->assertSet('dentalChartData.comments.notes', 'chart note');
    }

    public function test_edit_save_persists_staged_basic_and_health_data_in_one_flow(): void
    {
        $user = User::factory()->make([
            'role' => User::ROLE_STAFF,
            'username' => 'staff.user',
        ]);
        $user->id = 103;

        $this->actingAs($user);

        $patientService = Mockery::mock(PatientService::class);
        $patientService->shouldReceive('update')
            ->once()
            ->with(55, Mockery::on(fn (array $data) => ($data['first_name'] ?? null) === 'Ana'), 'staff.user');
        $patientService->shouldReceive('loadForForm')
            ->twice()
            ->with(55)
            ->andReturn([
                'basicInfo' => ['first_name' => 'Ana', 'gender' => 'Female'],
                'healthHistoryList' => [['id' => 8, 'label' => 'March 25, 2026']],
                'latestHealthHistory' => ['what_seeing_dentist_reason_q2' => 'Tooth pain'],
                'selectedHealthHistoryId' => 8,
                'age' => 31,
            ]);
        app()->instance(PatientService::class, $patientService);

        $healthHistoryService = Mockery::mock(HealthHistoryService::class);
        $healthHistoryService->shouldReceive('create')
            ->once()
            ->with(55, Mockery::on(fn (array $data) => ($data['what_seeing_dentist_reason_q2'] ?? null) === 'Tooth pain'), 'staff.user')
            ->andReturn(8);
        app()->instance(HealthHistoryService::class, $healthHistoryService);

        $draftService = Mockery::mock(PatientFormDraftService::class);
        $draftService->shouldReceive('discardDraft')
            ->once()
            ->with($user->id, 'edit', 55)
            ->andReturn(1);
        app()->instance(PatientFormDraftService::class, $draftService);

        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('newPatientId', 55)
            ->set('currentStep', 2)
            ->set('isReadOnly', false)
            ->set('basicInfoData', [
                'first_name' => 'Ana',
                'gender' => 'Female',
            ])
            ->set('selectedHealthHistoryId', 'new')
            ->set('consentAuthorizationAccepted', true)
            ->set('consentTruthfulnessAccepted', true)
            ->call('save')
            ->assertDispatched('validateHealthHistory')
            ->dispatch('healthHistoryValidated', data: [
                'what_seeing_dentist_reason_q2' => 'Tooth pain',
                'is_clicking_jaw_q3a' => 0,
                'is_pain_jaw_q3b' => 1,
                'is_difficulty_opening_closing_q3c' => 0,
                'is_locking_jaw_q3d' => 0,
                'is_clench_grind_q4' => 0,
                'is_bad_experience_q5' => 0,
                'is_nervous_q6' => 0,
                'is_condition_q1' => 0,
                'is_hospitalized_q2' => 0,
                'is_serious_illness_operation_q3' => 0,
                'is_taking_medications_q4' => 0,
                'is_allergic_medications_q5' => 0,
                'is_allergic_latex_rubber_metals_q6' => 0,
                'is_pregnant_q7' => 0,
                'is_breast_feeding_q8' => 0,
                'selectedHistoryId' => 'new',
            ])
            ->assertSet('isReadOnly', true)
            ->assertSet('isSaving', false)
            ->assertSet('selectedHealthHistoryId', 8)
            ->assertDispatched('patient-added')
            ->assertDispatched('flash-message');
    }

    public function test_visit_history_list_groups_same_save_moment_into_one_global_record(): void
    {
        Schema::dropIfExists('health_histories');
        Schema::dropIfExists('dental_charts');

        Schema::create('health_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('dental_charts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->text('chart_data')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        $firstHealthId = DB::table('health_histories')->insertGetId([
            'patient_id' => 55,
            'created_at' => '2026-03-25 09:00:00',
        ]);

        $secondHealthId = DB::table('health_histories')->insertGetId([
            'patient_id' => 55,
            'created_at' => '2026-03-25 14:00:00',
        ]);

        $dentalId = DB::table('dental_charts')->insertGetId([
            'patient_id' => 55,
            'chart_data' => '{}',
            'created_at' => '2026-03-25 14:00:25',
        ]);

        $component = app(PatientFormModal::class);
        $method = new \ReflectionMethod($component, 'buildVisitHistoryList');
        $method->setAccessible(true);

        $history = $method->invoke($component, 55);

        $this->assertCount(2, $history);
        $this->assertSame('2026-03-25 14:00', $history[0]['value']);
        $this->assertSame('2026-03-25 09:00', $history[1]['value']);

        $this->assertNotSame($firstHealthId, $secondHealthId);
        $this->assertNotSame($dentalId, $secondHealthId);
    }

    public function test_selecting_past_record_dispatches_navigation_finished_event(): void
    {
        Livewire::test(PatientFormModal::class)
            ->set('showModal', true)
            ->set('isEditing', true)
            ->set('isReadOnly', true)
            ->set('newPatientId', 55)
            ->set('currentStep', 2)
            ->call('selectVisitRecord', '2026-03-25 14:00')
            ->assertDispatched('patient-form-navigation-finished');
    }
}
