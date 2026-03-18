<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->unsignedBigInteger('role')->nullable();
            $table->string('google_id')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
        });
    }

    public function test_profile_update_no_longer_requires_contact_or_username_fields(): void
    {
        $user = User::query()->create([
            'username' => 'staff.member',
            'email' => 'staff@example.com',
            'role' => 2,
            'password' => Hash::make('secret123'),
        ]);

        $this->actingAs($user)
            ->from(route('profile.index'))
            ->patch(route('profile.update'))
            ->assertRedirect(route('profile.index'))
            ->assertSessionHas('success', 'Account details are managed by the clinic.');
    }
}
