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

interface JenisItem {
    id: number;
    nama_jenis: string;
    laporan_count: number;
}

interface Props {
    jenisPekerjaans: Paginated<JenisItem>;
    filters: { search?: string };
    openCreate?: boolean;
    editingJenis?: JenisItem;
}

export default function MasterJenisPekerjaan({ jenisPekerjaans, filters, openCreate = false, editingJenis }: Props) {
    const [search, setSearch] = React.useState(filters.search ?? '');
    const [dialogOpen, setDialogOpen] = React.useState(Boolean(openCreate || editingJenis));
    const [editItem, setEditItem] = React.useState<JenisItem | null>(editingJenis ?? null);

    const form = useForm({ nama_jenis: editingJenis?.nama_jenis ?? '' });

    const openAdd = () => {
        setEditItem(null);
        form.setData('nama_jenis', '');
        form.clearErrors();
        setDialogOpen(true);
    };

    const openEdit = (item: JenisItem) => {
        setEditItem(item);
        form.setData('nama_jenis', item.nama_jenis);
        form.clearErrors();
        setDialogOpen(true);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editItem) {
            form.put(`/jenis_pekerjaan/${editItem.id}`, { preserveScroll: true, onSuccess: () => setDialogOpen(false) });
        } else {
            form.post('/jenis_pekerjaan', { preserveScroll: true, onSuccess: () => setDialogOpen(false) });
        }
    };

    return (
        <AppLayout
            title="Master Jenis Pekerjaan"
            subtitle="Katalog klasifikasi pekerjaan perbaikan dan pemeliharaan jaringan."
            actions={
                <Button type="button" onClick={openAdd}>
                    <Plus className="h-4 w-4" />
                    Tambah Jenis
                </Button>
            }
        >
            <Head title="Master Jenis Pekerjaan" />

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    router.get('/jenis_pekerjaan', search.trim() ? { search: search.trim() } : {}, { preserveState: true });
                }}
                className="mb-4 flex items-center gap-2 max-w-md"
            >
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#667085]" />
                    <Input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari jenis pekerjaan..." className="pl-9" />
                </div>
                <Button type="submit" variant="secondary">Cari</Button>
                {filters.search && (
                    <Button type="button" variant="ghost" onClick={() => { setSearch(''); router.get('/jenis_pekerjaan'); }}>
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
                                <TableHead>Jenis Pekerjaan</TableHead>
                                <TableHead className="text-right">Digunakan pada Laporan</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {jenisPekerjaans.data.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="font-mono text-xs text-[#5b616e]">#{item.id}</TableCell>
                                    <TableCell className="font-medium">{item.nama_jenis}</TableCell>
                                    <TableCell className="text-right">{item.laporan_count}</TableCell>
                                    <TableCell className="text-right">
                                        <div className="inline-flex items-center gap-1">
                                            <Button type="button" variant="ghost" size="icon" onClick={() => openEdit(item)} aria-label="Edit jenis pekerjaan">
                                                <Pencil className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                onClick={() => {
                                                    if (window.confirm(`Hapus jenis pekerjaan "${item.nama_jenis}"?`)) {
                                                        router.delete(`/jenis_pekerjaan/${item.id}`, { preserveScroll: true });
                                                    }
                                                }}
                                                aria-label="Hapus jenis pekerjaan"
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
                        <PaginationBar paginated={jenisPekerjaans} />
                    </div>
                </CardContent>
            </Card>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{editItem ? 'Edit Jenis Pekerjaan' : 'Tambah Jenis Pekerjaan'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-1.5">
                            <Label htmlFor="nama_jenis">Nama Jenis Pekerjaan *</Label>
                            <Input id="nama_jenis" value={form.data.nama_jenis} onChange={(e) => form.setData('nama_jenis', e.target.value)} required />
                            {form.errors.nama_jenis && <p className="text-xs text-[#c4242a]">{form.errors.nama_jenis}</p>}
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
