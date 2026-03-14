<?php

namespace Tests\Unit;

use App\Models\PatientFormDraft;
use App\Models\User;
use App\Support\PatientFormDraftService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientFormDraftServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_upserts_and_fetches_draft_by_context(): void
    {
        $user = User::factory()->create();
        $service = new PatientFormDraftService();

        $service->upsertDraft($user->id, 'create', 0, 2, [
            'mode' => 'create',
            'patientId' => 0,
            'currentStep' => 2,
            'updatedAt' => now()->toIso8601String(),
        ]);

        $service->upsertDraft($user->id, 'create', 0, 3, [
            'mode' => 'create',
            'patientId' => 0,
            'currentStep' => 3,
            'updatedAt' => now()->toIso8601String(),
        ]);

        $this->assertDatabaseCount('patient_form_drafts', 1);

        $draft = $service->getDraft($user->id, 'create', 0);
        $this->assertNotNull($draft);
        $this->assertSame(3, $draft->step);
    }

    public function test_it_discards_context_specific_draft(): void
    {
        $user = User::factory()->create();
        $service = new PatientFormDraftService();

        $service->upsertDraft($user->id, 'create', 0, 1, ['mode' => 'create', 'patientId' => 0, 'currentStep' => 1]);
        $service->upsertDraft($user->id, 'edit', 42, 4, ['mode' => 'edit', 'patientId' => 42, 'currentStep' => 4]);

        $deleted = $service->discardDraft($user->id, 'create', 0);
        $this->assertSame(1, $deleted);
        $this->assertNull($service->getDraft($user->id, 'create', 0));
        $this->assertNotNull($service->getDraft($user->id, 'edit', 42));
    }

    public function test_it_isolates_drafts_by_user_and_context(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $service = new PatientFormDraftService();

        $service->upsertDraft($userA->id, 'edit', 99, 3, ['mode' => 'edit', 'patientId' => 99, 'currentStep' => 3]);
        $service->upsertDraft($userB->id, 'edit', 99, 2, ['mode' => 'edit', 'patientId' => 99, 'currentStep' => 2]);

        $draftA = $service->getDraft($userA->id, 'edit', 99);
        $draftB = $service->getDraft($userB->id, 'edit', 99);

        $this->assertNotNull($draftA);
        $this->assertNotNull($draftB);
        $this->assertSame(3, $draftA->step);
        $this->assertSame(2, $draftB->step);
    }

    public function test_it_purges_expired_drafts(): void
    {
        $user = User::factory()->create();

        PatientFormDraft::query()->create([
            'user_id' => $user->id,
            'mode' => 'create',
            'patient_id' => 0,
            'step' => 1,
            'payload_json' => json_encode(['mode' => 'create', 'patientId' => 0]),
            'expires_at' => Carbon::now()->subDay(),
        ]);

        PatientFormDraft::query()->create([
            'user_id' => $user->id,
            'mode' => 'edit',
            'patient_id' => 10,
            'step' => 2,
            'payload_json' => json_encode(['mode' => 'edit', 'patientId' => 10]),
            'expires_at' => Carbon::now()->addDay(),
        ]);

        $deleted = (new PatientFormDraftService())->purgeExpired();
        $this->assertSame(1, $deleted);
        $this->assertDatabaseCount('patient_form_drafts', 1);
    }
}

