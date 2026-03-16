<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $patient = null;

        $appointmentsQuery = DB::table('appointments')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->where(function ($query) use ($user) {
                $query->where('appointments.requester_user_id', $user->id);

                if (!empty($user->email)) {
                    $query->orWhere('appointments.requester_email', $user->email);
                }
            })
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.service_id',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.requester_first_name',
                'appointments.requester_last_name',
                'appointments.updated_at',
                'services.service_name'
            );

        $upcomingAppointments = (clone $appointmentsQuery)
            ->whereIn('appointments.status', ['Pending', 'Scheduled', 'Waiting'])
            ->where('appointments.appointment_date', '>=', now())
            ->orderBy('appointments.appointment_date', 'asc')
            ->limit(5)
            ->get();

        $upcomingAppointment = $upcomingAppointments->first();

        if ($upcomingAppointment && $upcomingAppointment->patient_id) {
            $patient = DB::table('patients')->where('id', $upcomingAppointment->patient_id)->first();
        }

        if (!$patient) {
            $patient = DB::table('patients')
                ->where('email_address', $user->email)
                ->first();
        }

        $appointmentRequests = (clone $appointmentsQuery)
            ->whereIn('appointments.status', ['Pending', 'Scheduled', 'Waiting'])
            ->orderBy('appointments.appointment_date', 'asc')
            ->get();

        $appointmentHistory = (clone $appointmentsQuery)
            ->where(function ($query) {
                $query->where('appointments.appointment_date', '<', now())
                    ->orWhereIn('appointments.status', ['Completed', 'Cancelled']);
            })
            ->orderBy('appointments.appointment_date', 'desc')
            ->limit(10)
            ->get();

        return view('patient.dashboard', [
            'patient' => $patient,
            'upcomingAppointment' => $upcomingAppointment,
            'upcomingAppointments' => $upcomingAppointments,
            'appointmentHistory' => $appointmentHistory,
            'appointmentRequests' => $appointmentRequests,
        ]);
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        $ownsAppointment = (int) $appointment->requester_user_id === (int) $user->id
            || (!empty($user->email) && strtolower((string) $appointment->requester_email) === strtolower((string) $user->email));

        if (!$ownsAppointment) {
            abort(403, 'You are not allowed to cancel this appointment.');
        }

        if ($appointment->status !== 'Scheduled') {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'Only scheduled appointments can be cancelled.');
        }

        if (Carbon::parse($appointment->appointment_date)->lt(now())) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'Past appointments can no longer be cancelled.');
        }

        $updated = DB::table('appointments')
            ->where('id', $appointment->id)
            ->where('status', 'Scheduled')
            ->update([
                'status' => 'Cancelled',
                'modified_by' => $user->username ?? $user->email ?? 'PATIENT',
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            return redirect()
                ->route('patient.dashboard')
                ->with('failed', 'This appointment was already updated. Please refresh and try again.');
        }

        $subject = new Appointment();
        $subject->id = $appointment->id;

        activity()
            ->performedOn($subject)
            ->causedBy($user)
            ->event('appointment_cancelled_by_patient')
            ->withProperties([
                'appointment_id' => $appointment->id,
                'appointment_date' => $appointment->appointment_date,
            ])
            ->log('Patient Cancelled Appointment');

        return redirect()
            ->route('patient.dashboard')
            ->with('success', 'Your scheduled appointment has been cancelled and staff were notified.');
    }
}
