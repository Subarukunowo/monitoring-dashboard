<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Laporan;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardAndLaporanTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Area $area;

    private Status $pending;

    private Status $completed;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'user']);
        $this->area = Area::create(['nama_area' => 'Batam Centre']);
        $this->pending = Status::create(['status_kerja' => 'pending']);
        Status::create(['status_kerja' => 'on_progress']);
        Status::create(['status_kerja' => 'on_hold']);
        $this->completed = Status::create(['status_kerja' => 'completed']);
    }

    public function test_dashboard_renders_and_supports_filters(): void
    {
        Laporan::create([
            'no_insiden' => '910001',
            'tanggal_laporan' => now()->toDateString(),
            'id_area' => $this->area->id,
            'id_status' => $this->pending->id,
            'tanggal_selesai' => now()->subDays(2)->toDateString(),
            'keterangan' => 'Tiang miring dekat simpang',
        ]);

        $this->actingAs($this->user)
            ->get('/dashboard')
            ->assertStatus(200);

        $this->actingAs($this->user)
            ->get('/dashboard?status=pending&area='.$this->area->id.'&filter=overdue&search=910001')
            ->assertStatus(200);

        $this->actingAs($this->user)
            ->get('/dashboard?filter=due_today&search=terbuka')
            ->assertStatus(200);
    }

    public function test_laporan_full_crud_with_attachments(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)->get('/laporan')->assertStatus(200);
        $this->actingAs($this->user)->get('/laporan/create')->assertStatus(200);

        $response = $this->actingAs($this->user)->post('/laporan', [
            'no_insiden' => '915000',
            'tanggal_laporan' => now()->toDateString(),
            'jam_laporan' => '10:30',
            'nama_pelanggan' => 'Budi Santoso',
            'no_pelanggan' => '151001728699',
            'no_telepon' => '08123456789',
            'alamat' => 'Jl. Engku Putri',
            'status_pelanggan' => '1',
            'id_area' => $this->area->id,
            'nama_pengawas' => 'Refendi',
            'jenis_pekerjaan' => 'Geser Tiang',
            'id_status' => $this->pending->id,
            'tanggal_survei' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'nilai_rab' => 2500000,
            'no_sap' => 'SAP : 119900',
            'keterangan' => 'Pekerjaan geser tiang listrik',
            'foto_pekerjaan' => [UploadedFile::fake()->image('kerja.jpg')],
            'foto_administrasi' => [UploadedFile::fake()->image('admin.jpg')],
            'dokumen' => [UploadedFile::fake()->create('spk.pdf', 100, 'application/pdf')],
        ]);

        $response->assertRedirect('/laporan');
        $laporan = Laporan::where('no_insiden', '915000')->firstOrFail();
        $this->assertCount(3, $laporan->dokumen);

        $this->actingAs($this->user)
            ->get('/laporan?status=pending&search=915000&area='.$this->area->id)
            ->assertStatus(200);

        $this->actingAs($this->user)
            ->get('/laporan?filter=due_this_week&search=terbuka')
            ->assertStatus(200);

        $this->actingAs($this->user)->get("/laporan/{$laporan->id}")->assertStatus(200);
        $this->actingAs($this->user)->get("/laporan/{$laporan->id}/edit")->assertStatus(200);

        $docToDelete = $laporan->dokumen->first();
        $this->actingAs($this->user)->put("/laporan/{$laporan->id}", [
            'no_insiden' => '915000',
            'tanggal_laporan' => now()->toDateString(),
            'nama_pelanggan' => 'Budi Santoso Updated',
            'no_pelanggan' => '151001728699',
            'id_area' => $this->area->id,
            'id_status' => $this->completed->id,
            'nama_pengawas' => 'Refendi',
            'jenis_pekerjaan' => 'Geser Tiang',
            'hapus_dokumen' => [$docToDelete->id],
        ])->assertRedirect("/laporan/{$laporan->id}");

        $this->assertDatabaseMissing('dokumen', ['id' => $docToDelete->id]);

        // Also test creating and updating a non-pelanggan laporan (without no_pelanggan)
        $this->actingAs($this->user)->post('/laporan', [
            'no_insiden' => '915001',
            'tanggal_laporan' => now()->toDateString(),
            'nama_pelanggan' => 'Pelapor Umum',
            'id_area' => $this->area->id,
            'id_status' => $this->pending->id,
        ])->assertRedirect('/laporan');

        $umum = Laporan::where('no_insiden', '915001')->firstOrFail();
        $this->actingAs($this->user)->put("/laporan/{$umum->id}", [
            'no_insiden' => '915001',
            'tanggal_laporan' => now()->toDateString(),
            'nama_pelanggan' => 'Pelapor Umum 2',
            'id_area' => $this->area->id,
            'id_status' => $this->completed->id,
        ])->assertRedirect("/laporan/{$umum->id}");

        $this->actingAs($this->user)
            ->delete("/laporan/{$laporan->id}")
            ->assertRedirect('/laporan');

        $this->assertDatabaseMissing('laporan', ['id' => $laporan->id]);
    }
}
