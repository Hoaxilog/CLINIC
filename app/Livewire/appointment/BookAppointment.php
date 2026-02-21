<?php

namespace App\Livewire\appointment;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class BookAppointment extends Component
{
    // Form data
    public $first_name, $last_name, $age, $contact_number, $email, $service_id;
    public $selectedDate, $selectedSlot;
    public $recaptchaToken;
    
    // UI data
    public $availableSlots = [];

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->email = $user->email;

            $patient = null;
            if ($user->email) {
                $patient = DB::table('patients')->where('email_address', $user->email)->first();
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
        $this->availableSlots = $this->generateSlots($date);
    }

    public function generateSlots($dateString)
    {
        $date = Carbon::parse($dateString);
        // Default hours since schedules table does not exist
        $startTime = Carbon::parse($dateString . ' 09:00:00');
        $endTime = Carbon::parse($dateString . ' 17:00:00');
        $duration = 30; // minutes per slot

        $bookedCounts = DB::table('appointments')
            ->whereDate('appointment_date', $dateString)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->selectRaw('TIME(appointment_date) as time_slot, COUNT(*) as total')
            ->groupBy('time_slot')
            ->pluck('total', 'time_slot')
            ->toArray();

        $slots = [];

        while ($startTime->lt($endTime)) {
            $slotTime = $startTime->format('H:i:00');
            $currentCount = $bookedCounts[$slotTime] ?? 0;
            if ($currentCount < 2) {
                $slots[] = [
                    'time' => $startTime->format('h:i A'),
                    'value' => $slotTime,
                ];
            }
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
            'selectedDate' => 'required|date',
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

        $patient = null;
        if (Auth::check() && Auth::user()?->email) {
            $patient = DB::table('patients')->where('email_address', Auth::user()->email)->first();
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
            'status' => 'Pending',
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

        session()->flash('success', 'Appointment requested!');
        return redirect()->to('/book');
    }

    public function render()
    {
        $services = DB::table('services')->get();
        // We render the form view and wrap it in your main layout
        return view('livewire.appointment.book-appointment', compact('services'))
            ->layout('layouts.app'); 
    }
}
