<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('roles')->where('id', 4)->exists()) {
            DB::table('roles')->insert([
                'id' => 4,
                'role_name' => 'admin',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('id', 4)
            ->where('role_name', 'admin')
            ->delete();
    }
};
