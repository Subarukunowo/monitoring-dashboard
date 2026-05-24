<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------
        // Helper: ambil ID dari tabel master
        // ------------------------------------------------
        $area     = fn($n) => DB::table('areas')->where('nama_area', $n)->value('id');
        $pengawas = fn($n) => DB::table('pengawas')->where('nama', $n)->value('id');
        $jenis    = fn($n) => DB::table('jenis_pekerjaan')->where('nama_jenis', trim($n))->value('id');

        // Semua data di Excel statusnya "Ditindaklanjuti" → on_progress
        $idStatusOnProgress = DB::table('status')->where('status_kerja', 'on_progress')->value('id');

        // ------------------------------------------------
        // Helper: simpan / ambil pelanggan
        // ------------------------------------------------
        $pelanggan = function (?string $noPlg, string $nama, string $noTelp, string $alamat) {
            $isNon = empty($noPlg) || strtolower(trim($noPlg)) === 'non pelanggan';

            if (!$isNon) {
                $existing = DB::table('pelanggan')->where('no_pelanggan', $noPlg)->value('id');
                if ($existing) return $existing;
            }

            return DB::table('pelanggan')->insertGetId([
                'no_pelanggan'     => $isNon ? null : $noPlg,
                'no_telepon'       => $noTelp,
                'alamat'           => $alamat,
                'status_pelanggan' => $isNon ? 0 : 1,
            ]);
        };

        // ------------------------------------------------
        // Helper: parse tanggal Indonesia → Y-m-d
        // Menangani: "13 Januari 2026", angka serial Excel (46047 dll)
        // ------------------------------------------------
        $tgl = function ($raw): ?string {
            if (empty($raw)) return null;
            $raw = trim((string)$raw);
            if ($raw === '' || $raw === '0') return null;

            // Angka serial Excel (mis: 46047 = 25 Jan 2026)
            if (is_numeric($raw) && (int)$raw > 40000) {
                return Carbon::createFromFormat('Y-m-d', '1899-12-30')
                    ->addDays((int)$raw)->toDateString();
            }

            $bulan = [
                'Januari'=>'01','Februari'=>'02','Maret'=>'03','April'=>'04',
                'Mei'=>'05','Juni'=>'06','Juli'=>'07','Agustus'=>'08',
                'September'=>'09','Oktober'=>'10','November'=>'11','Desember'=>'12',
            ];
            foreach ($bulan as $id => $num) {
                if (str_contains($raw, $id)) {
                    $raw = str_replace($id, $num, $raw);
                    break;
                }
            }
            try {
                return Carbon::createFromFormat('d m Y', trim($raw))->toDateString();
            } catch (\Exception) {
                return null;
            }
        };

        // Helper: parse jam "11.41 Wib" → "11:41:00"
        $jam = function (?string $j): ?string {
            if (empty($j)) return null;
            preg_match('/(\d{1,2})[.:](\d{2})/', $j, $m);
            return isset($m[1]) ? sprintf('%02d:%02d:00', $m[1], $m[2]) : null;
        };

        // Helper: parse nilai RAB angka/string → float|null
        $rab = function ($val): ?string {
            if (empty(trim((string)($val ?? '')))) return null;
            $clean = preg_replace('/[^0-9]/', '', (string)$val);
            return $clean ?: null;
        };

        // Helper: auto-insert jenis pekerjaan jika belum ada
        $getOrCreateJenis = function (string $nama) use ($jenis) {
            $id = $jenis($nama);
            if (!$id) {
                $id = DB::table('jenis_pekerjaan')->insertGetId([
                    'nama_jenis' => trim($nama),
                    'created_at' => now(),
                ]);
            }
            return $id;
        };

        // ------------------------------------------------
        // DATA SHEET 2026 (26 baris sesuai file Excel)
        // Tanggal selesai angka serial Excel dikonversi:
        //   46047 = 25 Jan 2026 | 46042 = 20 Jan 2026
        //   46044 = 22 Jan 2026 | 46048 = 26 Jan 2026
        //   46077 = 24 Feb 2026 | 46059 = 06 Feb 2026
        //   46058 = 05 Feb 2026 | 46061 = 08 Feb 2026
        //   46060 = 07 Feb 2026 | 46065 = 12 Feb 2026
        //   46064 = 11 Feb 2026 | 46080 = 27 Feb 2026
        //   46085 = 04 Mar 2026 | 46119 = 07 Apr 2026
        //   46126 = 14 Apr 2026 | 46132 = 20 Apr 2026
        //   46135 = 23 Apr 2026 | 46136 = 24 Apr 2026
        //   46134 = 22 Apr 2026
        // ------------------------------------------------
        $rows = [
            // 1
            [
                'no_insiden'       => '914882',
                'tanggal_laporan'  => '13 Januari 2025',
                'jam_laporan'      => '11.41 Wib',
                'nama_pelapor'     => 'Bp Muhdi',
                'no_telp'          => '089602990540',
                'no_pelanggan'     => '151001728632',
                'alamat'           => 'KK Gudang Point Superland Blok A Batu Ampar 01A',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Miring',
                'tanggal_survei'   => '16 Januari 2026',
                'nilai_rab'        => null,
                'no_sap'           => 'TUG : 013737',
                'tanggal_selesai'  => 46047, // 25 Jan 2026
                'keterangan'       => 'Tiang diluruskan dan dicor',
            ],
            // 2
            [
                'no_insiden'       => '915377',
                'tanggal_laporan'  => '19 Januari 2026',
                'jam_laporan'      => '11.52 Wib',
                'nama_pelapor'     => 'Hadi Soemitro',
                'no_telp'          => '081364792963',
                'no_pelanggan'     => '154000425139',
                'alamat'           => 'KK Marbella 2 Blok F18 22 B',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Geser Tiang',
                'tanggal_survei'   => '19 Januari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46042, // 20 Jan 2026
                'keterangan'       => 'Menunggu persetujuan RT/RW setempat posisi tiang yang baru',
            ],
            // 3
            [
                'no_insiden'       => '915507',
                'tanggal_laporan'  => '20 Januari 2026',
                'jam_laporan'      => '12.30 Wib',
                'nama_pelapor'     => 'Nia Rizalin',
                'no_telp'          => '081223377147',
                'no_pelanggan'     => '151001844008',
                'alamat'           => 'Tanjung Buntung Bengkong Laut RT 1 RW 2',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Pindah Tiang',
                'tanggal_survei'   => '20 Januari 2026',
                'nilai_rab'        => '2860200',
                'no_sap'           => null,
                'tanggal_selesai'  => 46044, // 22 Jan 2026
                'keterangan'       => 'Pelanggan belum bersedia membayar',
            ],
            // 4
            [
                'no_insiden'       => '915636',
                'tanggal_laporan'  => '21 Januari 2026',
                'jam_laporan'      => '14.15 Wib',
                'nama_pelapor'     => 'Bapak Oka',
                'no_telp'          => '082284137273',
                'no_pelanggan'     => '154000340700',
                'alamat'           => 'KK Kav Sambau II Blok E 17',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Miring',
                'tanggal_survei'   => '21 Januari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46048, // 26 Jan 2026
                'keterangan'       => 'Tiang diluruskan',
            ],
            // 5
            [
                'no_insiden'       => '916391',
                'tanggal_laporan'  => '27 Januari 2026',
                'jam_laporan'      => '14.55 Wib',
                'nama_pelapor'     => 'Pak Ratno',
                'no_telp'          => '085265499770',
                'no_pelanggan'     => '151000095069',
                'alamat'           => 'KK Villand Garden 37',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Geser Tiang',
                'tanggal_survei'   => '27 Januari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => null,
                'keterangan'       => 'Kelewatan',
            ],
            // 6
            [
                'no_insiden'       => '916443',
                'tanggal_laporan'  => '28 Januari 2026',
                'jam_laporan'      => '10.28 Wib',
                'nama_pelapor'     => 'Maraden Manurung',
                'no_telp'          => '081372371126',
                'no_pelanggan'     => '151001121574',
                'alamat'           => 'KK KSB Senjulung Baru Telaga Punggur 109 B2',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Geser Tiang',
                'tanggal_survei'   => '28 Januari 2026',
                'nilai_rab'        => '4216500',
                'no_sap'           => null,
                'tanggal_selesai'  => 46077, // 24 Feb 2026
                'keterangan'       => 'Tiang digeser',
            ],
            // 7
            [
                'no_insiden'       => '917198',
                'tanggal_laporan'  => '05 Februari 2026',
                'jam_laporan'      => '12.28 Wib',
                'nama_pelapor'     => 'Bapak Erik',
                'no_telp'          => '081212454805',
                'no_pelanggan'     => '151000387849',
                'alamat'           => 'KK PR Karyawan BP Batam Blok E3 22',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Miring',
                'tanggal_survei'   => '05 Februari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46059, // 06 Feb 2026
                'keterangan'       => 'Yang dimaksud pelanggan adalah tiang Telkom',
            ],
            // 8
            [
                'no_insiden'       => '917174',
                'tanggal_laporan'  => '05 Februari 2026',
                'jam_laporan'      => '10.14 Wib',
                'nama_pelapor'     => 'Ibu Putri',
                'no_telp'          => '082171485969',
                'no_pelanggan'     => '151001750959',
                'alamat'           => 'KK MKS KP Pisang Pangkalan Petai Baloi 1 4',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Geser Tiang',
                'tanggal_survei'   => '05 Februari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46058, // 05 Feb 2026
                'keterangan'       => 'Tiang besi diluruskan dan dicor',
            ],
            // 9
            [
                'no_insiden'       => '917321',
                'tanggal_laporan'  => '06 Februari 2026',
                'jam_laporan'      => '10.27 Wib',
                'nama_pelapor'     => 'Pak Ardi',
                'no_telp'          => '081270202740',
                'no_pelanggan'     => '152001683905',
                'alamat'           => 'KK PR Pondok Pertiwi II Sei Harapan Sekupang 06 CC',
                'area'             => 'Tiban',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Miring',
                'tanggal_survei'   => '06 Februari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46061, // 08 Feb 2026
                'keterangan'       => 'Tertunda karna ada tetangga pelanggan yang berduka',
            ],
            // 10
            [
                'no_insiden'       => '917322',
                'tanggal_laporan'  => '06 Februari 2026',
                'jam_laporan'      => '10.28 Wib',
                'nama_pelapor'     => 'Pak Yansen',
                'no_telp'          => '081370010025',
                'no_pelanggan'     => null,
                'alamat'           => 'Kav Penataan KP Tua Tanjung Buntung JL. Garuda Blok A1 No. 08',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Pindah Tiang',
                'tanggal_survei'   => '06 Februari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46060, // 07 Feb 2026
                'keterangan'       => 'Yang dimaksud pelanggan adalah tiang GD Portal, kavlingnya juga belum dibangun dan masih jauh dari GD Portal',
            ],
            // 11
            [
                'no_insiden'       => '917676',
                'tanggal_laporan'  => '10 Februari 2026',
                'jam_laporan'      => '10.37 Wib',
                'nama_pelapor'     => 'Ibu Gema',
                'no_telp'          => '08127056362',
                'no_pelanggan'     => '151000133423',
                'alamat'           => 'KK Orchid Garden 9 B',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Geser Tiang',
                'tanggal_survei'   => '10 Februari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46065, // 12 Feb 2026
                'keterangan'       => 'Dipending atas permintaan pelanggan menunggu permohonan tambah daya pelanggan',
            ],
            // 12
            [
                'no_insiden'       => '917654',
                'tanggal_laporan'  => '10 Februari 2026',
                'jam_laporan'      => '08.34 Wib',
                'nama_pelapor'     => 'Muhammad Abdul Ghopur',
                'no_telp'          => '082111088543',
                'no_pelanggan'     => null,
                'alamat'           => 'Kav Senjulung Baru Blok B6 28',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Patah',
                'tanggal_survei'   => '10 Februari 2026',
                'nilai_rab'        => null,
                'no_sap'           => 'SAP : 117495',
                'tanggal_selesai'  => 46064, // 11 Feb 2026
                'keterangan'       => 'Ganti tiang beton baru',
            ],
            // 13
            [
                'no_insiden'       => '919452',
                'tanggal_laporan'  => '26 Februari 2026',
                'jam_laporan'      => '09.09 Wib',
                'nama_pelapor'     => 'Bp. Rudi Sitorus',
                'no_telp'          => '081364091477',
                'no_pelanggan'     => null,
                'alamat'           => 'Pos Polisi Samping Gerbang Plaza Batamindo',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Perapian Kabel dan Tiang Listrik',
                'tanggal_survei'   => '26 Februari 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46080, // 27 Feb 2026
                'keterangan'       => 'Ditindaklanjuti oleh tim Utilitas HAR',
            ],
            // 14
            [
                'no_insiden'       => '919947',
                'tanggal_laporan'  => '03 Maret 2026',
                'jam_laporan'      => '10.44 Wib',
                'nama_pelapor'     => 'Kurnaedi',
                'no_telp'          => '082131912788',
                'no_pelanggan'     => '154000073145',
                'alamat'           => 'KK KSB Senjulung BAru Telaga Punggur d3 13',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Miring',
                'tanggal_survei'   => '03 Maret 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46085, // 04 Mar 2026
                'keterangan'       => 'Tiang diluruskan dan dicor',
            ],
            // 15 — tanggal survei di Excel: 46000 = 09 Des 2025, tanggal selesai: 46042 = 20 Jan 2026
            [
                'no_insiden'       => '920314',
                'tanggal_laporan'  => '06 Maret 2026',
                'jam_laporan'      => '08.26 Wib',
                'nama_pelapor'     => 'Bu Helina',
                'no_telp'          => '082173553993',
                'no_pelanggan'     => null,
                'alamat'           => 'Taman Baloi Mas Blok I No. 12 Dekat Indomobil',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Miring',
                'tanggal_survei'   => 46000, // 09 Des 2025
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46042, // 20 Jan 2026
                'keterangan'       => 'Tiang diluruskan dan dicor dan sisip tiang beton',
            ],
            // 16
            [
                'no_insiden'       => '920573',
                'tanggal_laporan'  => '08 Maret 2026',
                'jam_laporan'      => '09.57 Wib',
                'nama_pelapor'     => 'PT Laguna Nauli',
                'no_telp'          => '08118572316',
                'no_pelanggan'     => null,
                'alamat'           => 'KK PR Citra LAGUNA 3 Tembesi Batu Aji 03A D2',
                'area'             => 'Batu Aji',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Geser JTR',
                'tanggal_survei'   => '08 Maret 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => null,
                'keterangan'       => 'Kelewatan',
            ],
            // 17
            [
                'no_insiden'       => '920694',
                'tanggal_laporan'  => '09 Maret 2026',
                'jam_laporan'      => '14.55 Wib',
                'nama_pelapor'     => 'Bp. Sugianto',
                'no_telp'          => '085265464711',
                'no_pelanggan'     => null,
                'alamat'           => 'Sei Tering II Blok D3 NO 09 RT 05 RW 05',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tegangan Rendah',
                'tanggal_survei'   => '03 Maret 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => null,
                'keterangan'       => 'Kelewatan',
            ],
            // 18
            [
                'no_insiden'       => '920880',
                'tanggal_laporan'  => '11 Maret 2026',
                'jam_laporan'      => '14.23 Wib',
                'nama_pelapor'     => 'Andy Wijaya',
                'no_telp'          => '081378011234',
                'no_pelanggan'     => '151001312475',
                'alamat'           => 'KK PR Oriana Batam Centre 01 B18',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Geser KWH Meter Dan Geser Tiang',
                'tanggal_survei'   => '11 Maret 2026',
                'nilai_rab'        => '2388000',
                'no_sap'           => null,
                'tanggal_selesai'  => 46119, // 07 Apr 2026
                'keterangan'       => 'Bongkar tiang dan JTR',
            ],
            // 19
            [
                'no_insiden'       => '923641',
                'tanggal_laporan'  => '07 April 2026',
                'jam_laporan'      => '10.53 Wib',
                'nama_pelapor'     => 'Jhon Ri',
                'no_telp'          => '082283542723',
                'no_pelanggan'     => '151000368111',
                'alamat'           => 'KK Baloi Kusuma Raya (Cucian Mobil DP Top 1 78)',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Jaringan Drop',
                'tanggal_survei'   => '07 April 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46126, // 14 Apr 2026
                'keterangan'       => 'Ditindaklanjuti oleh tim UP3 Nagoya',
            ],
            // 20 — tanggal laporan di Excel: 46122 = 10 Apr 2026
            [
                'no_insiden'       => '924011',
                'tanggal_laporan'  => 46122, // 10 Apr 2026
                'jam_laporan'      => '09.23 Wib',
                'nama_pelapor'     => 'Bapak Santoso',
                'no_telp'          => '082386756370',
                'no_pelanggan'     => '151000314435',
                'alamat'           => 'Jl.Duyung Samping Srimas Batu Ampar',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Geser Tiang',
                'tanggal_survei'   => '10 April 2026',
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46126, // 14 Apr 2026
                'keterangan'       => 'Dibatalkan permintaan pelanggan',
            ],
            // 21 — tanggal laporan & survei di Excel: 46123 = 11 Apr 2026, selesai: 46132 = 20 Apr 2026
            [
                'no_insiden'       => '924100',
                'tanggal_laporan'  => 46123, // 11 Apr 2026
                'jam_laporan'      => '08.56 Wib',
                'nama_pelapor'     => 'Bapak Heri',
                'no_telp'          => '082283331996',
                'no_pelanggan'     => 'Non Pelanggan',
                'alamat'           => 'Raja H Fisabilillah DAM Baloi Gedung',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Miring',
                'tanggal_survei'   => 46123, // 11 Apr 2026
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46132, // 20 Apr 2026
                'keterangan'       => 'Tiang diluruskan dan dicor',
            ],
            // 22 — tanggal laporan & survei: 46125 = 13 Apr 2026, selesai: 46132 = 20 Apr 2026
            [
                'no_insiden'       => '924358',
                'tanggal_laporan'  => 46125, // 13 Apr 2026
                'jam_laporan'      => '14.16 Wib',
                'nama_pelapor'     => 'Bapak Daman',
                'no_telp'          => '081372735332',
                'no_pelanggan'     => '151000666352',
                'alamat'           => 'KK PR Cibajas Nongsa Asri 7 A5',
                'area'             => 'Batam Centre',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Kabel Terkelupas',
                'tanggal_survei'   => 46125, // 13 Apr 2026
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46132, // 20 Apr 2026
                'keterangan'       => 'Ganti kabel SR dan dipindah dari depan rumah pelanggan',
            ],
            // 23 — tanggal laporan & survei: 46127 = 15 Apr 2026, selesai: 46136 = 24 Apr 2026
            [
                'no_insiden'       => '924576',
                'tanggal_laporan'  => 46127, // 15 Apr 2026
                'jam_laporan'      => '09.54 Wib',
                'nama_pelapor'     => 'PT.SYNTHESIS PROJECT LOGI',
                'no_telp'          => '081275065517',
                'no_pelanggan'     => null,
                'alamat'           => 'Duyung Komp. Harbour View',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Tiang Miring',
                'tanggal_survei'   => 46127, // 15 Apr 2026
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46136, // 24 Apr 2026
                'keterangan'       => 'Tiang diluruskan dan dicor',
            ],
            // 24 — tanggal laporan: 46128 = 16 Apr 2026, survei: 46127 = 15 Apr 2026, selesai: 46135 = 23 Apr 2026
            [
                'no_insiden'       => '924685',
                'tanggal_laporan'  => 46128, // 16 Apr 2026
                'jam_laporan'      => '03.54 Wib',
                'nama_pelapor'     => 'PT Rezeki Graha Mas',
                'no_telp'          => '081266699164',
                'no_pelanggan'     => '151000735341',
                'alamat'           => 'KK Ruko Rezeki Graha Mas 01 B',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Perapian Kabel JTR',
                'tanggal_survei'   => 46127, // 15 Apr 2026
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46135, // 23 Apr 2026
                'keterangan'       => 'Ditindaklanjuti oleh tim UP3 Nagoya',
            ],
            // 25 — tanggal laporan & survei: 46128 = 16 Apr 2026, selesai: 46135 = 23 Apr 2026
            [
                'no_insiden'       => '924849',
                'tanggal_laporan'  => 46128, // 16 Apr 2026
                'jam_laporan'      => '15.44 Wib',
                'nama_pelapor'     => 'PT Rezeki Graha Mas',
                'no_telp'          => '081266699164',
                'no_pelanggan'     => '151000735341',
                'alamat'           => 'KK Ruko Rezeki Graha Mas 01 B',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Perapian Kabel JTR',
                'tanggal_survei'   => 46128, // 16 Apr 2026
                'nilai_rab'        => null,
                'no_sap'           => null,
                'tanggal_selesai'  => 46135, // 23 Apr 2026
                'keterangan'       => 'Ditindaklanjuti oleh tim UP3 Nagoya',
            ],
            // 26 — tanggal laporan & survei: 46133 = 21 Apr 2026, selesai: 46134 = 22 Apr 2026
            [
                'no_insiden'       => '925292',
                'tanggal_laporan'  => 46133, // 21 Apr 2026
                'jam_laporan'      => '14.32 Wib',
                'nama_pelapor'     => 'BP Hairul',
                'no_telp'          => '085317888880',
                'no_pelanggan'     => 'Non Pelanggan',
                'alamat'           => 'JL Duyung TG Uma Depan DC Mall',
                'area'             => 'Nagoya',
                'pengawas'         => 'Refendi',
                'jenis'            => 'Pindah Tiang',
                'tanggal_survei'   => 46133, // 21 Apr 2026
                'nilai_rab'        => '5780500',
                'no_sap'           => 'SAP : 118873',
                'tanggal_selesai'  => 46134, // 22 Apr 2026
                'keterangan'       => 'Pindah dan sisip tiang baru',
            ],
        ];

        foreach ($rows as $row) {
            $idPelanggan = $pelanggan(
                $row['no_pelanggan'],
                $row['nama_pelapor'],
                $row['no_telp'],
                $row['alamat']
            );

            DB::table('laporan')->insert([
                'no_insiden'          => $row['no_insiden'],
                'tanggal_laporan'     => $tgl($row['tanggal_laporan']),
                'jam_laporan'         => $jam($row['jam_laporan']),
                'id_pelanggan'        => $idPelanggan,
                'id_area'             => $area($row['area']),
                'id_pengawas'         => $pengawas($row['pengawas']),
                'id_jenis_pekerjaan'  => $getOrCreateJenis($row['jenis']),
                'id_status'           => $idStatusOnProgress,
                'tanggal_survei'      => $tgl($row['tanggal_survei']),
                'nilai_rab'           => $rab($row['nilai_rab']),
                'no_sap'              => $row['no_sap'],
                'tanggal_selesai'     => $tgl($row['tanggal_selesai']),
                'keterangan'          => $row['keterangan'],
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }
}