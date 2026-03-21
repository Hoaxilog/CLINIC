<?php

namespace App\Livewire\Appointment;

class AppointmentRequests extends AppointmentCalendar
{
    public function mount(?string $initialTab = 'pending')
    {
        parent::mount($initialTab ?? 'pending');
    }

    public function render()
    {
        return view('livewire.appointment.appointment-requests');
    }
}
