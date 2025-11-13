<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;

Route::middleware(['isAdmin'])->group(function () {
    // Route::get('/dashboard', [])
    Route::get('/dashboard', [Dashboard::class, 'index']);
    Route::get('/appointment', function () {
        return view('appointment');
    });
});


Route::post('/login', [Login::class, 'login']);



Route::get('/', [Login::class, 'index']);
