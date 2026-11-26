import * as React from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { StatusBadge } from '@/components/StatusBadge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface StatusItem {
    id: number;
    status_kerja: string;
    label: string;
    laporan_count: number;
}

interface Props {
    statuses: StatusItem[];
    openCreate?: boolean;
    editingStatus?: { id: number; status_kerja: string };
}

export default function MasterStatus({ statuses, openCreate = false, editingStatus }: Props) {
    const [dialogOpen, setDialogOpen] = React.useState(Boolean(openCreate || editingStatus));
    const [editItem, setEditItem] = React.useState<{ id: number; status_kerja: string } | null>(editingStatus ?? null);

    const form = useForm({ status_kerja: editingStatus?.status_kerja ?? 'pending' });

    const openAdd = () => {
        setEditItem(null);
        form.setData('status_kerja', 'pending');
        form.clearErrors();
        setDialogOpen(true);
    };

    const openEdit = (item: StatusItem) => {
        setEditItem({ id: item.id, status_kerja: item.status_kerja });
        form.setData('status_kerja', item.status_kerja);
        form.clearErrors();
        setDialogOpen(true);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editItem) {
            form.put(`/status/${editItem.id}`, { preserveScroll: true, onSuccess: () => setDialogOpen(false) });
        } else {
            form.post('/status', { preserveScroll: true, onSuccess: () => setDialogOpen(false) });
        }
    };

    return (
        <AppLayout
            title="Master Status Kerja"
            subtitle="Konfigurasi status siklus hidup work order pada sistem."
            actions={
                <Button type="button" onClick={openAdd}>
                    <Plus className="h-4 w-4" />
                    Tambah Status
                </Button>
            }
        >
            <Head title="Master Status" />

            <Card>
                <CardContent className="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHead>ID</TableHead>
                                <TableHead>Kode Status</TableHead>
                                <TableHead>Tampilan Badge</TableHead>
                                <TableHead className="text-right">Jumlah Laporan</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {statuses.map((item) => (
                                <TableRow key={item.id}>
                                    <TableCell className="font-mono text-xs text-[#5b616e]">#{item.id}</TableCell>
                                    <TableCell className="font-mono text-xs">{item.status_kerja}</TableCell>
                                    <TableCell>
                                        <StatusBadge statusKerja={item.status_kerja} label={item.label} />
                                    </TableCell>
                                    <TableCell className="text-right">{item.laporan_count}</TableCell>
                                    <TableCell className="text-right">
                                        <div className="inline-flex items-center gap-1">
                                            <Button type="button" variant="ghost" size="icon" onClick={() => openEdit(item)} aria-label="Edit status">
                                                <Pencil className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                onClick={() => {
                                                    if (window.confirm(`Hapus status "${item.label}"?`)) {
                                                        router.delete(`/status/${item.id}`, { preserveScroll: true });
                                                    }
                                                }}
                                                aria-label="Hapus status"
                                            >
                                                <Trash2 className="h-4 w-4 text-[#c4242a]" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{editItem ? 'Edit Status Kerja' : 'Tambah Status Kerja'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="space-y-1.5">
                            <Label>Status Kerja *</Label>
                            <Select value={form.data.status_kerja} onValueChange={(v) => form.setData('status_kerja', v)}>
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="pending">pending (Terbuka)</SelectItem>
                                    <SelectItem value="on_progress">on_progress (Sedang Dikerjakan)</SelectItem>
                                    <SelectItem value="on_hold">on_hold (Ditunda)</SelectItem>
                                    <SelectItem value="completed">completed (Selesai)</SelectItem>
                                </SelectContent>
                            </Select>
                            {form.errors.status_kerja && <p className="text-xs text-[#c4242a]">{form.errors.status_kerja}</p>}
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
