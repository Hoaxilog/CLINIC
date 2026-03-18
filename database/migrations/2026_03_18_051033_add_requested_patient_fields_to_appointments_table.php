<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            if (! Schema::hasColumn('appointments', 'booking_for_other')) {
                $table->boolean('booking_for_other')->default(false)->after('requester_email');
            }

            if (! Schema::hasColumn('appointments', 'requested_patient_first_name')) {
                $table->string('requested_patient_first_name')->nullable()->after('booking_for_other');
            }

            if (! Schema::hasColumn('appointments', 'requested_patient_last_name')) {
                $table->string('requested_patient_last_name')->nullable()->after('requested_patient_first_name');
            }

            if (! Schema::hasColumn('appointments', 'requested_patient_birth_date')) {
                $table->date('requested_patient_birth_date')->nullable()->after('requested_patient_last_name');
            }

            if (! Schema::hasColumn('appointments', 'requester_relationship_to_patient')) {
                $table->string('requester_relationship_to_patient')->nullable()->after('requested_patient_birth_date');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $columns = collect([
                'requester_relationship_to_patient',
                'requested_patient_birth_date',
                'requested_patient_last_name',
                'requested_patient_first_name',
                'booking_for_other',
            ])->filter(fn (string $column) => Schema::hasColumn('appointments', $column))->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
