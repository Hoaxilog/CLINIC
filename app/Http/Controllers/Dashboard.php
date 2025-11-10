<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    public function index() {
        $notes = DB::table('notes')->get();
        return view('dashboard', ['notes' => $notes]);
    }

}
