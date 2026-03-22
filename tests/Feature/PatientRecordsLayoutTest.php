<?php

namespace Tests\Feature;

use App\Livewire\Patient\PatientRecords;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class PatientRecordsLayoutTest extends TestCase
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
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
            $table->timestamps();
        });

        $staffUser = User::query()->create([
            'username' => 'staff.member',
            'email' => 'staff@example.com',
            'password' => bcrypt('secret123'),
            'role' => User::ROLE_STAFF,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($staffUser);
    }

    public function test_patient_records_list_has_full_viewport_minimum_height(): void
    {
        Livewire::test(PatientRecords::class)
            ->assertSeeHtml('min-h-[100vh]');
    }
}