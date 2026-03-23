<?php

namespace Tests\Feature;

use App\Livewire\Appointment\BookAppointment;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class BookPageComponentResolutionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('service_id');
            $table->dateTime('appointment_date');
            $table->string('status')->default('Pending');
            $table->unsignedBigInteger('requester_user_id')->nullable();
            $table->string('requester_first_name')->nullable();
            $table->string('requester_middle_name')->nullable();
            $table->string('requester_last_name')->nullable();
            $table->date('requester_birth_date')->nullable();
            $table->string('requester_contact_number')->nullable();
            $table->string('requester_email')->nullable();
            $table->boolean('booking_for_other')->default(false);
            $table->string('requested_patient_first_name')->nullable();
            $table->string('requested_patient_middle_name')->nullable();
            $table->string('requested_patient_last_name')->nullable();
            $table->date('requested_patient_birth_date')->nullable();
            $table->string('requester_relationship_to_patient')->nullable();
            $table->string('modified_by')->nullable();
            $table->timestamps();
        });

        DB::table('services')->insert([
            'id' => 1,
            'service_name' => 'General Checkup',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_book_page_renders_booking_component(): void
    {
        $this->get(route('book'))
            ->assertOk()
            ->assertSee('bookingForm', false)
            ->assertSee('wire:name="appointment.book-appointment"', false)
            ->assertSee('General Checkup', false);
    }

    public function test_booking_component_mounts_by_class(): void
    {
        Livewire::test(BookAppointment::class)
            ->assertSet('selectedDate', now()->toDateString())
            ->assertSee('General Checkup');
    }
}
