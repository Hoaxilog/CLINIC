<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function showBookingForm()
    {
        // Get services for the dropdown (Cleaning, Extraction, etc.)
        $services = DB::table('services')->get(); //

        // If user is logged in, we can pre-fill their data later
        $user = Auth::user();
        
        return view('appointment.book-appointment', compact('services', 'user'));
    }
}
