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
        // Canonical aliases under the new namespaced appointment components.
        Livewire::component('appointment.appointment-calendar', AppointmentCalendar::class);
        Livewire::component('appointment.appointment-requests', AppointmentRequests::class);
        // Backward-compatible aliases for stale snapshots/browser state.
        Livewire::component('appointment-calendar', AppointmentCalendar::class);
        Livewire::component('appointment-requests', AppointmentRequests::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
