import * as React from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { Pencil, Plus, RotateCcw, Search, Trash2 } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { PaginationBar } from '@/components/PaginationBar';
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
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Paginated } from '@/types';

interface AreaItem {
    id: number;
    nama_area: string;
    laporan_count: number;
}

interface Props {
    areas: Paginated<AreaItem>;
    filters: { search?: string };
    openCreate?: boolean;
    editingArea?: AreaItem;
}

export default function MasterAreas({ areas, filters, openCreate = false, editingArea }: Props) {
    const [search, setSearch] = React.useState(filters.search ?? '');
    const [dialogOpen, setDialogOpen] = React.useState(Boolean(openCreate || editingArea));
    const [editItem, setEditItem] = React.useState<AreaItem | null>(editingArea ?? null);

    const form = useForm({ nama_area: editingArea?.nama_area ?? '' });

    const openAdd = () => {
        setEditItem(null);
        form.setData('nama_area', '');
        form.clearErrors();
        setDialogOpen(true);
    };

    const openEdit = (item: AreaItem) => {
        setEditItem(item);
        form.setData('nama_area', item.nama_area);
        form.clearErrors();
        setDialogOpen(true);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editItem) {
            form.put(`/areas/${editItem.id}`, {
                preserveScroll: true,
                onSuccess: () => setDialogOpen(false),
            });
        } else {
            form.post('/areas', {
                preserveScroll: true,
                onSuccess: () => setDialogOpen(false),
            });
        }
    };

    const handleDelete = (item: AreaItem) => {
        if (window.confirm(`Hapus area "${item.nama_area}"?`)) {
            router.delete(`/areas/${item.id}`, { preserveScroll: true });
        }
    };

    return (
        <AppLayout
            title="Master Area Distribusi"
            subtitle="Kelola daftar wilayah kerja PLN yang tersedia pada work order."
            actions={
                <Button type="button" onClick={openAdd}>
                    <Plus className="h-4 w-4" />
                    Tambah Area
                </Button>
            }
        >
            <Head title="Master Area" />

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    router.get('/areas', search.trim() ? { search: search.trim() } : {}, { preserveState: true });
                }}
                className="mb-4 flex items-center gap-2 max-w-md"
            >
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#667085]" />
                    <Input
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Cari nama area..."
                        className="pl-9"
                    />
                </div>
                <Button type="submit" variant="secondary">Cari</Button>
                {filters.search && (
                    <Button type="button" variant="ghost" onClick={() => { setSearch(''); router.get('/areas'); }}>
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
                                <TableHead>Nama Area</TableHead>
                                <TableHead className="text-right">Jumlah Laporan</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {areas.data.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="font-mono text-xs text-[#5b616e]">#{item.id}</TableCell>
                                    <TableCell className="font-medium">{item.nama_area}</TableCell>
                                    <TableCell className="text-right">{item.laporan_count}</TableCell>
                                    <TableCell className="text-right">
                                        <div className="inline-flex items-center gap-1">
                                            <Button type="button" variant="ghost" size="icon" onClick={() => openEdit(item)} aria-label="Edit area">
                                                <Pencil className="h-4 w-4" />
                                            </Button>
                                            <Button type="button" variant="ghost" size="icon" onClick={() => handleDelete(item)} aria-label="Hapus area">
                                                <Trash2 className="h-4 w-4 text-[#c4242a]" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                    <div className="border-t border-[#eef0f4] px-4 pb-4">
                        <PaginationBar paginated={areas} />
                    </div>
                </CardContent>
            </Card>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{editItem ? 'Edit Area Distribusi' : 'Tambah Area Distribusi'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-1.5">
                            <Label htmlFor="nama_area">Nama Area *</Label>
                            <Input
                                id="nama_area"
                                value={form.data.nama_area}
                                onChange={(e) => form.setData('nama_area', e.target.value)}
                                placeholder="Contoh: Sekupang"
                                required
                            />
                            {form.errors.nama_area && <p className="text-xs text-[#c4242a]">{form.errors.nama_area}</p>}
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
