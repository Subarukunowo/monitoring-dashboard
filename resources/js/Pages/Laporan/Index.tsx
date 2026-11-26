import * as React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Plus, RotateCcw, Search } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { PaginationBar } from '@/components/PaginationBar';
import { StatusBadge } from '@/components/StatusBadge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatDateId, formatRupiah } from '@/lib/utils';
import type { Paginated } from '@/types';

interface LaporanItem {
    id: number;
    no_insiden: string;
    tanggal_laporan: string | null;
    tanggal_selesai: string | null;
    no_sap: string | null;
    nilai_rab: number | null;
    status_rab: string;
    is_closed: boolean;
    is_overdue: boolean;
    dokumen_count: number;
    bq_items_count: number;
    area: { id: number; nama_area: string } | null;
    pelanggan: { id: number; nama: string; no_pelanggan: string | null } | null;
    pengawas: { id: number; nama: string } | null;
    jenis_pekerjaan: { id: number; nama_jenis: string } | null;
    status: { id: number; status_kerja: string; label: string } | null;
}

interface Props {
    laporans: Paginated<LaporanItem>;
    statusCounts: {
        all: number;
        pending: number;
        on_progress: number;
        on_hold: number;
        completed: number;
    };
    areas: Array<{ id: number; nama_area: string }>;
    filters: {
        status?: string;
        area?: string;
        filter?: string;
        search?: string;
    };
}

export default function LaporanIndex({ laporans, statusCounts, areas, filters }: Props) {
    const [search, setSearch] = React.useState(filters.search ?? '');

    const applyFilter = (next: Record<string, string | undefined>) => {
        const merged = { ...filters, ...next };
        const clean: Record<string, string> = {};
        Object.entries(merged).forEach(([k, v]) => {
            if (v && v !== 'all') {
                clean[k] = v;
            }
        });
        router.get('/laporan', clean, { preserveState: true, preserveScroll: true });
    };

    const countCards = [
        { key: 'all', label: 'Semua Laporan', value: statusCounts.all },
        { key: 'pending', label: 'Terbuka', value: statusCounts.pending },
        { key: 'on_progress', label: 'Sedang Dikerjakan', value: statusCounts.on_progress },
        { key: 'on_hold', label: 'Ditunda', value: statusCounts.on_hold },
        { key: 'completed', label: 'Selesai', value: statusCounts.completed },
    ];

    const activeStatus = filters.status ?? 'all';

    return (
        <AppLayout
            title="Laporan Insiden"
            subtitle={`Total ${laporans.total} work order tercatat dalam buku pantau`}
            actions={
                <Button asChild>
                    <Link href="/laporan/create">
                        <Plus className="h-4 w-4" />
                        Buat Laporan Baru
                    </Link>
                </Button>
            }
        >
            <Head title="Laporan Insiden" />

            {/* Peer Count Cards as Type Filter (app-screens.md List pattern) */}
            <div className="mb-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5 tabular-nums">
                {countCards.map((card) => {
                    const selected = activeStatus === card.key;

                    return (
                        <button
                            key={card.key}
                            type="button"
                            onClick={() => applyFilter({ status: card.key === 'all' ? undefined : card.key })}
                            className={`rounded-[8px] border p-3 text-left transition-colors cursor-pointer ${
                                selected
                                    ? 'border-[1.5px] border-[#141414] bg-white'
                                    : 'border-[#e3e6ec] bg-white hover:bg-[#f7f8fa]'
                            }`}
                        >
                            <div className="text-[13px] text-[#5b616e]">{card.label}</div>
                            <div className="mt-1 text-base font-semibold text-[#0a0a0a]">{card.value}</div>
                        </button>
                    );
                })}
            </div>

            {/* Filter Bar */}
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    applyFilter({ search: search.trim() || undefined });
                }}
                className="mb-4 flex flex-wrap items-center gap-2"
            >
                <div className="relative flex-1 min-w-[220px]">
                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#667085]" />
                    <Input
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Cari No. Insiden, pelanggan, SAP, alamat..."
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

                <Button type="submit" variant="secondary">
                    Cari
                </Button>

                {(filters.search || filters.status || filters.area || filters.filter) && (
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => {
                            setSearch('');
                            router.get('/laporan');
                        }}
                    >
                        <RotateCcw className="h-3.5 w-3.5" />
                        Reset
                    </Button>
                )}
            </form>

            {/* Ledger Table */}
            <Card>
                <CardContent className="p-0">
                    {laporans.data.length > 0 ? (
                        <>
                            <Table>
                                <TableHeader>
                                    <TableRow className="hover:bg-transparent">
                                        <TableHead>No. Insiden</TableHead>
                                        <TableHead>Pelanggan</TableHead>
                                        <TableHead>Area</TableHead>
                                        <TableHead>Jenis Pekerjaan</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead className="text-right">RAB</TableHead>
                                        <TableHead className="text-right">Deadline</TableHead>
                                        <TableHead className="text-right">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {laporans.data.map((item) => (
                                        <TableRow
                                            key={item.id}
                                            className="cursor-pointer"
                                            onClick={() => router.visit(`/laporan/${item.id}`)}
                                        >
                                            <TableCell className="font-mono text-xs font-medium">
                                                <Link
                                                    href={`/laporan/${item.id}`}
                                                    onClick={(e) => e.stopPropagation()}
                                                    className="text-[#1d5fbf] hover:underline"
                                                >
                                                    {item.no_insiden}
                                                </Link>
                                                <div className="font-sans text-[11px] text-[#5b616e]">
                                                    {formatDateId(item.tanggal_laporan)}
                                                    {item.no_sap ? ` · ${item.no_sap}` : ''}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div className="max-w-[180px] truncate font-medium">
                                                    {item.pelanggan?.nama ?? 'Umum / Non-Pelanggan'}
                                                </div>
                                                {item.pelanggan?.no_pelanggan && (
                                                    <div className="font-mono text-[11px] text-[#5b616e]">
                                                        {item.pelanggan.no_pelanggan}
                                                    </div>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-[#5b616e]">
                                                {item.area?.nama_area ?? '—'}
                                            </TableCell>
                                            <TableCell className="max-w-[190px] truncate text-[#5b616e]">
                                                {item.jenis_pekerjaan?.nama_jenis ?? '—'}
                                            </TableCell>
                                            <TableCell>
                                                <StatusBadge
                                                    statusKerja={item.status?.status_kerja}
                                                    label={item.status?.label}
                                                />
                                            </TableCell>
                                            <TableCell className="text-right font-medium">
                                                {formatRupiah(item.nilai_rab)}
                                            </TableCell>
                                            <TableCell
                                                className={`text-right ${
                                                    item.is_overdue ? 'font-medium text-[#c4242a]' : 'text-[#5b616e]'
                                                }`}
                                            >
                                                {formatDateId(item.tanggal_selesai)}
                                            </TableCell>
                                            <TableCell
                                                className="text-right"
                                                onClick={(e) => e.stopPropagation()}
                                            >
                                                <Link
                                                    href={`/laporan/${item.id}`}
                                                    className="text-xs font-medium text-[#0a0a0a] hover:text-[#1d5fbf] hover:underline"
                                                >
                                                    Detail
                                                </Link>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                            <div className="border-t border-[#eef0f4] px-4 pb-4">
                                <PaginationBar paginated={laporans} />
                            </div>
                        </>
                    ) : (
                        <div className="p-12 text-center">
                            <p className="text-sm font-medium text-[#0a0a0a]">Belum ada laporan tercatat</p>
                            <p className="mt-1 text-xs text-[#5b616e]">
                                Mulai dengan menambahkan laporan insiden distribusi baru.
                            </p>
                            <Button asChild className="mt-4">
                                <Link href="/laporan/create">
                                    <Plus className="h-4 w-4" />
                                    Buat Laporan Baru
                                </Link>
                            </Button>
                        </div>
                    )}
                </CardContent>
            </Card>
        </AppLayout>
    );
}
