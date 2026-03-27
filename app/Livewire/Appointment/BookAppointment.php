<?php

namespace App\Livewire\Appointment;

use App\Services\BlockedSlotService;
use App\Services\CalendarQueryService;
use App\Support\InputSanitizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Throwable;

class BookAppointment extends Component
{
    private const PH_CONTACT_RULE = 'regex:/^\d{10}$/';

    protected const SLOT_CAPACITY = 2;

    protected const APPROVED_SLOT_STATUSES = ['Scheduled', 'Waiting', 'Ongoing'];

    protected const REQUEST_SLOT_CAP = 5;

    protected const INACTIVE_APPOINTMENT_STATUSES = ['Cancelled', 'Completed'];

    // Form data
    public $first_name;

    public $last_name;

    public $middle_name = '';

    public $contact_number;

    public $email;

    public $booking_for = 'self';

    public $patient_first_name = '';

    public $patient_middle_name = '';

    public $patient_last_name = '';

    public $patient_birth_date = '';

    public $relationship_to_patient = '';

    public $service_id;

    public $selectedDate;

    public $selectedSlot;

    public $recaptchaToken;

    public $booking_agreement = false;

    public $guestEmailOtp = '';

    public $guestEmailOtpHash = null;

    public $guestEmailOtpExpiresAt = null;

    public $guestEmailOtpCooldownUntil = null;

    public $guestEmailOtpResendLockedUntil = null;

    public $guestEmailOtpVerified = false;

    public $guestEmailOtpTargetEmail = null;

    public $guestOtpStepActive = false;

    public $guestEmailOtpResendCount = 0;

    public $otpMessage = null;

    protected ?bool $blockedSlotsTableExists = null;

    protected $appointmentStatuses = null;

    // UI data
    public $availableSlots = [];

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
        $this->availableSlots = $this->generateSlots($this->selectedDate);

        if (Auth::check()) {
            $user = Auth::user();
            $this->email = $user->email;
            $this->contact_number = $user->mobile_number ?? '';
            $this->first_name = InputSanitizer::sanitizeTitleCase($user->first_name ?? '');
            $this->last_name = InputSanitizer::sanitizeTitleCase($user->last_name ?? '');

            $this->prefillFromPreviousBooking();
        }

        $this->sanitizeBookingFields();
    }

    public function updatedSelectedDate($date)
    {
        if (empty($date) || ! $this->isValidSelectedDate($date)) {
            $this->availableSlots = [];
            $this->selectedSlot = null;
            $this->selectedDate = null;
            $this->dispatch('book-calendar-refresh', selectedDate: null);

            return;
        }

        // A slot value like 09:00:00 exists on many dates, so clear the old
        // selection whenever the user changes the appointment day.
        $this->selectedSlot = null;
        $this->resetValidation('selectedSlot');
        $this->availableSlots = $this->generateSlots($date);
        $this->dispatch('book-calendar-refresh', selectedDate: $date);
    }

    public function updatedServiceId($value): void
    {
        // Re-generate slots whenever the service changes so duration-based
        // filtering reflects the newly selected service immediately.
        $this->selectedSlot = null;
        $this->resetValidation('selectedSlot');

        if (! empty($this->selectedDate)) {
            $this->availableSlots = $this->generateSlots($this->selectedDate);
        }
    }

    public function updatedEmail($value): void
    {
        $normalizedEmail = InputSanitizer::sanitizeEmail($value);
        if ($normalizedEmail === '' || $normalizedEmail !== $this->guestEmailOtpTargetEmail) {
            $this->resetGuestEmailOtpState();
        }

        if ($normalizedEmail === '') {
            return;
        }

        $this->email = $normalizedEmail;
        $this->prefillFromPreviousBooking();
    }

    public function updated($propertyName): void
    {
        $this->sanitizeField($propertyName);

        $clearableFields = [
            'first_name',
            'last_name',
            'middle_name',
            'contact_number',
            'email',
            'booking_for',
            'patient_first_name',
            'patient_middle_name',
            'patient_last_name',
            'patient_birth_date',
            'relationship_to_patient',
            'service_id',
            'selectedDate',
            'selectedSlot',
            'booking_agreement',
            'recaptchaToken',
            'guestEmailOtp',
        ];

        if (in_array($propertyName, $clearableFields, true)) {
            $this->resetValidation($propertyName);
        }

        if ($propertyName === 'recaptchaToken') {
            $this->resetValidation('recaptcha');
        }
    }

    public function updatedBookingFor(string $value): void
    {
        $this->patient_first_name = '';
        $this->patient_middle_name = '';
        $this->patient_last_name = '';
        $this->patient_birth_date = '';
        $this->relationship_to_patient = '';

        $this->resetValidation([
            'patient_first_name',
            'patient_middle_name',
            'patient_last_name',
            'patient_birth_date',
            'relationship_to_patient',
        ]);

        if ($value !== 'someone_else') {
            $this->prefillFromPreviousBooking();
        }

        $this->sanitizeBookingFields();
    }

    public function generateSlots($dateString)
    {
        $clinicOpen  = Carbon::parse($dateString.' 09:00:00');
        $clinicClose = Carbon::parse($dateString.' 18:00:00');
        $latestStart = $clinicClose->copy()->subHour();
        $blockedSlots = collect();

        // Resolve selected service duration in minutes (default 60 if none selected yet)
        $durationMinutes = 60;
        if (! empty($this->service_id)) {
            $service = DB::table('services')->where('id', $this->service_id)->first();
            if ($service && ! empty($service->duration)) {
                [$h, $m] = array_map('intval', explode(':', $service->duration));
                $durationMinutes = max(60, $h * 60 + $m);
            }
        }

        if ($this->blockedSlotsEnabled()) {
            $blockedSlots = DB::table('blocked_slots')
                ->whereDate('date', $dateString)
                ->select('start_time', 'end_time')
                ->get();
        }

        $cqs = app(CalendarQueryService::class);

        $requestCounts = DB::table('appointments')
            ->whereDate('appointment_date', $dateString)
            ->whereNotIn('status', self::INACTIVE_APPOINTMENT_STATUSES)
            ->selectRaw('TIME(appointment_date) as time_slot, COUNT(*) as total')
            ->groupBy('time_slot')
            ->pluck('total', 'time_slot')
            ->toArray();

        $slots = [];
        $cursor = $clinicOpen->copy();

        while ($cursor->lte($latestStart)) {
            $slotTime     = $cursor->format('H:i:00');
            $slotStart    = Carbon::parse($dateString.' '.$slotTime)->seconds(0);
            $slotEnd      = $slotStart->copy()->addMinutes($durationMinutes);
            $isPast       = $slotStart->lt(now());

            // Hide slots whose end time exceeds clinic closing time
            $exceedsHours = $slotEnd->gt($clinicClose);

            // Duration-aware overlap check against approved/active appointments
            $approvedConflicts = $exceedsHours ? 0 : $cqs->countConflicts($slotStart, $slotEnd, self::APPROVED_SLOT_STATUSES);
            $blockedCapacity   = $exceedsHours ? self::SLOT_CAPACITY : $this->blockedCapacityBySlotCollection($slotStart, $slotEnd, $blockedSlots);
            $effectiveCapacity = max(0, self::SLOT_CAPACITY - $blockedCapacity);
            $isApprovedFull    = $approvedConflicts >= $effectiveCapacity;

            $currentRequests   = $requestCounts[$slotTime] ?? 0;
            $isRequestFull     = $currentRequests >= self::REQUEST_SLOT_CAP;

            // Duration-aware block check
            $isBlocked = (! $exceedsHours) && $blockedCapacity >= self::SLOT_CAPACITY;

            $slots[] = [
                'time'       => $cursor->format('h:i A'),
                'value'      => $slotTime,
                'is_full'    => $exceedsHours || $isApprovedFull || $isRequestFull,
                'is_past'    => $isPast,
                'is_blocked' => $isBlocked,
            ];

            $cursor->addMinutes(60); // Grid steps are always 1 hour
        }

        return $slots;
    }

    public function bookAppointment()
    {
        $this->validateBookingFormData();

        if (Auth::check() && empty(Auth::user()->email_verified_at)) {
            $this->addError('email', 'Please verify your email address before booking an appointment.');

            return;
        }

        if (! $this->assertBookingStillAvailable()) {
            return;
        }

        if (! Auth::check()) {
            if ($this->canResumeGuestOtpStep()) {
                $this->guestEmailOtp = '';
                $this->resetValidation('guestEmailOtp');
                $this->otpMessage = 'Enter the 6-digit code we already sent to your email.';
                $this->guestOtpStepActive = true;

                return;
            }

            if (! $this->verifyRecaptchaForGuest()) {
                return;
            }

            $this->sendGuestEmailOtp();
            if ($this->getErrorBag()->isNotEmpty()) {
                return;
            }

            $this->guestOtpStepActive = true;

            return;
        }

        return $this->createAppointmentAndRedirect();
    }

    public function sendGuestEmailOtp(): void
    {
        $this->issueGuestEmailOtp(false);
    }

    public function resendGuestEmailOtp(): void
    {
        $this->issueGuestEmailOtp(true);
    }

    protected function issueGuestEmailOtp(bool $isResend): void
    {
        if (Auth::check()) {
            return;
        }

        $this->validate([
            'email' => 'required|email',
        ]);

        $normalizedEmail = $this->normalizeEmail($this->email);
        if ($normalizedEmail === '') {
            $this->addError('email', 'Please enter a valid email address.');

            return;
        }

        if ($isResend && ! $this->canResumeGuestOtpStep()) {
            // Keep the user in OTP step and allow issuing a fresh code for the same email
            // instead of forcing a full form re-submit.
            if ($this->guestEmailOtpTargetEmail !== null && $normalizedEmail !== $this->guestEmailOtpTargetEmail) {
                $this->addError('guestEmailOtp', 'Email changed. Please submit the form again.');

                return;
            }
        }

        $resendState = $this->guestEmailOtpResendState($normalizedEmail);
        $lockRemaining = $this->guestEmailOtpResendLockRemaining($resendState);
        if ($lockRemaining > 0) {
            $seconds = max(1, $lockRemaining);
            $this->guestEmailOtpResendCount = $this->guestEmailOtpResendStateCount($resendState);
            $this->guestEmailOtpResendLockedUntil = $resendState['locked_until'] ?? null;
            $this->addError('guestEmailOtp', "OTP resend limit reached. Please wait {$seconds} second(s) before requesting another OTP.");

            return;
        }

        if ($isResend && max($this->guestEmailOtpResendCount, $this->guestEmailOtpResendStateCount($resendState)) >= $this->otpMaxResends()) {
            $this->addError('guestEmailOtp', 'OTP resend limit reached. Please wait for the lock window to expire.');

            return;
        }

        $cooldownKey = $this->guestOtpCooldownThrottleKey($normalizedEmail, (string) request()->ip());

        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = max(1, RateLimiter::availableIn($cooldownKey));
            $this->addError('guestEmailOtp', "Please wait {$seconds} second(s) before resending OTP.");

            return;
        }

        RateLimiter::hit($cooldownKey, $this->otpResendCooldownSeconds());

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes($this->otpExpiresInMinutes());
        $cooldownUntil = now()->addSeconds($this->otpResendCooldownSeconds());

        $this->guestEmailOtpHash = Hash::make($code);
        $this->guestEmailOtpExpiresAt = $expiresAt->toDateTimeString();
        $this->guestEmailOtpCooldownUntil = $cooldownUntil->toDateTimeString();
        $this->guestEmailOtpVerified = false;
        $this->guestEmailOtpTargetEmail = $normalizedEmail;
        $this->guestEmailOtp = '';
        $this->guestEmailOtpResendCount = $isResend
            ? $this->recordGuestEmailOtpResend($normalizedEmail)
            : $this->guestEmailOtpResendStateCount($resendState);
        $latestResendState = $this->guestEmailOtpResendState($normalizedEmail);
        $this->guestEmailOtpResendLockedUntil = $latestResendState['locked_until'] ?? null;

        Mail::send('appointment.emails.guest-booking-otp', [
            'otp' => $code,
            'email' => $normalizedEmail,
            'expiresAt' => $expiresAt,
        ], function ($message) use ($normalizedEmail) {
            $message->to($normalizedEmail);
            $message->subject('Your Tejadent Booking OTP');
        });

        $remainingResends = $this->remainingGuestOtpResends();
        $this->otpMessage = $isResend
            ? "A new OTP was sent. You have {$remainingResends} resend attempt(s) left."
            : 'OTP sent. Check your email and enter the 6-digit code below.';

        $this->dispatch('otp-ready', [
            'expiresAt' => $this->guestEmailOtpExpiresAt,
            'cooldownUntil' => $this->guestEmailOtpCooldownUntil,
            'resendsRemaining' => $this->guestOtpResendsRemaining,
        ]);
    }

    public function verifyGuestEmailOtp()
    {
        if (Auth::check()) {
            return;
        }

        $this->validateBookingFormData();

        $this->validate([
            'email' => 'required|email',
            'guestEmailOtp' => 'required|digits:6',
        ], [
            'guestEmailOtp.required' => 'Please enter the OTP code.',
            'guestEmailOtp.digits' => 'OTP must be 6 digits.',
        ]);

        $normalizedEmail = $this->normalizeEmail($this->email);
        if ($normalizedEmail === '' || $normalizedEmail !== $this->guestEmailOtpTargetEmail) {
            $this->addError('guestEmailOtp', 'Email changed. Please request a new OTP.');
            $this->resetGuestEmailOtpState();
            $this->guestOtpStepActive = false;

            return;
        }

        if (empty($this->guestEmailOtpHash) || empty($this->guestEmailOtpExpiresAt)) {
            $this->addError('guestEmailOtp', 'Please request an OTP first.');

            return;
        }

        $verifyThrottleKey = $this->guestOtpVerifyThrottleKey($normalizedEmail, (string) request()->ip());
        if (RateLimiter::tooManyAttempts($verifyThrottleKey, $this->otpVerifyMaxAttempts())) {
            $seconds = RateLimiter::availableIn($verifyThrottleKey);
            $minutes = max(1, (int) ceil($seconds / 60));
            $this->addError('guestEmailOtp', "Too many OTP attempts. Try again in {$minutes} minute(s).");

            return;
        }

        if (now()->greaterThan(Carbon::parse((string) $this->guestEmailOtpExpiresAt))) {
            $this->resetGuestEmailOtpState();
            $this->addError('guestEmailOtp', 'OTP expired. Please request a new one.');

            return;
        }

        if (! Hash::check((string) $this->guestEmailOtp, (string) $this->guestEmailOtpHash)) {
            RateLimiter::hit($verifyThrottleKey, $this->otpVerifyBlockSeconds());
            $this->addError('guestEmailOtp', 'Invalid OTP code.');

            return;
        }

        RateLimiter::clear($verifyThrottleKey);
        $this->guestEmailOtpVerified = true;
        $this->guestEmailOtp = '';
        $this->guestEmailOtpHash = null;
        $this->guestEmailOtpExpiresAt = null;

        if (! $this->assertBookingStillAvailable()) {
            $this->guestOtpStepActive = false;
            $this->addError('guestEmailOtp', 'We could not complete your booking. Please review the form and try again.');

            return;
        }

        $this->guestOtpStepActive = false;

        return $this->createAppointmentAndRedirect();
    }

    public function cancelGuestOtpStep(): void
    {
        $this->guestOtpStepActive = false;
        $this->guestEmailOtp = '';
        $this->resetValidation('guestEmailOtp');
        $this->resetRecaptchaState();
    }

    protected function createAppointmentAndRedirect()
    {
        $appointmentDateTime = Carbon::parse($this->selectedDate.' '.$this->selectedSlot)->toDateTimeString();

        $appointmentPayload = [
            'patient_id' => null,
            'service_id' => $this->service_id,
            'appointment_date' => $appointmentDateTime,
            'status' => $this->resolveNewAppointmentStatus(),
            'requester_user_id' => Auth::id(),
            'requester_first_name' => $this->first_name,
            'requester_last_name' => $this->last_name,
            'requester_contact_number' => $this->contact_number,
            'requester_email' => $this->email,
            'modified_by' => Auth::check() ? Auth::user()->username : 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('appointments', 'requester_middle_name')) {
            $appointmentPayload['requester_middle_name'] = $this->normalizedRequesterMiddleName();
        }

        if (Schema::hasColumn('appointments', 'booking_type')) {
            $appointmentPayload['booking_type'] = 'online_appointment';
        }

        if (Schema::hasColumn('appointments', 'requester_birth_date')) {
            $appointmentPayload['requester_birth_date'] = $this->isBookingForSelf()
                ? $this->resolvedPatientBirthDate()
                : null;
        }

        if (Schema::hasColumn('appointments', 'booking_for_other')) {
            $appointmentPayload['booking_for_other'] = ! $this->isBookingForSelf();
        }

        if (Schema::hasColumn('appointments', 'requested_patient_first_name')) {
            $appointmentPayload['requested_patient_first_name'] = $this->isBookingForSelf()
                ? null
                : $this->resolvedPatientFirstName();
        }

        if (Schema::hasColumn('appointments', 'requested_patient_last_name')) {
            $appointmentPayload['requested_patient_last_name'] = $this->isBookingForSelf()
                ? null
                : $this->resolvedPatientLastName();
        }

        if (Schema::hasColumn('appointments', 'requested_patient_middle_name')) {
            $appointmentPayload['requested_patient_middle_name'] = $this->isBookingForSelf()
                ? null
                : $this->normalizedRequestedPatientMiddleName();
        }

        if (Schema::hasColumn('appointments', 'requested_patient_birth_date')) {
            $appointmentPayload['requested_patient_birth_date'] = $this->isBookingForSelf()
                ? null
                : $this->resolvedPatientBirthDate();
        }

        if (Schema::hasColumn('appointments', 'requester_relationship_to_patient')) {
            $appointmentPayload['requester_relationship_to_patient'] = $this->isBookingForSelf()
                ? null
                : $this->normalizedRelationshipToPatient();
        }

        DB::table('appointments')->insert($appointmentPayload);

        $service = DB::table('services')->where('id', $this->service_id)->first();

        try {
            Mail::send('appointment.emails.appointment-confirmation', [
                'name' => trim($this->resolvedPatientFirstName().' '.$this->resolvedPatientLastName()),
                'appointment_date' => Carbon::parse($appointmentDateTime)->format('F j, Y g:i A'),
                'service_name' => $service?->service_name ?? 'Service',
            ], function ($message) {
                $message->to($this->email);
                $message->subject('Appointment Confirmation');
            });
        } catch (Throwable $th) {
            // Do not block booking if email fails
        }

        session()->flash('success', 'Appointment requested! Please check your email for the details of your appointment.');

        if (Auth::check() && (int) (Auth::user()->role ?? 0) === 3) {
            return redirect()->route('patient.dashboard');
        }

        return redirect()->route('book');
    }

    protected function validateBookingFormData(): void
    {
        $this->sanitizeBookingFields();

        $this->validate([
            'first_name' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'last_name' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'middle_name' => ['nullable', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'email' => 'required|email',
            'service_id' => 'required',
            'selectedDate' => 'required|date_format:Y-m-d|after_or_equal:today',
            'selectedSlot' => 'required',
            'contact_number' => ['required', 'string', self::PH_CONTACT_RULE],
            'booking_for' => 'required|in:self,someone_else',
            'booking_agreement' => 'accepted',
        ], [
            'contact_number.regex' => 'Contact number must be exactly 10 digits after +63.',
            'booking_agreement.accepted' => 'Please confirm the booking agreement before submitting your request.',
        ]);

        $patientRules = $this->isBookingForSelf()
            ? [
                'patient_birth_date' => 'required|date|before_or_equal:today',
            ]
            : [
                'patient_first_name' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
                'patient_middle_name' => ['nullable', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
                'patient_last_name' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
                'patient_birth_date' => 'required|date|before_or_equal:today',
                'relationship_to_patient' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            ];

        $this->validate($patientRules, [
            'patient_birth_date.required' => 'Please provide the patient birth date.',
            'patient_birth_date.date' => 'Please enter a valid patient birth date.',
            'patient_birth_date.before_or_equal' => 'Patient birth date cannot be in the future.',
            'relationship_to_patient.required' => 'Please tell us your relationship to the patient.',
        ]);
    }

    protected function prefillFromPreviousBooking(): void
    {
        $previousBooking = $this->findPreviousBookingProfile();
        if (! $previousBooking) {
            return;
        }

        $this->first_name = $this->preferExistingValue($this->first_name, $previousBooking->first_name ?? null);
        $this->last_name = $this->preferExistingValue($this->last_name, $previousBooking->last_name ?? null);
        $this->middle_name = $this->preferExistingValue($this->middle_name, $previousBooking->middle_name ?? null);
        $this->contact_number = $this->preferExistingValue($this->contact_number, $previousBooking->contact_number ?? null);

        if ($this->isBookingForSelf()) {
            $this->patient_birth_date = $this->preferExistingValue($this->patient_birth_date, $previousBooking->birth_date ?? null);
        }

        $this->sanitizeBookingFields();
    }

    protected function findPreviousBookingProfile(): ?object
    {
        $email = $this->normalizeEmail($this->email);
        $hasRequesterBirthDate = Schema::hasColumn('appointments', 'requester_birth_date');
        $hasRequestedPatientBirthDate = Schema::hasColumn('appointments', 'requested_patient_birth_date');
        $hasRequesterMiddleName = Schema::hasColumn('appointments', 'requester_middle_name');
        $hasPatientMiddleName = Schema::hasColumn('patients', 'middle_name');
        $requesterMiddleNameExpression = match (true) {
            $hasRequesterMiddleName && $hasPatientMiddleName => 'COALESCE(appointments.requester_middle_name, patients.middle_name)',
            $hasRequesterMiddleName => 'appointments.requester_middle_name',
            $hasPatientMiddleName => 'patients.middle_name',
            default => 'NULL',
        };

        $query = DB::table('appointments as appointments')
            ->leftJoin('patients', 'patients.id', '=', 'appointments.patient_id')
            ->selectRaw(
                'COALESCE(appointments.requester_first_name, patients.first_name) as first_name,
                 COALESCE(appointments.requester_last_name, patients.last_name) as last_name,
                 '.$requesterMiddleNameExpression.' as middle_name,
                 COALESCE(appointments.requester_contact_number, patients.mobile_number) as contact_number,
                 '.($hasRequestedPatientBirthDate
                    ? 'COALESCE(appointments.requested_patient_birth_date, appointments.requester_birth_date, patients.birth_date)'
                    : ($hasRequesterBirthDate
                        ? 'COALESCE(appointments.requester_birth_date, patients.birth_date)'
                        : 'patients.birth_date')).' as birth_date'
            )
            ->orderByDesc('appointments.appointment_date');

        if (Auth::check()) {
            $userId = Auth::id();

            $query->where(function ($builder) use ($userId, $email) {
                $builder->where('appointments.requester_user_id', $userId);

                if ($email !== '') {
                    $builder->orWhereRaw('LOWER(appointments.requester_email) = ?', [$email])
                        ->orWhereRaw('LOWER(patients.email_address) = ?', [$email]);
                }
            });
        } else {
            if ($email === '') {
                return null;
            }

            $query->where(function ($builder) use ($email) {
                $builder->whereRaw('LOWER(appointments.requester_email) = ?', [$email])
                    ->orWhereRaw('LOWER(patients.email_address) = ?', [$email]);
            });
        }

        return $query->first();
    }

    protected function preferExistingValue(mixed $currentValue, mixed $fallbackValue): mixed
    {
        $current = trim((string) ($currentValue ?? ''));
        if ($current !== '') {
            return $currentValue;
        }

        $fallback = trim((string) ($fallbackValue ?? ''));

        return $fallback !== '' ? $fallbackValue : $currentValue;
    }

    protected function verifyRecaptchaForGuest(): bool
    {
        if (empty($this->recaptchaToken)) {
            $this->addError('recaptcha', 'Please complete the CAPTCHA verification.');

            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $this->recaptchaToken,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->json('success')) {
            $this->resetRecaptchaState();
            $this->addError('recaptcha', 'CAPTCHA verification failed.');

            return false;
        }

        $this->resetRecaptchaState();

        return true;
    }

    protected function assertBookingStillAvailable(): bool
    {
        $appointmentDateTime = Carbon::parse($this->selectedDate.' '.$this->selectedSlot)->toDateTimeString();
        $slotStart = Carbon::parse($appointmentDateTime)->seconds(0);

        // Resolve actual service duration for range-based checks
        $durationMinutes = 60;
        if (! empty($this->service_id)) {
            $service = DB::table('services')->where('id', $this->service_id)->first();
            if ($service && ! empty($service->duration)) {
                [$h, $m] = array_map('intval', explode(':', $service->duration));
                $durationMinutes = max(60, $h * 60 + $m);
            }
        }

        $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);

        // Reject if the appointment would end after clinic closing time
        $clinicClose = Carbon::parse($this->selectedDate.' 18:00:00');
        if ($slotEnd->gt($clinicClose)) {
            $this->addError('selectedSlot', 'This service cannot start at this time as it would end after clinic hours.');
            $this->availableSlots = $this->generateSlots($this->selectedDate);

            return false;
        }

        // Duration-aware blocked slot check
        $blockedCapacity = 0;
        if ($this->blockedSlotsEnabled()) {
            $blockedCapacity = app(BlockedSlotService::class)->blockedCapacityForRange($slotStart, $slotEnd, self::SLOT_CAPACITY);
            $isBlocked = $blockedCapacity >= self::SLOT_CAPACITY;

            if ($isBlocked) {
                $this->addError('selectedSlot', 'This time slot is unavailable. Please choose another time.');
                $this->availableSlots = $this->generateSlots($this->selectedDate);

                return false;
            }
        }

        // Duration-aware approved appointment capacity check
        $activeSlotBookings = app(CalendarQueryService::class)
            ->countConflicts($slotStart, $slotEnd, self::APPROVED_SLOT_STATUSES);

        $remainingCapacity = max(0, self::SLOT_CAPACITY - $blockedCapacity);

        if ($activeSlotBookings >= $remainingCapacity) {
            $this->addError('selectedSlot', 'This time slot is already full. Please choose another time.');
            $this->availableSlots = $this->generateSlots($this->selectedDate);

            return false;
        }

        // Pending request cap — still exact-slot based (Phase 4 scope)
        $totalActiveRequestsInSlot = DB::table('appointments')
            ->where('appointment_date', $appointmentDateTime)
            ->whereNotIn('status', self::INACTIVE_APPOINTMENT_STATUSES)
            ->count();

        if ($totalActiveRequestsInSlot >= self::REQUEST_SLOT_CAP) {
            $this->addError('selectedSlot', 'This time slot already reached the maximum of 5 requests. Please choose another time.');
            $this->availableSlots = $this->generateSlots($this->selectedDate);

            return false;
        }

        $hasRequestedPatientFirstName = Schema::hasColumn('appointments', 'requested_patient_first_name');
        $hasRequestedPatientLastName = Schema::hasColumn('appointments', 'requested_patient_last_name');
        $hasRequestedPatientBirthDate = Schema::hasColumn('appointments', 'requested_patient_birth_date');
        $hasRequesterBirthDate = Schema::hasColumn('appointments', 'requester_birth_date');

        $hasActiveAppointmentQuery = DB::table('appointments')
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->where(function ($query) use (
                $hasRequestedPatientFirstName,
                $hasRequestedPatientLastName,
                $hasRequestedPatientBirthDate,
                $hasRequesterBirthDate
            ) {
                if ($hasRequestedPatientFirstName) {
                    $query->whereRaw('LOWER(COALESCE(requested_patient_first_name, requester_first_name, \'\')) = ?', [
                        strtolower($this->resolvedPatientFirstName()),
                    ]);
                } else {
                    $query->whereRaw('LOWER(COALESCE(requester_first_name, \'\')) = ?', [
                        strtolower($this->resolvedPatientFirstName()),
                    ]);
                }

                if ($hasRequestedPatientLastName) {
                    $query->whereRaw('LOWER(COALESCE(requested_patient_last_name, requester_last_name, \'\')) = ?', [
                        strtolower($this->resolvedPatientLastName()),
                    ]);
                } else {
                    $query->whereRaw('LOWER(COALESCE(requester_last_name, \'\')) = ?', [
                        strtolower($this->resolvedPatientLastName()),
                    ]);
                }

                $patientBirthDate = $this->resolvedPatientBirthDate();
                if ($patientBirthDate !== null) {
                    if ($hasRequestedPatientBirthDate) {
                        $query->whereRaw('DATE(COALESCE(requested_patient_birth_date, requester_birth_date)) = ?', [$patientBirthDate]);
                    } elseif ($hasRequesterBirthDate) {
                        $query->whereDate('requester_birth_date', $patientBirthDate);
                    }
                }
            });

        $hasActiveAppointment = $hasActiveAppointmentQuery->exists();

        if ($hasActiveAppointment) {
            $message = $this->isBookingForSelf()
                ? 'You already have a pending or upcoming appointment request. Please wait until your current request is completed before booking a new one.'
                : 'This patient already has a pending or upcoming appointment request. Please review the existing request first.';

            $this->addError('selectedSlot', $message);

            return false;
        }

        return true;
    }

    public function render()
    {
        $services = DB::table('services')->get();

        return view('livewire.appointment.book-appointment', compact('services'));
    }

    protected function resolveNewAppointmentStatus(): string
    {
        $preferred = 'Pending';
        $statuses = $this->getAppointmentStatuses();

        if (empty($statuses)) {
            return $preferred;
        }

        if (in_array($preferred, $statuses, true)) {
            return $preferred;
        }

        foreach (['Scheduled', 'Waiting'] as $fallback) {
            if (in_array($fallback, $statuses, true)) {
                return $fallback;
            }
        }

        return $statuses[0];
    }

    protected function getAppointmentStatuses(): array
    {
        if (is_array($this->appointmentStatuses)) {
            return $this->appointmentStatuses;
        }

        try {
            $column = DB::selectOne("SHOW COLUMNS FROM appointments LIKE 'status'");
            $type = $column->Type ?? $column->type ?? '';

            if (preg_match('/^enum\\((.*)\\)$/i', $type, $matches) === 1) {
                $rawValues = str_getcsv($matches[1], ',', "'");
                $this->appointmentStatuses = array_values(array_filter($rawValues, fn ($v) => $v !== null && $v !== ''));

                return $this->appointmentStatuses;
            }
        } catch (Throwable $e) {
            // Fall back to app default when schema inspection is unavailable.
        }

        $this->appointmentStatuses = [];

        return $this->appointmentStatuses;
    }

    protected function isValidSelectedDate(string $date): bool
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d') === $date;
        } catch (Throwable $e) {
            return false;
        }
    }

    protected function blockedSlotsEnabled(): bool
    {
        if ($this->blockedSlotsTableExists === null) {
            $this->blockedSlotsTableExists = Schema::hasTable('blocked_slots');
        }

        return $this->blockedSlotsTableExists;
    }

    protected function isBlockedBySlotCollection(Carbon $slotStart, Carbon $slotEnd, $blockedSlots): bool
    {
        return $this->blockedCapacityBySlotCollection($slotStart, $slotEnd, $blockedSlots) >= self::SLOT_CAPACITY;
    }

    protected function blockedCapacityBySlotCollection(Carbon $slotStart, Carbon $slotEnd, $blockedSlots): int
    {
        if (! $this->blockedSlotsEnabled() || collect($blockedSlots)->isEmpty()) {
            return 0;
        }

        return app(BlockedSlotService::class)->blockedCapacityForRangeFromCollection($slotStart, $slotEnd, $blockedSlots, self::SLOT_CAPACITY);
    }

    protected function resetGuestEmailOtpState(): void
    {
        $this->guestEmailOtp = '';
        $this->guestEmailOtpHash = null;
        $this->guestEmailOtpExpiresAt = null;
        $this->guestEmailOtpCooldownUntil = null;
        $this->guestEmailOtpResendLockedUntil = null;
        $this->guestEmailOtpVerified = false;
        $this->guestEmailOtpTargetEmail = null;
        $this->guestEmailOtpResendCount = 0;
    }

    public function getGuestOtpResendsRemainingProperty(): int
    {
        return $this->remainingGuestOtpResends();
    }

    protected function remainingGuestOtpResends(): int
    {
        return max(0, $this->otpMaxResends() - (int) $this->guestEmailOtpResendCount);
    }

    protected function normalizeEmail(mixed $email): string
    {
        return InputSanitizer::sanitizeEmail($email);
    }

    protected function isBookingForSelf(): bool
    {
        return $this->booking_for !== 'someone_else';
    }

    protected function resolvedPatientFirstName(): string
    {
        return InputSanitizer::sanitizeTitleCase($this->isBookingForSelf() ? $this->first_name : $this->patient_first_name);
    }

    protected function resolvedPatientLastName(): string
    {
        return InputSanitizer::sanitizeTitleCase($this->isBookingForSelf() ? $this->last_name : $this->patient_last_name);
    }

    protected function resolvedPatientBirthDate(): ?string
    {
        $birthDate = trim((string) $this->patient_birth_date);

        return $birthDate !== '' ? $birthDate : null;
    }

    protected function normalizedRelationshipToPatient(): ?string
    {
        $relationship = InputSanitizer::sanitizeTitleCase($this->relationship_to_patient);

        return $relationship !== '' ? $relationship : null;
    }

    protected function normalizedRequesterMiddleName(): ?string
    {
        $middleName = InputSanitizer::sanitizeTitleCase($this->middle_name);

        return $middleName !== '' ? $middleName : null;
    }

    protected function normalizedRequestedPatientMiddleName(): ?string
    {
        $middleName = InputSanitizer::sanitizeTitleCase($this->patient_middle_name);

        return $middleName !== '' ? $middleName : null;
    }

    protected function canResumeGuestOtpStep(): bool
    {
        if ($this->guestEmailOtpVerified) {
            return false;
        }

        $normalizedEmail = $this->normalizeEmail($this->email);
        if ($normalizedEmail === '' || $normalizedEmail !== $this->guestEmailOtpTargetEmail) {
            return false;
        }

        if (empty($this->guestEmailOtpHash) || empty($this->guestEmailOtpExpiresAt)) {
            return false;
        }

        try {
            return now()->lessThanOrEqualTo(Carbon::parse((string) $this->guestEmailOtpExpiresAt));
        } catch (Throwable $e) {
            return false;
        }
    }

    protected function resetRecaptchaState(): void
    {
        $this->recaptchaToken = null;
        $this->dispatch('reset-recaptcha');
    }

    protected function guestOtpSendThrottleKey(string $email, string $ip): string
    {
        return 'guest-book-otp-send:'.sha1($email.'|'.$ip);
    }

    protected function otpExpiresInMinutes(): int
    {
        return max(1, (int) config('verification.otp_expires_in_minutes', 3));
    }

    protected function otpResendCooldownSeconds(): int
    {
        return max(1, (int) config('verification.resend_cooldown_seconds', 60));
    }

    protected function otpMaxResends(): int
    {
        return max(0, (int) config('verification.max_resends', 3));
    }

    protected function otpResendLockSeconds(): int
    {
        return max(1, (int) config('verification.otp_resend_lock_seconds', 600));
    }

    protected function otpVerifyMaxAttempts(): int
    {
        return max(1, (int) config('verification.otp_verify_max_attempts', 5));
    }

    protected function otpVerifyBlockSeconds(): int
    {
        return max(1, (int) config('verification.otp_verify_block_seconds', 300));
    }

    protected function guestEmailOtpResendState(string $email): array
    {
        return Cache::get($this->guestEmailOtpResendStateKey($email), [
            'count' => 0,
            'locked_until' => null,
        ]);
    }

    protected function guestEmailOtpResendStateCount(array $state): int
    {
        return (int) ($state['count'] ?? 0);
    }

    protected function guestEmailOtpResendLockRemaining(array $state): int
    {
        $lockedUntil = $state['locked_until'] ?? null;

        if (! $lockedUntil) {
            return 0;
        }

        return max(0, now()->diffInSeconds(Carbon::parse($lockedUntil), false));
    }

    protected function recordGuestEmailOtpResend(string $email): int
    {
        $state = $this->guestEmailOtpResendState($email);
        $count = min($this->otpMaxResends(), $this->guestEmailOtpResendStateCount($state) + 1);
        $lockedUntil = $count >= $this->otpMaxResends()
            ? now()->addSeconds($this->otpResendLockSeconds())->toDateTimeString()
            : null;

        Cache::put(
            $this->guestEmailOtpResendStateKey($email),
            [
                'count' => $count,
                'locked_until' => $lockedUntil,
            ],
            now()->addSeconds($this->otpResendLockSeconds())
        );

        return $count;
    }

    protected function guestOtpCooldownThrottleKey(string $email, string $ip): string
    {
        return 'guest-book-otp-cooldown:'.sha1($email.'|'.$ip);
    }

    protected function guestEmailOtpResendStateKey(string $email): string
    {
        return 'guest-book-otp-resend-state:'.sha1($email);
    }

    protected function guestOtpVerifyThrottleKey(string $email, string $ip): string
    {
        return 'guest-book-otp-verify:'.sha1($email.'|'.$ip);
    }

    protected function sanitizeBookingFields(): void
    {
        foreach (['first_name', 'last_name', 'middle_name', 'patient_first_name', 'patient_middle_name', 'patient_last_name', 'relationship_to_patient'] as $field) {
            $this->{$field} = InputSanitizer::sanitizeTitleCase($this->{$field} ?? '');
        }

        $this->contact_number = InputSanitizer::sanitizeCountryCodeLocalNumber($this->contact_number ?? '');
        $this->email = InputSanitizer::sanitizeEmail($this->email ?? '');
    }

    protected function sanitizeField(string $propertyName): void
    {
        if (in_array($propertyName, ['first_name', 'last_name', 'middle_name', 'patient_first_name', 'patient_middle_name', 'patient_last_name', 'relationship_to_patient'], true)) {
            $this->{$propertyName} = InputSanitizer::sanitizeTitleCase($this->{$propertyName} ?? '');
            return;
        }

        if ($propertyName === 'contact_number') {
            $this->contact_number = InputSanitizer::sanitizeCountryCodeLocalNumber($this->contact_number ?? '');
            return;
        }

        if ($propertyName === 'email') {
            $this->email = InputSanitizer::sanitizeEmail($this->email ?? '');
        }
    }
}
