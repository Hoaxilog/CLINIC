<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\PendingApprovalsWidget;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class PendingApprovalsWidgetTest extends TestCase
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
            $table->string('middle_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('modified_by')->nullable();
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
            $table->boolean('booking_for_other')->default(false);
            $table->string('requested_patient_first_name')->nullable();
            $table->string('requested_patient_last_name')->nullable();
            $table->date('requested_patient_birth_date')->nullable();
            $table->string('requester_relationship_to_patient')->nullable();
            $table->date('requester_birth_date')->nullable();
            $table->string('modified_by')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->string('event')->nullable();
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
            'username' => 'dashboard.staff',
            'email' => 'dashboard@example.com',
            'password' => bcrypt('secret123'),
            'role' => 2,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs(User::query()->findOrFail($staffUserId));
    }

    public function test_dashboard_widget_shows_actual_patient_and_booked_by_details(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(13, 0)->toDateTimeString(),
            'status' => 'Pending',
            'requester_first_name' => 'Maria',
            'requester_last_name' => 'Cruz',
            'requester_contact_number' => '09123456789',
            'requester_email' => 'maria@example.com',
            'booking_for_other' => true,
            'requested_patient_first_name' => 'Jamie',
            'requested_patient_last_name' => 'Cruz',
            'requested_patient_birth_date' => '2010-05-01',
            'requester_relationship_to_patient' => 'Mother',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(PendingApprovalsWidget::class)
            ->assertSee('Booked by Maria Cruz')
            ->assertSee('(Mother)')
            ->call('viewApproval', $appointmentId)
            ->assertSee('Patient')
            ->assertSee('Jamie Cruz')
            ->assertSee('Booked By / Contact')
            ->assertSee('Maria Cruz')
            ->assertSee('Relationship: Mother')
            ->assertSee('Approve first, link on arrival');
    }

    public function test_dashboard_widget_can_approve_pending_request_without_patient_link(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(14, 0)->toDateTimeString(),
            'status' => 'Pending',
            'requester_first_name' => 'Paolo',
            'requester_last_name' => 'Reyes',
            'requester_contact_number' => '09998887777',
            'requester_email' => 'paolo@example.com',
            'requester_birth_date' => '1995-08-09',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(PendingApprovalsWidget::class)
            ->call('approveAppointment', $appointmentId);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'status' => 'Scheduled',
            'patient_id' => null,
        ]);
    }

    public function test_dashboard_widget_review_request_link_points_to_the_selected_appointment_request(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(15, 0)->toDateTimeString(),
            'status' => 'Pending',
            'requester_first_name' => 'Nico',
            'requester_last_name' => 'Dela Cruz',
            'requester_contact_number' => '09997778888',
            'requester_email' => 'nico@example.com',
            'requester_birth_date' => '1994-12-11',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(PendingApprovalsWidget::class)
            ->assertSee(route('appointment.requests', ['appointment' => $appointmentId]), false);
    }
}