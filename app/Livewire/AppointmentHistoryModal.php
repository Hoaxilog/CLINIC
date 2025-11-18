<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Carbon\Carbon;

class AppointmentHistoryModal extends Component
{
    public $showModal = false;
    public $patient = null;
    public $appointmentHistory = [];

    #[On('open-history-modal')]
    public function loadHistory($patientId)
    {
        // 1. Fetch Patient Basic Info (for the header)
        $this->patient = DB::table('patients')->where('id', $patientId)->first();

        if (!$this->patient) {
            return;
        }

        // 2. Fetch Appointments joined with Services
        $history = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.patient_id', $patientId)
            ->select(
                'appointments.*', 
                'services.service_name',
                'services.duration'
            )
            ->orderBy('appointments.appointment_date', 'desc')
            ->get();
            
        // 3. Transform data (Duration calculation)
        $this->appointmentHistory = $history->map(function($item) {
            sscanf($item->duration, '%d:%d:%d', $h, $m, $s);
            $item->duration_minutes = ($h * 60) + $m;
            return $item;
        });

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset('appointmentHistory', 'patient');
    }

    public function render()
    {
        return view('livewire.appointment-history-modal');
    }
}