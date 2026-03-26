<?php

namespace App\Livewire\Appointment;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AppointmentHistoryPage extends Component
{
    use WithPagination;

    private const STATUS_ALL = 'All';

    private const HISTORY_STATUSES = ['Cancelled', 'Completed'];

    public string $search = '';

    public string $status = self::STATUS_ALL;

    public string $serviceId = '';

    public string $fromDate = '';

    public string $toDate = '';

    public int $perPage = 12;

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->fromDate = now()->subMonths(3)->toDateString();
        $this->toDate = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingServiceId(): void
    {
        $this->resetPage();
    }

    public function updatingFromDate(): void
    {
        $this->resetPage();
    }

    public function updatingToDate(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'serviceId');
        $this->status = self::STATUS_ALL;
        $this->fromDate = now()->subMonths(3)->toDateString();
        $this->toDate = now()->toDateString();
        $this->resetPage();
    }

    public function render(): View
    {
        $hasRequestedPatient = $this->hasRequestedPatientColumns();
        $firstNameExpr = $hasRequestedPatient
            ? "COALESCE(patients.first_name, appointments.requested_patient_first_name, appointments.requester_first_name)"
            : "COALESCE(patients.first_name, appointments.requester_first_name)";
        $lastNameExpr = $hasRequestedPatient
            ? "COALESCE(patients.last_name, appointments.requested_patient_last_name, appointments.requester_last_name)"
            : "COALESCE(patients.last_name, appointments.requester_last_name)";
        $middleNameExpr = $hasRequestedPatient
            ? "COALESCE(patients.middle_name, appointments.requested_patient_middle_name, appointments.requester_middle_name)"
            : "COALESCE(patients.middle_name, appointments.requester_middle_name)";

        $appointments = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.service_id',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.updated_at',
                'appointments.cancellation_reason',
                'services.service_name',
                DB::raw($firstNameExpr.' as first_name'),
                DB::raw($lastNameExpr.' as last_name'),
                DB::raw($middleNameExpr.' as middle_name')
            )
            ->whereIn('appointments.status', self::HISTORY_STATUSES)
            ->when($this->status !== self::STATUS_ALL, fn ($query) => $query->where('appointments.status', $this->status))
            ->when($this->serviceId !== '', fn ($query) => $query->where('appointments.service_id', $this->serviceId))
            ->when($this->fromDate !== '', fn ($query) => $query->where('appointments.appointment_date', '>=', Carbon::parse($this->fromDate)->startOfDay()))
            ->when($this->toDate !== '', fn ($query) => $query->where('appointments.appointment_date', '<=', Carbon::parse($this->toDate)->endOfDay()))
            ->when($this->search !== '', function ($query) use ($firstNameExpr, $lastNameExpr) {
                $term = trim($this->search);

                $query->where(function ($subQuery) use ($term, $firstNameExpr, $lastNameExpr) {
                    $subQuery
                        ->where('services.service_name', 'like', '%'.$term.'%')
                        ->orWhere('appointments.id', 'like', '%'.$term.'%')
                        ->orWhere(DB::raw($firstNameExpr), 'like', '%'.$term.'%')
                        ->orWhere(DB::raw($lastNameExpr), 'like', '%'.$term.'%')
                        ->orWhere(DB::raw("CONCAT($firstNameExpr, ' ', $lastNameExpr)"), 'like', '%'.$term.'%');
                });
            })
            ->orderByDesc('appointments.appointment_date')
            ->paginate($this->perPage);

        $appointments->getCollection()->transform(function (object $appointment): object {
            $appointment->patient_name = trim(implode(' ', array_filter([
                $appointment->first_name ?? null,
                $appointment->middle_name ?? null,
                $appointment->last_name ?? null,
            ])));

            if ($appointment->patient_name === '') {
                $appointment->patient_name = 'Unknown patient';
            }

            $appointment->reason_label = filled($appointment->cancellation_reason)
                ? (string) $appointment->cancellation_reason
                : 'No cancellation reason was provided.';

            return $appointment;
        });

        $serviceOptions = DB::table('services')
            ->orderBy('service_name')
            ->pluck('service_name', 'id');

        return view('livewire.appointment.appointment-history-page', [
            'appointments' => $appointments,
            'serviceOptions' => $serviceOptions,
        ]);
    }

    protected function hasRequestedPatientColumns(): bool
    {
        static $resolved = null;

        if ($resolved !== null) {
            return $resolved;
        }

        $schema = DB::connection()->getSchemaBuilder();
        $resolved = $schema->hasColumn('appointments', 'requested_patient_first_name')
            && $schema->hasColumn('appointments', 'requested_patient_last_name');

        return $resolved;
    }
}
