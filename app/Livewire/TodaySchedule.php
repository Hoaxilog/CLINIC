<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TodaySchedule extends Component
{
    // === 1. DEFINE 3 SEPARATE LISTS ===
    public $todayAppointments = [];   // LEFT: Agenda (Future + History)
    public $waitingQueue = [];        // MIDDLE: Waiting Room (Lobby)
    public $ongoingAppointments = []; // RIGHT: Now Serving (In Chair)

    // Properties
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

    public function mount()
    {
        $this->loadDashboardData();
        $this->servicesList = DB::table('services')->get();
    }

    public function loadDashboardData()
    {
        $today = Carbon::today();

        // 1. LEFT COLUMN: The Agenda (Scheduled + Completed/Cancelled)
        // NOTE: We REMOVED 'Ongoing' from here.
        $this->todayAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['Scheduled', 'Completed', 'Cancelled']) 
            ->orderBy('appointment_date', 'asc')
            ->select('appointments.*', 'patients.first_name', 'patients.last_name', 'services.service_name', 'services.duration')
            ->get(); 

        // 2. MIDDLE COLUMN: Waiting Room (Waiting + Arrived)
        $this->waitingQueue = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['Waiting', 'Arrived']) 
            ->orderBy('created_at', 'asc')
            ->select('appointments.*', 'patients.first_name', 'patients.last_name', 'services.service_name', 'services.duration')
            ->get();

        // 3. RIGHT COLUMN: Now Serving (Ongoing ONLY)
        $this->ongoingAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointment_date', $today)
            ->where('status', 'Ongoing') 
            ->orderBy('updated_at', 'desc')
            ->select('appointments.*', 'patients.first_name', 'patients.last_name', 'services.service_name', 'services.duration')
            ->get();
    }

    // --- LOGIC: Admit without changing time ---
    public function admitPatient()
    {
        $appointment = DB::table('appointments')->find($this->viewingAppointmentId);
        $service = DB::table('services')->where('id', $this->selectedService)->first();
        
        if (!$appointment || !$service) return;

        // Use Original Time
        $startTime = Carbon::parse($appointment->appointment_date); 
        
        sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
        $durationMinutes = ($h * 60) + $m;
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        // Conflict Check
        $hasConflict = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $this->viewingAppointmentId)
            ->whereNotIn('appointments.status', ['Cancelled', 'Waiting', 'Completed'])
            ->whereDate('appointment_date', $startTime->toDateString())
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('appointment_date', '<', $endTime)
                      ->whereRaw("DATE_ADD(appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND) > ?", [$startTime]);
            })
            ->exists();

        if ($hasConflict) {
            session()->flash('error', 'Cannot admit: This slot is double-booked.');
        } else {
            // Update Status -> This moves them to the RIGHT COLUMN automatically
            DB::table('appointments')->where('id', $this->viewingAppointmentId)->update([
                'status' => 'Ongoing',
                'service_id' => $this->selectedService,
                'updated_at' => now()
            ]);
            
            $this->loadDashboardData();
            $this->closeAppointmentModal();
            $this->dispatch('editPatient', id: $appointment->patient_id, startStep: 3);
        }
    }

    // --- HELPER FUNCTIONS ---
    public function viewAppointment($appointmentId)
    {
        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select(
                'appointments.*',
                'patients.first_name', 'patients.last_name', 'patients.middle_name',
                'patients.mobile_number', 'patients.birth_date',
                'services.service_name', 'services.duration'
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
            $this->selectedTime = $dt->format('h:i A');
    
            sscanf($appointment->duration, '%d:%d:%d', $h, $m, $s);
            $durationInMinutes = ($h * 60) + $m;
            $this->endTime = $dt->copy()->addMinutes($durationInMinutes)->format('h:i A');

            $this->showAppointmentModal = true;
        }
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

    public function processPatient() {
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
            DB::table('appointments')->where('id', $this->viewingAppointmentId)->update([
                'status' => $newStatus, 'updated_at' => now()
            ]);
            $this->loadDashboardData();
            $this->closeAppointmentModal();
        }
    }

    public function closeAppointmentModal()
    {
        $this->showAppointmentModal = false;
        $this->reset(['firstName', 'lastName', 'contactNumber', 'viewingAppointmentId']);
    }

    public function render() 
    { 
        return view('livewire.today-schedule'); 
    }
}