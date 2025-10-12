<?php

use App\Http\Controllers\Appointment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\Database;


Route::middleware(['auth', 'admin'])->group(function () {
    // Route::get('/', [Database::class,'index'])->middleware('admin');
    Route::get('appointment', [Appointment::class,'index'])->name('appointment');
});


Route::get('appointment', function () {
    return view('appointment');
});                                                                                 



Route::get('login', [Login::class, 'index']);
