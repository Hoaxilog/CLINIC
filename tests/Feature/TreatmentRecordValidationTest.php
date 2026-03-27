<?php

namespace Tests\Feature;

use App\Livewire\Patient\Form\TreatmentRecord;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class TreatmentRecordValidationTest extends TestCase
{
    public function test_treatment_record_blocks_out_of_range_numeric_values(): void
    {
        Livewire::test(TreatmentRecord::class)
            ->set('dmd', 'Dr Sample')
            ->set('selectedTreatments', ['Cleaning'])
            ->set('cost_of_treatment', '23132132321')
            ->set('amount_charged', '31232312')
            ->call('validateForm')
            ->assertHasErrors([
                'cost_of_treatment',
            ]);
    }

    public function test_pending_before_image_can_be_removed_before_save(): void
    {
        $firstImage = UploadedFile::fake()->image('before-1.jpg');
        $secondImage = UploadedFile::fake()->image('before-2.jpg');

        Livewire::test(TreatmentRecord::class)
            ->set('beforeImages', [$firstImage, $secondImage])
            ->call('removeBeforeImage', 0)
            ->assertCount('beforeImages', 1)
            ->assertSet('beforeImages.0', fn ($file) => $file->getClientOriginalName() === 'before-2.jpg');
    }

    public function test_pending_after_image_can_be_removed_before_save(): void
    {
        $firstImage = UploadedFile::fake()->image('after-1.jpg');
        $secondImage = UploadedFile::fake()->image('after-2.jpg');

        Livewire::test(TreatmentRecord::class)
            ->set('afterImages', [$firstImage, $secondImage])
            ->call('removeAfterImage', 1)
            ->assertCount('afterImages', 1)
            ->assertSet('afterImages.0', fn ($file) => $file->getClientOriginalName() === 'after-1.jpg');
    }
}
