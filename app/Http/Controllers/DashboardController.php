<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        // 🔐 Ganti dengan auth()->id() atau auth()->user()->pengawas_id saat login system siap
        $currentPengawasId = 1; 

        // 🔍 Ambil parameter filter
        $search = $request->get('search');
        $statusFilter = $request->get('status');      // open, on_progress, completed
        $filterType = $request->get('filter');         // overdue, due_today, created_today, due_this_week
        $areaFilter = $request->get('area');           // id area

        // --- 1. STATUS OVERVIEW (GLOBAL - tidak terpengaruh filter) ---
        $statusCounts = DB::table('laporan')
            ->select('id_status', DB::raw('count(*) as total'))
            ->groupBy('id_status')
            ->pluck('total', 'id_status');

        $statusData = [
            'open'        => $statusCounts[1] ?? 0,  // pending/open
            'in_progress' => $statusCounts[2] ?? 0,  // on_progress
            'on_hold'     => 0,
            'completed'   => $statusCounts[3] ?? 0,  // completed
        ];

        // --- 2. WORK ORDERS STATS (GLOBAL - tidak terpengaruh filter) ---
        $workOrders = [
            'overdue_open' => DB::table('laporan')
                ->whereIn('id_status', [1, 2])
                ->whereNotNull('tanggal_selesai')
                ->whereDate('tanggal_selesai', '<', $today)
                ->count(),
            'high_priority_open' => 0,
            'due_today_open' => DB::table('laporan')
                ->whereIn('id_status', [1, 2])
                ->whereDate('tanggal_selesai', $today)
                ->count(),
            'assigned_to_me' => DB::table('laporan')
                ->where('id_pengawas', $currentPengawasId)
                ->whereIn('id_status', [1, 2])
                ->count(),
            'created_today' => DB::table('laporan')
                ->whereDate('created_at', $today)
                ->count(),
            'created_by_me' => DB::table('laporan')
                ->where('id_pengawas', $currentPengawasId)
                ->count(),
            'due_this_week' => DB::table('laporan')
                ->whereIn('id_status', [1, 2])
                ->whereNotNull('tanggal_selesai')
                ->whereDate('tanggal_selesai', '>=', $today)
                ->whereDate('tanggal_selesai', '<=', $endOfWeek)
                ->count(),
        ];

        // --- 3. RECENT ACTIVITY QUERY (TERFILTER) ---
        $query = DB::table('laporan')
            ->join('areas', 'laporan.id_area', '=', 'areas.id')
            ->join('jenis_pekerjaan', 'laporan.id_jenis_pekerjaan', '=', 'jenis_pekerjaan.id')
            ->join('status', 'laporan.id_status', '=', 'status.id')
            ->leftJoin('pelanggan', 'laporan.id_pelanggan', '=', 'pelanggan.id')
            ->select(
                'laporan.*', 
                'areas.nama_area', 
                'jenis_pekerjaan.nama_jenis', 
                'status.status_kerja',
                'pelanggan.nama as nama_pelanggan'
            )
            ->where('laporan.id_pengawas', $currentPengawasId)
            ->orderBy('laporan.created_at', 'desc');

        // 🔹 Filter by Status (mapping ke id_status)
        if ($statusFilter) {
            $statusMap = [
                'open'        => [1],
                'on_progress' => [2],
                'completed'   => [3],
                'on_hold'     => [], // tidak ada di DB
            ];
            $statusIds = $statusMap[$statusFilter] ?? [];
            if (!empty($statusIds)) {
                $query->whereIn('laporan.id_status', $statusIds);
            }
        }

        // 🔹 Filter by Area
        if ($areaFilter) {
            $query->where('laporan.id_area', $areaFilter);
        }

        // 🔹 Filter Cepat (filter type)
        if ($filterType) {
            match ($filterType) {
                'overdue' => $query->whereIn('laporan.id_status', [1, 2])
                                   ->whereNotNull('laporan.tanggal_selesai')
                                   ->whereDate('laporan.tanggal_selesai', '<', $today),
                'due_today' => $query->whereIn('laporan.id_status', [1, 2])
                                     ->whereDate('laporan.tanggal_selesai', $today),
                'created_today' => $query->whereDate('laporan.created_at', $today),
                'due_this_week' => $query->whereIn('laporan.id_status', [1, 2])
                                         ->whereNotNull('laporan.tanggal_selesai')
                                         ->whereDate('laporan.tanggal_selesai', '>=', $today)
                                         ->whereDate('laporan.tanggal_selesai', '<=', $endOfWeek),
                default => null,
            };
        }

        // 🔹 Filter Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('laporan.no_insiden', 'LIKE', "%{$search}%")
                  ->orWhere('laporan.keterangan', 'LIKE', "%{$search}%")
                  ->orWhere('laporan.no_sap', 'LIKE', "%{$search}%")
                  ->orWhere('areas.nama_area', 'LIKE', "%{$search}%")
                  ->orWhere('jenis_pekerjaan.nama_jenis', 'LIKE', "%{$search}%")
                  ->orWhere('pelanggan.nama', 'LIKE', "%{$search}%");
            });
        }

        $recentReports = $query->limit(10)->get();
        $searchCount = $search ? $recentReports->count() : null;

        return view('dashboard.overview', [
            'statusData'     => $statusData,
            'workOrders'     => $workOrders,
            'recentReports'  => $recentReports,
            'search'         => $search,
            'searchCount'    => $searchCount,
        ]);
    }
}