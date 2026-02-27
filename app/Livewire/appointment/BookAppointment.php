<?php

namespace App\Livewire\appointment;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Throwable;

class BookAppointment extends Component
{
    // Form data
    public $first_name, $last_name, $age, $contact_number, $email, $service_id;
    public $selectedDate, $selectedSlot;
    public $recaptchaToken;
    protected $usesPatientUserId = null;
    protected $appointmentStatuses = null;
    
    // UI data
    public $availableSlots = [];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->selectedDate = now()->toDateString();
        $this->availableSlots = $this->generateSlots($this->selectedDate);

        if (Auth::check()) {
            $user = Auth::user();
            $this->email = $user->email;

            $patient = null;
            if ($this->patientsUsesUserId()) {
                $patient = DB::table('patients')->where('user_id', $user->id)->first();
            }

            if (!$patient && $user->email) {
                $patient = DB::table('patients')->where('email_address', $user->email)->first();

                if ($patient && $this->patientsUsesUserId() && empty($patient->user_id)) {
                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update([
                            'user_id' => $user->id,
                            'updated_at' => now(),
                        ]);
                    $patient->user_id = $user->id;
                }
            }

            if ($patient) {
                $this->first_name = $patient->first_name;
                $this->last_name = $patient->last_name;
                $this->contact_number = $patient->mobile_number;
            }
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

    public function generateSlots($dateString)
    {
        $date = Carbon::parse($dateString);
        // Default hours since schedules table does not exist
        $startTime = Carbon::parse($dateString . ' 09:00:00');
        $endTime = Carbon::parse($dateString . ' 20:00:00');
        $duration = 60; // minutes per slot

        $bookedCounts = DB::table('appointments')
            ->whereDate('appointment_date', $dateString)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->selectRaw('TIME(appointment_date) as time_slot, COUNT(*) as total')
            ->groupBy('time_slot')
            ->pluck('total', 'time_slot')
            ->toArray();

        $slots = [];

        while ($startTime->lte($endTime)) {
            $slotTime = $startTime->format('H:i:00');
            $currentCount = $bookedCounts[$slotTime] ?? 0;
            $slotDateTime = Carbon::parse($dateString . ' ' . $slotTime);
            $isPast = $slotDateTime->lt(now());
            $slots[] = [
                'time' => $startTime->format('h:i A'),
                'value' => $slotTime,
                'is_full' => $currentCount >= 2,
                'is_past' => $isPast,
            ];
            $startTime->addMinutes($duration);
        }
        return $slots;
    }

    public function bookAppointment()
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

        if (Auth::check() && empty(Auth::user()->email_verified_at)) {
            $this->addError('email', 'Please verify your email address before booking an appointment.');
            return;
        }

        if (!Auth::check()) {
            if (empty($this->recaptchaToken)) {
                $this->addError('recaptcha', 'Please complete the captcha.');
                return;
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $this->recaptchaToken,
                'remoteip' => request()->ip(),
            ]);

            if (!$response->json('success')) {
                $this->addError('recaptcha', 'CAPTCHA verification failed.');
                return;
            }
        }

        $appointmentDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedSlot)->toDateTimeString();

        $activeSlotBookings = DB::table('appointments')
            ->where('appointment_date', $appointmentDateTime)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->count();

        if ($activeSlotBookings >= 2) {
            $this->addError('selectedSlot', 'This time slot is already full. Please choose another time.');
            $this->availableSlots = $this->generateSlots($this->selectedDate);
            return;
        }

        // Block booking if the patient already has a pending/active appointment
        $existingPatient = null;
        if (Auth::check() && $this->patientsUsesUserId()) {
            $existingPatient = DB::table('patients')->where('user_id', Auth::id())->first();
        }
        if (!$existingPatient && $this->email) {
            $existingPatient = DB::table('patients')->where('email_address', $this->email)->first();
        }

        if ($existingPatient) {
            $hasActiveAppointment = DB::table('appointments')
                ->where('patient_id', $existingPatient->id)
                ->whereNotIn('status', ['Cancelled', 'Completed'])
                ->exists();

            if ($hasActiveAppointment) {
                $this->addError('selectedSlot', 'You already have a pending or upcoming appointment. Please wait until your current appointment is completed before booking a new one.');
                return;
            }
        }

        $patient = null;
        if (Auth::check()) {
            $user = Auth::user();

            if ($this->patientsUsesUserId()) {
                $patient = DB::table('patients')->where('user_id', $user->id)->first();
            }

            if (!$patient && $user?->email) {
                $patient = DB::table('patients')->where('email_address', $user->email)->first();

                if ($patient && $this->patientsUsesUserId() && empty($patient->user_id)) {
                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update([
                            'user_id' => $user->id,
                            'updated_at' => now(),
                        ]);
                    $patient->user_id = $user->id;
                }
            }
        }

        if (!$patient && $this->email) {
            $patient = DB::table('patients')->where('email_address', $this->email)->first();
        }

        $patientData = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile_number' => $this->contact_number,
            'email_address' => $this->email,
            'modified_by' => Auth::check() ? Auth::user()->username : 'GUEST',
            'updated_at' => now(),
        ];

        if (Auth::check() && $this->patientsUsesUserId()) {
            $patientData['user_id'] = Auth::id();
        }

        if ($patient) {
            DB::table('patients')->where('id', $patient->id)->update($patientData);
            $patientId = $patient->id;
        } else {
            $patientId = DB::table('patients')->insertGetId(array_merge($patientData, [
                'created_at' => now(),
            ]));
        }

        DB::table('appointments')->insert([
            'patient_id' => $patientId,
            'service_id' => $this->service_id,
            'appointment_date' => $appointmentDateTime,
            'status' => $this->resolveNewAppointmentStatus(),
            'modified_by' => Auth::check() ? Auth::user()->username : 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

    public function render()
    {
        $services = DB::table('services')->get();
        $layout = (Auth::check() && Auth::user()?->role === 3) ? 'layouts.patient-portal' : 'layouts.app';
        return view('livewire.appointment.book-appointment', compact('services'))
            ->layout($layout);
    }

    protected function patientsUsesUserId(): bool
    {
        if ($this->usesPatientUserId !== null) {
            return $this->usesPatientUserId;
        }

        try {
            $this->usesPatientUserId = Schema::hasColumn('patients', 'user_id');
        } catch (Throwable $e) {
            $this->usesPatientUserId = false;
        }

        return $this->usesPatientUserId;
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
}
