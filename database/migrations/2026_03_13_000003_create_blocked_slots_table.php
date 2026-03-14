<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('chair_id')->nullable();
            $table->string('reason')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index(['date', 'start_time']);
            $table->index(['date', 'end_time']);
            $table->index('chair_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_slots');
    }
};
