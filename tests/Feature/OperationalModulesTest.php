<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\BqItem;
use App\Models\ContactMessage;
use App\Models\Dokumen;
use App\Models\Laporan;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperationalModulesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $petugas;

    private Laporan $laporan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->petugas = User::factory()->create(['role' => 'user']);
        $area = Area::create(['nama_area' => 'Nagoya']);
        $completed = Status::create(['status_kerja' => 'completed']);

        $this->laporan = Laporan::create([
            'no_insiden' => '920001',
            'tanggal_laporan' => now()->toDateString(),
            'id_area' => $area->id,
            'id_status' => $completed->id,
            'no_sap' => 'SAP : 8811',
            'nilai_rab' => 1000000,
        ]);
    }

    public function test_rab_and_bq_modules_and_sync(): void
    {
        $this->actingAs($this->petugas)->get('/rab')->assertStatus(200);
        $this->actingAs($this->petugas)->get('/bill-of-quantity')->assertStatus(200);

        // Petugas can submit RAB as diajukan, but cannot approve directly
        $this->actingAs($this->petugas)->patch("/rab/{$this->laporan->id}", [
            'nilai_rab' => 1250000,
            'no_sap' => 'SAP : 8811',
            'status_rab' => 'diajukan',
        ])->assertRedirect();

        $this->actingAs($this->petugas)->patch("/rab/{$this->laporan->id}", [
            'nilai_rab' => 1250000,
            'status_rab' => 'disetujui',
        ])->assertSessionHasErrors('status_rab');

        // Admin can approve RAB
        $this->actingAs($this->admin)->patch("/rab/{$this->laporan->id}", [
            'nilai_rab' => 1250000,
            'status_rab' => 'disetujui',
        ])->assertRedirect();

        $this->assertDatabaseHas('laporan', [
            'id' => $this->laporan->id,
            'status_rab' => 'disetujui',
        ]);

        // Add, update, sync, and delete BQ item
        $this->actingAs($this->petugas)->post("/bill-of-quantity/{$this->laporan->id}/items", [
            'kode_material' => 'MAT-10',
            'uraian' => 'Tiang Beton 9m',
            'kategori' => 'Material',
            'satuan' => 'Batang',
            'volume' => 2,
            'harga_satuan' => 1500000,
        ])->assertRedirect();

        $item = BqItem::firstOrFail();
        $this->assertEquals(3000000, (float) $item->subtotal);

        $this->actingAs($this->petugas)->put("/bill-of-quantity/items/{$item->id}", [
            'kode_material' => 'MAT-10',
            'uraian' => 'Tiang Beton 9m Updated',
            'kategori' => 'Material',
            'satuan' => 'Batang',
            'volume' => 3,
            'harga_satuan' => 1500000,
        ])->assertRedirect();

        $this->actingAs($this->petugas)
            ->post("/bill-of-quantity/{$this->laporan->id}/sync-rab")
            ->assertRedirect();

        $this->assertEquals(4500000, (float) $this->laporan->fresh()->nilai_rab);

        $this->actingAs($this->petugas)->delete("/bill-of-quantity/items/{$item->id}")->assertRedirect();
        $this->assertDatabaseMissing('bq_items', ['id' => $item->id]);
    }

    public function test_tutup_kontrak_permissions_and_flow(): void
    {
        $this->actingAs($this->petugas)->get('/tutup-kontrak')->assertStatus(200);

        // Petugas cannot close contract (403)
        $this->actingAs($this->petugas)
            ->post("/tutup-kontrak/{$this->laporan->id}/close", ['catatan_penutupan' => 'Uji'])
            ->assertStatus(403);

        // Admin can close and reopen contract
        $this->actingAs($this->admin)
            ->post("/tutup-kontrak/{$this->laporan->id}/close", ['catatan_penutupan' => 'BAST selesai'])
            ->assertRedirect();

        $this->assertTrue((bool) $this->laporan->fresh()->is_closed);

        $this->actingAs($this->admin)
            ->post("/tutup-kontrak/{$this->laporan->id}/reopen")
            ->assertRedirect();

        $this->assertFalse((bool) $this->laporan->fresh()->is_closed);
    }

    public function test_dokumentasi_routes_upload_and_delete(): void
    {
        Storage::fake('public');

        $this->actingAs($this->petugas)->get('/dokumentasi-pekerjaan')->assertStatus(200);
        $this->actingAs($this->petugas)->get('/dokumentasi-administrasi')->assertStatus(200);

        $this->actingAs($this->petugas)->post("/laporan/{$this->laporan->id}/dokumen", [
            'tipe' => 'pekerjaan',
            'file' => UploadedFile::fake()->image('lapangan.png'),
        ])->assertRedirect();

        $doc = Dokumen::firstOrFail();
        $this->actingAs($this->petugas)->delete("/dokumen/{$doc->id}")->assertRedirect();
        $this->assertDatabaseMissing('dokumen', ['id' => $doc->id]);
    }

    public function test_legacy_coming_soon_routes_redirect_to_real_modules(): void
    {
        $this->actingAs($this->petugas)->get('/coming-soon/rab')->assertRedirect('/rab');
        $this->actingAs($this->petugas)->get('/coming-soon/tutup-kontrak')->assertRedirect('/tutup-kontrak');
        $this->actingAs($this->petugas)->get('/coming-soon/dokumentasi-pekerjaan')->assertRedirect('/dokumentasi-pekerjaan');
        $this->actingAs($this->petugas)->get('/coming-soon/dokumentasi-administrasi')->assertRedirect('/dokumentasi-administrasi');
        $this->actingAs($this->petugas)->get('/coming-soon/bill-of-quantity')->assertRedirect('/bill-of-quantity');
        $this->actingAs($this->petugas)->get('/coming-soon/unknown')->assertStatus(404);
    }

    public function test_contact_developer_works_for_guest_user_and_admin(): void
    {
        $this->get('/contact')->assertStatus(200);

        $this->post('/contact', [
            'nama' => 'Tamu',
            'email' => 'tamu@example.com',
            'topik' => 'Pertanyaan akses',
            'pesan' => 'Mohon info akun petugas.',
        ])->assertRedirect('/contact');

        $msg = ContactMessage::firstOrFail();

        $this->actingAs($this->petugas)
            ->patch("/contact/{$msg->id}/status", ['status' => 'selesai'])
            ->assertStatus(403);

        $this->actingAs($this->admin)
            ->patch("/contact/{$msg->id}/status", ['status' => 'selesai'])
            ->assertRedirect();

        $this->assertSame('selesai', $msg->fresh()->status);
    }
}
