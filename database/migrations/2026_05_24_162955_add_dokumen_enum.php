<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah nilai 'dokumen' ke ENUM tipe di tabel dokumen.
     * Nilai existing ('pekerjaan', 'administrasi') TIDAK dihapus.
     */
    public function up(): void
    {
        // Gunakan raw ALTER TABLE karena ->change() di ENUM
        // membutuhkan doctrine/dbal dan tetap aman untuk data existing.
        DB::statement("
            ALTER TABLE `dokumen`
            MODIFY COLUMN `tipe`
            ENUM('pekerjaan', 'administrasi', 'dokumen') NOT NULL
        ");
    }

    public function down(): void
    {
        // Kembalikan ke enum lama — hanya aman jika tidak ada row dengan tipe='dokumen'
        DB::statement("
            ALTER TABLE `dokumen`
            MODIFY COLUMN `tipe`
            ENUM('pekerjaan', 'administrasi') NOT NULL
        ");
    }
};