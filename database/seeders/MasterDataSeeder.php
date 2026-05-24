<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------
        // Area
        // ----------------------------------------
        DB::table('areas')->insert([
            ['nama_area' => 'Batam Centre', 'created_at' => now()],
            ['nama_area' => 'Nagoya',       'created_at' => now()],
            ['nama_area' => 'Tiban',        'created_at' => now()],
            ['nama_area' => 'Batu Aji',     'created_at' => now()],
        ]);

        // ----------------------------------------
        // Pengawas
        // ----------------------------------------
        DB::table('pengawas')->insert([
            ['nama' => 'Refendi', 'no_telepon' => null, 'created_at' => now()],
        ]);

        // ----------------------------------------
        // Jenis Pekerjaan
        // ----------------------------------------
        DB::table('jenis_pekerjaan')->insert([
            ['nama_jenis' => 'Geser JTR',                      'created_at' => now()],
            ['nama_jenis' => 'Geser KWH Meter Dan Geser Tiang','created_at' => now()],
            ['nama_jenis' => 'Geser Tiang',                    'created_at' => now()],
            ['nama_jenis' => 'Jaringan Drop',                  'created_at' => now()],
            ['nama_jenis' => 'Kabel Terkelupas',               'created_at' => now()],
            ['nama_jenis' => 'Perapian Kabel JTR',             'created_at' => now()],
            ['nama_jenis' => 'Perapian Kabel dan Tiang Listrik','created_at' => now()],
            ['nama_jenis' => 'Pindah Tiang',                   'created_at' => now()],
            ['nama_jenis' => 'Tegangan Rendah',                'created_at' => now()],
            ['nama_jenis' => 'Tiang Miring',                   'created_at' => now()],
            ['nama_jenis' => 'Tiang Patah',                    'created_at' => now()],
        ]);

        // ----------------------------------------
        // Status
        // ----------------------------------------
        DB::table('status')->insert([
            ['status_kerja' => 'pending',     'created_at' => now()],
            ['status_kerja' => 'on_progress', 'created_at' => now()],
            ['status_kerja' => 'completed',   'created_at' => now()],
        ]);

        // ----------------------------------------
        // Pelanggan
        // 16 pelanggan PLN (status_pelanggan = 1)
        // 9  non-pelanggan  (status_pelanggan = 0)
        // ----------------------------------------
        DB::table('pelanggan')->insert([

            // --- Pelanggan PLN ---
            [
                'no_pelanggan'     => 151001728632,
                'nama'             => 'Bp Muhdi',
                'no_telepon'       => '089602990540',
                'alamat'           => 'KK Gudang Point Superland Blok A Batu Ampar 01A',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 154000425139,
                'nama'             => 'Hadi Soemitro',
                'no_telepon'       => '081364792963',
                'alamat'           => 'KK Marbella 2 Blok F18 22 B',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151001844008,
                'nama'             => 'Nia Rizalin',
                'no_telepon'       => '081223377147',
                'alamat'           => 'Tanjung Buntung Bengkong Laut RT 1 RW 2',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 154000340700,
                'nama'             => 'Bapak Oka',
                'no_telepon'       => '082284137273',
                'alamat'           => 'KK Kav Sambau II Blok E 17',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151000095069,
                'nama'             => 'Pak Ratno',
                'no_telepon'       => '085265499770',
                'alamat'           => 'KK Villand Garden 37',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151001121574,
                'nama'             => 'Maraden Manurung',
                'no_telepon'       => '081372371126',
                'alamat'           => 'KK KSB Senjulung Baru Telaga Punggur 109 B2',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151000387849,
                'nama'             => 'Bapak Erik',
                'no_telepon'       => '081212454805',
                'alamat'           => 'KK PR Karyawan BP Batam Blok E3 22',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151001750959,
                'nama'             => 'Ibu Putri',
                'no_telepon'       => '082171485969',
                'alamat'           => 'KK MKS KP Pisang Pangkalan Petai Baloi 1 4',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 152001683905,
                'nama'             => 'Pak Ardi',
                'no_telepon'       => '081270202740',
                'alamat'           => 'KK PR Pondok Pertiwi II Sei Harapan Sekupang 06 CC',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151000133423,
                'nama'             => 'Ibu Gema',
                'no_telepon'       => '08127056362',
                'alamat'           => 'KK Orchid Garden 9 B',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 154000073145,
                'nama'             => 'Kurnaedi',
                'no_telepon'       => '082131912788',
                'alamat'           => 'KK KSB Senjulung Baru Telaga Punggur d3 13',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151001312475,
                'nama'             => 'Andy Wijaya',
                'no_telepon'       => '081378011234',
                'alamat'           => 'KK PR Oriana Batam Centre 01 B18',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151000368111,
                'nama'             => 'Jhon Ri',
                'no_telepon'       => '082283542723',
                'alamat'           => 'KK Baloi Kusuma Raya (Cucian Mobil DP Top 1 78)',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151000314435,
                'nama'             => 'Bapak Santoso',
                'no_telepon'       => '082386756370',
                'alamat'           => 'Jl.Duyung Samping Srimas Batu Ampar',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151000666352,
                'nama'             => 'Bapak Daman',
                'no_telepon'       => '081372735332',
                'alamat'           => 'KK PR Cibajas Nongsa Asri 7 A5',
                'status_pelanggan' => 1,
            ],
            [
                'no_pelanggan'     => 151000735341,
                'nama'             => 'PT Rezeki Graha Mas',
                'no_telepon'       => '081266699164',
                'alamat'           => 'KK Ruko Rezeki Graha Mas 01 B',
                'status_pelanggan' => 1,
            ],

            // --- Non Pelanggan ---
            [
                'no_pelanggan'     => null,
                'nama'             => 'Pak Yansen',
                'no_telepon'       => '081370010025',
                'alamat'           => 'Kav Penataan KP Tua Tanjung Buntung JL. Garuda Blok A1 No. 08',
                'status_pelanggan' => 0,
            ],
            [
                'no_pelanggan'     => null,
                'nama'             => 'Muhammad Abdul Ghopur',
                'no_telepon'       => '082111088543',
                'alamat'           => 'Kav Senjulung Baru Blok B6 28',
                'status_pelanggan' => 0,
            ],
            [
                'no_pelanggan'     => null,
                'nama'             => 'Bp. Rudi Sitorus',
                'no_telepon'       => '081364091477',
                'alamat'           => 'Pos Polisi Samping Gerbang Plaza Batamindo',
                'status_pelanggan' => 0,
            ],
            [
                'no_pelanggan'     => null,
                'nama'             => 'Bu Helina',
                'no_telepon'       => '082173553993',
                'alamat'           => 'Taman Baloi Mas Blok I No. 12 Dekat Indomobil',
                'status_pelanggan' => 0,
            ],
            [
                'no_pelanggan'     => null,
                'nama'             => 'PT Laguna Nauli',
                'no_telepon'       => '08118572316',
                'alamat'           => 'KK PR Citra LAGUNA 3 Tembesi Batu Aji 03A D2',
                'status_pelanggan' => 0,
            ],
            [
                'no_pelanggan'     => null,
                'nama'             => 'Bp. Sugianto',
                'no_telepon'       => '085265464711',
                'alamat'           => 'Sei Tering II Blok D3 NO 09 RT 05 RW 05',
                'status_pelanggan' => 0,
            ],
            [
                'no_pelanggan'     => null,
                'nama'             => 'Bapak Heri',
                'no_telepon'       => '082283331996',
                'alamat'           => 'Raja H Fisabilillah DAM Baloi Gedung',
                'status_pelanggan' => 0,
            ],
            [
                'no_pelanggan'     => null,
                'nama'             => 'PT Synthesis Project Logi',
                'no_telepon'       => '081275065517',
                'alamat'           => 'Duyung Komp. Harbour View',
                'status_pelanggan' => 0,
            ],
            [
                'no_pelanggan'     => null,
                'nama'             => 'BP Hairul',
                'no_telepon'       => '085317888880',
                'alamat'           => 'JL Duyung TG Uma Depan DC Mall',
                'status_pelanggan' => 0,
            ],
        ]);
    }
}