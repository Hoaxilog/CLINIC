<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\WithFileUploads;
use Illuminate\Validation\ValidationException;

class TreatmentRecord extends Component
{
    use WithFileUploads;

    public $dmd = '';
    public $treatment = '';
    public $cost_of_treatment = '';
    public $amount_charged = '';
    public $remarks = '';
    
    public $beforeImages = [];
    public $afterImages = [];
    public $existingImages = [];

    #[Reactive] 
    public $isReadOnly = false;

    public function mount($data = [], $isReadOnly = false)
    {
        $this->isReadOnly = $isReadOnly;
        if (!empty($data)) {
            if (!empty($data['image_list'])) {
                $this->existingImages = $data['image_list'];
                unset($data['image_list']);
            }
            $this->fill($data);
        }
    }

    public function rules()
    {
        return [
            // [UPDATED] Made these fields Required
            'dmd' => 'required|string',
            'treatment' => 'required|string',
            'cost_of_treatment' => 'required|numeric|min:0',
            'amount_charged' => 'required|numeric|min:0',
            
            'remarks' => 'nullable|string',
            'beforeImages' => 'nullable|array|max:4',
            'beforeImages.*' => 'image|max:10240',
            'afterImages' => 'nullable|array|max:4',
            'afterImages.*' => 'image|max:10240',
        ];
    }

    // [ADDED] Custom Attribute Names for cleaner error messages
    protected $validationAttributes = [
        'dmd' => 'Dentist (DMD)',
        'treatment' => 'Treatment/Procedure',
        'cost_of_treatment' => 'Cost',
        'amount_charged' => 'Amount Charged',
    ];

    #[On('validateTreatmentRecord')]
    public function validateForm()
    {
        // This will now fail and stop if fields are empty
        try {
            $validatedData = $this->validate();
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            $field = $e->validator->errors()->keys()[0] ?? null;
            if ($field) {
                $this->dispatch('scroll-to-error', field: $field);
            }
            return;
        }

        $beforeCount = is_array($this->beforeImages) ? count($this->beforeImages) : 0;
        $afterCount = is_array($this->afterImages) ? count($this->afterImages) : 0;
        if (($beforeCount + $afterCount) > 4) {
            $this->addError('beforeImages', 'You can upload up to 4 images total.');
            return;
        }

        $payloads = [];
        if (!empty($this->beforeImages)) {
            foreach ($this->beforeImages as $image) {
                $path = $image->store('treatment-records/before/' . now()->format('Y/m'), 'public');
                $payloads[] = ['path' => $path, 'type' => 'before'];
            }
        }
        if (!empty($this->afterImages)) {
            foreach ($this->afterImages as $image) {
                $path = $image->store('treatment-records/after/' . now()->format('Y/m'), 'public');
                $payloads[] = ['path' => $path, 'type' => 'after'];
            }
        }
        if (!empty($payloads)) {
            $validatedData['image_payloads'] = $payloads;
        }

        $this->dispatch('treatmentRecordValidated', data: $validatedData);
        $this->reset(['beforeImages', 'afterImages']);
    }

    public function render()
    {
        return view('livewire.PatientFormViews.treatment-record');
    }
}
