<?php

namespace Tests\Feature;

use App\Http\Controllers\Dashboard;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardTodayAppointmentsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->dateTime('appointment_date');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('role')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->time('duration')->default('01:00:00');
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
            $table->text('description')->nullable();
            $table->string('event')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->timestamps();
        });

        DB::table('services')->insert([
            'id' => 1,
            'service_name' => 'Cleaning',
            'duration' => '01:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_dashboard_today_appointments_count_excludes_pending_requests(): void
    {
        DB::table('appointments')->insert([
            [
                'service_id' => 1,
                'appointment_date' => now()->setTime(9, 0)->toDateTimeString(),
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_id' => 1,
                'appointment_date' => now()->setTime(10, 0)->toDateTimeString(),
                'status' => 'Scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_id' => 1,
                'appointment_date' => now()->setTime(11, 0)->toDateTimeString(),
                'status' => 'Completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $view = app(Dashboard::class)->index();
        $data = $view->getData();

        $this->assertSame(2, $data['todayAppointmentsCount']);
        $this->assertSame(1, $data['todayCompletedCount']);
        $this->assertSame(1, $data['todayUpcomingCount']);
    }
}
