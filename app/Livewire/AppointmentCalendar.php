<?php

namespace App\Livewire;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Livewire\Component;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
    /** @var \Illuminate\Support\Collection */
    public $occupiedAppointments = [];
    public $showAppointmentModal = false;
    /** @var \Illuminate\Support\Collection */
    public $servicesList = [];
    public $firstName = '';
    public $lastName = '';
    public $middleName = '';
    public $recordNumber = '';
    public $contactNumber = '';
    public $birthDate = null;  
    public $age = '';
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
    public $activeTab = 'calendar';
    public $prefillPatientId = null;
    public $prefillPatientLabel = null;

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

        $this->activeTab = 'calendar';

        $this->generateWeekDates(); 
        $this->generateTimeSlots();
        $this->loadAppointments();
        $this->servicesList = DB::table('services')->get();

        $prefillId = request()->query('patient_id');
        if (!empty($prefillId)) {
            $patient = DB::table('patients')->find($prefillId);
            if ($patient) {
                $this->prefillPatientId = (int) $prefillId;
                $this->prefillPatientLabel = trim(
                    ($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '')
                );
            }
        }
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
        for ($hour = 9; $hour <= 20; $hour++) {
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

        $baseQuery = DB::table('appointments')
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
            );

        // For display: hide Pending, show everything else except Cancelled
        $this->appointments = (clone $baseQuery)
            ->where('appointments.status', '!=', 'Cancelled')
            ->where('appointments.status', '!=', 'Pending')
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

        // For occupancy: count Pending toward capacity (exclude Cancelled/Completed only)
        $this->occupiedAppointments = (clone $baseQuery)
            ->whereNotIn('appointments.status', ['Cancelled', 'Completed'])
            ->get()
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

        if ($this->prefillPatientId) {
            $this->selectPatient($this->prefillPatientId);
        }
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
    
        // 1. Validation: This will STOP everything if 'birthDate' is empty!
        $this->validate([
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'contactNumber' => 'required|string|max:20',
            'selectedService' => 'required',
            'selectedDate' => 'required',
            'selectedTime' => 'required',
            'birthDate' => 'required',
        ]);

        try {
            // 2. FIX: Wrap servicesList in collect() to prevent the "500 Error" crash
            $service = collect($this->servicesList)->firstWhere('id', $this->selectedService);
            
            if (!$service) {
                $this->addError('selectedService', 'Please select a valid service.');
                return;
            }
            
            sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
            $durationInMinutes = ($h * 60) + $m;

            $proposedStart = Carbon::parse($this->appointmentDate)->setTimeFromTimeString($this->selectedTime);
            $proposedEnd = $proposedStart->copy()->addMinutes($durationInMinutes);
            
            // Conflict Check
            $conflicts = DB::table('appointments')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->where(function ($query) use ($proposedStart, $proposedEnd) {
                    $existingStart = 'appointments.appointment_date';
                    $existingEnd = DB::raw("DATE_ADD(appointments.appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND)");
                    
                    $query->where($existingStart, '<', $proposedEnd->toDateTimeString())
                        ->where($existingEnd, '>', $proposedStart->toDateTimeString())
                        ->where('appointments.status', '!=', 'Cancelled');
                })
                ->count(); 

            if ($conflicts > 0) {
                $this->addError('conflict', 'This time and duration conflicts with an existing appointment.');
                return;
            }

            // Patient Logic
            $patient = DB::table('patients')->where('mobile_number', $this->contactNumber)->first();
            $patientId = null;
            $normalizedBirthDate = $this->normalizeBirthDate($this->birthDate);

            if ($patient) {
                $patientId = $patient->id;
                $oldPatient = (array) $patient;
                $patientUpdates = [
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'middle_name' => $this->middleName,
                    'birth_date' => $normalizedBirthDate
                ];

                DB::table('patients')->where('id', $patientId)->update($patientUpdates);

                $hasChanges = (
                    $oldPatient['first_name'] !== $patientUpdates['first_name'] ||
                    $oldPatient['last_name'] !== $patientUpdates['last_name'] ||
                    ($oldPatient['middle_name'] ?? null) !== $patientUpdates['middle_name'] ||
                    $oldPatient['birth_date'] !== $patientUpdates['birth_date']
                );

                if ($hasChanges) {
                    $patientSubject = new Patient();
                    $patientSubject->id = $patientId;

                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($patientSubject)
                        ->event('patient_updated')
                        ->withProperties([
                            'old' => [
                                'first_name' => $oldPatient['first_name'],
                                'last_name' => $oldPatient['last_name'],
                                'middle_name' => $oldPatient['middle_name'] ?? null,
                                'birth_date' => $oldPatient['birth_date'],
                            ],
                            'attributes' => $patientUpdates,
                        ])
                        ->log('Updated Patient');
                }
            } else {
                $patientId = DB::table('patients')->insertGetId([
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'middle_name' => $this->middleName,
                    'mobile_number' => $this->contactNumber,
                    'birth_date' => $normalizedBirthDate,
                    'modified_by' => Auth::check() ? Auth::user()->username : 'SYSTEM'
                ]);

                $patientSubject = new Patient();
                $patientSubject->id = $patientId;

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($patientSubject)
                    ->event('patient_created')
                    ->withProperties([
                        'attributes' => [
                            'first_name' => $this->firstName,
                            'last_name' => $this->lastName,
                            'middle_name' => $this->middleName,
                            'mobile_number' => $this->contactNumber,
                            'birth_date' => $normalizedBirthDate,
                        ],
                    ])
                    ->log('Created Patient');
            }

            $appointmentDateTime = Carbon::parse($this->appointmentDate)
                ->setTimeFromTimeString($this->selectedTime)
                ->toDateTimeString();

            // Insert Appointment
            $newApptId = DB::table('appointments')->insertGetId([
                'patient_id' => $patientId, 
                'service_id' => $this->selectedService,
                'appointment_date' => $appointmentDateTime,
                'status' => 'Scheduled',
                'modified_by' => Auth::check() ? Auth::user()->username : 'SYSTEM', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $appointmentSubject = new Appointment();
            $appointmentSubject->id = $newApptId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($appointmentSubject)
                ->event('appointment_created')
                ->withProperties([
                    'attributes' => [
                        'patient_id' => $patientId,
                        'patient_name' => trim("{$this->lastName}, {$this->firstName} {$this->middleName}"),
                        'service_id' => $this->selectedService,
                        'appointment_date' => $appointmentDateTime,
                        'status' => 'Scheduled',
                    ],
                ])
                ->log('Created Appointment');

            // Success!
            session()->flash('success', 'Appointment booked successfully!'); 

            $this->loadAppointments();
            $this->closeAppointmentModal();

        } catch (\Throwable $th) {
            // 3. FIX: Show the error instead of hiding it!
            session()->flash('error', 'Error saving: ' . $th->getMessage());
        }
    }

    public function validateAppointmentForConfirm()
    {
        $this->validate([
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'contactNumber' => 'required|string|max:20',
            'selectedService' => 'required',
            'selectedDate' => 'required',
            'selectedTime' => 'required',
            'birthDate' => 'required',
        ]);

        return true;
    }

    protected function normalizeBirthDate(?string $value): string
    {
        if (!is_string($value)) {
            throw new InvalidFormatException('Birth date is required.');
        }

        $value = trim($value);
        if ($value === '') {
            throw new InvalidFormatException('Birth date is required.');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new InvalidFormatException('Birth date is not a valid date.');
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
        foreach ($this->occupiedAppointments as $appointment) 
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

    // public function updatedBirthDate($value)
    // {
    //     if ($value) {
    //         $this->age = Carbon::parse($value)->age;
    //     } else {
    //         $this->age = '';
    //     }
    // }
    
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

            $this->sendStatusEmail($this->viewingAppointmentId, $newStatus);

            $this->loadAppointments();
            $this->closeAppointmentModal();
        }
    }

    public function setActiveTab($tab)
    {
        if ($tab === 'pending' && Auth::user()?->role === 3) {
            $this->activeTab = 'calendar';
            return;
        }
        $this->activeTab = $tab;
    }

    public function getPendingApprovals()
    {
        if (Auth::user()?->role === 3) {
            return collect();
        }

        return DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'Pending')
            ->orderBy('appointments.appointment_date', 'asc')
            ->select(
                'appointments.id',
                'appointments.appointment_date',
                'appointments.status',
                'patients.first_name',
                'patients.last_name',
                'patients.mobile_number',
                'patients.email_address',
                'services.service_name'
            )
            ->get();
    }

    public function approveAppointment($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $this->updateAppointmentStatusById($appointmentId, 'Scheduled');
    }

    public function rejectAppointment($appointmentId)
    {
        if (Auth::user()?->role === 3) {
            return;
        }

        $this->updateAppointmentStatusById($appointmentId, 'Cancelled');
    }

    protected function updateAppointmentStatusById($appointmentId, $newStatus)
    {
        $oldAppt = DB::table('appointments')->where('id', $appointmentId)->first();
        if (!$oldAppt) {
            return;
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
                    'attributes' => ['status' => $newStatus]
                ])
                ->log('Updated Appointment Status');
        }

        $this->sendStatusEmail($appointmentId, $newStatus);

        $this->loadAppointments();
    }

    protected function sendStatusEmail($appointmentId, $newStatus)
    {
        $appointment = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select(
                'appointments.appointment_date',
                'patients.first_name',
                'patients.last_name',
                'patients.email_address',
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

    public function clearPrefill()
    {
        $this->prefillPatientId = null;
        $this->prefillPatientLabel = null;
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

        // Use Original Time
        $startTime = Carbon::parse($appointment->appointment_date); 
        
        sscanf($service->duration, '%d:%d:%d', $h, $m, $s);
        $durationMinutes = ($h * 60) + $m;
        $endTime = $startTime->copy()->addMinutes($durationMinutes);

        // Conflict Check
        $dentistId = Auth::id();
        $hasConflict = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $this->viewingAppointmentId)
            ->where('appointments.status', 'Ongoing')
            ->where('appointments.dentist_id', $dentistId)
            ->whereDate('appointment_date', $startTime->toDateString())
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('appointment_date', '<', $endTime)
                      ->whereRaw("DATE_ADD(appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND) > ?", [$startTime]);
            })
            ->exists();

        if ($hasConflict) {
            session()->flash('error', 'Cannot admit: This slot is double-booked.');
        } else {
            $oldAppointment = $appointment;
            // === FIX IS HERE ===
            $updated = DB::table('appointments')
                ->where('id', $this->viewingAppointmentId)
                ->where('status', 'Waiting')
                ->update([
                    'status' => 'Ongoing',
                    'service_id' => $this->selectedService,
                    'dentist_id' => $dentistId, // <--- ADD THIS LINE TO CLAIM THE PATIENT
                    'updated_at' => now()
                ]);

            if (!$updated) {
                session()->flash('error', 'This appointment was already admitted or updated. Please refresh.');
                return;
            }
            // ===================

            $appointmentSubject = new Appointment();
            $appointmentSubject->id = $this->viewingAppointmentId;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($appointmentSubject)
                ->event('appointment_admitted')
                ->withProperties([
                    'old' => [
                        'status' => $oldAppointment->status ?? null,
                        'service_id' => $oldAppointment->service_id ?? null,
                        'dentist_id' => $oldAppointment->dentist_id ?? null,
                    ],
                    'attributes' => [
                        'status' => 'Ongoing',
                        'service_id' => $this->selectedService,
                        'dentist_id' => Auth::id(),
                    ],
                ])
                ->log('Admitted Appointment');
            
            $this->loadDashboardData();
            $this->closeAppointmentModal();
            $this->dispatch('editPatient', id: $appointment->patient_id, startStep: 3);
        }
    }

    public function render()
    {
        return view('livewire.appointment-calendar');
    }
}
