<?php

namespace Tests\Feature;

use App\Livewire\appointment\BookAppointment;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class BookAppointmentLegacySchemaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
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
            $table->string('requester_last_name')->nullable();
            $table->date('requester_birth_date')->nullable();
            $table->string('requester_contact_number')->nullable();
            $table->string('requester_email')->nullable();
            $table->string('modified_by')->nullable();
            $table->timestamps();
        });

        DB::table('appointments')->insert([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->toDateTimeString(),
            'status' => 'Pending',
            'requester_first_name' => 'Legacy',
            'requester_last_name' => 'Booker',
            'requester_birth_date' => '1999-01-15',
            'requester_contact_number' => '09123456789',
            'requester_email' => 'legacy@example.com',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_previous_booking_prefill_works_without_requested_patient_birth_date_column(): void
    {
        Livewire::test(BookAppointment::class)
            ->set('email', 'legacy@example.com')
            ->assertSet('first_name', 'Legacy')
            ->assertSet('last_name', 'Booker')
            ->assertSet('contact_number', '09123456789')
            ->assertSet('patient_birth_date', '1999-01-15');
    }
}
