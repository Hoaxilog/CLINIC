<?php

namespace App\Services;

use App\Models\Patient;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HealthHistoryService
{
    /**
     * Insert a new health history row and return the new ID.
     */
    public function create(int $patientId, array $data, string $modifier, CarbonInterface|string|null $timestamp = null): int
    {
        unset($data['selectedHistoryId'], $data['id']);
        $data = $this->normalizePayload($data);

        $time = $timestamp instanceof CarbonInterface ? $timestamp : ($timestamp ? Carbon::parse((string) $timestamp) : now());

        $data['patient_id']  = $patientId;
        $data['modified_by'] = $modifier;
        $data['created_at']  = $time;
        $data['updated_at']  = $time;

        $id = DB::table('health_histories')->insertGetId($data);

        $this->logCreated($patientId, $id, $data);

        return $id;
    }

    /**
     * Update an existing health history row and log the change.
     */
    public function update(int $historyId, int $patientId, array $data, string $modifier): void
    {
        $old = DB::table('health_histories')->where('id', $historyId)->first();

        unset($data['id'], $data['selectedHistoryId']);
        $data = $this->normalizePayload($data);
        $data['modified_by'] = $modifier;

        DB::table('health_histories')->where('id', $historyId)->update($data);

        if ($old) {
            $this->logUpdated($patientId, $historyId, (array) $old, $data);
        }
    }

    private function logCreated(int $patientId, int $historyId, array $attributes): void
    {
        $subject = new Patient;
        $subject->id = $patientId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('health_history_created')
            ->withProperties(['health_history_id' => $historyId, 'attributes' => $attributes])
            ->log('Created Health History');
    }

    private function logUpdated(int $patientId, int $historyId, array $old, array $new): void
    {
        $subject = new Patient;
        $subject->id = $patientId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('health_history_updated')
            ->withProperties(['health_history_id' => $historyId, 'old' => $old, 'attributes' => $new])
            ->log('Updated Health History');
    }

    private function normalizePayload(array $data): array
    {
        if (! array_key_exists('when_last_visit_q1', $data)) {
            return $data;
        }

        $value = $data['when_last_visit_q1'];

        if ($value instanceof CarbonInterface) {
            $data['when_last_visit_q1'] = $value->toDateString();

            return $data;
        }

        if ($value === null) {
            return $data;
        }

        if (! is_string($value)) {
            $data['when_last_visit_q1'] = null;

            return $data;
        }

        $value = trim($value);
        if ($value === '') {
            $data['when_last_visit_q1'] = null;

            return $data;
        }

        try {
            $data['when_last_visit_q1'] = Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            $data['when_last_visit_q1'] = null;
        }

        return $data;
    }
}
