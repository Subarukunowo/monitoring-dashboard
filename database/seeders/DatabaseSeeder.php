<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterDataSeeder::class, // Area, Pengawas, JenisPekerjaan, Status
            LaporanSeeder::class,    // Pelanggan + Laporan
        ]);
    }
}