<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Patient;
use App\Support\PatientMatchService;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class AppointmentCalendar extends Component
{
    protected const SLOT_CAPACITY = 2;

    protected const APPROVED_SLOT_STATUSES = ['Scheduled', 'Waiting', 'Ongoing'];

    protected const REQUEST_SLOT_CAP = 5;

    protected const INACTIVE_APPOINTMENT_STATUSES = ['Cancelled', 'Completed'];

    // ... (All other properties and methods are unchanged) ...
    public $currentDate;

    public $viewType = 'week';

    public $weekDates = [];

    public $timeSlots = [];

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

    public $searchQuery = '';

    public $patientSearchResults = [];

    public $selectedMonthYear; // stores "YYYY-MM" format

    public $selectableMonthYears = [];

    public $activeTab = 'calendar';

    public $isTabLocked = false;

    public $lockedTab = null;

    public $prefillPatientId = null;

    public $prefillPatientLabel = null;

    public $pendingFilterDate = null;

    public $pendingMatchCandidates = [];

    public $pendingDuplicateWarnings = [];

    public $selectedPendingPatientId = null;

    public $isBlockMode = false;

    public $blockingSlotId = null;

    public $blockDate = null;

    public $blockStartTime = null;

    public $blockEndTime = null;

    public $blockReason = '';

    protected $rules = [
        'firstName' => 'required|string|max:100',
        'lastName' => 'required|string|max:100',
        'middleName' => 'nullable|string|max:100',
        'contactNumber' => 'required|string|max:20',
        'selectedService' => 'required',
        'selectedDate' => 'required',
        'selectedTime' => 'required',
        'endTime' => 'required',
        'birthDate' => 'required',
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
        for ($hour = 9; $hour <= 20; $hour++) {
            $this->timeSlots[] = sprintf('%02d:00', $hour);
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
        $endOfWeek = $this->weekDates[6]->endOfDay();

        $calendarQuery = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.service_id',
                'appointments.appointment_date',
                'appointments.status',
                'patients.first_name',
                'patients.last_name',
                'patients.middle_name',
                'patients.mobile_number',
                'patients.birth_date',
                'services.service_name',
                'services.duration'
            );

        // For display: hide Pending, show everything else except Cancelled
        $this->appointments = (clone $calendarQuery)
            ->where('appointments.status', '!=', 'Cancelled')
            ->where('appointments.status', '!=', 'Pending')
            ->get()
            ->map(fn ($appointment) => $this->hydrateAppointmentTiming($appointment))
            ->values();
    }

    public function refreshSlotCounts()
    {
        if (count($this->weekDates) < 7) {
            $this->occupiedAppointments = collect();
            $this->blockedSlots = collect();
            $this->occupiedSlotCounts = [];
            $this->blockedSlotMap = [];
            $this->blockedSlotLookup = [];

            return;
        }

        $startOfWeek = $this->weekDates[0]->startOfDay();
        $endOfWeek = $this->weekDates[6]->endOfDay();

        // For occupancy: only approved appointments consume slot capacity.
        $this->occupiedAppointments = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
            ->whereIn('appointments.status', self::APPROVED_SLOT_STATUSES)
            ->select(
                'appointments.appointment_date',
                'services.duration'
            )
            ->get()
            ->map(fn ($appointment) => $this->hydrateAppointmentTiming($appointment));

        if ($this->blockedSlotsEnabled()) {
            $this->blockedSlots = DB::table('blocked_slots')
                ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();
        } else {
            $this->blockedSlots = collect();
        }

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
        $this->searchQuery = '';
        $this->patientSearchResults = [];
        $this->pendingMatchCandidates = [];
        $this->pendingDuplicateWarnings = [];
        $this->selectedPendingPatientId = null;
        $this->isRescheduling = false;

    }

    protected function resetBlockForm(): void
    {
        $this->resetValidation([
            'blockDate',
            'blockStartTime',
            'blockEndTime',
            'blockReason',
        ]);
        $this->blockingSlotId = null;
        $this->blockDate = $this->selectedDate ?: now()->toDateString();
        $this->blockStartTime = null;
        $this->blockEndTime = null;
        $this->blockReason = '';
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
        }
    }

    public function closeAppointmentModal(bool $resetForm = false)
    {
        $this->showAppointmentModal = false;
        $this->dispatch('appointment-modal-closed');

        if ($resetForm) {
            $this->resetForm();
        }
    }

    public function toggleBlockMode(): void
    {
        if (! $this->blockedSlotsEnabled()) {
            session()->flash('error', 'Blocked slots table is not available yet. Please run migrations.');

            return;
        }

        $this->isBlockMode = ! $this->isBlockMode;

        if ($this->isBlockMode) {
            $this->closeAppointmentModal(true);
            session()->flash('info', 'Select one calendar slot to block.');
        }
    }

    public function blockSlot(string $date, string $time): void
    {
        if (! $this->blockedSlotsEnabled()) {
            session()->flash('error', 'Blocked slots table is not available yet. Please run migrations.');

            return;
        }

        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $slotStart = Carbon::parse($date.' '.$normalizedTime)->seconds(0);
        $slotEnd = $slotStart->copy()->addHour();
        $slotKey = $slotStart->format('Y-m-d H:i');

        if (($this->blockedSlotMap[$slotKey] ?? false) === true) {
            $this->isBlockMode = false;
            session()->flash('info', 'That slot is already blocked.');

            return;
        }

        if (($this->occupiedSlotCounts[$slotKey] ?? 0) > 0) {
            $this->isBlockMode = false;
            session()->flash('error', 'Cannot block a slot that already has appointments.');

            return;
        }

        $hasOverlap = DB::table('blocked_slots')
            ->whereDate('date', $slotStart->toDateString())
            ->where('start_time', '<', $slotEnd->format('H:i:s'))
            ->where('end_time', '>', $slotStart->format('H:i:s'))
            ->exists();

        if ($hasOverlap) {
            $this->isBlockMode = false;
            session()->flash('info', 'That slot is already blocked.');

            return;
        }

        DB::table('blocked_slots')->insert([
            'date' => $slotStart->toDateString(),
            'start_time' => $slotStart->format('H:i:s'),
            'end_time' => $slotEnd->format('H:i:s'),
            'reason' => null,
            'created_by' => Auth::check() ? Auth::user()->username : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->refreshSlotCounts();
        session()->flash('success', 'Time slot blocked. Select another slot to continue blocking.');
    }

    public function openBlockSlotModal(?string $date = null, ?string $time = null): void
    {
        if (! $this->blockedSlotsEnabled()) {
            session()->flash('error', 'Blocked slots table is not available yet. Please run migrations.');

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

        $slot = DB::table('blocked_slots')->where('id', $blockedSlotId)->first();
        if (! $slot) {
            return;
        }

        $this->resetBlockForm();
        $this->blockingSlotId = (int) $slot->id;
        $this->blockDate = $slot->date;
        $this->blockStartTime = substr((string) $slot->start_time, 0, 5);
        $this->blockEndTime = substr((string) $slot->end_time, 0, 5);
        $this->blockReason = (string) ($slot->reason ?? '');
        $this->showBlockModal = true;
    }

    public function saveBlockedSlot(): void
    {
        if (! $this->blockedSlotsEnabled()) {
            session()->flash('error', 'Blocked slots table is not available yet. Please run migrations.');

            return;
        }

        $this->validate([
            'blockDate' => 'required|date',
            'blockStartTime' => 'required|date_format:H:i',
            'blockEndTime' => 'required|date_format:H:i|after:blockStartTime',
            'blockReason' => 'nullable|string|max:255',
        ]);

        $start = Carbon::parse($this->blockDate.' '.$this->blockStartTime);
        $end = Carbon::parse($this->blockDate.' '.$this->blockEndTime);

        if (! $this->isAlignedToSlot($start) || ! $this->isAlignedToSlot($end)) {
            $this->addError('blockEndTime', 'Please use 1-hour boundaries (e.g. 10:00, 11:00).');

            return;
        }

        $hasOverlap = DB::table('blocked_slots')
            ->whereDate('date', $this->blockDate)
            ->when($this->blockingSlotId, fn ($query) => $query->where('id', '!=', $this->blockingSlotId))
            ->where('start_time', '<', $end->format('H:i:s'))
            ->where('end_time', '>', $start->format('H:i:s'))
            ->exists();

        if ($hasOverlap) {
            $this->addError('blockStartTime', 'This range overlaps another blocked slot.');

            return;
        }

        $isEditing = $this->blockingSlotId !== null;

        $payload = [
            'date' => $this->blockDate,
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'reason' => trim($this->blockReason) !== '' ? trim($this->blockReason) : null,
            'updated_at' => now(),
        ];

        if ($isEditing) {
            DB::table('blocked_slots')
                ->where('id', $this->blockingSlotId)
                ->update($payload);
        } else {
            $payload['created_by'] = Auth::check() ? Auth::user()->username : null;
            $payload['created_at'] = now();
            DB::table('blocked_slots')->insert($payload);
        }

        $this->refreshSlotCounts();
        $this->closeBlockSlotModal(true);
        session()->flash('success', $isEditing ? 'Blocked slot updated.' : 'Time slot blocked.');
    }

    public function unblockSlot(int $blockedSlotId): void
    {
        if (! $this->blockedSlotsEnabled()) {
            return;
        }

        DB::table('blocked_slots')->where('id', $blockedSlotId)->delete();
        $this->refreshSlotCounts();
        $this->closeBlockSlotModal(true);
        session()->flash('success', 'Blocked slot removed.');
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
    }

    public function updatedSelectedTime($value): void
    {
        if (! empty($this->selectedService)) {
            $this->updatedSelectedService($this->selectedService);
        }
    }

    public function updatedBirthDate($value): void
    {
        $this->resetValidation('birthDate');
    }

    public function updated($propertyName): void
    {
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
            'selectedPendingPatientId',
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

        $this->validate([
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'contactNumber' => 'required|string|max:20',
            'selectedService' => 'required',
            'selectedDate' => 'required',
            'selectedTime' => 'required',
            'birthDate' => 'required',
        ]);

        try {
            $service = collect($this->servicesList)->firstWhere('id', $this->selectedService);

            if (! $service) {
                $this->addError('selectedService', 'Please select a valid service.');

                return;
            }

            $durationInMinutes = $this->durationToMinutes($service->duration);

            $proposedStart = Carbon::parse($this->appointmentDate)->setTimeFromTimeString($this->selectedTime);
            $proposedEnd = $proposedStart->copy()->addMinutes($durationInMinutes);

            if ($this->hasBlockedConflict($proposedStart, $proposedEnd)) {
                $this->addError('conflict', 'This time range includes blocked slots.');

                return;
            }

            // Conflict Check: allow up to two approved appointments per overlapping slot.
            $conflicts = DB::table('appointments')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->where(function ($query) use ($proposedStart, $proposedEnd) {
                    $existingStart = 'appointments.appointment_date';
                    $existingEnd = DB::raw('DATE_ADD(appointments.appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND)');

                    $query->where($existingStart, '<', $proposedEnd->toDateTimeString())
                        ->where($existingEnd, '>', $proposedStart->toDateTimeString())
                        ->whereIn('appointments.status', self::APPROVED_SLOT_STATUSES);
                })
                ->count();

            if ($conflicts >= self::SLOT_CAPACITY) {
                $this->addError('conflict', 'This time slot already has two approved appointments.');

                return;
            }

            // Patient Logic
            $patient = DB::table('patients')->where('mobile_number', $this->contactNumber)->first();
            $patientId = null;
            $normalizedBirthDate = $this->normalizeBirthDate($this->birthDate);

            if ($patient) {
                $patientId = $patient->id;
                $oldPatient = (array) $patient;
                $patientUpdates = [
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'middle_name' => $this->middleName,
                    'birth_date' => $normalizedBirthDate,
                ];

                DB::table('patients')->where('id', $patientId)->update($patientUpdates);

                $hasChanges = (
                    $oldPatient['first_name'] !== $patientUpdates['first_name'] ||
                    $oldPatient['last_name'] !== $patientUpdates['last_name'] ||
                    ($oldPatient['middle_name'] ?? null) !== $patientUpdates['middle_name'] ||
                    $oldPatient['birth_date'] !== $patientUpdates['birth_date']
                );

                if ($hasChanges) {
                    $patientSubject = new Patient;
                    $patientSubject->id = $patientId;

                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($patientSubject)
                        ->event('patient_updated')
                        ->withProperties([
                            'old' => [
                                'first_name' => $oldPatient['first_name'],
                                'last_name' => $oldPatient['last_name'],
                                'middle_name' => $oldPatient['middle_name'] ?? null,
                                'birth_date' => $oldPatient['birth_date'],
                            ],
                            'attributes' => $patientUpdates,
                        ])
                        ->log('Updated Patient');
                }
            } else {
                $patientId = DB::table('patients')->insertGetId([
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'middle_name' => $this->middleName,
                    'mobile_number' => $this->contactNumber,
                    'birth_date' => $normalizedBirthDate,
                    'modified_by' => Auth::check() ? Auth::user()->username : 'SYSTEM',
                ]);

                $patientSubject = new Patient;
                $patientSubject->id = $patientId;

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($patientSubject)
                    ->event('patient_created')
                    ->withProperties([
                        'attributes' => [
                            'first_name' => $this->firstName,
                            'last_name' => $this->lastName,
                            'middle_name' => $this->middleName,
                            'mobile_number' => $this->contactNumber,
                            'birth_date' => $normalizedBirthDate,
                        ],
                    ])
                    ->log('Created Patient');
            }

            $appointmentDateTime = Carbon::parse($this->appointmentDate)
                ->setTimeFromTimeString($this->selectedTime)
                ->toDateTimeString();

            // Insert Appointment
            $appointmentPayload = [
                'patient_id' => $patientId,
                'service_id' => $this->selectedService,
                'appointment_date' => $appointmentDateTime,
                'status' => 'Scheduled',
                'modified_by' => Auth::check() ? Auth::user()->username : 'SYSTEM',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('appointments', 'booking_type')) {
                $appointmentPayload['booking_type'] = 'online_appointment';
            }

            $newApptId = DB::table('appointments')->insertGetId($appointmentPayload);

            $appointmentSubject = new Appointment;
            $appointmentSubject->id = $newApptId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($appointmentSubject)
                ->event('appointment_created')
                ->withProperties([
                    'attributes' => [
                        'patient_id' => $patientId,
                        'patient_name' => trim("{$this->lastName}, {$this->firstName} {$this->middleName}"),
                        'service_id' => $this->selectedService,
                        'appointment_date' => $appointmentDateTime,
                        'status' => 'Scheduled',
                    ],
                ])
                ->log('Created Appointment');

            // Success!
            session()->flash('success', 'Appointment booked successfully!');

            $this->clearPrefill();
            $this->loadAppointments();
            $this->closeAppointmentModal(true);

        } catch (\Throwable $th) {
            // 3. FIX: Show the error instead of hiding it!
            session()->flash('error', 'Error saving: '.$th->getMessage());
        }
    }

    public function validateAppointmentForConfirm()
    {
        $this->validate([
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'contactNumber' => 'required|string|max:20',
            'selectedService' => 'required',
            'selectedDate' => 'required',
            'selectedTime' => 'required',
            'birthDate' => 'required',
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

        return ($this->occupiedSlotCounts[$key] ?? 0) >= self::SLOT_CAPACITY;
    }

    public function isSlotBlocked($date, $time): bool
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;

        return ($this->blockedSlotMap[$key] ?? false) === true;
    }

    public function hasAppointmentsInSlot(string $date, string $time): bool
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;

        return ($this->occupiedSlotCounts[$key] ?? 0) > 0;
    }

    public function getBlockedSlotAt(string $date, string $time): ?object
    {
        $normalizedTime = strlen($time) >= 5 ? substr($time, 0, 5) : $time;
        $key = $date.' '.$normalizedTime;

        return $this->blockedSlotLookup[$key] ?? null;
    }

    protected function rebuildOccupiedSlotCounts(): void
    {
        $slotCounts = [];
        $blockedMap = [];
        $blockedLookup = [];

        foreach ($this->occupiedAppointments as $appointment) {
            $slotCursor = Carbon::parse($appointment->start_date.' '.$appointment->start_time)->seconds(0);
            $slotEnd = $slotCursor->copy()->addMinutes((int) $appointment->duration_in_minutes);

            while ($slotCursor < $slotEnd) {
                $key = $slotCursor->format('Y-m-d H:i');
                $slotCounts[$key] = ($slotCounts[$key] ?? 0) + 1;
                $slotCursor->addMinutes(30);
            }
        }

        foreach ($this->blockedSlots as $slot) {
            $slotCursor = Carbon::parse($slot->date.' '.$slot->start_time)->seconds(0);
            $slotEnd = Carbon::parse($slot->date.' '.$slot->end_time)->seconds(0);

            while ($slotCursor < $slotEnd) {
                $key = $slotCursor->format('Y-m-d H:i');
                $blockedMap[$key] = true;
                $blockedLookup[$key] = $slot;
                $slotCursor->addMinutes(30);
            }
        }

        $this->occupiedSlotCounts = $slotCounts;
        $this->blockedSlotMap = $blockedMap;
        $this->blockedSlotLookup = $blockedLookup;
    }

    protected function hasBlockedConflict(Carbon $proposedStart, Carbon $proposedEnd): bool
    {
        if (! $this->blockedSlotsEnabled()) {
            return false;
        }

        return DB::table('blocked_slots')
            ->whereDate('date', $proposedStart->toDateString())
            ->where('start_time', '<', $proposedEnd->format('H:i:s'))
            ->where('end_time', '>', $proposedStart->format('H:i:s'))
            ->exists();
    }

    protected function isAlignedToSlot(Carbon $dateTime): bool
    {
        return (int) $dateTime->minute === 0 && (int) $dateTime->second === 0;
    }

    protected function blockedSlotsEnabled(): bool
    {
        if ($this->blockedSlotsTableExists === null) {
            $this->blockedSlotsTableExists = Schema::hasTable('blocked_slots');
        }

        return $this->blockedSlotsTableExists;
    }

    public function viewAppointment($appointmentId)
    {
        // Fast path: use already loaded weekly data.
        $appointment = collect($this->appointments)->firstWhere('id', (int) $appointmentId);

        // Fallback path for any appointment not in current in-memory collection.
        if (! $appointment) {
            $appointment = DB::table('appointments')
                ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->select(
                    'appointments.*',
                    DB::raw('COALESCE(patients.first_name, appointments.requester_first_name) as first_name'),
                    DB::raw('COALESCE(patients.last_name, appointments.requester_last_name) as last_name'),
                    'patients.middle_name',
                    DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as mobile_number'),
                    'patients.birth_date',
                    'services.service_name',
                    'services.duration'
                )
                ->where('appointments.id', $appointmentId)
                ->first();
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
            $this->hydratePendingReviewContext($appointment);

            // 3. Format Dates and Times
            $dt = Carbon::parse($appointment->appointment_date);
            $this->appointmentDate = $dt->toDateString();  // NEW: Use this
            $this->selectedTime = $dt->format('H:i:s');
            $this->selectedService = $appointment->service_id;

            // Calculate End Time for display
            $durationInMinutes = $this->durationToMinutes($appointment->duration);
            $this->endTime = $dt->copy()->addMinutes($durationInMinutes)->format('H:i A');

            // 4. Set mode to Viewing and open modal
            $this->isViewing = true;
            $this->showAppointmentModal = true;
            $this->dispatch('appointment-modal-opened');
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
        if ($this->viewingAppointmentId) {
            $didUpdate = $this->updateAppointmentStatusById((int) $this->viewingAppointmentId, (string) $newStatus);
            if ($didUpdate) {
                $this->closeAppointmentModal();
            }
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

        return DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'Pending')
            ->when($this->pendingFilterDate, function ($query) {
                $query->whereDate('appointments.appointment_date', $this->pendingFilterDate);
            })
            ->orderBy('appointments.appointment_date', 'asc')
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.appointment_date',
                'appointments.status',
                DB::raw('COALESCE(patients.first_name, appointments.requester_first_name) as first_name'),
                DB::raw('COALESCE(patients.last_name, appointments.requester_last_name) as last_name'),
                DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as mobile_number'),
                DB::raw('COALESCE(patients.email_address, appointments.requester_email) as email_address'),
                'services.service_name'
            )
            ->get();
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

        $appointment = DB::table('appointments')->where('id', $appointmentId)->first();
        if (! $appointment) {
            return;
        }

        if (empty($appointment->patient_id)) {
            $this->viewAppointment($appointmentId);
            session()->flash('error', 'Review required: link to an existing patient or create a new patient before approval.');

            return;
        }

        $didUpdate = $this->updateAppointmentStatusById($appointmentId, 'Scheduled');
        if ($didUpdate) {
            session()->flash('success', 'Appointment request approved.');
        }
    }

    public function rejectAppointment($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $this->updateAppointmentStatusById($appointmentId, 'Cancelled');
        session()->flash('info', 'Appointment request rejected.');
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
        $this->resetValidation(['selectedDate', 'selectedTime', 'conflict']);
    }

    public function cancelPendingReschedule(): void
    {
        $this->isRescheduling = false;
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

        if ($this->hasBlockedConflict($proposedStart, $proposedEnd)) {
            $this->addError('conflict', 'This time range includes blocked slots.');

            return;
        }

        $approvedConflicts = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $this->viewingAppointmentId)
            ->where(function ($query) use ($proposedStart, $proposedEnd) {
                $existingStart = 'appointments.appointment_date';
                $existingEnd = DB::raw('DATE_ADD(appointments.appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND)');

                $query->where($existingStart, '<', $proposedEnd->toDateTimeString())
                    ->where($existingEnd, '>', $proposedStart->toDateTimeString())
                    ->whereIn('appointments.status', self::APPROVED_SLOT_STATUSES);
            })
            ->count();

        if ($approvedConflicts >= self::SLOT_CAPACITY) {
            $this->addError('conflict', 'This time slot already has two approved appointments.');

            return;
        }

        $requestCountInTargetSlot = DB::table('appointments')
            ->where('id', '!=', $this->viewingAppointmentId)
            ->where('appointment_date', $proposedStart->toDateTimeString())
            ->whereNotIn('status', self::INACTIVE_APPOINTMENT_STATUSES)
            ->count();

        if ($requestCountInTargetSlot >= self::REQUEST_SLOT_CAP) {
            $this->addError('conflict', 'This time slot already reached the maximum of 5 requests.');

            return;
        }

        DB::table('appointments')
            ->where('id', $this->viewingAppointmentId)
            ->update([
                'appointment_date' => $proposedStart->toDateTimeString(),
                'service_id' => $this->selectedService,
                'updated_at' => now(),
            ]);

        $subject = new Appointment;
        $subject->id = $this->viewingAppointmentId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('appointment_request_rescheduled')
            ->withProperties([
                'attributes' => [
                    'appointment_id' => (int) $this->viewingAppointmentId,
                    'appointment_date' => $proposedStart->toDateTimeString(),
                    'service_id' => $this->selectedService,
                ],
            ])
            ->log('Rescheduled Appointment Request');

        $this->isRescheduling = false;
        $this->loadAppointments();
        $this->viewAppointment((int) $this->viewingAppointmentId);
        session()->flash('success', 'Appointment request rescheduled successfully.');
    }

    protected function updateAppointmentStatusById($appointmentId, $newStatus): bool
    {
        $oldAppt = DB::table('appointments')->where('id', $appointmentId)->first();
        if (! $oldAppt) {
            return false;
        }

        if ($newStatus === 'Scheduled' && empty($oldAppt->patient_id)) {
            session()->flash('error', 'Cannot approve without a linked patient record.');

            return false;
        }

        DB::table('appointments')
            ->where('id', $appointmentId)
            ->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

        if ($oldAppt->status !== $newStatus) {
            $subject = new Appointment;
            $subject->id = $appointmentId;

            $eventName = $newStatus === 'Cancelled' ? 'appointment_cancelled' : 'appointment_updated';

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->event($eventName)
                ->withProperties([
                    'old' => ['status' => $oldAppt->status],
                    'attributes' => ['status' => $newStatus],
                ])
                ->log('Updated Appointment Status');

            if ($oldAppt->status === 'Pending' && $newStatus === 'Scheduled') {
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subject)
                    ->event('appointment_request_approved')
                    ->withProperties([
                        'attributes' => [
                            'patient_id' => $oldAppt->patient_id,
                            'appointment_id' => (int) $appointmentId,
                        ],
                    ])
                    ->log('Approved Appointment Request');

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subject)
                    ->event('official_appointment_created')
                    ->withProperties([
                        'attributes' => [
                            'appointment_id' => (int) $appointmentId,
                            'patient_id' => $oldAppt->patient_id,
                            'status' => $newStatus,
                        ],
                    ])
                    ->log('Official Appointment Linked to Patient');
            }
        }

        $this->sendStatusEmail($appointmentId, $newStatus);

        $this->loadAppointments();

        return true;
    }

    protected function hydratePendingReviewContext(object $appointment): void
    {
        $this->pendingMatchCandidates = [];
        $this->pendingDuplicateWarnings = [];
        $this->selectedPendingPatientId = null;

        if (($appointment->status ?? null) !== 'Pending') {
            return;
        }

        $requestData = $this->resolveCurrentPendingRequestData($appointment);
        $matcher = app(PatientMatchService::class);

        $this->pendingMatchCandidates = $matcher->suggestMatches($requestData)->all();
        $this->pendingDuplicateWarnings = $matcher->duplicateWarnings($requestData);
        $this->selectedPendingPatientId = ! empty($appointment->patient_id)
            ? (int) $appointment->patient_id
            : null;
    }

    public function linkPendingRequestToExistingPatient(): void
    {
        if (! $this->viewingAppointmentId || ! $this->selectedPendingPatientId) {
            session()->flash('error', 'Select a patient record to link.');

            return;
        }

        $appointment = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
        if (! $appointment || $appointment->status !== 'Pending') {
            session()->flash('error', 'Only pending requests can be linked.');

            return;
        }

        $patient = DB::table('patients')->where('id', $this->selectedPendingPatientId)->first();
        if (! $patient) {
            session()->flash('error', 'Selected patient record was not found.');

            return;
        }

        DB::table('appointments')
            ->where('id', $this->viewingAppointmentId)
            ->update([
                'patient_id' => (int) $patient->id,
                'updated_at' => now(),
            ]);

        $this->viewingPatientId = (int) $patient->id;

        $subject = new Appointment;
        $subject->id = $this->viewingAppointmentId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('appointment_request_linked_existing')
            ->withProperties([
                'attributes' => [
                    'appointment_id' => (int) $this->viewingAppointmentId,
                    'patient_id' => (int) $patient->id,
                ],
            ])
            ->log('Linked Appointment Request to Existing Patient');

        session()->flash('success', 'Request linked to existing patient. You can now approve.');
        $this->loadAppointments();
    }

    public function createPatientForPendingRequest(): void
    {
        if (! $this->viewingAppointmentId) {
            return;
        }

        $appointment = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
        if (! $appointment || $appointment->status !== 'Pending') {
            session()->flash('error', 'Only pending requests can create a patient link.');

            return;
        }

        $requestData = $this->resolveCurrentPendingRequestData($appointment);
        $firstName = trim((string) ($requestData['first_name'] ?? ''));
        $lastName = trim((string) ($requestData['last_name'] ?? ''));

        if ($firstName === '' || $lastName === '') {
            session()->flash('error', 'Request must have both first and last name before creating a patient record.');

            return;
        }

        $patientId = DB::table('patients')->insertGetId([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'mobile_number' => trim((string) ($requestData['mobile_number'] ?? '')),
            'birth_date' => ! empty($requestData['birth_date']) ? $requestData['birth_date'] : null,
            'email_address' => ! empty($requestData['email_address']) ? $requestData['email_address'] : null,
            'modified_by' => Auth::user()?->username ?? 'SYSTEM',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('appointments')
            ->where('id', $this->viewingAppointmentId)
            ->update([
                'patient_id' => $patientId,
                'updated_at' => now(),
            ]);

        $patientSubject = new Patient;
        $patientSubject->id = $patientId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($patientSubject)
            ->event('patient_created_from_request')
            ->withProperties([
                'attributes' => [
                    'patient_id' => (int) $patientId,
                    'source_appointment_id' => (int) $this->viewingAppointmentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ],
            ])
            ->log('Created Patient from Appointment Request');

        $appointmentSubject = new Appointment;
        $appointmentSubject->id = $this->viewingAppointmentId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($appointmentSubject)
            ->event('appointment_request_linked_new_patient')
            ->withProperties([
                'attributes' => [
                    'appointment_id' => (int) $this->viewingAppointmentId,
                    'patient_id' => (int) $patientId,
                ],
            ])
            ->log('Linked Appointment Request to New Patient');

        $this->viewingPatientId = (int) $patientId;
        $this->selectedPendingPatientId = (int) $patientId;
        $this->loadAppointments();
        $this->hydratePendingReviewContext((object) array_merge((array) $appointment, ['patient_id' => $patientId]));
        session()->flash('success', 'New patient created and linked. You can now approve.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveCurrentPendingRequestData(object $appointment): array
    {
        $requestBirthDate = null;
        if (Schema::hasColumn('appointments', 'requester_birth_date') && isset($appointment->requester_birth_date)) {
            $requestBirthDate = $appointment->requester_birth_date;
        }

        return [
            'first_name' => $appointment->requester_first_name ?? $appointment->first_name ?? '',
            'last_name' => $appointment->requester_last_name ?? $appointment->last_name ?? '',
            'mobile_number' => $appointment->requester_contact_number ?? $appointment->mobile_number ?? '',
            'email_address' => $appointment->requester_email ?? $appointment->email_address ?? '',
            'birth_date' => $requestBirthDate ?: ($appointment->birth_date ?? null),
        ];
    }

    protected function sendStatusEmail($appointmentId, $newStatus)
    {
        $appointment = DB::table('appointments')
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

        if (! $appointment || empty($appointment->email_address)) {
            return;
        }

        try {
            Mail::send('appointment.emails.appointment-status-update', [
                'name' => trim($appointment->first_name.' '.$appointment->last_name),
                'appointment_date' => Carbon::parse($appointment->appointment_date)->format('F j, Y g:i A'),
                'service_name' => $appointment->service_name,
                'status' => $newStatus,
            ], function ($message) use ($appointment) {
                $message->to($appointment->email_address);
                $message->subject('Appointment Status Update');
            });
        } catch (\Throwable $th) {
            // Do not break UI if mail fails
        }
    }

    // --- SEARCH FUNCTIONALITY ---

    // This runs automatically whenever $searchQuery changes (as you type)
    public function updatedSearchQuery($value)
    {
        $this->searchQuery = is_string($value) ? trim($value) : '';

        // Don't search if empty or too short
        if (strlen($this->searchQuery) < 2) {
            $this->patientSearchResults = [];

            return;
        }

        // Search by first name, last name, or mobile number.
        $this->patientSearchResults = DB::table('patients')
            ->select('id', 'first_name', 'last_name', 'middle_name', 'mobile_number', 'birth_date')
            ->where(function ($query) {
                $query->where('first_name', 'like', '%'.$this->searchQuery.'%')
                    ->orWhere('last_name', 'like', '%'.$this->searchQuery.'%')
                    ->orWhere('mobile_number', 'like', '%'.$this->searchQuery.'%');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(10)
            ->get();
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
            session()->flash('error', 'Patient record was not found for this appointment.');
            $this->dispatch('patient-form-open-failed');

            return;
        }

        $this->dispatch('editPatient', id: (int) $patientId, startStep: $startStep);
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

    public function admitPatient()
    {
        $appointment = DB::table('appointments')->find($this->viewingAppointmentId);
        $service = DB::table('services')->where('id', $this->selectedService)->first();

        if (! $appointment || ! $service) {
            return;
        }

        // Use Original Time
        $startTime = Carbon::parse($appointment->appointment_date);

        $durationMinutes = $this->durationToMinutes($service->duration);
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        // Conflict Check
        $dentistId = Auth::id();
        $hasConflict = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $this->viewingAppointmentId)
            ->where('appointments.status', 'Ongoing')
            ->where('appointments.dentist_id', $dentistId)
            ->whereDate('appointment_date', $startTime->toDateString())
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('appointment_date', '<', $endTime)
                    ->whereRaw('DATE_ADD(appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND) > ?', [$startTime]);
            })
            ->exists();

        if ($hasConflict) {
            session()->flash('error', 'Cannot admit: This slot is double-booked.');
        } else {
            $oldAppointment = $appointment;
            // === FIX IS HERE ===
            $updated = DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->where('status', 'Waiting')
                ->update([
                    'status' => 'Ongoing',
                    'service_id' => $this->selectedService,
                    'dentist_id' => $dentistId, // <--- ADD THIS LINE TO CLAIM THE PATIENT
                    'updated_at' => now(),
                ]);

            if (! $updated) {
                session()->flash('error', 'This appointment was already admitted or updated. Please refresh.');

                return;
            }
            // ===================

            $appointmentSubject = new Appointment;
            $appointmentSubject->id = $this->viewingAppointmentId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($appointmentSubject)
                ->event('appointment_admitted')
                ->withProperties([
                    'old' => [
                        'status' => $oldAppointment->status ?? null,
                        'service_id' => $oldAppointment->service_id ?? null,
                        'dentist_id' => $oldAppointment->dentist_id ?? null,
                    ],
                    'attributes' => [
                        'status' => 'Ongoing',
                        'service_id' => $this->selectedService,
                        'dentist_id' => Auth::id(),
                    ],
                ])
                ->log('Admitted Appointment');

            $this->loadAppointments();
            $this->closeAppointmentModal();
            $this->dispatch('editPatient', id: $appointment->patient_id, startStep: 3);
        }
    }

    public function render()
    {
        return view('livewire.appointment-calendar');
    }
}
