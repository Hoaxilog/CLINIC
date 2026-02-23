<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PatientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $patient = null;

        if ($user) {
            $usesUserId = false;
            try {
                $usesUserId = Schema::hasColumn('patients', 'user_id');
            } catch (Throwable $e) {
                $usesUserId = false;
            }

            if ($usesUserId) {
                $patient = DB::table('patients')->where('user_id', $user->id)->first();
            }

            if (!$patient && $user->email) {
                $patient = DB::table('patients')->where('email_address', $user->email)->first();

                if ($patient && $usesUserId && empty($patient->user_id)) {
                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update([
                            'user_id' => $user->id,
                            'updated_at' => now(),
                        ]);
                    $patient->user_id = $user->id;
                }
            }
        }

        if (!$patient) {
            return view('patient.dashboard', [
                'patient' => null,
                'upcomingAppointment' => null,
                'appointmentHistory' => collect(),
                'appointmentRequests' => collect(),
                'treatmentRecords' => collect(),
            ]);
        }

        $upcomingAppointment = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.patient_id', $patient->id)
            ->whereNotIn('appointments.status', ['Cancelled', 'Completed'])
            ->orderBy('appointments.appointment_date', 'asc')
            ->select(
                'appointments.appointment_date',
                'appointments.status',
                'services.service_name'
            )
            ->first();

        $appointmentHistory = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.patient_id', $patient->id)
            ->whereIn('appointments.status', ['Completed', 'Cancelled'])
            ->orderBy('appointments.appointment_date', 'desc')
            ->limit(8)
            ->select(
                'appointments.appointment_date',
                'appointments.status',
                'services.service_name'
            )
            ->get();

        $appointmentRequests = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.patient_id', $patient->id)
            ->whereIn('appointments.status', ['Pending', 'Waiting', 'Ongoing', 'Scheduled'])
            ->orderBy('appointments.appointment_date', 'desc')
            ->limit(6)
            ->select(
                'appointments.appointment_date',
                'appointments.status',
                'services.service_name'
            )
            ->get();

        $treatmentRecords = DB::table('treatment_records')
            ->where('patient_id', $patient->id)
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->select(
                'treatment',
                'dmd',
                'remarks',
                'updated_at'
            )
            ->get();

        return view('patient.dashboard', [
            'patient' => $patient,
            'upcomingAppointment' => $upcomingAppointment,
            'appointmentHistory' => $appointmentHistory,
            'appointmentRequests' => $appointmentRequests,
            'treatmentRecords' => $treatmentRecords,
        ]);
    }
}
