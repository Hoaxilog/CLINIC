<?php

namespace App\Livewire\Appointment;

use App\Services\AppointmentService;
use App\Services\BlockedSlotService;
use App\Services\CalendarQueryService;
use App\Support\InputSanitizer;
use App\Support\PatientMatchService;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class AppointmentCalendar extends Component
{
    private const PH_CONTACT_RULE = 'regex:/^\d{10}$/';

    protected const SLOT_CAPACITY = 2;

    protected const APPROVED_SLOT_STATUSES = ['Scheduled', 'Waiting', 'Ongoing'];

    protected const REQUEST_SLOT_CAP = 5;

    protected const INACTIVE_APPOINTMENT_STATUSES = ['Cancelled', 'Completed'];

    // ... (All other properties and methods are unchanged) ...
    public $currentDate;

    public $viewType = 'week';

    public $weekDates = [];

    public $timeSlots = [];

    /** @var array<int, array<string, mixed>> */
    public $rescheduleTimeOptions = [];

    /** @var array<int, array<string, mixed>>|Collection<int, object> */
    public $appointments = [];

    /** @var Collection<int, object> */
    protected $occupiedAppointments;

    /** @var Collection<int, object> */
    protected $blockedSlots;

    protected ?bool $blockedSlotsTableExists = null;

    public $occupiedSlotCounts = [];

    public $blockedSlotMap = [];

    public $blockedSlotLookup = [];

    public $blockedChairMap = [];

    public $blockedChairCounts = [];

    public $occupiedChairSlotMap = [];

    protected array $blockedByNameCache = [];

    public $showAppointmentModal = false;

    public $showBlockModal = false;

    public $isRescheduling = false;

    /** @var Collection */
    public $servicesList = [];

    public $firstName = '';

    public $lastName = '';

    public $middleName = '';

    public $recordNumber = '';

    public $contactNumber = '';

    public $birthDate = null;

    public $age = '';

    public $selectedService = '';

    public $selectedDate;      // For the date picker input (Y-m-d)

    public $appointmentDate;   // For the modal display (Y-m-d stored, formatted on display)

    public $selectedTime = null;

    public $endTime = null;

    public $isViewing = false;

    public $viewingAppointmentId = null;

    public $viewingPatientId = null;

    public $appointmentStatus = '';

    public $viewingBookingForOther = false;

    public $viewingRequesterFirstName = '';

    public $viewingRequesterLastName = '';

    public $viewingRequesterContactNumber = '';

    public $viewingRequesterEmail = '';

    public $viewingRequesterRelationship = '';

    public $searchQuery = '';

    public $patientSearchResults = [];

    public $selectedMonthYear; // stores "YYYY-MM" format

    public $selectableMonthYears = [];

    public $activeTab = 'calendar';

    public $isTabLocked = false;

    public $lockedTab = null;

    public $prefillPatientId = null;

    public $prefillPatientLabel = null;

    public $prefillAppointmentPayload = [];

    public $pendingFilterDate = null;

    public $pendingMatchCandidates = [];

    public $pendingDuplicateWarnings = [];

    public $selectedPendingPatientId = null;

    /** @var array<string, mixed> */
    public $pendingApprovalSafety = [];

    public $isBlockMode = false;

    public $blockingSlotId = null;

    public $blockDate = null;

    public $blockStartTime = null;

    public $blockEndTime = null;

    public $blockReason = '';

    public $blockChairId = null;

    protected $rules = [
        'firstName' => ["required", 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
        'lastName' => ["required", 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
        'middleName' => ['nullable', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
        'contactNumber' => ['required', 'string', self::PH_CONTACT_RULE],
        'selectedService' => 'required',
        'selectedDate' => 'required',
        'selectedTime' => 'required',
        'endTime' => 'required',
        'birthDate' => 'required',
    ];

    protected $messages = [
        'contactNumber.regex' => 'Contact number must be exactly 10 digits after +63.',
    ];

    public function mount(?string $initialTab = null)
    {
        $this->occupiedAppointments = collect();
        $this->blockedSlots = collect();

        $this->currentDate = Carbon::now();
        $this->selectedDate = $this->currentDate->format('Y-m-d');

        $requestedTab = $initialTab ?: request()->query('tab');
        if (! in_array($requestedTab, ['pending', 'calendar'], true)) {
            $requestedTab = 'calendar';
        }
        if ($requestedTab === 'pending' && Auth::user()?->role === 3) {
            $requestedTab = 'calendar';
        }
        $this->activeTab = $requestedTab;

        $this->isTabLocked = in_array($initialTab, ['pending', 'calendar'], true);
        $this->lockedTab = $this->isTabLocked ? $requestedTab : null;

        $this->generateWeekDates();
        $this->generateTimeSlots();
        $this->loadAppointments();
        $this->servicesList = DB::table('services')->get();
        $this->sanitizeFormInputs();

        $this->hydratePrefillFromRequest();

        $requestedAppointmentId = (int) request()->query('appointment', 0);
        if ($requestedAppointmentId > 0 && Auth::user()?->role !== 3) {
            $requestedAppointment = DB::table('appointments')
                ->select('id', 'appointment_date')
                ->where('id', $requestedAppointmentId)
                ->first();

            if ($requestedAppointment) {
                $appointmentDate = Carbon::parse($requestedAppointment->appointment_date);
                $this->currentDate = $appointmentDate->copy();
                $this->selectedDate = $appointmentDate->toDateString();
                $this->generateWeekDates();
                $this->loadAppointments();
                $this->viewAppointment($requestedAppointmentId);
            }
        }
    }

    public function generateWeekDates()
    {
        $this->weekDates = [];
        $startOfWeek = $this->currentDate->copy()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $this->weekDates[] = $startOfWeek->copy()->addDays($i);
        }
    }

    public function generateTimeSlots()
    {
        $this->timeSlots = [];
        for ($hour = 9; $hour <= 17; $hour++) {
            $this->timeSlots[] = sprintf('%02d:00', $hour);
        }
    }

    protected function refreshRescheduleTimeOptions(): void
    {
        $this->rescheduleTimeOptions = [];

        if (! $this->isViewing || ! $this->isRescheduling || $this->appointmentStatus !== 'Pending') {
            return;
        }

        $slotDate = $this->selectedDate ?: $this->appointmentDate;
        if (empty($slotDate)) {
            return;
        }

        $durationInMinutes = $this->resolveSelectedServiceDurationMinutes();

        foreach ($this->timeSlots as $time) {
            $slotStart = Carbon::parse($slotDate.' '.$time)->seconds(0);
            $slotEnd = $slotStart->copy()->addMinutes($durationInMinutes);
            $approvedConflicts = $this->countApprovedConflictsForRange($slotStart, $slotEnd);
            $requestCount = $this->countActiveRequestsForExactSlot($slotStart);
            $blockedCapacity = $this->blockedCapacityForRange($slotStart, $slotEnd);
            $isPast = $slotStart->lt(now());
            $isBlocked = $blockedCapacity >= self::SLOT_CAPACITY;
            $effectiveCapacity = max(0, self::SLOT_CAPACITY - $blockedCapacity);
            $isApprovedFull = $approvedConflicts >= $effectiveCapacity;
            $isRequestFull = $requestCount >= self::REQUEST_SLOT_CAP;

            $status = null;
            if ($isPast) {
                $status = 'Past';
            } elseif ($isBlocked) {
                $status = 'Blocked';
            } elseif ($isApprovedFull) {
                $status = 'Full';
            } elseif ($isRequestFull) {
                $status = 'Max Requests';
            }

            $this->rescheduleTimeOptions[] = [
                'value' => $time,
                'label' => Carbon::parse($time)->format('g:i A'),
                'disabled' => $isPast || $isBlocked || $isApprovedFull || $isRequestFull,
                'status' => $status,
            ];
        }

        if (! empty($this->selectedTime)) {
            $selectedTime = substr((string) $this->selectedTime, 0, 5);
            $selectedOption = collect($this->rescheduleTimeOptions)->firstWhere('value', $selectedTime);

            if (($selectedOption['disabled'] ?? false) === true) {
                $this->selectedTime = null;
                $this->endTime = null;
            }
        }
    }

    public function loadAppointments()
    {
        $this->refreshAppointments();
        $this->refreshSlotCounts();
    }

    public function refreshAppointments()
    {
        if (count($this->weekDates) < 7) {
            $this->appointments = collect();
            return;
        }

        $startOfWeek = $this->weekDates[0]->startOfDay();
        $endOfWeek   = $this->weekDates[6]->endOfDay();

        $this->appointments = app(CalendarQueryService::class)
            ->weekAppointments($startOfWeek, $endOfWeek)
            ->map(fn ($appt) => $this->hydrateAppointmentTiming($appt))
            ->values();
    }

    public function refreshSlotCounts()
    {
        if (count($this->weekDates) < 7) {
            $this->occupiedAppointments = collect();
            $this->blockedSlots         = collect();
            $this->occupiedSlotCounts   = [];
            $this->blockedSlotMap       = [];
            $this->blockedSlotLookup    = [];
            $this->blockedChairMap      = [];
            $this->blockedChairCounts   = [];
            $this->occupiedChairSlotMap = [];
            $this->blockedByNameCache   = [];
            return;
        }

        $startOfWeek = $this->weekDates[0]->startOfDay();
        $endOfWeek   = $this->weekDates[6]->endOfDay();
        $cqs         = app(CalendarQueryService::class);

        $this->occupiedAppointments = $cqs->weekOccupied($startOfWeek, $endOfWeek)
            ->map(fn ($appt) => $this->hydrateAppointmentTiming($appt));

        $this->blockedSlots = app(BlockedSlotService::class)->forWeek($startOfWeek, $endOfWeek);
        $this->hydrateBlockedByNameCache();

        $this->rebuildOccupiedSlotCounts();
    }

    protected function hydrateAppointmentTiming(object $appointment): object
    {
        $appointment->duration_in_minutes = $this->durationToMinutes($appointment->duration ?? null);
        $start = Carbon::parse($appointment->appointment_date);
        $appointment->start_date = $start->toDateString();
        $appointment->start_time = $start->format('H:i');
        $appointment->end_time = $start->copy()
            ->addMinutes($appointment->duration_in_minutes)
            ->format('H:i');

        return $appointment;
    }

    protected function durationToMinutes(?string $duration): int
    {
        if (! $duration) {
            return 0;
        }

        sscanf($duration, '%d:%d:%d', $hours, $minutes, $seconds);

        return ((int) $hours * 60) + (int) $minutes;
    }

    public function getAppointmentsForDay($date)
    {
        return collect($this->appointments)->where('start_date', $date->toDateString());
    }

    public function previousWeek()
    {
        $this->currentDate = $this->currentDate->subWeek();
        $this->generateWeekDates();
        $this->loadAppointments();
    }

    public function nextWeek()
    {
        $this->currentDate = $this->currentDate->addWeek();
        $this->generateWeekDates();
        $this->loadAppointments();
    }

    public function changeView($type)
    {
        $this->viewType = $type;
    }

    protected function resetForm()
    {
        $this->resetValidation();
        $this->firstName = '';
        $this->lastName = '';
        $this->middleName = '';
        $this->recordNumber = '';
        $this->contactNumber = '';
        $this->birthDate = null;
        $this->selectedService = '';
        $this->appointmentDate = null;
        $this->selectedTime = null;
        $this->endTime = null;
        $this->isViewing = false;
        $this->viewingAppointmentId = null;
        $this->viewingPatientId = null;
        $this->appointmentStatus = '';
        $this->viewingBookingForOther = false;
        $this->viewingRequesterFirstName = '';
        $this->viewingRequesterLastName = '';
        $this->viewingRequesterContactNumber = '';
        $this->viewingRequesterEmail = '';
        $this->viewingRequesterRelationship = '';
        $this->searchQuery = '';
        $this->patientSearchResults = [];
        $this->pendingMatchCandidates = [];
        $this->pendingDuplicateWarnings = [];
        $this->selectedPendingPatientId = null;
        $this->pendingApprovalSafety = [];
        $this->isRescheduling = false;
        $this->rescheduleTimeOptions = [];

    }

    protected function resetBlockForm(): void
    {
        $this->resetValidation([
            'blockDate',
            'blockStartTime',
            'blockEndTime',
            'blockReason',
            'blockChairId',
        ]);
        $this->blockingSlotId = null;
        $this->blockDate = $this->selectedDate ?: now()->toDateString();
        $this->blockStartTime = null;
        $this->blockEndTime = null;
        $this->blockReason = '';
        $this->blockChairId = null;
    }

    public function openAppointmentModal($date, $time)
    {
        $this->resetForm();
        $this->selectedDate = $date;
        $this->appointmentDate = $date;
        $this->selectedTime = $time;
        $this->showAppointmentModal = true;

        if ($this->prefillPatientId) {
            $this->selectPatient($this->prefillPatientId);
        } elseif (! empty($this->prefillAppointmentPayload)) {
            $this->applyPrefillAppointmentPayload();
        }
    }

    public function closeAppointmentModal(bool $resetForm = false)
    {
        $this->showAppointmentModal = false;

        if ($resetForm) {
            $this->resetForm();
        }
    }

    public function toggleBlockMode(): void
    {
        if (! $this->blockedSlotsEnabled()) {
            $this->dispatch('flash-message', type: 'error', message: 'Blocked slots table is not available yet. Please run migrations.');

            return;
        }

        $this->isBlockMode = ! $this->isBlockMode;

        if ($this->isBlockMode) {
            $this->closeAppointmentModal(true);
            $this->dispatch('flash-message', type: 'info', message: 'Select one calendar slot to block.');
        }
    }

    public function blockSlot(string $date, string $time, ?int $chairId = null): void
    {
        if (! $this->blockedSlotsEnabled()) {
            $this->dispatch('flash-message', type: 'error', message: 'Blocked slots table is not available yet. Please run migrations.');

            return;
        }

        $normalizedChairId = in_array($chairId, [1, 2], true) ? $chairId : null;

        if ($normalizedChairId !== null && $this->hasAppointmentsInChairSlot($date, $time, $normalizedChairId)) {
            $this->dispatch('flash-message', type: 'error', message: 'Cannot block a chair lane that already has an appointment.');

            return;
        }

        $result = app(BlockedSlotService::class)->quickBlock(
            $date,
            $time,
            $this->occupiedSlotCounts,
            $this->blockedSlotMap,
            $normalizedChairId
        );

        if (! $result['ok']) {
            if ($result['error'] ?? false) {
                $this->dispatch('flash-message', type: 'error', message: $result['message']);
            } else {
                $this->dispatch('flash-message', type: 'info', message: $result['message']);
            }
            return;
        }

        $this->refreshSlotCounts();
        $this->dispatch('flash-message', type: 'success', message: $result['message']);
    }

    public function openBlockSlotModal(?string $date = null, ?string $time = null): void
    {
        if (! $this->blockedSlotsEnabled()) {
            $this->dispatch('flash-message', type: 'error', message: 'Blocked slots table is not available yet. Please run migrations.');

            return;
        }

        $this->resetBlockForm();
        $this->showBlockModal = true;
        $this->blockDate = $date ?: ($this->selectedDate ?: now()->toDateString());
        if ($time) {
            $formatted = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
            $this->blockStartTime = $formatted;
            $this->blockEndTime = Carbon::parse($formatted)->addMinutes(60)->format('H:i');
        }
    }

    public function closeBlockSlotModal(bool $reset = false): void
    {
        $this->showBlockModal = false;

        if ($reset) {
            $this->resetBlockForm();
        }
    }

    public function editBlockedSlot(int $blockedSlotId): void
    {
        if (! $this->blockedSlotsEnabled()) {
            return;
        }

        $slot = app(BlockedSlotService::class)->find($blockedSlotId);
        if (! $slot) {
            return;
        }

        $this->resetBlockForm();
        $this->blockingSlotId  = (int) $slot->id;
        $this->blockDate       = $slot->date;
        $this->blockStartTime  = substr((string) $slot->start_time, 0, 5);
        $this->blockEndTime    = substr((string) $slot->end_time, 0, 5);
        $this->blockReason     = (string) ($slot->reason ?? '');
        $this->blockChairId    = isset($slot->chair_id) ? (int) $slot->chair_id : null;
        $this->showBlockModal  = true;
    }

    public function saveBlockedSlot(): void
    {
        if (! $this->blockedSlotsEnabled()) {
            $this->dispatch('flash-message', type: 'error', message: 'Blocked slots table is not available yet. Please run migrations.');
            return;
        }

        $this->blockReason = InputSanitizer::sanitizeSentenceCase($this->blockReason ?? '', true, '.,&()/:;!?-');

        $this->validate([
            'blockDate'      => 'required|date',
            'blockStartTime' => 'required|date_format:H:i',
            'blockEndTime'   => 'required|date_format:H:i|after:blockStartTime',
            'blockReason'    => ['nullable', 'string', 'max:255', "regex:/^[\\pL\\pM\\pN\\s'\",.&()\\/:;!?-]+$/u"],
        ]);

        $start = Carbon::parse($this->blockDate.' '.$this->blockStartTime);
        $end   = Carbon::parse($this->blockDate.' '.$this->blockEndTime);

        if (! $this->isAlignedToSlot($start) || ! $this->isAlignedToSlot($end)) {
            $this->addError('blockEndTime', 'Please use 1-hour boundaries (e.g. 10:00, 11:00).');
            return;
        }

        $result = app(BlockedSlotService::class)->save(
            $this->blockingSlotId,
            $this->blockDate,
            $this->blockStartTime,
            $this->blockEndTime,
            $this->blockReason,
            $this->blockChairId
        );

        if (! $result['ok']) {
            $this->addError($result['field'], $result['message']);
            return;
        }

        $this->refreshSlotCounts();
        $this->closeBlockSlotModal(true);
        $this->dispatch('flash-message', type: 'success', message: $result['message']);
    }

    public function unblockSlot(int $blockedSlotId): void
    {
        if (! $this->blockedSlotsEnabled()) {
            return;
        }

        $slot = app(BlockedSlotService::class)->find($blockedSlotId);
        if (! $slot) {
            return;
        }

        if (! $this->canUnblockSlot($slot)) {
            $this->dispatch('flash-message', type: 'error', message: 'Past blocked slots can no longer be unblocked.');
            return;
        }

        app(BlockedSlotService::class)->delete($blockedSlotId);
        $this->refreshSlotCounts();
        $this->closeBlockSlotModal(true);
        $this->dispatch('flash-message', type: 'success', message: 'Slot unblocked successfully.');
    }

    public function updatedSelectedService($serviceId)
    {
        $service = $this->servicesList->firstWhere('id', $serviceId);

        if ($service && ! empty($this->selectedTime)) {
            [$hours, $minutes, $seconds] = explode(':', $service->duration);
            $this->endTime = Carbon::parse($this->selectedTime)
                ->addHours((int) $hours)
                ->addMinutes((int) $minutes)
                ->format('H:i');
        } else {
            $this->endTime = null;
        }

        $this->refreshRescheduleTimeOptions();
    }

    public function updatedSelectedTime($value): void
    {
        if (! empty($this->selectedService)) {
            $this->updatedSelectedService($this->selectedService);
        }
    }

    public function updatedSelectedDate($value): void
    {
        if (! empty($value) && ! $this->isViewing) {
            $this->appointmentDate = $value;
        }

        $this->refreshRescheduleTimeOptions();
    }

    public function updatedBirthDate($value): void
    {
        $this->resetValidation('birthDate');
    }

    public function updated($propertyName): void
    {
        $this->sanitizeUpdatedProperty(is_string($propertyName) ? $propertyName : null);

        $clearableFields = [
            'firstName',
            'middleName',
            'lastName',
            'contactNumber',
            'birthDate',
            'selectedService',
            'selectedDate',
            'selectedTime',
            'endTime',
            'blockDate',
            'blockStartTime',
            'blockEndTime',
            'blockReason',
        ];

        if (in_array($propertyName, $clearableFields, true)) {
            $this->resetValidation($propertyName);
        }

        if (in_array($propertyName, ['selectedService', 'selectedDate', 'selectedTime', 'endTime'], true)) {
            $this->resetValidation('conflict');
        }
    }

    public function saveAppointment()
    {
        if ($this->isViewing) {
            return;
        }

        $this->sanitizeFormInputs();

        $this->validate([
            'firstName'       => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'lastName'        => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'contactNumber'   => ['required', 'string', self::PH_CONTACT_RULE],
            'selectedService' => 'required',
            'selectedDate'    => 'required',
            'selectedTime'    => 'required',
            'birthDate'       => 'required',
        ], [
            'contactNumber.regex' => 'Contact number must be exactly 10 digits after +63.',
        ]);

        try {
            $service = collect($this->servicesList)->firstWhere('id', $this->selectedService);
            if (! $service) {
                $this->addError('selectedService', 'Please select a valid service.');
                return;
            }

            $cqs              = app(CalendarQueryService::class);
            $bss              = app(BlockedSlotService::class);
            $durationMinutes  = $this->durationToMinutes($service->duration);
            $appointmentDate  = $this->selectedDate ?: $this->appointmentDate;
            $proposedStart    = Carbon::parse($appointmentDate)->setTimeFromTimeString($this->selectedTime);
            $proposedEnd      = $proposedStart->copy()->addMinutes($durationMinutes);

            $blockedCapacity = $this->blockedCapacityForRange($proposedStart, $proposedEnd);

            if ($blockedCapacity >= self::SLOT_CAPACITY) {
                $this->addError('conflict', 'This time range includes blocked slots.');
                return;
            }

            $clinicClose = Carbon::parse($appointmentDate)->setTime(18, 0, 0);
            if ($proposedEnd->gt($clinicClose)) {
                $this->addError('conflict', 'This service cannot start at this time as it would end after clinic hours (6:00 PM).');
                return;
            }

            $remainingCapacity = max(0, self::SLOT_CAPACITY - $blockedCapacity);

            if ($cqs->countConflicts($proposedStart, $proposedEnd, self::APPROVED_SLOT_STATUSES) >= $remainingCapacity) {
                $this->addError('conflict', 'This time slot already has two approved appointments.');
                return;
            }

            $normalizedBirthDate = $this->normalizeBirthDate($this->birthDate);

            app(AppointmentService::class)->createScheduled([
                'first_name'       => $this->firstName,
                'last_name'        => $this->lastName,
                'middle_name'      => $this->middleName,
                'contact_number'   => $this->contactNumber,
                'birth_date'       => $normalizedBirthDate,
                'service_id'       => $this->selectedService,
                'appointment_date' => $appointmentDate,
                'time'             => $this->selectedTime,
            ]);

            $this->dispatch('flash-message', type: 'success', message: 'Appointment booked successfully!');
            $this->clearPrefill();
            $this->loadAppointments();
            $this->closeAppointmentModal(true);

        } catch (\Throwable $th) {
            $this->dispatch('flash-message', type: 'error', message: 'Error saving: '.$th->getMessage());
        }
    }



    public function validateAppointmentForConfirm()
    {
        $this->sanitizeFormInputs();

        $this->validate([
            'firstName' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'lastName' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'contactNumber' => ['required', 'string', self::PH_CONTACT_RULE],
            'selectedService' => 'required',
            'selectedDate' => 'required',
            'selectedTime' => 'required',
            'birthDate' => 'required',
        ], [
            'contactNumber.regex' => 'Contact number must be exactly 10 digits after +63.',
        ]);

        return true;
    }

    protected function normalizeBirthDate(?string $value): string
    {
        if (! is_string($value)) {
            throw new InvalidFormatException('Birth date is required.');
        }

        $value = trim($value);
        if ($value === '') {
            throw new InvalidFormatException('Birth date is required.');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new InvalidFormatException('Birth date is not a valid date.');
        }
    }

    public function isSlotOccupied($date, $time)
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;
        if (($this->blockedSlotMap[$key] ?? false) === true) {
            return true;
        }

        return (($this->occupiedSlotCounts[$key] ?? 0) + ($this->blockedChairCounts[$key] ?? 0)) >= self::SLOT_CAPACITY;
    }

    public function isSlotBlocked($date, $time): bool
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;

        return ($this->blockedSlotMap[$key] ?? false) === true
            || (($this->blockedChairCounts[$key] ?? 0) >= self::SLOT_CAPACITY);
    }

    public function hasAppointmentsInSlot(string $date, string $time): bool
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;

        return ($this->occupiedSlotCounts[$key] ?? 0) > 0;
    }

    public function getBlockedSlotAt(string $date, string $time, ?int $chairId = null): ?object
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;

        if (in_array($chairId, [1, 2], true)) {
            return $this->blockedSlotLookup[$key.'|chair:'.$chairId]
                ?? $this->blockedSlotLookup[$key.'|all']
                ?? null;
        }

        return $this->blockedSlotLookup[$key.'|all'] ?? null;
    }

    public function isChairBlocked(string $date, string $time, int $chairId): bool
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;

        return ($this->blockedSlotMap[$key] ?? false) === true
            || (($this->blockedChairMap[$key][$chairId] ?? false) === true);
    }

    public function hasAppointmentsInChairSlot(string $date, string $time, int $chairId): bool
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;

        return ($this->occupiedChairSlotMap[$key][$chairId] ?? false) === true;
    }

    public function canUnblockSlot(?object $blockedSlot): bool
    {
        if (! $blockedSlot) {
            return false;
        }

        try {
            $slotStart = Carbon::parse($blockedSlot->date.' '.$blockedSlot->start_time)->seconds(0);
        } catch (\Throwable) {
            return false;
        }

        return $slotStart->gt(now());
    }

    public function blockedByLabel(?object $blockedSlot): string
    {
        $label = trim((string) ($blockedSlot->created_by ?? ''));
        if ($label === '') {
            return 'Unknown staff';
        }

        if (isset($this->blockedByNameCache[$label])) {
            return $this->blockedByNameCache[$label];
        }

        if (str_contains($label, '@')) {
            return 'Unknown staff';
        }

        return $label;
    }

    protected function hydrateBlockedByNameCache(): void
    {
        $this->blockedByNameCache = [];

        $actors = collect($this->blockedSlots)
            ->pluck('created_by')
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->values();

        if ($actors->isEmpty()) {
            return;
        }

        $users = DB::table('users')
            ->select('username', 'email', 'first_name', 'last_name')
            ->where(function ($query) use ($actors) {
                $query->whereIn('username', $actors)
                    ->orWhereIn('email', $actors);
            })
            ->get();

        foreach ($users as $user) {
            $fullName = trim(trim((string) ($user->first_name ?? '')).' '.trim((string) ($user->last_name ?? '')));
            if ($fullName === '') {
                continue;
            }

            $username = trim((string) ($user->username ?? ''));
            $email = trim((string) ($user->email ?? ''));

            if ($username !== '') {
                $this->blockedByNameCache[$username] = $fullName;
            }

            if ($email !== '') {
                $this->blockedByNameCache[$email] = $fullName;
            }
        }
    }

    protected function rebuildOccupiedSlotCounts(): void
    {
        $slotCounts = [];
        $blockedMap = [];
        $blockedLookup = [];
        $blockedChairMap = [];
        $blockedChairCounts = [];
        $occupiedChairSlotMap = [];

        foreach ($this->occupiedAppointments as $appointment) {
            $slotCursor = Carbon::parse($appointment->start_date.' '.$appointment->start_time)->seconds(0);
            $slotEnd = $slotCursor->copy()->addMinutes((int) $appointment->duration_in_minutes);

            while ($slotCursor < $slotEnd) {
                $key = $slotCursor->format('Y-m-d H:i');
                $slotCounts[$key] = ($slotCounts[$key] ?? 0) + 1;
                $slotCursor->addMinutes(30);
            }
        }

        $appointmentsByDate = $this->occupiedAppointments
            ->groupBy('start_date')
            ->map(fn (Collection $appointments) => $appointments->sortBy([
                ['start_time', 'asc'],
                ['duration_in_minutes', 'desc'],
                ['id', 'asc'],
            ])->values());

        foreach ($appointmentsByDate as $date => $appointments) {
            $appointmentClusters = [];
            $currentCluster = null;

            foreach ($appointments as $appointment) {
                $appointmentStart = Carbon::parse($appointment->start_time)->seconds(0);
                $appointmentEnd = Carbon::parse($appointment->end_time)->seconds(0);

                if ($currentCluster === null || $appointmentStart->greaterThanOrEqualTo($currentCluster['end'])) {
                    if ($currentCluster !== null) {
                        $appointmentClusters[] = $currentCluster;
                    }

                    $currentCluster = [
                        'items' => [$appointment],
                        'end' => $appointmentEnd,
                    ];
                } else {
                    $currentCluster['items'][] = $appointment;
                    if ($appointmentEnd->greaterThan($currentCluster['end'])) {
                        $currentCluster['end'] = $appointmentEnd;
                    }
                }
            }

            if ($currentCluster !== null) {
                $appointmentClusters[] = $currentCluster;
            }

            foreach ($appointmentClusters as $cluster) {
                $laneEnds = [];

                foreach ($cluster['items'] as $appointment) {
                    $appointmentStart = Carbon::parse($appointment->start_time)->seconds(0);
                    $appointmentEnd = Carbon::parse($appointment->end_time)->seconds(0);
                    $laneIndex = null;

                    foreach ($laneEnds as $index => $laneEnd) {
                        if ($appointmentStart->greaterThanOrEqualTo($laneEnd)) {
                            $laneIndex = $index;
                            break;
                        }
                    }

                    if ($laneIndex === null) {
                        $laneIndex = count($laneEnds);
                    }

                    $laneEnds[$laneIndex] = $appointmentEnd;

                    $slotCursor = Carbon::parse($date.' '.$appointment->start_time)->seconds(0);
                    $slotEnd = Carbon::parse($date.' '.$appointment->end_time)->seconds(0);
                    $chairId = min(self::SLOT_CAPACITY, $laneIndex + 1);

                    while ($slotCursor < $slotEnd) {
                        $occupiedChairSlotMap[$slotCursor->format('Y-m-d H:i')][$chairId] = true;
                        $slotCursor->addMinutes(30);
                    }
                }
            }
        }

        foreach ($this->blockedSlots as $slot) {
            $slotCursor = Carbon::parse($slot->date.' '.$slot->start_time)->seconds(0);
            $slotEnd = Carbon::parse($slot->date.' '.$slot->end_time)->seconds(0);
            $chairId = isset($slot->chair_id) ? (int) $slot->chair_id : null;
            $isChairSpecific = in_array($chairId, [1, 2], true);

            while ($slotCursor < $slotEnd) {
                $key = $slotCursor->format('Y-m-d H:i');
                if (! $isChairSpecific) {
                    $blockedMap[$key] = true;
                    $blockedChairCounts[$key] = self::SLOT_CAPACITY;
                    $blockedLookup[$key.'|all'] = $slot;
                } else {
                    $blockedChairMap[$key][$chairId] = true;
                    $blockedChairCounts[$key] = count($blockedChairMap[$key]);
                    $blockedLookup[$key.'|chair:'.$chairId] = $slot;
                }
                $slotCursor->addMinutes(30);
            }
        }

        $this->occupiedSlotCounts = $slotCounts;
        $this->blockedSlotMap = $blockedMap;
        $this->blockedSlotLookup = $blockedLookup;
        $this->blockedChairMap = $blockedChairMap;
        $this->blockedChairCounts = $blockedChairCounts;
        $this->occupiedChairSlotMap = $occupiedChairSlotMap;
    }

    protected function hasBlockedConflict(Carbon $proposedStart, Carbon $proposedEnd): bool
    {
        return $this->blockedCapacityForRange($proposedStart, $proposedEnd) >= self::SLOT_CAPACITY;
    }

    protected function blockedCapacityForRange(Carbon $proposedStart, Carbon $proposedEnd): int
    {
        return app(BlockedSlotService::class)->blockedCapacityForRange($proposedStart, $proposedEnd, self::SLOT_CAPACITY);
    }

    protected function blockedSlotsEnabled(): bool
    {
        return app(BlockedSlotService::class)->isEnabled();
    }

    protected function resolveSelectedServiceDurationMinutes(): int
    {
        $service = $this->servicesList->firstWhere('id', $this->selectedService);

        return max(60, $this->durationToMinutes($service?->duration));
    }

    protected function countApprovedConflictsForRange(Carbon $proposedStart, Carbon $proposedEnd): int
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $existingEnd = DB::raw(
                "datetime(appointments.appointment_date, '+' || "
                . "(CAST(substr(services.duration, 1, 2) AS INTEGER) * 3600 "
                . "+ CAST(substr(services.duration, 4, 2) AS INTEGER) * 60 "
                . "+ CAST(substr(services.duration, 7, 2) AS INTEGER)) || ' seconds')"
            );
        } else {
            $existingEnd = DB::raw('DATE_ADD(appointments.appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND)');
        }

        return DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $this->viewingAppointmentId)
            ->where(function ($query) use ($proposedStart, $proposedEnd, $existingEnd) {
                $query->where('appointments.appointment_date', '<', $proposedEnd->toDateTimeString())
                    ->where($existingEnd, '>', $proposedStart->toDateTimeString())
                    ->whereIn('appointments.status', self::APPROVED_SLOT_STATUSES);
            })
            ->count();
    }

    protected function countActiveRequestsForExactSlot(Carbon $slotStart): int
    {
        return DB::table('appointments')
            ->where('id', '!=', $this->viewingAppointmentId)
            ->where('appointment_date', $slotStart->toDateTimeString())
            ->whereNotIn('status', self::INACTIVE_APPOINTMENT_STATUSES)
            ->count();
    }

    public function viewAppointment($appointmentId)
    {
        // Fast path: use already loaded weekly data.
        $appointment = collect($this->appointments)->firstWhere('id', (int) $appointmentId);

        // Fallback: hit the DB for any appointment not in the current week.
        if (! $appointment) {
            $appointment = app(CalendarQueryService::class)->findForView((int) $appointmentId);
        }

        if ($appointment) {
            $this->resetForm();

            // 2. Populate the form fields
            $this->firstName = $appointment->first_name;
            $this->lastName = $appointment->last_name;
            $this->middleName = $appointment->middle_name;
            $this->contactNumber = $appointment->mobile_number;
            $this->birthDate = $appointment->birth_date;
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
            $this->pendingApprovalSafety = $appointment->status === 'Pending'
                ? $this->buildPendingApprovalSafetySummary($appointment)
                : [];

            // 3. Format Dates and Times
            $dt = Carbon::parse($appointment->appointment_date);
            $this->appointmentDate = $dt->toDateString();  // NEW: Use this
            $this->selectedTime = $dt->format('H:i:s');
            $this->selectedService = $appointment->service_id;

            // Keep the internal value in 24-hour time; the view formats it for display.
            $durationInMinutes = $this->durationToMinutes($appointment->duration);
            $this->endTime = $dt->copy()->addMinutes($durationInMinutes)->format('H:i');

            // 4. Set mode to Viewing and open modal
            $this->isViewing = true;
            $this->showAppointmentModal = true;
        }
    }

    // public function updatedBirthDate($value)
    // {
    //     if ($value) {
    //         $this->age = Carbon::parse($value)->age;
    //     } else {
    //         $this->age = '';
    //     }
    // }

    public function updateStatus($newStatus)
    {
        if (! $this->viewingAppointmentId) {
            return;
        }

        $old = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
        if (! $old) {
            return;
        }

        if ($old->status === 'Pending' && $newStatus === 'Scheduled') {
            $safety = $this->buildPendingApprovalSafetySummary($old);
            if (! ($safety['can_approve'] ?? false)) {
                $message = (string) ($safety['summary'] ?? 'This request cannot be approved right now.');
                $this->dispatch('flash-message', type: 'error', message: $message);
                $this->addError('conflict', $message);
                $this->pendingApprovalSafety = $safety;
                return;
            }
        }

        $didUpdate = app(AppointmentService::class)->updateStatus((int) $this->viewingAppointmentId, (string) $newStatus);
        if ($didUpdate) {
            $label = match ($newStatus) {
                'Scheduled' => 'Appointment approved and scheduled.',
                'Cancelled' => 'Appointment cancelled.',
                'Completed' => 'Appointment marked as completed.',
                default     => "Appointment status updated to '{$newStatus}'." ,
            };
            $this->dispatch('flash-message', type: 'success', message: $label);
            $this->loadAppointments();
            $this->closeAppointmentModal();
        }
    }

    public function setActiveTab($tab)
    {
        if ($this->isTabLocked) {
            return;
        }

        if ($tab === 'pending' && Auth::user()?->role === 3) {
            $this->activeTab = 'calendar';

            return;
        }
        $this->activeTab = $tab;
    }

    public function getPendingApprovals()
    {
        if (Auth::user()?->role === 3) {
            return collect();
        }

        return app(CalendarQueryService::class)->pendingAppointments($this->pendingFilterDate);
    }

    public function clearPendingFilterDate()
    {
        $this->pendingFilterDate = null;
    }

    public function approveAppointment($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $old = DB::table('appointments')->where('id', $appointmentId)->first();
        if (! $old) {
            return;
        }

        $safety = $this->buildPendingApprovalSafetySummary($old);
        if (! ($safety['can_approve'] ?? false)) {
            $this->dispatch('flash-message', type: 'error', message: (string) ($safety['summary'] ?? 'This request cannot be approved right now.'));
            return;
        }

        $didUpdate = app(AppointmentService::class)->updateStatus((int) $appointmentId, 'Scheduled');
        if ($didUpdate) {
            $this->dispatch('flash-message', type: 'success', message: 'Appointment request approved.');
            $this->loadAppointments();
        }
    }

    public function rejectAppointment($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        app(AppointmentService::class)->updateStatus((int) $appointmentId, 'Cancelled');
        $this->loadAppointments();
        $this->dispatch('flash-message', type: 'info', message: 'Appointment request rejected.');
    }

    public function beginPendingReschedule(): void
    {
        if (! $this->isViewing || $this->appointmentStatus !== 'Pending' || Auth::user()?->role === 3) {
            return;
        }

        $this->isRescheduling = true;
        $this->selectedDate = $this->appointmentDate ?: $this->selectedDate;
        if (! empty($this->selectedTime)) {
            $this->selectedTime = substr((string) $this->selectedTime, 0, 5);
        }
        $this->updatedSelectedService($this->selectedService);
        $this->refreshRescheduleTimeOptions();
        $this->resetValidation(['selectedDate', 'selectedTime', 'conflict']);
    }

    public function cancelPendingReschedule(): void
    {
        $this->isRescheduling = false;
        $this->rescheduleTimeOptions = [];
        $this->resetValidation(['selectedDate', 'selectedTime', 'conflict']);

        if ($this->viewingAppointmentId) {
            $this->viewAppointment((int) $this->viewingAppointmentId);
        }
    }

    public function savePendingReschedule(): void
    {
        if (! $this->viewingAppointmentId || ! $this->isViewing || $this->appointmentStatus !== 'Pending') {
            return;
        }

        if (Auth::user()?->role === 3) {
            return;
        }

        $this->validate([
            'selectedDate' => 'required|date|after_or_equal:today',
            'selectedTime' => 'required|date_format:H:i',
            'selectedService' => 'required',
        ]);

        $service = collect($this->servicesList)->firstWhere('id', $this->selectedService);
        if (! $service) {
            $this->addError('selectedService', 'Please select a valid service.');

            return;
        }

        $durationInMinutes = $this->durationToMinutes($service->duration);
        $proposedStart = Carbon::parse($this->selectedDate.' '.$this->selectedTime)->seconds(0);
        $proposedEnd = $proposedStart->copy()->addMinutes($durationInMinutes);

        $blockedCapacity = $this->blockedCapacityForRange($proposedStart, $proposedEnd);

        if ($blockedCapacity >= self::SLOT_CAPACITY) {
            $this->addError('conflict', 'This time range includes blocked slots.');

            return;
        }

        $clinicClose = Carbon::parse($this->selectedDate)->setTime(18, 0, 0);
        if ($proposedEnd->gt($clinicClose)) {
            $this->addError('conflict', 'This service cannot start at this time as it would end after clinic hours (6:00 PM).');

            return;
        }

        $approvedConflicts = $this->countApprovedConflictsForRange($proposedStart, $proposedEnd);
        $remainingCapacity = max(0, self::SLOT_CAPACITY - $blockedCapacity);

        if ($approvedConflicts >= $remainingCapacity) {
            $this->addError('conflict', 'This time slot already has two approved appointments.');

            return;
        }

        $requestCountInTargetSlot = $this->countActiveRequestsForExactSlot($proposedStart);

        if ($requestCountInTargetSlot >= self::REQUEST_SLOT_CAP) {
            $this->addError('conflict', 'This time slot already reached the maximum of 5 requests.');

            return;
        }

        app(AppointmentService::class)->reschedulePending(
            (int) $this->viewingAppointmentId,
            $proposedStart->toDateTimeString(),
            (int) $this->selectedService
        );

        $this->isRescheduling = false;
        $this->loadAppointments();
        $this->viewAppointment((int) $this->viewingAppointmentId);
        $this->dispatch('flash-message', type: 'success', message: 'Appointment rescheduled and approved successfully.');
    }



    protected function hydratePendingReviewContext(object $appointment): void
    {
        // Snapshot the user's current selection before clearing.
        $previousSelection = $this->selectedPendingPatientId;

        $this->pendingMatchCandidates       = [];
        $this->pendingDuplicateWarnings     = [];
        $this->selectedPendingPatientId     = null;

        if (! empty($appointment->patient_id) || ! in_array(($appointment->status ?? null), ['Waiting', 'Scheduled'], true)) {
            return;
        }

        $requestData = $this->resolveCurrentPendingRequestData($appointment);
        $matcher = app(PatientMatchService::class);

        $this->pendingMatchCandidates   = $matcher->suggestMatches($requestData)->all();
        $this->pendingDuplicateWarnings = $matcher->duplicateWarnings($requestData);

        // Restore from DB-linked patient if available.
        if (! empty($appointment->patient_id)) {
            $this->selectedPendingPatientId = (int) $appointment->patient_id;
            return;
        }

        // Restore the user's previously-selected candidate if it still appears in the new list.
        if ($previousSelection !== null) {
            $stillExists = collect($this->pendingMatchCandidates)->contains('id', $previousSelection);
            if ($stillExists) {
                $this->selectedPendingPatientId = (int) $previousSelection;
                return;
            }
        }
    }


    /**
     * @return array<string, mixed>
     */
    protected function buildPendingApprovalSafetySummary(object $appointment): array
    {
        $appointmentAt = Carbon::parse($appointment->appointment_date)->seconds(0);
        $serviceId     = (int) ($appointment->service_id ?? 0);
        $service       = $this->servicesList->firstWhere('id', $serviceId);

        if (! $service && $serviceId > 0) {
            $service = DB::table('services')->where('id', $serviceId)->first();
        }

        $cqs               = app(CalendarQueryService::class);
        $durationInMinutes = $this->durationToMinutes($service->duration ?? null);
        $appointmentEnd    = $appointmentAt->copy()->addMinutes($durationInMinutes);

        $approvedOverlaps = $cqs->overlappingApproved($appointment->id, $appointmentAt, $appointmentEnd)
            ->filter(function (object $conflict) use ($appointmentAt, $appointmentEnd): bool {
                $conflictStart = Carbon::parse($conflict->appointment_date)->seconds(0);
                $conflictEnd   = $conflictStart->copy()->addMinutes($this->durationToMinutes($conflict->duration ?? null));
                return $conflictStart < $appointmentEnd && $conflictEnd > $appointmentAt;
            })
            ->map(function (object $conflict): array {
                $conflictAt = Carbon::parse($conflict->appointment_date);
                return [
                    'id'           => (int) $conflict->id,
                    'patient_name' => trim((string) (($conflict->first_name ?? '').' '.($conflict->last_name ?? ''))),
                    'service_name' => (string) ($conflict->service_name ?? 'Appointment'),
                    'status'       => (string) ($conflict->status ?? ''),
                    'time'         => $conflictAt->format('g:i A'),
                ];
            })
            ->values()
            ->all();

        $sameTimePendingCount = $cqs->countSameTimePending($appointment->id, $appointmentAt->toDateTimeString());
        $sameDayActiveCount   = $cqs->countSameDayActive($appointmentAt, self::INACTIVE_APPOINTMENT_STATUSES);
        $blockedCapacity      = $durationInMinutes > 0
            ? $this->blockedCapacityForRange($appointmentAt, $appointmentEnd)
            : 0;
        $hasBlockedConflict   = $blockedCapacity >= self::SLOT_CAPACITY;

        $approvedOverlapCount  = count($approvedOverlaps);
        $remainingCapacity     = max(0, self::SLOT_CAPACITY - $approvedOverlapCount - $blockedCapacity);
        $canApprove            = ! $hasBlockedConflict && $approvedOverlapCount < max(0, self::SLOT_CAPACITY - $blockedCapacity);

        if ($hasBlockedConflict) {
            $headline = 'Reschedule before approval';
            $summary  = 'This request overlaps a blocked slot, so approving it now would create an unsafe schedule.';
            $tone     = 'rose';
        } elseif ($approvedOverlapCount >= self::SLOT_CAPACITY) {
            $headline = 'Slot already full';
            $summary  = 'This request overlaps '.self::SLOT_CAPACITY.' approved appointments. Reschedule it before approval.';
            $tone     = 'amber';
        } elseif ($approvedOverlapCount === self::SLOT_CAPACITY - 1) {
            $headline = 'Safe to approve with caution';
            $summary  = 'One approved appointment already overlaps this time. You can still approve, but this will use the last available chair slot.';
            $tone     = 'amber';
        } else {
            $headline = 'Safe to approve';
            $summary  = 'No approved appointment conflicts were found for this request, so staff can approve it without leaving this review.';
            $tone     = 'emerald';
        }

        return [
            'can_approve'              => $canApprove,
            'headline'                 => $headline,
            'summary'                  => $summary,
            'tone'                     => $tone,
            'approved_overlap_count'   => $approvedOverlapCount,
            'remaining_capacity'       => $remainingCapacity,
            'same_time_pending_count'  => $sameTimePendingCount,
            'same_day_active_count'    => $sameDayActiveCount,
            'has_blocked_conflict'     => $hasBlockedConflict,
            'overlapping_appointments' => $approvedOverlaps,
        ];
    }

    public function linkPendingRequestToExistingPatient(): void
    {
        if (! $this->viewingAppointmentId || ! $this->selectedPendingPatientId) {
            $this->dispatch('flash-message', type: 'error', message: 'Select a patient record to link.');
            return;
        }

        $appointment = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
        if (! $appointment || ! in_array($appointment->status, ['Waiting', 'Scheduled'], true)) {
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

        $this->loadAppointments();
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
        if (! $appointment || ! in_array($appointment->status, ['Waiting', 'Scheduled'], true)) {
            $this->dispatch('flash-message', type: 'error', message: 'Only waiting or scheduled appointments can create a patient link.');
            return;
        }

        $requestData = $this->resolveCurrentPendingRequestData($appointment);
        $firstName   = trim((string) ($requestData['first_name'] ?? ''));
        $lastName    = trim((string) ($requestData['last_name'] ?? ''));

        if ($firstName === '' || $lastName === '') {
            $this->dispatch('flash-message', type: 'error', message: 'Request must have both first and last name before creating a patient record.');
            return;
        }

        $patientId = app(AppointmentService::class)->createPatientFromRequest(
            (int) $this->viewingAppointmentId,
            $requestData,
            $appointment
        );

        $this->loadAppointments();
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

        $this->loadAppointments();
        $this->viewAppointment((int) $this->viewingAppointmentId);
        $this->dispatch('flash-message', type: 'success', message: 'Appointment unlinked. Staff can now relink to the correct patient record.');
    }

    protected function syncRequesterAccountPatientLink(object $appointment, int $patientId): void
    {
        app(AppointmentService::class)->syncRequesterAccountPatientLink($appointment, $patientId);
    }

    protected function clearRequesterAccountPatientLinkIfMatched(object $appointment, int $currentPatientId): void
    {
        app(AppointmentService::class)->clearRequesterAccountPatientLinkIfMatched($appointment, $currentPatientId);
    }

    protected function canPersistRequesterPatientLink(object $appointment): bool
    {
        return app(AppointmentService::class)->canPersistRequesterPatientLink($appointment);
    }

    /**
     * @return array<string, mixed>
     */
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
            'first_name' => $this->appointmentRequestedPatientValue(
                $appointment,
                'requested_patient_first_name',
                'requester_first_name',
                'first_name'
            ),
            'last_name' => $this->appointmentRequestedPatientValue(
                $appointment,
                'requested_patient_last_name',
                'requester_last_name',
                'last_name'
            ),
            'middle_name' => $this->appointmentRequestedPatientValue(
                $appointment,
                'requested_patient_middle_name',
                'requester_middle_name',
                'middle_name'
            ),
            'mobile_number' => $this->appointmentUsesRequestedPatientIdentity($appointment)
                ? ''
                : ($appointment->requester_contact_number ?? $appointment->mobile_number ?? ''),
            'email_address' => $this->appointmentUsesRequestedPatientIdentity($appointment)
                ? ''
                : ($appointment->requester_email ?? $appointment->email_address ?? ''),
            'birth_date' => $this->appointmentRequestedPatientBirthDate($appointment, $requestBirthDate),
        ];
    }



    // --- SEARCH FUNCTIONALITY ---

    // This runs automatically whenever $searchQuery changes (as you type)
    public function updatedSearchQuery($value)
    {
        $this->searchQuery = InputSanitizer::sanitizeSearch($value);

        if (strlen($this->searchQuery) < 2) {
            $this->patientSearchResults = [];
            return;
        }

        $this->patientSearchResults = app(CalendarQueryService::class)
            ->searchPatients($this->searchQuery);
    }

    public function selectPatient($patientId)
    {
        $patient = DB::table('patients')->find($patientId);

        if ($patient) {
            // Auto-fill the form
            $this->firstName = $patient->first_name;
            $this->lastName = $patient->last_name;
            $this->middleName = $patient->middle_name;
            $this->contactNumber = $patient->mobile_number;
            $this->birthDate = $patient->birth_date;

            // Optional: If you store record_number in DB, map it here too
            // $this->recordNumber = $patient->record_number;

            // Clear the search so the dropdown disappears
            $this->searchQuery = '';
            $this->patientSearchResults = [];
        }
    }

    public function goToDate()
    {
        if (! $this->selectedDate) {
            return;
        }

        $this->currentDate = Carbon::parse($this->selectedDate);
        $this->generateWeekDates();
        $this->loadAppointments();
    }

    public function goToToday()
    {
        $this->currentDate = Carbon::now();
        $this->selectedDate = $this->currentDate->format('Y-m-d');
        $this->generateWeekDates();
        $this->loadAppointments();

    }

    public function clearPrefill()
    {
        $this->prefillPatientId = null;
        $this->prefillPatientLabel = null;
        $this->prefillAppointmentPayload = [];
    }

    protected function hydratePrefillFromRequest(): void
    {
        $prefillId = request()->query('patient_id');
        if (! empty($prefillId)) {
            $patient = DB::table('patients')->find($prefillId);
            if ($patient) {
                $this->prefillPatientId = (int) $prefillId;
                $this->prefillPatientLabel = trim(
                    ($patient->first_name ?? '').' '.($patient->last_name ?? '')
                );
            }
        }

        $firstName = InputSanitizer::sanitizeTitleCase(request()->query('prefill_first_name', ''));
        $lastName = InputSanitizer::sanitizeTitleCase(request()->query('prefill_last_name', ''));
        $middleName = InputSanitizer::sanitizeTitleCase(request()->query('prefill_middle_name', ''));
        $contactNumber = InputSanitizer::sanitizeCountryCodeLocalNumber(request()->query('prefill_contact_number', ''));
        $birthDate = trim((string) request()->query('prefill_birth_date', ''));
        $serviceId = (string) request()->query('prefill_service_id', '');

        $this->prefillAppointmentPayload = array_filter([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'contact_number' => $contactNumber,
            'birth_date' => $birthDate,
            'service_id' => $serviceId,
        ], fn ($value) => $value !== null && $value !== '');

        if ($this->prefillPatientLabel === null && ($firstName !== '' || $lastName !== '')) {
            $this->prefillPatientLabel = trim($firstName.' '.$lastName);
        }
    }

    protected function applyPrefillAppointmentPayload(): void
    {
        if (! empty($this->prefillAppointmentPayload['first_name'])) {
            $this->firstName = (string) $this->prefillAppointmentPayload['first_name'];
        }

        if (! empty($this->prefillAppointmentPayload['last_name'])) {
            $this->lastName = (string) $this->prefillAppointmentPayload['last_name'];
        }

        if (! empty($this->prefillAppointmentPayload['middle_name'])) {
            $this->middleName = (string) $this->prefillAppointmentPayload['middle_name'];
        }

        if (! empty($this->prefillAppointmentPayload['contact_number'])) {
            $this->contactNumber = (string) $this->prefillAppointmentPayload['contact_number'];
        }

        if (! empty($this->prefillAppointmentPayload['birth_date'])) {
            $this->birthDate = (string) $this->prefillAppointmentPayload['birth_date'];
        }

        if (! empty($this->prefillAppointmentPayload['service_id'])) {
            $this->selectedService = (string) $this->prefillAppointmentPayload['service_id'];
            $this->updatedSelectedService($this->selectedService);
        }

        $this->sanitizeFormInputs();
    }

    public function processPatient()
    {
        $patientId = $this->viewingPatientId;
        if (! $patientId && $this->viewingAppointmentId) {
            $patientId = DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->value('patient_id');
        }

        if ($patientId) {
            $this->dispatch('editPatient', id: (int) $patientId, startStep: 1);
            $this->closeAppointmentModal();
        }
    }

    public function dispatchPatientForm(int $startStep = 1): void
    {
        $patientId = $this->viewingPatientId;

        if (! $patientId && $this->viewingAppointmentId) {
            $patientId = DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->value('patient_id');
        }

        if (! $patientId) {
            $this->dispatch('flash-message', type: 'error', message: 'Patient record was not found for this appointment.');
            $this->dispatch('patient-form-open-failed');

            return;
        }

        $this->dispatch('editPatient', id: (int) $patientId, startStep: $startStep);
    }

    public function previewPendingPatientRecord(int $patientId, int $startStep = 1): void
    {
        if ($patientId <= 0) {
            $this->dispatch('flash-message', type: 'error', message: 'Patient record was not found.');
            $this->dispatch('patient-form-open-failed');

            return;
        }

        $patientExists = DB::table('patients')->where('id', $patientId)->exists();

        if (! $patientExists) {
            $this->dispatch('flash-message', type: 'error', message: 'Patient record was not found.');
            $this->dispatch('patient-form-open-failed');

            return;
        }

        $this->dispatch('editPatient', id: $patientId, startStep: $startStep);
    }

    public function openPatientChart()
    {
        $patientId = $this->viewingPatientId;
        if (! $patientId && $this->viewingAppointmentId) {
            $patientId = DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->value('patient_id');
        }

        if ($patientId) {
            $this->dispatch('editPatient', id: (int) $patientId, startStep: 3);
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

        $this->loadAppointments();
        $this->closeAppointmentModal();
        $this->dispatch('editPatient', id: $result['patientId'], startStep: 3);
    }

    protected function appointmentPatientFirstNameExpression(): string
    {
        return app(CalendarQueryService::class)->firstNameExpr();
    }

    protected function appointmentPatientLastNameExpression(): string
    {
        return app(CalendarQueryService::class)->lastNameExpr();
    }

    protected function appointmentPatientMiddleNameExpression(): string
    {
        return app(CalendarQueryService::class)->middleNameExpr();
    }

    protected function appointmentPatientBirthDateExpression(): string
    {
        return app(CalendarQueryService::class)->birthDateExpr();
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

    public function appointmentHasSeparateRequester(object $appointment): bool
    {
        return $this->appointmentUsesRequestedPatientIdentity($appointment);
    }

    public function appointmentPatientDisplayName(object $appointment): string
    {
        return trim((string) (($appointment->first_name ?? '').' '.($appointment->last_name ?? '')));
    }

    public function appointmentRequesterDisplayName(object $appointment): string
    {
        $firstName = trim((string) ($appointment->requester_first_name ?? ''));
        $lastName = trim((string) ($appointment->requester_last_name ?? ''));

        return trim($firstName.' '.$lastName);
    }

    public function appointmentPatientBirthDateDisplay(object $appointment): ?string
    {
        $birthDate = $this->appointmentRequestedPatientBirthDate($appointment, $appointment->birth_date ?? null);

        if (empty($birthDate)) {
            return null;
        }

        try {
            return Carbon::parse($birthDate)->format('F j, Y');
        } catch (\Throwable) {
            return (string) $birthDate;
        }
    }

    public function appointmentRequesterRelationshipLabel(object $appointment): ?string
    {
        $relationship = trim((string) ($appointment->requester_relationship_to_patient ?? ''));

        return $relationship !== '' ? $relationship : null;
    }

    public function appointmentNeedsPatientLink(object $appointment): bool
    {
        return empty($appointment->patient_id) && in_array(($appointment->status ?? null), ['Scheduled', 'Waiting'], true);
    }

    public function render()
    {
        return view('livewire.appointment.appointment-calendar');
    }

    protected function sanitizeFormInputs(): void
    {
        foreach (['firstName', 'middleName', 'lastName'] as $field) {
            $this->{$field} = InputSanitizer::sanitizeTitleCase($this->{$field} ?? '');
        }

        $this->contactNumber = InputSanitizer::sanitizeCountryCodeLocalNumber($this->contactNumber ?? '');
        $this->searchQuery = InputSanitizer::sanitizeSearch($this->searchQuery ?? '');
        $this->blockReason = InputSanitizer::sanitizeSentenceCase($this->blockReason ?? '', true, '.,&()/:;!?-');
    }

    protected function sanitizeUpdatedProperty(?string $propertyName): void
    {
        if (! is_string($propertyName) || $propertyName === '') {
            return;
        }

        if (in_array($propertyName, ['firstName', 'middleName', 'lastName'], true)) {
            $this->{$propertyName} = InputSanitizer::sanitizeTitleCase($this->{$propertyName} ?? '');
            return;
        }

        if ($propertyName === 'contactNumber') {
            $this->contactNumber = InputSanitizer::sanitizeCountryCodeLocalNumber($this->contactNumber ?? '');
            return;
        }

        if ($propertyName === 'searchQuery') {
            $this->searchQuery = InputSanitizer::sanitizeSearch($this->searchQuery ?? '');
            return;
        }

        if ($propertyName === 'blockReason') {
            $this->blockReason = InputSanitizer::sanitizeSentenceCase($this->blockReason ?? '', true, '.,&()/:;!?-');
        }
    }
}
