<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'requester_middle_name')) {
                $table->string('requester_middle_name')->nullable();
            }

            if (! Schema::hasColumn('appointments', 'requested_patient_middle_name')) {
                $table->string('requested_patient_middle_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'requested_patient_middle_name')) {
                $table->dropColumn('requested_patient_middle_name');
            }

            if (Schema::hasColumn('appointments', 'requester_middle_name')) {
                $table->dropColumn('requester_middle_name');
            }
        });
    }
};
