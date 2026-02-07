<?php

namespace App\Livewire\appointment;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Component
{
    public $selectedDate;
    public $availableSlots = [];
    public $selectedSlot;

    // Called when the user picks a date from the calendar
    public function updatedSelectedDate($date)
    {
        $this->availableSlots = $this->generateSlots($date);
    }

    public function generateSlots($dateString)
    {
        $date = Carbon::parse($dateString);
        $dayOfWeek = $date->format('l'); // 'Monday', 'Tuesday'...

        // 1. Get the Schedule for this day
        $schedule = DB::table('schedules')
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return []; // Clinic is closed this day
        }

        // 2. Get Existing Appointments for this date
        // Assuming 'appointment_date' and 'time_slot' are your column names
        $bookedSlots = DB::table('appointments')
            ->where('appointment_date', $dateString)
            ->whereIn('status', ['Pending', 'Approved']) // Don't count Cancelled
            ->pluck('time_slot') // Returns array like ['09:00:00', '10:30:00']
            ->toArray();

        // 3. Generate All Possible Slots
        $slots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $duration = $schedule->slot_duration; // e.g., 30 minutes

        while ($startTime->lt($endTime)) {
            $slotTime = $startTime->format('H:i:00');
            
            // 4. Check Availability
            // If this slot is NOT in the booked list, add it
            if (!in_array($slotTime, $bookedSlots)) {
                $slots[] = [
                    'time' => $startTime->format('h:i A'), // Display: 09:00 AM
                    'value' => $slotTime,                 // Value: 09:00:00
                ];
            }

            $startTime->addMinutes($duration);
        }

        return $slots;
    }

    public function bookAppointment()
    {
        // Validation
        $this->validate([
            'selectedDate' => 'required|date|after:today',
            'selectedSlot' => 'required',
        ]);

        // Double check availability (Race condition prevention)
        $exists = DB::table('appointments')
            ->where('appointment_date', $this->selectedDate)
            ->where('time_slot', $this->selectedSlot)
            ->whereIn('status', ['Pending', 'Approved'])
            ->exists();

        if ($exists) {
            session()->flash('error', 'Sorry, that slot was just taken.');
            $this->updatedSelectedDate($this->selectedDate); // Refresh
            return;
        }

        // Insert Appointment
        DB::table('appointments')->insert([
            'user_id' => Auth::id(), // Or patient_id logic
            'appointment_date' => $this->selectedDate,
            'time_slot' => $this->selectedSlot,
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->flash('success', 'Appointment request sent successfully!');
        $this->reset(['selectedDate', 'selectedSlot', 'availableSlots']);
    }

    public function render()
    {
        $services = DB::table('services')->get(); 

        // If user is logged in, we can pre-fill their data later
        $user = Auth::user();
        
        return view('appointment.book-appointment', compact('services', 'user'));
    }
}