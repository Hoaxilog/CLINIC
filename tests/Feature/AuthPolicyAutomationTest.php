<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthPolicyAutomationTest extends TestCase
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
            $table->string('google_id')->nullable();
            $table->string('verification_token')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_login_otp_allows_three_resends_after_the_initial_send(): void
    {
        Mail::fake();

        DB::table('users')->insert([
            'username' => 'admin@example.com',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'role' => 1,
            'email_verified_at' => now(),
            'google_id' => null,
            'verification_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ])->assertRedirect(route('login.otp'));

        $this->assertSame(0, session('pending_otp_login')['resend_count']);

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->travel(61)->seconds();

            $response = $this->from(route('login.otp'))
                ->post(route('login.otp.resend'))
                ->assertSessionDoesntHaveErrors();

            $this->assertNull(session('failed'));
            $this->assertNotNull(session('success'));
            $response->assertSessionHas('pending_otp_login.resend_count', $attempt);
        }

        $this->travel(61)->seconds();

        $this->from(route('login.otp'))
            ->post(route('login.otp.resend'))
            ->assertSessionHas('failed');

        $this->assertStringContainsString(
            'OTP resend limit reached. Try again in',
            session('failed')
        );
    }

    public function test_login_otp_resend_lock_applies_across_new_login_sessions_until_it_expires(): void
    {
        Mail::fake();

        DB::table('users')->insert([
            'username' => 'locked-resend@example.com',
            'email' => 'locked-resend@example.com',
            'password' => Hash::make('secret123'),
            'role' => 1,
            'email_verified_at' => now(),
            'google_id' => null,
            'verification_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post('/login', [
            'email' => 'locked-resend@example.com',
            'password' => 'secret123',
        ])->assertRedirect(route('login.otp'));

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->travel(61)->seconds();

            $this->from(route('login.otp'))
                ->post(route('login.otp.resend'))
                ->assertSessionDoesntHaveErrors();
        }

        $this->flushSession();

        $this->post('/login', [
            'email' => 'locked-resend@example.com',
            'password' => 'secret123',
        ])->assertRedirect(route('login'));

        $this->assertSame(
            'OTP resend limit reached. Try again in 10 minute(s).',
            session('failed')
        );

        $this->travel(601)->seconds();

        $this->post('/login', [
            'email' => 'locked-resend@example.com',
            'password' => 'secret123',
        ])->assertRedirect(route('login.otp'));
    }

    public function test_login_otp_wrong_guesses_are_blocked_after_five_attempts_for_five_minutes(): void
    {
        Mail::fake();

        DB::table('users')->insert([
            'username' => 'staff@example.com',
            'email' => 'staff@example.com',
            'password' => Hash::make('secret123'),
            'role' => 2,
            'email_verified_at' => now(),
            'google_id' => null,
            'verification_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post('/login', [
            'email' => 'staff@example.com',
            'password' => 'secret123',
        ])->assertRedirect(route('login.otp'));

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from(route('login.otp'))
                ->post(route('login.otp.verify'), ['otp' => '000000'])
                ->assertSessionHasErrors(['otp' => 'Invalid OTP code.']);
        }

        $this->from(route('login.otp'))
            ->post(route('login.otp.verify'), ['otp' => '000000'])
            ->assertSessionHasErrors(['otp']);

        $this->assertStringContainsString(
            'Too many OTP attempts. Try again in 5 minute(s).',
            session('errors')->first('otp')
        );
    }

    public function test_login_otp_block_applies_across_different_sessions_with_the_same_ip(): void
    {
        Mail::fake();

        DB::table('users')->insert([
            'username' => 'locked@example.com',
            'email' => 'locked@example.com',
            'password' => Hash::make('secret123'),
            'role' => 1,
            'email_verified_at' => now(),
            'google_id' => null,
            'verification_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clientIp = '203.0.113.55';

        $this->withServerVariables(['REMOTE_ADDR' => $clientIp])
            ->post('/login', [
                'email' => 'locked@example.com',
                'password' => 'secret123',
            ])->assertRedirect(route('login.otp'));

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => $clientIp])
                ->from(route('login.otp'))
                ->post(route('login.otp.verify'), ['otp' => '000000'])
                ->assertSessionHasErrors(['otp' => 'Invalid OTP code.']);
        }

        $this->flushSession();

        $this->withServerVariables(['REMOTE_ADDR' => $clientIp])
            ->post('/login', [
                'email' => 'locked@example.com',
                'password' => 'secret123',
            ])->assertRedirect(route('login.otp'));

        $this->withServerVariables(['REMOTE_ADDR' => $clientIp])
            ->from(route('login.otp'))
            ->post(route('login.otp.verify'), ['otp' => '000000'])
            ->assertSessionHasErrors(['otp']);

        $this->assertStringContainsString(
            'Too many OTP attempts. Try again in 5 minute(s).',
            session('errors')->first('otp')
        );
    }

    public function test_verification_resend_has_one_minute_cooldown_and_three_resends(): void
    {
        Mail::fake();

        DB::table('users')->insert([
            'username' => 'patient@example.com',
            'email' => 'patient@example.com',
            'password' => Hash::make('secret123'),
            'role' => 3,
            'email_verified_at' => null,
            'google_id' => null,
            'verification_token' => 'initial-token',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->from(route('verification.notice'))->post(route('verification.resend'), [
            'email' => 'patient@example.com',
        ])->assertSessionHas('success');

        $this->from(route('verification.notice'))->post(route('verification.resend'), [
            'email' => 'patient@example.com',
        ])->assertSessionHasErrors(['email']);

        $this->assertStringContainsString(
            'Please wait',
            session('errors')->first('email')
        );

        for ($attempt = 2; $attempt <= 3; $attempt++) {
            $this->travel(61)->seconds();

            $this->from(route('verification.notice'))->post(route('verification.resend'), [
                'email' => 'patient@example.com',
            ])->assertSessionHas('success');
        }

        $this->travel(61)->seconds();

        $this->from(route('verification.notice'))->post(route('verification.resend'), [
            'email' => 'patient@example.com',
        ])->assertSessionHasErrors(['email']);

        $this->assertSame(
            'You have reached the maximum of 3 resend attempts for this verification flow.',
            session('errors')->first('email')
        );
    }

    public function test_password_reset_request_has_one_minute_cooldown_and_three_resends_after_initial_send(): void
    {
        Mail::fake();
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        DB::table('users')->insert([
            'email' => 'recover@example.com',
            'password' => Hash::make('secret123'),
            'google_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'email' => 'recover@example.com',
            'g-recaptcha-response' => 'captcha-token',
        ];

        $this->from(route('password.forgot'))
            ->post(route('password.email'), $payload)
            ->assertSessionHas('reset_email', 'recover@example.com');

        $this->from(route('password.forgot'))
            ->post(route('password.email'), $payload)
            ->assertSessionHasErrors(['email']);

        $this->assertStringContainsString(
            'Please wait',
            session('errors')->first('email')
        );

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->travel(61)->seconds();

            $this->from(route('password.forgot'))
                ->post(route('password.email'), $payload)
                ->assertSessionHas('reset_email', 'recover@example.com');
        }

        $this->travel(61)->seconds();

        $this->from(route('password.forgot'))
            ->post(route('password.email'), $payload)
            ->assertSessionHasErrors(['email']);

        $this->assertSame(
            'You have reached the maximum of 3 resend attempts for this reset flow.',
            session('errors')->first('email')
        );
    }

    public function test_unverified_user_can_reset_password_but_is_still_redirected_to_verification_on_login(): void
    {
        Mail::fake();
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        DB::table('users')->insert([
            'username' => 'pending@example.com',
            'email' => 'pending@example.com',
            'password' => Hash::make('old-password'),
            'role' => 3,
            'email_verified_at' => null,
            'google_id' => null,
            'verification_token' => 'verify-me',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post('/login', [
            'email' => 'pending@example.com',
            'password' => 'old-password',
        ])->assertRedirect(route('verification.notice'));

        $this->from(route('password.forgot'))
            ->post(route('password.email'), [
                'email' => 'pending@example.com',
                'g-recaptcha-response' => 'captcha-token',
            ])->assertSessionHas('reset_email', 'pending@example.com');

        $token = DB::table('password_reset_tokens')
            ->where('email', 'pending@example.com')
            ->value('token');

        $this->assertNotNull($token);

        $this->post(route('password.update'), [
            'email' => 'pending@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
            'token' => $token,
        ])->assertRedirect('/login');

        $this->post('/login', [
            'email' => 'pending@example.com',
            'password' => 'new-password123',
        ])->assertRedirect(route('verification.notice'));
    }
}
