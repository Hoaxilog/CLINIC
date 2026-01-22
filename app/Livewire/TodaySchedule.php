<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $this->loadDashboardData();
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
        $this->loadDashboardData(); // Refresh data immediately upon closing
    }

    public function loadDashboardData()
    {
        $today = Carbon::today();
        $user = Auth::user(); 

        // 1. LEFT COLUMN: Agenda
        $this->todayAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['Scheduled', 'Completed', 'Cancelled']) 
            ->orderBy('appointment_date', 'asc')
            ->select('appointments.*', 'patients.first_name', 'patients.last_name', 'services.service_name', 'services.duration')
            ->get(); 

        // 2. MIDDLE COLUMN: Waiting Room
        $this->waitingQueue = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['Waiting', 'Arrived']) 
            ->orderBy('created_at', 'asc')
            ->select('appointments.*', 'patients.first_name', 'patients.last_name', 'services.service_name', 'services.duration')
            ->get();

        // 3. RIGHT COLUMN: Now Serving
        $ongoingQuery = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            // [ADDED] Join users table to get dentist name
            ->leftJoin('users', 'appointments.dentist_id', '=', 'users.id') 
            ->whereDate('appointment_date', $today)
            ->where('status', 'Ongoing')
            ->orderBy('updated_at', 'desc')
            ->select(
                'appointments.*', 
                'patients.first_name', 
                'patients.last_name', 
                'services.service_name', 
                'services.duration',
                'users.username as dentist_name' // [ADDED] Select dentist name
            );

        if ($user->role === 1) { 
            $ongoingQuery->where('dentist_id', $user->id);
        }

        $this->ongoingAppointments = $ongoingQuery->get();
    }

    public function admitPatient()
    {
        $appointment = DB::table('appointments')->find($this->viewingAppointmentId);
        $service = DB::table('services')->where('id', $this->selectedService)->first();
        
        if (!$appointment || !$service) return;

        $startTime = Carbon::parse($appointment->appointment_date); 
        sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
        $durationMinutes = ($h * 60) + $m;
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

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
            DB::table('appointments')->where('id', $this->viewingAppointmentId)->update([
                'status' => 'Ongoing',
                'service_id' => $this->selectedService,
                'dentist_id' => Auth::id(), 
                'updated_at' => now()
            ]);

            session()->flash('success', 'Patient admitted to chair successfully!');
            
            $this->loadDashboardData();
            $this->closeAppointmentModal();
            $this->dispatch('editPatient', id: $appointment->patient_id, startStep: 3);
        }
    }

    public function viewAppointment($appointmentId)
    {
        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            // [ADDED] Join users to get dentist details
            ->leftJoin('users', 'appointments.dentist_id', '=', 'users.id') 
            ->select(
                'appointments.*',
                'patients.first_name', 'patients.last_name', 'patients.middle_name',
                'patients.mobile_number', 'patients.birth_date',
                'services.service_name', 'services.duration',
                'users.username as dentist_name' // [ADDED]
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
            
            // [ADDED] Set the dentist name property
            $this->dentistName = $appointment->dentist_name; 

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

            session()->flash('success', "Appointment status updated to '$newStatus'.");
            $this->loadDashboardData();
            $this->closeAppointmentModal();
        }
    }

    public function closeAppointmentModal()
    {
        $this->showAppointmentModal = false;
        // [ADDED] Reset dentist name
        $this->reset(['firstName', 'lastName', 'contactNumber', 'viewingAppointmentId', 'dentistName']);
    }

    public function render() 
    { 
        return view('livewire.today-schedule'); 
    }
}