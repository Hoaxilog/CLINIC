<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'patient_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('patient_id')->nullable()->index()->after('email');
            });
        }

        if (! Schema::hasTable('patients')) {
            return;
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('patient_id', 'users_patient_id_foreign')
                    ->references('id')
                    ->on('patients')
                    ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // Keep migration resilient across existing databases with manual schema differences.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'patient_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign('users_patient_id_foreign');
                });
            } catch (\Throwable $e) {
                // Ignore if foreign key is missing or uses a different name.
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('patient_id');
            });
        }
    }
};
