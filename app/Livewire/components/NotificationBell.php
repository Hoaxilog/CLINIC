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
        
        $upcomingTime = $now->copy()->addDay(); 

        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
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

        // Transform the data to match what the Blade View expects
        $this->notifications = $appointments->map(function($appt) {
            $appointmentTime = Carbon::parse($appt->appointment_date);
            
            return (object) [ // Cast to object so Blade can use -> syntax
                'id' => $appt->id,
                'title' => 'Upcoming Appointment',
                // Create a readable message
                'message' => "{$appt->first_name} {$appt->last_name} - {$appointmentTime->format('h:i A')}",
                'created_at' => $appt->appointment_date, // Used for "time ago"
                'status' => $appt->status,
                'is_read' => false, // Default to unread for these alerts
                'link' => url('/appointment') 
            ];
        });

        $this->unreadCount = $this->notifications->count();
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}