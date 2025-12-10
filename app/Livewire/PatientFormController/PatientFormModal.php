<?php

namespace App\Livewire\PatientFormController;
 
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\On;

class PatientFormModal extends Component
{
    // ... (Properties remain the same) ...
    public $showModal = false;
    public $currentStep = 1;
    public $isEditing = false;
    public $isAdmin = false;
    public $isReadOnly = false;
    public $isSaving = false;
    public $forceNewRecord = false;

    public $basicInfoData = [];
    public $healthHistoryData = [];
    public $dentalChartData = []; 
    public $chartHistory = []; 
    public $newPatientId;
    
    public $chartKey = 'initial'; 
    public $selectedHistoryId = '';
    
    // ... (mount/open methods same) ...
    #[On('openAddPatientModal')]
    public function openModal()
    {
        $this->reset(); 
        $this->showModal = true;
        $this->isEditing = false;
        $this->isReadOnly = false; 
        $this->isSaving = false;
        $this->forceNewRecord = false;
        $this->chartKey = uniqid(); 
        
        $user = Auth::user();
        if ($user) {
            $this->isAdmin = ($user->role === 1); 
        }
    }

    #[On('editPatient')]
    public function editPatient($id)
    {
        $this->reset(); 
        $this->isEditing = true;
        $this->newPatientId = $id;
        $this->isReadOnly = true;
        $this->isSaving = false;
        $this->forceNewRecord = false;
        $this->selectedHistoryId = ''; 

        $user = Auth::user();
        if ($user) {
            $this->isAdmin = ($user->role === 1); 
        }

        $patient = DB::table('patients')->where('id', $id)->first();
        $this->basicInfoData = (array) $patient;

        $history = DB::table('health_histories')->where('patient_id', $id)->first();
        $this->healthHistoryData = $history ? (array) $history : [];

        if ($this->isAdmin) {
            $this->loadLatestChart();
            $this->loadHistoryList($id);
        }

        $this->showModal = true;
        $this->currentStep = 1;
    }

    // ... (getMaxStep same) ...
    protected function getMaxStep()
    {
        if (!$this->isEditing) return 2;
        return $this->isAdmin ? 4 : 2;
    }

    public function loadLatestChart()
    {
        $latestChart = DB::table('dental_charts')
            ->where('patient_id', $this->newPatientId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestChart && !empty($latestChart->chart_data)) {
            $this->dentalChartData = json_decode($latestChart->chart_data, true);
            // [FIX] Set the selected history ID to this latest record
            $this->selectedHistoryId = $latestChart->id;
        } else {
            $this->dentalChartData = [];
            $this->selectedHistoryId = '';
        }
        $this->chartKey = uniqid(); 
    }

    // ... (Rest of the file remains exactly the same) ...
    public function loadHistoryList($patientId)
    {
        $this->chartHistory = DB::table('dental_charts')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->select('id', 'created_at')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'date' => Carbon::parse($item->created_at)->format('F j, Y - h:i A')
                ];
            })
            ->toArray();
    }

    #[On('switchChartHistory')]
    public function switchChartHistory($chartId)
    {
        if (empty($chartId)) {
            $this->loadLatestChart();
            return;
        }

        $chart = DB::table('dental_charts')->where('id', $chartId)->first();
        
        if ($chart) {
            $this->isReadOnly = true; 
            $this->selectedHistoryId = $chartId; 
            
            if (!empty($chart->chart_data)) {
                $this->dentalChartData = json_decode($chart->chart_data, true);
            } else {
                $this->dentalChartData = [];
            }
            $this->chartKey = uniqid(); 
        }
    }

    #[On('startNewChartSession')]
    public function startNewChartSession()
    {
        $this->isReadOnly = false; 
        $this->forceNewRecord = true; 
        $this->dentalChartData = []; 
        $this->selectedHistoryId = ''; 
        $this->chartKey = uniqid(); 
    }

    public function enableEditMode()
    {
        $this->isReadOnly = false;
    }

    public function cancelEdit()
    {
        if ($this->isEditing && !$this->isReadOnly) {
            $this->isReadOnly = true;
            if ($this->forceNewRecord) {
                $this->forceNewRecord = false;
                $this->loadLatestChart();
            } else {
                $this->loadLatestChart();
            }
        } else {
            $this->closeModal();
        }
    }

    #[On('basicInfoValidated')]
    public function handleBasicInfoValidated($data)
    {
        $this->basicInfoData = $data;
        if ($this->isSaving) {
            if ($this->isEditing) $this->updatePatientData();
            else $this->savePatientData();
            $this->isSaving = false; 
        } else {
            if ($this->currentStep < $this->getMaxStep()) {
                $this->currentStep = 2;
                $this->dispatch('setGender', gender: $this->basicInfoData['gender'])
                     ->to('PatientFormController.health-history');
            }
        }
    }

    #[On('healthHistoryValidated')]
    public function handleHealthHistoryValidated($data)
    {
        $this->healthHistoryData = $data;
        
        if ($this->isSaving) {
            if ($this->isEditing) $this->updatePatientData();
            else $this->savePatientData();
            $this->isSaving = false;
        } else {
            if ($this->isAdmin && $this->isEditing && $this->currentStep < $this->getMaxStep()) {
                $this->currentStep = 3;
            }
        }
    }

    #[On('dentalChartDataProvided')]
    public function handleDentalChartData($data)
    {
        if ($this->isReadOnly) return;
        $this->dentalChartData = $data;
        if ($this->isEditing) {
            $this->updatePatientData();
        }
    }

    public function nextStep()
    {
        $this->isSaving = false; 
        $maxStep = $this->getMaxStep();

        if ($this->isReadOnly) {
            if ($this->currentStep < $maxStep) {
                $this->currentStep++;
                if ($this->currentStep == 2 && isset($this->basicInfoData['gender'])) {
                    $this->dispatch('setGender', gender: $this->basicInfoData['gender'])
                        ->to('PatientFormController.health-history');
                }
            }
            return;
        }

        if ($this->currentStep < $maxStep) {
            if($this->currentStep == 1) {
                $this->dispatch('validateBasicInfo')->to('PatientFormController.basic-info');
            } 
            elseif ($this->currentStep == 2) {
                if (!$this->isEditing) return;
                $this->dispatch('validateHealthHistory')->to('PatientFormController.health-history');
            }
            elseif ($this->currentStep == 3) {
                $this->currentStep = 4;
            }
        }
    }
    
    public function previousStep()
    {
        $this->currentStep--;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(); 
        $this->currentStep = 1;
    }

    public function save()
    {
        if ($this->isReadOnly) return;
        $this->isSaving = true;

        if ($this->currentStep == 1) {
            $this->dispatch('validateBasicInfo')->to('PatientFormController.basic-info');
        } 
        elseif ($this->currentStep == 2) {
            $this->dispatch('validateHealthHistory')->to('PatientFormController.health-history');
        } 
        elseif ($this->currentStep >= 3 && $this->isAdmin) {
            $this->dispatch('requestDentalChartData')->to('PatientFormController.dental-chart');
        }
    }

    public function savePatientData()
    {
        try {
            DB::transaction(function () {
                $modifier = Auth::check() ? Auth::user()->username : 'SYSTEM';
                $this->basicInfoData['modified_by'] = $modifier;
                
                $this->newPatientId = DB::table('patients')->insertGetId($this->basicInfoData);
                
                $this->healthHistoryData['patient_id'] = $this->newPatientId;
                $this->healthHistoryData['modified_by'] = $modifier;
                DB::table('health_histories')->insert($this->healthHistoryData);
            });

            $this->dispatch('patient-added');
            $this->closeModal();
            
        } catch (\Exception $e) { }
    }

    public function updatePatientData()
    {
        try {
            DB::transaction(function () {
                $modifier = Auth::check() ? Auth::user()->username : 'SYSTEM';

                if ($this->currentStep == 1) {
                    $this->basicInfoData['modified_by'] = $modifier;
                    DB::table('patients')->where('id', $this->newPatientId)->update($this->basicInfoData);
                }
                elseif ($this->currentStep == 2) {
                    $this->healthHistoryData['patient_id'] = $this->newPatientId;
                    $this->healthHistoryData['modified_by'] = $modifier;
                    unset($this->healthHistoryData['id']); 
                    DB::table('health_histories')->updateOrInsert(['patient_id' => $this->newPatientId], $this->healthHistoryData);
                }
                elseif ($this->currentStep == 3 && $this->isAdmin) {
                    if (!empty($this->dentalChartData)) {
                        $shouldInsert = true;
                        if (!$this->forceNewRecord) {
                            $todayStart = Carbon::today();
                            $todayEnd = Carbon::tomorrow();
                            $existingToday = DB::table('dental_charts')
                                ->where('patient_id', $this->newPatientId)
                                ->whereBetween('created_at', [$todayStart, $todayEnd])
                                ->orderBy('created_at', 'desc')
                                ->first();

                            if ($existingToday) {
                                $shouldInsert = false;
                                DB::table('dental_charts')->where('id', $existingToday->id)->update([
                                    'chart_data' => json_encode($this->dentalChartData),
                                    'modified_by' => $modifier,
                                    'updated_at' => now()
                                ]);
                            }
                        }
                        if ($shouldInsert) {
                            DB::table('dental_charts')->insert([
                                'patient_id' => $this->newPatientId,
                                'chart_data' => json_encode($this->dentalChartData),
                                'modified_by' => $modifier,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $this->forceNewRecord = false; 
                        }
                    }
                }
            });

            if ($this->isAdmin) {
                $this->loadHistoryList($this->newPatientId);
            }
            $this->isReadOnly = true; 
            $this->dispatch('patient-added'); 
        } catch (\Exception $e) { }
    }

    public function render()
    {
        return view('livewire.PatientFormViews.patient-form-modal');
    }
}