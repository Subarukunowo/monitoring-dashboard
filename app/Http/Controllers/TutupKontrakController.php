<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TutupKontrakController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::with(['area', 'pelanggan', 'status', 'dokumen', 'closedByUser'])
            ->orderByDesc('tanggal_laporan')
            ->orderByDesc('id');

        if ($request->filled('state') && $request->state !== 'all') {
            match ($request->state) {
                'closed' => $query->where('is_closed', true),
                'ready' => $query->where('is_closed', false)
                    ->whereHas('status', fn ($q) => $q->where('status_kerja', 'completed')),
                'incomplete' => $query->where('is_closed', false)
                    ->whereHas('status', fn ($q) => $q->where('status_kerja', '!=', 'completed')),
                default => null,
            };
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q
                ->where('no_insiden', 'like', "%{$s}%")
                ->orWhere('no_sap', 'like', "%{$s}%")
                ->orWhereHas('pelanggan', fn ($r) => $r->where('nama', 'like', "%{$s}%"))
            );
        }

        $laporans = $query->paginate(12)->withQueryString()->through(function (Laporan $l) {
            $hasFotoPekerjaan = $l->dokumen->where('tipe', 'pekerjaan')->isNotEmpty();
            $hasFotoAdmin = $l->dokumen->whereIn('tipe', ['administrasi', 'dokumen'])->isNotEmpty();
            $isCompleted = ($l->status?->status_kerja ?? '') === 'completed';
            $hasSap = ! empty($l->no_sap);
            $hasRab = $l->nilai_rab !== null && (float) $l->nilai_rab > 0;

            $checks = [
                'status_selesai' => $isCompleted,
                'no_sap_terisi' => $hasSap,
                'nilai_rab_terisi' => $hasRab,
                'foto_pekerjaan' => $hasFotoPekerjaan,
                'dokumen_admin' => $hasFotoAdmin,
            ];
            $passed = count(array_filter($checks));

            return [
                'id' => $l->id,
                'no_insiden' => $l->no_insiden,
                'area' => $l->area?->nama_area ?? '—',
                'pelanggan' => $l->pelanggan?->nama ?? 'Umum',
                'status_label' => $l->status?->label ?? '—',
                'status_kerja' => $l->status?->status_kerja ?? '',
                'no_sap' => $l->no_sap,
                'nilai_rab' => $l->nilai_rab !== null ? (float) $l->nilai_rab : null,
                'is_closed' => (bool) $l->is_closed,
                'closed_at' => $l->closed_at?->isoFormat('D MMM Y, HH:mm'),
                'closed_by_name' => $l->closedByUser?->name,
                'catatan_penutupan' => $l->catatan_penutupan,
                'checks' => $checks,
                'passed_checks' => $passed,
                'total_checks' => count($checks),
                'can_close' => $isCompleted && ! $l->is_closed,
            ];
        });

        $summary = [
            'total' => Laporan::count(),
            'closed' => Laporan::where('is_closed', true)->count(),
            'ready' => Laporan::where('is_closed', false)
                ->whereHas('status', fn ($q) => $q->where('status_kerja', 'completed'))
                ->count(),
            'in_progress' => Laporan::where('is_closed', false)
                ->whereHas('status', fn ($q) => $q->where('status_kerja', '!=', 'completed'))
                ->count(),
        ];

        return Inertia::render('TutupKontrak/Index', [
            'laporans' => $laporans,
            'summary' => $summary,
            'filters' => $request->only(['state', 'search']),
        ]);
    }

    public function close(Request $request, Laporan $laporan)
    {
        $request->validate([
            'catatan_penutupan' => 'nullable|string|max:1000',
        ]);

        $laporan->update([
            'is_closed' => true,
            'closed_at' => now(),
            'closed_by' => Auth::id(),
            'catatan_penutupan' => $request->catatan_penutupan,
        ]);

        return back()->with('success', "Kontrak insiden {$laporan->no_insiden} berhasil ditutup.");
    }

    public function reopen(Laporan $laporan)
    {
        $laporan->update([
            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,
        ]);

        return back()->with('success', "Kontrak insiden {$laporan->no_insiden} dibuka kembali.");
    }
}
