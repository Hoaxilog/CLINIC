<?php

namespace App\Livewire\appointment;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    private const PH_CONTACT_RULE = 'regex:/^\d{11}$/';

    protected const SLOT_CAPACITY = 2;

    protected const APPROVED_SLOT_STATUSES = ['Scheduled', 'Waiting', 'Ongoing'];

    protected const REQUEST_SLOT_CAP = 5;

    protected const INACTIVE_APPOINTMENT_STATUSES = ['Cancelled', 'Completed'];

    protected const OTP_MAX_SEND_ATTEMPTS = 4;

    protected const OTP_MAX_SEND_WINDOW_SECONDS = 15 * 60;

    protected const OTP_RESEND_COOLDOWN_SECONDS = 60;

    protected const OTP_MAX_RESENDS = 3;

    protected const OTP_EXPIRES_IN_MINUTES = 5;

    // Form data
    public $first_name;

    public $last_name;

    public $contact_number;

    public $email;

    public $booking_for = 'self';

    public $patient_first_name = '';

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
            $this->contact_number = $user->contact ?? '';

            $username = trim((string) ($user->username ?? ''));
            if ($username !== '' && ! filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $nameParts = preg_split('/\s+/', $username);
                $this->first_name = $nameParts[0] ?? '';
                $this->last_name = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
            } else {
                $this->first_name = '';
                $this->last_name = '';
            }

            $this->prefillFromPreviousBooking();
        }
    }

    // This runs automatically when $selectedDate is updated via JavaScript
    public function updatedSelectedDate($date)
    {
        if (empty($date) || ! $this->isValidSelectedDate($date)) {
            $this->availableSlots = [];
            $this->selectedSlot = null;
            $this->selectedDate = null;
            $this->dispatch('book-calendar-refresh', selectedDate: null);

            return;
        }

        $this->availableSlots = $this->generateSlots($date);
        $this->dispatch('book-calendar-refresh', selectedDate: $date);
    }

    public function updatedEmail($value): void
    {
        $normalizedEmail = $this->normalizeEmail($value);
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
        // Keep server-side error bag in sync with user edits so error UI does not reappear after rerenders.
        $clearableFields = [
            'first_name',
            'last_name',
            'contact_number',
            'email',
            'booking_for',
            'patient_first_name',
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
        $this->patient_last_name = '';
        $this->patient_birth_date = '';
        $this->relationship_to_patient = '';

        $this->resetValidation([
            'patient_first_name',
            'patient_last_name',
            'patient_birth_date',
            'relationship_to_patient',
        ]);

        if ($value !== 'someone_else') {
            $this->prefillFromPreviousBooking();
        }
    }

    public function generateSlots($dateString)
    {
        $date = Carbon::parse($dateString);
        // Default hours since schedules table does not exist
        $startTime = Carbon::parse($dateString.' 09:00:00');
        $endTime = Carbon::parse($dateString.' 20:00:00');
        $duration = 60; // minutes per slot
        $blockedSlots = collect();

        if ($this->blockedSlotsEnabled()) {
            $blockedSlots = DB::table('blocked_slots')
                ->whereDate('date', $dateString)
                ->select('start_time', 'end_time')
                ->get();
        }

        $bookedCounts = DB::table('appointments')
            ->whereDate('appointment_date', $dateString)
            ->whereIn('status', self::APPROVED_SLOT_STATUSES)
            ->selectRaw('TIME(appointment_date) as time_slot, COUNT(*) as total')
            ->groupBy('time_slot')
            ->pluck('total', 'time_slot')
            ->toArray();

        $requestCounts = DB::table('appointments')
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
            $isPast = $slotDateTime->lt(now());
            $isBlocked = $this->isBlockedBySlotCollection($slotDateTime, $slotEndDateTime, $blockedSlots);
            $slots[] = [
                'time' => $startTime->format('h:i A'),
                'value' => $slotTime,
                'is_full' => $currentCount >= self::SLOT_CAPACITY || $currentRequests >= self::REQUEST_SLOT_CAP,
                'is_past' => $isPast,
                'is_blocked' => $isBlocked,
            ];
            $startTime->addMinutes($duration);
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

        if (! Auth::check()) {
            if ($this->canResumeGuestOtpStep()) {
                if (! $this->assertBookingStillAvailable()) {
                    return;
                }

                $this->guestEmailOtp = '';
                $this->resetValidation('guestEmailOtp');
                $this->otpMessage = 'Enter the 6-digit code we already sent to your email.';
                $this->guestOtpStepActive = true;

                return;
            }

            if (! $this->verifyRecaptchaForGuest()) {
                return;
            }

            if (! $this->assertBookingStillAvailable()) {
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
            $this->addError('guestEmailOtp', 'Your current OTP session is no longer active. Please submit the form again.');

            return;
        }

        if ($isResend && $this->remainingGuestOtpResends() < 1) {
            $this->addError('guestEmailOtp', 'You can only resend the OTP 3 times. Please submit the form again to request a new OTP session.');

            return;
        }

        $sendThrottleKey = $this->guestOtpSendThrottleKey($normalizedEmail, (string) request()->ip());
        $cooldownKey = $this->guestOtpCooldownThrottleKey($normalizedEmail, (string) request()->ip());

        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = max(1, RateLimiter::availableIn($cooldownKey));
            $this->addError('guestEmailOtp', "Please wait {$seconds} second(s) before resending OTP.");

            return;
        }

        if (RateLimiter::tooManyAttempts($sendThrottleKey, self::OTP_MAX_SEND_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($sendThrottleKey);
            $minutes = max(1, (int) ceil($seconds / 60));
            $this->addError('guestEmailOtp', 'You have reached the maximum of '.self::OTP_MAX_SEND_ATTEMPTS." OTP sends. Try again in {$minutes} minute(s).");

            return;
        }

        RateLimiter::hit($cooldownKey, self::OTP_RESEND_COOLDOWN_SECONDS);
        RateLimiter::hit($sendThrottleKey, self::OTP_MAX_SEND_WINDOW_SECONDS);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRES_IN_MINUTES);
        $cooldownUntil = now()->addSeconds(self::OTP_RESEND_COOLDOWN_SECONDS);

        $this->guestEmailOtpHash = Hash::make($code);
        $this->guestEmailOtpExpiresAt = $expiresAt->toDateTimeString();
        $this->guestEmailOtpCooldownUntil = $cooldownUntil->toDateTimeString();
        $this->guestEmailOtpVerified = false;
        $this->guestEmailOtpTargetEmail = $normalizedEmail;
        $this->guestEmailOtp = '';
        $this->guestEmailOtpResendCount = $isResend ? ((int) $this->guestEmailOtpResendCount + 1) : 0;

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
        if (RateLimiter::tooManyAttempts($verifyThrottleKey, 5)) {
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
            RateLimiter::hit($verifyThrottleKey, 15 * 60);
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
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'service_id' => 'required',
            'selectedDate' => 'required|date_format:Y-m-d|after_or_equal:today',
            'selectedSlot' => 'required',
            'contact_number' => ['required', 'string', self::PH_CONTACT_RULE],
            'booking_for' => 'required|in:self,someone_else',
            'booking_agreement' => 'accepted',
        ], [
            'contact_number.regex' => 'Contact number must be exactly 11 digits.',
            'booking_agreement.accepted' => 'Please confirm the booking agreement before submitting your request.',
        ]);

        $patientRules = $this->isBookingForSelf()
            ? [
                'patient_birth_date' => 'required|date|before_or_equal:today',
            ]
            : [
                'patient_first_name' => 'required',
                'patient_last_name' => 'required',
                'patient_birth_date' => 'required|date|before_or_equal:today',
                'relationship_to_patient' => 'required|string|max:100',
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
        $this->contact_number = $this->preferExistingValue($this->contact_number, $previousBooking->contact_number ?? null);

        if ($this->isBookingForSelf()) {
            $this->patient_birth_date = $this->preferExistingValue($this->patient_birth_date, $previousBooking->birth_date ?? null);
        }
    }

    protected function findPreviousBookingProfile(): ?object
    {
        $email = $this->normalizeEmail($this->email);
        $hasRequesterBirthDate = Schema::hasColumn('appointments', 'requester_birth_date');
        $hasRequestedPatientBirthDate = Schema::hasColumn('appointments', 'requested_patient_birth_date');

        $query = DB::table('appointments as appointments')
            ->leftJoin('patients', 'patients.id', '=', 'appointments.patient_id')
            ->selectRaw(
                'COALESCE(appointments.requester_first_name, patients.first_name) as first_name,
                COALESCE(appointments.requester_last_name, patients.last_name) as last_name,
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
        $slotEnd = $slotStart->copy()->addHour();

        if ($this->blockedSlotsEnabled()) {
            $isBlocked = DB::table('blocked_slots')
                ->whereDate('date', $slotStart->toDateString())
                ->where('start_time', '<', $slotEnd->format('H:i:s'))
                ->where('end_time', '>', $slotStart->format('H:i:s'))
                ->exists();

            if ($isBlocked) {
                $this->addError('selectedSlot', 'This time slot is unavailable. Please choose another time.');
                $this->availableSlots = $this->generateSlots($this->selectedDate);

                return false;
            }
        }

        $activeSlotBookings = DB::table('appointments')
            ->where('appointment_date', $appointmentDateTime)
            ->whereIn('status', self::APPROVED_SLOT_STATUSES)
            ->count();

        if ($activeSlotBookings >= self::SLOT_CAPACITY) {
            $this->addError('selectedSlot', 'This time slot is already full. Please choose another time.');
            $this->availableSlots = $this->generateSlots($this->selectedDate);

            return false;
        }

        $totalActiveRequestsInSlot = DB::table('appointments')
            ->where('appointment_date', $appointmentDateTime)
            ->whereNotIn('status', self::INACTIVE_APPOINTMENT_STATUSES)
            ->count();

        if ($totalActiveRequestsInSlot >= self::REQUEST_SLOT_CAP) {
            $this->addError('selectedSlot', 'This time slot already reached the maximum of 5 requests. Please choose another time.');
            $this->availableSlots = $this->generateSlots($this->selectedDate);

            return false;
        }

        $hasActiveAppointmentQuery = DB::table('appointments')
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->where(function ($query) {
                if ($this->isBookingForSelf()) {
                    if (Auth::check()) {
                        $query->where('requester_user_id', Auth::id());

                        if (! empty($this->email)) {
                            $query->orWhere('requester_email', $this->email);
                        }

                        return;
                    }

                    $query->where('requester_email', $this->email);

                    return;
                }

                if (Schema::hasColumn('appointments', 'requested_patient_first_name')) {
                    $query->whereRaw('LOWER(COALESCE(requested_patient_first_name, requester_first_name, \'\')) = ?', [
                        strtolower($this->resolvedPatientFirstName()),
                    ]);
                } else {
                    $query->whereRaw('LOWER(COALESCE(requester_first_name, \'\')) = ?', [
                        strtolower($this->resolvedPatientFirstName()),
                    ]);
                }

                if (Schema::hasColumn('appointments', 'requested_patient_last_name')) {
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
                    if (Schema::hasColumn('appointments', 'requested_patient_birth_date')) {
                        $query->whereRaw('DATE(COALESCE(requested_patient_birth_date, requester_birth_date)) = ?', [$patientBirthDate]);
                    } elseif (Schema::hasColumn('appointments', 'requester_birth_date')) {
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
        $layout = 'layouts.app';

        return view('livewire.appointment.book-appointment', compact('services'))
            ->layout($layout);
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
        if (! $this->blockedSlotsEnabled() || $blockedSlots->isEmpty()) {
            return false;
        }

        foreach ($blockedSlots as $blockedSlot) {
            $blockedStart = Carbon::parse($slotStart->toDateString().' '.$blockedSlot->start_time)->seconds(0);
            $blockedEnd = Carbon::parse($slotStart->toDateString().' '.$blockedSlot->end_time)->seconds(0);

            if ($blockedStart < $slotEnd && $blockedEnd > $slotStart) {
                return true;
            }
        }

        return false;
    }

    protected function resetGuestEmailOtpState(): void
    {
        $this->guestEmailOtp = '';
        $this->guestEmailOtpHash = null;
        $this->guestEmailOtpExpiresAt = null;
        $this->guestEmailOtpCooldownUntil = null;
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
        return max(0, self::OTP_MAX_RESENDS - (int) $this->guestEmailOtpResendCount);
    }

    protected function normalizeEmail(mixed $email): string
    {
        return strtolower(trim((string) $email));
    }

    protected function isBookingForSelf(): bool
    {
        return $this->booking_for !== 'someone_else';
    }

    protected function resolvedPatientFirstName(): string
    {
        return trim((string) ($this->isBookingForSelf() ? $this->first_name : $this->patient_first_name));
    }

    protected function resolvedPatientLastName(): string
    {
        return trim((string) ($this->isBookingForSelf() ? $this->last_name : $this->patient_last_name));
    }

    protected function resolvedPatientBirthDate(): ?string
    {
        $birthDate = trim((string) $this->patient_birth_date);

        return $birthDate !== '' ? $birthDate : null;
    }

    protected function normalizedRelationshipToPatient(): ?string
    {
        $relationship = trim((string) $this->relationship_to_patient);

        return $relationship !== '' ? $relationship : null;
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

    protected function guestOtpCooldownThrottleKey(string $email, string $ip): string
    {
        return 'guest-book-otp-cooldown:'.sha1($email.'|'.$ip);
    }

    protected function guestOtpVerifyThrottleKey(string $email, string $ip): string
    {
        return 'guest-book-otp-verify:'.sha1($email.'|'.$ip);
    }
}
