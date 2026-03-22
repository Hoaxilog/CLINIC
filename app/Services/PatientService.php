<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PatientService
{
    /**
     * Insert a new patient row and return the new ID.
     */
    public function create(array $data, string $modifier): int
    {
        $data['modified_by'] = $modifier;

        return DB::table('patients')->insertGetId($data);
    }

    /**
     * Update an existing patient row and log the change.
     */
    public function update(int $patientId, array $data, string $modifier): void
    {
        $old = DB::table('patients')->where('id', $patientId)->first();

        $data['modified_by'] = $modifier;
        DB::table('patients')->where('id', $patientId)->update($data);

        if ($old) {
            $subject = new Patient;
            $subject->id = $patientId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->event('patient_updated')
                ->withProperties(['old' => (array) $old, 'attributes' => $data])
                ->log('Updated Patient');
        }
    }

    /**
     * Load a patient row plus their health history list for the form.
     * Returns ['basicInfo', 'healthHistoryList', 'latestHealthHistory', 'selectedHealthHistoryId', 'age']
     */
    public function loadForForm(int $patientId): array
    {
        $patient = DB::table('patients')->where('id', $patientId)->first();

        $healthHistoryList = DB::table('health_histories')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->select('id', 'created_at')
            ->get()
            ->map(fn ($item) => [
                'id'    => $item->id,
                'label' => Carbon::parse($item->created_at)->format('F j, Y'),
            ])->toArray();

        $latest = DB::table('health_histories')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'basicInfo'              => $patient ? (array) $patient : [],
            'healthHistoryList'      => $healthHistoryList,
            'latestHealthHistory'    => $latest ? (array) $latest : [],
            'selectedHealthHistoryId' => $latest ? $latest->id : '',
            'age'                    => $this->calculateAge($patient?->birth_date ?? null),
        ];
    }

    /**
     * Create a walk-in appointment for a newly registered patient.
     */
    public function createWalkInAppointment(int $patientId, string $modifier): ?int
    {
        $defaultService = DB::table('services')->first();
        if (! $defaultService) {
            return null;
        }

        $payload = [
            'patient_id'       => $patientId,
            'service_id'       => $defaultService->id,
            'appointment_date' => now(),
            'status'           => 'Waiting',
            'created_at'       => now(),
            'updated_at'       => now(),
            'modified_by'      => $modifier,
        ];

        if (Schema::hasColumn('appointments', 'booking_type')) {
            $payload['booking_type'] = 'walk_in';
        }

        return DB::table('appointments')->insertGetId($payload);
    }

    /**
     * Log the creation of a patient record.
     */
    public function logCreated(int $patientId, array $attributes): void
    {
        $subject = new Patient;
        $subject->id = $patientId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('patient_created')
            ->withProperties(['attributes' => $attributes])
            ->log('Created Patient');
    }

    /**
     * Log the creation of a walk-in appointment.
     */
    public function logWalkInAppointment(int $appointmentId, int $patientId, array $basicInfo): void
    {
        $subject = new Appointment;
        $subject->id = $appointmentId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('appointment_created')
            ->withProperties([
                'attributes' => [
                    'patient_id'   => $patientId,
                    'patient_name' => trim(
                        ($basicInfo['last_name'] ?? '').', '.
                        ($basicInfo['first_name'] ?? '').' '.
                        ($basicInfo['middle_name'] ?? '')
                    ),
                    'status' => 'Waiting',
                ],
            ])
            ->log('Created Walk-in Appointment');
    }

    private function calculateAge(?string $birthDate): ?int
    {
        if (empty($birthDate)) {
            return null;
        }
        try {
            return Carbon::parse($birthDate)->age;
        } catch (\Throwable) {
            return null;
        }
    }
}
