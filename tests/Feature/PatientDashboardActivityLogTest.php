<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PatientDashboardActivityLogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            .DIRECTORY_SEPARATOR.'clinic-testing-views-'.uniqid('patient-dashboard-', true);
        if (! is_dir($compiledPath)) {
            mkdir($compiledPath, 0777, true);
        }
        config(['view.compiled' => $compiledPath]);
        $this->app->forgetInstance('blade.compiler');
        $this->app->forgetInstance('view.engine.resolver');
        $this->app->forgetInstance('view');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->unsignedBigInteger('role')->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('requester_user_id')->nullable();
            $table->dateTime('appointment_date');
            $table->string('status')->default('Pending');
            $table->string('requester_email')->nullable();
            $table->string('requester_first_name')->nullable();
            $table->string('requester_last_name')->nullable();
            $table->timestamps();
        });

        Schema::create('treatment_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('treatment')->nullable();
            $table->decimal('amount_charged', 10, 2)->nullable();
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

    public function test_patient_dashboard_shows_recent_login_log_entries_only(): void
    {
        $patientUserId = DB::table('users')->insertGetId([
            'username' => 'patient.one',
            'email' => 'patient@example.com',
            'first_name' => 'Pat',
            'last_name' => 'Ient',
            'mobile_number' => '09123456789',
            'role' => User::ROLE_PATIENT,
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activity_log')->insert([
            [
                'description' => 'Updated User Account',
                'subject_id' => $patientUserId,
                'subject_type' => 'App\\Models\\User',
                'causer_id' => $patientUserId,
                'causer_type' => 'App\\Models\\User',
                'properties' => json_encode(['attributes' => ['mobile_number' => '09999999999']]),
                'event' => 'user_updated',
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
            [
                'description' => 'Logged In',
                'subject_id' => $patientUserId,
                'subject_type' => 'App\\Models\\User',
                'causer_id' => $patientUserId,
                'causer_type' => 'App\\Models\\User',
                'properties' => json_encode(['attributes' => [
                    'login_at' => now()->subHours(4)->toDateTimeString(),
                    'ip_address' => '203.0.113.41',
                    'browser' => 'Chrome',
                    'platform' => 'Windows',
                    'device' => 'Desktop',
                ]]),
                'event' => 'user_logged_in',
                'created_at' => now()->subHours(4),
                'updated_at' => now()->subHours(4),
            ],
            [
                'description' => 'Logged In',
                'subject_id' => $patientUserId,
                'subject_type' => 'App\\Models\\User',
                'causer_id' => 99,
                'causer_type' => 'App\\Models\\User',
                'properties' => json_encode(['attributes' => [
                    'login_at' => now()->subHour()->toDateTimeString(),
                    'ip_address' => '198.51.100.22',
                    'browser' => 'Safari',
                    'platform' => 'iPhone',
                    'device' => 'Mobile',
                ]]),
                'event' => 'user_logged_in',
                'created_at' => now()->subHour(),
                'updated_at' => now()->subHour(),
            ],
        ]);

        $patient = User::query()->findOrFail($patientUserId);

        $response = $this->actingAs($patient)->get(route('patient.dashboard'));

        $response->assertOk();
        $response->assertSee('Login Logs');
        $response->assertSee('Logged in');
        $response->assertSee('Successfully logged into your account.');
        $response->assertSee(now()->subHours(4)->format('Y-m-d'));
        $response->assertSee('Chrome');
        $response->assertSee('Windows');
        $response->assertSee('Desktop');
        $response->assertSee('IP: 203.0.113.41');
        $response->assertDontSee('Recent updates related to your appointments, records, and account.');
        $response->assertDontSee('Updated User Account');
    }
}
