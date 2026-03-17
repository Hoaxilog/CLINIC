<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 120);
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('title', 255);
            $table->text('message');
            $table->string('link', 255);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('cleared_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at'], 'notifications_user_read_index');
            $table->index(['user_id', 'cleared_at'], 'notifications_user_cleared_index');
            $table->index(['type', 'created_at'], 'notifications_type_created_at_index');
            $table->index('user_id', 'notifications_user_id_index');
            $table->index('appointment_id', 'notifications_appointment_id_index');
            $table->index('actor_user_id', 'notifications_actor_user_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
