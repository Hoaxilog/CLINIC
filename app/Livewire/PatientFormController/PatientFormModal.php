<?php

namespace App\Livewire\PatientFormController;
 
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\On;

class PatientFormModal extends Component
{
    // ... (Existing Properties) ...
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
    public $treatmentRecordData = []; 
    public $chartHistory = []; 
    public $newPatientId;
    
    // Track the current Dental Chart ID to link Treatment Record
    public $currentDentalChartId = null;

    public $chartKey = 'initial'; 
    public $selectedHistoryId = '';
    
    #[On('openAddPatientModal')]
    public function openModal()
    {
        $this->reset(); 
        $this->showModal = true;
        $this->isEditing = false;
        $this->isReadOnly = false; 
        $this->isSaving = false;
        $this->forceNewRecord = false;
        $this->currentDentalChartId = null; 
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
        $this->currentDentalChartId = null;

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
            $this->currentDentalChartId = $latestChart->id; 
            
            // Load the treatment record linked to THIS chart
            $this->loadTreatmentRecordForChart($latestChart->id);
        } else {
            $this->dentalChartData = [];
            $this->currentDentalChartId = null;
            $this->treatmentRecordData = [];
        }
        $this->selectedHistoryId = ''; 
        $this->chartKey = uniqid(); 
    }

    public function loadTreatmentRecordForChart($chartId)
    {
        $record = DB::table('treatment_records')
            ->where('dental_chart_id', $chartId)
            ->first();

        if ($record) {
            $this->treatmentRecordData = (array) $record;
        } else {
            $this->treatmentRecordData = [];
        }
    }

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
            $this->currentDentalChartId = $chartId;
            
            if (!empty($chart->chart_data)) {
                $this->dentalChartData = json_decode($chart->chart_data, true);
            } else {
                $this->dentalChartData = [];
            }
            
            // Load connected treatment record
            $this->loadTreatmentRecordForChart($chartId);
            
            $this->chartKey = uniqid(); 
        }
    }

    #[On('startNewChartSession')]
    public function startNewChartSession()
    {
        $this->isReadOnly = false; 
        $this->forceNewRecord = true; 
        $this->dentalChartData = []; 
        $this->currentDentalChartId = null; 
        $this->selectedHistoryId = ''; 
        $this->treatmentRecordData = []; 
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
        
        // [MODIFIED] Only save if explicitly requested. 
        // If navigating Next, just update state and move step.
        if ($this->isSaving) {
            if ($this->isEditing) $this->updatePatientData();
        } elseif ($this->currentStep == 3) {
            // Move to Treatment Record
            $this->currentStep = 4;
        }
    }

    #[On('treatmentRecordValidated')]
    public function handleTreatmentRecordValidated($data)
    {
        $this->treatmentRecordData = $data;
        if ($this->isSaving && $this->isEditing) {
            $this->updatePatientData();
        }
        $this->isSaving = false;
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
                // [MODIFIED] Request data but DO NOT save yet. 
                // The handler will move us to Step 4.
                $this->dispatch('requestDentalChartData')->to('PatientFormController.dental-chart');
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
        elseif ($this->currentStep == 3 && $this->isAdmin) {
            // This case might not be reached if button is hidden, but kept for safety
            $this->dispatch('requestDentalChartData')->to('PatientFormController.dental-chart');
        }
        elseif ($this->currentStep == 4 && $this->isAdmin) {
            $this->dispatch('validateTreatmentRecord')->to('PatientFormController.treatment-record');
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

    private function saveDentalChart() 
    {
        $modifier = Auth::check() ? Auth::user()->username : 'SYSTEM';

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
                    $this->currentDentalChartId = $existingToday->id;
                }
            }

            if ($shouldInsert) {
                $this->currentDentalChartId = DB::table('dental_charts')->insertGetId([
                    'patient_id' => $this->newPatientId,
                    'chart_data' => json_encode($this->dentalChartData),
                    'modified_by' => $modifier,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->forceNewRecord = false; 
            }
        }
        return $this->currentDentalChartId;
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
                   $this->saveDentalChart();
                }
                elseif ($this->currentStep == 4 && $this->isAdmin) {
                    // Save Chart FIRST to get ID
                    $chartId = $this->saveDentalChart();

                    // Then Save Treatment Record linked to that ID
                    if ($chartId && !empty($this->treatmentRecordData)) {
                        DB::table('treatment_records')->updateOrInsert(
                            ['dental_chart_id' => $chartId], 
                            [
                                'patient_id' => $this->newPatientId,
                                'dmd' => $this->treatmentRecordData['dmd'] ?? null,
                                'treatment' => $this->treatmentRecordData['treatment'] ?? null,
                                'cost_of_treatment' => $this->treatmentRecordData['cost_of_treatment'] ?? null,
                                'amount_charged' => $this->treatmentRecordData['amount_charged'] ?? null,
                                'remarks' => $this->treatmentRecordData['remarks'] ?? null,
                                'image' => $this->treatmentRecordData['image'] ?? null,
                                'modified_by' => $modifier,
                                'updated_at' => now(),
                            ]
                        );
                    }
                }
            });

            if ($this->isAdmin) {
                $this->loadHistoryList($this->newPatientId);
                if ($this->currentStep == 4 && $this->currentDentalChartId) {
                    $this->loadTreatmentRecordForChart($this->currentDentalChartId);
                }
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