import * as React from 'react';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { Pencil, RotateCcw, Search } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { PaginationBar } from '@/components/PaginationBar';
import { RabStatusBadge } from '@/components/StatusBadge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { formatDateId, formatRupiah } from '@/lib/utils';
import type { PageProps, Paginated } from '@/types';

interface RabRow {
    id: number;
    no_insiden: string;
    tanggal_laporan: string | null;
    area: string;
    pelanggan: string;
    jenis_pekerjaan: string;
    status_kerja: string;
    no_sap: string | null;
    nilai_rab: number | null;
    bq_total: number;
    bq_items_count: number;
    status_rab: string;
    rab_catatan: string | null;
}

interface Props {
    laporans: Paginated<RabRow>;
    summary: {
        total_nilai: number;
        count_disetujui: number;
        count_diajukan: number;
        count_draft: number;
        count_kosong: number;
    };
    areas: Array<{ id: number; nama_area: string }>;
    filters: {
        status_rab?: string;
        area?: string;
        search?: string;
    };
}

export default function RabIndex({ laporans, summary, areas, filters }: Props) {
    const { props } = usePage<PageProps>();
    const isAdmin = Boolean(props.auth?.user?.is_admin);
    const [search, setSearch] = React.useState(filters.search ?? '');
    const [editing, setEditing] = React.useState<RabRow | null>(null);

    const form = useForm({
        nilai_rab: '',
        no_sap: '',
        status_rab: 'draft',
        rab_catatan: '',
    });

    const openEdit = (row: RabRow) => {
        setEditing(row);
        form.setData({
            nilai_rab: row.nilai_rab !== null ? String(row.nilai_rab) : String(row.bq_total || ''),
            no_sap: row.no_sap ?? '',
            status_rab: row.status_rab || 'draft',
            rab_catatan: row.rab_catatan ?? '',
        });
    };

    const submitEdit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!editing) return;
        form.patch(`/rab/${editing.id}`, {
            preserveScroll: true,
            onSuccess: () => setEditing(null),
        });
    };

    const applyFilter = (next: Record<string, string | undefined>) => {
        const merged = { ...filters, ...next };
        const clean: Record<string, string> = {};
        Object.entries(merged).forEach(([k, v]) => {
            if (v && v !== 'all') clean[k] = v;
        });
        router.get('/rab', clean, { preserveState: true, preserveScroll: true });
    };

    return (
        <AppLayout
            title="Rencana Anggaran Biaya (RAB)"
            subtitle="Pantau nilai anggaran pekerjaan, nomor SAP/TUG, dan persetujuan RAB."
            actions={
                <Button asChild variant="outline">
                    <Link href="/bill-of-quantity">Kelola Bill of Quantity</Link>
                </Button>
            }
        >
            <Head title="RAB" />

            {/* Divided KPI Strip */}
            <div className="mb-4 grid grid-cols-2 rounded-[8px] border border-[#e3e6ec] bg-white lg:grid-cols-4 tabular-nums">
                <div className="p-4">
                    <div className="text-xs text-[#5b616e]">Total Akumulasi RAB</div>
                    <div className="mt-1.5 text-xl font-bold text-[#0a0a0a] sm:text-2xl">
                        {formatRupiah(summary.total_nilai)}
                    </div>
                </div>
                <div className="border-l border-[#e3e6ec] p-4">
                    <div className="text-xs text-[#5b616e]">RAB Disetujui</div>
                    <div className="mt-1.5 text-2xl font-medium text-[#15803d]">{summary.count_disetujui}</div>
                </div>
                <div className="border-t border-[#e3e6ec] p-4 lg:border-t-0 lg:border-l">
                    <div className="text-xs text-[#5b616e]">Menunggu Persetujuan</div>
                    <div className="mt-1.5 text-2xl font-medium text-[#92400e]">{summary.count_diajukan}</div>
                </div>
                <div className="border-l border-t border-[#e3e6ec] p-4 lg:border-t-0">
                    <div className="text-xs text-[#5b616e]">Belum Ada RAB</div>
                    <div className="mt-1.5 text-2xl font-medium text-[#5b616e]">{summary.count_kosong}</div>
                </div>
            </div>

            {/* Filter bar */}
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
                        placeholder="Cari No. Insiden, No. SAP, pelanggan..."
                        className="pl-9"
                    />
                </div>

                <Select
                    value={filters.status_rab ?? 'all'}
                    onValueChange={(val) => applyFilter({ status_rab: val === 'all' ? undefined : val })}
                >
                    <SelectTrigger className="w-[180px]">
                        <SelectValue placeholder="Status RAB" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Status RAB</SelectItem>
                        <SelectItem value="draft">Draft</SelectItem>
                        <SelectItem value="diajukan">Diajukan</SelectItem>
                        <SelectItem value="disetujui">Disetujui</SelectItem>
                        <SelectItem value="belum_ada">Belum Ada Nilai</SelectItem>
                    </SelectContent>
                </Select>

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
                    Filter
                </Button>

                {(filters.search || filters.status_rab || filters.area) && (
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => {
                            setSearch('');
                            router.get('/rab');
                        }}
                    >
                        <RotateCcw className="h-3.5 w-3.5" />
                        Reset
                    </Button>
                )}
            </form>

            <Card>
                <CardContent className="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHead>No. Insiden</TableHead>
                                <TableHead>Pelanggan & Area</TableHead>
                                <TableHead>No. SAP / TUG</TableHead>
                                <TableHead className="text-right">Total BQ</TableHead>
                                <TableHead className="text-right">Nilai RAB</TableHead>
                                <TableHead>Status RAB</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {laporans.data.map((row) => (
                                <TableRow key={row.id}>
                                    <TableCell>
                                        <Link
                                            href={`/laporan/${row.id}`}
                                            className="font-mono text-xs font-medium text-[#1d5fbf] hover:underline"
                                        >
                                            {row.no_insiden}
                                        </Link>
                                        <div className="text-[11px] text-[#5b616e]">
                                            {formatDateId(row.tanggal_laporan)}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div className="font-medium">{row.pelanggan}</div>
                                        <div className="text-xs text-[#5b616e]">
                                            {row.area} · {row.jenis_pekerjaan}
                                        </div>
                                    </TableCell>
                                    <TableCell className="font-mono text-xs text-[#5b616e]">
                                        {row.no_sap ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-right text-[#5b616e]">
                                        {row.bq_items_count > 0 ? (
                                            <Link
                                                href={`/bill-of-quantity?laporan_id=${row.id}`}
                                                className="text-[#1d5fbf] hover:underline"
                                            >
                                                {formatRupiah(row.bq_total)} ({row.bq_items_count})
                                            </Link>
                                        ) : (
                                            '—'
                                        )}
                                    </TableCell>
                                    <TableCell className="text-right font-bold">
                                        {formatRupiah(row.nilai_rab)}
                                    </TableCell>
                                    <TableCell>
                                        <RabStatusBadge statusRab={row.status_rab} />
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            onClick={() => openEdit(row)}
                                        >
                                            <Pencil className="h-3.5 w-3.5" />
                                            Atur RAB
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                    <div className="border-t border-[#eef0f4] px-4 pb-4">
                        <PaginationBar paginated={laporans} />
                    </div>
                </CardContent>
            </Card>

            <Dialog open={Boolean(editing)} onOpenChange={(open) => !open && setEditing(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Perbarui RAB — Insiden {editing?.no_insiden}</DialogTitle>
                        <DialogDescription>
                            {isAdmin
                                ? 'Administrator dapat menyetujui nilai RAB dan memperbarui nomor SAP.'
                                : 'Petugas dapat mengajukan RAB untuk diverifikasi oleh Administrator.'}
                        </DialogDescription>
                    </DialogHeader>
                    <form onSubmit={submitEdit} className="space-y-4">
                        <div className="space-y-1.5">
                            <Label htmlFor="nilai_rab">Nilai RAB (Rp)</Label>
                            <Input
                                id="nilai_rab"
                                type="number"
                                min="0"
                                step="100"
                                value={form.data.nilai_rab}
                                onChange={(e) => form.setData('nilai_rab', e.target.value)}
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="no_sap">No. SAP / TUG</Label>
                            <Input
                                id="no_sap"
                                value={form.data.no_sap}
                                onChange={(e) => form.setData('no_sap', e.target.value)}
                                placeholder="SAP : 118873"
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label>Status RAB</Label>
                            <Select
                                value={form.data.status_rab}
                                onValueChange={(val) => form.setData('status_rab', val)}
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="draft">Draft</SelectItem>
                                    <SelectItem value="diajukan">Diajukan ke Admin</SelectItem>
                                    {isAdmin && <SelectItem value="disetujui">Disetujui</SelectItem>}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="rab_catatan">Catatan RAB</Label>
                            <Textarea
                                id="rab_catatan"
                                rows={2}
                                value={form.data.rab_catatan}
                                onChange={(e) => form.setData('rab_catatan', e.target.value)}
                                placeholder="Catatan revisi atau referensi penawaran..."
                            />
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setEditing(null)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Menyimpan...' : 'Simpan RAB'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
