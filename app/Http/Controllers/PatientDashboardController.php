<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PatientDashboardController extends Controller
{
    protected const PATIENT_CANCELLABLE_STATUSES = ['Pending', 'Scheduled'];

    protected const PATIENT_RESCHEDULABLE_STATUSES = ['Pending'];

    protected const APPROVED_SLOT_STATUSES = ['Scheduled', 'Waiting', 'Ongoing'];

    protected const INACTIVE_APPOINTMENT_STATUSES = ['Cancelled', 'Completed'];

    protected const SLOT_CAPACITY = 2;

    protected const REQUEST_SLOT_CAP = 5;

    protected const SELF_SERVICE_CHANGE_WINDOW_DAYS = 7;

    protected const SELF_SERVICE_CHANGE_LIMIT = 3;

    protected ?bool $blockedSlotsTableExists = null;

    protected ?bool $appointmentCancellationReasonExists = null;

    public function index(Request $request)
    {
        $user = Auth::user();

        $appointmentsQuery = DB::table('appointments')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->where(function ($query) use ($user) {
                $query->where('appointments.requester_user_id', $user->id);

                if (! empty($user->email)) {
                    $query->orWhere('appointments.requester_email', $user->email);
                }
            })
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.service_id',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.requester_first_name',
                'appointments.requester_last_name',
                'appointments.updated_at',
                'services.service_name'
            );

        $allAppointments = (clone $appointmentsQuery)
            ->orderByDesc('appointments.updated_at')
            ->orderByDesc('appointments.appointment_date')
            ->get();

        $pendingRequests = (clone $appointmentsQuery)
            ->where('appointments.status', 'Pending')
            ->orderBy('appointments.appointment_date', 'asc')
            ->limit(5)
            ->get();

        $upcomingAppointments = (clone $appointmentsQuery)
            ->whereIn('appointments.status', ['Scheduled', 'Waiting'])
            ->where('appointments.appointment_date', '>=', now())
            ->orderBy('appointments.appointment_date', 'asc')
            ->limit(5)
            ->get();

        $upcomingAppointment = $upcomingAppointments->first();

        $appointmentRequests = (clone $appointmentsQuery)
            ->whereIn('appointments.status', ['Pending', 'Scheduled', 'Waiting'])
            ->orderBy('appointments.appointment_date', 'asc')
            ->get();

        $appointmentHistory = (clone $appointmentsQuery)
            ->where(function ($query) {
                $query->where('appointments.appointment_date', '<', now())
                    ->orWhereIn('appointments.status', ['Completed', 'Cancelled']);
            })
            ->orderBy('appointments.appointment_date', 'desc')
            ->limit(10)
            ->get();

        $recentUpdates = (clone $appointmentsQuery)
            ->orderByDesc('appointments.updated_at')
            ->orderByDesc('appointments.appointment_date')
            ->limit(4)
            ->get();

        $latestRequestIdentity = (clone $appointmentsQuery)
            ->orderByDesc('appointments.updated_at')
            ->orderByDesc('appointments.appointment_date')
            ->first();

        $completedVisits = (clone $appointmentsQuery)
            ->where('appointments.status', 'Completed')
            ->count();

        $profileChecklist = [
            filled($user->first_name ?? null),
            filled($user->last_name ?? null),
            filled($user->email ?? null),
            filled($user->mobile_number ?? null),
        ];

        $completedProfileItems = collect($profileChecklist)->filter()->count();
        $profileCompleteness = [
            'completed' => $completedProfileItems,
            'total' => count($profileChecklist),
            'percentage' => (int) round(($completedProfileItems / max(count($profileChecklist), 1)) * 100),
            'label' => $completedProfileItems === count($profileChecklist) ? 'Complete' : 'Needs Update',
        ];

        $nextAppointmentSummary = null;

        if ($upcomingAppointment) {
            $nextDate = Carbon::parse($upcomingAppointment->appointment_date);
            $nextAppointmentSummary = [
                'service_name' => $upcomingAppointment->service_name ?? 'Service',
                'status' => $upcomingAppointment->status ?? 'Pending',
                'date_label' => $nextDate->format('F d, Y'),
                'time_label' => $nextDate->format('h:i A'),
                'day_label' => $nextDate->isToday()
                    ? 'Today'
                    : ($nextDate->isTomorrow() ? 'Tomorrow' : $nextDate->diffForHumans(now(), ['parts' => 2, 'short' => false])),
            ];
        }

        $dashboardStats = [
            'requests_in_progress' => $appointmentRequests->count(),
            'completed_visits' => $completedVisits,
            'next_visit_label' => $nextAppointmentSummary
                ? $nextAppointmentSummary['date_label'].' at '.$nextAppointmentSummary['time_label']
                : 'No appointment yet',
        ];

        $requesterDisplayName = trim((string) ($user->first_name ?? '').' '.(string) ($user->last_name ?? ''));

        if ($requesterDisplayName === '') {
            $requesterDisplayName = trim(
            (string) ($latestRequestIdentity->requester_first_name ?? '').' '.
            (string) ($latestRequestIdentity->requester_last_name ?? '')
            );
        }

        if ($requesterDisplayName === '') {
            $requesterDisplayName = 'Patient';
        }

        $activityLog = $this->buildPatientActivityLog($user, $allAppointments);

        return view('patient.dashboard', [
            'user' => $user,
            'requesterDisplayName' => $requesterDisplayName,
            'upcomingAppointment' => $upcomingAppointment,
            'upcomingAppointments' => $upcomingAppointments,
            'pendingRequests' => $pendingRequests,
            'appointmentHistory' => $appointmentHistory,
            'appointmentRequests' => $appointmentRequests,
            'recentUpdates' => $recentUpdates,
            'dashboardStats' => $dashboardStats,
            'profileCompleteness' => $profileCompleteness,
            'nextAppointmentSummary' => $nextAppointmentSummary,
            'activityLog' => $activityLog,
            'selfServiceLimitMessage' => $this->selfServiceLimitMessage(),
        ]);
    }

    public function editReschedule(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        $this->ensureOwnsAppointment($appointment, $user);

        if (! in_array($appointment->status, self::PATIENT_RESCHEDULABLE_STATUSES, true)) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'Only pending appointment requests can be rescheduled online.');
        }

        if ($this->hasExceededSelfServiceChangeLimit($user)) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', $this->selfServiceLimitMessage());
        }

        $selectedDate = (string) $request->query('date', Carbon::parse($appointment->appointment_date)->toDateString());
        if (! $this->isValidSelectedDate($selectedDate)) {
            $selectedDate = Carbon::parse($appointment->appointment_date)->toDateString();
        }

        return view('patient.reschedule', [
            'appointment' => $appointment,
            'service' => DB::table('services')->where('id', $appointment->service_id)->first(),
            'selectedDate' => $selectedDate,
            'availableSlots' => $this->generateSlots($selectedDate, $appointment->id),
            'currentSlotValue' => Carbon::parse($appointment->appointment_date)->format('H:i:00'),
            'changeLimitMessage' => $this->selfServiceLimitMessage(),
        ]);
    }

    public function updateReschedule(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        $this->ensureOwnsAppointment($appointment, $user);

        if (! in_array($appointment->status, self::PATIENT_RESCHEDULABLE_STATUSES, true)) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'Only pending appointment requests can be rescheduled online.');
        }

        if ($this->hasExceededSelfServiceChangeLimit($user)) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', $this->selfServiceLimitMessage());
        }

        $validated = $request->validate([
            'selectedDate' => 'required|date|after_or_equal:today',
            'selectedSlot' => 'required|date_format:H:i:s',
        ]);

        $availabilityError = $this->validateRescheduleAvailability(
            $appointment,
            $validated['selectedDate'],
            $validated['selectedSlot']
        );

        if ($availabilityError !== null) {
            return redirect()
                ->route('patient.appointments.reschedule.edit', [
                    'appointment' => $appointment->id,
                    'date' => $validated['selectedDate'],
                ])
                ->withInput()
                ->with('failed', $availabilityError);
        }

        $newAppointmentDate = Carbon::parse($validated['selectedDate'].' '.$validated['selectedSlot'])->toDateTimeString();

        $updated = DB::table('appointments')
            ->where('id', $appointment->id)
            ->where('status', 'Pending')
            ->update([
                'appointment_date' => $newAppointmentDate,
                'updated_at' => now(),
                'modified_by' => $user->username ?? $user->email ?? 'PATIENT',
            ]);

        if ($updated === 0) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'This appointment request was already updated. Please refresh and try again.');
        }

        $subject = new Appointment;
        $subject->id = $appointment->id;

        activity()
            ->performedOn($subject)
            ->causedBy($user)
            ->event('appointment_rescheduled_by_patient')
            ->withProperties([
                'appointment_id' => $appointment->id,
                'old_appointment_date' => $appointment->appointment_date,
                'appointment_date' => $newAppointmentDate,
            ])
            ->log('Patient Rescheduled Appointment Request');

        $this->createStaffNotification([
            'type' => 'patient_rescheduled_request',
            'appointment_id' => $appointment->id,
            'actor_user_id' => $user->id,
            'title' => 'Patient Rescheduled Request',
            'message' => sprintf(
                '%s rescheduled from %s to %s.',
                $this->resolvePatientDisplayName($appointment, $user),
                Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A'),
                Carbon::parse($newAppointmentDate)->format('M d, Y h:i A')
            ),
            'link' => url('/appointment'),
        ]);

        return redirect()
            ->route('patient.dashboard')
            ->with('success', 'Your pending appointment request was rescheduled successfully.');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        $this->ensureOwnsAppointment($appointment, $user);

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        if (! in_array($appointment->status, self::PATIENT_CANCELLABLE_STATUSES, true)) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'Only pending or scheduled appointment requests can be cancelled.');
        }

        if ($this->hasExceededSelfServiceChangeLimit($user)) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', $this->selfServiceLimitMessage());
        }

        if ($appointment->status === 'Scheduled' && Carbon::parse($appointment->appointment_date)->lt(now())) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'Scheduled appointments that have already passed can no longer be cancelled. Please contact the clinic directly.');
        }

        $cancellationReason = $this->normalizeCancellationReason($validated['cancellation_reason'] ?? null);

        $updatePayload = [
            'status' => 'Cancelled',
            'modified_by' => $user->username ?? $user->email ?? 'PATIENT',
            'updated_at' => now(),
        ];

        if ($this->appointmentCancellationReasonEnabled()) {
            $updatePayload['cancellation_reason'] = $cancellationReason;
        }

        $updated = DB::table('appointments')
            ->where('id', $appointment->id)
            ->whereIn('status', self::PATIENT_CANCELLABLE_STATUSES)
            ->update($updatePayload);

        if ($updated === 0) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'This appointment was already updated. Please refresh and try again.');
        }

        $subject = new Appointment;
        $subject->id = $appointment->id;

        activity()
            ->performedOn($subject)
            ->causedBy($user)
            ->event('appointment_cancelled_by_patient')
            ->withProperties([
                'appointment_id' => $appointment->id,
                'appointment_date' => $appointment->appointment_date,
                'cancellation_reason' => $cancellationReason,
            ])
            ->log('Patient Cancelled Appointment');

        $staffMessage = sprintf(
            '%s cancelled their appointment request scheduled for %s.',
            $this->resolvePatientDisplayName($appointment, $user),
            Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A')
        );

        if ($cancellationReason !== null) {
            $staffMessage .= ' Reason: '.$cancellationReason;
        }

        $this->createStaffNotification([
            'type' => 'patient_cancelled_request',
            'appointment_id' => $appointment->id,
            'actor_user_id' => $user->id,
            'title' => 'Patient Cancelled Request',
            'message' => $staffMessage,
            'link' => url('/appointment'),
        ]);

        return redirect()
            ->route('patient.dashboard')
            ->with('success', 'Your appointment request has been cancelled and staff were notified.');
    }

    protected function ensureOwnsAppointment(Appointment $appointment, $user): void
    {
        $ownsAppointment = (int) $appointment->requester_user_id === (int) $user->id
            || (! empty($user->email) && strtolower((string) $appointment->requester_email) === strtolower((string) $user->email));

        if (! $ownsAppointment) {
            abort(403, 'You are not allowed to manage this appointment.');
        }
    }

    protected function hasExceededSelfServiceChangeLimit($user): bool
    {
        $recentChanges = DB::table('activity_log')
            ->where('causer_type', 'App\\Models\\User')
            ->where('causer_id', $user->id)
            ->whereIn('event', [
                'appointment_cancelled_by_patient',
                'appointment_rescheduled_by_patient',
            ])
            ->where('created_at', '>=', now()->subDays(self::SELF_SERVICE_CHANGE_WINDOW_DAYS))
            ->count();

        return $recentChanges >= self::SELF_SERVICE_CHANGE_LIMIT;
    }

    protected function selfServiceLimitMessage(): string
    {
        return 'You have reached the limit for online appointment changes. Please contact the clinic for further assistance.';
    }

    protected function createStaffNotification(array $payload): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        $recipientIds = DB::table('users')
            ->whereIn('role', [1, 2])
            ->pluck('id');

        if ($recipientIds->isEmpty()) {
            return;
        }

        $rows = $recipientIds->map(fn ($recipientId) => [
            'user_id' => $recipientId,
            'type' => $payload['type'],
            'appointment_id' => $payload['appointment_id'] ?? null,
            'actor_user_id' => $payload['actor_user_id'] ?? null,
            'title' => $payload['title'],
            'message' => $payload['message'],
            'link' => $payload['link'] ?? url('/appointment'),
            'read_at' => null,
            'cleared_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        DB::table('notifications')->insert($rows);
    }

    protected function resolvePatientDisplayName(Appointment $appointment, $user): string
    {
        $requesterName = trim(
            (string) ($appointment->requester_first_name ?? '').' '.
            (string) ($appointment->requester_last_name ?? '')
        );

        if ($requesterName !== '') {
            return $requesterName;
        }

        return (string) ($user->email ?? $user->username ?? 'Patient');
    }

    protected function buildPatientActivityLog($user, $appointments)
    {
        $appointments = collect($appointments);

        $appointmentsById = $appointments
            ->keyBy(fn ($appointment) => (int) $appointment->id)
            ->map(fn ($appointment) => [
                'id' => (int) $appointment->id,
                'patient_id' => $appointment->patient_id ? (int) $appointment->patient_id : null,
                'appointment_date' => $appointment->appointment_date,
                'service_name' => $appointment->service_name ?? 'Service',
                'status' => $appointment->status ?? null,
            ])
            ->all();

        $appointmentIds = array_values(array_filter(array_map('intval', array_keys($appointmentsById))));
        $patientIds = $appointments
            ->pluck('patient_id')
            ->filter(fn ($patientId) => filled($patientId))
            ->map(fn ($patientId) => (int) $patientId)
            ->unique()
            ->values()
            ->all();

        $activityEntries = collect();

        if (Schema::hasTable('activity_log')) {
            if (! empty($appointmentIds)) {
                $appointmentActivities = DB::table('activity_log')
                    ->where('subject_type', Appointment::class)
                    ->whereIn('subject_id', $appointmentIds)
                    ->orderByDesc('created_at')
                    ->limit(20)
                    ->get();

                $activityEntries = $activityEntries->merge(
                    $appointmentActivities
                        ->map(fn ($activity) => $this->mapAppointmentActivityLogEntry($activity, $appointmentsById))
                        ->filter()
                );
            }

            if (! empty($patientIds)) {
                $patientActivities = DB::table('activity_log')
                    ->where('subject_type', 'App\\Models\\Patient')
                    ->whereIn('subject_id', $patientIds)
                    ->whereIn('event', ['patient_created', 'patient_updated'])
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get();

                $activityEntries = $activityEntries->merge(
                    $patientActivities
                        ->map(fn ($activity) => $this->mapProfileActivityLogEntry($activity))
                        ->filter()
                );
            }

            $userActivities = DB::table('activity_log')
                ->where('subject_type', 'App\\Models\\User')
                ->where('subject_id', $user->id)
                ->whereIn('event', ['user_updated', 'user_logged_in'])
                ->orderByDesc('created_at')
                ->limit(16)
                ->get();

            $activityEntries = $activityEntries->merge(
                $userActivities
                    ->map(fn ($activity) => $this->mapUserActivityLogEntry($activity))
                    ->filter()
            );

            if (! empty($patientIds) && Schema::hasTable('treatment_records')) {
                $treatmentRecordIds = DB::table('treatment_records')
                    ->whereIn('patient_id', $patientIds)
                    ->pluck('id')
                    ->map(fn ($recordId) => (int) $recordId)
                    ->all();

                if (! empty($treatmentRecordIds)) {
                    $treatmentActivities = DB::table('activity_log')
                        ->where('subject_type', 'App\\Models\\TreatmentRecord')
                        ->whereIn('subject_id', $treatmentRecordIds)
                        ->whereIn('event', ['treatment_record_created', 'treatment_record_updated'])
                        ->orderByDesc('created_at')
                        ->limit(12)
                        ->get();

                    $activityEntries = $activityEntries->merge(
                        $treatmentActivities
                            ->map(fn ($activity) => $this->mapTreatmentActivityLogEntry($activity))
                            ->filter()
                    );
                }
            }
        }

        if (! empty($patientIds) && Schema::hasTable('treatment_records')) {
            $payments = DB::table('treatment_records')
                ->whereIn('patient_id', $patientIds)
                ->whereNotNull('amount_charged')
                ->where('amount_charged', '>', 0)
                ->orderByDesc('updated_at')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get([
                    'id',
                    'treatment',
                    'amount_charged',
                    'created_at',
                    'updated_at',
                ]);

            $activityEntries = $activityEntries->merge(
                $payments->map(function ($payment) {
                    $loggedAt = $payment->updated_at ?? $payment->created_at ?? now();
                    $treatmentName = trim((string) ($payment->treatment ?? ''));
                    $amount = number_format((float) $payment->amount_charged, 2);

                    return [
                        'key' => 'payment-'.$payment->id,
                        'type' => 'payment recorded',
                        'description' => $treatmentName !== ''
                            ? "Payment of PHP {$amount} recorded for {$treatmentName}."
                            : "Payment of PHP {$amount} recorded.",
                        'status' => 'Paid',
                        'logged_at' => Carbon::parse($loggedAt),
                    ];
                })
            );
        }

        return $activityEntries
            ->sortByDesc(fn (array $entry) => $entry['logged_at']->getTimestamp())
            ->unique('key')
            ->take(8)
            ->values();
    }

    protected function mapAppointmentActivityLogEntry(object $activity, array $appointmentsById): ?array
    {
        $event = strtolower((string) ($activity->event ?? ''));
        $description = strtolower((string) ($activity->description ?? ''));
        $appointment = $appointmentsById[(int) ($activity->subject_id ?? 0)] ?? null;
        $serviceName = $appointment['service_name'] ?? 'Appointment';
        $status = $appointment['status'] ?? null;
        $appointmentDate = ! empty($appointment['appointment_date'])
            ? Carbon::parse($appointment['appointment_date'])->format('M d, Y h:i A')
            : null;

        if (str_contains($event, 'rescheduled') || str_contains($description, 'rescheduled')) {
            return [
                'key' => 'appointment-'.((int) ($activity->subject_id ?? 0)).'-updated',
                'type' => 'appointment updated',
                'description' => $appointmentDate
                    ? "{$serviceName} was rescheduled to {$appointmentDate}."
                    : "{$serviceName} was rescheduled.",
                'status' => $status,
                'logged_at' => Carbon::parse($activity->created_at ?? now()),
            ];
        }

        if (str_contains($event, 'cancelled') || str_contains($description, 'cancelled')) {
            return [
                'key' => 'appointment-'.((int) ($activity->subject_id ?? 0)).'-updated',
                'type' => 'appointment updated',
                'description' => $appointmentDate
                    ? "{$serviceName} on {$appointmentDate} was cancelled."
                    : "{$serviceName} was cancelled.",
                'status' => 'Cancelled',
                'logged_at' => Carbon::parse($activity->created_at ?? now()),
            ];
        }

        if (str_contains($event, 'approved') || str_contains($event, 'updated')) {
            return [
                'key' => 'appointment-'.((int) ($activity->subject_id ?? 0)).'-updated',
                'type' => 'appointment updated',
                'description' => $appointmentDate
                    ? "{$serviceName} was updated for {$appointmentDate}."
                    : "{$serviceName} details were updated.",
                'status' => $status,
                'logged_at' => Carbon::parse($activity->created_at ?? now()),
            ];
        }

        if (str_contains($event, 'created') || str_contains($description, 'created')) {
            return [
                'key' => 'appointment-'.((int) ($activity->subject_id ?? 0)).'-created',
                'type' => 'appointment created',
                'description' => $appointmentDate
                    ? "{$serviceName} was created for {$appointmentDate}."
                    : "{$serviceName} request was created.",
                'status' => $status,
                'logged_at' => Carbon::parse($activity->created_at ?? now()),
            ];
        }

        return null;
    }

    protected function mapTreatmentActivityLogEntry(object $activity): ?array
    {
        $properties = $this->decodeActivityProperties($activity->properties ?? null);
        $attributes = is_array($properties['attributes'] ?? null) ? $properties['attributes'] : [];
        $treatmentName = trim((string) ($attributes['treatment'] ?? ''));
        $event = strtolower((string) ($activity->event ?? ''));
        $isUpdate = str_contains($event, 'updated');
        $actionLabel = $isUpdate ? 'treatment record updated' : 'treatment record added';

        return [
            'key' => 'activity-'.$activity->id,
            'type' => $actionLabel,
            'description' => $treatmentName !== ''
                ? ucfirst($actionLabel).' for '.$treatmentName.'.'
                : ucfirst($actionLabel).'.',
            'status' => null,
            'logged_at' => Carbon::parse($activity->created_at ?? now()),
        ];
    }

    protected function mapProfileActivityLogEntry(object $activity): array
    {
        return [
            'key' => 'activity-'.$activity->id,
            'type' => 'profile updated',
            'description' => 'Your profile information was updated.',
            'status' => 'Updated',
            'logged_at' => Carbon::parse($activity->created_at ?? now()),
        ];
    }

    protected function mapUserActivityLogEntry(object $activity): ?array
    {
        $event = strtolower((string) ($activity->event ?? ''));

        if ($event === 'user_logged_in') {
            $properties = $this->decodeActivityProperties($activity->properties ?? null);
            $attributes = is_array($properties['attributes'] ?? null) ? $properties['attributes'] : [];
            $loginAt = ! empty($attributes['login_at'])
                ? Carbon::parse($attributes['login_at'])
                : Carbon::parse($activity->created_at ?? now());

            return [
                'key' => 'activity-'.$activity->id,
                'type' => 'Logged in',
                'description' => 'Successfully logged into your account.',
                'status' => 'Success',
                'logged_at' => $loginAt,
            ];
        }

        return $this->mapProfileActivityLogEntry($activity);
    }

    protected function decodeActivityProperties($properties): array
    {
        if (is_array($properties)) {
            return $properties;
        }

        if (is_string($properties) && $properties !== '') {
            $decoded = json_decode($properties, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    protected function validateRescheduleAvailability(Appointment $appointment, string $selectedDate, string $selectedSlot): ?string
    {
        if (! $this->isValidSelectedDate($selectedDate)) {
            return 'Please choose a valid date.';
        }

        if (! preg_match('/^\d{2}:\d{2}:\d{2}$/', $selectedSlot)) {
            return 'Please choose a valid time slot.';
        }

        $appointmentDateTime = Carbon::parse($selectedDate.' '.$selectedSlot)->toDateTimeString();
        $slotStart = Carbon::parse($appointmentDateTime)->seconds(0);
        $slotEnd = $slotStart->copy()->addHour();

        if ($slotStart->lt(now())) {
            return 'This time slot is already in the past. Please choose another time.';
        }

        if ($this->blockedSlotsEnabled()) {
            $isBlocked = DB::table('blocked_slots')
                ->whereDate('date', $slotStart->toDateString())
                ->where('start_time', '<', $slotEnd->format('H:i:s'))
                ->where('end_time', '>', $slotStart->format('H:i:s'))
                ->exists();

            if ($isBlocked) {
                return 'This time slot is unavailable. Please choose another time.';
            }
        }

        $activeSlotBookings = DB::table('appointments')
            ->where('id', '!=', $appointment->id)
            ->where('appointment_date', $appointmentDateTime)
            ->whereIn('status', self::APPROVED_SLOT_STATUSES)
            ->count();

        if ($activeSlotBookings >= self::SLOT_CAPACITY) {
            return 'This time slot is already full. Please choose another time.';
        }

        $totalActiveRequestsInSlot = DB::table('appointments')
            ->where('id', '!=', $appointment->id)
            ->where('appointment_date', $appointmentDateTime)
            ->whereNotIn('status', self::INACTIVE_APPOINTMENT_STATUSES)
            ->count();

        if ($totalActiveRequestsInSlot >= self::REQUEST_SLOT_CAP) {
            return 'This time slot already reached the maximum of 5 requests. Please choose another time.';
        }

        return null;
    }

    protected function generateSlots(string $dateString, ?int $ignoreAppointmentId = null): array
    {
        $startTime = Carbon::parse($dateString.' 09:00:00');
        $endTime = Carbon::parse($dateString.' 20:00:00');
        $duration = 60;
        $blockedSlots = collect();

        if ($this->blockedSlotsEnabled()) {
            $blockedSlots = DB::table('blocked_slots')
                ->whereDate('date', $dateString)
                ->select('start_time', 'end_time')
                ->get();
        }

        $bookedCounts = DB::table('appointments')
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->whereDate('appointment_date', $dateString)
            ->whereIn('status', self::APPROVED_SLOT_STATUSES)
            ->selectRaw('TIME(appointment_date) as time_slot, COUNT(*) as total')
            ->groupBy('time_slot')
            ->pluck('total', 'time_slot')
            ->toArray();

        $requestCounts = DB::table('appointments')
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->whereDate('appointment_date', $dateString)
            ->whereNotIn('status', self::INACTIVE_APPOINTMENT_STATUSES)
            ->selectRaw('TIME(appointment_date) as time_slot, COUNT(*) as total')
            ->groupBy('time_slot')
            ->pluck('total', 'time_slot')
            ->toArray();

        $slots = [];

        while ($startTime->lte($endTime)) {
            $slotTime = $startTime->format('H:i:00');
            $currentCount = $bookedCounts[$slotTime] ?? 0;
            $currentRequests = $requestCounts[$slotTime] ?? 0;
            $slotDateTime = Carbon::parse($dateString.' '.$slotTime);
            $slotEndDateTime = $slotDateTime->copy()->addMinutes($duration);

            $slots[] = [
                'time' => $startTime->format('h:i A'),
                'value' => $slotTime,
                'is_full' => $currentCount >= self::SLOT_CAPACITY || $currentRequests >= self::REQUEST_SLOT_CAP,
                'is_past' => $slotDateTime->lt(now()),
                'is_blocked' => $this->isBlockedBySlotCollection($slotDateTime, $slotEndDateTime, $blockedSlots),
            ];

            $startTime->addMinutes($duration);
        }

        return $slots;
    }

    protected function blockedSlotsEnabled(): bool
    {
        if ($this->blockedSlotsTableExists === null) {
            $this->blockedSlotsTableExists = Schema::hasTable('blocked_slots');
        }

        return $this->blockedSlotsTableExists;
    }

    protected function appointmentCancellationReasonEnabled(): bool
    {
        if ($this->appointmentCancellationReasonExists === null) {
            $this->appointmentCancellationReasonExists = Schema::hasTable('appointments')
                && Schema::hasColumn('appointments', 'cancellation_reason');
        }

        return $this->appointmentCancellationReasonExists;
    }

    protected function isBlockedBySlotCollection(Carbon $slotStart, Carbon $slotEnd, $blockedSlots): bool
    {
        if (! $this->blockedSlotsEnabled() || $blockedSlots->isEmpty()) {
            return false;
        }

        foreach ($blockedSlots as $blockedSlot) {
            $blockedStart = Carbon::parse($slotStart->toDateString().' '.(string) $blockedSlot->start_time);
            $blockedEnd = Carbon::parse($slotStart->toDateString().' '.(string) $blockedSlot->end_time);

            if ($blockedStart->lt($slotEnd) && $blockedEnd->gt($slotStart)) {
                return true;
            }
        }

        return false;
    }

    protected function isValidSelectedDate(string $date): bool
    {
        if ($date === '') {
            return false;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $date)?->format('Y-m-d') === $date;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function normalizeCancellationReason(?string $reason): ?string
    {
        $reason = trim((string) $reason);

        return $reason !== '' ? $reason : null;
    }
}
