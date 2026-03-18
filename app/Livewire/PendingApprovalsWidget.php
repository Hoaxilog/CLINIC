<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

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
                'appointments.patient_id',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.booking_for_other',
                'appointments.requester_first_name',
                'appointments.requester_last_name',
                'appointments.requester_contact_number',
                'appointments.requester_email',
                'appointments.requester_relationship_to_patient',
                'appointments.requester_birth_date',
                'appointments.requested_patient_first_name',
                'appointments.requested_patient_last_name',
                'appointments.requested_patient_birth_date',
                DB::raw($this->appointmentPatientFirstNameExpression().' as first_name'),
                DB::raw($this->appointmentPatientLastNameExpression().' as last_name'),
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
                'appointments.patient_id',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.booking_for_other',
                'appointments.requester_first_name',
                'appointments.requester_last_name',
                'appointments.requester_contact_number',
                'appointments.requester_email',
                'appointments.requester_relationship_to_patient',
                'appointments.requester_birth_date',
                'appointments.requested_patient_first_name',
                'appointments.requested_patient_last_name',
                'appointments.requested_patient_birth_date',
                DB::raw($this->appointmentPatientFirstNameExpression().' as first_name'),
                DB::raw($this->appointmentPatientLastNameExpression().' as last_name'),
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
        if (! $appointment) {
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
        if (! $oldAppt) {
            return false;
        }

        DB::table('appointments')
            ->where('id', $appointmentId)
            ->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

        if ($oldAppt->status !== $newStatus) {
            $subject = new Appointment;
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

            if ($oldAppt->status === 'Pending' && $newStatus === 'Scheduled') {
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subject)
                    ->event('appointment_request_approved')
                    ->withProperties([
                        'attributes' => [
                            'patient_id' => $oldAppt->patient_id,
                            'appointment_id' => (int) $appointmentId,
                        ],
                    ])
                    ->log('Approved Appointment Request');

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subject)
                    ->event('official_appointment_created')
                    ->withProperties([
                        'attributes' => [
                            'appointment_id' => (int) $appointmentId,
                            'patient_id' => $oldAppt->patient_id,
                            'status' => $newStatus,
                        ],
                    ])
                    ->log('Official Appointment Linked to Patient');
            }
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
                DB::raw($this->appointmentPatientFirstNameExpression().' as first_name'),
                DB::raw($this->appointmentPatientLastNameExpression().' as last_name'),
                DB::raw('COALESCE(patients.email_address, appointments.requester_email) as email_address'),
                'services.service_name'
            )
            ->where('appointments.id', $appointmentId)
            ->first();

        if (! $appointment || empty($appointment->email_address)) {
            return;
        }

        try {
            Mail::send('appointment.emails.appointment-status-update', [
                'name' => trim($appointment->first_name.' '.$appointment->last_name),
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

    protected function appointmentPatientFirstNameExpression(): string
    {
        if (Schema::hasColumn('appointments', 'requested_patient_first_name')) {
            return 'COALESCE(patients.first_name, appointments.requested_patient_first_name, appointments.requester_first_name)';
        }

        return 'COALESCE(patients.first_name, appointments.requester_first_name)';
    }

    protected function appointmentPatientLastNameExpression(): string
    {
        if (Schema::hasColumn('appointments', 'requested_patient_last_name')) {
            return 'COALESCE(patients.last_name, appointments.requested_patient_last_name, appointments.requester_last_name)';
        }

        return 'COALESCE(patients.last_name, appointments.requester_last_name)';
    }

    public function appointmentHasSeparateRequester(object $appointment): bool
    {
        return ! empty($appointment->booking_for_other);
    }

    public function appointmentPatientDisplayName(object $appointment): string
    {
        return trim((string) (($appointment->first_name ?? '').' '.($appointment->last_name ?? '')));
    }

    public function appointmentRequesterDisplayName(object $appointment): string
    {
        $firstName = trim((string) ($appointment->requester_first_name ?? ''));
        $lastName = trim((string) ($appointment->requester_last_name ?? ''));

        return trim($firstName.' '.$lastName);
    }

    public function appointmentRequesterRelationshipLabel(object $appointment): ?string
    {
        $relationship = trim((string) ($appointment->requester_relationship_to_patient ?? ''));

        return $relationship !== '' ? $relationship : null;
    }

    public function appointmentPatientBirthDateDisplay(object $appointment): ?string
    {
        $birthDate = null;

        if (! empty($appointment->booking_for_other) && ! empty($appointment->requested_patient_birth_date)) {
            $birthDate = $appointment->requested_patient_birth_date;
        } elseif (! empty($appointment->requester_birth_date)) {
            $birthDate = $appointment->requester_birth_date;
        }

        if (empty($birthDate)) {
            return null;
        }

        try {
            return Carbon::parse($birthDate)->format('F j, Y');
        } catch (\Throwable) {
            return (string) $birthDate;
        }
    }
}
