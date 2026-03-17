<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments') || Schema::hasColumn('appointments', 'requester_birth_date')) {
            return;
        }

        DB::statement('ALTER TABLE appointments ADD requester_birth_date DATE NULL AFTER requester_last_name');
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments') || ! Schema::hasColumn('appointments', 'requester_birth_date')) {
            return;
        }

        DB::statement('ALTER TABLE appointments DROP COLUMN requester_birth_date');
    }
};
