<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\BackupController;

Route::middleware(['isAdmin'])->group(function () {
    Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
    Route::get('/appointment', function () {return view('appointment');})->name('appointment');
    Route::get('/patient-records', action: [PatientsController::class, 'index'])->name('patient-records');
    Route::get('/admin/backup-database', [BackupController::class, 'downloadBackup'])
             ->name('admin.db.backup');
    Route::post('/logout', [Login::class, 'logout'])->name('logout');
});

Route::middleware(['guest'])->group(function() {
    Route::get('/', [Login::class, 'index'])->name('login');
    Route::post('/login', [Login::class, 'login']);
});

