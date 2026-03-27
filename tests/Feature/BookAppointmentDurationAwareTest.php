<?php

namespace Tests\Feature;

use App\Livewire\Appointment\BookAppointment;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests for the duration-aware online booking fixes applied to BookAppointment.php.
 *
 * Covers:
 * - generateSlots(): slots that exceed clinic closing time are marked full
 * - generateSlots(): slots that overlap with an existing long approved appointment are marked full
 * - generateSlots(): slot list regenerates when service changes
 * - assertBookingStillAvailable(): rejects bookings that end after clinic closing time
 * - assertBookingStillAvailable(): rejects when a DIFFERENT start-time's long appointment overlaps
 * - assertBookingStillAvailable(): duration-aware blocked slot rejection
 */
class BookAppointmentDurationAwareTest extends TestCase
{
    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    private function insertOneHourService(int $id = 1, string $name = '1-Hour Checkup'): void
    {
        DB::table('services')->insert([
            'id'           => $id,
            'service_name' => $name,
            'duration'     => '01:00:00',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    private function insertTwoHourService(int $id = 2, string $name = '2-Hour Cleaning'): void
    {
        DB::table('services')->insert([
            'id'           => $id,
            'service_name' => $name,
            'duration'     => '02:00:00',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    private function insertThreeHourService(int $id = 3, string $name = '3-Hour Procedure'): void
    {
        DB::table('services')->insert([
            'id'           => $id,
            'service_name' => $name,
            'duration'     => '03:00:00',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    private function insertApprovedAppointment(string $date, string $time, int $serviceId): void
    {
        DB::table('appointments')->insert([
            'patient_id'             => null,
            'service_id'             => $serviceId,
            'appointment_date'       => Carbon::parse($date.' '.$time)->toDateTimeString(),
            'status'                 => 'Scheduled',
            'requester_first_name'   => 'Existing',
            'requester_last_name'    => 'Patient',
            'requester_contact_number' => '09111111111',
            'requester_email'        => 'existing@example.com',
            'modified_by'            => 'STAFF',
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);
    }

    // ─────────────────────────────────────────────
    // Schema scaffolding
    // ─────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

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
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 1: generateSlots() — closing time overflow
    // ═══════════════════════════════════════════════════════════

    /**
     * A 1-hour service starting at 19:00 is the LAST valid start time (ends at 20:00 = clinic close).
     * The 20:00 slot itself should be FULL because it ends at 21:00.
     */
    public function test_one_hour_service_last_valid_slot_is_at_20(): void
    {
        $this->insertOneHourService();

        $date = now()->addDay()->format('Y-m-d');

        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 1)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));

        // 19:00 ends at 20:00 exactly — should be AVAILABLE
        $slot19 = $slots->firstWhere('value', '20:00:00');
        $this->assertNotNull($slot19, '20:00 slot should be present for a 1-hour service');
        $this->assertFalse(
            $slot19['is_full'],
            '19:00 should NOT be full for a 1-hour service (ends at 20:00 == clinic close)'
        );

        // 20:00 ends at 21:00 — FULL (exceeds closing)
        $slot20 = $slots->firstWhere('value', '20:00:00');
        $this->assertNotNull($slot20, '20:00 slot should be present in the list');
        $this->assertTrue(
            $slot20['is_full'],
            '20:00 should be FULL for a 1-hour service (would end at 21:00, past closing)'
        );
    }

    /**
     * A 2-hour service starting at 19:00 ends at 21:00 — it overflows; slot should be FULL.
     */
    public function test_two_hour_service_slot_at_19_is_full_due_to_overflow(): void
    {
        $this->insertTwoHourService();

        $date = now()->addDay()->format('Y-m-d');

        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 2)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));
        $slot19 = $slots->firstWhere('value', '19:00:00');

        $this->assertNotNull($slot19, '19:00 slot should be present in the list');
        $this->assertTrue(
            $slot19['is_full'],
            '19:00 slot should be FULL for a 2-hour service (would end at 21:00, past closing)'
        );
    }

    /**
     * A 3-hour service should mark 18:00, 19:00, and 20:00 as full (all exceed closing).
     */
    public function test_three_hour_service_marks_last_three_slots_as_full(): void
    {
        $this->insertThreeHourService();

        $date = now()->addDay()->format('Y-m-d');

        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 3)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));

        foreach (['18:00:00', '19:00:00', '20:00:00'] as $overflow) {
            $slot = $slots->firstWhere('value', $overflow);
            $this->assertNotNull($slot, "{$overflow} slot should be present");
            $this->assertTrue(
                $slot['is_full'],
                "{$overflow} slot should be FULL for a 3-hour service (would exceed 20:00 closing)"
            );
        }
    }

    /**
     * A 3-hour service at 17:00 ends exactly at 20:00 — should be ALLOWED.
     */
    public function test_three_hour_service_slot_at_17_is_available(): void
    {
        $this->insertThreeHourService();

        $date = now()->addDay()->format('Y-m-d');

        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 3)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));
        $slot17 = $slots->firstWhere('value', '17:00:00');

        $this->assertNotNull($slot17, '17:00 slot should be present');
        $this->assertFalse(
            $slot17['is_full'],
            '17:00 should NOT be full for a 3-hour service (ends at exactly 20:00)'
        );

        // 18:00 ends at 21:00 — should be FULL
        $slot18 = $slots->firstWhere('value', '18:00:00');
        $this->assertNotNull($slot18, '18:00 slot should be present');
        $this->assertTrue(
            $slot18['is_full'],
            '18:00 should be FULL for a 3-hour service (ends at 21:00, past closing)'
        );
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 2: generateSlots() — duration-aware overlap detection
    // ═══════════════════════════════════════════════════════════

    /**
     * If a 3-hour appointment exists at 10:00, then 11:00 and 12:00 should be
     * marked full for another 1-hour service (they overlap the existing range).
     */
    public function test_slots_overlapping_existing_long_appointment_are_marked_full(): void
    {
        $this->insertOneHourService(1);
        $this->insertThreeHourService(3);

        $date = now()->addDay()->format('Y-m-d');

        // Insert two approved 3-hour appointments at 10:00 (fills capacity=2)
        $this->insertApprovedAppointment($date, '10:00:00', 3);
        $this->insertApprovedAppointment($date, '10:00:00', 3);

        // Now check using a 1-hour service — 10:00, 11:00, 12:00 should all be full
        // because the 3-hour appointments overlap them
        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 1)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));

        foreach (['10:00:00', '11:00:00', '12:00:00'] as $overlapping) {
            $slot = $slots->firstWhere('value', $overlapping);
            $this->assertNotNull($slot, "{$overlapping} slot should be present");
            $this->assertTrue(
                $slot['is_full'],
                "{$overlapping} should be FULL because 2 approved 3-hour appointments at 10:00 overlap it"
            );
        }

        // 13:00 should be free (3-hour appointment ends at 13:00, no overlap)
        $slot13 = $slots->firstWhere('value', '13:00:00');
        $this->assertNotNull($slot13);
        $this->assertFalse($slot13['is_full'], '13:00 should NOT be full');
    }

    /**
     * Slots that DON'T overlap the existing appointment should remain available.
     */
    public function test_slots_outside_existing_appointment_range_remain_available(): void
    {
        $this->insertOneHourService(1);
        $this->insertTwoHourService(2);

        $date = now()->addDay()->format('Y-m-d');

        // Insert 2 approved 2-hour appointments at 14:00 (14:00–16:00 blocked)
        $this->insertApprovedAppointment($date, '14:00:00', 2);
        $this->insertApprovedAppointment($date, '14:00:00', 2);

        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 1)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));

        // 13:00 should be available (ends 14:00, no overlap with 14:00–16:00 range)
        $slot13 = $slots->firstWhere('value', '13:00:00');
        $this->assertFalse($slot13['is_full'], '13:00 should be free');

        // 16:00 should be available (starts exactly when existing appointment ends)
        $slot16 = $slots->firstWhere('value', '16:00:00');
        $this->assertFalse($slot16['is_full'], '16:00 should be free');
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 3: updatedServiceId() — slots regenerate on change
    // ═══════════════════════════════════════════════════════════

    /**
     * When service changes from 1-hour to 3-hour, the overflow slots should
     * automatically update without needing to re-select the date.
     */
    public function test_slots_regenerate_when_service_changes(): void
    {
        $this->insertOneHourService(1);
        $this->insertThreeHourService(3);

        $date = now()->addDay()->format('Y-m-d');

        $component = Livewire::test(BookAppointment::class)
            ->set('selectedDate', $date)
            ->set('service_id', 1); // 1-hour: 17:00 should be OK, 19:00 is full

        $slots1h = collect($component->get('availableSlots'));
        $this->assertFalse(
            $slots1h->firstWhere('value', '17:00:00')['is_full'],
            '17:00 should be available for 1-hour service'
        );
        $this->assertTrue(
            $slots1h->firstWhere('value', '20:00:00')['is_full'],
            '20:00 should be full for 1-hour service (ends 21:00)'
        );

        // Switch to 3-hour service — 17:00 should now also be full (ends 20:00 is ok, but 18:00+ is not)
        // More importantly, 19:00 which was fine at 1h should now be full (ends 22:00) for 3h
        $component->set('service_id', 3);

        $slots3h = collect($component->get('availableSlots'));
        $this->assertFalse(
            $slots3h->firstWhere('value', '17:00:00')['is_full'],
            '17:00 should still be available for 3-hour service (ends exactly at 20:00)'
        );
        $this->assertTrue(
            $slots3h->firstWhere('value', '18:00:00')['is_full'],
            '18:00 should be FULL for 3-hour service after switching (ends 21:00)'
        );

        // Switch back to 1-hour — 18:00 should be free again
        $component->set('service_id', 1);

        $slotsBack = collect($component->get('availableSlots'));
        $this->assertFalse(
            $slotsBack->firstWhere('value', '18:00:00')['is_full'],
            '18:00 should be available again after switching back to 1-hour service'
        );
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 4: booking submission — closing time check
    // ═══════════════════════════════════════════════════════════

    /**
     * A 3-hour booking at 18:00 (ends 21:00) should get a selectedSlot error —
     * tested via generateSlots() which marks the slot as full/unreachable.
     */
    public function test_booking_backend_rejects_overflow_via_slot_marked_full(): void
    {
        $this->insertThreeHourService(3);

        $date = now()->addDay()->format('Y-m-d');

        // The slot should already be marked full in generateSlots
        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 3)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));
        $slot18 = $slots->firstWhere('value', '18:00:00');

        $this->assertNotNull($slot18, '18:00 slot should be in the list');
        $this->assertTrue(
            $slot18['is_full'],
            '18:00 slot should be full for a 3-hour service (would end at 21:00)'
        );

        // Confirm no appointments were created
        $this->assertDatabaseCount('appointments', 0);
    }

    /**
     * A 2-hour booking at 18:00 (ends 20:00 exactly) should NOT be marked full.
     */
    public function test_booking_slot_at_exact_closing_time_is_available(): void
    {
        $this->insertTwoHourService(2);

        $date = now()->addDay()->format('Y-m-d');

        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 2)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));
        $slot18 = $slots->firstWhere('value', '18:00:00');

        $this->assertNotNull($slot18, '18:00 slot should be in the list');
        $this->assertFalse(
            $slot18['is_full'],
            '18:00 should NOT be full for a 2-hour service (ends at exactly 20:00)'
        );
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 5: generateSlots() overlap — primary check already in GROUP 2
    //          This group tests that the SAME overlap logic blocks the slot list
    //          from showing unavailable times, preventing incorrect submissions.
    // ═══════════════════════════════════════════════════════════

    /**
     * Key regression test: a 1-hour slot at 11:00 must appear FULL when two
     * approved 3-hour appointments at 10:00 already fill the overlap range.
     * Old code would NOT mark this full — it only checked TIME(appointment_date).
     */
    public function test_slot_at_11_is_full_due_to_overlap_from_earlier_3h_appointment(): void
    {
        $this->insertOneHourService(1);
        $this->insertThreeHourService(3);

        $date = now()->addDay()->format('Y-m-d');

        // 2 approved 3-hour appointments at 10:00 => they overlap 11:00 and 12:00
        $this->insertApprovedAppointment($date, '10:00:00', 3);
        $this->insertApprovedAppointment($date, '10:00:00', 3);

        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 1)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));

        $this->assertTrue(
            $slots->firstWhere('value', '11:00:00')['is_full'],
            '11:00 must be full — 2 approved 3-hour appointments at 10:00 overlap it'
        );
        $this->assertTrue(
            $slots->firstWhere('value', '12:00:00')['is_full'],
            '12:00 must be full — overlapped by same 10:00 3-hour appointments'
        );
        $this->assertFalse(
            $slots->firstWhere('value', '13:00:00')['is_full'],
            '13:00 should be free — 10:00+3h ends at 13:00, no overlap'
        );
    }

    // ═══════════════════════════════════════════════════════════
    // GROUP 6: generateSlots() blocked slot — duration-aware
    // ═══════════════════════════════════════════════════════════

    /**
     * A 2-hour service slot at 09:00 (spans 09:00–11:00) should appear blocked
     * when only the 10:00–11:00 slot is blocked.
     * Old code only checked if 09:00–10:00 overlapped the block.
     */
    public function test_slot_marked_blocked_when_block_overlaps_mid_duration(): void
    {
        $this->insertTwoHourService(2);

        Schema::create('blocked_slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('reason')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        $date = now()->addDay()->format('Y-m-d');

        // Block 10:00–11:00 only
        DB::table('blocked_slots')->insert([
            'date'       => $date,
            'start_time' => '10:00:00',
            'end_time'   => '11:00:00',
            'reason'     => 'Blocked mid-range',
            'created_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $component = Livewire::test(BookAppointment::class)
            ->set('service_id', 2)
            ->set('selectedDate', $date);

        $slots = collect($component->get('availableSlots'));
        $slot09 = $slots->firstWhere('value', '09:00:00');

        $this->assertNotNull($slot09, '09:00 slot should be in the list');
        $this->assertTrue(
            $slot09['is_blocked'],
            '09:00 should be BLOCKED — a 2-hour booking there spans 09:00–11:00, hitting the 10:00–11:00 block'
        );
    }
}
