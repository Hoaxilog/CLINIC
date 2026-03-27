<?php

namespace Tests\Feature;

use App\Livewire\Appointment\AppointmentCalendar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests for duration-aware validation in AppointmentCalendar (staff side).
 *
 * Covers:
 * - saveAppointment(): rejects when appointment would end after 20:00
 * - saveAppointment(): duration-aware overlap check via CalendarQueryService
 * - savePendingReschedule(): rejects when rescheduled time would end after 20:00
 * - savePendingReschedule(): duration-aware overlap check
 */
class AppointmentCalendarDurationAwareTest extends TestCase
{
    // ─────────────────────────────────────────────
    // Schema scaffolding (same pattern as existing calendar tests)
    // ─────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

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
        });

        Schema::create('blocked_slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('chair_id')->nullable();
            $table->string('reason')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        // Seed services
        DB::table('services')->insert([
            ['id' => 1, 'service_name' => '1-Hour Checkup',   'duration' => '01:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'service_name' => '2-Hour Cleaning',  'duration' => '02:00:00', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'service_name' => '3-Hour Procedure', 'duration' => '03:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed staff user and act as them
        $staffId = DB::table('users')->insertGetId([
            'username'          => 'staff.member',
            'email'             => 'staff@example.com',
            'password'          => bcrypt('secret'),
            'role'              => 2,
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $this->actingAs(User::query()->findOrFail($staffId));
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    private function insertApprovedAppointment(string $date, string $time, int $serviceId, string $status = 'Scheduled'): int
    {
        return (int) DB::table('appointments')->insertGetId([
            'patient_id'             => null,
            'service_id'             => $serviceId,
            'appointment_date'       => Carbon::parse($date.' '.$time)->toDateTimeString(),
            'status'                 => $status,
            'requester_first_name'   => 'Existing',
            'requester_last_name'    => 'Patient',
            'requester_contact_number' => '09111111111',
            'requester_email'        => 'existing@example.com',
            'requester_birth_date'   => '1990-01-01',
            'modified_by'            => 'STAFF',
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);
    }

    private function insertBlockedSlot(string $date, string $startTime, string $endTime, ?int $chairId = null): int
    {
        return (int) DB::table('blocked_slots')->insertGetId([
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'chair_id' => $chairId,
            'reason' => 'Chair blocked',
            'created_by' => 'Staff Member',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 1: saveAppointment() — closing time validation
    // ═══════════════════════════════════════════════════════════

    /**
     * Staff booking a 3-hour service at 19:00 (ends 22:00) should be rejected.
     */
    public function test_save_appointment_rejects_3h_service_at_19_past_closing(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        Livewire::test(AppointmentCalendar::class)
            ->set('firstName', 'Joel')
            ->set('lastName', 'Patient')
            ->set('birthDate', '1990-01-01')
            ->set('contactNumber', '9123456789')
            ->set('selectedService', 3) // 3-hour service
            ->set('selectedDate', $date)
            ->set('selectedTime', '19:00')
            ->call('saveAppointment')
            ->assertHasErrors(['conflict']);

        $this->assertDatabaseCount('appointments', 0);
    }

    /**
     * Staff booking a 2-hour service at 18:00 (ends 20:00 exactly) should SUCCEED.
     */
    public function test_save_appointment_allows_2h_service_at_18_ends_exactly_at_closing(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        Livewire::test(AppointmentCalendar::class)
            ->set('firstName', 'Joel')
            ->set('lastName', 'Patient')
            ->set('birthDate', '1990-01-01')
            ->set('contactNumber', '9123456789')
            ->set('selectedService', 2) // 2-hour service
            ->set('selectedDate', $date)
            ->set('selectedTime', '18:00')
            ->call('saveAppointment')
            ->assertHasNoErrors('conflict');

        $this->assertDatabaseCount('appointments', 1);
    }

    /**
     * Staff booking a 3-hour service at 17:00 (ends 20:00 exactly) should SUCCEED.
     */
    public function test_save_appointment_allows_3h_service_at_17_ends_exactly_at_closing(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        Livewire::test(AppointmentCalendar::class)
            ->set('firstName', 'Joel')
            ->set('lastName', 'Patient')
            ->set('birthDate', '1990-01-01')
            ->set('contactNumber', '9123456789')
            ->set('selectedService', 3) // 3-hour service
            ->set('selectedDate', $date)
            ->set('selectedTime', '17:00')
            ->call('saveAppointment')
            ->assertHasNoErrors('conflict');

        $this->assertDatabaseCount('appointments', 1);
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 2: saveAppointment() — duration-aware overlap
    // ═══════════════════════════════════════════════════════════

    /**
     * Key regression: booking a 1-hour slot at 11:00 should be rejected because
     * two approved 3-hour appointments at 10:00 overlap the 10:00–13:00 range.
     * Old code would have allowed this (it only checked exact start time).
     */
    public function test_save_appointment_rejects_overlap_from_earlier_long_appointment(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        // 2 approved 3-hour appointments starting at 10:00 → they fill 10:00–13:00
        $this->insertApprovedAppointment($date, '10:00:00', 3);
        $this->insertApprovedAppointment($date, '10:00:00', 3, 'Waiting');

        Livewire::test(AppointmentCalendar::class)
            ->set('firstName', 'Joel')
            ->set('lastName', 'Patient')
            ->set('birthDate', '1990-01-01')
            ->set('contactNumber', '9123456789')
            ->set('selectedService', 1) // 1-hour service at 11:00
            ->set('selectedDate', $date)
            ->set('selectedTime', '11:00')
            ->call('saveAppointment')
            ->assertHasErrors(['conflict']);

        // Only the 2 pre-existing ones in DB
        $this->assertDatabaseCount('appointments', 2);
    }

    /**
     * A 1-hour booking at 13:00 should succeed — the 10:00–13:00 range is full
     * but 13:00 itself starts AFTER the 3h block ends.
     */
    public function test_save_appointment_allows_slot_right_after_long_appointment_ends(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        // Only 1 approved 3-hour appointment (capacity=2, still 1 slot free)
        $this->insertApprovedAppointment($date, '10:00:00', 3);

        Livewire::test(AppointmentCalendar::class)
            ->set('firstName', 'Joel')
            ->set('lastName', 'Patient')
            ->set('birthDate', '1990-01-01')
            ->set('contactNumber', '9123456789')
            ->set('selectedService', 1) // 1-hour at 13:00
            ->set('selectedDate', $date)
            ->set('selectedTime', '13:00')
            ->call('saveAppointment')
            ->assertHasNoErrors('conflict');

        $this->assertDatabaseCount('appointments', 2); // 1 pre-existing + 1 new
    }

    public function test_save_appointment_allows_booking_when_only_one_chair_is_blocked(): void
    {
        $date = now()->addDay()->format('Y-m-d');
        $this->insertBlockedSlot($date, '10:00:00', '11:00:00', 1);

        Livewire::test(AppointmentCalendar::class)
            ->set('firstName', 'Joel')
            ->set('lastName', 'Patient')
            ->set('birthDate', '1990-01-01')
            ->set('contactNumber', '9123456789')
            ->set('selectedService', 1)
            ->set('selectedDate', $date)
            ->set('selectedTime', '10:00')
            ->call('saveAppointment')
            ->assertHasNoErrors('conflict');

        $this->assertDatabaseCount('appointments', 1);
    }

    public function test_save_appointment_rejects_when_one_chair_is_blocked_and_other_is_taken(): void
    {
        $date = now()->addDay()->format('Y-m-d');
        $this->insertBlockedSlot($date, '10:00:00', '11:00:00', 1);
        $this->insertApprovedAppointment($date, '10:00:00', 1, 'Scheduled');

        Livewire::test(AppointmentCalendar::class)
            ->set('firstName', 'Joel')
            ->set('lastName', 'Patient')
            ->set('birthDate', '1990-01-01')
            ->set('contactNumber', '9123456789')
            ->set('selectedService', 1)
            ->set('selectedDate', $date)
            ->set('selectedTime', '10:00')
            ->call('saveAppointment')
            ->assertHasErrors(['conflict']);

        $this->assertDatabaseCount('appointments', 1);
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 3: savePendingReschedule() — closing time validation
    // ═══════════════════════════════════════════════════════════

    /**
     * Same-start long appointments should render as parallel cards in the desktop overlay
     * while keeping the full time range visible on each card.
     */
    public function test_calendar_renders_same_start_long_appointments_side_by_side(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        $this->insertApprovedAppointment($date, '13:00:00', 2, 'Scheduled');
        $this->insertApprovedAppointment($date, '13:00:00', 2, 'Waiting');

        $component = Livewire::test(AppointmentCalendar::class);
        $html = $component->html();

        $this->assertSame(2, substr_count($html, 'calendar-overlap-card'));
        $this->assertSame(2, substr_count($html, 'data-lane-count="2"'));
        $this->assertStringContainsString('1:00 PM', $html);
        $this->assertStringContainsString('3:00 PM', $html);
    }

    /**
     * Mixed-duration same-start appointments should use the longest group height,
     * while each card keeps its own visual height and time range.
     */
    public function test_calendar_renders_mixed_duration_same_start_appointments_with_individual_heights(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        $this->insertApprovedAppointment($date, '13:00:00', 1, 'Scheduled');
        $this->insertApprovedAppointment($date, '13:00:00', 2, 'Waiting');

        $component = Livewire::test(AppointmentCalendar::class);
        $html = $component->html();

        $this->assertSame(2, substr_count($html, 'data-lane-count="2"'));
        $this->assertStringContainsString('data-lane-index="0"', $html);
        $this->assertStringContainsString('data-lane-index="1"', $html);
        $this->assertStringContainsString('2:00 PM', $html);
        $this->assertStringContainsString('1:00 PM', $html);
    }

    /**
     * Different start times that overlap should share parallel lanes instead of
     * visually occupying the same column space.
     */
    public function test_calendar_renders_different_start_overlaps_in_parallel_lanes(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        $this->insertApprovedAppointment($date, '15:00:00', 2, 'Scheduled');
        $this->insertApprovedAppointment($date, '16:00:00', 2, 'Waiting');

        $component = Livewire::test(AppointmentCalendar::class);
        $html = $component->html();

        $this->assertSame(2, substr_count($html, 'data-lane-count="2"'));
        $this->assertStringContainsString('data-lane-index="0"', $html);
        $this->assertStringContainsString('data-lane-index="1"', $html);
        $this->assertStringContainsString('5:00 PM', $html);
        $this->assertStringContainsString('6:00 PM', $html);
    }

    /**
     * Rescheduling a Pending appointment to a 3-hour service at 19:00 (ends 22:00) should fail.
     */
    public function test_reschedule_pending_rejects_3h_at_19_past_closing(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        $appointmentId = $this->insertApprovedAppointment($date, '09:00:00', 3, 'Pending');

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->set('isRescheduling', true)
            ->set('selectedService', 3)
            ->set('selectedDate', $date)
            ->set('selectedTime', '19:00')
            ->call('savePendingReschedule')
            ->assertHasErrors(['conflict']);

        $this->assertDatabaseHas('appointments', [
            'id'     => $appointmentId,
            'status' => 'Pending',
        ]);
    }

    /**
     * Rescheduling a Pending appointment to a 2-hour service at 18:00 (ends 20:00) should SUCCEED.
     */
    public function test_reschedule_pending_allows_2h_at_18_ends_exactly_at_closing(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        $appointmentId = $this->insertApprovedAppointment($date, '09:00:00', 2, 'Pending');

        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $appointmentId)
            ->set('isRescheduling', true)
            ->set('selectedService', 2)
            ->set('selectedDate', $date)
            ->set('selectedTime', '18:00')
            ->call('savePendingReschedule')
            ->assertHasNoErrors('conflict');

        // Status should have changed to Scheduled (or at least not rejected by closing time)
        $this->assertDatabaseHas('appointments', [
            'id'     => $appointmentId,
            'status' => 'Scheduled',
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 4: savePendingReschedule() — duration-aware overlap
    // ═══════════════════════════════════════════════════════════

    /**
     * Rescheduling a Pending appointment to 11:00 (1-hour) should be rejected
     * when two 3-hour Scheduled appointments at 10:00 overlap that slot.
     */
    public function test_reschedule_pending_rejects_overlap_from_earlier_long_appointment(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        // Pre-existing full appointments (10:00–13:00 range)
        $this->insertApprovedAppointment($date, '10:00:00', 3, 'Scheduled');
        $this->insertApprovedAppointment($date, '10:00:00', 3, 'Waiting');

        // The pending appointment being rescheduled (at a safe time — 08:00)
        $pendingId = $this->insertApprovedAppointment($date, '08:00:00', 1, 'Pending');

        // viewAppointment() will set isViewing=true and appointmentStatus='Pending' automatically
        Livewire::test(AppointmentCalendar::class)
            ->call('viewAppointment', $pendingId)
            ->set('selectedService', 1)          // 1-hour service
            ->set('selectedDate', $date)
            ->set('selectedTime', '11:00')        // H:i format (required by validation)
            ->call('savePendingReschedule')
            ->assertHasErrors(['conflict']);

        $this->assertDatabaseHas('appointments', [
            'id'     => $pendingId,
            'status' => 'Pending',
        ]);
    }
}
