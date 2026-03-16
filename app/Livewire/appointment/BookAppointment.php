<?php

namespace App\Livewire\appointment;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Throwable;

class BookAppointment extends Component
{
    protected const SLOT_CAPACITY = 2;
    protected const APPROVED_SLOT_STATUSES = ['Scheduled', 'Waiting', 'Ongoing'];

    // Form data
    public $first_name, $last_name, $age, $contact_number, $email, $service_id;
    public $selectedDate, $selectedSlot;
    public $recaptchaToken;
    public $guestEmailOtp = '';
    public $guestEmailOtpHash = null;
    public $guestEmailOtpExpiresAt = null;
    public $guestEmailOtpVerified = false;
    public $guestEmailOtpTargetEmail = null;
    public $guestOtpStepActive = false;
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

            $nameParts = preg_split('/\s+/', trim((string) ($user->username ?? '')));
            $this->first_name = $nameParts[0] ?? '';
            $this->last_name = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        }
    }

    // This runs automatically when $selectedDate is updated via JavaScript
    public function updatedSelectedDate($date)
    {
        if (empty($date) || !$this->isValidSelectedDate($date)) {
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
        if (Auth::check()) {
            return;
        }

        $normalizedEmail = $this->normalizeEmail($value);
        if ($normalizedEmail === '' || $normalizedEmail !== $this->guestEmailOtpTargetEmail) {
            $this->resetGuestEmailOtpState();
        }
    }

    public function updated($propertyName): void
    {
        // Keep server-side error bag in sync with user edits so error UI does not reappear after rerenders.
        $clearableFields = [
            'first_name',
            'last_name',
            'age',
            'contact_number',
            'email',
            'service_id',
            'selectedDate',
            'selectedSlot',
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

    public function generateSlots($dateString)
    {
        $date = Carbon::parse($dateString);
        // Default hours since schedules table does not exist
        $startTime = Carbon::parse($dateString . ' 09:00:00');
        $endTime = Carbon::parse($dateString . ' 20:00:00');
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

        $slots = [];

        while ($startTime->lte($endTime)) {
            $slotTime = $startTime->format('H:i:00');
            $currentCount = $bookedCounts[$slotTime] ?? 0;
            $slotDateTime = Carbon::parse($dateString . ' ' . $slotTime);
            $slotEndDateTime = $slotDateTime->copy()->addMinutes($duration);
            $isPast = $slotDateTime->lt(now());
            $isBlocked = $this->isBlockedBySlotCollection($slotDateTime, $slotEndDateTime, $blockedSlots);
            $slots[] = [
                'time' => $startTime->format('h:i A'),
                'value' => $slotTime,
                'is_full' => $currentCount >= self::SLOT_CAPACITY,
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

        if (!Auth::check()) {
            if (!$this->verifyRecaptchaForGuest()) {
                return;
            }

            if (!$this->assertBookingStillAvailable()) {
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

        $sendThrottleKey = $this->guestOtpSendThrottleKey($normalizedEmail, (string) request()->ip());
        if (RateLimiter::tooManyAttempts($sendThrottleKey, 3)) {
            $seconds = RateLimiter::availableIn($sendThrottleKey);
            $minutes = max(1, (int) ceil($seconds / 60));
            $this->addError('guestEmailOtp', "Too many OTP requests. Try again in {$minutes} minute(s).");
            return;
        }

        RateLimiter::hit($sendThrottleKey, 15 * 60);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10);

        $this->guestEmailOtpHash = Hash::make($code);
        $this->guestEmailOtpExpiresAt = $expiresAt->toDateTimeString();
        $this->guestEmailOtpVerified = false;
        $this->guestEmailOtpTargetEmail = $normalizedEmail;
        $this->guestEmailOtp = '';

        Mail::send('appointment.emails.guest-booking-otp', [
            'otp' => $code,
            'email' => $normalizedEmail,
            'expiresAt' => $expiresAt,
        ], function ($message) use ($normalizedEmail) {
            $message->to($normalizedEmail);
            $message->subject('Your Tejadent Booking OTP');
        });

        session()->flash('otp_success', 'OTP sent. Check your email and enter the 6-digit code below.');
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

        if (!Hash::check((string) $this->guestEmailOtp, (string) $this->guestEmailOtpHash)) {
            RateLimiter::hit($verifyThrottleKey, 15 * 60);
            $this->addError('guestEmailOtp', 'Invalid OTP code.');
            return;
        }

        RateLimiter::clear($verifyThrottleKey);
        $this->guestEmailOtpVerified = true;
        $this->guestEmailOtp = '';
        $this->guestEmailOtpHash = null;
        $this->guestEmailOtpExpiresAt = null;

        if (!$this->assertBookingStillAvailable()) {
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
        $this->resetGuestEmailOtpState();
    }

    protected function createAppointmentAndRedirect()
    {
        $appointmentDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedSlot)->toDateTimeString();

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

        DB::table('appointments')->insert($appointmentPayload);

        $service = DB::table('services')->where('id', $this->service_id)->first();

        try {
            Mail::send('appointment.emails.appointment-confirmation', [
                'name' => trim($this->first_name . ' ' . $this->last_name),
                'appointment_date' => Carbon::parse($appointmentDateTime)->format('F j, Y g:i A'),
                'service_name' => $service?->service_name ?? 'Service',
            ], function ($message) {
                $message->to($this->email);
                $message->subject('Appointment Confirmation');
            });
        } catch (\Throwable $th) {
            // Do not block booking if email fails
        }

        session()->flash('success', 'Appointment requested! Please check your email for the details of your appointment.');
        return redirect()->to('/book');
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
            'contact_number' => 'required',
        ]);
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

        if (!$response->json('success')) {
            $this->addError('recaptcha', 'CAPTCHA verification failed.');
            return false;
        }

        return true;
    }

    protected function assertBookingStillAvailable(): bool
    {
        $appointmentDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedSlot)->toDateTimeString();
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

        // Block duplicate active requests using requester identity (account/email), not patient records.
        $hasActiveAppointmentQuery = DB::table('appointments')
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->where(function ($query) {
                if (Auth::check()) {
                    $query->where('requester_user_id', Auth::id());

                    if (!empty($this->email)) {
                        $query->orWhere('requester_email', $this->email);
                    }

                    return;
                }

                $query->where('requester_email', $this->email);
            });

        $hasActiveAppointment = $hasActiveAppointmentQuery->exists();

        if ($hasActiveAppointment) {
            $this->addError('selectedSlot', 'You already have a pending or upcoming appointment request. Please wait until your current request is completed before booking a new one.');
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

            if (preg_match("/^enum\\((.*)\\)$/i", $type, $matches) === 1) {
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
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
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
        if (!$this->blockedSlotsEnabled() || $blockedSlots->isEmpty()) {
            return false;
        }

        foreach ($blockedSlots as $blockedSlot) {
            $blockedStart = Carbon::parse($slotStart->toDateString() . ' ' . $blockedSlot->start_time)->seconds(0);
            $blockedEnd = Carbon::parse($slotStart->toDateString() . ' ' . $blockedSlot->end_time)->seconds(0);

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
        $this->guestEmailOtpVerified = false;
        $this->guestEmailOtpTargetEmail = null;
    }

    protected function normalizeEmail(mixed $email): string
    {
        return strtolower(trim((string) $email));
    }

    protected function guestOtpSendThrottleKey(string $email, string $ip): string
    {
        return 'guest-book-otp-send:' . sha1($email . '|' . $ip);
    }

    protected function guestOtpVerifyThrottleKey(string $email, string $ip): string
    {
        return 'guest-book-otp-verify:' . sha1($email . '|' . $ip);
    }
}
