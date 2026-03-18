<?php

namespace Tests\Feature;

use App\Livewire\PatientFormController\BasicInfo;
use Livewire\Livewire;
use Tests\TestCase;

class PatientFormBasicInfoTest extends TestCase
{
    public function test_birth_date_input_uses_live_binding_for_reactive_age_updates(): void
    {
        Livewire::test(BasicInfo::class)
            ->assertSeeHtml('wire:model.live="birth_date"');
    }

    public function test_minor_section_is_shown_for_patients_below_eighteen(): void
    {
        Livewire::test(BasicInfo::class)
            ->assertDontSee('For Patients Below 18')
            ->set('birth_date', now()->subYears(10)->toDateString())
            ->assertSee('For Patients Below 18')
            ->assertSee('Who is answering this form?');
    }

    public function test_minor_section_is_hidden_for_adult_patients(): void
    {
        Livewire::test(BasicInfo::class)
            ->set('birth_date', now()->subYears(10)->toDateString())
            ->assertSee('For Patients Below 18')
            ->set('birth_date', now()->subYears(20)->toDateString())
            ->assertDontSee('For Patients Below 18');
    }
}
