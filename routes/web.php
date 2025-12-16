<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

// Public (guest) routes
Route::middleware(['guest'])->group(function() {
    Route::get('/', [Login::class, 'index'])->name('login');
    Route::post('/login', [Login::class, 'login']);
});

// Authenticated user routes (visible to all logged-in users)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
    Route::get('/appointment', function () { return view('appointment'); })->name('appointment');
    Route::get('/patient-records', [PatientsController::class, 'index'])->name('patient-records');
    // Logout should be available to all authenticated users
    Route::post('/logout', [Login::class, 'logout'])->name('logout');
});

// Admin-only routes
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/backup-database', [BackupController::class, 'downloadBackup'])->name('admin.db.backup');

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

