<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonInterval; // <-- Add this at the top

class AppointmentCalendar extends Component
{
    // ... (All other properties and methods are unchanged) ...
    public $currentDate;
    public $viewType = 'week';
    public $weekDates = [];
    public $timeSlots = [];
    /** @var \Illuminate\Support\Collection */
    public $appointments = [];
    public $showAppointmentModal = false;
    /** @var \Illuminate\Support\Collection */
    public $servicesList = [];
    public $firstName = '';
    public $lastName = '';
    public $middleName = '';
    public $recordNumber = '';
    public $contactNumber = '';
    public $birthDate = null;  
    public $selectedService = '';
    public $selectedDate;      // For the date picker input (Y-m-d)
    public $appointmentDate;   // For the modal display (Y-m-d stored, formatted on display)
    public $selectedTime = null;
    public $endTime = null;
    public $isViewing = false; 
    public $viewingAppointmentId = null; 
    public $appointmentStatus = '';     
    public $searchQuery = '';
    public $patientSearchResults = [];
    public $selectedMonthYear; // stores "YYYY-MM" format
    public $selectableMonthYears = [];

    protected $rules = [
        'firstName' => 'required|string|max:100',
        'lastName' => 'required|string|max:100',
        'middleName' => 'nullable|string|max:100',
        'contactNumber' => 'required|string|max:20',
        'selectedService' => 'required',
        'selectedDate' => 'required',
        'selectedTime' => 'required',
        'endTime' => 'required',
        'birthDate' => 'required'
    ];

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->selectedDate = $this->currentDate->format('Y-m-d');

        $this->generateWeekDates(); 
        $this->generateTimeSlots();
        $this->loadAppointments();
        $this->servicesList = DB::table('services')->get();
    }

    public function generateWeekDates()
    {
        $this->weekDates = [];
        $startOfWeek = $this->currentDate->copy()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $this->weekDates[] = $startOfWeek->copy()->addDays($i);
        }
    }

    public function generateTimeSlots()
    {
        $this->timeSlots = [];
        for ($hour = 9; $hour <= 24; $hour++) {
            $this->timeSlots[] = sprintf('%02d:00', $hour);
            if ($hour != 19) {
                $this->timeSlots[] = sprintf('%02d:30', $hour);
            }        
        }
    }

    public function loadAppointments()
    {
        $startOfWeek = $this->weekDates[0]->startOfDay();
        $endOfWeek = $this->weekDates[6]->endOfDay();

        $this->appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
            ->select(
                'appointments.*', 
                'patients.first_name', 
                'patients.last_name', 
                'patients.middle_name', 
                'services.service_name', 
                'services.duration'
            )
            ->get()
            // THIS IS WTF
            ->map(function($appointment) {
                
                sscanf($appointment->duration, '%d:%d:%d', $h, $m, $s);
                $appointment->duration_in_minutes = ($h * 60) + $m;
                
                $carbonDate = Carbon::parse($appointment->appointment_date);
                $appointment->start_date = $carbonDate->toDateString();
                $appointment->start_time = $carbonDate->format('H:i');
                
                $appointment->end_time = $carbonDate->copy()
                                 ->addMinutes($appointment->duration_in_minutes)
                                 ->format('H:i');
                return $appointment;
            });
    }
    

    public function getAppointmentsForDay($date)
    {
        return $this->appointments->where('start_date', $date->toDateString());
    }

    public function previousWeek()
    {
        $this->currentDate = $this->currentDate->subWeek();
        $this->generateWeekDates();
        $this->loadAppointments();
    }

    public function nextWeek()
    {
        $this->currentDate = $this->currentDate->addWeek();
        $this->generateWeekDates();
        $this->loadAppointments();
    }

    public function changeView($type)
    {
        $this->viewType = $type;
    }
    
    protected function resetForm()
    {
        $this->resetValidation();
        $this->firstName = '';
        $this->lastName = '';
        $this->middleName = '';
        $this->recordNumber = '';
        $this->contactNumber = '';
        $this->birthDate = null; 
        $this->selectedService = '';
        $this->appointmentDate = null;  // Reset this instead
        $this->selectedTime = null;
        $this->endTime = null;
        $this->isViewing = false; 
        $this->viewingAppointmentId = null;
        $this->appointmentStatus = '';
        $this->searchQuery = '';
        $this->patientSearchResults = [];   
        
    }

    public function openAppointmentModal($date, $time)
    {
        $this->resetForm();
        $this->selectedDate = $date;      // Keep for date picker
        $this->appointmentDate = $date;   // NEW: Use this in modal
        $this->selectedTime = $time;
        $this->showAppointmentModal = true;
    }

    public function closeAppointmentModal()
    {
        $this->showAppointmentModal = false;
        $this->resetForm();
    }

    public function updatedSelectedService($serviceId)
    {
        $service = $this->servicesList->firstWhere('id', $serviceId);

        if ($service) {
            // This logic was already correct, as it's used in the modal
            list($hours, $minutes, $seconds) = explode(':', $service->duration);
            $this->endTime = Carbon::parse($this->selectedTime)
                                   ->addHours((int)$hours)
                                   ->addMinutes((int)$minutes)
                                   ->format('H:i');
        } else {
            $this->endTime = null;
        }
    }

    public function saveAppointment()
    {
        if ($this->isViewing) {
            return; 
        }
    
        $this->validate();

        try {
            $service = $this->servicesList->firstWhere('id', $this->selectedService);
            if (!$service) {
                $this->addError('selectedService', 'Please select a valid service.');
                return;
            }
            sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
            $durationInMinutes = ($h * 60) + $m;

            $proposedStart = Carbon::parse($this->appointmentDate)->setTimeFromTimeString($this->selectedTime);
            $proposedEnd = $proposedStart->copy()->addMinutes($durationInMinutes);
            
            // Conflict Check (Keep your existing logic)
            $conflicts = DB::table('appointments')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->where(function ($query) use ($proposedStart, $proposedEnd) {
                    $existingStart = 'appointments.appointment_date';
                    $existingEnd = DB::raw("DATE_ADD(appointments.appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND)");
                    
                    $query->where($existingStart, '<', $proposedEnd->toDateTimeString())
                        ->where($existingEnd, '>', $proposedStart->toDateTimeString())
                        ->where('appointments.status', '!=', 'Cancelled'); // Optional: Ignore cancelled appts in conflict check
                })
                ->count(); 

            if ($conflicts > 0) {   
                $this->addError('conflict', 'This time and duration conflicts with an existing appointment.');
                return;
            }

            // Patient Logic (Keep your existing logic)
            $patient = DB::table('patients')->where('mobile_number', $this->contactNumber)->first();
            $patientId = null;

            if ($patient) {
                $patientId = $patient->id;
                DB::table('patients')->where('id', $patientId)->update([
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'middle_name' => $this->middleName,
                    'birth_date' => $this->birthDate
                ]);
            } else {
                $patientId = DB::table('patients')->insertGetId([
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'middle_name' => $this->middleName,
                    'mobile_number' => $this->contactNumber,
                    'birth_date' => $this->birthDate,
                    'modified_by' => Auth::check() ? Auth::user()->username : 'SYSTEM'
                ]);
            }

            $appointmentDateTime = Carbon::parse($this->appointmentDate)
                ->setTimeFromTimeString($this->selectedTime)
                ->toDateTimeString();

            // 1. Prepare Data
            $apptData = [
                'patient_id' => $patientId, 
                'service_id' => $this->selectedService,
                'appointment_date' => $appointmentDateTime,
                'status' => 'Scheduled',
                'modified_by' => Auth::check() ? Auth::user()->username : 'SYSTEM', 
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 2. Insert and Get ID (Changed from insert to insertGetId)
            $newApptId = DB::table('appointments')->insertGetId($apptData);

            // 3. === LOGGING BLOCK (Appointment Created) ===
            $subject = new Appointment();
            $subject->id = $newApptId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->event('appointment_created')
                ->withProperties(['attributes' => $apptData])
                ->log('Booked New Appointment');
            // ===============================================  
            session()->flash('success', 'Appointment booked successfully!');
            $this->loadAppointments();
            $this->closeAppointmentModal();

        } catch (\Throwable $th) {
            // dd($th);
        }
    }

    public function isSlotOccupied($date, $time)
    {
        // 1. Kunin ang start time ng slot na tinitingnan (e.g., "16:30")
        $slotStart = Carbon::parse($date . ' ' . $time);

        // Convert sa "total minutes from start of day" (e.g., 16:30 -> 16*60 + 30 = 990)
        $slotStartInMinutes = $slotStart->hour * 60 + $slotStart->minute;
        
        // Ang bawat slot ay 30 minutes ang haba
        $slotEndInMinutes = $slotStartInMinutes + 30;

        // 2. Loop sa lahat ng appointments
        foreach ($this->appointments as $appointment) 
        {
            if (Carbon::parse($appointment->start_date)->isSameDay($slotStart)) {

                $existingStart = Carbon::parse($appointment->start_date . ' ' . $appointment->start_time);
                
                // Convert sa "total minutes from start of day"
                $existingStartInMinutes = $existingStart->hour * 60 + $existingStart->minute;
                $existingEndInMinutes = $existingStartInMinutes + $appointment->duration_in_minutes;

                $isOverlapping = (
                    $slotStartInMinutes < $existingEndInMinutes && 
                    $slotEndInMinutes > $existingStartInMinutes
                );

                if ($isOverlapping) {
                    return true; 
                }
            }
        }
        
        return false;
    }

    public function viewAppointment($appointmentId)
    {
        // 1. Fetch the appointment with patient and service details
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
            $this->resetForm(); 

            // 2. Populate the form fields
            $this->firstName = $appointment->first_name;
            $this->lastName = $appointment->last_name;
            $this->middleName = $appointment->middle_name;
            $this->contactNumber = $appointment->mobile_number;
            $this->birthDate = $appointment->birth_date;
            $this->selectedService = $appointment->service_id;
            $this->viewingAppointmentId = $appointment->id;
            $this->appointmentStatus = $appointment->status;   

            // 3. Format Dates and Times
            $dt = Carbon::parse($appointment->appointment_date);
            $this->appointmentDate = $dt->toDateString();  // NEW: Use this
            $this->selectedTime = $dt->format('H:i:s');

            // Calculate End Time for display
            sscanf($appointment->duration, '%d:%d:%d', $h, $m, $s);
            $durationInMinutes = ($h * 60) + $m;
            $this->endTime = $dt->copy()->addMinutes($durationInMinutes)->format('H:i A');

            // 4. Set mode to Viewing and open modal
            $this->isViewing = true;
            $this->showAppointmentModal = true;
        }
    }
    public function updateStatus($newStatus)
    {
        if ($this->viewingAppointmentId) {
            
            // 1. Fetch Old Status (For the log)
            $oldAppt = DB::table('appointments')->where('id', $this->viewingAppointmentId)->first();
            $oldStatus = $oldAppt ? $oldAppt->status : 'Unknown';

            // 2. Perform the Update
            DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(), // Good practice to update timestamp
                ]);

            // 3. === LOGGING BLOCK (Status Changed) ===
            // Only log if the status actually changed
            if ($oldStatus !== $newStatus) {
                $subject = new Appointment();
                $subject->id = $this->viewingAppointmentId;

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subject)
                    ->event('appointment_updated')
                    ->withProperties([
                        'old' => ['status' => $oldStatus],
                        'attributes' => ['status' => $newStatus]
                    ])
                    ->log('Updated Appointment Status');
            }
            // ==========================================

            $this->loadAppointments();
            $this->closeAppointmentModal();
        }
    }

    // --- SEARCH FUNCTIONALITY ---

    // This runs automatically whenever $searchQuery changes (as you type)
    public function updatedSearchQuery()
    {
        // Don't search if empty or too short
        if (strlen($this->searchQuery) < 2) {
            $this->patientSearchResults = [];
            return;
        }

        // Search by First Name, Last Name, or Mobile Number
        $this->patientSearchResults = DB::table('patients')
            ->select('id', 'first_name', 'last_name', 'middle_name', 'mobile_number', 'birth_date') 
            ->orwhere('first_name', 'like', '%' . $this->searchQuery . '%')
            ->orwhere('last_name', 'like', '%' . $this->searchQuery . '%')
            ->limit(10) // Limit results to keep UI clean
            ->get();
    }

    public function selectPatient($patientId)
    {
        $patient = DB::table('patients')->find($patientId);

        if ($patient) {
            // Auto-fill the form
            $this->firstName = $patient->first_name;
            $this->lastName = $patient->last_name;
            $this->middleName = $patient->middle_name;
            $this->contactNumber = $patient->mobile_number;
            $this->birthDate = $patient->birth_date;

            // Optional: If you store record_number in DB, map it here too
            // $this->recordNumber = $patient->record_number; 

            // Clear the search so the dropdown disappears
            $this->searchQuery = '';
            $this->patientSearchResults = [];
        }
    }

    public function goToDate()
    {
        if (!$this->selectedDate) {
            return;
        }

        $this->currentDate = Carbon::parse($this->selectedDate);
        $this->generateWeekDates();
        $this->loadAppointments();
    }

    public function goToToday()
    {
        $this->currentDate = Carbon::now();
        $this->selectedDate = $this->currentDate->format('Y-m-d');
        $this->generateWeekDates();
        $this->loadAppointments();
        
    }

    public function processPatient() 
    {
         if ($this->viewingAppointmentId) {
            $appt = DB::table('appointments')->find($this->viewingAppointmentId);
            if ($appt) {
                // Open Patient Modal (Step 1: Basic Info)
                $this->dispatch('editPatient', id: $appt->patient_id, startStep: 1);
                $this->closeAppointmentModal();
            }
        }
    }

    public function openPatientChart()
    {
        if ($this->viewingAppointmentId) {
            $appt = DB::table('appointments')->find($this->viewingAppointmentId);
            if ($appt) {
                // Open Patient Modal (Step 3: Dental Chart)
                $this->dispatch('editPatient', id: $appt->patient_id, startStep: 3);
                $this->closeAppointmentModal();
            }
        }
    }

    public function admitPatient()
    {
        $appointment = DB::table('appointments')->find($this->viewingAppointmentId);
        $service = DB::table('services')->where('id', $this->selectedService)->first();
        
        if (!$appointment || !$service) return;

        // Use Original Time for conflict check (we don't move the slot, just the status)
        $startTime = Carbon::parse($appointment->appointment_date); 
        sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
        $durationMinutes = ($h * 60) + $m;
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        // Double-check for conflicts (just in case)
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
            // Optional: You can choose to block or just warn. 
            // For now, we allow it but you might want to add an error message property.
            // session()->flash('error', 'Warning: Slot conflict detected.');
        } 

        // Update Status -> Ongoing
        DB::table('appointments')->where('id', $this->viewingAppointmentId)->update([
            'status' => 'Ongoing',
            'updated_at' => now()
        ]);
        
        $this->loadAppointments();
        $this->closeAppointmentModal();
        
        // Immediately open the chart
        $this->dispatch('editPatient', id: $appointment->patient_id, startStep: 3);
    }

    public function render()
    {
        return view('livewire.appointment-calendar');
    }
}