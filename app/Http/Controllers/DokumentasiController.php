<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Dokumen;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DokumentasiController extends Controller
{
    public function pekerjaan(Request $request)
    {
        return $this->renderGallery($request, 'pekerjaan', 'Dokumentasi Pekerjaan');
    }

    public function administrasi(Request $request)
    {
        return $this->renderGallery($request, 'administrasi', 'Dokumentasi Administrasi');
    }

    private function renderGallery(Request $request, string $mode, string $title)
    {
        $types = $mode === 'pekerjaan' ? ['pekerjaan'] : ['administrasi', 'dokumen'];

        $query = Dokumen::with(['laporan.area', 'laporan.pelanggan'])
            ->whereIn('tipe', $types)
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id');

        if ($request->filled('laporan_id')) {
            $query->where('id_laporan', $request->laporan_id);
        }

        if ($request->filled('area')) {
            $query->whereHas('laporan', fn ($q) => $q->where('id_area', $request->area));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q
                ->where('nama_file', 'like', "%{$s}%")
                ->orWhereHas('laporan', fn ($r) => $r
                    ->where('no_insiden', 'like', "%{$s}%")
                    ->orWhereHas('pelanggan', fn ($p) => $p->where('nama', 'like', "%{$s}%"))
                )
            );
        }

        $items = $query->paginate(16)->withQueryString()->through(fn (Dokumen $d) => [
            'id' => $d->id,
            'tipe' => $d->tipe,
            'nama_file' => $d->nama_file,
            'url' => Storage::url($d->path_file),
            'mime_type' => $d->mime_type,
            'is_image' => str_starts_with($d->mime_type ?? '', 'image/'),
            'ukuran_file' => $d->ukuran_file,
            'uploaded_at' => $d->uploaded_at?->isoFormat('D MMM Y, HH:mm'),
            'laporan_id' => $d->id_laporan,
            'no_insiden' => $d->laporan?->no_insiden ?? '—',
            'area' => $d->laporan?->area?->nama_area ?? '—',
            'pelanggan' => $d->laporan?->pelanggan?->nama ?? 'Umum',
        ]);

        return Inertia::render('Dokumentasi/Index', [
            'mode' => $mode,
            'title' => $title,
            'items' => $items,
            'laporans' => Laporan::orderByDesc('tanggal_laporan')->get(['id', 'no_insiden']),
            'areas' => Area::orderBy('nama_area')->get(['id', 'nama_area']),
            'filters' => $request->only(['laporan_id', 'area', 'search']),
        ]);
    }
}
