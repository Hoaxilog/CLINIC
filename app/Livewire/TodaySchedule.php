<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class TodaySchedule extends Component
{
    public $todayAppointments = [];

    public $waitingQueue = [];

    public $ongoingAppointments = [];

    public $showAppointmentModal = false;

    public $viewingAppointmentId = null;

    public $firstName = '';

    public $lastName = '';

    public $middleName = '';

    public $contactNumber = '';

    public $birthDate = null;

    public $selectedService = '';

    public $selectedDate = null;

    public $selectedTime = null;

    public $endTime = null;

    public $appointmentStatus = '';

    public $servicesList = [];

    public $isPatientFormOpen = false;

    // [ADDED] Property to store the dentist's name
    public $dentistName = '';

    public function mount()
    {
        $this->refreshAllSections();
        $this->servicesList = DB::table('services')->get();
    }

    #[On('patient-form-opened')]
    public function stopPolling()
    {
        $this->isPatientFormOpen = true;
    }

    #[On('patient-form-closed')]
    public function resumePolling()
    {
        $this->isPatientFormOpen = false;
        $this->refreshAllSections();
    }

    public function loadDashboardData()
    {
        $this->refreshAllSections();
    }

    public function refreshAllSections()
    {
        $this->refreshTodaySchedule();
        $this->refreshWaitingQueue();
        $this->refreshOngoingAppointments();
    }

    public function refreshTodaySchedule()
    {
        $today = Carbon::today();
        $this->todayAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointments.appointment_date', $today)
            ->whereIn('appointments.status', ['Scheduled', 'Completed', 'Cancelled'])
            ->orderBy('appointments.appointment_date', 'asc')
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.service_id',
                'appointments.appointment_date',
                'appointments.status',
                'patients.first_name',
                'patients.last_name',
                'patients.middle_name',
                'patients.mobile_number',
                'patients.birth_date',
                'services.duration',
                'services.service_name'
            )
            ->get();
    }

    public function refreshWaitingQueue()
    {
        $today = Carbon::today();
        $this->waitingQueue = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointments.appointment_date', $today)
            ->where('appointments.status', 'Waiting')
            ->orderBy('appointments.appointment_date', 'asc')
            ->orderBy('appointments.created_at', 'asc')
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.service_id',
                'appointments.appointment_date',
                'appointments.status',
                'patients.first_name',
                'patients.last_name',
                'patients.middle_name',
                'patients.mobile_number',
                'patients.birth_date',
                'services.duration',
                'services.service_name',
                DB::raw('appointments.updated_at as waited_at')
            )
            ->get();
    }

    public function refreshOngoingAppointments()
    {
        $today = Carbon::today();
        $user = Auth::user();

        $ongoingQuery = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.dentist_id', '=', 'users.id')
            ->whereDate('appointments.appointment_date', $today)
            ->where('appointments.status', 'Ongoing')
            ->orderBy('appointments.updated_at', 'desc')
            ->select(
                'appointments.id',
                'appointments.patient_id',
                'appointments.service_id',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.dentist_id',
                'patients.first_name',
                'patients.last_name',
                'patients.middle_name',
                'patients.mobile_number',
                'patients.birth_date',
                'services.duration',
                'services.service_name',
                'users.username as dentist_name'
            );

        if ($user?->isDentist()) {
            $ongoingQuery->where('dentist_id', $user->id);
        }

        $this->ongoingAppointments = $ongoingQuery->get();
    }

    public function admitPatient()
    {
        $appointment = DB::table('appointments')->find($this->viewingAppointmentId);
        $service = DB::table('services')->where('id', $this->selectedService)->first();

        if (! $appointment || ! $service) {
            return;
        }
        if ($appointment->status !== 'Waiting') {
            session()->flash('error', 'Only Ready patients can be admitted.');

            return;
        }

        $startTime = Carbon::parse($appointment->appointment_date);
        sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
        $durationMinutes = ($h * 60) + $m;
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        $dentistId = Auth::id();
        $hasConflict = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $this->viewingAppointmentId)
            ->where('appointments.status', 'Ongoing')
            ->where('appointments.dentist_id', $dentistId)
            ->whereDate('appointment_date', $startTime->toDateString())
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('appointment_date', '<', $endTime)
                    ->whereRaw('DATE_ADD(appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND) > ?', [$startTime]);
            })
            ->exists();

        if ($hasConflict) {
            session()->flash('error', 'Cannot admit: This slot is double-booked.');
        } else {
            $updated = DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->where('status', 'Waiting')
                ->update([
                    'status' => 'Ongoing',
                    'service_id' => $this->selectedService,
                    'dentist_id' => $dentistId,
                    'updated_at' => now(),
                ]);

            if (! $updated) {
                session()->flash('error', 'This appointment was already admitted or updated. Please refresh.');

                return;
            }

            $subject = new Appointment;
            $subject->id = $this->viewingAppointmentId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->event('appointment_admitted')
                ->log('Admitted Patient to Chair');

            session()->flash('success', 'Patient admitted to chair successfully!');

            $this->refreshWaitingQueue();
            $this->refreshOngoingAppointments();
            $this->closeAppointmentModal();
            $this->dispatch('editPatient', id: $appointment->patient_id, startStep: 3);
        }
    }

    public function callNextPatient()
    {
        if (Auth::user()?->role !== 1) {
            session()->flash('error', 'Only dentists can call the next patient.');

            return;
        }

        $today = Carbon::today();

        $next = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->where('status', 'Waiting')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();

        if (! $next) {
            session()->flash('info', 'No Ready patients in queue.');

            return;
        }

        $service = DB::table('services')->where('id', $next->service_id)->first();
        if (! $service) {
            session()->flash('error', 'Missing service for selected appointment.');

            return;
        }

        $startTime = Carbon::parse($next->appointment_date);
        sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
        $durationMinutes = ($h * 60) + $m;
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        $hasConflict = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $next->id)
            ->whereNotIn('appointments.status', ['Cancelled', 'Waiting', 'Completed'])
            ->whereDate('appointment_date', $startTime->toDateString())
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('appointment_date', '<', $endTime)
                    ->whereRaw('DATE_ADD(appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND) > ?', [$startTime]);
            })
            ->exists();

        if ($hasConflict) {
            session()->flash('error', 'Cannot call next: This slot is double-booked.');

            return;
        }

        DB::table('appointments')->where('id', $next->id)->update([
            'status' => 'Ongoing',
            'dentist_id' => Auth::id(),
            'updated_at' => now(),
        ]);

        $subject = new Appointment;
        $subject->id = $next->id;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('appointment_admitted')
            ->withProperties([
                'old' => [
                    'status' => $next->status ?? null,
                    'service_id' => $next->service_id ?? null,
                    'dentist_id' => $next->dentist_id ?? null,
                ],
                'attributes' => [
                    'status' => 'Ongoing',
                    'service_id' => $next->service_id,
                    'dentist_id' => Auth::id(),
                ],
            ])
            ->log('Admitted Appointment');

        $this->refreshWaitingQueue();
        $this->refreshOngoingAppointments();
        $this->viewAppointment($next->id);
    }

    public function viewAppointment($appointmentId)
    {
        // Reset fields first so stale patient data never flashes while switching cards.
        $this->reset([
            'firstName',
            'lastName',
            'middleName',
            'contactNumber',
            'birthDate',
            'selectedService',
            'selectedDate',
            'selectedTime',
            'endTime',
            'appointmentStatus',
            'viewingAppointmentId',
            'dentistName',
        ]);

        // Fast path: use already loaded board datasets before hitting the database.
        $appointment = $this->findLoadedAppointment((int) $appointmentId);

        if (! $appointment) {
            $appointment = DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->leftJoin('users', 'appointments.dentist_id', '=', 'users.id')
                ->select(
                    'appointments.id',
                    'appointments.patient_id',
                    'appointments.service_id',
                    'appointments.appointment_date',
                    'appointments.status',
                    'patients.first_name',
                    'patients.last_name',
                    'patients.middle_name',
                    'patients.mobile_number',
                    'patients.birth_date',
                    'services.service_name',
                    'services.duration',
                    'users.username as dentist_name'
                )
                ->where('appointments.id', $appointmentId)
                ->first();
        }

        if ($appointment) {
            $this->firstName = $appointment->first_name;
            $this->lastName = $appointment->last_name;
            $this->middleName = $appointment->middle_name;
            $this->contactNumber = $appointment->mobile_number;
            $this->birthDate = $appointment->birth_date;
            $this->selectedService = $appointment->service_id;
            $this->viewingAppointmentId = $appointment->id;
            $this->appointmentStatus = $appointment->status;
            $this->dentistName = $appointment->dentist_name ?? '';

            $dt = Carbon::parse($appointment->appointment_date);
            $this->selectedDate = $dt->toDateString();
            $this->selectedTime = $dt->format('h:i A');
            $durationInMinutes = $this->durationToMinutes($appointment->duration ?? null);
            $this->endTime = $dt->copy()->addMinutes($durationInMinutes)->format('h:i A');

            $this->showAppointmentModal = true;
            $this->dispatch('appointment-details-loaded');

            return;
        }

        $this->dispatch('appointment-details-loaded');
    }

    protected function findLoadedAppointment(int $appointmentId): ?object
    {
        $appointment = collect($this->todayAppointments)
            ->concat($this->waitingQueue)
            ->concat($this->ongoingAppointments)
            ->firstWhere('id', $appointmentId);

        return $appointment ?: null;
    }

    protected function durationToMinutes(?string $duration): int
    {
        if (! $duration) {
            return 0;
        }

        sscanf($duration, '%d:%d:%d', $hours, $minutes, $seconds);

        return ((int) $hours * 60) + (int) $minutes;
    }

    public function openPatientChart()
    {
        if ($this->viewingAppointmentId) {
            $appt = DB::table('appointments')->find($this->viewingAppointmentId);
            if ($appt) {
                $this->dispatch('editPatient', id: $appt->patient_id, startStep: 3);
                $this->closeAppointmentModal();
            }
        }
    }

    public function processPatient()
    {
        if ($this->viewingAppointmentId) {
            $appt = DB::table('appointments')->find($this->viewingAppointmentId);
            if ($appt) {
                $this->dispatch('editPatient', id: $appt->patient_id, startStep: 1);
                $this->closeAppointmentModal();
            }
        }
    }

    public function updateStatus($newStatus)
    {
        if ($this->viewingAppointmentId) {
            $oldAppt = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
            $oldStatus = $oldAppt ? $oldAppt->status : 'Unknown';

            DB::table('appointments')->where('id', $this->viewingAppointmentId)->update([
                'status' => $newStatus, 'updated_at' => now(),
            ]);

            if ($oldStatus !== $newStatus) {
                $subject = new Appointment;
                $subject->id = $this->viewingAppointmentId;

                $eventName = $newStatus === 'Cancelled' ? 'appointment_cancelled' : 'appointment_updated';
                $logMessage = $newStatus === 'Cancelled' ? 'Cancelled Appointment' : 'Updated Appointment Status';

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subject)
                    ->event($eventName)
                    ->withProperties([
                        'old' => ['status' => $oldStatus],
                        'attributes' => [
                            'status' => $newStatus,
                            'patient_name' => trim("{$this->lastName}, {$this->firstName} {$this->middleName}"),
                        ],
                    ])
                    ->log($logMessage);
            }

            session()->flash('success', "Appointment status updated to '$newStatus'.");
            $this->refreshAllSections();
            $this->closeAppointmentModal();
        }
    }

    public function closeAppointmentModal()
    {
        $this->showAppointmentModal = false;
        $this->reset([
            'firstName',
            'lastName',
            'middleName',
            'contactNumber',
            'birthDate',
            'selectedService',
            'selectedDate',
            'selectedTime',
            'endTime',
            'appointmentStatus',
            'viewingAppointmentId',
            'dentistName',
        ]);
    }

    public function render()
    {
        return view('livewire.today-schedule');
    }
}
