<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\Database;


Route::middleware(['auth', 'admin'])->group(function () {
    // Route::get('/dashboard', [])
});

Route::get('/dashboard', [Dashboard::class, 'index']);


Route::post('/login', [Login::class, 'login']);

Route::get('/appointment', function () {
    return view('appointment');
});


Route::get('/login', [Login::class, 'index']);
