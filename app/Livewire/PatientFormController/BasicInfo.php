<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\On;

class BasicInfo extends Component
{
    // ... (Properties remain the same) ...
    // == Step 1: Patient Information ==
    public $last_name = '';
    public $first_name = '';
    public $middle_name = '';
    public $nickname = '';
    public $occupation = '';
    public $birth_date;
    public $gender;
    public $civil_status = '';
    public $home_address = '';
    public $office_address = '';
    public $home_number = '';
    public $office_number = '';
    public $mobile_number = '';
    public $email_address = '';
    public $referral = '';

    // == Step 1: Emergency Contact ==
    public $emergency_contact_name = '';
    public $emergency_contact_number = '';
    public $relationship = '';

    // == Step 1: For Patient's Below 18 ==
    public $who_answering = '';
    public $relationship_to_patient = '';
    public $father_name = '';
    public $father_number = '';
    public $mother_name = '';
    public $mother_number = '';
    public $guardian_name = '';
    public $guardian_number = '';

    // --- ADDED: Mount function to accept data passed from parent ---
    public function mount($data = [])
    {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    // ... (getAgeProperty, rules, validationAttributes remain the same) ...

    // Computed property for Age
    public function getAgeProperty()
    {
        if ($this->birth_date) {
            try {
                return Carbon::parse($this->birth_date)->age;
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function rules()
    {
        $rules = [
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'nickname' => 'nullable|string',
            'occupation' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'civil_status' => 'required|string',
            'home_address' => 'required|string',
            'office_address' => 'nullable|string',
            'home_number' => 'nullable|string',
            'office_number' => 'nullable|string',
            'mobile_number' => 'required|string',
            'email_address' => 'nullable|email',
            'referral' => 'nullable|string',
            
            'emergency_contact_name' => 'required|string',
            'emergency_contact_number' => 'required|string',
            'relationship' => 'required|string',

            'who_answering' => 'nullable|string',
            'relationship_to_patient' => 'nullable|string',
            'father_name' => 'nullable|string',
            'father_number' => 'nullable|string',
            'mother_name' => 'nullable|string',
            'mother_number' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_number' => 'nullable|string',
        ];

        if ($this->age !== null && $this->age < 18) {
            $rules['who_answering'] = 'required|string';
            $rules['relationship_to_patient'] = 'required|string';
        }

        return $rules;
    }

    protected $validationAttributes = [
        'last_name' => 'Last Name',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'birth_date' => 'Date of Birth',
        'gender' => 'Gender',
        'civil_status' => 'Civil Status',
        'home_address' => 'Home Address',
        'mobile_number' => 'Mobile Number',
        'email_address' => 'E-mail Address',
        'emergency_contact_name' => 'Emergency Contact Name',
        'emergency_contact_number' => 'Emergency Contact Number',
        'relationship' => 'Relationship to Patient',
        'who_answering' => 'Who is Answering',
        'relationship_to_patient' => 'Relationship to Patient (Below 18)',
    ];

    #[On('validateBasicInfo')]
    public function validateForm()
    {
        $validatedData = $this->validate();
        $this->dispatch('basicInfoValidated', data: $validatedData);
    }

    // We can keep this for safety, but mount() does the heavy lifting now
    #[On('fillBasicInfo')]
    public function fillForm($data)
    {
        $this->fill($data); 
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.PatientFormViews.basic-info');
    }
}