<?php

namespace App\Livewire\Patient\Form;

use App\Livewire\Patient\Form\Concerns\SanitizesPatientFormInput;
use App\Support\Services\ServiceCatalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\WithFileUploads;
use Illuminate\Validation\ValidationException;

class TreatmentRecord extends Component
{
    use WithFileUploads;
    use SanitizesPatientFormInput;

    protected const DECIMAL_FIELDS = [
        'cost_of_treatment',
        'amount_charged',
    ];
    protected const MONEY_MAX = 99999999.99;

    protected const FALLBACK_TREATMENT_OPTIONS = [
        'Consultation',
        'Oral Prophylaxis',
        'Tooth Extraction',
        'Restoration',
        'Composite Filling',
        'Amalgam Filling',
        'Root Canal Treatment',
        'Crown Placement',
        'Bridge Placement',
        'Denture',
        'Sealant',
        'Fluoride Treatment',
        'Orthodontic Adjustment',
        'Teeth Whitening',
        'Emergency Treatment',
    ];

    public $dmd = '';
    public $treatment = '';
    public $selectedTreatments = [];
    public $cost_of_treatment = '';
    public $amount_charged = '';
    public $remarks = '';
    
    public $beforeImages = [];
    public $afterImages = [];
    public $existingImages = [];
    public $removedExistingImageIds = [];

    #[Reactive] 
    public $isReadOnly = false;

    #[Reactive]
    public $imageUploadOnly = false;

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

        $this->applyDefaultDentistName();
        $this->selectedTreatments = $this->parseTreatmentString($this->treatment);
        $this->sanitizeFormData();
    }

    public function rules()
    {
        return [
            // [UPDATED] Made these fields Required
            'dmd' => ['required', 'string', 'regex:/^[\pL\pM\s\'\-.&,\/()]+$/u'],
            'treatment' => ['required', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],
            'cost_of_treatment' => ['required', 'numeric', 'min:0', 'max:' . self::MONEY_MAX],
            'amount_charged' => ['required', 'numeric', 'min:0', 'max:' . self::MONEY_MAX],
            
            'remarks' => ['nullable', 'string', 'regex:/^[\pL\pM\pN\s\'",.&()\/:;!?-]+$/u'],
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

    protected function messages(): array
    {
        $max = number_format(self::MONEY_MAX, 2, '.', ',');

        return [
            'cost_of_treatment.max' => "Cost must not be greater than {$max}.",
            'amount_charged.max' => "Amount Charged must not be greater than {$max}.",
        ];
    }

    public function updated($propertyName): void
    {
        if (!is_string($propertyName) || $propertyName === '') {
            return;
        }

        $this->sanitizeField($propertyName);

        $this->resetValidation($propertyName);

        if (in_array($propertyName, ['beforeImages', 'afterImages'], true)) {
            $this->resetValidation(['beforeImages', 'beforeImages.*', 'afterImages', 'afterImages.*']);
        }
    }

    public function updatedSelectedTreatments($value): void
    {
        $this->selectedTreatments = $this->sanitizeSelectedTreatments($value);
        $this->treatment = implode(', ', $this->selectedTreatments);
        $this->resetValidation('treatment');
    }

    public function removeBeforeImage(int $index): void
    {
        if (! array_key_exists($index, $this->beforeImages)) {
            return;
        }

        unset($this->beforeImages[$index]);
        $this->beforeImages = array_values($this->beforeImages);
        $this->resetValidation(['beforeImages', 'beforeImages.*']);
    }

    public function removeAfterImage(int $index): void
    {
        if (! array_key_exists($index, $this->afterImages)) {
            return;
        }

        unset($this->afterImages[$index]);
        $this->afterImages = array_values($this->afterImages);
        $this->resetValidation(['afterImages', 'afterImages.*']);
    }

    public function removeExistingImage(int $imageId): void
    {
        $index = collect($this->existingImages)->search(fn ($image) => (int) ($image['id'] ?? 0) === $imageId);
        if ($index === false) {
            return;
        }

        $image = $this->existingImages[$index] ?? null;
        unset($this->existingImages[$index]);
        $this->existingImages = array_values($this->existingImages);

        if (! in_array($imageId, $this->removedExistingImageIds, true)) {
            $this->removedExistingImageIds[] = $imageId;
        }

        $this->resetValidation(['beforeImages', 'beforeImages.*', 'afterImages', 'afterImages.*']);
    }

    #[On('fillTreatmentRecord')]
    public function fillForm($data)
    {
        $this->resetValidation();
        $this->reset(['dmd', 'treatment', 'selectedTreatments', 'cost_of_treatment', 'amount_charged', 'remarks', 'beforeImages', 'afterImages', 'removedExistingImageIds']);
        $this->existingImages = [];

        if (!empty($data)) {
            if (!empty($data['image_list'])) {
                $this->existingImages = $data['image_list'];
                unset($data['image_list']);
            }
            $this->fill($data);
        }

        $this->applyDefaultDentistName();
        $this->selectedTreatments = $this->parseTreatmentString($this->treatment);
        $this->sanitizeFormData();
    }

    #[On('validateTreatmentRecord')]
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
            $this->dispatch('patient-form-navigation-finished', currentStep: 4);
            return;
        }

        if (! $this->validateImageLimit()) {
            $this->dispatch('patient-form-navigation-finished', currentStep: 4);
            return;
        }

        $payloads = $this->storePendingImagePayloads();
        if (!empty($payloads)) {
            $validatedData['image_payloads'] = $payloads;
        }
        if (! empty($this->removedExistingImageIds)) {
            $validatedData['removed_image_ids'] = array_values(array_unique(array_map('intval', $this->removedExistingImageIds)));
        }

        $this->dispatch('treatmentRecordValidated', data: $validatedData);
        $this->reset(['beforeImages', 'afterImages', 'removedExistingImageIds']);
    }

    #[On('validateTreatmentRecordImagesOnly')]
    public function validateImagesOnly(): void
    {
        try {
            $validatedData = $this->validate([
                'beforeImages' => 'nullable|array|max:4',
                'beforeImages.*' => 'image|max:10240',
                'afterImages' => 'nullable|array|max:4',
                'afterImages.*' => 'image|max:10240',
            ]);
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
            $field = $e->validator->errors()->keys()[0] ?? null;
            if ($field) {
                $this->dispatch('scroll-to-error', field: $field);
            }
            $this->dispatch('patient-form-navigation-finished', currentStep: 4);
            return;
        }

        if (! $this->validateImageLimit()) {
            $this->dispatch('patient-form-navigation-finished', currentStep: 4);
            return;
        }

        $payloads = $this->storePendingImagePayloads();
        $removedImageIds = array_values(array_unique(array_map('intval', $this->removedExistingImageIds)));

        if (empty($payloads) && empty($removedImageIds)) {
            $this->addError('beforeImages', 'Add or remove at least one image before saving.');
            $this->dispatch('patient-form-navigation-finished', currentStep: 4);
            return;
        }

        $validatedData['image_payloads'] = $payloads;
        $validatedData['image_list'] = $this->existingImages;
        if (! empty($removedImageIds)) {
            $validatedData['removed_image_ids'] = $removedImageIds;
        }

        foreach (['dmd', 'treatment', 'cost_of_treatment', 'amount_charged', 'remarks'] as $field) {
            $validatedData[$field] = $this->{$field};
        }

        $this->dispatch('treatmentRecordImagesValidated', data: $validatedData);
        $this->reset(['beforeImages', 'afterImages', 'removedExistingImageIds']);
    }

    #[On('requestTreatmentRecordData')]
    public function provideDataWithoutValidation()
    {
        $this->sanitizeFormData();

        $payload = [
            'dmd' => $this->dmd,
            'treatment' => $this->treatment,
            'cost_of_treatment' => $this->cost_of_treatment,
            'amount_charged' => $this->amount_charged,
            'remarks' => $this->remarks,
        ];

        if (! empty($this->existingImages)) {
            $payload['image_list'] = $this->existingImages;
        }

        $this->dispatch('treatmentRecordDataProvided', data: $payload);
    }

    public function render()
    {
        $treatmentOptions = $this->resolveTreatmentOptions();

        return view('livewire.patient.form.treatment-record', [
            'treatmentOptions' => $treatmentOptions,
        ]);
    }

    protected function sanitizeFormData(): void
    {
        foreach (self::DECIMAL_FIELDS as $field) {
            $this->sanitizeField($field);
        }

        $this->selectedTreatments = $this->sanitizeSelectedTreatments($this->selectedTreatments);
        $this->treatment = implode(', ', $this->selectedTreatments);
        $this->sanitizeField('dmd');
        $this->sanitizeField('remarks');
    }

    protected function sanitizeField(string $field): void
    {
        if (in_array($field, self::DECIMAL_FIELDS, true)) {
            $this->{$field} = $this->sanitizeDecimalValue($this->{$field});
            return;
        }

        if ($field === 'dmd') {
            $this->dmd = $this->sanitizeTitleCaseText($this->dmd, false, '.,&/()-');
            return;
        }

        if ($field === 'treatment') {
            $this->selectedTreatments = $this->parseTreatmentString($this->treatment);
            $this->treatment = implode(', ', $this->selectedTreatments);
            return;
        }

        if ($field === 'remarks') {
            $this->{$field} = $this->sanitizeSentenceCaseText($this->{$field}, true, '.,&()/:;!?-');
        }
    }

    private function applyDefaultDentistName(): void
    {
        if (filled($this->dmd)) {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $name = trim(collect([
            $user->first_name ?? null,
            $user->last_name ?? null,
        ])->filter()->implode(' '));

        if ($name !== '') {
            $this->dmd = $name;
        }
    }

    private function parseTreatmentString($value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return $this->sanitizeSelectedTreatments(array_map('trim', explode(',', $value)));
    }

    private function sanitizeSelectedTreatments($value): array
    {
        $selected = is_array($value) ? $value : [];
        $clean = [];

        foreach ($selected as $item) {
            $item = is_string($item) ? trim($item) : '';
            if ($item !== '') {
                $clean[] = $item;
            }
        }

        return array_values(array_unique($clean));
    }

    private function resolveTreatmentOptions(): array
    {
        $options = [];

        if (Schema::hasTable('services') && Schema::hasColumn('services', 'service_name')) {
            $options = DB::table('services')
                ->whereNotNull('service_name')
                ->where('service_name', '!=', '')
                ->orderBy('service_name')
                ->pluck('service_name')
                ->map(fn($name) => trim((string) $name))
                ->filter()
                ->values()
                ->all();
        }

        $catalogOptions = collect(ServiceCatalog::all())
            ->pluck('title')
            ->map(fn($name) => trim((string) $name))
            ->filter()
            ->values()
            ->all();

        return array_values(array_unique(array_merge(
            $options,
            $catalogOptions,
            self::FALLBACK_TREATMENT_OPTIONS,
        )));
    }

    private function validateImageLimit(): bool
    {
        $existingCount = is_array($this->existingImages) ? count($this->existingImages) : 0;
        $beforeCount = is_array($this->beforeImages) ? count($this->beforeImages) : 0;
        $afterCount = is_array($this->afterImages) ? count($this->afterImages) : 0;

        if (($existingCount + $beforeCount + $afterCount) > 4) {
            $this->addError('beforeImages', 'You can store up to 4 images total per treatment record.');
            return false;
        }

        return true;
    }

    private function storePendingImagePayloads(): array
    {
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

        return $payloads;
    }
}
