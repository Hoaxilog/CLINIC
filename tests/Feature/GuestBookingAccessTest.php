<?php

namespace Tests\Feature;

use App\Livewire\appointment\BookAppointment;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class GuestBookingAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
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
            $table->boolean('booking_for_other')->default(false);
            $table->string('requested_patient_first_name')->nullable();
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_book_route_is_registered_and_public(): void
    {
        $route = Route::getRoutes()->getByName('book');

        $this->assertNotNull($route);
        $this->assertSame(['GET', 'HEAD'], $route->methods());
        $this->assertSame('book', $route->uri());
        $this->assertNotContains('auth', $route->middleware());
    }

    public function test_book_page_contains_client_side_submit_validation_hook(): void
    {
        $this->get(route('book'))
            ->assertOk()
            ->assertSee('validateBookingFormBeforeSubmit', false)
            ->assertSee('data-validate-field="patient_birth_date"', false);
    }

    public function test_guest_booking_requires_captcha_token(): void
    {
        Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('booking_agreement', true)
            ->call('bookAppointment')
            ->assertHasErrors('recaptcha');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_guest_booking_for_self_requires_birth_date(): void
    {
        Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('patient_birth_date', '')
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('booking_agreement', true)
            ->call('bookAppointment')
            ->assertHasErrors(['patient_birth_date' => 'required']);

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_guest_booking_creates_pending_request_with_null_patient(): void
    {
        Mail::fake();

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        $component = Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('booking_agreement', true)
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertSet('guestOtpStepActive', true);

        $component
            ->set('guestEmailOtpHash', Hash::make('123456'))
            ->set('guestEmailOtpTargetEmail', 'guest@example.com')
            ->set('guestEmailOtpExpiresAt', now()->addMinutes(5)->toDateTimeString());

        $component
            ->set('guestEmailOtp', '123456')
            ->call('verifyGuestEmailOtp')
            ->assertRedirect('/book');

        $this->assertDatabaseHas('appointments', [
            'patient_id' => null,
            'status' => 'Pending',
            'requester_user_id' => null,
            'requester_first_name' => 'Guest',
            'requester_last_name' => 'Patient',
            'requester_birth_date' => now()->subYears(25)->toDateString(),
            'requester_email' => 'guest@example.com',
            'requester_contact_number' => '09123456789',
            'booking_for_other' => false,
            'modified_by' => 'GUEST',
        ]);
    }

    public function test_guest_booking_for_someone_else_stores_actual_patient_separately(): void
    {
        Mail::fake();

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        $component = Livewire::test(BookAppointment::class)
            ->set('booking_for', 'someone_else')
            ->set('first_name', 'Parent')
            ->set('last_name', 'Booker')
            ->set('email', 'parent@example.com')
            ->set('contact_number', '09123456789')
            ->set('patient_first_name', 'Jamie')
            ->set('patient_last_name', 'Booker')
            ->set('patient_birth_date', '2015-05-01')
            ->set('relationship_to_patient', 'Mother')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '10:00:00')
            ->set('booking_agreement', true)
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertSet('guestOtpStepActive', true);

        $component
            ->set('guestEmailOtpHash', Hash::make('123456'))
            ->set('guestEmailOtpTargetEmail', 'parent@example.com')
            ->set('guestEmailOtpExpiresAt', now()->addMinutes(5)->toDateTimeString());

        $component
            ->set('guestEmailOtp', '123456')
            ->call('verifyGuestEmailOtp')
            ->assertRedirect('/book');

        $this->assertDatabaseHas('appointments', [
            'requester_first_name' => 'Parent',
            'requester_last_name' => 'Booker',
            'requester_email' => 'parent@example.com',
            'requester_contact_number' => '09123456789',
            'booking_for_other' => true,
            'requested_patient_first_name' => 'Jamie',
            'requested_patient_last_name' => 'Booker',
            'requested_patient_birth_date' => '2015-05-01',
            'requester_relationship_to_patient' => 'Mother',
        ]);
    }

    public function test_guest_booking_requires_otp_verification_before_submit(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('booking_agreement', true)
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertSet('guestOtpStepActive', true);

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_guest_booking_rejects_upcoming_request_when_slot_is_already_full(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        $date = now()->addDay()->format('Y-m-d');
        $slot = '09:00:00';
        $appointmentDateTime = Carbon::parse($date.' '.$slot)->toDateTimeString();

        DB::table('appointments')->insert([
            [
                'patient_id' => null,
                'service_id' => 1,
                'appointment_date' => $appointmentDateTime,
                'status' => 'Scheduled',
                'requester_first_name' => 'First',
                'requester_last_name' => 'Booked',
                'requester_contact_number' => '09111111111',
                'requester_email' => 'first@example.com',
                'modified_by' => 'STAFF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_id' => null,
                'service_id' => 1,
                'appointment_date' => $appointmentDateTime,
                'status' => 'Waiting',
                'requester_first_name' => 'Second',
                'requester_last_name' => 'Booked',
                'requester_contact_number' => '09222222222',
                'requester_email' => 'second@example.com',
                'modified_by' => 'STAFF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', $date)
            ->set('selectedSlot', $slot)
            ->set('booking_agreement', true)
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertHasErrors('selectedSlot');

        $this->assertDatabaseCount('appointments', 2);
    }

    public function test_guest_booking_rejects_upcoming_request_when_slot_is_blocked(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

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
        $slot = '10:00:00';

        DB::table('blocked_slots')->insert([
            'date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'reason' => 'Clinic unavailable',
            'created_by' => 'STAFF',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', $date)
            ->set('selectedSlot', $slot)
            ->set('booking_agreement', true)
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertHasErrors('selectedSlot');

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_guest_booking_rejects_upcoming_request_when_slot_reaches_five_active_requests(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        $date = now()->addDay()->format('Y-m-d');
        $slot = '11:00:00';
        $appointmentDateTime = Carbon::parse($date.' '.$slot)->toDateTimeString();

        $appointments = [];
        for ($i = 1; $i <= 5; $i++) {
            $appointments[] = [
                'patient_id' => null,
                'service_id' => 1,
                'appointment_date' => $appointmentDateTime,
                'status' => 'Pending',
                'requester_first_name' => 'Guest'.$i,
                'requester_last_name' => 'Patient',
                'requester_contact_number' => '09'.str_pad((string) $i, 9, '0', STR_PAD_LEFT),
                'requester_email' => "guest{$i}@example.com",
                'modified_by' => 'GUEST',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('appointments')->insert($appointments);

        Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'newguest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', $date)
            ->set('selectedSlot', $slot)
            ->set('booking_agreement', true)
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertHasErrors('selectedSlot');

        $this->assertDatabaseCount('appointments', 5);
    }

    public function test_guest_booking_email_otp_expires_in_five_minutes(): void
    {
        Mail::fake();

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        $component = Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('patient_birth_date', now()->subYears(25)->toDateString())
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('booking_agreement', true)
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertSet('guestOtpStepActive', true);

        $expiresAt = Carbon::parse((string) $component->get('guestEmailOtpExpiresAt'));

        $this->assertTrue(now()->addMinutes(4)->lt($expiresAt));
        $this->assertTrue(now()->addMinutes(6)->gt($expiresAt));
    }
}
