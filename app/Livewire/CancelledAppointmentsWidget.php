<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class CancelledAppointmentsWidget extends Component
{
    public Collection $cancelledAppointments;

    public function mount(): void
    {
        $this->loadCancelledAppointments();
    }

    public function loadCancelledAppointments(): void
    {
        if ((int) (Auth::user()?->role ?? 0) === 3) {
            $this->cancelledAppointments = collect();

            return;
        }

        $this->cancelledAppointments = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'Cancelled')
            ->orderByDesc('appointments.updated_at')
            ->limit(5)
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.service_id',
                'appointments.appointment_date',
                'appointments.updated_at',
                'appointments.cancellation_reason',
                DB::raw('COALESCE(patients.first_name, appointments.requested_patient_first_name, appointments.requester_first_name) as first_name'),
                DB::raw('COALESCE(patients.last_name, appointments.requested_patient_last_name, appointments.requester_last_name) as last_name'),
                DB::raw('COALESCE(patients.middle_name, appointments.requested_patient_middle_name, appointments.requester_middle_name) as middle_name'),
                DB::raw('COALESCE(patients.birth_date, appointments.requested_patient_birth_date, appointments.requester_birth_date) as birth_date'),
                DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as contact_number'),
                DB::raw('COALESCE(patients.email_address, appointments.requester_email) as email_address'),
                'services.service_name'
            )
            ->get()
            ->map(function (object $appointment): object {
                $appointment->patient_name = trim(
                    implode(' ', array_filter([
                        $appointment->first_name ?? null,
                        $appointment->last_name ?? null,
                    ]))
                );

                $appointment->patient_name = $appointment->patient_name !== ''
                    ? $appointment->patient_name
                    : 'Unknown patient';

                $appointment->reason_label = $appointment->cancellation_reason
                    ? (string) $appointment->cancellation_reason
                    : 'No cancellation reason was provided.';

                $appointment->phone_href = $this->phoneHref($appointment->contact_number ?? null);
                $appointment->mail_href = $this->mailHref($appointment->email_address ?? null);

                return $appointment;
            });
    }

    protected function phoneHref(?string $contactNumber): ?string
    {
        $digits = preg_replace('/[^0-9+]/', '', (string) $contactNumber);

        return $digits !== '' ? 'tel:'.$digits : null;
    }

    protected function mailHref(?string $emailAddress): ?string
    {
        $emailAddress = trim((string) $emailAddress);

        return $emailAddress !== '' ? 'mailto:'.$emailAddress : null;
    }

    public function render(): View
    {
        return view('livewire.cancelled-appointments-widget');
    }
}
