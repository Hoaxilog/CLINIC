<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonInterval; // <-- Add this at the top

class AppointmentCalendar extends Component
{
    // ... (All other properties and methods are unchanged) ...
    public $currentDate;
    public $viewType = 'week';
    public $weekDates = [];
    public $timeSlots = [];
    public $appointments = [];
    public $showAppointmentModal = false;
    public $servicesList = [];
    public $firstName = '';
    public $lastName = '';
    public $middleName = '';
    public $recordNumber = '';
    public $contactNumber = '';
    public $birthDate = null; // <--- ADD THIS    
    public $selectedService = '';
    public $selectedDate = null;
    public $selectedTime = null;
    public $endTime = null;
    public $isViewing = false; // <-- Add this
    public $viewingAppointmentId = null; // To track which ID we are updating
    public $appointmentStatus = '';      // To track the current status
    public $searchQuery = '';
    public $patientSearchResults = [];

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
        for ($hour = 9; $hour <= 19; $hour++) {
            $this->timeSlots[] = sprintf('%02d:00', $hour);
            if ($hour != 19) {
                $this->timeSlots[] = sprintf('%02d:30', $hour);
            }        
        }
    }

    // ---
    //
    // THIS IS THE ONLY FUNCTION THAT HAS CHANGED
    //
    // ---
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
            ->map(function($appointment) {
                
                // --- THIS IS THE FIX ---
                // Use sscanf to reliably parse the 'HH:MM:SS' string
                sscanf($appointment->duration, '%d:%d:%d', $h, $m, $s);
                $appointment->duration_in_minutes = ($h * 60) + $m;
                // --- END OF FIX ---
                
                $carbonDate = Carbon::parse($appointment->appointment_date);
                $appointment->start_date = $carbonDate->toDateString();
                $appointment->start_time = $carbonDate->format('H:i');
                
                $appointment->end_time = $carbonDate->copy()
                                 ->addMinutes($appointment->duration_in_minutes)
                                 ->format('H:i');
                return $appointment;
            });
    }
    // ---
    //
    // (The rest of your file is correct)
    //
    // ---

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
        $this->birthDate = null; // <--- ADD THIS
        $this->selectedService = '';
        $this->selectedDate = null;
        $this->selectedTime = null;
        $this->endTime = null;
        $this->isViewing = false; // <-- Add this line
        $this->viewingAppointmentId = null;
        $this->appointmentStatus = '';
        $this->searchQuery = '';
        $this->patientSearchResults = [];   
        
    }

    public function openAppointmentModal($date, $time)
    {
        $this->resetForm();
        $this->selectedDate = $date;
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
        $this->validate();

        try {
            $service = $this->servicesList->firstWhere('id', $this->selectedService);
            if (!$service) {
                $this->addError('selectedService', 'Please select a valid service.');
                return;
            }
            sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
            $durationInMinutes = ($h * 60) + $m;

            // 2. Calculate proposed start and end
            $proposedStart = Carbon::parse($this->selectedDate)->setTimeFromTimeString($this->selectedTime);
            $proposedEnd = $proposedStart->copy()->addMinutes($durationInMinutes);

            // 3. Find conflicting appointments using SQL overlap logic
            // An overlap occurs if (StartA < EndB) AND (EndA > StartB)
            $conflicts = DB::table('appointments')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->where(function ($query) use ($proposedStart, $proposedEnd) {
                    
                    // Define existing start and end times using SQL
                    $existingStart = 'appointments.appointment_date';
                    // Use MySQL's TIME_TO_SEC to convert HH:MM:SS duration to seconds
                    $existingEnd = DB::raw("DATE_ADD(appointments.appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND)");
                    
                    $query->where($existingStart, '<', $proposedEnd->toDateTimeString())
                        ->where($existingEnd, '>', $proposedStart->toDateTimeString());
                })
                ->count(); // We just need to know if any conflict exists

            if ($conflicts > 0) {
                // 4. If a conflict exists, add an error and stop.
                $this->addError('conflict', 'This time and duration conflicts with an existing appointment.');
                return;
            }
            $patient = DB::table('patients')
                        ->where('mobile_number', $this->contactNumber)
                        ->first();

            $patientId = null;

            if ($patient) {
                $patientId = $patient->id;
                DB::table('patients')
                    ->where('id', $patientId)
                    ->update([
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
                    'modified_by' => 'SYSTEM'
                ]);
            }

            $appointmentDateTime = Carbon::parse($this->selectedDate)
                ->setTimeFromTimeString($this->selectedTime)
                ->toDateTimeString();

            DB::table('appointments')->insert([
                'patient_id' => $patientId, 
                'service_id' => $this->selectedService,
                'appointment_date' => $appointmentDateTime,
                'status' => 'Scheduled',
                'modified_by' => 'SYSTEM', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->loadAppointments();
            $this->closeAppointmentModal();

        } catch (\Throwable $th) {
            // dd($th); // You can uncomment this to debug if it fails
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
            // Check muna kung 'yung date ay 'yun din
            if (Carbon::parse($appointment->start_date)->isSameDay($slotStart)) {

                // 3. Kunin ang start at end ng existing booking
                $existingStart = Carbon::parse($appointment->start_date . ' ' . $appointment->start_time);
                
                // Convert sa "total minutes from start of day"
                $existingStartInMinutes = $existingStart->hour * 60 + $existingStart->minute;
                $existingEndInMinutes = $existingStartInMinutes + $appointment->duration_in_minutes;

                // 4. Ang Pinal na Overlap Logic (using numbers)
                // (StartA < EndB) AND (EndA > StartB)
                // (slotStart < existingEnd) AND (slotEnd > existingStart)

                $isOverlapping = (
                    $slotStartInMinutes < $existingEndInMinutes && 
                    $slotEndInMinutes > $existingStartInMinutes
                );

                if ($isOverlapping) {
                    return true; // May overlap! I-disable ang slot.
                }
            }
        }
        
        return false; // Walang overlap, hayaang clickable.
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
                'patients.birth_date', // <--- ADD THIS (Select the column)
                'services.service_name',
                'services.duration'
            )
            ->where('appointments.id', $appointmentId)
            ->first();

        if ($appointment) {
            $this->resetForm(); // Clear old data

            // 2. Populate the form fields
            $this->firstName = $appointment->first_name;
            $this->lastName = $appointment->last_name;
            $this->middleName = $appointment->middle_name;
            $this->contactNumber = $appointment->mobile_number;
            $this->selectedService = $appointment->service_id;
            $this->viewingAppointmentId = $appointment->id;    // <--- NEW
            $this->appointmentStatus = $appointment->status;   // <--- NEW

            // 3. Format Dates and Times
            $dt = Carbon::parse($appointment->appointment_date);
            $this->selectedDate = $dt->toDateString();
            $this->selectedTime = $dt->format('H:i:s'); // Use H:i:s to match time inputs usually

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
            DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->update([
                    'status' => $newStatus,
                ]);

            // Refresh the calendar to show changes (e.g. color coding if you add it later)
            $this->loadAppointments();

            // Close the modal
            $this->closeAppointmentModal();

            // Optional: Flash a success message
            // session()->flash('message', "Appointment marked as $newStatus");
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
            ->select('id', 'first_name', 'last_name', 'middle_name', 'mobile_number', 'birth_date') // Select birthdate
            ->where('first_name', 'like', '%' . $this->searchQuery . '%')
            ->limit(5) // Limit results to keep UI clean
            ->get();
    }

    // This runs when you click a name from the dropdown
    public function selectPatient($patientId)
    {
        $patient = DB::table('patients')->find($patientId);

        if ($patient) {
            // Auto-fill the form
            $this->firstName = $patient->first_name;
            $this->lastName = $patient->last_name;
            $this->middleName = $patient->middle_name;
            $this->contactNumber = $patient->mobile_number;
            $this->birthDate = $patient->birth_date; // <--- ADD THIS

            // Optional: If you store record_number in DB, map it here too
            // $this->recordNumber = $patient->record_number; 

            // Clear the search so the dropdown disappears
            $this->searchQuery = '';
            $this->patientSearchResults = [];
        }
    }

    public function render()
    {
        return view('livewire.appointment-calendar');
    }
}