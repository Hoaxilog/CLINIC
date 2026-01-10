<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TodaySchedule extends Component
{
    // Stats
    public $todayCount = 0;
    public $completedCount = 0;
    public $todayAppointments = [];
    public $showAppointmentModal = false;
    public $isViewing = false;
    public $viewingAppointmentId = null;

    public $firstName = '';
    public $lastName = '';
    public $middleName = '';
    public $contactNumber = '';
    public $birthDate = null;
    public $recordNumber = '';
    public $selectedService = '';
    public $selectedDate = null;
    public $selectedTime = null;
    public $endTime = null;
    public $appointmentStatus = '';
    
    public $servicesList = [];
    public $searchQuery = ''; 
    public $patientSearchResults = [];

    public function mount()
    {
        $this->loadDashboardData();
        $this->servicesList = DB::table('services')->get();
    }

    public function loadDashboardData()
    {
        $this->completedCount = DB::table('appointments')
            ->where('status', 'Completed')
            ->count();

        // today schedule only
        $this->todayAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointment_date', Carbon::today())
            ->orderBy('appointment_date', 'asc')
            ->select(
                'appointments.*',
                'patients.first_name',
                'patients.last_name',
                'services.service_name',
                'services.duration'
            )
            ->get()
            ->toArray();

        $this->todayCount = count($this->todayAppointments);
    }

    public function viewAppointment($appointmentId)
    {
        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select(
                'appointments.*',
                'patients.first_name',
                'patients.last_name',
                'patients.middle_name',
                'patients.mobile_number',
                'patients.birth_date',
                'services.service_name',
                'services.duration'
            )
            ->where('appointments.id', $appointmentId)
            ->first();

        if ($appointment) {
            $this->firstName = $appointment->first_name;
            $this->lastName = $appointment->last_name;
            $this->middleName = $appointment->middle_name;
            $this->contactNumber = $appointment->mobile_number;
            $this->birthDate = $appointment->birth_date;
            $this->selectedService = $appointment->service_id;
            $this->viewingAppointmentId = $appointment->id;
            $this->appointmentStatus = $appointment->status;

            $dt = Carbon::parse($appointment->appointment_date);
            $this->selectedDate = $dt->toDateString();
            
            // Format to 12-hour AM/PM
            $this->selectedTime = $dt->format('h:i A');

            sscanf($appointment->duration, '%d:%d:%d', $h, $m, $s);
            $durationInMinutes = ($h * 60) + $m;
            
            $this->endTime = $dt->copy()->addMinutes($durationInMinutes)->format('h:i A');

            $this->isViewing = true;
            $this->showAppointmentModal = true;
        }
    }

    public function updateStatus($newStatus)
    {
        if ($this->viewingAppointmentId) {
            DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);

            $this->loadDashboardData(); // Refreshes counts immediately
            $this->closeAppointmentModal();
        }
    }

    public function closeAppointmentModal()
    {
        $this->showAppointmentModal = false;
        $this->reset(['firstName', 'lastName', 'contactNumber', 'viewingAppointmentId']);
    }

    public function saveAppointment() { $this->closeAppointmentModal(); }
    public function updatedSearchQuery() { } 
    public function selectPatient($id) { }

    public function render()
    {
        return view('livewire.today-schedule');
    }
}