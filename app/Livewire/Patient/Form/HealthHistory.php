<?php

namespace App\Livewire\Patient\Form;

use App\Livewire\Patient\Form\Concerns\SanitizesPatientFormInput;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class HealthHistory extends Component
{
    use SanitizesPatientFormInput;

    protected const SENTENCE_CASE_FIELDS = [
        'what_last_visit_reason_q1',
        'what_seeing_dentist_reason_q2',
        'what_nervous_concern_q6',
        'what_condition_reason_q1',
        'what_hospitalized_reason_q2',
        'what_serious_illness_operation_reason_q3',
        'what_medications_list_q4',
        'what_allergies_list_q5',
    ];

    public $when_last_visit_q1;

    public $what_last_visit_reason_q1 = '';

    public $what_seeing_dentist_reason_q2 = '';

    public $is_clicking_jaw_q3a = '';

    public $is_pain_jaw_q3b = '';

    public $is_difficulty_opening_closing_q3c = '';

    public $is_locking_jaw_q3d = '';

    public $is_clench_grind_q4 = '';

    public $is_bad_experience_q5 = '';

    public $is_nervous_q6 = '';

    public $what_nervous_concern_q6 = '';

    public $is_condition_q1 = '';

    public $what_condition_reason_q1 = '';

    public $is_hospitalized_q2 = '';

    public $what_hospitalized_reason_q2 = '';

    public $is_serious_illness_operation_q3 = '';

    public $what_serious_illness_operation_reason_q3 = '';

    public $is_taking_medications_q4 = '';

    public $what_medications_list_q4 = '';

    public $is_allergic_medications_q5 = '';

    public $what_allergies_list_q5 = '';

    public $is_allergic_latex_rubber_metals_q6 = '';

    public $is_pregnant_q7 = '';

    public $is_breast_feeding_q8 = '';

    public $gender;

    public $historyList = [];

    public $selectedHistoryId = '';

    #[Reactive]
    public $isReadOnly = '';

    public $isCreating = '';

    public function mount($data = [], $gender = null, $isReadOnly = false, $historyList = [], $selectedHistoryId = '')
    {
        if (! empty($data)) {
            $this->fill($data);
        }
        if ($gender) {
            $this->gender = $gender;
        }

        $this->isReadOnly = $isReadOnly;
        $this->historyList = is_array($historyList) ? $historyList : [];
        $this->selectedHistoryId = $selectedHistoryId;
        $this->isCreating = $selectedHistoryId === 'new';
        $this->sanitizeFormData();
        $this->dispatchUiStateSync();
    }

    public function triggerNewHistory()
    {
        $this->isCreating = true;
        $this->selectedHistoryId = 'new';

        $this->resetHealthFields();
        $this->dispatchUiStateSync();
    }

    #[On('setHealthHistoryContext')]
    public function setContext($gender, $historyList, $selectedId)
    {
        $this->gender = $gender;
        $this->historyList = $historyList;
        $this->selectedHistoryId = $selectedId;
        $this->isCreating = $selectedId === 'new';
        $this->dispatchUiStateSync();
    }

    public function updatedSelectedHistoryId($value)
    {
        if ($value === 'new') {
            $this->triggerNewHistory();
        } else {
            $this->isCreating = false;
            $this->dispatch('switchHealthHistory', historyId: $value);
        }
    }

    public function updated($propertyName): void
    {
        if (! is_string($propertyName) || $propertyName === '') {
            return;
        }

        $this->sanitizeField($propertyName);
        $this->resetValidation($propertyName);
    }

    protected $casts = [
        'is_clicking_jaw_q3a' => 'boolean',
        'is_pain_jaw_q3b' => 'boolean',
        'is_difficulty_opening_closing_q3c' => 'boolean',
        'is_locking_jaw_q3d' => 'boolean',
        'is_clench_grind_q4' => 'boolean',
        'is_bad_experience_q5' => 'boolean',
        'is_nervous_q6' => 'boolean',
        'is_condition_q1' => 'boolean',
        'is_hospitalized_q2' => 'boolean',
        'is_serious_illness_operation_q3' => 'boolean',
        'is_taking_medications_q4' => 'boolean',
        'is_allergic_medications_q5' => 'boolean',
        'is_allergic_latex_rubber_metals_q6' => 'boolean',
        'is_pregnant_q7' => 'boolean',
        'is_breast_feeding_q8' => 'boolean',
    ];

    public function rules()
    {
        $isFemale = ($this->gender === 'Female');

        return [
            'when_last_visit_q1' => 'nullable|date',
            'what_last_visit_reason_q1' => ['nullable', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],
            'what_seeing_dentist_reason_q2' => ['required', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],

            'is_clicking_jaw_q3a' => 'required|boolean',
            'is_pain_jaw_q3b' => 'required|boolean',
            'is_difficulty_opening_closing_q3c' => 'required|boolean',
            'is_locking_jaw_q3d' => 'required|boolean',
            'is_clench_grind_q4' => 'required|boolean',
            'is_bad_experience_q5' => 'required|boolean',

            'is_nervous_q6' => 'required|boolean',
            'what_nervous_concern_q6' => ['required_if:is_nervous_q6,true', 'nullable', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],

            'is_condition_q1' => 'required|boolean',
            'what_condition_reason_q1' => ['required_if:is_condition_q1,true', 'nullable', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],

            'is_hospitalized_q2' => 'required|boolean',
            'what_hospitalized_reason_q2' => ['required_if:is_hospitalized_q2,true', 'nullable', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],

            'is_serious_illness_operation_q3' => 'required|boolean',
            'what_serious_illness_operation_reason_q3' => ['required_if:is_serious_illness_operation_q3,true', 'nullable', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],

            'is_taking_medications_q4' => 'required|boolean',
            'what_medications_list_q4' => ['required_if:is_taking_medications_q4,true', 'nullable', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],

            'is_allergic_medications_q5' => 'required|boolean',
            'what_allergies_list_q5' => ['required_if:is_allergic_medications_q5,true', 'nullable', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],

            'is_allergic_latex_rubber_metals_q6' => 'required|boolean',

            'is_pregnant_q7' => $isFemale ? 'required|boolean' : 'nullable|boolean',
            'is_breast_feeding_q8' => $isFemale ? 'required|boolean' : 'nullable|boolean',
        ];
    }

    protected $validationAttributes = [
        'what_seeing_dentist_reason_q2' => 'reason for today\'s visit',
        'is_clicking_jaw_q3a' => 'clicking of the jaw',
        'is_pain_jaw_q3b' => 'pain below the ear',
        'is_difficulty_opening_closing_q3c' => 'difficulty opening or closing your mouth',
        'is_locking_jaw_q3d' => 'locking of the jaw',
        'is_clench_grind_q4' => 'clenching or grinding',
        'is_bad_experience_q5' => 'bad dental experience',
        'is_nervous_q6' => 'feeling nervous about treatment',
        'what_nervous_concern_q6' => 'concern about treatment',
        'is_condition_q1' => 'medical condition history',
        'what_condition_reason_q1' => 'medical condition details',
        'is_hospitalized_q2' => 'hospitalization history',
        'what_hospitalized_reason_q2' => 'hospitalization details',
        'is_serious_illness_operation_q3' => 'serious illness or operation history',
        'what_serious_illness_operation_reason_q3' => 'serious illness or operation details',
        'is_taking_medications_q4' => 'current medications',
        'what_medications_list_q4' => 'medication details',
        'is_allergic_medications_q5' => 'medication allergy history',
        'what_allergies_list_q5' => 'allergy details',
        'is_allergic_latex_rubber_metals_q6' => 'allergy to latex, rubber, or metals',
        'is_pregnant_q7' => 'pregnancy status',
        'is_breast_feeding_q8' => 'breastfeeding status',
    ];

    protected function messages(): array
    {
        return [
            'what_seeing_dentist_reason_q2.required' => 'Please tell us why you are visiting the dentist today.',

            'is_clicking_jaw_q3a.required' => 'Please choose Yes or No for clicking of the jaw.',
            'is_pain_jaw_q3b.required' => 'Please choose Yes or No for pain below the ear.',
            'is_difficulty_opening_closing_q3c.required' => 'Please choose Yes or No for difficulty opening or closing your mouth.',
            'is_locking_jaw_q3d.required' => 'Please choose Yes or No for locking of the jaw.',
            'is_clench_grind_q4.required' => 'Please choose Yes or No for clenching or grinding your teeth.',
            'is_bad_experience_q5.required' => 'Please choose Yes or No for bad experience in a dental office.',
            'is_nervous_q6.required' => 'Please choose Yes or No if you feel nervous about treatment.',
            'what_nervous_concern_q6.required_if' => 'Please share your concern so we can make your treatment more comfortable.',

            'is_condition_q1.required' => 'Please choose Yes or No for medical condition history.',
            'what_condition_reason_q1.required_if' => 'Please provide details about the medical condition.',
            'is_hospitalized_q2.required' => 'Please choose Yes or No for hospitalization history.',
            'what_hospitalized_reason_q2.required_if' => 'Please provide details about the hospitalization.',
            'is_serious_illness_operation_q3.required' => 'Please choose Yes or No for serious illness or operation history.',
            'what_serious_illness_operation_reason_q3.required_if' => 'Please provide details about the illness or operation.',
            'is_taking_medications_q4.required' => 'Please choose Yes or No if you are currently taking medications.',
            'what_medications_list_q4.required_if' => 'Please list the medications you are currently taking.',
            'is_allergic_medications_q5.required' => 'Please choose Yes or No for medication allergies.',
            'what_allergies_list_q5.required_if' => 'Please tell us which medications you are allergic to.',
            'is_allergic_latex_rubber_metals_q6.required' => 'Please choose Yes or No for allergies to latex, rubber, or metals.',

            'is_pregnant_q7.required' => 'Please choose Yes or No for pregnancy status.',
            'is_breast_feeding_q8.required' => 'Please choose Yes or No for breastfeeding status.',

            'what_last_visit_reason_q1.regex' => 'Please use letters, numbers, spaces, and common punctuation only.',
            'what_seeing_dentist_reason_q2.regex' => 'Please use letters, numbers, spaces, and common punctuation only.',
            'what_nervous_concern_q6.regex' => 'Please use letters, numbers, spaces, and common punctuation only.',
            'what_condition_reason_q1.regex' => 'Please use letters, numbers, spaces, and common punctuation only.',
            'what_hospitalized_reason_q2.regex' => 'Please use letters, numbers, spaces, and common punctuation only.',
            'what_serious_illness_operation_reason_q3.regex' => 'Please use letters, numbers, spaces, and common punctuation only.',
            'what_medications_list_q4.regex' => 'Please use letters, numbers, spaces, and common punctuation only.',
            'what_allergies_list_q5.regex' => 'Please use letters, numbers, spaces, and common punctuation only.',
        ];
    }

    #[On('validateHealthHistory')]
    public function validateForm()
    {
        try {
            $this->sanitizeFormData();
            $validatedData = $this->validate($this->rules(), $this->messages(), $this->validationAttributes);
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            $field = $e->validator->errors()->keys()[0] ?? null;
            if ($field) {
                $this->dispatch('scroll-to-error', field: $field);
            }
            $this->dispatch('patient-form-navigation-finished', currentStep: 2);

            return;
        }

        // 1. Sanitize Booleans (Convert '' or null to 0)
        // This fixes the "Incorrect integer value" error
        $booleans = [
            'is_clicking_jaw_q3a', 'is_pain_jaw_q3b', 'is_difficulty_opening_closing_q3c',
            'is_locking_jaw_q3d', 'is_clench_grind_q4', 'is_bad_experience_q5', 'is_nervous_q6',
            'is_condition_q1', 'is_hospitalized_q2', 'is_serious_illness_operation_q3',
            'is_taking_medications_q4', 'is_allergic_medications_q5', 'is_allergic_latex_rubber_metals_q6',
            'is_pregnant_q7', 'is_breast_feeding_q8',
        ];

        foreach ($booleans as $field) {
            // Force to integer: (int)'' becomes 0, (int)'1' becomes 1
            if (isset($validatedData[$field])) {
                $validatedData[$field] = (int) $validatedData[$field];
            } else {
                $validatedData[$field] = 0; // Default to 0 if missing
            }
        }

        // 2. Sanitize Date (Convert '' to NULL)
        // This fixes the "Invalid datetime format" error
        if (isset($validatedData['when_last_visit_q1']) && $validatedData['when_last_visit_q1'] === '') {
            $validatedData['when_last_visit_q1'] = null;
        }

        // 3. Add the Context ID (for your edit logic)
        $validatedData['selectedHistoryId'] = $this->selectedHistoryId;

        // 4. Send the clean data to the parent
        $this->dispatch('healthHistoryValidated', data: $validatedData);
    }

    #[On('requestHealthHistoryData')]
    public function provideDataWithoutValidation()
    {
        $this->sanitizeFormData();

        $payload = [
            'when_last_visit_q1' => $this->when_last_visit_q1,
            'what_last_visit_reason_q1' => $this->what_last_visit_reason_q1,
            'what_seeing_dentist_reason_q2' => $this->what_seeing_dentist_reason_q2,
            'is_clicking_jaw_q3a' => $this->is_clicking_jaw_q3a,
            'is_pain_jaw_q3b' => $this->is_pain_jaw_q3b,
            'is_difficulty_opening_closing_q3c' => $this->is_difficulty_opening_closing_q3c,
            'is_locking_jaw_q3d' => $this->is_locking_jaw_q3d,
            'is_clench_grind_q4' => $this->is_clench_grind_q4,
            'is_bad_experience_q5' => $this->is_bad_experience_q5,
            'is_nervous_q6' => $this->is_nervous_q6,
            'what_nervous_concern_q6' => $this->what_nervous_concern_q6,
            'is_condition_q1' => $this->is_condition_q1,
            'what_condition_reason_q1' => $this->what_condition_reason_q1,
            'is_hospitalized_q2' => $this->is_hospitalized_q2,
            'what_hospitalized_reason_q2' => $this->what_hospitalized_reason_q2,
            'is_serious_illness_operation_q3' => $this->is_serious_illness_operation_q3,
            'what_serious_illness_operation_reason_q3' => $this->what_serious_illness_operation_reason_q3,
            'is_taking_medications_q4' => $this->is_taking_medications_q4,
            'what_medications_list_q4' => $this->what_medications_list_q4,
            'is_allergic_medications_q5' => $this->is_allergic_medications_q5,
            'what_allergies_list_q5' => $this->what_allergies_list_q5,
            'is_allergic_latex_rubber_metals_q6' => $this->is_allergic_latex_rubber_metals_q6,
            'is_pregnant_q7' => $this->is_pregnant_q7,
            'is_breast_feeding_q8' => $this->is_breast_feeding_q8,
            'selectedHistoryId' => $this->selectedHistoryId,
        ];

        $this->dispatch('healthHistoryValidated', data: $payload);
    }

    #[On('fillHealthHistory')]
    public function fillForm($data, $gender)
    {
        $this->resetValidation();
        $this->resetHealthFields();
        $this->fill($data);
        if ($gender) {
            $this->gender = $gender;
        }
        $this->sanitizeFormData();
        $this->dispatchUiStateSync();
    }

    public function resetForm()
    {
        $this->resetExcept(['isReadOnly', 'historyList', 'selectedHistoryId', 'gender', 'isCreating']);
        $this->resetValidation();
        $this->dispatchUiStateSync();
    }

    public function render()
    {
        return view('livewire.patient.form.health-history');
    }

    private function resetHealthFields(): void
    {
        $this->reset([
            'when_last_visit_q1', 'what_last_visit_reason_q1', 'what_seeing_dentist_reason_q2',
            'is_clicking_jaw_q3a', 'is_pain_jaw_q3b', 'is_difficulty_opening_closing_q3c',
            'is_locking_jaw_q3d', 'is_clench_grind_q4', 'is_bad_experience_q5',
            'is_nervous_q6', 'what_nervous_concern_q6',
            'is_condition_q1', 'what_condition_reason_q1',
            'is_hospitalized_q2', 'what_hospitalized_reason_q2',
            'is_serious_illness_operation_q3', 'what_serious_illness_operation_reason_q3',
            'is_taking_medications_q4', 'what_medications_list_q4',
            'is_allergic_medications_q5', 'what_allergies_list_q5',
            'is_allergic_latex_rubber_metals_q6',
            'is_pregnant_q7', 'is_breast_feeding_q8',
        ]);
    }

    private function sanitizeFormData(): void
    {
        foreach (self::SENTENCE_CASE_FIELDS as $field) {
            $this->sanitizeField($field);
        }
    }

    private function sanitizeField(string $field): void
    {
        if (! in_array($field, self::SENTENCE_CASE_FIELDS, true)) {
            return;
        }

        $this->{$field} = $this->sanitizeSentenceCaseText($this->{$field}, true, '.,&()/:;!?-');
    }

    private function dispatchUiStateSync(): void
    {
        $this->dispatch('sync-health-history-ui',
            nervous: $this->toUiString($this->is_nervous_q6),
            condition: $this->toUiString($this->is_condition_q1),
            hospitalized: $this->toUiString($this->is_hospitalized_q2),
            seriousIllness: $this->toUiString($this->is_serious_illness_operation_q3),
            medications: $this->toUiString($this->is_taking_medications_q4),
            allergies: $this->toUiString($this->is_allergic_medications_q5)
        );
    }

    private function toUiString($value): string
    {
        if ($value === true || $value === 1 || $value === '1') {
            return '1';
        }

        if ($value === false || $value === 0 || $value === '0') {
            return '0';
        }

        return '';
    }
}
