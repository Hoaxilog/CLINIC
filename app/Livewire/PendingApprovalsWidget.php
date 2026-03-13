<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class PendingApprovalsWidget extends Component
{
    public $pendingApprovals = [];
    public $showDetails = false;
    public $selectedApproval = null;
    protected ?bool $patientsUsesUserId = null;

    public function mount()
    {
        $this->loadPendingApprovals();
    }

    public function loadPendingApprovals()
    {
        if (Auth::user()?->role === 3) {
            $this->pendingApprovals = collect();
            return;
        }

        $columns = [
            'appointments.id',
            'appointments.appointment_date',
            'appointments.status',
            'appointments.patient_id',
            'patients.first_name',
            'patients.last_name',
            'patients.mobile_number',
            'patients.email_address',
            'services.service_name',
        ];

        if ($this->patientsUsesUserId()) {
            $columns[] = 'patients.user_id';
        } else {
            $columns[] = DB::raw('NULL as user_id');
        }

        $this->pendingApprovals = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'Pending')
            ->orderBy('appointments.appointment_date', 'asc')
            ->select($columns)
            ->limit(6)
            ->get();

        $this->pendingApprovals = $this->attachPatientProfilePictures($this->pendingApprovals);
    }

    public function viewApproval($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $columns = [
            'appointments.id',
            'appointments.appointment_date',
            'appointments.status',
            'appointments.patient_id',
            'patients.first_name',
            'patients.last_name',
            'patients.mobile_number',
            'patients.email_address',
            'services.service_name',
        ];

        if ($this->patientsUsesUserId()) {
            $columns[] = 'patients.user_id';
        } else {
            $columns[] = DB::raw('NULL as user_id');
        }

        $this->selectedApproval = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', $appointmentId)
            ->select($columns)
            ->first();

        if ($this->selectedApproval) {
            $this->selectedApproval = $this->attachPatientProfilePictures(collect([$this->selectedApproval]))->first();
        }

        $this->showDetails = (bool) $this->selectedApproval;
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedApproval = null;
    }

    public function approveAppointment($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $this->updateAppointmentStatusById($appointmentId, 'Scheduled');
        $this->loadPendingApprovals();

        if ($this->selectedApproval && $this->selectedApproval->id === $appointmentId) {
            $this->selectedApproval->status = 'Scheduled';
        }
    }

    public function rejectAppointment($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $this->updateAppointmentStatusById($appointmentId, 'Cancelled');
        $this->loadPendingApprovals();

        if ($this->selectedApproval && $this->selectedApproval->id === $appointmentId) {
            $this->selectedApproval->status = 'Cancelled';
        }
    }

    protected function updateAppointmentStatusById($appointmentId, $newStatus)
    {
        $oldAppt = DB::table('appointments')->where('id', $appointmentId)->first();
        if (!$oldAppt) {
            return;
        }

        DB::table('appointments')
            ->where('id', $appointmentId)
            ->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

        if ($oldAppt->status !== $newStatus) {
            $subject = new Appointment();
            $subject->id = $appointmentId;

            $eventName = $newStatus === 'Cancelled' ? 'appointment_cancelled' : 'appointment_updated';

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->event($eventName)
                ->withProperties([
                    'old' => ['status' => $oldAppt->status],
                    'attributes' => ['status' => $newStatus],
                ])
                ->log('Updated Appointment Status');
        }

        $this->sendStatusEmail($appointmentId, $newStatus);
    }

    protected function sendStatusEmail($appointmentId, $newStatus)
    {
        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select(
                'appointments.appointment_date',
                'patients.first_name',
                'patients.last_name',
                'patients.email_address',
                'services.service_name'
            )
            ->where('appointments.id', $appointmentId)
            ->first();

        if (!$appointment || empty($appointment->email_address)) {
            return;
        }

        try {
            Mail::send('appointment.emails.appointment-status-update', [
                'name' => trim($appointment->first_name . ' ' . $appointment->last_name),
                'appointment_date' => Carbon::parse($appointment->appointment_date)->format('F j, Y g:i A'),
                'service_name' => $appointment->service_name,
                'status' => $newStatus,
            ], function ($message) use ($appointment) {
                $message->to($appointment->email_address);
                $message->subject('Appointment Status Update');
            });
        } catch (\Throwable $th) {
            // Do not break UI if mail fails
        }
    }

    public function render()
    {
        return view('livewire.pending-approvals-widget');
    }

    protected function attachPatientProfilePictures($appointments)
    {
        $appointments = collect($appointments);

        if ($appointments->isEmpty()) {
            return $appointments;
        }

        $usesPatientUserId = false;

        try {
            $usesPatientUserId = Schema::hasColumn('patients', 'user_id');
        } catch (Throwable $e) {
            $usesPatientUserId = false;
        }

        $userIds = $usesPatientUserId
            ? $appointments->pluck('user_id')->filter()->unique()->values()
            : collect();

        $emails = $appointments->pluck('email_address')
            ->filter()
            ->map(fn ($email) => Str::lower(trim($email)))
            ->unique()
            ->values();

        if ($userIds->isEmpty() && $emails->isEmpty()) {
            return $appointments->map(function ($appointment) {
                $appointment->profile_picture = null;
                return $appointment;
            });
        }

        $users = DB::table('users')
            ->where(function ($query) use ($userIds, $emails) {
                if ($userIds->isNotEmpty()) {
                    $query->whereIn('id', $userIds->all());
                }

                if ($emails->isNotEmpty()) {
                    $query->orWhereIn(DB::raw('LOWER(email)'), $emails->all())
                        ->orWhereIn(DB::raw('LOWER(username)'), $emails->all());
                }
            })
            ->get();

        $usersById = $users->keyBy('id');
        $usersByEmail = $users->filter(fn ($user) => !empty($user->email))
            ->keyBy(fn ($user) => Str::lower(trim($user->email)));
        $usersByUsername = $users->filter(fn ($user) => !empty($user->username))
            ->keyBy(fn ($user) => Str::lower(trim($user->username)));

        return $appointments->map(function ($appointment) use ($usesPatientUserId, $usersById, $usersByEmail, $usersByUsername) {
            $linkedUser = null;

            if ($usesPatientUserId && !empty($appointment->user_id)) {
                $linkedUser = $usersById->get($appointment->user_id);
            }

            if (!$linkedUser && !empty($appointment->email_address)) {
                $emailKey = Str::lower(trim($appointment->email_address));
                $linkedUser = $usersByEmail->get($emailKey) ?? $usersByUsername->get($emailKey);
            }

            $appointment->profile_picture = data_get($linkedUser, 'profile_picture');
            $appointment->profile_picture_updated_at = data_get($linkedUser, 'updated_at');

            return $appointment;
        });
    }

    protected function patientsUsesUserId(): bool
    {
        if ($this->patientsUsesUserId !== null) {
            return $this->patientsUsesUserId;
        }

        try {
            $this->patientsUsesUserId = Schema::hasColumn('patients', 'user_id');
        } catch (Throwable $e) {
            $this->patientsUsesUserId = false;
        }

        return $this->patientsUsesUserId;
    }
}
