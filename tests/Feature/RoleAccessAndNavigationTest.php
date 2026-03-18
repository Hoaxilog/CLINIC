<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoleAccessAndNavigationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('role')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('google_id')->nullable();
            $table->string('verification_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->time('duration')->default('01:00:00');
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('dentist_id')->nullable();
            $table->unsignedBigInteger('requester_user_id')->nullable();
            $table->dateTime('appointment_date');
            $table->string('status')->default('Pending');
            $table->boolean('booking_for_other')->default(false);
            $table->string('requester_email')->nullable();
            $table->string('requester_first_name')->nullable();
            $table->string('requester_last_name')->nullable();
            $table->string('requester_contact_number')->nullable();
            $table->string('requester_relationship_to_patient')->nullable();
            $table->date('requester_birth_date')->nullable();
            $table->string('requested_patient_first_name')->nullable();
            $table->string('requested_patient_last_name')->nullable();
            $table->date('requested_patient_birth_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('treatment_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('treatment')->nullable();
            $table->decimal('amount_charged', 10, 2)->nullable();
            $table->decimal('cost_of_treatment', 10, 2)->nullable();
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

        DB::table('roles')->insert([
            ['id' => 1, 'role_name' => 'dentist'],
            ['id' => 2, 'role_name' => 'staff'],
            ['id' => 3, 'role_name' => 'patient'],
        ]);

        DB::table('services')->insert([
            'id' => 1,
            'service_name' => 'Cleaning',
            'duration' => '01:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_admin_role_migration_adds_role_four(): void
    {
        DB::table('roles')->where('id', 4)->delete();

        $migration = require base_path('database/migrations/2026_03_18_133220_add_admin_role_to_roles_table.php');
        $migration->up();

        $this->assertDatabaseHas('roles', [
            'id' => 4,
            'role_name' => 'admin',
        ]);
    }

    public function test_role_redirect_mapping_returns_expected_destinations(): void
    {
        $controller = app(LoginController::class);
        $redirectByRole = new \ReflectionMethod($controller, 'redirectByRole');
        $redirectByRole->setAccessible(true);

        $this->assertSame(url('/dashboard'), $redirectByRole->invoke($controller, User::ROLE_ADMIN)->getTargetUrl());
        $this->assertSame(url('/dashboard'), $redirectByRole->invoke($controller, User::ROLE_DENTIST)->getTargetUrl());
        $this->assertSame(url('/appointment'), $redirectByRole->invoke($controller, User::ROLE_STAFF)->getTargetUrl());
        $this->assertSame(url('/patient/dashboard'), $redirectByRole->invoke($controller, User::ROLE_PATIENT)->getTargetUrl());
    }

    public function test_admin_can_access_management_and_operational_routes(): void
    {
        $admin = $this->createUser(User::ROLE_ADMIN, 'admin@example.com');

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('activity-logs'))->assertOk();
        $this->actingAs($admin)->get(route('reports.index'))->assertOk();
        $this->actingAs($admin)->get(route('appointment'))->assertOk();
        $this->actingAs($admin)->get(route('queue'))->assertOk();
        $this->actingAs($admin)->get(route('patient-records'))->assertOk();
    }

    public function test_dentist_cannot_access_management_routes(): void
    {
        $dentist = $this->createUser(User::ROLE_DENTIST, 'dentist@example.com');

        $this->actingAs($dentist)->get(route('users.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($dentist)->get(route('activity-logs'))->assertRedirect(route('dashboard'));
        $this->actingAs($dentist)->get(route('reports.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($dentist)->get(route('appointment'))->assertOk();
    }

    public function test_staff_cannot_access_management_routes(): void
    {
        $staff = $this->createUser(User::ROLE_STAFF, 'staff@example.com');

        $this->actingAs($staff)->get(route('users.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($staff)->get(route('activity-logs'))->assertRedirect(route('dashboard'));
        $this->actingAs($staff)->get(route('reports.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($staff)->get(route('appointment'))->assertOk();
    }

    public function test_patient_cannot_access_internal_routes(): void
    {
        $patient = $this->createUser(User::ROLE_PATIENT, 'patient@example.com');

        $this->actingAs($patient)->get(route('users.index'))->assertRedirect(route('patient.dashboard'));
        $this->actingAs($patient)->get(route('reports.index'))->assertRedirect(route('patient.dashboard'));
        $this->actingAs($patient)->get(route('activity-logs'))->assertRedirect(route('patient.dashboard'));
        $this->actingAs($patient)->get(route('dashboard'))->assertRedirect(route('patient.dashboard'));
    }

    public function test_admin_navigation_shows_management_items(): void
    {
        $admin = $this->createUser(User::ROLE_ADMIN, 'admin.nav@example.com');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('TEJADA CLINIC');
        $response->assertSee('Dental Care');
        $response->assertDontSee('Tejada Dent');
        $response->assertSee('Management View');
        $response->assertSee('User Accounts');
        $response->assertSee('Reports');
        $response->assertSee('Activity Logs');
        $response->assertSee('Appointments');
        $response->assertSee('Queue');
        $response->assertSeeInOrder(['Patient', 'Records']);
    }

    public function test_dentist_navigation_hides_management_items(): void
    {
        $dentist = $this->createUser(User::ROLE_DENTIST, 'dentist.nav@example.com');

        $response = $this->actingAs($dentist)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Clinical View');
        $response->assertSee('Appointments');
        $response->assertSee('Queue');
        $response->assertSeeInOrder(['Patient', 'Records']);
        $response->assertDontSee('User Accounts');
        $response->assertDontSee('Activity Logs');
    }

    public function test_staff_navigation_hides_management_items(): void
    {
        $staff = $this->createUser(User::ROLE_STAFF, 'staff.nav@example.com');

        $response = $this->actingAs($staff)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('TEJADA CLINIC');
        $response->assertSee('Dental Care');
        $response->assertDontSee('Tejada Dent');
        $response->assertSee('Operations View');
        $response->assertSee('Appointments');
        $response->assertSee('Queue');
        $response->assertSeeInOrder(['Patient', 'Records']);
        $response->assertDontSee('User Accounts');
        $response->assertDontSee('Activity Logs');
    }

    public function test_dashboard_modes_are_distinct_for_admin_dentist_and_staff(): void
    {
        $admin = $this->createUser(User::ROLE_ADMIN, 'admin.mode@example.com');
        $dentist = $this->createUser(User::ROLE_DENTIST, 'dentist.mode@example.com');
        $staff = $this->createUser(User::ROLE_STAFF, 'staff.mode@example.com');

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertSee('Admin Dashboard')
            ->assertSee('Management Snapshot')
            ->assertDontSee('Clinical Priorities');

        $this->actingAs($dentist)->get(route('dashboard'))
            ->assertSee('Dentist Dashboard')
            ->assertSee('Clinical Priorities')
            ->assertDontSee('Management Snapshot');

        $this->actingAs($staff)->get(route('dashboard'))
            ->assertSee('Staff Dashboard')
            ->assertSee('Appointment Overview')
            ->assertDontSee('Clinical Priorities');
    }

    private function createUser(int $role, string $email): User
    {
        $userId = DB::table('users')->insertGetId([
            'username' => $email,
            'name' => $email,
            'email' => $email,
            'password' => bcrypt('secret123'),
            'role' => $role,
            'email_verified_at' => now(),
            'google_id' => null,
            'verification_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::query()->findOrFail($userId);
    }
}
