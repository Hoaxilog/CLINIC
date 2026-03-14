<?php

namespace App\Support;

use App\Models\PatientFormDraft;
use Carbon\Carbon;

class PatientFormDraftService
{
    public function upsertDraft(int $userId, string $mode, int $patientId, int $step, array $payload, int $ttlDays = 7): PatientFormDraft
    {
        $safeMode = $mode === 'edit' ? 'edit' : 'create';
        $safePatientId = $safeMode === 'edit' ? max(0, $patientId) : 0;
        $safeStep = max(1, min(4, $step));

        $record = PatientFormDraft::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'mode' => $safeMode,
                'patient_id' => $safePatientId,
            ],
            [
                'step' => $safeStep,
                'payload_json' => json_encode($payload),
                'expires_at' => Carbon::now()->addDays($ttlDays),
            ]
        );

        return $record;
    }

    public function getDraft(int $userId, string $mode, int $patientId): ?PatientFormDraft
    {
        $safeMode = $mode === 'edit' ? 'edit' : 'create';
        $safePatientId = $safeMode === 'edit' ? max(0, $patientId) : 0;

        return PatientFormDraft::query()
            ->where('user_id', $userId)
            ->where('mode', $safeMode)
            ->where('patient_id', $safePatientId)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    public function discardDraft(int $userId, string $mode, int $patientId): int
    {
        $safeMode = $mode === 'edit' ? 'edit' : 'create';
        $safePatientId = $safeMode === 'edit' ? max(0, $patientId) : 0;

        return PatientFormDraft::query()
            ->where('user_id', $userId)
            ->where('mode', $safeMode)
            ->where('patient_id', $safePatientId)
            ->delete();
    }

    public function purgeExpired(): int
    {
        return PatientFormDraft::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->delete();
    }
}

