<?php

use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Middleware\StaffOrDentistMiddleware;
use App\Http\Middleware\IsPatientMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // If an authenticated user reaches a guest-only page, send them to the correct area.
        $middleware->redirectUsersTo(function (Request $request) {
            return match ((int) ($request->user()?->role ?? 0)) {
                3 => route('patient.dashboard'),
                1, 2 => route('dashboard'),
                default => route('home'),
            };
        });
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'isAdmin' => IsAdminMiddleware::class,
            'staffOrDentist' => StaffOrDentistMiddleware::class,
            'isPatient' => IsPatientMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
