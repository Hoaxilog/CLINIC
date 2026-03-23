<?php

namespace App\Providers;

use App\Livewire\Appointment\AppointmentCalendar;
use App\Livewire\Appointment\AppointmentRequests;
use App\Livewire\Appointment\BookAppointment;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Livewire::component('appointment.appointment-calendar', AppointmentCalendar::class);
        Livewire::component('appointment.appointment-requests', AppointmentRequests::class);
        Livewire::component('appointment.book-appointment', BookAppointment::class);

        // Backward-compatible aliases for stale snapshots/browser state.
        Livewire::component('appointment-calendar', AppointmentCalendar::class);
        Livewire::component('appointment-requests', AppointmentRequests::class);
        Livewire::component('book-appointment', BookAppointment::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
