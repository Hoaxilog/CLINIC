<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\BackupController;

Route::middleware(['isAdmin'])->group(function () {
    // Route::get('/dashboard', [])
    Route::get('/dashboard', [Dashboard::class, 'index']);
    Route::get('/appointment', function () {
        return view('appointment');
    });
});


Route::get('/patient-records', action: [PatientsController::class, 'index']);


Route::post('/login', [Login::class, 'login']);
Route::get('/', [Login::class, 'index']);

Route::get('/admin/backup-database', [BackupController::class, 'downloadBackup'])
         ->name('admin.db.backup');