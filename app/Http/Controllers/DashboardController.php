<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Laporan;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $endOfWeek = Carbon::now()->endOfWeek();
        $currentPengawasId = optional(Auth::user())->id ?? 1;

        $statusData = [
            'pending' => Laporan::whereHas('status', fn ($q) => $q->whereIn('status_kerja', ['pending', 'open']))->count(),
            'in_progress' => Laporan::whereHas('status', fn ($q) => $q->where('status_kerja', 'on_progress'))->count(),
            'on_hold' => Laporan::whereHas('status', fn ($q) => $q->where('status_kerja', 'on_hold'))->count(),
            'completed' => Laporan::whereHas('status', fn ($q) => $q->where('status_kerja', 'completed'))->count(),
        ];
        $statusData['total'] = array_sum($statusData);

        $workOrders = [
            'overdue_open' => Laporan::whereHas('status', fn ($q) => $q->whereNotIn('status_kerja', ['completed']))
                ->whereNotNull('tanggal_selesai')
                ->whereDate('tanggal_selesai', '<', $today)
                ->count(),
            'due_today_open' => Laporan::whereHas('status', fn ($q) => $q->whereNotIn('status_kerja', ['completed']))
                ->whereDate('tanggal_selesai', $today)
                ->count(),
            'assigned_to_me' => Laporan::where('id_pengawas', $currentPengawasId)
                ->whereHas('status', fn ($q) => $q->whereNotIn('status_kerja', ['completed']))
                ->count(),
            'created_today' => Laporan::whereDate('created_at', $today)->count(),
            'created_by_me' => Laporan::where('id_pengawas', $currentPengawasId)->count(),
            'due_this_week' => Laporan::whereHas('status', fn ($q) => $q->whereNotIn('status_kerja', ['completed']))
                ->whereNotNull('tanggal_selesai')
                ->whereDate('tanggal_selesai', '>=', $today)
                ->whereDate('tanggal_selesai', '<=', $endOfWeek)
                ->count(),
            'total_rab' => (float) Laporan::sum('nilai_rab'),
            'closed_contracts' => Laporan::where('is_closed', true)->count(),
        ];

        $areaDistribution = Area::withCount([
            'laporan',
            'laporan as open_count' => fn ($q) => $q->whereHas('status', fn ($s) => $s->where('status_kerja', '!=', 'completed')),
        ])->orderByDesc('laporan_count')->get()->map(fn ($area) => [
            'id' => $area->id,
            'nama_area' => $area->nama_area,
            'total' => $area->laporan_count,
            'open' => $area->open_count,
        ]);

        // Monthly incident volume over latest 6 distinct months present (or last 6 months from latest report)
        $latestDate = Laporan::max('tanggal_laporan');
        $anchor = $latestDate ? Carbon::parse($latestDate)->startOfMonth() : Carbon::now()->startOfMonth();
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = $anchor->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $monthlyTrend[] = [
                'label' => $monthStart->locale('id')->isoFormat('MMM YY'),
                'count' => Laporan::whereDate('tanggal_laporan', '>=', $monthStart->toDateString())
                    ->whereDate('tanggal_laporan', '<=', $monthEnd->toDateString())
                    ->count(),
            ];
        }

        $query = Laporan::with(['area', 'jenisPekerjaan', 'status', 'pelanggan', 'pengawas'])
            ->orderByDesc('tanggal_laporan')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $statusValue = $request->status === 'open' ? 'pending' : $request->status;
            $query->whereHas('status', function ($q) use ($statusValue) {
                if ($statusValue === 'pending') {
                    $q->whereIn('status_kerja', ['pending', 'open']);
                } else {
                    $q->where('status_kerja', $statusValue);
                }
            });
        }

        if ($request->filled('area')) {
            $query->where('id_area', $request->area);
        }

        if ($request->filled('filter')) {
            match ($request->filter) {
                'overdue', 'overdue_open' => $query
                    ->whereHas('status', fn ($q) => $q->whereNotIn('status_kerja', ['completed']))
                    ->whereNotNull('tanggal_selesai')
                    ->whereDate('tanggal_selesai', '<', $today),
                'due_today' => $query
                    ->whereHas('status', fn ($q) => $q->whereNotIn('status_kerja', ['completed']))
                    ->whereDate('tanggal_selesai', $today),
                'created_today' => $query->whereDate('created_at', $today),
                'due_this_week' => $query
                    ->whereHas('status', fn ($q) => $q->whereNotIn('status_kerja', ['completed']))
                    ->whereNotNull('tanggal_selesai')
                    ->whereDate('tanggal_selesai', '>=', $today)
                    ->whereDate('tanggal_selesai', '<=', $endOfWeek),
                default => null,
            };
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_insiden', 'like', "%{$s}%")
                    ->orWhere('keterangan', 'like', "%{$s}%")
                    ->orWhere('no_sap', 'like', "%{$s}%")
                    ->orWhereHas('area', fn ($r) => $r->where('nama_area', 'like', "%{$s}%"))
                    ->orWhereHas('jenisPekerjaan', fn ($r) => $r->where('nama_jenis', 'like', "%{$s}%"))
                    ->orWhereHas('pelanggan', fn ($r) => $r->where('nama', 'like', "%{$s}%")
                        ->orWhere('no_pelanggan', 'like', "%{$s}%"))
                    ->orWhereHas('status', function ($r) use ($s) {
                        $map = [
                            'terbuka' => ['pending', 'open'],
                            'open' => ['pending', 'open'],
                            'pending' => ['pending', 'open'],
                            'sedang dikerjakan' => ['on_progress'],
                            'dikerjakan' => ['on_progress'],
                            'proses' => ['on_progress'],
                            'on progress' => ['on_progress'],
                            'on_progress' => ['on_progress'],
                            'ditunda' => ['on_hold'],
                            'on hold' => ['on_hold'],
                            'on_hold' => ['on_hold'],
                            'selesai' => ['completed'],
                            'completed' => ['completed'],
                            'done' => ['completed'],
                        ];
                        $key = strtolower(trim($s));
                        $values = $map[$key] ?? null;
                        if ($values) {
                            $r->whereIn('status_kerja', $values);
                        } else {
                            $r->where('status_kerja', 'like', "%{$s}%");
                        }
                    });
            });
        }

        $recentReports = $query->limit(10)->get()->map(function ($report) {
            $statusKerja = $report->status?->status_kerja ?? '';
            $isOverdue = $report->tanggal_selesai !== null
                && $report->tanggal_selesai->isPast()
                && $statusKerja !== 'completed';

            return [
                'id' => $report->id,
                'no_insiden' => $report->no_insiden,
                'area' => $report->area?->nama_area ?? '—',
                'jenis_pekerjaan' => $report->jenisPekerjaan?->nama_jenis ?? '—',
                'pelanggan' => $report->pelanggan?->nama ?? 'Umum',
                'pengawas' => $report->pengawas?->nama ?? '—',
                'status_kerja' => $statusKerja,
                'status_label' => $report->status?->label ?? '—',
                'tanggal_laporan' => $report->tanggal_laporan?->format('Y-m-d'),
                'tanggal_selesai' => $report->tanggal_selesai?->format('Y-m-d'),
                'nilai_rab' => $report->nilai_rab !== null ? (float) $report->nilai_rab : null,
                'is_overdue' => $isOverdue,
                'is_closed' => (bool) $report->is_closed,
            ];
        });

        $searchCount = $request->filled('search') ? $recentReports->count() : null;

        return Inertia::render('Dashboard/Index', [
            'statusData' => $statusData,
            'workOrders' => $workOrders,
            'areaDistribution' => $areaDistribution,
            'monthlyTrend' => $monthlyTrend,
            'recentReports' => $recentReports,
            'searchCount' => $searchCount,
            'areas' => Area::orderBy('nama_area')->get(['id', 'nama_area']),
            'statuses' => Status::orderBy('id')->get()->map(fn ($s) => [
                'id' => $s->id,
                'status_kerja' => $s->status_kerja,
                'label' => $s->label,
            ]),
            'filters' => $request->only(['search', 'status', 'area', 'filter']),
        ]);
    }
}
