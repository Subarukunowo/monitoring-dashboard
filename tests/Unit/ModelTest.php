<?php

namespace Tests\Unit;

use App\Models\Area;
use App\Models\BqItem;
use App\Models\ContactMessage;
use App\Models\Dokumen;
use App\Models\JenisPekerjaan;
use App\Models\Laporan;
use App\Models\Pelanggan;
use App\Models\Pengawas;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_admin_helper(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    public function test_status_accessors_and_laporan_relationships(): void
    {
        $area = Area::create(['nama_area' => 'Nagoya']);
        $pengawas = Pengawas::create(['nama' => 'Refendi', 'no_telepon' => '0812']);
        $jenis = JenisPekerjaan::create(['nama_jenis' => 'Tiang Miring']);
        $pelanggan = Pelanggan::create([
            'no_pelanggan' => 151001728632,
            'nama' => 'Bp Muhdi',
            'no_telepon' => '0896',
            'alamat' => 'Batu Ampar',
            'status_pelanggan' => 1,
        ]);

        $pending = Status::create(['status_kerja' => 'pending']);
        $progress = Status::create(['status_kerja' => 'on_progress']);
        $hold = Status::create(['status_kerja' => 'on_hold']);
        $completed = Status::create(['status_kerja' => 'completed']);

        $this->assertSame('Terbuka', $pending->label);
        $this->assertSame('Sedang Dikerjakan', $progress->label);
        $this->assertSame('Ditunda', $hold->label);
        $this->assertSame('Selesai', $completed->label);
        $this->assertStringContainsString('bg-blue-100', $pending->badge_class);
        $this->assertStringContainsString('bg-yellow-100', $progress->badge_class);
        $this->assertStringContainsString('bg-orange-100', $hold->badge_class);
        $this->assertStringContainsString('bg-green-100', $completed->badge_class);

        $laporan = Laporan::create([
            'no_insiden' => '900001',
            'tanggal_laporan' => Carbon::yesterday(),
            'id_area' => $area->id,
            'id_pelanggan' => $pelanggan->id,
            'id_pengawas' => $pengawas->id,
            'id_jenis_pekerjaan' => $jenis->id,
            'id_status' => $progress->id,
            'tanggal_selesai' => Carbon::yesterday(),
            'nilai_rab' => 1500000,
        ]);

        $dok = Dokumen::create([
            'id_laporan' => $laporan->id,
            'tipe' => 'pekerjaan',
            'nama_file' => 'foto.jpg',
            'path_file' => 'laporan/1/pekerjaan/foto.jpg',
            'mime_type' => 'image/jpeg',
            'ukuran_file' => 1024,
        ]);

        $bq = BqItem::create([
            'id_laporan' => $laporan->id,
            'kode_material' => 'MAT-1',
            'uraian' => 'Kabel JTR',
            'kategori' => 'Material',
            'satuan' => 'Meter',
            'volume' => 10,
            'harga_satuan' => 50000,
            'subtotal' => 500000,
        ]);

        $user = User::factory()->create(['role' => 'user']);
        $msg = ContactMessage::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'email' => $user->email,
            'topik' => 'Uji',
            'pesan' => 'Pesan uji',
            'status' => 'baru',
        ]);

        $this->assertTrue($laporan->is_overdue);
        $this->assertSame('Sedang Dikerjakan', $laporan->status_badge['label']);
        $this->assertCount(1, $area->laporan);
        $this->assertCount(1, $pelanggan->laporan);
        $this->assertCount(1, $pengawas->laporan);
        $this->assertCount(1, $jenis->laporan);
        $this->assertCount(1, $progress->laporan);
        $this->assertSame($laporan->id, $dok->laporan->id);
        $this->assertSame($laporan->id, $bq->laporan->id);
        $this->assertSame($user->id, $msg->user->id);
    }
}
