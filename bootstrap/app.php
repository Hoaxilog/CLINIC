<?php

use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Middleware\StaffOrDentistMiddleware;
use App\Http\Middleware\IsPatientMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo('/dashboard'); //if we see that the user is already login we will redirect them to dashboard (['guest'])
        $middleware->alias([
            'isAdmin' => IsAdminMiddleware::class,
            'staffOrDentist' => StaffOrDentistMiddleware::class,
            'isPatient' => IsPatientMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
