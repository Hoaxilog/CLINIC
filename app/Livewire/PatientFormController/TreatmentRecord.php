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
    
    // [UPDATED] Renamed from $attachment to $image
    public $image; 

    #[Reactive] 
    public $isReadOnly = false;

    public function mount($data = [], $isReadOnly = false)
    {
        $this->isReadOnly = $isReadOnly;
        if (!empty($data)) {
            // Fill data but exclude image initially to prevent overwriting with non-file object if unnecessary
            // actually we want to fill it if it's a string (from DB)
            $this->fill($data);
        }
    }

    public function rules()
    {
        $isNewUpload = is_object($this->image);

        return [
            'dmd' => 'nullable|string',
            'treatment' => 'required|string',
            'cost_of_treatment' => 'nullable|numeric',
            'amount_charged' => 'nullable|numeric',
            'remarks' => 'nullable|string',
            
            'image' => $isNewUpload ? 'nullable|image|max:10240' : 'nullable', 
        ];
    }

    #[On('validateTreatmentRecord')]
    public function validateForm()
    {
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