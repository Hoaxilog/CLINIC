<?php

namespace Tests\Feature;

use App\Livewire\Patient\Form\TreatmentRecord;
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
}
