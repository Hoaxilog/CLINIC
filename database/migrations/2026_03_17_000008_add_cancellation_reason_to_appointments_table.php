<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('appointments') || Schema::hasColumn('appointments', 'cancellation_reason')) {
            return;
        }

        DB::statement('ALTER TABLE appointments ADD cancellation_reason TEXT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointments') || ! Schema::hasColumn('appointments', 'cancellation_reason')) {
            return;
        }

        DB::statement('ALTER TABLE appointments DROP COLUMN cancellation_reason');
    }
};
