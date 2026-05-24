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
        
        // 🔐 Ganti dengan auth()->user()->pengawas_id jika sudah ada login system
        $currentPengawasId = 1; 

        // 🔍 Ambil keyword search
        $search = $request->get('search');

        // --- 1. STATUS OVERVIEW ---
        $statusCounts = DB::table('laporan')
            ->select('id_status', DB::raw('count(*) as total'))
            ->groupBy('id_status')
            ->pluck('total', 'id_status');

        $statusData = [
            'open'        => $statusCounts[1] ?? 0,  // pending
            'in_progress' => $statusCounts[2] ?? 0,  // on_progress
            'on_hold'     => 0,
            'completed'   => $statusCounts[3] ?? 0,  // completed
        ];

        // --- 2. WORK ORDERS STATS ---
        $workOrders = [
            'overdue_open' => DB::table('laporan')
                ->whereIn('id_status', [1, 2])
                ->where('tanggal_survei', '<', $today)
                ->whereNull('tanggal_selesai')
                ->count(),
            'high_priority_open' => 0,
            'due_today_open' => DB::table('laporan')
                ->whereIn('id_status', [1, 2])
                ->whereDate('tanggal_survei', $today)
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
                ->whereBetween('tanggal_survei', [$startOfWeek, $endOfWeek])
                ->count(),
        ];

        // --- 3. RECENT ACTIVITY + SEARCH ---
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
            ->where('laporan.id_pengawas', $currentPengawasId) // Filter by user
            ->orderBy('laporan.created_at', 'desc');

        // 🔍 Apply Search Filter
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

        return view('dashboard', [
            'statusData'     => $statusData,
            'workOrders'     => $workOrders,
            'recentReports'  => $recentReports,
            'search'         => $search,
            'searchCount'    => $searchCount,
        ]);
    }
}