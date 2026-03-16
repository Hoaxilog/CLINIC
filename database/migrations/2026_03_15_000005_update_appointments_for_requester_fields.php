<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('appointments')) {
            return;
        }

        // Allow pending online requests to exist without immediate patient-record linkage.
        DB::statement('ALTER TABLE appointments MODIFY patient_id INT NULL');

        if (!Schema::hasColumn('appointments', 'requester_user_id')) {
            DB::statement('ALTER TABLE appointments ADD requester_user_id BIGINT NULL');
            DB::statement('ALTER TABLE appointments ADD INDEX appointments_requester_user_id_index (requester_user_id)');
        }

        if (!Schema::hasColumn('appointments', 'requester_first_name')) {
            DB::statement('ALTER TABLE appointments ADD requester_first_name VARCHAR(100) NULL');
        }

        if (!Schema::hasColumn('appointments', 'requester_last_name')) {
            DB::statement('ALTER TABLE appointments ADD requester_last_name VARCHAR(100) NULL');
        }

        if (!Schema::hasColumn('appointments', 'requester_contact_number')) {
            DB::statement('ALTER TABLE appointments ADD requester_contact_number VARCHAR(30) NULL');
        }

        if (!Schema::hasColumn('appointments', 'requester_email')) {
            DB::statement('ALTER TABLE appointments ADD requester_email VARCHAR(255) NULL');
            DB::statement('ALTER TABLE appointments ADD INDEX appointments_requester_email_index (requester_email)');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('appointments')) {
            return;
        }

        if (Schema::hasColumn('appointments', 'requester_email')) {
            DB::statement('ALTER TABLE appointments DROP COLUMN requester_email');
        }

        if (Schema::hasColumn('appointments', 'requester_contact_number')) {
            DB::statement('ALTER TABLE appointments DROP COLUMN requester_contact_number');
        }

        if (Schema::hasColumn('appointments', 'requester_last_name')) {
            DB::statement('ALTER TABLE appointments DROP COLUMN requester_last_name');
        }

        if (Schema::hasColumn('appointments', 'requester_first_name')) {
            DB::statement('ALTER TABLE appointments DROP COLUMN requester_first_name');
        }

        if (Schema::hasColumn('appointments', 'requester_user_id')) {
            DB::statement('ALTER TABLE appointments DROP COLUMN requester_user_id');
        }
    }
};
