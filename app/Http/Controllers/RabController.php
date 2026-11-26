<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RabController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::with(['area', 'pelanggan', 'jenisPekerjaan', 'status'])
            ->withCount('bqItems')
            ->withSum('bqItems', 'subtotal')
            ->orderByDesc('tanggal_laporan')
            ->orderByDesc('id');

        if ($request->filled('status_rab') && $request->status_rab !== 'all') {
            if ($request->status_rab === 'belum_ada') {
                $query->whereNull('nilai_rab');
            } else {
                $query->where('status_rab', $request->status_rab);
            }
        }

        if ($request->filled('area')) {
            $query->where('id_area', $request->area);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_insiden', 'like', "%{$s}%")
                    ->orWhere('no_sap', 'like', "%{$s}%")
                    ->orWhereHas('pelanggan', fn ($r) => $r->where('nama', 'like', "%{$s}%"))
                    ->orWhereHas('jenisPekerjaan', fn ($r) => $r->where('nama_jenis', 'like', "%{$s}%"));
            });
        }

        $laporans = $query->paginate(12)->withQueryString()->through(fn (Laporan $l) => [
            'id' => $l->id,
            'no_insiden' => $l->no_insiden,
            'tanggal_laporan' => $l->tanggal_laporan?->format('Y-m-d'),
            'area' => $l->area?->nama_area ?? '—',
            'pelanggan' => $l->pelanggan?->nama ?? 'Umum',
            'jenis_pekerjaan' => $l->jenisPekerjaan?->nama_jenis ?? '—',
            'status_kerja' => $l->status?->label ?? '—',
            'no_sap' => $l->no_sap,
            'nilai_rab' => $l->nilai_rab !== null ? (float) $l->nilai_rab : null,
            'bq_total' => $l->bq_items_sum_subtotal !== null ? (float) $l->bq_items_sum_subtotal : 0.0,
            'bq_items_count' => $l->bq_items_count,
            'status_rab' => $l->status_rab ?? 'draft',
            'rab_catatan' => $l->rab_catatan,
        ]);

        $summary = [
            'total_nilai' => (float) Laporan::sum('nilai_rab'),
            'count_disetujui' => Laporan::where('status_rab', 'disetujui')->count(),
            'count_diajukan' => Laporan::where('status_rab', 'diajukan')->count(),
            'count_draft' => Laporan::where('status_rab', 'draft')->whereNotNull('nilai_rab')->count(),
            'count_kosong' => Laporan::whereNull('nilai_rab')->count(),
        ];

        return Inertia::render('Rab/Index', [
            'laporans' => $laporans,
            'summary' => $summary,
            'areas' => Area::orderBy('nama_area')->get(['id', 'nama_area']),
            'filters' => $request->only(['status_rab', 'area', 'search']),
        ]);
    }

    public function update(Request $request, Laporan $laporan)
    {
        $isAdmin = (Auth::user()?->role ?? 'user') === 'admin';
        $allowedStatuses = $isAdmin ? ['draft', 'diajukan', 'disetujui'] : ['draft', 'diajukan'];

        $validated = $request->validate([
            'nilai_rab' => 'nullable|numeric|min:0',
            'no_sap' => 'nullable|string|max:50',
            'status_rab' => ['required', Rule::in($allowedStatuses)],
            'rab_catatan' => 'nullable|string|max:1000',
        ]);

        $laporan->update($validated);

        return back()->with('success', "RAB untuk insiden {$laporan->no_insiden} berhasil diperbarui.");
    }
}
