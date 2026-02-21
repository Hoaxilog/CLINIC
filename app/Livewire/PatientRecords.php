<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;


class PatientRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $sortOption = 'recent';
    public $selectedPatient;
    public $lastVisit;
    public $viewMode = 'table';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->fetchFirstPatient();
    }

    public function selectPatient($patientId)
    {
        if (Auth::check() && Auth::user()->role === 3) {
            $patientId = DB::table('patients')
                ->where('id', $patientId)
                ->where('email_address', Auth::user()?->email)
                ->value('id');

            if (!$patientId) {
                return;
            }
        }

        $this->selectedPatient = DB::table('patients')->where('id', $patientId)->first();

        $this->lastVisit = DB::table('appointments')
                            ->where('patient_id', $patientId)
                            ->where('status', 'Completed')
                            ->orderBy('appointment_date', 'desc')
                            ->first();
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->fetchFirstPatient(); 
    }
    
    public function setSort($option)
    {
        $this->sortOption = $option;
        $this->resetPage();
        $this->fetchFirstPatient(); 
        $this->dispatch('closeSortDropdown');

    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    protected function getPatientsQuery()
    {
        $query = DB::table('patients');

        if (Auth::check() && Auth::user()->role === 3) {
            $query->where('email_address', Auth::user()?->email);
            return $query;
        }

        if (!empty($this->search)) {
            $query->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $this->search . '%');;
        }

        switch ($this->sortOption) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'a_z':
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                break;
            case 'z_a':
                $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    protected function fetchFirstPatient()
    {
        $patient = $this->getPatientsQuery()->first();
        
        if ($patient) {
            $this->selectPatient($patient->id);
        } else {
            $this->selectedPatient = null;
            $this->lastVisit = null;
        }
    }

    public function deletePatient($id)
    {
        if (Auth::check() && Auth::user()->role === 3) {
            session()->flash('error', 'You do not have permission to delete patient records.');
            return;
        }

        $patient = DB::table('patients')->where('id', $id)->first();

        if ($patient) {
            $patientSubject = new Patient();
            $patientSubject->id = $id;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($patientSubject)
                ->event('patient_deleted')
                ->withProperties([
                    'old' => (array) $patient,
                    'attributes' => [
                        'first_name' => $patient->first_name ?? null,
                        'last_name' => $patient->last_name ?? null,
                        'middle_name' => $patient->middle_name ?? null,
                        'mobile_number' => $patient->mobile_number ?? null,
                    ],
                ])
                ->log('Deleted Patient');

            DB::table('patients')->where('id', $id)->delete();

            // Reset selection if the deleted patient was currently being viewed
            if ($this->selectedPatient && $this->selectedPatient->id == $id) {
                $this->selectedPatient = null;
                $this->lastVisit = null;
            }
            session()->flash('error', 'Patient deleted successfully.');
        }
    }

    public function render()
    {
        return view('livewire.patient-records', [
            'patients' => $this->getPatientsQuery()->paginate(10),
        ]);
    }
}
