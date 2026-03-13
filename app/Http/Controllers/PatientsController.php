<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class PatientsController extends Controller
{
    public function index()
    {
        return view('patient');
    }

    public function show(int $id)
    {
        $this->ensureStaffAccess();

        $patient = DB::table('patients')->where('id', $id)->first();

        if (!$patient) {
            throw new NotFoundHttpException('Patient not found.');
        }

        $linkedUser = $this->resolveLinkedUser($patient);
        $patient->profile_picture = data_get($linkedUser, 'profile_picture');
        $patient->profile_picture_updated_at = data_get($linkedUser, 'updated_at');
        $patient->portal_username = data_get($linkedUser, 'username');
        $patient->portal_email = data_get($linkedUser, 'email') ?: data_get($patient, 'email_address');

        $lastVisit = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->where('status', 'Completed')
            ->orderBy('appointment_date', 'desc')
            ->first();

        $latestAppointment = DB::table('appointments')
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->first();

        $treatmentRecords = DB::table('treatment_records')
            ->where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $patientAge = $patient->birth_date ? Carbon::parse($patient->birth_date)->age : null;
        $activeCutoff = Carbon::now()->subYears(2);
        $patientType = $latestAppointment && !empty($latestAppointment->appointment_date) && Carbon::parse($latestAppointment->appointment_date)->gte($activeCutoff)
            ? 'Active'
            : 'Inactive';

        return view('patients.show', [
            'patient' => $patient,
            'linkedUser' => $linkedUser,
            'lastVisit' => $lastVisit,
            'latestAppointment' => $latestAppointment,
            'treatmentRecords' => $treatmentRecords,
            'patientAge' => $patientAge,
            'patientType' => $patientType,
        ]);
    }

    private function ensureStaffAccess(): void
    {
        $role = Auth::user()?->role;

        if (!in_array($role, [1, 2], true)) {
            abort(403, 'Unauthorized.');
        }
    }

    private function resolveLinkedUser(object $patient): ?object
    {
        $usesUserId = false;

        try {
            $usesUserId = Schema::hasColumn('patients', 'user_id');
        } catch (Throwable $e) {
            $usesUserId = false;
        }

        if ($usesUserId && !empty($patient->user_id)) {
            $user = DB::table('users')->where('id', $patient->user_id)->first();
            if ($user) {
                return $user;
            }
        }

        if (!empty($patient->email_address)) {
            return DB::table('users')
                ->where('email', $patient->email_address)
                ->orWhere('username', $patient->email_address)
                ->first();
        }

        return null;
    }
}
