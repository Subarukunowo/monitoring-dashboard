<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\JenisPekerjaan;
use App\Models\Pelanggan;
use App\Models\Pengawas;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $petugas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->petugas = User::factory()->create(['role' => 'user']);
    }

    public function test_non_admin_is_blocked_from_master_data(): void
    {
        $this->actingAs($this->petugas)->get('/areas')->assertStatus(403);
        $this->actingAs($this->petugas)->get('/pelanggans')->assertStatus(403);
        $this->actingAs($this->petugas)->get('/pengawas')->assertStatus(403);
        $this->actingAs($this->petugas)->get('/jenis_pekerjaan')->assertStatus(403);
        $this->actingAs($this->petugas)->get('/status')->assertStatus(403);
    }

    public function test_admin_can_crud_all_master_data(): void
    {
        // Areas
        $this->actingAs($this->admin)->get('/areas')->assertStatus(200);
        $this->actingAs($this->admin)->get('/areas/create')->assertStatus(200);
        $this->actingAs($this->admin)->post('/areas', ['nama_area' => 'Sekupang'])->assertRedirect('/areas');
        $area = Area::firstOrFail();
        $this->actingAs($this->admin)->get("/areas/{$area->id}/edit")->assertStatus(200);
        $this->actingAs($this->admin)->put("/areas/{$area->id}", ['nama_area' => 'Sekupang Baru'])->assertRedirect('/areas');
        $this->actingAs($this->admin)->delete("/areas/{$area->id}")->assertRedirect('/areas');

        // Pelanggan
        $this->actingAs($this->admin)->get('/pelanggans')->assertStatus(200);
        $this->actingAs($this->admin)->get('/pelanggans/create')->assertStatus(200);
        $this->actingAs($this->admin)->post('/pelanggans', [
            'no_pelanggan' => '151001999888',
            'nama' => 'PT Uji Listrik',
            'no_telepon' => '0811223344',
            'alamat' => 'Batam',
            'status_pelanggan' => true,
        ])->assertRedirect('/pelanggans');
        $pelanggan = Pelanggan::firstOrFail();
        $this->actingAs($this->admin)->get("/pelanggans/{$pelanggan->id}/edit")->assertStatus(200);
        $this->actingAs($this->admin)->put("/pelanggans/{$pelanggan->id}", [
            'no_pelanggan' => '151001999888',
            'nama' => 'PT Uji Listrik Updated',
            'status_pelanggan' => true,
        ])->assertRedirect('/pelanggans');
        $this->actingAs($this->admin)->delete("/pelanggans/{$pelanggan->id}")->assertRedirect('/pelanggans');

        // Pengawas
        $this->actingAs($this->admin)->get('/pengawas')->assertStatus(200);
        $this->actingAs($this->admin)->get('/pengawas/create')->assertStatus(200);
        $this->actingAs($this->admin)->post('/pengawas', ['nama' => 'Andi', 'no_telepon' => '0812'])->assertRedirect('/pengawas');
        $pengawas = Pengawas::firstOrFail();
        $this->actingAs($this->admin)->get("/pengawas/{$pengawas->id}/edit")->assertStatus(200);
        $this->actingAs($this->admin)->put("/pengawas/{$pengawas->id}", ['nama' => 'Andi Pratama'])->assertRedirect('/pengawas');
        $this->actingAs($this->admin)->delete("/pengawas/{$pengawas->id}")->assertRedirect('/pengawas');

        // Jenis Pekerjaan
        $this->actingAs($this->admin)->get('/jenis_pekerjaan')->assertStatus(200);
        $this->actingAs($this->admin)->get('/jenis_pekerjaan/create')->assertStatus(200);
        $this->actingAs($this->admin)->post('/jenis_pekerjaan', ['nama_jenis' => 'Ganti KWH'])->assertRedirect('/jenis_pekerjaan');
        $jenis = JenisPekerjaan::firstOrFail();
        $this->actingAs($this->admin)->get("/jenis_pekerjaan/{$jenis->id}/edit")->assertStatus(200);
        $this->actingAs($this->admin)->put("/jenis_pekerjaan/{$jenis->id}", ['nama_jenis' => 'Ganti KWH 3 Fasa'])->assertRedirect('/jenis_pekerjaan');
        $this->actingAs($this->admin)->delete("/jenis_pekerjaan/{$jenis->id}")->assertRedirect('/jenis_pekerjaan');

        // Status
        $this->actingAs($this->admin)->get('/status')->assertStatus(200);
        $this->actingAs($this->admin)->get('/status/create')->assertStatus(200);
        $this->actingAs($this->admin)->post('/status', ['status_kerja' => 'on_hold'])->assertRedirect('/status');
        $status = Status::firstOrFail();
        $this->actingAs($this->admin)->get("/status/{$status->id}/edit")->assertStatus(200);
        $this->actingAs($this->admin)->put("/status/{$status->id}", ['status_kerja' => 'completed'])->assertRedirect('/status');
        $this->actingAs($this->admin)->delete("/status/{$status->id}")->assertRedirect('/status');
    }
}
