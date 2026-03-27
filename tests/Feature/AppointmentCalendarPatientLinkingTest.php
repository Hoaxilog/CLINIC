<?php

namespace Tests\Feature;

use App\Livewire\Appointment\AppointmentCalendar;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class AppointmentCalendarPatientLinkingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            .DIRECTORY_SEPARATOR.'clinic-testing-views-'.uniqid('ac-linking-', true);
        if (! is_dir($compiledPath)) {
            mkdir($compiledPath, 0777, true);
        }
        config(['view.compiled' => $compiledPath]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->unsignedBigInteger('patient_id')->nullable();
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
            $table->string('requester_middle_name')->nullable();
            $table->string('requester_last_name')->nullable();
            $table->string('requester_contact_number')->nullable();
            $table->string('requester_email')->nullable();
            $table->boolean('booking_for_other')->default(false);
            $table->string('requested_patient_first_name')->nullable();
            $table->string('requested_patient_middle_name')->nullable();
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
            $table->index(['subject_id', 'subject_type']);
            $table->index(['causer_id', 'causer_type']);
            $table->index('log_name');
            $table->index('event');
        });

        DB::table('services')->insert([
            'id' => 1,
            'service_name' => 'Cleaning',
            'duration' => '01:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staffUserId = DB::table('users')->insertGetId([
            'username' => 'staff.member',
            'email' => 'staff@example.com',
            'password' => bcrypt('secret123'),
            'role' => 2,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs(User::query()->findOrFail($staffUserId));
    }

    public function test_scheduled_appointment_without_patient_shows_linking_actions(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(9, 0)->toDateTimeString(),
            'status' => 'Scheduled',
            'requester_first_name' => 'Renz',
            'requester_last_name' => 'Rosales',
            'requester_contact_number' => '09979775797',
            'requester_email' => 'renz@example.com',
            'requester_birth_date' => '2003-04-29',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->assertSee('No patient record linked yet')
            ->assertSee('Link to Selected Patient')
            ->assertSee('Create New Patient Record');
    }

    public function test_pending_appointment_can_be_approved_without_linked_patient(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(8, 0)->toDateTimeString(),
            'status' => 'Pending',
            'requester_first_name' => 'Lia',
            'requester_last_name' => 'Santos',
            'requester_contact_number' => '09990001111',
            'requester_email' => 'lia@example.com',
            'requester_birth_date' => '1999-02-14',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->call('updateStatus', 'Scheduled');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'status' => 'Scheduled',
            'patient_id' => null,
        ]);
    }

    public function test_pending_appointment_review_shows_approval_safety_summary(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(15, 0)->toDateTimeString(),
            'status' => 'Pending',
            'requester_first_name' => 'Nina',
            'requester_last_name' => 'Lopez',
            'requester_contact_number' => '09993334444',
            'requester_email' => 'nina@example.com',
            'requester_birth_date' => '1997-03-14',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->assertSee('Approval Safety Check')
            ->assertSee('Safe to approve')
            ->assertSee('Approved in this slot')
            ->assertSee('Appointment Request at Same Time');
    }

    public function test_pending_appointment_cannot_be_approved_when_slot_is_full(): void
    {
        $slot = now()->addDay()->setTime(16, 0)->toDateTimeString();

        DB::table('appointments')->insert([
            [
                'patient_id' => null,
                'service_id' => 1,
                'appointment_date' => $slot,
                'status' => 'Scheduled',
                'requester_first_name' => 'First',
                'requester_last_name' => 'Booked',
                'requester_contact_number' => '09990000001',
                'requester_email' => 'first@example.com',
                'requester_birth_date' => '1992-01-01',
                'modified_by' => 'STAFF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_id' => null,
                'service_id' => 1,
                'appointment_date' => $slot,
                'status' => 'Waiting',
                'requester_first_name' => 'Second',
                'requester_last_name' => 'Booked',
                'requester_contact_number' => '09990000002',
                'requester_email' => 'second@example.com',
                'requester_birth_date' => '1993-02-02',
                'modified_by' => 'STAFF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $pendingAppointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => $slot,
            'status' => 'Pending',
            'requester_first_name' => 'Third',
            'requester_last_name' => 'Pending',
            'requester_contact_number' => '09990000003',
            'requester_email' => 'third@example.com',
            'requester_birth_date' => '1994-03-03',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $pendingAppointmentId)
            ->assertSee('Slot already full')
            ->call('updateStatus', 'Scheduled')
            ->assertHasErrors(['conflict']);

        $this->assertDatabaseHas('appointments', [
            'id' => $pendingAppointmentId,
            'status' => 'Pending',
        ]);
    }

    public function test_approved_unlinked_appointment_shows_arrival_linking_guidance(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(8, 30)->toDateTimeString(),
            'status' => 'Pending',
            'requester_first_name' => 'Mia',
            'requester_last_name' => 'Rivera',
            'requester_contact_number' => '09990002222',
            'requester_email' => 'mia@example.com',
            'requester_birth_date' => '1998-07-20',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->call('updateStatus', 'Scheduled');

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->assertSee('No patient record linked yet')
            ->assertSee('Link an existing patient record');
    }

    public function test_staff_can_link_scheduled_appointment_to_existing_patient(): void
    {
        $patientId = DB::table('patients')->insertGetId([
            'first_name' => 'Renz',
            'last_name' => 'Rosales',
            'mobile_number' => '09979775797',
            'email_address' => 'renz@example.com',
            'birth_date' => '2003-04-29',
            'modified_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(10, 0)->toDateTimeString(),
            'status' => 'Scheduled',
            'requester_first_name' => 'Renz',
            'requester_last_name' => 'Rosales',
            'requester_contact_number' => '09979775797',
            'requester_email' => 'renz@example.com',
            'requester_birth_date' => '2003-04-29',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->set('selectedPendingPatientId', $patientId)
            ->call('linkPendingRequestToExistingPatient');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'patient_id' => $patientId,
        ]);
    }

    public function test_scheduled_appointment_shows_detailed_possible_patient_match_information(): void
    {
        $patientId = DB::table('patients')->insertGetId([
            'first_name' => 'Renz',
            'last_name' => 'Rosales',
            'mobile_number' => '09979775797',
            'email_address' => 'renz@example.com',
            'birth_date' => '2003-04-29',
            'modified_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(10, 30)->toDateTimeString(),
            'status' => 'Scheduled',
            'requester_first_name' => 'Renz',
            'requester_last_name' => 'Rosales',
            'requester_contact_number' => '09979775797',
            'requester_email' => 'renz@example.com',
            'requester_birth_date' => '2003-04-29',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->assertSee('Possible Patient Matches')
            ->assertSee('Appointment Request')
            ->assertSee('Existing Patient Record')
            ->assertSee('Patient Name')
            ->assertSee('Contact Number')
            ->assertSee('Email Address')
            ->assertSee('Renz Rosales')
            ->assertSee('09979775797')
            ->assertSee('renz@example.com')
            ->assertSee('Full name + birth date match')
            ->assertSee('View Full Record')
            ->call('previewPendingPatientRecord', $patientId)
            ->assertDispatched('editPatient');
    }

    public function test_pending_request_tab_button_is_not_rendered_on_calendar(): void
    {
        Livewire::test(AppointmentCalendar::class)
            ->assertDontSeeHtml('wire:click="setActiveTab(\'pending\')"')
            ->assertDontSeeHtml('wire:click="setActiveTab(\'calendar\')"');
    }

    public function test_pending_request_route_query_opens_the_selected_request_immediately(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(14, 30)->toDateTimeString(),
            'status' => 'Pending',
            'requester_first_name' => 'Aira',
            'requester_last_name' => 'Flores',
            'requester_contact_number' => '09995556666',
            'requester_email' => 'aira@example.com',
            'requester_birth_date' => '1996-06-08',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::withQueryParams(['appointment' => $appointmentId])
            ->test(AppointmentCalendar::class, ['initialTab' => 'pending'])
            ->assertSet('activeTab', 'pending')
            ->assertSet('showAppointmentModal', true)
            ->assertSet('viewingAppointmentId', $appointmentId)
            ->assertSee('Aira')
            ->assertSee('Flores');
    }

    public function test_waiting_request_middle_name_prefills_appointment_modal_before_patient_linking(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(10, 0)->toDateTimeString(),
            'status' => 'Waiting',
            'requester_first_name' => 'Maria',
            'requester_middle_name' => 'Lopez',
            'requester_last_name' => 'Cruz',
            'requester_contact_number' => '09123456789',
            'requester_email' => 'maria@example.com',
            'booking_for_other' => true,
            'requested_patient_first_name' => 'Jamie',
            'requested_patient_middle_name' => 'Mae',
            'requested_patient_last_name' => 'Cruz',
            'requested_patient_birth_date' => '2010-05-01',
            'requester_relationship_to_patient' => 'Mother',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->assertSet('middleName', 'Mae');
    }

    public function test_staff_can_create_patient_from_waiting_appointment(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(11, 0)->toDateTimeString(),
            'status' => 'Waiting',
            'requester_first_name' => 'Maria',
            'requester_last_name' => 'Cruz',
            'requester_contact_number' => '09123456789',
            'requester_email' => 'maria@example.com',
            'booking_for_other' => true,
            'requested_patient_first_name' => 'Jamie',
            'requested_patient_middle_name' => 'Mae',
            'requested_patient_last_name' => 'Cruz',
            'requested_patient_birth_date' => '2010-05-01',
            'requester_relationship_to_patient' => 'Mother',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->call('createPatientForPendingRequest');

        $patientId = DB::table('appointments')->where('id', $appointmentId)->value('patient_id');

        $this->assertNotNull($patientId);
        $this->assertDatabaseHas('patients', [
            'id' => $patientId,
            'first_name' => 'Jamie',
            'middle_name' => 'Mae',
            'last_name' => 'Cruz',
            'mobile_number' => '',
            'email_address' => null,
        ]);
    }

    public function test_waiting_appointment_without_patient_cannot_be_admitted(): void
    {
        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(11, 30)->toDateTimeString(),
            'status' => 'Waiting',
            'requester_first_name' => 'Carlo',
            'requester_last_name' => 'Mendoza',
            'requester_contact_number' => '09111112222',
            'requester_email' => 'carlo@example.com',
            'requester_birth_date' => '1994-09-15',
            'modified_by' => 'GUEST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->call('admitPatient')
            ->assertSet('appointmentStatus', 'Waiting');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'status' => 'Waiting',
            'patient_id' => null,
        ]);
    }

    public function test_for_myself_linking_persists_user_patient_mapping(): void
    {
        $patientUserId = DB::table('users')->insertGetId([
            'username' => 'patient.self',
            'email' => 'self@example.com',
            'patient_id' => null,
            'password' => bcrypt('secret123'),
            'role' => 3,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $patientId = DB::table('patients')->insertGetId([
            'first_name' => 'Self',
            'last_name' => 'Patient',
            'mobile_number' => '09981234567',
            'email_address' => 'self@example.com',
            'birth_date' => '2001-01-01',
            'modified_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(10, 0)->toDateTimeString(),
            'status' => 'Scheduled',
            'requester_user_id' => $patientUserId,
            'requester_first_name' => 'Self',
            'requester_last_name' => 'Patient',
            'requester_contact_number' => '09981234567',
            'requester_email' => 'self@example.com',
            'requester_birth_date' => '2001-01-01',
            'booking_for_other' => false,
            'modified_by' => 'PATIENT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->set('selectedPendingPatientId', $patientId)
            ->call('linkPendingRequestToExistingPatient');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'patient_id' => $patientId,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $patientUserId,
            'patient_id' => $patientId,
        ]);
    }

    public function test_for_someone_else_linking_does_not_persist_requester_user_mapping(): void
    {
        $requesterUserId = DB::table('users')->insertGetId([
            'username' => 'requester.mom',
            'email' => 'mom@example.com',
            'patient_id' => null,
            'password' => bcrypt('secret123'),
            'role' => 3,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $patientId = DB::table('patients')->insertGetId([
            'first_name' => 'Child',
            'last_name' => 'Patient',
            'mobile_number' => '09980002222',
            'email_address' => null,
            'birth_date' => '2012-06-01',
            'modified_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => null,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(11, 0)->toDateTimeString(),
            'status' => 'Scheduled',
            'requester_user_id' => $requesterUserId,
            'requester_first_name' => 'Mom',
            'requester_last_name' => 'Patient',
            'requester_contact_number' => '09981112222',
            'requester_email' => 'mom@example.com',
            'booking_for_other' => true,
            'requested_patient_first_name' => 'Child',
            'requested_patient_last_name' => 'Patient',
            'requested_patient_birth_date' => '2012-06-01',
            'requester_relationship_to_patient' => 'Mother',
            'modified_by' => 'PATIENT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->set('selectedPendingPatientId', $patientId)
            ->call('linkPendingRequestToExistingPatient');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'patient_id' => $patientId,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $requesterUserId,
            'patient_id' => null,
        ]);
    }

    public function test_unlink_clears_appointment_and_user_patient_mapping_for_self_booking(): void
    {
        $patientUserId = DB::table('users')->insertGetId([
            'username' => 'unlink.self',
            'email' => 'unlink-self@example.com',
            'patient_id' => null,
            'password' => bcrypt('secret123'),
            'role' => 3,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $patientId = DB::table('patients')->insertGetId([
            'first_name' => 'Unlink',
            'last_name' => 'Self',
            'mobile_number' => '09983334444',
            'email_address' => 'unlink-self@example.com',
            'birth_date' => '1995-08-08',
            'modified_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->where('id', $patientUserId)->update([
            'patient_id' => $patientId,
            'updated_at' => now(),
        ]);

        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => $patientId,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(12, 0)->toDateTimeString(),
            'status' => 'Scheduled',
            'requester_user_id' => $patientUserId,
            'requester_first_name' => 'Unlink',
            'requester_last_name' => 'Self',
            'requester_contact_number' => '09983334444',
            'requester_email' => 'unlink-self@example.com',
            'booking_for_other' => false,
            'modified_by' => 'PATIENT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->call('unlinkAppointmentPatient');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'patient_id' => null,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $patientUserId,
            'patient_id' => null,
        ]);
    }

    public function test_unlink_then_relink_updates_user_patient_mapping(): void
    {
        $patientUserId = DB::table('users')->insertGetId([
            'username' => 'relink.self',
            'email' => 'relink-self@example.com',
            'patient_id' => null,
            'password' => bcrypt('secret123'),
            'role' => 3,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $oldPatientId = DB::table('patients')->insertGetId([
            'first_name' => 'Old',
            'last_name' => 'Record',
            'mobile_number' => '09984445555',
            'email_address' => 'old@example.com',
            'birth_date' => '1990-10-10',
            'modified_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $newPatientId = DB::table('patients')->insertGetId([
            'first_name' => 'New',
            'last_name' => 'Record',
            'mobile_number' => '09985556666',
            'email_address' => 'relink-self@example.com',
            'birth_date' => '1991-11-11',
            'modified_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->where('id', $patientUserId)->update([
            'patient_id' => $oldPatientId,
            'updated_at' => now(),
        ]);

        $appointmentId = DB::table('appointments')->insertGetId([
            'patient_id' => $oldPatientId,
            'service_id' => 1,
            'appointment_date' => now()->addDay()->setTime(13, 0)->toDateTimeString(),
            'status' => 'Scheduled',
            'requester_user_id' => $patientUserId,
            'requester_first_name' => 'Relink',
            'requester_last_name' => 'Self',
            'requester_contact_number' => '09985556666',
            'requester_email' => 'relink-self@example.com',
            'booking_for_other' => false,
            'modified_by' => 'PATIENT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->call('unlinkAppointmentPatient')
            ->set('selectedPendingPatientId', $newPatientId)
            ->call('linkPendingRequestToExistingPatient');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'patient_id' => $newPatientId,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $patientUserId,
            'patient_id' => $newPatientId,
        ]);
    }
}