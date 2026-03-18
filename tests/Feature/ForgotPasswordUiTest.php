<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ForgotPasswordUiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_forgot_password_page_renders_email_error_hooks(): void
    {
        $response = $this->from(route('password.forgot'))->post(route('password.email'), [
            'email' => '',
            'g-recaptcha-response' => '',
        ]);

        $response->assertRedirect(route('password.forgot'));
        $response->assertSessionHasErrors(['email', 'g-recaptcha-response']);

        $this->get(route('password.forgot'))
            ->assertOk()
            ->assertSee('id="forgot-password-email"', false);
    }

    public function test_forgot_password_page_displays_email_error_markup_after_validation_failure(): void
    {
        $response = $this->from(route('password.forgot'))->post(route('password.email'), [
            'email' => 'invalid-email',
            'g-recaptcha-response' => 'captcha-token',
        ]);

        $response->assertRedirect(route('password.forgot'));
        $response->assertSessionHasErrors(['email']);

        $this->followingRedirects()
            ->from(route('password.forgot'))
            ->post(route('password.email'), [
                'email' => 'invalid-email',
                'g-recaptcha-response' => 'captcha-token',
            ])
            ->assertSee('id="forgot-password-email-error"', false)
            ->assertSee('border-red-500', false);
    }

    public function test_reset_password_page_renders_password_visibility_controls(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => 'patient@example.com',
            'token' => 'valid-token',
            'created_at' => now(),
        ]);

        $this->get(route('password.reset', ['token' => 'valid-token', 'email' => 'patient@example.com']))
            ->assertOk()
            ->assertSee('id="reset-password"', false)
            ->assertSee('id="reset-password-confirmation"', false)
            ->assertSee('toggleResetPassword', false);
    }

    public function test_reset_password_validation_returns_password_errors(): void
    {
        DB::table('users')->insert([
            'email' => 'patient@example.com',
            'password' => Hash::make('secret123'),
            'google_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => 'patient@example.com',
            'token' => 'test-token',
            'created_at' => now(),
        ]);

        $this->followingRedirects()
            ->from('/reset-password/test-token?email=patient@example.com')
            ->post(route('password.update'), [
                'email' => 'patient@example.com',
                'password' => 'short',
                'password_confirmation' => 'different',
                'token' => 'test-token',
            ])
            ->assertSee('id="reset-password-error"', false)
            ->assertSee('id="reset-password-confirmation-error"', false)
            ->assertSee('border-red-500', false);
    }

    public function test_reset_password_link_expires_after_five_minutes(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => 'patient@example.com',
            'token' => 'expired-token',
            'created_at' => now()->subMinutes(6),
        ]);

        $this->get(route('password.reset', ['token' => 'expired-token', 'email' => 'patient@example.com']))
            ->assertRedirect('/forgot-password');
    }
}
