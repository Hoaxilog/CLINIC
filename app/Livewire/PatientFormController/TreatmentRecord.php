<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\WithFileUploads;

class TreatmentRecord extends Component
{
    use WithFileUploads;

    public $dmd = '';
    public $treatment = '';
    public $cost_of_treatment = '';
    public $amount_charged = '';
    public $remarks = '';
    
    public $image; 

    #[Reactive] 
    public $isReadOnly = false;

    public function mount($data = [], $isReadOnly = false)
    {
        $this->isReadOnly = $isReadOnly;
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function rules()
    {
        $isNewUpload = is_object($this->image);

        return [
            // [UPDATED] Made these fields Required
            'dmd' => 'required|string',
            'treatment' => 'required|string',
            'cost_of_treatment' => 'required|numeric|min:0',
            'amount_charged' => 'required|numeric|min:0',
            
            'remarks' => 'nullable|string',
            'image' => $isNewUpload ? 'nullable|image|max:10240' : 'nullable', 
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
        $validatedData = $this->validate();
        
        if (isset($this->image) && is_object($this->image)) {
            $imageContent = file_get_contents($this->image->getRealPath());
            $base64 = base64_encode($imageContent);
            $mimeType = $this->image->getMimeType();
            
            $validatedData['image'] = 'data:' . $mimeType . ';base64,' . $base64;
        }

        $this->dispatch('treatmentRecordValidated', data: $validatedData);
    }

    public function render()
    {
        return view('livewire.PatientFormViews.treatment-record');
    }
}