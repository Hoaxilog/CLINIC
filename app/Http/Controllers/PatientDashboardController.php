<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('patient.dashboard', [
            'patient' => null,
            'upcomingAppointment' => null,
            'appointmentHistory' => collect(),
            'appointmentRequests' => collect(),
            'treatmentRecords' => collect(),
        ]);
    }
}
