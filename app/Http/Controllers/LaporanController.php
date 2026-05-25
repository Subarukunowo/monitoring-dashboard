<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Area;
use App\Models\Pelanggan;
use App\Models\JenisPekerjaan;
use App\Models\Pengawas;
use App\Models\Status;
use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::with(['area', 'pelanggan', 'pengawas', 'jenisPekerjaan', 'status'])
            ->orderBy('created_at', 'desc');

        // 🔹 Filter by Status
        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) =>
                $q->where('status_kerja', $request->status)
            );
        }

        // 🔹 Filter by Area ← DITAMBAHKAN
        if ($request->filled('area')) {
            $query->where('id_area', $request->area);
        }

        // 🔹 Filter Cepat (overdue, due_today, dll)
        if ($request->filled('filter')) {
            $today = now()->toDateString();
            match ($request->filter) {
                'overdue'       => $query->whereHas('status', fn($q) => $q->where('status_kerja', '!=', 'completed'))
                                         ->whereNotNull('tanggal_selesai')
                                         ->whereDate('tanggal_selesai', '<', $today),
                'due_today'     => $query->whereHas('status', fn($q) => $q->where('status_kerja', '!=', 'completed'))
                                         ->whereDate('tanggal_selesai', $today),
                'created_today' => $query->whereDate('created_at', $today),
                'due_this_week' => $query->whereHas('status', fn($q) => $q->where('status_kerja', '!=', 'completed'))
                                         ->whereNotNull('tanggal_selesai')
                                         ->whereDate('tanggal_selesai', '>=', $today)
                                         ->whereDate('tanggal_selesai', '<=', now()->addDays(7)->toDateString()),
                default         => null,
            };
        }

        // 🔹 Filter Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_insiden',   'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%")
                  ->orWhere('no_sap',     'like', "%{$s}%")
                  ->orWhereHas('area',      fn($r) => $r->where('nama_area', 'like', "%{$s}%"))
                  ->orWhereHas('pelanggan', fn($r) => $r->where('nama',      'like', "%{$s}%"));
            });
        }

        $laporans = $query->paginate(15)->withQueryString();
        return view('laporan.index', compact('laporans'));
    }

    public function create()
    {
        $areas    = Area::all();
        $statuses = Status::all();
        return view('laporan.create', compact('areas', 'statuses'));
    }

    public function store(Request $request)
    {
        Log::info('=== STORE DIPANGGIL ===');
        Log::info('FILES:', $request->allFiles());

        $request->validate([
            'no_insiden'      => 'required|unique:laporan,no_insiden|max:20',
            'tanggal_laporan' => 'required|date',
            'jam_laporan'     => 'nullable|date_format:H:i',
            'nama_pelanggan'  => 'nullable|string|max:255',
            'id_area'         => 'required|exists:areas,id',
            'jenis_pekerjaan' => 'nullable|string|max:255',
            'id_status'       => 'required|exists:status,id',
            'tanggal_survei'  => 'nullable|date',
            'no_sap'          => 'nullable|string|max:50',
            'tanggal_selesai' => 'nullable|date',
            'keterangan'      => 'nullable|string',
            'link_maps'       => 'nullable|url|max:2048',
            'foto_pekerjaan.*'    => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_administrasi.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen.*'           => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        DB::transaction(function () use ($request) {
            $idPelanggan = null;
            if ($request->filled('nama_pelanggan')) {
                $idPelanggan = Pelanggan::firstOrCreate(
                    ['nama' => trim($request->nama_pelanggan)]
                )->id;
            }

            $idJenisPekerjaan = null;
            if ($request->filled('jenis_pekerjaan')) {
                $idJenisPekerjaan = JenisPekerjaan::firstOrCreate(
                    ['nama_jenis' => trim($request->jenis_pekerjaan)]
                )->id;
            }

            $laporan = Laporan::create([
                'no_insiden'         => $request->no_insiden,
                'tanggal_laporan'    => $request->tanggal_laporan,
                'jam_laporan'        => $request->jam_laporan,
                'id_pelanggan'       => $idPelanggan,
                'id_area'            => $request->id_area,
                'id_pengawas'        => null,
                'id_jenis_pekerjaan' => $idJenisPekerjaan,
                'id_status'          => $request->id_status,
                'tanggal_survei'     => $request->tanggal_survei,
                'no_sap'             => $request->no_sap,
                'tanggal_selesai'    => $request->tanggal_selesai,
                'keterangan'         => $request->keterangan,
                'link_maps'          => $request->link_maps,
            ]);

            Log::info('Laporan created, ID: ' . $laporan->id);
            $this->simpanFile($request, $laporan);
        });

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['area', 'pelanggan', 'pengawas', 'jenisPekerjaan', 'status', 'dokumen']);
        return view('laporan.show', compact('laporan'));
    }

    public function edit(Laporan $laporan)
    {
        $laporan->load(['area', 'pelanggan', 'pengawas', 'jenisPekerjaan', 'status', 'dokumen']);
        $areas    = Area::all();
        $statuses = Status::all();
        return view('laporan.edit', compact('laporan', 'areas', 'statuses'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        Log::info('=== UPDATE DIPANGGIL ===');
        Log::info('FILES:', $request->allFiles());

        $request->validate([
            'no_insiden'      => ['required', 'max:20', Rule::unique('laporan', 'no_insiden')->ignore($laporan->id)],
            'tanggal_laporan' => 'required|date',
            'jam_laporan'     => 'nullable|date_format:H:i',
            'nama_pelanggan'  => 'nullable|string|max:255',
            'id_area'         => 'required|exists:areas,id',
            'jenis_pekerjaan' => 'nullable|string|max:255',
            'id_status'       => 'required|exists:status,id',
            'tanggal_survei'  => 'nullable|date',
            'no_sap'          => 'nullable|string|max:50',
            'tanggal_selesai' => 'nullable|date',
            'keterangan'      => 'nullable|string',
            'link_maps'       => 'nullable|url|max:2048',
            'foto_pekerjaan.*'    => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_administrasi.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen.*'           => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'hapus_dokumen'   => 'nullable|array',
            'hapus_dokumen.*' => 'integer|exists:dokumen,id',
        ]);

        DB::transaction(function () use ($request, $laporan) {
            $idPelanggan = $laporan->id_pelanggan;
            if ($request->filled('nama_pelanggan')) {
                $idPelanggan = Pelanggan::firstOrCreate(
                    ['nama' => trim($request->nama_pelanggan)]
                )->id;
            } elseif ($request->has('nama_pelanggan') && $request->nama_pelanggan === '') {
                $idPelanggan = null;
            }

            $idJenisPekerjaan = $laporan->id_jenis_pekerjaan;
            if ($request->filled('jenis_pekerjaan')) {
                $idJenisPekerjaan = JenisPekerjaan::firstOrCreate(
                    ['nama_jenis' => trim($request->jenis_pekerjaan)]
                )->id;
            } elseif ($request->has('jenis_pekerjaan') && $request->jenis_pekerjaan === '') {
                $idJenisPekerjaan = null;
            }

            $laporan->update([
                'no_insiden'         => $request->no_insiden,
                'tanggal_laporan'    => $request->tanggal_laporan,
                'jam_laporan'        => $request->jam_laporan,
                'id_pelanggan'       => $idPelanggan,
                'id_area'            => $request->id_area,
                'id_jenis_pekerjaan' => $idJenisPekerjaan,
                'id_status'          => $request->id_status,
                'tanggal_survei'     => $request->tanggal_survei,
                'no_sap'             => $request->no_sap,
                'tanggal_selesai'    => $request->tanggal_selesai,
                'keterangan'         => $request->keterangan,
                'link_maps'          => $request->link_maps,
            ]);

            if ($request->filled('hapus_dokumen')) {
                $this->hapusDokumen($request->hapus_dokumen, $laporan->id);
            }

            $this->simpanFile($request, $laporan);
        });

        return redirect()->route('laporan.show', $laporan)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Laporan $laporan)
    {
        DB::transaction(function () use ($laporan) {
            foreach ($laporan->dokumen as $dok) {
                Storage::disk('public')->delete($dok->path_file);
                $folder = dirname($dok->path_file);
                $remaining = Storage::disk('public')->files($folder);
                if (empty($remaining)) {
                    Storage::disk('public')->deleteDirectory($folder);
                }
            }
            $laporan->dokumen()->delete();
            $laporan->delete();
        });

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan dan semua file terkait berhasil dihapus.');
    }

    private function simpanFile(Request $request, Laporan $laporan): void
    {
        $uploads = [
            'foto_pekerjaan'    => 'pekerjaan',
            'foto_administrasi' => 'administrasi',
            'dokumen'           => 'dokumen',
        ];

        foreach ($uploads as $inputName => $tipe) {
            Log::info("Cek input [{$inputName}]: hasFile=" . ($request->hasFile($inputName) ? 'true' : 'false'));

            if (!$request->hasFile($inputName)) continue;

            foreach ($request->file($inputName) as $file) {
                Log::info("File: {$file->getClientOriginalName()}, valid=" . ($file->isValid() ? 'true' : 'false') . ", error=" . $file->getError());

                if (!$file->isValid()) continue;

                $path = $file->store("laporan/{$laporan->id}/{$tipe}", 'public');
                Log::info("Tersimpan di: {$path}");

                Dokumen::create([
                    'id_laporan'  => $laporan->id,
                    'tipe'        => $tipe,
                    'nama_file'   => $file->getClientOriginalName(),
                    'path_file'   => $path,
                    'mime_type'   => $file->getMimeType(),
                    'ukuran_file' => $file->getSize(),
                ]);

                Log::info("Dokumen DB created untuk: {$file->getClientOriginalName()}");
            }
        }
    }

    private function hapusDokumen(array $ids, int $idLaporan): void
    {
        $docs = Dokumen::whereIn('id', $ids)
            ->where('id_laporan', $idLaporan)
            ->get();

        foreach ($docs as $dok) {
            Storage::disk('public')->delete($dok->path_file);
            $dok->delete();
        }
    }
}