import * as React from 'react';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { Check, Lock, RotateCcw, Search, Unlock, X } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { PaginationBar } from '@/components/PaginationBar';
import { StatusBadge } from '@/components/StatusBadge';
import { Badge } from '@/components/ui/badge';
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
import { formatRupiah } from '@/lib/utils';
import type { PageProps, Paginated } from '@/types';

interface ContractRow {
    id: number;
    no_insiden: string;
    area: string;
    pelanggan: string;
    status_label: string;
    status_kerja: string;
    no_sap: string | null;
    nilai_rab: number | null;
    is_closed: boolean;
    closed_at: string | null;
    closed_by_name: string | null;
    catatan_penutupan: string | null;
    checks: {
        status_selesai: boolean;
        no_sap_terisi: boolean;
        nilai_rab_terisi: boolean;
        foto_pekerjaan: boolean;
        dokumen_admin: boolean;
    };
    passed_checks: number;
    total_checks: number;
    can_close: boolean;
}

interface Props {
    laporans: Paginated<ContractRow>;
    summary: {
        total: number;
        closed: number;
        ready: number;
        in_progress: number;
    };
    filters: {
        state?: string;
        search?: string;
    };
}

export default function TutupKontrakIndex({ laporans, summary, filters }: Props) {
    const { props } = usePage<PageProps>();
    const isAdmin = Boolean(props.auth?.user?.is_admin);
    const [search, setSearch] = React.useState(filters.search ?? '');
    const [closingRow, setClosingRow] = React.useState<ContractRow | null>(null);

    const closeForm = useForm({ catatan_penutupan: '' });

    const applyFilter = (next: Record<string, string | undefined>) => {
        const merged = { ...filters, ...next };
        const clean: Record<string, string> = {};
        Object.entries(merged).forEach(([k, v]) => {
            if (v && v !== 'all') clean[k] = v;
        });
        router.get('/tutup-kontrak', clean, { preserveState: true, preserveScroll: true });
    };

    const submitClose = (e: React.FormEvent) => {
        e.preventDefault();
        if (!closingRow) return;
        closeForm.post(`/tutup-kontrak/${closingRow.id}/close`, {
            preserveScroll: true,
            onSuccess: () => {
                closeForm.reset();
                setClosingRow(null);
            },
        });
    };

    const checkFlag = (ok: boolean, label: string) => (
        <span
            className={`inline-flex items-center gap-1 rounded-[4px] px-1.5 py-0.5 text-[11px] ${
                ok ? 'bg-[#dcfae5] text-[#04250e]' : 'bg-[#f4f5f7] text-[#5b616e]'
            }`}
        >
            {ok ? <Check className="h-3 w-3" /> : <X className="h-3 w-3" />}
            {label}
        </span>
    );

    return (
        <AppLayout
            title="Tutup Kontrak"
            subtitle={
                isAdmin
                    ? 'Verifikasi kelengkapan administrasi dan tutup kontrak pekerjaan yang telah selesai.'
                    : 'Pantau kelengkapan syarat penutupan kontrak work order.'
            }
        >
            <Head title="Tutup Kontrak" />

            <div className="mb-4 grid grid-cols-2 rounded-[8px] border border-[#e3e6ec] bg-white lg:grid-cols-4 tabular-nums">
                <div className="p-4">
                    <div className="text-xs text-[#5b616e]">Total Work Order</div>
                    <div className="mt-1.5 text-2xl font-medium text-[#0a0a0a]">{summary.total}</div>
                </div>
                <div className="border-l border-[#e3e6ec] p-4">
                    <div className="text-xs text-[#5b616e]">Siap Ditutup</div>
                    <div className="mt-1.5 text-2xl font-medium text-[#1d5fbf]">{summary.ready}</div>
                </div>
                <div className="border-t border-[#e3e6ec] p-4 lg:border-t-0 lg:border-l">
                    <div className="text-xs text-[#5b616e]">Sudah Ditutup</div>
                    <div className="mt-1.5 text-2xl font-medium text-[#15803d]">{summary.closed}</div>
                </div>
                <div className="border-l border-t border-[#e3e6ec] p-4 lg:border-t-0">
                    <div className="text-xs text-[#5b616e]">Masih Berjalan</div>
                    <div className="mt-1.5 text-2xl font-medium text-[#5b616e]">{summary.in_progress}</div>
                </div>
            </div>

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
                        placeholder="Cari No. Insiden, SAP, atau pelanggan..."
                        className="pl-9"
                    />
                </div>

                <Select
                    value={filters.state ?? 'all'}
                    onValueChange={(val) => applyFilter({ state: val === 'all' ? undefined : val })}
                >
                    <SelectTrigger className="w-[190px]">
                        <SelectValue placeholder="Semua Status Kontrak" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Kontrak</SelectItem>
                        <SelectItem value="ready">Siap Ditutup</SelectItem>
                        <SelectItem value="closed">Sudah Ditutup</SelectItem>
                        <SelectItem value="incomplete">Belum Selesai</SelectItem>
                    </SelectContent>
                </Select>

                <Button type="submit" variant="secondary">
                    Filter
                </Button>

                {(filters.search || filters.state) && (
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => {
                            setSearch('');
                            router.get('/tutup-kontrak');
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
                                <TableHead>Status Kerja</TableHead>
                                <TableHead>Kelengkapan Syarat</TableHead>
                                <TableHead className="text-right">Nilai RAB</TableHead>
                                <TableHead>Status Kontrak</TableHead>
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
                                        <div className="text-xs text-[#5b616e]">
                                            {row.pelanggan} · {row.area}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <StatusBadge statusKerja={row.status_kerja} label={row.status_label} />
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex flex-wrap gap-1">
                                            {checkFlag(row.checks.status_selesai, 'Selesai')}
                                            {checkFlag(row.checks.no_sap_terisi, 'SAP')}
                                            {checkFlag(row.checks.nilai_rab_terisi, 'RAB')}
                                            {checkFlag(row.checks.foto_pekerjaan, 'Foto Kerja')}
                                            {checkFlag(row.checks.dokumen_admin, 'Dok. Admin')}
                                        </div>
                                    </TableCell>
                                    <TableCell className="text-right font-medium">
                                        {formatRupiah(row.nilai_rab)}
                                    </TableCell>
                                    <TableCell>
                                        {row.is_closed ? (
                                            <div>
                                                <Badge variant="positive">Ditutup</Badge>
                                                <div className="mt-0.5 text-[11px] text-[#5b616e]">
                                                    {row.closed_at}
                                                </div>
                                            </div>
                                        ) : (
                                            <Badge variant={row.can_close ? 'info' : 'secondary'}>
                                                {row.can_close ? 'Siap Ditutup' : 'Terbuka'}
                                            </Badge>
                                        )}
                                    </TableCell>
                                    <TableCell className="text-right">
                                        {isAdmin ? (
                                            row.is_closed ? (
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        router.post(`/tutup-kontrak/${row.id}/reopen`, {}, { preserveScroll: true })
                                                    }
                                                >
                                                    <Unlock className="h-3.5 w-3.5" />
                                                    Buka Lagi
                                                </Button>
                                            ) : (
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    className={row.can_close ? 'border-[#141414] text-[#0a0a0a]' : ''}
                                                    onClick={() => setClosingRow(row)}
                                                >
                                                    <Lock className="h-3.5 w-3.5" />
                                                    Tutup Kontrak
                                                </Button>
                                            )
                                        ) : (
                                            <Link
                                                href={`/laporan/${row.id}`}
                                                className="text-xs font-medium text-[#1d5fbf] hover:underline"
                                            >
                                                Lengkapi Data
                                            </Link>
                                        )}
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

            <Dialog open={Boolean(closingRow)} onOpenChange={(open) => !open && setClosingRow(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Tutup Kontrak Insiden {closingRow?.no_insiden}</DialogTitle>
                        <DialogDescription>
                            Kelengkapan terpenuhi: {closingRow?.passed_checks} dari {closingRow?.total_checks} kriteria.
                        </DialogDescription>
                    </DialogHeader>
                    <form onSubmit={submitClose} className="space-y-4">
                        <div className="space-y-1.5">
                            <Label htmlFor="catatan_penutupan">Berita Acara / Catatan Penutupan</Label>
                            <Textarea
                                id="catatan_penutupan"
                                rows={3}
                                value={closeForm.data.catatan_penutupan}
                                onChange={(e) => closeForm.setData('catatan_penutupan', e.target.value)}
                                placeholder="Catatan serah terima atau verifikasi akhir..."
                            />
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setClosingRow(null)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={closeForm.processing}>
                                {closeForm.processing ? 'Memproses...' : 'Konfirmasi Tutup Kontrak'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
