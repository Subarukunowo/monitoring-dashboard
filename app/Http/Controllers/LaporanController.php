<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Dokumen;
use App\Models\JenisPekerjaan;
use App\Models\Laporan;
use App\Models\Pelanggan;
use App\Models\Pengawas;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::with(['area', 'pelanggan', 'pengawas', 'jenisPekerjaan', 'status'])
            ->withCount(['dokumen', 'bqItems'])
            ->orderByDesc('tanggal_laporan')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $statusVal = $request->status === 'open' ? 'pending' : $request->status;
            $query->whereHas('status', function ($q) use ($statusVal) {
                if ($statusVal === 'pending') {
                    $q->whereIn('status_kerja', ['pending', 'open']);
                } else {
                    $q->where('status_kerja', $statusVal);
                }
            });
        }

        if ($request->filled('area')) {
            $query->where('id_area', $request->area);
        }

        if ($request->filled('filter')) {
            $today = now()->toDateString();
            match ($request->filter) {
                'overdue' => $query->whereHas('status', fn ($q) => $q->where('status_kerja', '!=', 'completed'))
                    ->whereNotNull('tanggal_selesai')
                    ->whereDate('tanggal_selesai', '<', $today),
                'due_today' => $query->whereHas('status', fn ($q) => $q->where('status_kerja', '!=', 'completed'))
                    ->whereDate('tanggal_selesai', $today),
                'created_today' => $query->whereDate('created_at', $today),
                'due_this_week' => $query->whereHas('status', fn ($q) => $q->where('status_kerja', '!=', 'completed'))
                    ->whereNotNull('tanggal_selesai')
                    ->whereDate('tanggal_selesai', '>=', $today)
                    ->whereDate('tanggal_selesai', '<=', now()->addDays(7)->toDateString()),
                default => null,
            };
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $driver = DB::getDriverName();
            $query->where(function ($q) use ($s, $driver) {
                $q->where('no_insiden', 'like', "%{$s}%")
                    ->orWhere('keterangan', 'like', "%{$s}%")
                    ->orWhere('no_sap', 'like', "%{$s}%")
                    ->orWhere('link_maps', 'like', "%{$s}%");

                if ($driver === 'mysql') {
                    $q->orWhereRaw("DATE_FORMAT(tanggal_laporan, '%d %M %Y') LIKE ?", ["%{$s}%"])
                        ->orWhereRaw("DATE_FORMAT(tanggal_selesai, '%d %M %Y') LIKE ?", ["%{$s}%"]);
                } else {
                    $q->orWhere('tanggal_laporan', 'like', "%{$s}%")
                        ->orWhere('tanggal_selesai', 'like', "%{$s}%");
                }

                $q->orWhereHas('status', function ($r) use ($s) {
                    $searchLower = strtolower(trim($s));
                    $statusMapping = [
                        'terbuka' => 'pending',
                        'open' => 'pending',
                        'pending' => 'pending',
                        'sedang dikerjakan' => 'on_progress',
                        'dikerjakan' => 'on_progress',
                        'proses' => 'on_progress',
                        'on progress' => 'on_progress',
                        'on_progress' => 'on_progress',
                        'ditunda' => 'on_hold',
                        'on hold' => 'on_hold',
                        'on_hold' => 'on_hold',
                        'selesai' => 'completed',
                        'completed' => 'completed',
                        'done' => 'completed',
                    ];

                    if (isset($statusMapping[$searchLower])) {
                        $r->where('status_kerja', $statusMapping[$searchLower]);
                    } else {
                        $r->where('status_kerja', 'like', "%{$s}%");
                    }
                })
                    ->orWhereHas('area', fn ($r) => $r->where('nama_area', 'like', "%{$s}%"))
                    ->orWhereHas('pelanggan', fn ($r) => $r->where(function ($sub) use ($s) {
                        $sub->where('nama', 'like', "%{$s}%")
                            ->orWhere('no_pelanggan', 'like', "%{$s}%")
                            ->orWhere('no_telepon', 'like', "%{$s}%")
                            ->orWhere('alamat', 'like', "%{$s}%");
                    }))
                    ->orWhereHas('pengawas', fn ($r) => $r->where('nama', 'like', "%{$s}%"))
                    ->orWhereHas('jenisPekerjaan', fn ($r) => $r->where('nama_jenis', 'like', "%{$s}%"));
            });
        }

        $laporans = $query->paginate(15)->withQueryString()->through(function (Laporan $laporan) {
            $statusKerja = $laporan->status?->status_kerja ?? '';
            $isOverdue = $laporan->tanggal_selesai !== null
                && $statusKerja !== 'completed'
                && $laporan->tanggal_selesai->isPast();

            return [
                'id' => $laporan->id,
                'no_insiden' => $laporan->no_insiden,
                'tanggal_laporan' => $laporan->tanggal_laporan?->format('Y-m-d'),
                'tanggal_selesai' => $laporan->tanggal_selesai?->format('Y-m-d'),
                'no_sap' => $laporan->no_sap,
                'nilai_rab' => $laporan->nilai_rab !== null ? (float) $laporan->nilai_rab : null,
                'status_rab' => $laporan->status_rab ?? 'draft',
                'is_closed' => (bool) $laporan->is_closed,
                'is_overdue' => $isOverdue,
                'dokumen_count' => $laporan->dokumen_count,
                'bq_items_count' => $laporan->bq_items_count,
                'area' => $laporan->area ? ['id' => $laporan->area->id, 'nama_area' => $laporan->area->nama_area] : null,
                'pelanggan' => $laporan->pelanggan ? [
                    'id' => $laporan->pelanggan->id,
                    'nama' => $laporan->pelanggan->nama,
                    'no_pelanggan' => $laporan->pelanggan->no_pelanggan,
                ] : null,
                'pengawas' => $laporan->pengawas ? ['id' => $laporan->pengawas->id, 'nama' => $laporan->pengawas->nama] : null,
                'jenis_pekerjaan' => $laporan->jenisPekerjaan ? ['id' => $laporan->jenisPekerjaan->id, 'nama_jenis' => $laporan->jenisPekerjaan->nama_jenis] : null,
                'status' => $laporan->status ? [
                    'id' => $laporan->status->id,
                    'status_kerja' => $laporan->status->status_kerja,
                    'label' => $laporan->status->label,
                ] : null,
            ];
        });

        $statusCounts = [
            'all' => Laporan::count(),
            'pending' => Laporan::whereHas('status', fn ($q) => $q->whereIn('status_kerja', ['pending', 'open']))->count(),
            'on_progress' => Laporan::whereHas('status', fn ($q) => $q->where('status_kerja', 'on_progress'))->count(),
            'on_hold' => Laporan::whereHas('status', fn ($q) => $q->where('status_kerja', 'on_hold'))->count(),
            'completed' => Laporan::whereHas('status', fn ($q) => $q->where('status_kerja', 'completed'))->count(),
        ];

        return Inertia::render('Laporan/Index', [
            'laporans' => $laporans,
            'statusCounts' => $statusCounts,
            'areas' => Area::orderBy('nama_area')->get(['id', 'nama_area']),
            'filters' => $request->only(['status', 'area', 'filter', 'search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Laporan/Form', [
            'mode' => 'create',
            'areas' => Area::orderBy('nama_area')->get(['id', 'nama_area']),
            'statuses' => Status::orderBy('id')->get()->map(fn ($s) => [
                'id' => $s->id,
                'status_kerja' => $s->status_kerja,
                'label' => $s->label,
            ]),
            'pengawasSuggestions' => Pengawas::orderBy('nama')->pluck('nama'),
            'jenisSuggestions' => JenisPekerjaan::orderBy('nama_jenis')->pluck('nama_jenis'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_insiden' => 'required|unique:laporan,no_insiden|max:20',
            'tanggal_laporan' => 'required|date',
            'jam_laporan' => 'nullable|date_format:H:i',
            'no_pelanggan' => 'nullable|numeric|min:1000000000',
            'nama_pelanggan' => 'required|string|max:150',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'status_pelanggan' => 'nullable|boolean',
            'id_area' => 'required|exists:areas,id',
            'nama_pengawas' => 'nullable|string|max:100',
            'jenis_pekerjaan' => 'nullable|string|max:255',
            'id_status' => 'required|exists:status,id',
            'tanggal_survei' => 'nullable|date',
            'nilai_rab' => 'nullable|numeric|min:0',
            'no_sap' => 'nullable|string|max:50',
            'tanggal_selesai' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'link_maps' => 'nullable|url|max:2048',
            'foto_pekerjaan.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_administrasi.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        DB::transaction(function () use ($request) {
            $idPelanggan = null;
            if ($request->filled('nama_pelanggan')) {
                if ($request->filled('no_pelanggan')) {
                    $pelanggan = Pelanggan::firstOrCreate(
                        ['no_pelanggan' => $request->no_pelanggan],
                        [
                            'nama' => trim($request->nama_pelanggan),
                            'no_telepon' => $request->no_telepon,
                            'alamat' => $request->alamat,
                            'status_pelanggan' => $request->boolean('status_pelanggan', true),
                        ]
                    );
                } else {
                    $pelanggan = Pelanggan::firstOrNew(
                        ['nama' => trim($request->nama_pelanggan), 'no_telepon' => $request->no_telepon]
                    );
                    if (! $pelanggan->exists) {
                        $pelanggan->alamat = $request->alamat;
                        $pelanggan->status_pelanggan = $request->boolean('status_pelanggan', true);
                        $pelanggan->save();
                    }
                }
                $idPelanggan = $pelanggan->id;
            }

            $idJenisPekerjaan = null;
            if ($request->filled('jenis_pekerjaan')) {
                $idJenisPekerjaan = JenisPekerjaan::firstOrCreate(
                    ['nama_jenis' => trim($request->jenis_pekerjaan)]
                )->id;
            }

            $idPengawas = null;
            if ($request->filled('nama_pengawas')) {
                $idPengawas = Pengawas::firstOrCreate(
                    ['nama' => trim($request->nama_pengawas)]
                )->id;
            }

            $laporan = Laporan::create([
                'no_insiden' => $request->no_insiden,
                'tanggal_laporan' => $request->tanggal_laporan,
                'jam_laporan' => $request->jam_laporan,
                'id_pelanggan' => $idPelanggan,
                'id_area' => $request->id_area,
                'id_pengawas' => $idPengawas,
                'id_jenis_pekerjaan' => $idJenisPekerjaan,
                'id_status' => $request->id_status,
                'tanggal_survei' => $request->tanggal_survei,
                'nilai_rab' => $request->nilai_rab,
                'no_sap' => $request->no_sap,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan' => $request->keterangan,
                'link_maps' => $request->link_maps,
            ]);

            $this->simpanFile($request, $laporan);
        });

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['area', 'pelanggan', 'pengawas', 'jenisPekerjaan', 'status', 'dokumen', 'bqItems', 'closedByUser']);

        $dokumenMapped = $laporan->dokumen->map(fn ($d) => [
            'id' => $d->id,
            'tipe' => $d->tipe,
            'nama_file' => $d->nama_file,
            'url' => Storage::url($d->path_file),
            'mime_type' => $d->mime_type,
            'ukuran_file' => $d->ukuran_file,
            'uploaded_at' => $d->uploaded_at?->toIso8601String(),
        ]);

        return Inertia::render('Laporan/Show', [
            'laporan' => [
                'id' => $laporan->id,
                'no_insiden' => $laporan->no_insiden,
                'tanggal_laporan' => $laporan->tanggal_laporan?->format('Y-m-d'),
                'jam_laporan' => $laporan->jam_laporan?->format('H:i'),
                'tanggal_survei' => $laporan->tanggal_survei?->format('Y-m-d'),
                'tanggal_selesai' => $laporan->tanggal_selesai?->format('Y-m-d'),
                'nilai_rab' => $laporan->nilai_rab !== null ? (float) $laporan->nilai_rab : null,
                'status_rab' => $laporan->status_rab ?? 'draft',
                'rab_catatan' => $laporan->rab_catatan,
                'no_sap' => $laporan->no_sap,
                'keterangan' => $laporan->keterangan,
                'link_maps' => $laporan->link_maps,
                'is_closed' => (bool) $laporan->is_closed,
                'closed_at' => $laporan->closed_at?->isoFormat('D MMM Y, HH:mm'),
                'closed_by_name' => $laporan->closedByUser?->name,
                'catatan_penutupan' => $laporan->catatan_penutupan,
                'created_at' => $laporan->created_at?->isoFormat('D MMM Y, HH:mm'),
                'updated_at' => $laporan->updated_at?->isoFormat('D MMM Y, HH:mm'),
                'is_overdue' => $laporan->is_overdue,
                'area' => $laporan->area,
                'pelanggan' => $laporan->pelanggan,
                'pengawas' => $laporan->pengawas,
                'jenis_pekerjaan' => $laporan->jenisPekerjaan,
                'status' => $laporan->status ? [
                    'id' => $laporan->status->id,
                    'status_kerja' => $laporan->status->status_kerja,
                    'label' => $laporan->status->label,
                ] : null,
                'dokumen' => $dokumenMapped,
                'bq_items' => $laporan->bqItems,
            ],
        ]);
    }

    public function edit(Laporan $laporan)
    {
        $laporan->load(['area', 'pelanggan', 'pengawas', 'jenisPekerjaan', 'status', 'dokumen']);

        return Inertia::render('Laporan/Form', [
            'mode' => 'edit',
            'laporan' => [
                'id' => $laporan->id,
                'no_insiden' => $laporan->no_insiden,
                'tanggal_laporan' => $laporan->tanggal_laporan?->format('Y-m-d'),
                'jam_laporan' => $laporan->jam_laporan?->format('H:i'),
                'id_area' => $laporan->id_area,
                'id_status' => $laporan->id_status,
                'nama_pengawas' => $laporan->pengawas?->nama ?? '',
                'jenis_pekerjaan' => $laporan->jenisPekerjaan?->nama_jenis ?? '',
                'tanggal_survei' => $laporan->tanggal_survei?->format('Y-m-d'),
                'tanggal_selesai' => $laporan->tanggal_selesai?->format('Y-m-d'),
                'nilai_rab' => $laporan->nilai_rab,
                'no_sap' => $laporan->no_sap ?? '',
                'link_maps' => $laporan->link_maps ?? '',
                'keterangan' => $laporan->keterangan ?? '',
                'no_pelanggan' => $laporan->pelanggan?->no_pelanggan ? (string) $laporan->pelanggan->no_pelanggan : '',
                'nama_pelanggan' => $laporan->pelanggan?->nama ?? '',
                'no_telepon' => $laporan->pelanggan?->no_telepon ?? '',
                'alamat' => $laporan->pelanggan?->alamat ?? '',
                'status_pelanggan' => (bool) ($laporan->pelanggan?->status_pelanggan ?? true),
                'dokumen' => $laporan->dokumen->map(fn ($d) => [
                    'id' => $d->id,
                    'tipe' => $d->tipe,
                    'nama_file' => $d->nama_file,
                    'url' => Storage::url($d->path_file),
                    'mime_type' => $d->mime_type,
                    'ukuran_file' => $d->ukuran_file,
                ]),
            ],
            'areas' => Area::orderBy('nama_area')->get(['id', 'nama_area']),
            'statuses' => Status::orderBy('id')->get()->map(fn ($s) => [
                'id' => $s->id,
                'status_kerja' => $s->status_kerja,
                'label' => $s->label,
            ]),
            'pengawasSuggestions' => Pengawas::orderBy('nama')->pluck('nama'),
            'jenisSuggestions' => JenisPekerjaan::orderBy('nama_jenis')->pluck('nama_jenis'),
        ]);
    }

    public function update(Request $request, Laporan $laporan)
    {
        $request->validate([
            'no_insiden' => ['required', 'max:20', Rule::unique('laporan', 'no_insiden')->ignore($laporan->id)],
            'tanggal_laporan' => 'required|date',
            'jam_laporan' => 'nullable|date_format:H:i',
            'no_pelanggan' => 'nullable|numeric|min:1000000000',
            'nama_pelanggan' => 'required|string|max:150',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'status_pelanggan' => 'nullable|boolean',
            'id_area' => 'required|exists:areas,id',
            'nama_pengawas' => 'nullable|string|max:100',
            'jenis_pekerjaan' => 'nullable|string|max:255',
            'id_status' => 'required|exists:status,id',
            'tanggal_survei' => 'nullable|date',
            'nilai_rab' => 'nullable|numeric|min:0',
            'no_sap' => 'nullable|string|max:50',
            'tanggal_selesai' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'link_maps' => 'nullable|url|max:2048',
            'foto_pekerjaan.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_administrasi.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'dokumen.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'hapus_dokumen' => 'nullable|array',
            'hapus_dokumen.*' => 'integer|exists:dokumen,id',
        ]);

        DB::transaction(function () use ($request, $laporan) {
            $idPelanggan = $laporan->id_pelanggan;
            if ($request->filled('nama_pelanggan')) {
                if ($request->filled('no_pelanggan')) {
                    $pelanggan = Pelanggan::updateOrCreate(
                        ['no_pelanggan' => $request->no_pelanggan],
                        [
                            'nama' => trim($request->nama_pelanggan),
                            'no_telepon' => $request->no_telepon,
                            'alamat' => $request->alamat,
                            'status_pelanggan' => $request->boolean('status_pelanggan', true),
                        ]
                    );
                } else {
                    if ($idPelanggan) {
                        Pelanggan::where('id', $idPelanggan)->update([
                            'nama' => trim($request->nama_pelanggan),
                            'no_telepon' => $request->no_telepon,
                            'alamat' => $request->alamat,
                            'status_pelanggan' => $request->boolean('status_pelanggan', true),
                        ]);
                    } else {
                        $pelanggan = Pelanggan::firstOrNew(
                            ['nama' => trim($request->nama_pelanggan), 'no_telepon' => $request->no_telepon]
                        );
                        $pelanggan->alamat = $request->alamat;
                        $pelanggan->status_pelanggan = $request->boolean('status_pelanggan', true);
                        $pelanggan->save();
                    }
                }
                $idPelanggan = $pelanggan->id ?? $idPelanggan;
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

            $idPengawas = $laporan->id_pengawas;
            if ($request->filled('nama_pengawas')) {
                $idPengawas = Pengawas::firstOrCreate(
                    ['nama' => trim($request->nama_pengawas)]
                )->id;
            } elseif ($request->has('nama_pengawas') && $request->nama_pengawas === '') {
                $idPengawas = null;
            }

            $laporan->update([
                'no_insiden' => $request->no_insiden,
                'tanggal_laporan' => $request->tanggal_laporan,
                'jam_laporan' => $request->jam_laporan,
                'id_pelanggan' => $idPelanggan,
                'id_area' => $request->id_area,
                'id_pengawas' => $idPengawas,
                'id_jenis_pekerjaan' => $idJenisPekerjaan,
                'id_status' => $request->id_status,
                'tanggal_survei' => $request->tanggal_survei,
                'nilai_rab' => $request->nilai_rab,
                'no_sap' => $request->no_sap,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keterangan' => $request->keterangan,
                'link_maps' => $request->link_maps,
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
            'foto_pekerjaan' => 'pekerjaan',
            'foto_administrasi' => 'administrasi',
            'dokumen' => 'dokumen',
        ];

        foreach ($uploads as $inputName => $tipe) {
            if (! $request->hasFile($inputName)) {
                continue;
            }

            foreach ($request->file($inputName) as $file) {
                if (! $file->isValid()) {
                    continue;
                }

                $path = $file->store("laporan/{$laporan->id}/{$tipe}", 'public');

                Dokumen::create([
                    'id_laporan' => $laporan->id,
                    'tipe' => $tipe,
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'mime_type' => $file->getMimeType(),
                    'ukuran_file' => $file->getSize(),
                ]);
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
