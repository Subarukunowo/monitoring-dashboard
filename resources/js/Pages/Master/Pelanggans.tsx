import * as React from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { Pencil, Plus, RotateCcw, Search, Trash2 } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { PaginationBar } from '@/components/PaginationBar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import type { Paginated } from '@/types';

interface PelangganItem {
    id: number;
    no_pelanggan: string | null;
    nama: string;
    no_telepon: string | null;
    alamat: string | null;
    status_pelanggan: number;
    laporan_count: number;
}

interface Props {
    pelanggans: Paginated<PelangganItem>;
    filters: { search?: string; tipe?: string };
    openCreate?: boolean;
    editingPelanggan?: PelangganItem;
}

export default function MasterPelanggans({ pelanggans, filters, openCreate = false, editingPelanggan }: Props) {
    const [search, setSearch] = React.useState(filters.search ?? '');
    const [dialogOpen, setDialogOpen] = React.useState(Boolean(openCreate || editingPelanggan));
    const [editItem, setEditItem] = React.useState<PelangganItem | null>(editingPelanggan ?? null);

    const form = useForm({
        no_pelanggan: editingPelanggan?.no_pelanggan ? String(editingPelanggan.no_pelanggan) : '',
        nama: editingPelanggan?.nama ?? '',
        no_telepon: editingPelanggan?.no_telepon ?? '',
        alamat: editingPelanggan?.alamat ?? '',
        status_pelanggan: editingPelanggan ? Boolean(editingPelanggan.status_pelanggan) : true,
    });

    const openAdd = () => {
        setEditItem(null);
        form.setData({ no_pelanggan: '', nama: '', no_telepon: '', alamat: '', status_pelanggan: true });
        form.clearErrors();
        setDialogOpen(true);
    };

    const openEdit = (item: PelangganItem) => {
        setEditItem(item);
        form.setData({
            no_pelanggan: item.no_pelanggan ? String(item.no_pelanggan) : '',
            nama: item.nama,
            no_telepon: item.no_telepon ?? '',
            alamat: item.alamat ?? '',
            status_pelanggan: Boolean(item.status_pelanggan),
        });
        form.clearErrors();
        setDialogOpen(true);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editItem) {
            form.put(`/pelanggans/${editItem.id}`, { preserveScroll: true, onSuccess: () => setDialogOpen(false) });
        } else {
            form.post('/pelanggans', { preserveScroll: true, onSuccess: () => setDialogOpen(false) });
        }
    };

    const applyFilter = (next: Record<string, string | undefined>) => {
        const merged = { ...filters, ...next };
        const clean: Record<string, string> = {};
        Object.entries(merged).forEach(([k, v]) => {
            if (v && v !== 'all') clean[k] = v;
        });
        router.get('/pelanggans', clean, { preserveState: true });
    };

    return (
        <AppLayout
            title="Master Pelanggan"
            subtitle="Daftar pelanggan resmi PLN dan pelapor non-pelanggan."
            actions={
                <Button type="button" onClick={openAdd}>
                    <Plus className="h-4 w-4" />
                    Tambah Pelanggan
                </Button>
            }
        >
            <Head title="Master Pelanggan" />

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
                        placeholder="Cari nama, ID PLN, atau alamat..."
                        className="pl-9"
                    />
                </div>
                <Select value={filters.tipe ?? 'all'} onValueChange={(v) => applyFilter({ tipe: v })}>
                    <SelectTrigger className="w-[180px]">
                        <SelectValue placeholder="Semua Kategori" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Kategori</SelectItem>
                        <SelectItem value="resmi">Pelanggan Resmi</SelectItem>
                        <SelectItem value="umum">Non-Pelanggan</SelectItem>
                    </SelectContent>
                </Select>
                <Button type="submit" variant="secondary">Cari</Button>
                {(filters.search || filters.tipe) && (
                    <Button type="button" variant="ghost" onClick={() => { setSearch(''); router.get('/pelanggans'); }}>
                        <RotateCcw className="h-3.5 w-3.5" />
                    </Button>
                )}
            </form>

            <Card>
                <CardContent className="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHead>No. Pelanggan</TableHead>
                                <TableHead>Nama & Alamat</TableHead>
                                <TableHead>Telepon</TableHead>
                                <TableHead>Kategori</TableHead>
                                <TableHead className="text-right">Laporan</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {pelanggans.data.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="font-mono text-xs text-[#5b616e]">
                                        {item.no_pelanggan ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <div className="font-medium">{item.nama || 'Non-Pelanggan / Umum'}</div>
                                        <div className="max-w-[280px] truncate text-xs text-[#5b616e]">
                                            {item.alamat ?? '—'}
                                        </div>
                                    </TableCell>
                                    <TableCell className="font-mono text-xs text-[#5b616e]">
                                        {item.no_telepon ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <Badge variant={item.status_pelanggan ? 'positive' : 'secondary'}>
                                            {item.status_pelanggan ? 'Pelanggan PLN' : 'Non-Pelanggan'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell className="text-right">{item.laporan_count}</TableCell>
                                    <TableCell className="text-right">
                                        <div className="inline-flex items-center gap-1">
                                            <Button type="button" variant="ghost" size="icon" onClick={() => openEdit(item)} aria-label="Edit pelanggan">
                                                <Pencil className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                onClick={() => {
                                                    if (window.confirm(`Hapus pelanggan "${item.nama}"?`)) {
                                                        router.delete(`/pelanggans/${item.id}`, { preserveScroll: true });
                                                    }
                                                }}
                                                aria-label="Hapus pelanggan"
                                            >
                                                <Trash2 className="h-4 w-4 text-[#c4242a]" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                    <div className="border-t border-[#eef0f4] px-4 pb-4">
                        <PaginationBar paginated={pelanggans} />
                    </div>
                </CardContent>
            </Card>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{editItem ? 'Edit Pelanggan' : 'Tambah Pelanggan'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid grid-cols-2 gap-3">
                            <div className="space-y-1.5">
                                <Label htmlFor="no_pelanggan">No. Pelanggan (ID PLN)</Label>
                                <Input
                                    id="no_pelanggan"
                                    type="number"
                                    value={form.data.no_pelanggan}
                                    onChange={(e) => form.setData('no_pelanggan', e.target.value)}
                                />
                                {form.errors.no_pelanggan && <p className="text-xs text-[#c4242a]">{form.errors.no_pelanggan}</p>}
                            </div>
                            <div className="space-y-1.5">
                                <Label>Kategori</Label>
                                <Select
                                    value={form.data.status_pelanggan ? '1' : '0'}
                                    onValueChange={(v) => form.setData('status_pelanggan', v === '1')}
                                >
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">Pelanggan Resmi</SelectItem>
                                        <SelectItem value="0">Non-Pelanggan</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-3">
                            <div className="space-y-1.5">
                                <Label htmlFor="nama">Nama Lengkap *</Label>
                                <Input
                                    id="nama"
                                    value={form.data.nama}
                                    onChange={(e) => form.setData('nama', e.target.value)}
                                    required
                                />
                                {form.errors.nama && <p className="text-xs text-[#c4242a]">{form.errors.nama}</p>}
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="no_telepon">No. Telepon</Label>
                                <Input
                                    id="no_telepon"
                                    value={form.data.no_telepon}
                                    onChange={(e) => form.setData('no_telepon', e.target.value)}
                                />
                            </div>
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="alamat">Alamat</Label>
                            <Textarea
                                id="alamat"
                                rows={2}
                                value={form.data.alamat}
                                onChange={(e) => form.setData('alamat', e.target.value)}
                            />
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setDialogOpen(false)}>Batal</Button>
                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Menyimpan...' : 'Simpan'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
