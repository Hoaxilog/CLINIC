<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\Database;


Route::get('/', [Database::class,'index']);


Route::get('appointment', function () {
    return view('appointment');
});


Route::get('login', [Login::class, 'index']);
