<?php

namespace Database\Seeders;

use App\Models\BqItem;
use App\Models\Laporan;
use Illuminate\Database\Seeder;

class BqSeeder extends Seeder
{
    public function run(): void
    {
        $laporansWithRab = Laporan::whereNotNull('nilai_rab')->get();

        foreach ($laporansWithRab as $laporan) {
            if ($laporan->bqItems()->exists()) {
                continue;
            }

            $total = (float) $laporan->nilai_rab;
            $material = round($total * 0.65, 2);
            $jasa = round($total - $material, 2);

            BqItem::create([
                'id_laporan' => $laporan->id,
                'kode_material' => 'MAT-JTR-01',
                'uraian' => 'Material Tiang & Aksesoris JTR',
                'kategori' => 'Material',
                'satuan' => 'Set',
                'volume' => 1,
                'harga_satuan' => $material,
                'subtotal' => $material,
            ]);

            BqItem::create([
                'id_laporan' => $laporan->id,
                'kode_material' => 'JASA-HAR-01',
                'uraian' => 'Jasa Pemasangan & Penarikan Jaringan',
                'kategori' => 'Jasa',
                'satuan' => 'Ls',
                'volume' => 1,
                'harga_satuan' => $jasa,
                'subtotal' => $jasa,
            ]);
        }
    }
}
