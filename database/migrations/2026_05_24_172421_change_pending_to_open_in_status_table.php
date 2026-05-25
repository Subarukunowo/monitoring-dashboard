<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🔹 STEP 1: Ubah ENUM dulu - tambahkan 'open', hapus 'pending'
        DB::statement("ALTER TABLE `status` CHANGE `status_kerja` `status_kerja` 
            ENUM('open','on_progress','completed') NOT NULL");
        
        // 🔹 STEP 2: Update data dari 'pending' → 'open'
        DB::table('status')->where('status_kerja', 'pending')->update(['status_kerja' => 'open']);
        
        // 🔹 STEP 3: Update juga di tabel laporan yang terkait
        // Cari ID status 'open' yang baru
        $openStatusId = DB::table('status')->where('status_kerja', 'open')->value('id');
        $pendingStatusId = DB::table('status')->where('status_kerja', 'pending')->value('id');
        
        if ($openStatusId && $pendingStatusId) {
            DB::table('laporan')
                ->where('id_status', $pendingStatusId)
                ->update(['id_status' => $openStatusId]);
        }
    }

    public function down(): void
    {
        // 🔹 Rollback: Update data dulu
        DB::table('status')->where('status_kerja', 'open')->update(['status_kerja' => 'pending']);
        
        // 🔹 Lalu kembalikan ENUM ke nilai lama
        DB::statement("ALTER TABLE `status` CHANGE `status_kerja` `status_kerja` 
            ENUM('pending','on_progress','completed') NOT NULL");
    }
};