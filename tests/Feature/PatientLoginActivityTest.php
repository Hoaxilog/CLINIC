<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PatientLoginActivityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('role')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('google_id')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->string('event')->nullable();
            $table->timestamps();
        });
    }

    public function test_successful_patient_login_records_login_activity(): void
    {
        $userId = DB::table('users')->insertGetId([
            'username' => 'patient.login',
            'email' => 'patient-login@example.com',
            'password' => Hash::make('secret123'),
            'role' => User::ROLE_PATIENT,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'patient-login@example.com',
            'password' => 'secret123',
        ], [
            'REMOTE_ADDR' => '203.0.113.15',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36',
        ]);

        $response->assertRedirect('/patient/dashboard');

        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $userId,
            'subject_type' => User::class,
            'causer_id' => $userId,
            'causer_type' => User::class,
            'event' => 'user_logged_in',
            'description' => 'Logged In',
        ]);

        $properties = json_decode((string) DB::table('activity_log')->where('subject_id', $userId)->value('properties'), true);

        $this->assertSame('203.0.113.15', data_get($properties, 'attributes.ip_address'));
        $this->assertSame('Chrome', data_get($properties, 'attributes.browser'));
        $this->assertSame('Windows', data_get($properties, 'attributes.platform'));
        $this->assertSame('Desktop', data_get($properties, 'attributes.device'));
        $this->assertNotEmpty(data_get($properties, 'attributes.user_agent'));
    }

    public function test_successful_patient_login_is_only_logged_once_per_day(): void
    {
        $userId = DB::table('users')->insertGetId([
            'username' => 'patient.once',
            'email' => 'patient-once@example.com',
            'password' => Hash::make('secret123'),
            'role' => User::ROLE_PATIENT,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activity_log')->insert([
            'log_name' => 'default',
            'description' => 'Logged In',
            'subject_id' => $userId,
            'subject_type' => User::class,
            'causer_id' => $userId,
            'causer_type' => User::class,
            'properties' => json_encode([
                'attributes' => [
                    'login_at' => now()->startOfDay()->addHours(8)->toDateTimeString(),
                ],
            ]),
            'batch_uuid' => null,
            'event' => 'user_logged_in',
            'created_at' => now()->startOfDay()->addHours(8),
            'updated_at' => now()->startOfDay()->addHours(8),
        ]);

        $response = $this->post('/login', [
            'email' => 'patient-once@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/patient/dashboard');

        $this->assertSame(
            1,
            DB::table('activity_log')
                ->where('event', 'user_logged_in')
                ->where('subject_id', $userId)
                ->count()
        );
    }
}
