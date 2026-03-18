<?php

namespace App\Providers;

use App\Livewire\appointment\AppointmentCalendar;
use App\Livewire\appointment\AppointmentRequests;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('appointment.appointment-calendar', AppointmentCalendar::class);
        Livewire::component('appointment.appointment-requests', AppointmentRequests::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
