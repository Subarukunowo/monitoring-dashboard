<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('status')) {
            DB::statement("ALTER TABLE `status` CHANGE `status_kerja` `status_kerja`
                ENUM('pending', 'on_progress', 'on_hold', 'completed') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('status')) {
            DB::statement("ALTER TABLE `status` CHANGE `status_kerja` `status_kerja`
                ENUM('pending', 'on_progress', 'on_hold', 'completed') NOT NULL");
        }
    }
};
