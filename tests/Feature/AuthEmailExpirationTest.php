<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthEmailExpirationTest extends TestCase
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
    }

    public function test_login_otp_session_expires_in_three_minutes(): void
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

        $pendingOtp = session('pending_otp_login');

        $this->assertNotNull($pendingOtp);
        $this->assertTrue(now()->addMinutes(2)->lt($pendingOtp['expires_at']));
        $this->assertTrue(now()->addMinutes(4)->gt($pendingOtp['expires_at']));
    }

    public function test_email_verification_link_expires_after_three_minutes(): void
    {
        $userId = DB::table('users')->insertGetId([
            'username' => 'patient@example.com',
            'email' => 'patient@example.com',
            'password' => Hash::make('secret123'),
            'role' => 3,
            'email_verified_at' => null,
            'google_id' => null,
            'verification_token' => 'expired-verification-token',
            'created_at' => now()->subMinutes(4),
            'updated_at' => now()->subMinutes(4),
        ]);

        $this->get(route('verification.verify', ['id' => $userId, 'token' => 'expired-verification-token']))
            ->assertRedirect(route('verification.expired'));

        $this->assertNull(
            DB::table('users')->where('id', $userId)->value('verification_token')
        );
    }
}
