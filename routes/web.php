<?php

use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\Database;
use App\Http\Controllers\NotesController;

Route::middleware(['isAdmin'])->group(function () {
    // Route::get('/dashboard', [])
    Route::get('/dashboard', [Dashboard::class, 'index']);
});




Route::post('/notes', [NotesController::class, 'createNotes']);


Route::post('/login', [Login::class, 'login']);

Route::get('/appointment', function () {
    return view('appointment');
});


Route::get('/', [Login::class, 'index']);
