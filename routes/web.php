<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\PatientOnboardingController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Livewire\Appointment\BookAppointment;
use App\Support\Services\ServiceCatalog;
use Illuminate\Support\Facades\Route;

Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/terms-of-service', 'terms-of-service')->name('terms-of-service');

// Public home page
Route::get('/', function () {
    return view('home-page', [
        'services' => ServiceCatalog::all(),
    ]);
})->name('home');

Route::get('/home', function () {
    return view('home-page', [
        'services' => ServiceCatalog::all(),
    ]);
});

// Service pages
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// Public (guest) routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/login/otp', [LoginController::class, 'showOtpForm'])->name('login.otp');
    Route::post('/login/otp', [LoginController::class, 'verifyOtp'])->name('login.otp.verify');
    Route::post('/login/otp/resend', [LoginController::class, 'resendOtp'])->name('login.otp.resend');

    Route::get('auth/google/redirect', [LoginController::class, 'redirectToGoogle'])
        ->name('auth.google.redirect');
    Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');


    // (Reset routes moved below to allow both guests and authenticated users)

    // Registration Routes (Manual)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

    Route::get('/email/verify-notice', [VerificationController::class, 'showNotice'])->name('verification.notice');
    Route::get('/email/verify-expired', [VerificationController::class, 'showExpired'])->name('verification.expired');
    Route::post('/email/verify/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    // 2. The Verification Link (Clicked from email)
    Route::get('/email/verify/{id}/{token}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::get('/email/verified', [VerificationController::class, 'showSuccess'])->name('verification.success');

});

// Password reset flow — accessible to ALL users (guest + authenticated)
Route::view('/reset-password/expired', 'auth.reset-password-expired')->name('password.expired'); // must be BEFORE {token}
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.forgot');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Public booking route (supports both guests and authenticated users)
Route::view('/book', 'book')->name('book');

// Authenticated user routes (all logged-in users)
Route::middleware(['auth'])->group(function () {
    // Logout should be available to all authenticated users
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/password/reset-link', [ProfileController::class, 'sendPasswordResetLink'])->name('profile.password.reset-link');

});

// Staff/Dentist-only routes
Route::middleware(['auth', 'staffOrDentist'])->group(function () {
    Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard/patient-stats', [Dashboard::class, 'patientStats'])->name('dashboard.patient-stats');
    Route::get('/queue', function () {
        return view('queue');
    })->name('queue');
    Route::get('/appointment', function () {
        return view('appointment', ['initialTab' => request()->query('tab')]);
    })->name('appointment');
    Route::get('/appointment/requests', function () {
        return view('appointment-requests');
    })->name('appointment.requests');
    Route::get('/appointment/calendar', function () {
        return view('appointment', ['initialTab' => 'calendar']);
    })->name('appointment.calendar');
    Route::get('/patient-records', [PatientsController::class, 'index'])->name('patient-records');
});

// Patient-only routes
Route::middleware(['auth', 'isPatient'])->group(function () {
    Route::get('/patient/complete-profile', [PatientOnboardingController::class, 'show'])
        ->name('patient.complete-profile.show');
    Route::post('/patient/complete-profile', [PatientOnboardingController::class, 'store'])
        ->name('patient.complete-profile.store');
});

Route::middleware(['auth', 'isPatient', 'patient.profile.complete'])->group(function () {
    Route::get('/patient/dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');
    Route::get('/patient/appointments/{appointment}/reschedule', [PatientDashboardController::class, 'editReschedule'])
        ->name('patient.appointments.reschedule.edit');
    Route::post('/patient/appointments/{appointment}/reschedule', [PatientDashboardController::class, 'updateReschedule'])
        ->name('patient.appointments.reschedule.update');
    Route::post('/patient/appointments/{appointment}/cancel', [PatientDashboardController::class, 'cancel'])
        ->name('patient.appointments.cancel');
});

// Admin-only routes
Route::middleware(['auth', 'isAdmin'])->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print/{reportType}', [ReportController::class, 'print'])->name('reports.print');
});
