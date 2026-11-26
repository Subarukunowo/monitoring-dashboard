import * as React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { ArrowRight, Plus, RotateCcw, Search } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { AreaWorkloadList, MonthlyIncidentChart } from '@/components/LedgerCharts';
import { StatusBadge } from '@/components/StatusBadge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatDateId, formatRupiah } from '@/lib/utils';

interface ReportRow {
    id: number;
    no_insiden: string;
    area: string;
    jenis_pekerjaan: string;
    pelanggan: string;
    pengawas: string;
    status_kerja: string;
    status_label: string;
    tanggal_laporan: string | null;
    tanggal_selesai: string | null;
    nilai_rab: number | null;
    is_overdue: boolean;
    is_closed: boolean;
}

interface Props {
    statusData: {
        pending: number;
        in_progress: number;
        on_hold: number;
        completed: number;
        total: number;
    };
    workOrders: {
        overdue_open: number;
        due_today_open: number;
        assigned_to_me: number;
        created_today: number;
        created_by_me: number;
        due_this_week: number;
        total_rab: number;
        closed_contracts: number;
    };
    areaDistribution: Array<{ id: number; nama_area: string; total: number; open: number }>;
    monthlyTrend: Array<{ label: string; count: number }>;
    recentReports: ReportRow[];
    searchCount: number | null;
    areas: Array<{ id: number; nama_area: string }>;
    statuses: Array<{ id: number; status_kerja: string; label: string }>;
    filters: {
        search?: string;
        status?: string;
        area?: string;
        filter?: string;
    };
}

export default function DashboardIndex({
    statusData,
    workOrders,
    areaDistribution,
    monthlyTrend,
    recentReports,
    searchCount,
    areas,
    filters,
}: Props) {
    const [search, setSearch] = React.useState(filters.search ?? '');

    const applyFilter = (next: Record<string, string | undefined>) => {
        const merged = { ...filters, ...next };
        const clean: Record<string, string> = {};
        Object.entries(merged).forEach(([k, v]) => {
            if (v && v !== 'all') {
                clean[k] = v;
            }
        });
        router.get('/dashboard', clean, { preserveState: true, preserveScroll: true });
    };

    const handleSearchSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        applyFilter({ search: search.trim() || undefined });
    };

    const hasActiveFilters = Boolean(filters.search || filters.status || filters.area || filters.filter);

    return (
        <AppLayout
            title="Ringkasan Operasional"
            subtitle={
                workOrders.overdue_open > 0
                    ? `${workOrders.overdue_open} pekerjaan melewati deadline memerlukan tindak lanjut`
                    : 'Seluruh pekerjaan berada dalam jadwal SLA'
            }
            actions={
                <Button asChild>
                    <Link href="/laporan/create">
                        <Plus className="h-4 w-4" />
                        Buat Laporan
                    </Link>
                </Button>
            }
        >
            <Head title="Ringkasan Operasional" />

            {/* Divided KPI Strip (no icons in tinted squares, tabular figures) */}
            <div className="mb-4 grid grid-cols-2 rounded-[8px] border border-[#e3e6ec] bg-white lg:grid-cols-4 tabular-nums">
                <button
                    type="button"
                    onClick={() => applyFilter({ status: undefined, filter: undefined })}
                    className="p-4 text-left transition-colors hover:bg-[#f7f8fa] cursor-pointer"
                >
                    <div className="text-xs font-medium text-[#5b616e]">Total Insiden</div>
                    <div className="mt-1.5 text-[28px] font-medium leading-tight tracking-tight text-[#0a0a0a]">
                        {statusData.total}
                    </div>
                    <div className="mt-1 text-xs text-[#5b616e]">
                        {statusData.completed} selesai · {workOrders.closed_contracts} tutup kontrak
                    </div>
                </button>

                <button
                    type="button"
                    onClick={() => applyFilter({ filter: 'overdue', status: undefined })}
                    className="border-l border-[#e3e6ec] p-4 text-left transition-colors hover:bg-[#f7f8fa] cursor-pointer"
                >
                    <div className="text-xs font-medium text-[#5b616e]">Melewati Deadline</div>
                    <div
                        className={`mt-1.5 text-[28px] font-medium leading-tight tracking-tight ${
                            workOrders.overdue_open > 0 ? 'text-[#c4242a]' : 'text-[#0a0a0a]'
                        }`}
                    >
                        {workOrders.overdue_open}
                    </div>
                    <div className="mt-1 text-xs text-[#5b616e]">
                        {workOrders.due_this_week} jatuh tempo minggu ini
                    </div>
                </button>

                <button
                    type="button"
                    onClick={() => applyFilter({ status: 'on_progress', filter: undefined })}
                    className="border-t border-[#e3e6ec] p-4 text-left transition-colors hover:bg-[#f7f8fa] lg:border-t-0 lg:border-l cursor-pointer"
                >
                    <div className="text-xs font-medium text-[#5b616e]">Sedang Dikerjakan</div>
                    <div className="mt-1.5 text-[28px] font-medium leading-tight tracking-tight text-[#0a0a0a]">
                        {statusData.in_progress}
                    </div>
                    <div className="mt-1 text-xs text-[#5b616e]">
                        {statusData.pending} terbuka · {statusData.on_hold} ditunda
                    </div>
                </button>

                <Link
                    href="/rab"
                    className="border-l border-t border-[#e3e6ec] p-4 text-left transition-colors hover:bg-[#f7f8fa] lg:border-t-0"
                >
                    <div className="text-xs font-medium text-[#5b616e]">Total Nilai RAB</div>
                    <div className="mt-1.5 text-[22px] font-bold leading-tight tracking-tight text-[#0a0a0a] sm:text-[26px]">
                        {formatRupiah(workOrders.total_rab)}
                    </div>
                    <div className="mt-1 text-xs text-[#1d5fbf]">Kelola RAB & BQ</div>
                </Link>
            </div>

            {/* Filter Toolbar */}
            <form onSubmit={handleSearchSubmit} className="mb-4 flex flex-wrap items-center gap-2">
                <div className="relative flex-1 min-w-[220px]">
                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#667085]" />
                    <Input
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Cari No. Insiden, area, pelanggan, SAP, atau keterangan..."
                        className="pl-9"
                    />
                </div>

                <Select
                    value={filters.area ?? 'all'}
                    onValueChange={(val) => applyFilter({ area: val === 'all' ? undefined : val })}
                >
                    <SelectTrigger className="w-[170px]">
                        <SelectValue placeholder="Semua Area" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Area</SelectItem>
                        {areas.map((a) => (
                            <SelectItem key={a.id} value={String(a.id)}>
                                {a.nama_area}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>

                <Select
                    value={filters.status ?? 'all'}
                    onValueChange={(val) => applyFilter({ status: val === 'all' ? undefined : val })}
                >
                    <SelectTrigger className="w-[180px]">
                        <SelectValue placeholder="Semua Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Status</SelectItem>
                        <SelectItem value="pending">Terbuka</SelectItem>
                        <SelectItem value="on_progress">Sedang Dikerjakan</SelectItem>
                        <SelectItem value="on_hold">Ditunda</SelectItem>
                        <SelectItem value="completed">Selesai</SelectItem>
                    </SelectContent>
                </Select>

                <Button type="submit" variant="secondary">
                    Terapkan
                </Button>

                {hasActiveFilters && (
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => {
                            setSearch('');
                            router.get('/dashboard');
                        }}
                    >
                        <RotateCcw className="h-3.5 w-3.5" />
                        Reset
                    </Button>
                )}
            </form>

            {searchCount !== null && (
                <p className="mb-3 text-xs text-[#5b616e]">
                    Ditemukan <strong>{searchCount}</strong> laporan untuk pencarian “{filters.search}”.
                </p>
            )}

            {/* 12-Col Analytics Row: Balanced height */}
            <div className="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-12">
                <Card className="lg:col-span-7 flex flex-col">
                    <CardHeader className="flex flex-row items-baseline justify-between space-y-0 pb-2">
                        <div>
                            <CardTitle>Tren Insiden per Bulan</CardTitle>
                            <p className="mt-0.5 text-xs text-[#5b616e]">
                                Volume laporan masuk 6 bulan terakhir (batang biru menandai periode aktif)
                            </p>
                        </div>
                    </CardHeader>
                    <CardContent className="flex-1 pt-2">
                        <MonthlyIncidentChart data={monthlyTrend} />
                    </CardContent>
                </Card>

                <Card className="lg:col-span-5 flex flex-col">
                    <CardHeader className="flex flex-row items-baseline justify-between space-y-0 pb-3">
                        <div>
                            <CardTitle>Distribusi per Area</CardTitle>
                            <p className="mt-0.5 text-xs text-[#5b616e]">
                                Klik area untuk memfilter tabel work order
                            </p>
                        </div>
                    </CardHeader>
                    <CardContent className="flex-1">
                        <AreaWorkloadList
                            data={areaDistribution}
                            onSelectArea={(areaId) => applyFilter({ area: String(areaId) })}
                        />
                    </CardContent>
                </Card>
            </div>

            {/* Recent Work Orders Ledger */}
            <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 border-b border-[#eef0f4] pb-3">
                    <div>
                        <CardTitle>Work Order Terbaru</CardTitle>
                        <p className="mt-0.5 text-xs text-[#5b616e]">
                            Menampilkan maksimal 10 laporan terkini sesuai filter aktif
                        </p>
                    </div>
                    <Button asChild variant="ghost" size="sm">
                        <Link href="/laporan">
                            Lihat semua
                            <ArrowRight className="h-3.5 w-3.5" />
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent className="p-0">
                    {recentReports.length > 0 ? (
                        <Table>
                            <TableHeader>
                                <TableRow className="hover:bg-transparent">
                                    <TableHead>No. Insiden</TableHead>
                                    <TableHead>Pelanggan</TableHead>
                                    <TableHead>Area</TableHead>
                                    <TableHead>Jenis Pekerjaan</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="text-right">Nilai RAB</TableHead>
                                    <TableHead className="text-right">Deadline</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {recentReports.map((row) => (
                                    <TableRow
                                        key={row.id}
                                        className="cursor-pointer"
                                        onClick={() => router.visit(`/laporan/${row.id}`)}
                                    >
                                        <TableCell className="font-mono text-xs font-medium text-[#0a0a0a]">
                                            <Link
                                                href={`/laporan/${row.id}`}
                                                onClick={(e) => e.stopPropagation()}
                                                className="text-[#1d5fbf] hover:underline"
                                            >
                                                {row.no_insiden}
                                            </Link>
                                            {row.is_overdue && (
                                                <span className="ml-2 text-[11px] font-sans font-medium text-[#c4242a]">
                                                    Terlambat
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell className="max-w-[180px] truncate">{row.pelanggan}</TableCell>
                                        <TableCell className="text-[#5b616e]">{row.area}</TableCell>
                                        <TableCell className="max-w-[200px] truncate text-[#5b616e]">
                                            {row.jenis_pekerjaan}
                                        </TableCell>
                                        <TableCell>
                                            <StatusBadge statusKerja={row.status_kerja} label={row.status_label} />
                                        </TableCell>
                                        <TableCell className="text-right font-medium">
                                            {formatRupiah(row.nilai_rab)}
                                        </TableCell>
                                        <TableCell
                                            className={`text-right ${
                                                row.is_overdue ? 'font-medium text-[#c4242a]' : 'text-[#5b616e]'
                                            }`}
                                        >
                                            {formatDateId(row.tanggal_selesai)}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    ) : (
                        <div className="p-10 text-center text-sm text-[#5b616e]">
                            Tidak ada laporan yang cocok dengan filter saat ini.
                        </div>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
