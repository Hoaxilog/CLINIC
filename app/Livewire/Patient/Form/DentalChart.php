<?php
namespace App\Livewire\Patient\Form;

use App\Livewire\Patient\Form\Concerns\SanitizesPatientFormInput;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Illuminate\Validation\ValidationException;

class DentalChart extends Component
{
    use SanitizesPatientFormInput;

    private const ORAL_EXAM_OPTIONS = [
        'oral_hygiene_status' => ['Excellent', 'Good', 'Fair', 'Poor', 'Bad'],
        'gingiva' => ['Healthy', 'Mildly Inflamed', 'Severe Inflamed'],
        'calcular_deposits' => ['None', 'Slight', 'Moderate', 'Severe'],
        'stains' => ['None', 'Slight', 'Moderate', 'Severe'],
        'complete_denture' => ['None', 'Upper', 'Lower', 'Upper & Lower'],
        'partial_denture' => ['None', 'Upper', 'Lower', 'Upper & Lower'],
    ];

    public $teeth = []; 
    public $dentitionType = 'adult';
    public $numberingSystem = 'FDI';
    public $patientAge = null;
    
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

    public function getHasChartDataProperty(): bool
    {
        if (filled($this->selectedHistoryId)) {
            return true;
        }

        if (! empty($this->teeth)) {
            return true;
        }

        foreach ($this->oralExam as $value) {
            if (filled($value)) {
                return true;
            }
        }

        foreach ($this->chartComments as $value) {
            if (filled($value)) {
                return true;
            }
        }

        return false;
    }

    public function getShouldShowChartProperty(): bool
    {
        return count($this->history) > 0 || $this->isCreating || ! $this->isReadOnly || $this->hasChartData;
    }


    public function mount($data = [], $isReadOnly = false, $history = [], $selectedHistoryId = '', $isCreating = false, $patientAge = null)
    {
        $this->isReadOnly = $isReadOnly;
        $this->history = $history; 
        $this->selectedHistoryId = $selectedHistoryId; 
        $this->isCreating = $isCreating;
        $this->patientAge = is_numeric($patientAge) ? (int) $patientAge : null;

        if (!empty($data)) {
            if(isset($data['teeth'])) {
                $this->teeth = $data['teeth'];
                $this->oralExam = array_merge($this->oralExam, $data['oral_exam'] ?? []);
                $this->chartComments = array_merge($this->chartComments, $data['comments'] ?? []);
                $meta = $data['meta'] ?? [];
                $this->numberingSystem = $meta['numbering_system'] ?? 'FDI';
                // Backward-compatible default for legacy chart_data without meta.
                $this->dentitionType = $this->normalizeDentitionType($meta['dentition_type'] ?? 'adult');
            } else {
                $this->teeth = $data;
            }
        } else {
            $this->dentitionType = $this->defaultDentitionTypeFromAge();
        }

        $this->sanitizeFormData();
    }

    // --- [ADDED] VALIDATION RULES ---
    protected $rules = [
        'oralExam.oral_hygiene_status' => 'required|in:Excellent,Good,Fair,Poor,Bad',
        'oralExam.gingiva' => 'required|in:Healthy,Mildly Inflamed,Severe Inflamed',
        'oralExam.calcular_deposits' => 'required|in:None,Slight,Moderate,Severe',
        'oralExam.stains' => 'required|in:None,Slight,Moderate,Severe',
        'oralExam.complete_denture' => 'required|in:None,Upper,Lower,Upper & Lower',
        'oralExam.partial_denture' => 'required|in:None,Upper,Lower,Upper & Lower',
    ];

    protected $validationAttributes = [
        'oralExam.oral_hygiene_status' => 'Oral Hygiene Status',
        'oralExam.gingiva' => 'Gingiva',
        'oralExam.calcular_deposits' => 'Calcular Deposits',
        'oralExam.stains' => 'Stains',
        'oralExam.complete_denture' => 'Complete Denture',
        'oralExam.partial_denture' => 'Partial Denture',
    ];

    #[On('fillDentalChart')]
    public function fillFromRecord($data)
    {
        $this->resetValidation();
        // Reset to defaults first
        $this->teeth = [];
        $this->oralExam = [
            'oral_hygiene_status' => '',
            'gingiva' => '',
            'calcular_deposits' => '',
            'stains' => '',
            'complete_denture' => '',
            'partial_denture' => '',
        ];
        $this->chartComments = ['notes' => '', 'treatment_plan' => ''];
        $this->dentitionType = $this->defaultDentitionTypeFromAge();

        if (!empty($data)) {
            if (isset($data['teeth'])) {
                $this->teeth = $data['teeth'];
                $this->oralExam = array_merge($this->oralExam, $data['oral_exam'] ?? []);
                $this->chartComments = array_merge($this->chartComments, $data['comments'] ?? []);
                $meta = $data['meta'] ?? [];
                $this->dentitionType = $this->normalizeDentitionType($meta['dentition_type'] ?? 'adult');
                $this->numberingSystem = is_string($meta['numbering_system'] ?? null) ? $meta['numbering_system'] : 'FDI';
            } else {
                $this->teeth = $data;
            }
        }

        $this->sanitizeFormData();
    }

    public function updatedSelectedHistoryId($value)
    {
        $this->dispatch('switchChartHistory', chartId: $value);
    }

    public function triggerNewChart()
    {
        $this->selectedHistoryId = ''; 
        $this->dentitionType = $this->defaultDentitionTypeFromAge();
        $this->dispatch('startNewChartSession'); 
    }

    private function defaultDentitionTypeFromAge(): string
    {
        if ($this->patientAge !== null && $this->patientAge >= 0 && $this->patientAge < 13) {
            return 'child';
        }

        return 'adult';
    }

    public function updatedDentitionType($value): void
    {
        $this->dentitionType = $this->normalizeDentitionType($value);
        $this->teeth = $this->sanitizeTeethForDentition($this->teeth, $this->dentitionType);
    }

    public function updated($propertyName): void
    {
        if (!is_string($propertyName) || $propertyName === '') {
            return;
        }

        $this->sanitizeProperty($propertyName);
        $this->resetValidation($propertyName);
    }

    private function normalizeDentitionType($value): string
    {
        return in_array($value, ['adult', 'child'], true) ? $value : 'adult';
    }

    

    #[On('requestDentalChartData')]
    public function provideData()
    {
        $this->sanitizeFormData();
        $this->teeth = $this->sanitizeTeethForDentition($this->teeth, $this->dentitionType);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            $field = $e->validator->errors()->keys()[0] ?? null;
            if ($field) {
                $this->dispatch('scroll-to-error', field: $field);
            }
            $this->dispatch('patient-form-navigation-finished', currentStep: 3);
            return;
        }

        if (! $this->shouldShowChart) {
            $fullData = [
                'teeth' => $this->teeth,
                'oral_exam' => $this->oralExam,
                'comments' => $this->chartComments,
                'meta' => [
                    'dentition_type' => $this->dentitionType,
                    'numbering_system' => $this->numberingSystem,
                ],
            ];

            $this->dispatch('dentalChartDataProvided', data: $fullData);
            return;
        }

        $this->dispatch('request-dental-chart-teeth');
    }

    #[On('dentalChartTeethProvided')]
    public function handleTeethProvided($teeth)
    {
        $this->sanitizeFormData();
        $teeth = $this->sanitizeTeethForDentition($teeth, $this->dentitionType);

        $fullData = [
            'teeth' => $teeth,
            'oral_exam' => $this->oralExam,
            'comments' => $this->chartComments,
            'meta' => [
                'dentition_type' => $this->dentitionType,
                'numbering_system' => $this->numberingSystem,
            ],
        ];

        $this->dispatch('dentalChartDataProvided', data: $fullData);
    }

    public function render()
    {
        return view('livewire.patient.form.dental-chart', [
            'shouldShowChart' => $this->shouldShowChart,
        ]);
    }

    private function sanitizeTeethForDentition($teeth, string $dentitionType): array
    {
        if (!is_array($teeth)) {
            return [];
        }

        $allowedTeeth = $dentitionType === 'child'
            ? $this->getChildTeethSet()
            : $this->getAdultTeethSet();

        $allowedMap = array_flip($allowedTeeth);
        $filtered = [];

        foreach ($teeth as $key => $value) {
            $tooth = (int) $key;
            if (isset($allowedMap[$tooth])) {
                $filtered[(string) $tooth] = $value;
            }
        }

        return $filtered;
    }

    private function getAdultTeethSet(): array
    {
        return [
            11, 12, 13, 14, 15, 16, 17, 18,
            21, 22, 23, 24, 25, 26, 27, 28,
            31, 32, 33, 34, 35, 36, 37, 38,
            41, 42, 43, 44, 45, 46, 47, 48,
        ];
    }

    private function getChildTeethSet(): array
    {
        return [
            51, 52, 53, 54, 55,
            61, 62, 63, 64, 65,
            71, 72, 73, 74, 75,
            81, 82, 83, 84, 85,
        ];
    }

    private function sanitizeFormData(): void
    {
        foreach (array_keys(self::ORAL_EXAM_OPTIONS) as $field) {
            $this->oralExam[$field] = $this->sanitizeEnumValue(
                $this->oralExam[$field] ?? '',
                self::ORAL_EXAM_OPTIONS[$field]
            );
        }

        $this->chartComments['notes'] = $this->sanitizeSentenceCaseText($this->chartComments['notes'] ?? '', true, '.,&()/:;!?-');
        $this->chartComments['treatment_plan'] = $this->sanitizeSentenceCaseText($this->chartComments['treatment_plan'] ?? '', true, '.,&()/:;!?-');
        $this->numberingSystem = $this->sanitizeEnumValue($this->numberingSystem, ['FDI'], 'FDI');
    }

    private function sanitizeProperty(string $propertyName): void
    {
        if (str_starts_with($propertyName, 'oralExam.')) {
            $field = substr($propertyName, strlen('oralExam.'));
            if (isset(self::ORAL_EXAM_OPTIONS[$field])) {
                $this->oralExam[$field] = $this->sanitizeEnumValue(
                    $this->oralExam[$field] ?? '',
                    self::ORAL_EXAM_OPTIONS[$field]
                );
            }

            return;
        }

        if ($propertyName === 'chartComments.notes') {
            $this->chartComments['notes'] = $this->sanitizeSentenceCaseText($this->chartComments['notes'] ?? '', true, '.,&()/:;!?-');
            return;
        }

        if ($propertyName === 'chartComments.treatment_plan') {
            $this->chartComments['treatment_plan'] = $this->sanitizeSentenceCaseText($this->chartComments['treatment_plan'] ?? '', true, '.,&()/:;!?-');
        }
    }
}
