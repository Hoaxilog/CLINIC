<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Login extends Controller
{   
    public function index() { 
        $user = DB::table("users")->get();
        
        return view("login");
    }
}
