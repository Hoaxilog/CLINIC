<?php
namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class DentalChart extends Component
{
    public $teeth = []; 
    
    #[Reactive]
    public $isReadOnly = false; 

    #[Reactive]
    public $history = []; 
    
    public $selectedHistoryId = '';
    
    #[Reactive]
    public $isCreating = false;

    public $oralExam = [
        'oral_hygiene_status' => '', 
        'gingiva' => '', 
        'calcular_deposits' => '',
        'stains' => '', 
        'complete_denture' => '', 
        'partial_denture' => ''
    ];

    public $chartComments = [
        'notes' => '',
        'treatment_plan' => ''
    ];


    public function mount($data = [], $isReadOnly = false, $history = [], $selectedHistoryId = '', $isCreating = false)
    {
        $this->isReadOnly = $isReadOnly;
        $this->history = $history; 
        $this->selectedHistoryId = $selectedHistoryId; 
        $this->isCreating = $isCreating;

        if (!empty($data)) {
            if(isset($data['teeth'])) {
                $this->teeth = $data['teeth'];
                $this->oralExam = array_merge($this->oralExam, $data['oral_exam'] ?? []);
                $this->chartComments = array_merge($this->chartComments, $data['comments'] ?? []);
            } else {
                $this->teeth = $data;
            }
        }
    }

    // --- [ADDED] VALIDATION RULES ---
    protected $rules = [
        'oralExam.oral_hygiene_status' => 'required',
        'oralExam.gingiva' => 'required',
        'oralExam.calcular_deposits' => 'required',
        'oralExam.stains' => 'required',
        'oralExam.complete_denture' => 'required',
        'oralExam.partial_denture' => 'required',
    ];

    protected $validationAttributes = [
        'oralExam.oral_hygiene_status' => 'Oral Hygiene Status',
        'oralExam.gingiva' => 'Gingiva',
        'oralExam.calcular_deposits' => 'Calcular Deposits',
        'oralExam.stains' => 'Stains',
        'oralExam.complete_denture' => 'Complete Denture',
        'oralExam.partial_denture' => 'Partial Denture',
    ];

    public function updatedSelectedHistoryId($value)
    {
        $this->dispatch('switchChartHistory', chartId: $value);
    }

    public function triggerNewChart()
    {
        $this->selectedHistoryId = ''; 
        $this->dispatch('startNewChartSession'); 
    }

    

    #[On('requestDentalChartData')]
    public function provideData()
    {
        $this->validate(); 

        if (!(count($this->history) > 1 || $this->isCreating)) {
            $fullData = [
                'teeth' => $this->teeth,
                'oral_exam' => $this->oralExam,
                'comments' => $this->chartComments
            ];

            $this->dispatch('dentalChartDataProvided', data: $fullData);
            return;
        }

        $this->dispatch('request-dental-chart-teeth');
    }

    #[On('dentalChartTeethProvided')]
    public function handleTeethProvided($teeth)
    {
        $fullData = [
            'teeth' => $teeth,
            'oral_exam' => $this->oralExam,
            'comments' => $this->chartComments
        ];

        $this->dispatch('dentalChartDataProvided', data: $fullData);
    }

    public function render() {
        return view('livewire.PatientFormViews.dental-chart');
    }
}

