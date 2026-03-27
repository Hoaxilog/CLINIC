<?php

namespace App\Livewire\Patient;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;


class PatientRecords extends Component
{
    use WithPagination;

    public $search = '';
    public $sortOption = 'recent';
    public $selectedPatient;
    public $lastVisit;
    public $viewMode = 'cards';
    public $showProfile = false;
    public $treatmentRecords = [];

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        //
    }

    public function selectPatient($patientId)
    {
        if (Auth::check() && Auth::user()->role === 3) {
            return;
        }

        $this->selectedPatient = DB::table('patients')->where('id', $patientId)->first();

        $this->lastVisit = DB::table('appointments')
                            ->where('patient_id', $patientId)
                            ->where('status', 'Completed')
                            ->orderBy('appointment_date', 'desc')
                            ->first();

        $rawRecords = DB::table('treatment_records')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $recordIds = $rawRecords->pluck('id')->filter()->values();
        $allImages = $recordIds->isNotEmpty()
            ? DB::table('treatment_record_images')
                ->whereIn('treatment_record_id', $recordIds->all())
                ->orderBy('sort_order')
                ->get()
                ->groupBy('treatment_record_id')
            : collect();

        $this->treatmentRecords = $rawRecords->map(function ($record) use ($allImages) {
            $record->image_list = $allImages->get($record->id, collect())
                ->map(function ($image) {    
                    return (array) $image;
                })
                ->values()
                ->toArray();

            return $record;
        });

        $latestStatus = DB::table('appointments')
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'desc')
            ->value('status');

        $lastAppointmentAt = DB::table('appointments')
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'desc')
            ->value('appointment_date');

        $activeCutoff = Carbon::now()->subYears(2);

        if ($this->selectedPatient) {
            $this->selectedPatient->last_completed_at = $this->lastVisit?->appointment_date;
            $this->selectedPatient->latest_status = $latestStatus;
            $this->selectedPatient->patient_type = $lastAppointmentAt && Carbon::parse($lastAppointmentAt)->gte($activeCutoff)
                ? 'Active'
                : 'Inactive';
        }
    }

    public function openProfile($patientId)
    {
        $this->selectPatient($patientId);
        $this->showProfile = true;
    }

    public function backToList()
    {
        $this->showProfile = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->syncSelectionAfterDatasetChange();
    }
    
    public function setSort($option)
    {
        $this->sortOption = $option;
        $this->resetPage();
        $this->syncSelectionAfterDatasetChange();
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
            $query->whereRaw('1 = 0');
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

    protected function syncSelectionAfterDatasetChange(): void
    {
        if (! $this->showProfile) {
            return;
        }

        if (! $this->selectedPatient?->id) {
            return;
        }

        $existsInQuery = (clone $this->getPatientsQuery())
            ->where('id', $this->selectedPatient->id)
            ->exists();

        if ($existsInQuery) {
            $this->selectPatient($this->selectedPatient->id);
            return;
        }

        $this->selectedPatient = null;
        $this->lastVisit = null;
        $this->treatmentRecords = [];

        if ($this->showProfile) {
            $this->showProfile = false;
        }
    }

    public function deletePatient($id)
    {
        if (Auth::check() && Auth::user()->role === 3) {
            $this->dispatch('flash-message', type: 'error', message: 'You do not have permission to delete patient records.');
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
            $this->dispatch('flash-message', type: 'success', message: 'Patient deleted successfully.');
        }
    }

    public function render()
    {
        $patients = $this->getPatientsQuery()->paginate(21);
        $patientIds = $patients->getCollection()->pluck('id')->filter()->values();

        $lastCompletedMap = collect();
        $latestStatusMap = collect();
        $lastAppointmentMap = collect();

        if ($patientIds->isNotEmpty()) {
            $lastCompletedMap = DB::table('appointments')
                ->select('patient_id', DB::raw('MAX(appointment_date) as last_completed_at'))
                ->whereIn('patient_id', $patientIds->all())
                ->where('status', 'Completed')
                ->groupBy('patient_id')
                ->get()
                ->keyBy('patient_id');

            $latestSub = DB::table('appointments')
                ->select('patient_id', DB::raw('MAX(appointment_date) as last_appointment_at'))
                ->whereIn('patient_id', $patientIds->all())
                ->groupBy('patient_id');

            $lastAppointmentMap = (clone $latestSub)
                ->get()
                ->keyBy('patient_id');

            $latestStatusMap = DB::table('appointments as a')
                ->joinSub($latestSub, 'latest', function ($join) {
                    $join->on('a.patient_id', '=', 'latest.patient_id')
                        ->on('a.appointment_date', '=', 'latest.last_appointment_at');
                })
                ->select('a.patient_id', 'a.status')
                ->get()
                ->groupBy('patient_id')
                ->map(function ($rows) {
                    return $rows->first()->status ?? null;
                });
        }

        $patients->setCollection(
            $patients->getCollection()->map(function ($patient) use ($lastCompletedMap, $latestStatusMap, $lastAppointmentMap) {
                $activeCutoff = Carbon::now()->subYears(2);
                $patient->last_completed_at = $lastCompletedMap[$patient->id]->last_completed_at ?? null;
                $latestStatus = $latestStatusMap[$patient->id] ?? null;
                $patient->latest_status = $latestStatus;
                $lastAppointmentAt = $lastAppointmentMap[$patient->id]->last_appointment_at ?? null;
                $patient->patient_type = $lastAppointmentAt && Carbon::parse($lastAppointmentAt)->gte($activeCutoff)
                    ? 'Active'
                    : 'Inactive';
                return $patient;
            })
        );

        return view('livewire.patient.patient-records', compact('patients'));
    }

}
