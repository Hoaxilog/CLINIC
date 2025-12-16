<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $notifications = [];

    public function mount()
    {
        $this->fetchUpcomingAppointments();
    }

    public function fetchUpcomingAppointments()
    {
        $now = Carbon::now();
        
        // UPDATE: Set window to exactly 30 minutes from now
        $upcomingTime = $now->copy()->addMinutes(30); 

        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            // This gets appointments happening between NOW and NOW + 30 mins
            ->whereBetween('appointments.appointment_date', [$now, $upcomingTime])
            ->where('appointments.status', '!=', 'Completed') 
            ->where('appointments.status', '!=', 'Cancelled')
            ->orderBy('appointments.appointment_date', 'asc')
            ->select(
                'appointments.id',
                'appointments.appointment_date',
                'appointments.status',
                'patients.first_name',
                'patients.last_name'
            )
            ->get();

        $this->notifications = $appointments->map(function($appt) {
            $appointmentTime = Carbon::parse($appt->appointment_date);
            
            return [
                'id' => $appt->id,
                'patient_name' => $appt->first_name . ' ' . $appt->last_name,
                'time' => $appointmentTime->format('h:i A'),
                // This will say "in 15 minutes", "in 5 minutes", etc.
                'time_diff' => $appointmentTime->diffForHumans(), 
                'status' => $appt->status
            ];
        })->toArray();

        $this->unreadCount = count($this->notifications);
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}