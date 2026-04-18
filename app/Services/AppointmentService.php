<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class AppointmentService
{
    // ─────────────────────────────────────────────────────────────────────────
    // Create appointment (staff-side calendar booking)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Create a staff-side scheduled appointment without creating a patient record yet.
     * Returns $appointmentId.
     */
    public function createScheduled(array $data): int
    {
        $modifier = $this->modifier();
        $normalizedBirthDate = $data['birth_date'];

        $appointmentDateTime = Carbon::parse($data['appointment_date'])
            ->setTimeFromTimeString($data['time'])
            ->toDateTimeString();

        $payload = [
            'patient_id'       => null,
            'service_id'       => $data['service_id'],
            'appointment_date' => $appointmentDateTime,
            'status'           => 'Scheduled',
            'modified_by'      => $modifier,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];

        if (Schema::hasColumn('appointments', 'booking_type')) {
            $payload['booking_type'] = 'walk_in';
        }

        // Keep an appointment-level identity snapshot so details still render
        // even if the patient link is later removed or corrected.
        if (Schema::hasColumn('appointments', 'requester_first_name')) {
            $payload['requester_first_name'] = $data['first_name'];
        }

        if (Schema::hasColumn('appointments', 'requester_last_name')) {
            $payload['requester_last_name'] = $data['last_name'];
        }

        if (Schema::hasColumn('appointments', 'requester_middle_name')) {
            $payload['requester_middle_name'] = $data['middle_name'] ?: null;
        }

        if (Schema::hasColumn('appointments', 'requester_contact_number')) {
            $payload['requester_contact_number'] = $data['contact_number'];
        }

        if (Schema::hasColumn('appointments', 'requester_birth_date')) {
            $payload['requester_birth_date'] = $normalizedBirthDate;
        }

        if (Schema::hasColumn('appointments', 'booking_for_other')) {
            $payload['booking_for_other'] = false;
        }

        $appointmentId = DB::table('appointments')->insertGetId($payload);

        $subject = new Appointment;
        $subject->id = $appointmentId;
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('appointment_created')
            ->withProperties(['attributes' => [
                'patient_id'       => null,
                'patient_name'     => trim("{$data['last_name']}, {$data['first_name']} {$data['middle_name']}"),
                'service_id'       => $data['service_id'],
                'appointment_date' => $appointmentDateTime,
                'status'           => 'Scheduled',
            ]])
            ->log('Created Appointment');

        return $appointmentId;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Status changes
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Update appointment status and send email. Returns false if blocked by safety check.
     */
    public function updateStatus(int $appointmentId, string $newStatus, ?array $safetyCheck = null): bool
    {
        $old = DB::table('appointments')->where('id', $appointmentId)->first();
        if (! $old) {
            return false;
        }

        DB::table('appointments')->where('id', $appointmentId)->update([
            'status'     => $newStatus,
            'updated_at' => now(),
        ]);

        if ($old->status !== $newStatus) {
            $subject = new Appointment;
            $subject->id = $appointmentId;

            $eventName = $newStatus === 'Cancelled' ? 'appointment_cancelled' : 'appointment_updated';

            activity()->causedBy(Auth::user())->performedOn($subject)
                ->event($eventName)
                ->withProperties(['old' => ['status' => $old->status], 'attributes' => ['status' => $newStatus]])
                ->log('Updated Appointment Status');

            if ($old->status === 'Pending' && $newStatus === 'Scheduled') {
                activity()->causedBy(Auth::user())->performedOn($subject)
                    ->event('appointment_request_approved')
                    ->withProperties(['attributes' => [
                        'patient_id'     => $old->patient_id,
                        'appointment_id' => $appointmentId,
                    ]])
                    ->log('Approved Appointment Request');

                activity()->causedBy(Auth::user())->performedOn($subject)
                    ->event('official_appointment_created')
                    ->withProperties(['attributes' => [
                        'appointment_id' => $appointmentId,
                        'patient_id'     => $old->patient_id,
                        'status'         => $newStatus,
                    ]])
                    ->log('Official Appointment Linked to Patient');
            }
        }

        $this->sendStatusEmail($appointmentId, $newStatus);

        return true;
    }

    /**
     * Admit a patient (Waiting → Ongoing). Dentist-only.
     * Returns ['ok' => bool, 'error' => ?string]
     */
    public function admit(int $appointmentId, int $serviceId, int $dentistId): array
    {
        $appointment = DB::table('appointments')->find($appointmentId);
        if (! $appointment) {
            return ['ok' => false, 'error' => 'Appointment not found.'];
        }

        if (empty($appointment->patient_id)) {
            return ['ok' => false, 'error' => 'Link or create a patient record before admitting this appointment.'];
        }

        $service = DB::table('services')->where('id', $serviceId)->first();
        if (! $service) {
            return ['ok' => false, 'error' => 'Service not found.'];
        }

        $startTime = Carbon::parse($appointment->appointment_date);
        $endTime   = $startTime->copy()->addMinutes(
            $this->durationToMinutes($service->duration)
        );

        $hasConflict = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $appointmentId)
            ->where('appointments.status', 'Ongoing')
            ->where('appointments.dentist_id', $dentistId)
            ->whereDate('appointment_date', $startTime->toDateString())
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('appointment_date', '<', $endTime)
                  ->whereRaw('DATE_ADD(appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND) > ?', [$startTime]);
            })->exists();

        if ($hasConflict) {
            return ['ok' => false, 'error' => 'Cannot admit this patient yet: you are still handling another patient in this time slot.'];
        }

        $updated = DB::table('appointments')
            ->where('id', $appointmentId)
            ->where('status', 'Waiting')
            ->update([
                'status'     => 'Ongoing',
                'service_id' => $serviceId,
                'dentist_id' => $dentistId,
                'updated_at' => now(),
            ]);

        if (! $updated) {
            return ['ok' => false, 'error' => 'This appointment was already admitted or updated. Please refresh.'];
        }

        $subject     = new Appointment;
        $subject->id = $appointmentId;
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('appointment_admitted')
            ->withProperties([
                'old'        => ['status' => $appointment->status, 'service_id' => $appointment->service_id, 'dentist_id' => $appointment->dentist_id],
                'attributes' => ['status' => 'Ongoing', 'service_id' => $serviceId, 'dentist_id' => $dentistId],
            ])
            ->log('Admitted Appointment');

        return ['ok' => true, 'patientId' => $appointment->patient_id, 'error' => null];
    }

    /**
     * Reschedule a pending appointment (staff-side).
     */
    public function reschedulePending(int $appointmentId, string $proposedDateTime, int $serviceId): void
    {
        DB::table('appointments')->where('id', $appointmentId)->update([
            'appointment_date' => $proposedDateTime,
            'service_id'       => $serviceId,
            'status'           => 'Scheduled',
            'updated_at'       => now(),
        ]);

        $subject     = new Appointment;
        $subject->id = $appointmentId;
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('appointment_rescheduled_and_approved')
            ->withProperties(['attributes' => [
                'appointment_id'   => $appointmentId,
                'appointment_date' => $proposedDateTime,
                'service_id'       => $serviceId,
                'status'           => 'Scheduled',
            ]])
            ->log('Rescheduled And Approved Appointment');
    }

    /**
     * Reschedule an already-Scheduled appointment by updating its date/time in place.
     */
    public function rescheduleScheduled(int $appointmentId, string $proposedDateTime, int $serviceId): void
    {
        $old = DB::table('appointments')->where('id', $appointmentId)->first();

        DB::table('appointments')->where('id', $appointmentId)->update([
            'appointment_date' => $proposedDateTime,
            'service_id'       => $serviceId,
            'updated_at'       => now(),
        ]);

        $subject     = new Appointment;
        $subject->id = $appointmentId;
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('appointment_rescheduled')
            ->withProperties(['old' => ['appointment_date' => $old?->appointment_date], 'attributes' => [
                'appointment_id'   => $appointmentId,
                'appointment_date' => $proposedDateTime,
                'service_id'       => $serviceId,
            ]])
            ->log('Rescheduled Appointment');
    }

    /**
     * Create a new Scheduled appointment from a cancelled one, preserving patient/requester data.
     * Returns the new appointment ID.
     */
    public function createRescheduledFromCancelled(int $cancelledAppointmentId, string $proposedDateTime, int $serviceId): int
    {
        $old = DB::table('appointments')->where('id', $cancelledAppointmentId)->first();

        $payload = [
            'patient_id'       => $old?->patient_id ?? null,
            'service_id'       => $serviceId,
            'appointment_date' => $proposedDateTime,
            'status'           => 'Scheduled',
            'modified_by'      => $this->modifier(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ];

        // Carry over requester/booking fields if the columns exist
        foreach ([
            'booking_type', 'booking_for_other',
            'requester_first_name', 'requester_last_name', 'requester_middle_name',
            'requester_contact_number', 'requester_birth_date', 'requester_email',
            'requester_user_id', 'requester_relationship_to_patient',
        ] as $col) {
            if (isset($old->$col) && Schema::hasColumn('appointments', $col)) {
                $payload[$col] = $old->$col;
            }
        }

        $newId = DB::table('appointments')->insertGetId($payload);

        $subject     = new Appointment;
        $subject->id = $newId;
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('appointment_rescheduled_from_cancelled')
            ->withProperties(['attributes' => [
                'source_appointment_id' => $cancelledAppointmentId,
                'new_appointment_id'    => $newId,
                'appointment_date'      => $proposedDateTime,
                'service_id'            => $serviceId,
                'status'                => 'Scheduled',
            ]])
            ->log('Rescheduled Appointment from Cancelled');

        return $newId;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Patient linking
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Link an existing patient to an appointment.
     */
    public function linkExistingPatient(int $appointmentId, int $patientId, object $appointment): void
    {
        DB::table('appointments')->where('id', $appointmentId)->update([
            'patient_id' => $patientId,
            'updated_at' => now(),
        ]);

        $subject     = new Appointment;
        $subject->id = $appointmentId;
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('appointment_request_linked_existing')
            ->withProperties(['attributes' => ['appointment_id' => $appointmentId, 'patient_id' => $patientId]])
            ->log('Linked Appointment Request to Existing Patient');

        $this->syncRequesterAccountPatientLink($appointment, $patientId);
    }

    /**
     * Create a new patient from a pending request and link it.
     * Returns $patientId.
     */
    public function createPatientFromRequest(int $appointmentId, array $requestData, object $appointment): int
    {
        $firstName  = trim((string) ($requestData['first_name'] ?? ''));
        $lastName   = trim((string) ($requestData['last_name'] ?? ''));
        $middleName = trim((string) ($requestData['middle_name'] ?? ''));

        $patientId = DB::table('patients')->insertGetId([
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'middle_name'   => $middleName !== '' ? $middleName : null,
            'mobile_number' => trim((string) ($requestData['mobile_number'] ?? '')),
            'birth_date'    => ! empty($requestData['birth_date']) ? $requestData['birth_date'] : null,
            'email_address' => ! empty($requestData['email_address']) ? $requestData['email_address'] : null,
            'modified_by'   => Auth::user()?->username ?? 'SYSTEM',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('appointments')->where('id', $appointmentId)->update([
            'patient_id' => $patientId,
            'updated_at' => now(),
        ]);

        $pSubject     = new Patient;
        $pSubject->id = $patientId;
        activity()->causedBy(Auth::user())->performedOn($pSubject)
            ->event('patient_created_from_request')
            ->withProperties(['attributes' => [
                'patient_id'            => $patientId,
                'source_appointment_id' => $appointmentId,
                'first_name'            => $firstName,
                'last_name'             => $lastName,
                'middle_name'           => $middleName !== '' ? $middleName : null,
            ]])
            ->log('Created Patient from Appointment Request');

        $aSubject     = new Appointment;
        $aSubject->id = $appointmentId;
        activity()->causedBy(Auth::user())->performedOn($aSubject)
            ->event('appointment_request_linked_new_patient')
            ->withProperties(['attributes' => ['appointment_id' => $appointmentId, 'patient_id' => $patientId]])
            ->log('Linked Appointment Request to New Patient');

        $this->syncRequesterAccountPatientLink($appointment, $patientId);

        return $patientId;
    }

    /**
     * Unlink a patient from an appointment.
     */
    public function unlinkPatient(int $appointmentId, int $currentPatientId, object $appointment): void
    {
        DB::table('appointments')->where('id', $appointmentId)->update([
            'patient_id' => null,
            'updated_at' => now(),
        ]);

        $this->clearRequesterAccountPatientLinkIfMatched($appointment, $currentPatientId);

        $subject     = new Appointment;
        $subject->id = $appointmentId;
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('appointment_patient_unlinked')
            ->withProperties(['attributes' => ['appointment_id' => $appointmentId, 'previous_patient_id' => $currentPatientId]])
            ->log('Unlinked Appointment from Patient Record');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    public function syncRequesterAccountPatientLink(object $appointment, int $patientId): void
    {
        if ($patientId <= 0 || ! Schema::hasColumn('users', 'patient_id')) {
            return;
        }

        $requesterUserId = (int) ($appointment->requester_user_id ?? 0);
        if ($requesterUserId <= 0 || ! $this->canPersistRequesterPatientLink($appointment)) {
            return;
        }

        $currentPatientId = DB::table('users')->where('id', $requesterUserId)->value('patient_id');
        if ((int) ($currentPatientId ?? 0) === $patientId) {
            return;
        }

        DB::table('users')->where('id', $requesterUserId)->update([
            'patient_id' => $patientId,
            'updated_at' => now(),
        ]);

        $subject     = new Appointment;
        $subject->id = (int) ($appointment->id ?? 0);
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('user_patient_linked')
            ->withProperties(['attributes' => ['user_id' => $requesterUserId, 'patient_id' => $patientId, 'appointment_id' => (int) $appointment->id]])
            ->log('Linked Patient Account to Patient Record');
    }

    public function clearRequesterAccountPatientLinkIfMatched(object $appointment, int $currentPatientId): void
    {
        if ($currentPatientId <= 0 || ! Schema::hasColumn('users', 'patient_id')) {
            return;
        }

        $requesterUserId = (int) ($appointment->requester_user_id ?? 0);
        if ($requesterUserId <= 0 || ! $this->canPersistRequesterPatientLink($appointment)) {
            return;
        }

        $user = DB::table('users')->select('id', 'patient_id')->where('id', $requesterUserId)->first();
        if (! $user || (int) ($user->patient_id ?? 0) !== $currentPatientId) {
            return;
        }

        DB::table('users')->where('id', $requesterUserId)->update(['patient_id' => null, 'updated_at' => now()]);

        $subject     = new Appointment;
        $subject->id = (int) $appointment->id;
        activity()->causedBy(Auth::user())->performedOn($subject)
            ->event('user_patient_unlinked')
            ->withProperties(['attributes' => ['user_id' => $requesterUserId, 'patient_id' => $currentPatientId, 'appointment_id' => (int) $appointment->id]])
            ->log('Unlinked Patient Account from Patient Record');
    }

    public function canPersistRequesterPatientLink(object $appointment): bool
    {
        if (! empty($appointment->booking_for_other)) {
            return false;
        }

        $requesterUserId = (int) ($appointment->requester_user_id ?? 0);
        if ($requesterUserId <= 0) {
            return false;
        }

        $query = DB::table('users')->where('id', $requesterUserId);
        if (Schema::hasColumn('users', 'role')) {
            $query->where('role', 3);
        }

        return $query->exists();
    }

    private function sendStatusEmail(int $appointmentId, string $newStatus): void
    {
        $record = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select(
                'appointments.appointment_date',
                DB::raw('COALESCE(patients.first_name, appointments.requester_first_name) as first_name'),
                DB::raw('COALESCE(patients.last_name, appointments.requester_last_name) as last_name'),
                DB::raw('COALESCE(patients.email_address, appointments.requester_email) as email_address'),
                'services.service_name'
            )
            ->where('appointments.id', $appointmentId)
            ->first();

        if (! $record || empty($record->email_address)) {
            return;
        }

        try {
            Mail::send('appointment.emails.appointment-status-update', [
                'name'             => trim($record->first_name.' '.$record->last_name),
                'appointment_date' => Carbon::parse($record->appointment_date)->format('F j, Y g:i A'),
                'service_name'     => $record->service_name,
                'status'           => $newStatus,
            ], function ($message) use ($record) {
                $message->to($record->email_address);
                $message->subject('Appointment Status Update');
            });
        } catch (\Throwable) {
            // Do not break UI if mail fails
        }
    }

    private function modifier(): string
    {
        return Auth::check() ? (Auth::user()->username ?? 'USER') : 'SYSTEM';
    }

    private function durationToMinutes(?string $duration): int
    {
        if (! $duration) {
            return 0;
        }
        sscanf($duration, '%d:%d:%d', $hours, $minutes, $seconds);
        return ((int) $hours * 60) + (int) $minutes;
    }
}
