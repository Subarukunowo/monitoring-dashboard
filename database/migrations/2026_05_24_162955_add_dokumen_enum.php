<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('dokumen')) {
            DB::statement("
                ALTER TABLE `dokumen`
                MODIFY COLUMN `tipe`
                ENUM('pekerjaan', 'administrasi', 'dokumen') NOT NULL
            ");
        } elseif (Schema::hasTable('dokumen')) {
            Schema::table('dokumen', function (Blueprint $table) {
                $table->string('tipe', 30)->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('dokumen')) {
            DB::statement("
                ALTER TABLE `dokumen`
                MODIFY COLUMN `tipe`
                ENUM('pekerjaan', 'administrasi') NOT NULL
            ");
        }
    }
};
