<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class Database extends Controller
{
    public function index() {   
            
        $users = DB::select('select * from users');
        return view('welcome', ['users' => $users]);
    }
}
