<?php

namespace Tests\Unit;

use App\Livewire\Patient\Form\PatientFormModal;
use App\Models\PatientFormDraft;
use App\Models\User;
use App\Support\PatientFormDraftService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class PatientFormModalDraftMethodsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('patient_form_drafts');
        Schema::create('patient_form_drafts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('patient_id')->default(0);
            $table->string('mode', 10);
            $table->unsignedTinyInteger('step')->default(1);
            $table->longText('payload_json');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_save_draft_from_client_persists_normalized_payload(): void
    {
        $user = User::factory()->make(['role' => User::ROLE_STAFF]);
        $user->id = 2001;
        $this->actingAs($user);

        $component = app(PatientFormModal::class);

        $response = $component->saveDraftFromClient([
            'currentStep' => 3,
            'basicInfo' => [
                'first_name' => 'Ana',
                'gender' => 'Female',
                'ignored_field' => 'drop-me',
            ],
            'healthHistory' => [
                'what_seeing_dentist_reason_q2' => 'Checkup',
            ],
            'dentalChart' => [
                'teeth' => ['11' => ['status' => 'filled']],
                'oralExam' => [
                    'gingiva' => 'Healthy',
                    'invalid' => 'drop-me',
                ],
                'chartComments' => [
                    'notes' => 'Draft note',
                ],
                'dentitionType' => 'child',
                'numberingSystem' => 'FDI',
            ],
            'treatmentRecord' => [
                'treatment' => 'Cleaning',
                'amount_charged' => '1500',
            ],
            'updatedAt' => now()->toIso8601String(),
            'mode' => 'create',
            'patientId' => 999,
        ]);

        $this->assertTrue($response['ok'] ?? false);

        $draft = PatientFormDraft::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($draft);
        $this->assertSame('create', $draft->mode);
        $this->assertSame(0, $draft->patient_id);
        $this->assertSame(3, $draft->step);

        $storedPayload = json_decode($draft->payload_json, true);
        $this->assertSame('Ana', $storedPayload['basicInfo']['first_name'] ?? null);
        $this->assertArrayNotHasKey('ignored_field', $storedPayload['basicInfo']);
        $this->assertSame('Healthy', $storedPayload['dentalChart']['oralExam']['gingiva'] ?? null);
        $this->assertArrayNotHasKey('invalid', $storedPayload['dentalChart']['oralExam']);
        $this->assertSame(0, $storedPayload['patientId'] ?? null);
    }

    public function test_fetch_server_draft_returns_saved_context_payload(): void
    {
        $user = User::factory()->make(['role' => User::ROLE_STAFF]);
        $user->id = 2002;
        $this->actingAs($user);

        app(PatientFormDraftService::class)->upsertDraft($user->id, 'edit', 55, 2, [
            'currentStep' => 2,
            'basicInfo' => ['first_name' => 'Luis'],
            'healthHistory' => ['what_seeing_dentist_reason_q2' => 'Pain'],
            'dentalChart' => [],
            'treatmentRecord' => [],
            'updatedAt' => now()->toIso8601String(),
            'mode' => 'edit',
            'patientId' => 55,
        ]);

        $component = app(PatientFormModal::class);
        $response = $component->fetchServerDraft('edit', 55);

        $this->assertIsArray($response);
        $this->assertSame('edit', $response['mode'] ?? null);
        $this->assertSame(55, $response['patientId'] ?? null);
        $this->assertSame(2, $response['step'] ?? null);
        $this->assertSame('Luis', $response['payload']['basicInfo']['first_name'] ?? null);
    }

    public function test_discard_draft_removes_only_requested_context(): void
    {
        $user = User::factory()->make(['role' => User::ROLE_STAFF]);
        $user->id = 2003;
        $this->actingAs($user);

        $service = app(PatientFormDraftService::class);
        $service->upsertDraft($user->id, 'create', 0, 1, [
            'mode' => 'create',
            'patientId' => 0,
            'currentStep' => 1,
        ]);
        $service->upsertDraft($user->id, 'edit', 55, 4, [
            'mode' => 'edit',
            'patientId' => 55,
            'currentStep' => 4,
        ]);

        $component = app(PatientFormModal::class);
        $response = $component->discardDraft('create', 0);

        $this->assertTrue($response['ok'] ?? false);
        $this->assertSame(1, $response['deleted'] ?? 0);
        $this->assertNull($service->getDraft($user->id, 'create', 0));
        $this->assertNotNull($service->getDraft($user->id, 'edit', 55));
    }

    public function test_apply_draft_payload_sets_edit_restore_to_new_record_state(): void
    {
        Livewire::test(PatientFormModal::class)
            ->set('isEditing', true)
            ->set('isAdmin', true)
            ->set('newPatientId', 55)
            ->set('selectedHealthHistoryId', '8')
            ->set('selectedVisitDate', '2026-03-25 14:00')
            ->call('applyDraftPayload', [
                'currentStep' => 4,
                'basicInfo' => ['first_name' => 'Ana'],
                'healthHistory' => ['what_seeing_dentist_reason_q2' => 'Follow-up'],
                'dentalChart' => [
                    'teeth' => ['11' => ['status' => 'filled']],
                    'oralExam' => ['gingiva' => 'Healthy'],
                    'chartComments' => ['notes' => 'Draft dental note'],
                    'dentitionType' => 'adult',
                    'numberingSystem' => 'FDI',
                ],
                'treatmentRecord' => ['treatment' => 'Cleaning'],
                'updatedAt' => now()->toIso8601String(),
                'mode' => 'edit',
                'patientId' => 55,
            ])
            ->assertSet('isReadOnly', false)
            ->assertSet('forceNewRecord', true)
            ->assertSet('selectedHealthHistoryId', 'new')
            ->assertSet('selectedVisitDate', '')
            ->assertSet('selectedHistoryId', '');
    }
}
