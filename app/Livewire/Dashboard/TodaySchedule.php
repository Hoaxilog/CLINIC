<?php

namespace App\Livewire\Dashboard;

use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\CalendarQueryService;
use App\Support\PatientMatchService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Livewire\Component;

class TodaySchedule extends Component
{
    public $todayAppointments = [];

    public $waitingQueue = [];

    public $ongoingAppointments = [];

    public $showAppointmentModal = false;

    public $isViewing = false;

    public $isRescheduling = false;

    public $viewingAppointmentId = null;

    public $viewingPatientId = null;

    public $firstName = '';

    public $lastName = '';

    public $middleName = '';

    public $contactNumber = '';

    public $birthDate = null;

    public $selectedService = '';

    public $selectedDate = null;

    public $selectedTime = null;

    public $endTime = null;

    public $appointmentStatus = '';

    public $appointmentDate = null;

    public $servicesList = [];

    public $isPatientFormOpen = false;

    public $viewingBookingForOther = false;

    public $viewingRequesterFirstName = '';

    public $viewingRequesterLastName = '';

    public $viewingRequesterContactNumber = '';

    public $viewingRequesterEmail = '';

    public $viewingRequesterRelationship = '';

    public $selectedPendingPatientId = null;

    public $pendingMatchCandidates = [];

    public $pendingDuplicateWarnings = [];

    public $pendingApprovalSafety = [];

    public function mount()
    {
        $this->refreshAllSections();
        $this->servicesList = DB::table('services')->get();
    }

    #[On('patient-form-opened')]
    public function stopPolling()
    {
        $this->isPatientFormOpen = true;
    }

    #[On('patient-form-closed')]
    public function resumePolling()
    {
        $this->isPatientFormOpen = false;
        $this->refreshAllSections();
    }

    public function loadDashboardData()
    {
        $this->refreshAllSections();
    }

    public function refreshAllSections()
    {
        $this->refreshTodaySchedule();
        $this->refreshWaitingQueue();
        $this->refreshOngoingAppointments();
    }

    public function refreshTodaySchedule()
    {
        $today = Carbon::today();
        $this->todayAppointments = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointments.appointment_date', $today)
            ->whereIn('appointments.status', ['Scheduled', 'Completed', 'Cancelled'])
            ->orderBy('appointments.appointment_date', 'asc')
            ->select($this->appointmentBoardSelect())
            ->get();
    }

    public function refreshWaitingQueue()
    {
        $today = Carbon::today();
        $this->waitingQueue = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointments.appointment_date', $today)
            ->where('appointments.status', 'Waiting')
            ->orderBy('appointments.appointment_date', 'asc')
            ->orderBy('appointments.created_at', 'asc')
            ->select(array_merge(
                $this->appointmentBoardSelect(),
                [DB::raw('appointments.updated_at as waited_at')]
            ))
            ->get();
    }

    public function refreshOngoingAppointments()
    {
        $today = Carbon::today();
        $user = Auth::user();

        $ongoingQuery = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.dentist_id', '=', 'users.id')
            ->whereDate('appointments.appointment_date', $today)
            ->where('appointments.status', 'Ongoing')
            ->orderBy('appointments.updated_at', 'desc')
            ->select(array_merge(
                $this->appointmentBoardSelect(),
                [
                    'appointments.dentist_id',
                    DB::raw("NULLIF(TRIM(CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, ''))), '') as dentist_name"),
                    'users.username as dentist_username',
                ]
            ));

        if ($user?->isDentist()) {
            $ongoingQuery->where('dentist_id', $user->id);
        }

        $this->ongoingAppointments = $ongoingQuery->get();
    }

    public function admitPatient()
    {
        if (! Auth::user()?->canHandleChairsideFlow()) {
            $this->dispatch('flash-message', type: 'error', message: 'Only admins or dentists can admit patients from the lobby.');

            return;
        }

        $result = app(AppointmentService::class)->admit(
            (int) $this->viewingAppointmentId,
            (int) $this->selectedService,
            (int) Auth::id()
        );

        if (! $result['ok']) {
            $this->dispatch('flash-message', type: 'error', message: $result['error']);

            return;
        }

        $this->refreshAllSections();
        $this->closeAppointmentModal();
        $this->dispatch('editPatient', id: $result['patientId'], startStep: 3);
    }

    public function callNextPatient()
    {
        if (Auth::user()?->role !== 1) {
            $this->dispatch('flash-message', type: 'error', message: 'Only dentists can call the next patient.');

            return;
        }

        $today = Carbon::today();

        $next = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->where('status', 'Waiting')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();

        if (! $next) {
            $this->dispatch('flash-message', type: 'info', message: 'No Ready patients in queue.');

            return;
        }

        $service = DB::table('services')->where('id', $next->service_id)->first();
        if (! $service) {
            $this->dispatch('flash-message', type: 'error', message: 'Missing service for selected appointment.');

            return;
        }

        $startTime = Carbon::parse($next->appointment_date);
        sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
        $durationMinutes = ($h * 60) + $m;
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        $hasConflict = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $next->id)
            ->whereNotIn('appointments.status', ['Cancelled', 'Waiting', 'Completed'])
            ->whereDate('appointment_date', $startTime->toDateString())
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('appointment_date', '<', $endTime)
                    ->whereRaw('DATE_ADD(appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND) > ?', [$startTime]);
            })
            ->exists();

        if ($hasConflict) {
            $this->dispatch('flash-message', type: 'error', message: 'Cannot call the next patient yet: you are still handling another patient in this time slot.');

            return;
        }

        DB::table('appointments')->where('id', $next->id)->update([
            'status' => 'Ongoing',
            'dentist_id' => Auth::id(),
            'updated_at' => now(),
        ]);

        $subject = new Appointment;
        $subject->id = $next->id;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('appointment_admitted')
            ->withProperties([
                'old' => [
                    'status' => $next->status ?? null,
                    'service_id' => $next->service_id ?? null,
                    'dentist_id' => $next->dentist_id ?? null,
                ],
                'attributes' => [
                    'status' => 'Ongoing',
                    'service_id' => $next->service_id,
                    'dentist_id' => Auth::id(),
                ],
            ])
            ->log('Admitted Appointment');

        $this->refreshWaitingQueue();
        $this->refreshOngoingAppointments();
        $this->viewAppointment($next->id);
    }

    public function viewAppointment($appointmentId)
    {
        $this->resetAppointmentViewState();

        $appointment = app(CalendarQueryService::class)->findForView((int) $appointmentId);

        if ($appointment) {
            $this->firstName = $appointment->first_name;
            $this->lastName = $appointment->last_name;
            $this->middleName = $appointment->middle_name;
            $this->contactNumber = $appointment->mobile_number;
            $this->birthDate = $appointment->birth_date;
            $this->selectedService = $appointment->service_id;
            $this->viewingAppointmentId = $appointment->id;
            $this->viewingPatientId = $appointment->patient_id ?? null;
            $this->appointmentStatus = $appointment->status;
            $this->viewingBookingForOther = ! empty($appointment->booking_for_other);
            $this->viewingRequesterFirstName = (string) ($appointment->requester_first_name ?? '');
            $this->viewingRequesterLastName = (string) ($appointment->requester_last_name ?? '');
            $this->viewingRequesterContactNumber = (string) ($appointment->requester_contact_number ?? $appointment->mobile_number ?? '');
            $this->viewingRequesterEmail = (string) ($appointment->requester_email ?? $appointment->email_address ?? '');
            $this->viewingRequesterRelationship = (string) ($appointment->requester_relationship_to_patient ?? '');
            $this->hydratePendingReviewContext($appointment);
            $this->pendingApprovalSafety = [];

            $dt = Carbon::parse($appointment->appointment_date);
            $this->appointmentDate = $dt->toDateString();
            $this->selectedDate = $dt->toDateString();
            $this->selectedTime = $dt->format('H:i:s');
            $durationInMinutes = $this->durationToMinutes($appointment->duration ?? null);
            $this->endTime = $dt->copy()->addMinutes($durationInMinutes)->format('H:i');

            $this->isViewing = true;
            $this->showAppointmentModal = true;
        }
    }

    protected function durationToMinutes(?string $duration): int
    {
        if (! $duration) {
            return 0;
        }

        sscanf($duration, '%d:%d:%d', $hours, $minutes, $seconds);

        return ((int) $hours * 60) + (int) $minutes;
    }

    protected function appointmentBoardSelect(): array
    {
        $calendarQuery = app(CalendarQueryService::class);

        $select = [
            'appointments.id',
            'appointments.patient_id',
            'appointments.service_id',
            'appointments.appointment_date',
            'appointments.status',
            DB::raw($calendarQuery->firstNameExpr().' as first_name'),
            DB::raw($calendarQuery->lastNameExpr().' as last_name'),
            DB::raw($calendarQuery->middleNameExpr().' as middle_name'),
            DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as mobile_number'),
            DB::raw($calendarQuery->birthDateExpr().' as birth_date'),
            'services.duration',
            'services.service_name',
        ];

        foreach ([
            'requester_first_name',
            'requester_last_name',
            'requester_contact_number',
        ] as $column) {
            if (Schema::hasColumn('appointments', $column)) {
                $select[] = 'appointments.'.$column;
            }
        }

        return $select;
    }

    public function openPatientChart()
    {
        if ($this->dispatchPatientForm(3)) {
            $this->closeAppointmentModal();
        }
    }

    public function canOpenViewingPatientChart(): bool
    {
        $user = Auth::user();
        if (! $user || ! $this->viewingAppointmentId) {
            return false;
        }

        $appointment = DB::table('appointments')
            ->select('status', 'dentist_id')
            ->where('id', $this->viewingAppointmentId)
            ->first();

        if (! $appointment || $appointment->status !== 'Ongoing') {
            return false;
        }

        return $user->isAdmin() || (int) ($appointment->dentist_id ?? 0) === (int) $user->id;
    }

    public function processPatient()
    {
        if ($this->dispatchPatientForm(1)) {
            $this->closeAppointmentModal();
        }
    }

    public function dispatchPatientForm(int $startStep = 1): bool
    {
        $patientId = null;

        if ($this->viewingAppointmentId) {
            $patientId = DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->value('patient_id');
        }

        if (! $patientId) {
            $this->dispatch('flash-message', type: 'error', message: 'Patient record was not found for this appointment.');
            $this->dispatch('patient-form-open-failed');

            return false;
        }

        $patientExists = DB::table('patients')->where('id', $patientId)->exists();

        if (! $patientExists) {
            $this->dispatch('flash-message', type: 'error', message: 'Patient record was not found for this appointment.');
            $this->dispatch('patient-form-open-failed');

            return false;
        }

        $this->dispatch('editPatient', id: (int) $patientId, startStep: $startStep);

        return true;
    }

    public function previewPendingPatientRecord(int $patientId, int $startStep = 1): void
    {
        if ($patientId <= 0 || ! DB::table('patients')->where('id', $patientId)->exists()) {
            $this->dispatch('flash-message', type: 'error', message: 'Patient record was not found.');
            $this->dispatch('patient-form-open-failed');

            return;
        }

        $this->dispatch('editPatient', id: $patientId, startStep: $startStep);
    }

    public function linkPendingRequestToExistingPatient(): void
    {
        if (! $this->viewingAppointmentId || ! $this->selectedPendingPatientId) {
            $this->dispatch('flash-message', type: 'error', message: 'Select a patient record to link.');

            return;
        }

        $appointment = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
        if (! $appointment || ! in_array((string) $appointment->status, ['Waiting', 'Scheduled'], true)) {
            $this->dispatch('flash-message', type: 'error', message: 'Only waiting or scheduled appointments can be linked.');

            return;
        }

        $patient = DB::table('patients')->where('id', $this->selectedPendingPatientId)->first();
        if (! $patient) {
            $this->dispatch('flash-message', type: 'error', message: 'Selected patient record was not found.');

            return;
        }

        app(AppointmentService::class)->linkExistingPatient(
            (int) $this->viewingAppointmentId,
            (int) $patient->id,
            $appointment
        );

        $this->refreshAllSections();
        $this->viewAppointment((int) $this->viewingAppointmentId);
        $this->dispatch('flash-message', type: 'success', message: $appointment->status === 'Waiting'
            ? 'Patient record linked. You can now admit the patient.'
            : 'Request linked to existing patient.');
    }

    public function createPatientForPendingRequest(): void
    {
        if (! $this->viewingAppointmentId) {
            return;
        }

        $appointment = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
        if (! $appointment || ! in_array((string) $appointment->status, ['Waiting', 'Scheduled'], true)) {
            $this->dispatch('flash-message', type: 'error', message: 'Only waiting or scheduled appointments can create a patient link.');

            return;
        }

        $requestData = $this->resolveCurrentPendingRequestData($appointment);
        $firstName = trim((string) ($requestData['first_name'] ?? ''));
        $lastName = trim((string) ($requestData['last_name'] ?? ''));

        if ($firstName === '' || $lastName === '') {
            $this->dispatch('flash-message', type: 'error', message: 'Request must have both first and last name before creating a patient record.');

            return;
        }

        app(AppointmentService::class)->createPatientFromRequest(
            (int) $this->viewingAppointmentId,
            $requestData,
            $appointment
        );

        $this->refreshAllSections();
        $this->viewAppointment((int) $this->viewingAppointmentId);
        $this->dispatch('flash-message', type: 'success', message: $appointment->status === 'Waiting'
            ? 'New patient created and linked. You can now admit the patient.'
            : 'New patient created and linked successfully.');
    }

    public function unlinkAppointmentPatient(): void
    {
        if (! $this->viewingAppointmentId) {
            return;
        }

        $appointment = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
        if (! $appointment || ! in_array((string) $appointment->status, ['Waiting', 'Scheduled'], true)) {
            $this->dispatch('flash-message', type: 'error', message: 'Only waiting or scheduled appointments can be unlinked.');

            return;
        }

        $currentPatientId = (int) ($appointment->patient_id ?? 0);
        if ($currentPatientId <= 0) {
            $this->dispatch('flash-message', type: 'error', message: 'This appointment is not linked to a patient record.');

            return;
        }

        app(AppointmentService::class)->unlinkPatient(
            (int) $this->viewingAppointmentId,
            $currentPatientId,
            $appointment
        );

        $this->refreshAllSections();
        $this->viewAppointment((int) $this->viewingAppointmentId);
        $this->dispatch('flash-message', type: 'success', message: 'Appointment unlinked. Staff can now relink to the correct patient record.');
    }

    public function updateStatus($newStatus)
    {
        if (! $this->viewingAppointmentId) {
            return;
        }

        $didUpdate = app(AppointmentService::class)->updateStatus((int) $this->viewingAppointmentId, (string) $newStatus);

        if ($didUpdate) {
            $label = match ($newStatus) {
                'Cancelled' => 'Appointment cancelled.',
                'Completed' => 'Appointment marked as completed.',
                default => "Appointment status updated to '{$newStatus}'.",
            };

            $this->dispatch('flash-message', type: 'success', message: $label);
            $this->refreshAllSections();
            $this->closeAppointmentModal();
        }
    }

    public function saveAppointment(): void
    {
        // Queue reuses the calendar's view-only appointment modal.
    }

    public function closeAppointmentModal(bool $refreshBoard = false)
    {
        $this->showAppointmentModal = false;
        $this->resetAppointmentViewState();

        if ($refreshBoard) {
            $this->refreshAllSections();
        }
    }

    protected function resetAppointmentViewState(): void
    {
        $this->reset([
            'isViewing',
            'isRescheduling',
            'firstName',
            'lastName',
            'middleName',
            'contactNumber',
            'birthDate',
            'selectedService',
            'selectedDate',
            'selectedTime',
            'endTime',
            'appointmentStatus',
            'appointmentDate',
            'viewingAppointmentId',
            'viewingPatientId',
            'viewingBookingForOther',
            'viewingRequesterFirstName',
            'viewingRequesterLastName',
            'viewingRequesterContactNumber',
            'viewingRequesterEmail',
            'viewingRequesterRelationship',
            'selectedPendingPatientId',
            'pendingMatchCandidates',
            'pendingDuplicateWarnings',
            'pendingApprovalSafety',
        ]);
    }

    protected function hydratePendingReviewContext(object $appointment): void
    {
        $previousSelection = $this->selectedPendingPatientId;

        $this->pendingMatchCandidates = [];
        $this->pendingDuplicateWarnings = [];
        $this->selectedPendingPatientId = null;

        if (! empty($appointment->patient_id) || ! in_array(($appointment->status ?? null), ['Waiting', 'Scheduled'], true)) {
            return;
        }

        $requestData = $this->resolveCurrentPendingRequestData($appointment);
        $matcher = app(PatientMatchService::class);

        $this->pendingMatchCandidates = $matcher->suggestMatches($requestData)->all();
        $this->pendingDuplicateWarnings = $matcher->duplicateWarnings($requestData);

        if ($previousSelection !== null) {
            $stillExists = collect($this->pendingMatchCandidates)->contains('id', $previousSelection);
            if ($stillExists) {
                $this->selectedPendingPatientId = (int) $previousSelection;
            }
        }
    }

    protected function resolveCurrentPendingRequestData(object $appointment): array
    {
        $requestBirthDate = null;

        if (Schema::hasColumn('appointments', 'requested_patient_birth_date') && isset($appointment->requested_patient_birth_date)) {
            $requestBirthDate = $appointment->requested_patient_birth_date;
        }

        if (Schema::hasColumn('appointments', 'requester_birth_date') && isset($appointment->requester_birth_date)) {
            $requestBirthDate = $requestBirthDate ?: $appointment->requester_birth_date;
        }

        return [
            'first_name' => $this->appointmentRequestedPatientValue($appointment, 'requested_patient_first_name', 'requester_first_name', 'first_name'),
            'last_name' => $this->appointmentRequestedPatientValue($appointment, 'requested_patient_last_name', 'requester_last_name', 'last_name'),
            'middle_name' => $this->appointmentRequestedPatientValue($appointment, 'requested_patient_middle_name', 'requester_middle_name', 'middle_name'),
            'mobile_number' => $this->appointmentUsesRequestedPatientIdentity($appointment)
                ? ''
                : ($appointment->requester_contact_number ?? $appointment->mobile_number ?? ''),
            'email_address' => $this->appointmentUsesRequestedPatientIdentity($appointment)
                ? ''
                : ($appointment->requester_email ?? $appointment->email_address ?? ''),
            'birth_date' => $this->appointmentRequestedPatientBirthDate($appointment, $requestBirthDate),
        ];
    }

    protected function appointmentUsesRequestedPatientIdentity(object $appointment): bool
    {
        if (! Schema::hasColumn('appointments', 'booking_for_other')) {
            return false;
        }

        return ! empty($appointment->booking_for_other);
    }

    protected function appointmentRequestedPatientValue(
        object $appointment,
        string $requestedField,
        string $fallbackField,
        string $displayField
    ): string {
        if ($this->appointmentUsesRequestedPatientIdentity($appointment)) {
            $requestedValue = trim((string) ($appointment->{$requestedField} ?? ''));
            if ($requestedValue !== '') {
                return $requestedValue;
            }
        }

        $fallbackValue = trim((string) ($appointment->{$fallbackField} ?? ''));
        if ($fallbackValue !== '') {
            return $fallbackValue;
        }

        return trim((string) ($appointment->{$displayField} ?? ''));
    }

    protected function appointmentRequestedPatientBirthDate(object $appointment, mixed $legacyBirthDate): mixed
    {
        if ($this->appointmentUsesRequestedPatientIdentity($appointment) && isset($appointment->requested_patient_birth_date)) {
            return $appointment->requested_patient_birth_date;
        }

        return $legacyBirthDate ?: ($appointment->birth_date ?? null);
    }

    public function render()
    {
        return view('livewire.dashboard.today-schedule');
    }
}
