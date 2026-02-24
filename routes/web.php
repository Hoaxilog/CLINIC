<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ServiceController;
use App\Livewire\appointment\BookAppointment;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;


Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/terms-of-service', 'terms-of-service')->name('terms-of-service');

// Public home page
Route::get('/', function () {
    return view('home-page');
})->name('home');

Route::get('/home', function () {
    return view('home-page');
});

// Service pages
Route::prefix('services')->group(function () {
    Route::get('/general-checkup', [ServiceController::class, 'generalCheckup'])->name('services.general-checkup');
    Route::get('/orthodontics', [ServiceController::class, 'orthodontics'])->name('services.orthodontics');
    Route::get('/teeth-whitening', [ServiceController::class, 'teethWhitening'])->name('services.teeth-whitening');
    Route::get('/oral-surgery', [ServiceController::class, 'oralSurgery'])->name('services.oral-surgery');
});

// Public (guest) routes
Route::middleware(['guest'])->group(function() {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);


    Route::get('auth/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])
        ->name('auth.google.redirect');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');


    // Forgot Password Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.forgot');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

    
    // Registration Routes (Manual)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

    
    Route::get('/email/verify-notice', [VerificationController::class, 'showNotice'])->name('verification.notice');
    
    // 2. The Verification Link (Clicked from email)
    Route::get('/email/verify/{id}/{token}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::get('/email/verified', [VerificationController::class, 'showSuccess'])->name('verification.success');

});

// Public booking route (guest or logged-in)
Route::get('/book', BookAppointment::class)->name('book');

// Authenticated user routes (all logged-in users)
Route::middleware(['auth'])->group(function () {
    // Logout should be available to all authenticated users
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Staff/Dentist-only routes
Route::middleware(['auth', 'staffOrDentist'])->group(function () {
    Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard/patient-stats', [Dashboard::class, 'patientStats'])->name('dashboard.patient-stats');
    Route::get('/queue', function () {
        return view('queue');
    })->name('queue');
    Route::get('/appointment', function () { return view('appointment'); })->name('appointment');
    Route::get('/patient-records', [PatientsController::class, 'index'])->name('patient-records');
}); 

// Patient-only routes
Route::middleware(['auth', 'isPatient'])->group(function () {
    Route::get('/patient/dashboard', [PatientDashboardController::class, 'index'])->name('patient.dashboard');
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
});
