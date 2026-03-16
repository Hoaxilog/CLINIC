<?php

namespace Tests\Feature;

use App\Livewire\appointment\BookAppointment;
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
            $table->string('requester_contact_number')->nullable();
            $table->string('requester_email')->nullable();
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

    public function test_book_route_is_registered_and_public(): void
    {
        $route = Route::getRoutes()->getByName('book');

        $this->assertNotNull($route);
        $this->assertSame(['GET', 'HEAD'], $route->methods());
        $this->assertSame('book', $route->uri());
        $this->assertNotContains('auth', $route->middleware());
    }

    public function test_guest_booking_requires_captcha_token(): void
    {
        Livewire::test(BookAppointment::class)
            ->set('first_name', 'Guest')
            ->set('last_name', 'Patient')
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('guestEmailOtpVerified', true)
            ->set('guestEmailOtpTargetEmail', 'guest@example.com')
            ->call('bookAppointment')
            ->assertHasErrors('recaptcha');

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
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertSet('guestOtpStepActive', true);

        $component
            ->set('guestEmailOtpHash', Hash::make('123456'))
            ->set('guestEmailOtpTargetEmail', 'guest@example.com');

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
            'requester_email' => 'guest@example.com',
            'requester_contact_number' => '09123456789',
            'modified_by' => 'GUEST',
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
            ->set('email', 'guest@example.com')
            ->set('contact_number', '09123456789')
            ->set('service_id', 1)
            ->set('selectedDate', now()->addDay()->format('Y-m-d'))
            ->set('selectedSlot', '09:00:00')
            ->set('recaptchaToken', 'test-token')
            ->call('bookAppointment')
            ->assertSet('guestOtpStepActive', true);

        $this->assertDatabaseCount('appointments', 0);
    }
}
