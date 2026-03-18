<?php

namespace App\Livewire\appointment;

class AppointmentRequests extends AppointmentCalendar
{
    public function mount(?string $initialTab = null): void
    {
        parent::mount('pending');
        $this->activeTab = 'pending';
        $this->isTabLocked = true;
        $this->lockedTab = 'pending';
    }

    public function render()
    {
        return view('livewire.appointment.appointment-requests');
    }
}
