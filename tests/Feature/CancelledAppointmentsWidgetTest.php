<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\CancelledAppointmentsWidget;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class CancelledAppointmentsWidgetTest extends TestCase
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

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->time('duration')->default('01:00:00');
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
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
            $table->string('requester_contact_number')->nullable();
            $table->string('requester_email')->nullable();
            $table->string('requested_patient_first_name')->nullable();
            $table->string('requested_patient_last_name')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

        DB::table('services')->insert([
            'id' => 1,
            'service_name' => 'Cleaning',
            'duration' => '01:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staffUserId = DB::table('users')->insertGetId([
            'username' => 'staff.user',
            'email' => 'staff@example.com',
            'password' => bcrypt('secret123'),
            'role' => 2,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs(User::query()->findOrFail($staffUserId));
    }

    public function test_widget_shows_cancelled_appointment_reason_and_contact_actions(): void
    {
        DB::table('appointments')->insert([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(14, 0)->toDateTimeString(),
            'status' => 'Cancelled',
            'requester_first_name' => 'Maria',
            'requester_last_name' => 'Santos',
            'requester_contact_number' => '09123456789',
            'requester_email' => 'maria@example.com',
            'requested_patient_first_name' => 'Jamie',
            'requested_patient_last_name' => 'Santos',
            'cancellation_reason' => 'Family emergency',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(CancelledAppointmentsWidget::class)
            ->assertSee('Jamie Santos')
            ->assertSee('Family emergency')
            ->assertSee('Call Patient')
            ->assertSee('Email Patient')
            ->assertSee('tel:09123456789', false)
            ->assertSee('mailto:maria@example.com', false);
    }

    public function test_widget_shows_fallback_when_no_reason_is_provided(): void
    {
        DB::table('appointments')->insert([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(15, 0)->toDateTimeString(),
            'status' => 'Cancelled',
            'requester_first_name' => 'Paolo',
            'requester_last_name' => 'Rivera',
            'requester_contact_number' => '09998887777',
            'requester_email' => 'paolo@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(CancelledAppointmentsWidget::class)
            ->assertSee('Paolo Rivera')
            ->assertSee('No cancellation reason was provided.');
    }
}