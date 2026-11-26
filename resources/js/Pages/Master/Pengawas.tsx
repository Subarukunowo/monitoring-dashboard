import * as React from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { Pencil, Plus, RotateCcw, Search, Trash2 } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { PaginationBar } from '@/components/PaginationBar';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Paginated } from '@/types';

interface PengawasItem {
    id: number;
    nama: string;
    no_telepon: string | null;
    laporan_count: number;
}

interface Props {
    pengawass: Paginated<PengawasItem>;
    filters: { search?: string };
    openCreate?: boolean;
    editingPengawas?: PengawasItem;
}

export default function MasterPengawas({ pengawass, filters, openCreate = false, editingPengawas }: Props) {
    const [search, setSearch] = React.useState(filters.search ?? '');
    const [dialogOpen, setDialogOpen] = React.useState(Boolean(openCreate || editingPengawas));
    const [editItem, setEditItem] = React.useState<PengawasItem | null>(editingPengawas ?? null);

    const form = useForm({
        nama: editingPengawas?.nama ?? '',
        no_telepon: editingPengawas?.no_telepon ?? '',
    });

    const openAdd = () => {
        setEditItem(null);
        form.setData({ nama: '', no_telepon: '' });
        form.clearErrors();
        setDialogOpen(true);
    };

    const openEdit = (item: PengawasItem) => {
        setEditItem(item);
        form.setData({ nama: item.nama, no_telepon: item.no_telepon ?? '' });
        form.clearErrors();
        setDialogOpen(true);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editItem) {
            form.put(`/pengawas/${editItem.id}`, { preserveScroll: true, onSuccess: () => setDialogOpen(false) });
        } else {
            form.post('/pengawas', { preserveScroll: true, onSuccess: () => setDialogOpen(false) });
        }
    };

    return (
        <AppLayout
            title="Master Pengawas Lapangan"
            subtitle="Kelola daftar penanggung jawab pengawasan pekerjaan."
            actions={
                <Button type="button" onClick={openAdd}>
                    <Plus className="h-4 w-4" />
                    Tambah Pengawas
                </Button>
            }
        >
            <Head title="Master Pengawas" />

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    router.get('/pengawas', search.trim() ? { search: search.trim() } : {}, { preserveState: true });
                }}
                className="mb-4 flex items-center gap-2 max-w-md"
            >
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#667085]" />
                    <Input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari nama pengawas..." className="pl-9" />
                </div>
                <Button type="submit" variant="secondary">Cari</Button>
                {filters.search && (
                    <Button type="button" variant="ghost" onClick={() => { setSearch(''); router.get('/pengawas'); }}>
                        <RotateCcw className="h-3.5 w-3.5" />
                    </Button>
                )}
            </form>

            <Card>
                <CardContent className="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHead>ID</TableHead>
                                <TableHead>Nama Pengawas</TableHead>
                                <TableHead>No. Telepon</TableHead>
                                <TableHead className="text-right">Laporan Ditugaskan</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {pengawass.data.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="font-mono text-xs text-[#5b616e]">#{item.id}</TableCell>
                                    <TableCell className="font-medium">{item.nama}</TableCell>
                                    <TableCell className="font-mono text-xs text-[#5b616e]">{item.no_telepon ?? '—'}</TableCell>
                                    <TableCell className="text-right">{item.laporan_count}</TableCell>
                                    <TableCell className="text-right">
                                        <div className="inline-flex items-center gap-1">
                                            <Button type="button" variant="ghost" size="icon" onClick={() => openEdit(item)} aria-label="Edit pengawas">
                                                <Pencil className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                onClick={() => {
                                                    if (window.confirm(`Hapus pengawas "${item.nama}"?`)) {
                                                        router.delete(`/pengawas/${item.id}`, { preserveScroll: true });
                                                    }
                                                }}
                                                aria-label="Hapus pengawas"
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
                        <PaginationBar paginated={pengawass} />
                    </div>
                </CardContent>
            </Card>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{editItem ? 'Edit Pengawas' : 'Tambah Pengawas'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-1.5">
                            <Label htmlFor="nama">Nama Pengawas *</Label>
                            <Input id="nama" value={form.data.nama} onChange={(e) => form.setData('nama', e.target.value)} required />
                            {form.errors.nama && <p className="text-xs text-[#c4242a]">{form.errors.nama}</p>}
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="no_telepon">No. Telepon</Label>
                            <Input id="no_telepon" value={form.data.no_telepon} onChange={(e) => form.setData('no_telepon', e.target.value)} />
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setDialogOpen(false)}>Batal</Button>
                            <Button type="submit" disabled={form.processing}>Simpan</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
