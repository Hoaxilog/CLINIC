<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PendingApprovalsWidget extends Component
{
    public $pendingApprovals = [];
    public $showDetails = false;
    public $selectedApproval = null;

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

        $this->pendingApprovals = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'Pending')
            ->orderBy('appointments.appointment_date', 'asc')
            ->select(
                'appointments.id',
                'appointments.appointment_date',
                'appointments.status',
                DB::raw('COALESCE(patients.first_name, appointments.requester_first_name) as first_name'),
                DB::raw('COALESCE(patients.last_name, appointments.requester_last_name) as last_name'),
                DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as mobile_number'),
                DB::raw('COALESCE(patients.email_address, appointments.requester_email) as email_address'),
                'services.service_name'
            )
            ->limit(5)
            ->get();
    }

    public function viewApproval($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $this->selectedApproval = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', $appointmentId)
            ->select(
                'appointments.id',
                'appointments.appointment_date',
                'appointments.status',
                DB::raw('COALESCE(patients.first_name, appointments.requester_first_name) as first_name'),
                DB::raw('COALESCE(patients.last_name, appointments.requester_last_name) as last_name'),
                DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as mobile_number'),
                DB::raw('COALESCE(patients.email_address, appointments.requester_email) as email_address'),
                'services.service_name'
            )
            ->first();

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

        $appointment = DB::table('appointments')->where('id', $appointmentId)->first();
        if (!$appointment) {
            return;
        }

        if (empty($appointment->patient_id)) {
            session()->flash('error', 'Review required in Appointment Calendar before approval.');
            return;
        }

        $didUpdate = $this->updateAppointmentStatusById($appointmentId, 'Scheduled');
        if ($didUpdate) {
            session()->flash('success', 'Appointment request approved.');
            $this->loadPendingApprovals();

            if ($this->selectedApproval && $this->selectedApproval->id === $appointmentId) {
                $this->selectedApproval->status = 'Scheduled';
            }
        }
    }

    public function rejectAppointment($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $this->updateAppointmentStatusById($appointmentId, 'Cancelled');
        session()->flash('info', 'Appointment request rejected.');
        $this->loadPendingApprovals();

        if ($this->selectedApproval && $this->selectedApproval->id === $appointmentId) {
            $this->selectedApproval->status = 'Cancelled';
        }
    }

    protected function updateAppointmentStatusById($appointmentId, $newStatus): bool
    {
        $oldAppt = DB::table('appointments')->where('id', $appointmentId)->first();
        if (!$oldAppt) {
            return false;
        }

        if ($newStatus === 'Scheduled' && empty($oldAppt->patient_id)) {
            session()->flash('error', 'Cannot approve without a linked patient record.');
            return false;
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

        return true;
    }

    protected function sendStatusEmail($appointmentId, $newStatus)
    {
        $appointment = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select(
                'appointments.appointment_date',
                DB::raw('COALESCE(patients.first_name, appointments.requester_first_name) as first_name'),
                DB::raw('COALESCE(patients.last_name, appointments.requester_last_name) as last_name'),
                DB::raw('COALESCE(patients.email_address, appointments.requester_email) as email_address'),
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
}
