<?php

namespace Tests\Feature;

use App\Livewire\Patient\PatientRecords;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class PatientRecordsLayoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('role')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('home_address')->nullable();
            $table->string('patient_type')->nullable();
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->dateTime('appointment_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        $staffUser = User::query()->create([
            'username' => 'staff.member',
            'email' => 'staff@example.com',
            'password' => bcrypt('secret123'),
            'role' => User::ROLE_STAFF,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($staffUser);
    }

    public function test_patient_records_list_has_full_viewport_minimum_height(): void
    {
        Livewire::test(PatientRecords::class)
            ->assertSeeHtml('max-height:calc(100vh - 220px);');
    }

    public function test_staff_cannot_delete_patient_records(): void
    {
        $patientId = DB::table('patients')->insertGetId([
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'mobile_number' => '09123456789',
            'email_address' => 'patient@example.com',
        ]);

        Livewire::test(PatientRecords::class)
            ->call('deletePatient', $patientId)
            ->assertDispatched('flash-message', type: 'error', message: 'You do not have permission to delete patient records.');

        $this->assertDatabaseHas('patients', ['id' => $patientId]);
    }

    public function test_staff_does_not_see_delete_action(): void
    {
        DB::table('patients')->insert([
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'mobile_number' => '09123456789',
            'email_address' => 'patient@example.com',
        ]);

        Livewire::test(PatientRecords::class)
            ->assertDontSee('Delete this patient? This cannot be undone.');
    }
}
