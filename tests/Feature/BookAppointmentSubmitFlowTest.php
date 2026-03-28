<?php

namespace Tests\Feature;

use App\Livewire\Appointment\BookAppointment;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class BookAppointmentSubmitFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->unsignedTinyInteger('role')->default(3);
            $table->rememberToken()->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->time('duration')->nullable();
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

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps();
        });

        DB::table('services')->insert([
            'id' => 1,
            'service_name' => 'General Checkup',
            'duration' => '01:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_authenticated_patient_can_submit_one_booking_successfully(): void
    {
        Mail::fake();

        $patient = User::query()->forceCreate([
            'username' => 'patient1',
            'first_name' => 'Pat',
            'last_name' => 'Ient',
            'email' => 'patient@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 3,
        ]);

        $this->actingAs($patient);

        Livewire::test(BookAppointment::class)
            ->set('first_name', 'Pat')
            ->set('last_name', 'Ient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'patient@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('booking_agreement', true)
            ->call('bookAppointment')
            ->assertHasNoErrors()
            ->assertRedirect(route('patient.dashboard'));

        $this->assertDatabaseCount('appointments', 1);
        $this->assertDatabaseHas('appointments', [
            'requester_user_id' => $patient->id,
            'requester_email' => 'patient@example.com',
            'status' => 'Pending',
        ]);
    }

    public function test_authenticated_patient_birth_date_prefills_from_user_profile(): void
    {
        $patient = User::query()->forceCreate([
            'username' => 'patient3',
            'first_name' => 'Pat',
            'last_name' => 'Ient',
            'email' => 'patient3@example.com',
            'email_verified_at' => now(),
            'birth_date' => '2000-04-15',
            'password' => bcrypt('password'),
            'role' => 3,
        ]);

        $this->actingAs($patient);

        Livewire::test(BookAppointment::class)
            ->assertSet('patient_birth_date', '2000-04-15');
    }

    public function test_duplicate_pending_message_only_appears_on_second_booking_attempt(): void
    {
        Mail::fake();

        $patient = User::query()->forceCreate([
            'username' => 'patient2',
            'first_name' => 'Pat',
            'last_name' => 'Ient',
            'email' => 'patient2@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 3,
        ]);

        $this->actingAs($patient);

        $component = Livewire::test(BookAppointment::class)
            ->set('first_name', 'Pat')
            ->set('last_name', 'Ient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'patient2@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '10:00:00')
            ->set('booking_agreement', true);

        $component
            ->call('bookAppointment')
            ->assertHasNoErrors();

        $component
            ->call('bookAppointment')
            ->assertHasErrors(['selectedSlot'])
            ->assertSee('You already have a pending or upcoming appointment request.');

        $this->assertDatabaseCount('appointments', 1);
    }
}
