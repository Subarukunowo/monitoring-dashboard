<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today       = Carbon::today();
        $endOfWeek   = Carbon::now()->endOfWeek();

        // Gunakan auth user id (pengawas). Fallback ke 1 jika belum ada auth.
        $currentPengawasId = optional(Auth::user())->id ?? 1;

        // --- 1. STATUS OVERVIEW (global, tidak terpengaruh filter) ---
        // Join ke tabel status agar tidak bergantung pada hardcode ID.
        $statusData = [
            'pending'     => Laporan::whereHas('status', fn($q) => $q->whereIn('status_kerja', ['pending', 'open']))->count(),
            'in_progress' => Laporan::whereHas('status', fn($q) => $q->where('status_kerja', 'on_progress'))->count(),
            'on_hold'     => Laporan::whereHas('status', fn($q) => $q->where('status_kerja', 'on_hold'))->count(),
            'completed'   => Laporan::whereHas('status', fn($q) => $q->where('status_kerja', 'completed'))->count(),
        ];

        // --- 2. WORK ORDERS STATS (global, tidak terpengaruh filter) ---
        $notDone = fn($q) => $q->whereHas('status', fn($s) => $s->whereNotIn('status_kerja', ['completed']));

        $workOrders = [
            'overdue_open' => Laporan::whereHas('status', fn($q) => $q->whereNotIn('status_kerja', ['completed']))
                ->whereNotNull('tanggal_selesai')
                ->whereDate('tanggal_selesai', '<', $today)
                ->count(),

            'due_today_open' => Laporan::whereHas('status', fn($q) => $q->whereNotIn('status_kerja', ['completed']))
                ->whereDate('tanggal_selesai', $today)
                ->count(),

            'assigned_to_me' => Laporan::where('id_pengawas', $currentPengawasId)
                ->whereHas('status', fn($q) => $q->whereNotIn('status_kerja', ['completed']))
                ->count(),

            'created_today' => Laporan::whereDate('created_at', $today)->count(),

            'created_by_me' => Laporan::where('id_pengawas', $currentPengawasId)->count(),

            'due_this_week' => Laporan::whereHas('status', fn($q) => $q->whereNotIn('status_kerja', ['completed']))
                ->whereNotNull('tanggal_selesai')
                ->whereDate('tanggal_selesai', '>=', $today)
                ->whereDate('tanggal_selesai', '<=', $endOfWeek)
                ->count(),
        ];

        // --- 3. RECENT ACTIVITY QUERY (terfilter) ---
        $query = Laporan::with(['area', 'jenisPekerjaan', 'status', 'pelanggan'])
            ->orderBy('created_at', 'desc');

        // Filter: Status
        if ($request->filled('status')) {
            // Normalise: 'open' dan 'pending' dianggap sama
            $statusValue = $request->status === 'open' ? 'pending' : $request->status;

            $query->whereHas('status', function ($q) use ($statusValue) {
                if ($statusValue === 'pending') {
                    $q->whereIn('status_kerja', ['pending', 'open']);
                } else {
                    $q->where('status_kerja', $statusValue);
                }
            });
        }

        // Filter: Area
        if ($request->filled('area')) {
            $query->where('id_area', $request->area);
        }

        // Filter: Cepat (overdue, due_today, dll)
        if ($request->filled('filter')) {
            match ($request->filter) {
                'overdue', 'overdue_open' =>
                    $query->whereHas('status', fn($q) => $q->whereNotIn('status_kerja', ['completed']))
                          ->whereNotNull('tanggal_selesai')
                          ->whereDate('tanggal_selesai', '<', $today),

                'due_today' =>
                    $query->whereHas('status', fn($q) => $q->whereNotIn('status_kerja', ['completed']))
                          ->whereDate('tanggal_selesai', $today),

                'created_today' =>
                    $query->whereDate('created_at', $today),

                'due_this_week' =>
                    $query->whereHas('status', fn($q) => $q->whereNotIn('status_kerja', ['completed']))
                          ->whereNotNull('tanggal_selesai')
                          ->whereDate('tanggal_selesai', '>=', $today)
                          ->whereDate('tanggal_selesai', '<=', $endOfWeek),

                default => null,
            };
        }

        // Filter: Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_insiden', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%")
                  ->orWhere('no_sap', 'like', "%{$s}%")
                  ->orWhereHas('area', fn($r) => $r->where('nama_area', 'like', "%{$s}%"))
                  ->orWhereHas('jenisPekerjaan', fn($r) => $r->where('nama_jenis', 'like', "%{$s}%"))
                  ->orWhereHas('pelanggan', fn($r) => $r->where('nama', 'like', "%{$s}%")
                      ->orWhere('no_pelanggan', 'like', "%{$s}%"))
                  ->orWhereHas('status', function ($r) use ($s) {
                      $map = [
                          'terbuka'           => ['pending', 'open'],
                          'open'              => ['pending', 'open'],
                          'pending'           => ['pending', 'open'],
                          'sedang dikerjakan' => ['on_progress'],
                          'dikerjakan'        => ['on_progress'],
                          'proses'            => ['on_progress'],
                          'on progress'       => ['on_progress'],
                          'on_progress'       => ['on_progress'],
                          'ditunda'           => ['on_hold'],
                          'on hold'           => ['on_hold'],
                          'on_hold'           => ['on_hold'],
                          'selesai'           => ['completed'],
                          'completed'         => ['completed'],
                          'done'              => ['completed'],
                      ];
                      $key    = strtolower(trim($s));
                      $values = $map[$key] ?? null;
                      if ($values) {
                          $r->whereIn('status_kerja', $values);
                      } else {
                          $r->where('status_kerja', 'like', "%{$s}%");
                      }
                  });
            });
        }

        $recentReports = $query->limit(10)->get();
        $searchCount   = $request->filled('search') ? $recentReports->count() : null;

        return view('dashboard.overview', compact(
            'statusData',
            'workOrders',
            'recentReports',
            'searchCount',
        ));
    }
}