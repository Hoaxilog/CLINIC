<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Livewire\Attributes\On;

class HealthHistory extends Component
{
    // ... (Properties remain the same) ...
    public $when_last_visit_q1;
    public $what_last_visit_reason_q1 = '';
    public $what_seeing_dentist_reason_q2 = '';
    public $is_clicking_jaw_q3a = false;
    public $is_pain_jaw_q3b = false;
    public $is_difficulty_opening_closing_q3c = false;
    public $is_locking_jaw_q3d = false;
    public $is_clench_grind_q4 = false;
    public $is_bad_experience_q5 = false;
    public $is_nervous_q6 = false;
    public $what_nervous_concern_q6 = '';

    public $is_condition_q1 = false;
    public $what_condition_reason_q1 = '';
    public $is_hospitalized_q2 = false;
    public $what_hospitalized_reason_q2 = '';
    public $is_serious_illness_operation_q3 = false;
    public $what_serious_illness_operation_reason_q3 = '';
    public $is_taking_medications_q4 = false;
    public $what_medications_list_q4 = '';
    public $is_allergic_medications_q5 = false;
    public $what_allergies_list_q5 = '';
    public $is_allergic_latex_rubber_metals_q6 = false;

    public $is_pregnant_q7 = false;
    public $is_breast_feeding_q8 = false;
    
    public $gender;
    
    // --- ADDED: Mount function handles data and gender passed from parent ---
    public function mount($data = [], $gender = null)
    {
        if (!empty($data)) {
            $this->fill($data);
        }
        if ($gender) {
            $this->gender = $gender;
        }
    }

    #[On('setGender')]
    public function setGender($gender) {
        $this->gender = $gender;
    }
    
    // ... (Casts and rules remain the same) ...
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
            'what_last_visit_reason_q1' => 'nullable|string',
            'what_seeing_dentist_reason_q2' => 'required|string',
            
            'is_clicking_jaw_q3a' => 'required|boolean',
            'is_pain_jaw_q3b' => 'required|boolean',
            'is_difficulty_opening_closing_q3c' => 'required|boolean',
            'is_locking_jaw_q3d' => 'required|boolean',
            'is_clench_grind_q4' => 'required|boolean',
            'is_bad_experience_q5' => 'required|boolean',
            
            'is_nervous_q6' => 'required|boolean',
            'what_nervous_concern_q6' => 'required_if:is_nervous_q6,true|nullable|string',

            'is_condition_q1' => 'required|boolean',
            'what_condition_reason_q1' => 'required_if:is_condition_q1,true|nullable|string',
            
            'is_hospitalized_q2' => 'required|boolean',
            'what_hospitalized_reason_q2' => 'required_if:is_hospitalized_q2,true|nullable|string',

            'is_serious_illness_operation_q3' => 'required|boolean',
            'what_serious_illness_operation_reason_q3' => 'required_if:is_serious_illness_operation_q3,true|nullable|string',

            'is_taking_medications_q4' => 'required|boolean',
            'what_medications_list_q4' => 'required_if:is_taking_medications_q4,true|nullable|string',

            'is_allergic_medications_q5' => 'required|boolean',
            'what_allergies_list_q5' => 'required_if:is_allergic_medications_q5,true|nullable|string',

            'is_allergic_latex_rubber_metals_q6' => 'required|boolean',
            
            'is_pregnant_q7' => $isFemale ? 'required|boolean' : 'nullable|boolean',
            'is_breast_feeding_q8' => $isFemale? 'required|boolean' : 'nullable|boolean',
        ];
    }

    protected $validationAttributes = [
        'what_seeing_dentist_reason_q2' => 'Reason for seeing dentist',
        'is_clicking_jaw_q3a' => 'Clicking of the Jaw',
        'is_pain_jaw_q3b' => 'Pain below the ear',
        'is_difficulty_opening_closing_q3c' => 'Difficulty opening/closing',
        'is_locking_jaw_q3d' => 'Locking of the jaw',
        'is_clench_grind_q4' => 'Clench or grind',
        'is_bad_experience_q5' => 'Bad experience',
        'is_nervous_q6' => 'Nervous',
        'what_nervous_concern_q6' => 'Nervous concern',
        'is_condition_q1' => 'Medical condition',
        'what_condition_reason_q1' => 'Medical condition reason',
        'is_hospitalized_q2' => 'Hospitalized',
        'what_hospitalized_reason_q2' => 'Hospitalized reason',
        'is_serious_illness_operation_q3' => 'Serious illness/operation',
        'what_serious_illness_operation_reason_q3' => 'Serious illness/operation reason',
        'is_taking_medications_q4' => 'Taking medications',
        'what_medications_list_q4' => 'Medications list',
        'is_allergic_medications_q5' => 'Allergic to medications',
        'what_allergies_list_q5' => 'Allergies list',
        'is_allergic_latex_rubber_metals_q6' => 'Allergic to latex/rubber/metals',
        'is_pregnant_q7' => 'Pregnant',
        'is_breast_feeding_q8' => 'Breast feeding',
    ];

    #[On('validateHealthHistory')]
    public function validateForm()
    {
        $validatedData = $this->validate();
        $this->dispatch('healthHistoryValidated', data: $validatedData);
    }

    
    #[On('fillHealthHistory')]
    public function fillForm($data, $gender)
    {   
        $this->fill($data); 
        if($gender) {
            $this->gender = $gender;
        }
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.PatientFormViews.health-history');
    }
}