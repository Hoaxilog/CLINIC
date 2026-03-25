<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        if (! DB::table('roles')->where('id', 4)->exists()) {
            DB::table('roles')->insert([
                'id' => 4,
                'role_name' => 'admin',
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')
            ->where('id', 4)
            ->where('role_name', 'admin')
            ->delete();
    }
};
