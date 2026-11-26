import * as React from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { Plus, RefreshCw, Trash2 } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { RabStatusBadge } from '@/components/StatusBadge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { formatRupiah } from '@/lib/utils';

interface BqItem {
    id: number;
    kode_material: string | null;
    uraian: string;
    kategori: 'Material' | 'Jasa' | 'Sipil';
    satuan: string;
    volume: number;
    harga_satuan: number;
    subtotal: number;
}

interface Props {
    laporans: Array<{
        id: number;
        no_insiden: string;
        area: string;
        jenis_pekerjaan: string;
        nilai_rab: number | null;
        bq_items_count: number;
        bq_total: number;
    }>;
    selectedLaporan: {
        id: number;
        no_insiden: string;
        area: string;
        pelanggan: string;
        jenis_pekerjaan: string;
        nilai_rab: number | null;
        status_rab: string;
        items: BqItem[];
    } | null;
}

export default function BqIndex({ laporans, selectedLaporan }: Props) {
    const [dialogOpen, setDialogOpen] = React.useState(false);

    const form = useForm({
        kode_material: '',
        uraian: '',
        kategori: 'Material' as 'Material' | 'Jasa' | 'Sipil',
        satuan: 'Unit',
        volume: '1',
        harga_satuan: '0',
    });

    const totalBq = selectedLaporan?.items.reduce((sum, item) => sum + Number(item.subtotal), 0) ?? 0;
    const totalMaterial =
        selectedLaporan?.items
            .filter((i) => i.kategori === 'Material')
            .reduce((s, i) => s + Number(i.subtotal), 0) ?? 0;
    const totalJasa =
        selectedLaporan?.items
            .filter((i) => i.kategori !== 'Material')
            .reduce((s, i) => s + Number(i.subtotal), 0) ?? 0;

    const handleAdd = (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedLaporan) return;
        form.post(`/bill-of-quantity/${selectedLaporan.id}/items`, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                setDialogOpen(false);
            },
        });
    };

    const handleDelete = (itemId: number) => {
        if (window.confirm('Hapus item BQ ini?')) {
            router.delete(`/bill-of-quantity/items/${itemId}`, { preserveScroll: true });
        }
    };

    const handleSyncRab = () => {
        if (!selectedLaporan) return;
        router.post(`/bill-of-quantity/${selectedLaporan.id}/sync-rab`, {}, { preserveScroll: true });
    };

    return (
        <AppLayout
            title="Bill of Quantity (BQ)"
            subtitle="Rincian volume material dan jasa per work order untuk perhitungan RAB."
            actions={
                selectedLaporan && (
                    <>
                        <Button type="button" variant="outline" onClick={handleSyncRab}>
                            <RefreshCw className="h-4 w-4" />
                            Sinkronkan ke Nilai RAB
                        </Button>
                        <Button type="button" onClick={() => setDialogOpen(true)}>
                            <Plus className="h-4 w-4" />
                            Tambah Item BQ
                        </Button>
                    </>
                )
            }
        >
            <Head title="Bill of Quantity" />

            {/* Selector Bar */}
            <div className="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-[8px] border border-[#e3e6ec] bg-white p-4">
                <div className="flex flex-wrap items-center gap-3">
                    <Label className="text-xs text-[#5b616e]">Pilih Insiden:</Label>
                    <Select
                        value={selectedLaporan ? String(selectedLaporan.id) : ''}
                        onValueChange={(val) => router.get('/bill-of-quantity', { laporan_id: val })}
                    >
                        <SelectTrigger className="w-[320px]">
                            <SelectValue placeholder="Pilih No. Insiden" />
                        </SelectTrigger>
                        <SelectContent>
                            {laporans.map((l) => (
                                <SelectItem key={l.id} value={String(l.id)}>
                                    {l.no_insiden} — {l.area} ({l.bq_items_count} item)
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>
                {selectedLaporan && (
                    <div className="flex items-center gap-2 text-xs text-[#5b616e]">
                        <span>{selectedLaporan.pelanggan}</span>
                        <span>·</span>
                        <RabStatusBadge statusRab={selectedLaporan.status_rab} />
                        <Link
                            href={`/laporan/${selectedLaporan.id}`}
                            className="ml-2 font-medium text-[#1d5fbf] hover:underline"
                        >
                            Detail Insiden
                        </Link>
                    </div>
                )}
            </div>

            {selectedLaporan ? (
                <>
                    <div className="mb-4 grid grid-cols-1 rounded-[8px] border border-[#e3e6ec] bg-white sm:grid-cols-3 tabular-nums">
                        <div className="p-4">
                            <div className="text-xs text-[#5b616e]">Total Material</div>
                            <div className="mt-1 text-xl font-semibold text-[#0a0a0a]">
                                {formatRupiah(totalMaterial)}
                            </div>
                        </div>
                        <div className="border-t border-[#e3e6ec] p-4 sm:border-t-0 sm:border-l">
                            <div className="text-xs text-[#5b616e]">Total Jasa & Sipil</div>
                            <div className="mt-1 text-xl font-semibold text-[#0a0a0a]">
                                {formatRupiah(totalJasa)}
                            </div>
                        </div>
                        <div className="border-t border-[#e3e6ec] p-4 sm:border-t-0 sm:border-l">
                            <div className="text-xs text-[#5b616e]">Grand Total BQ</div>
                            <div className="mt-1 text-xl font-bold text-[#1d5fbf]">
                                {formatRupiah(totalBq)}
                            </div>
                            <div className="mt-0.5 text-[11px] text-[#5b616e]">
                                Nilai RAB tersimpan: {formatRupiah(selectedLaporan.nilai_rab)}
                            </div>
                        </div>
                    </div>

                    <Card>
                        <CardHeader>
                            <CardTitle>Daftar Item Pekerjaan & Material</CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            {selectedLaporan.items.length > 0 ? (
                                <Table>
                                    <TableHeader>
                                        <TableRow className="hover:bg-transparent">
                                            <TableHead>Kode</TableHead>
                                            <TableHead>Uraian</TableHead>
                                            <TableHead>Kategori</TableHead>
                                            <TableHead className="text-right">Volume</TableHead>
                                            <TableHead className="text-right">Harga Satuan</TableHead>
                                            <TableHead className="text-right">Subtotal</TableHead>
                                            <TableHead className="text-right">Aksi</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {selectedLaporan.items.map((item) => (
                                            <TableRow key={item.id}>
                                                <TableCell className="font-mono text-xs text-[#5b616e]">
                                                    {item.kode_material || '—'}
                                                </TableCell>
                                                <TableCell className="font-medium">{item.uraian}</TableCell>
                                                <TableCell>
                                                    <Badge variant="outline">{item.kategori}</Badge>
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {item.volume} {item.satuan}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {formatRupiah(item.harga_satuan)}
                                                </TableCell>
                                                <TableCell className="text-right font-semibold">
                                                    {formatRupiah(item.subtotal)}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="icon"
                                                        onClick={() => handleDelete(item.id)}
                                                        aria-label="Hapus item"
                                                    >
                                                        <Trash2 className="h-4 w-4 text-[#c4242a]" />
                                                    </Button>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            ) : (
                                <div className="p-10 text-center text-sm text-[#5b616e]">
                                    Belum ada item Bill of Quantity untuk insiden {selectedLaporan.no_insiden}.
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </>
            ) : (
                <Card>
                    <CardContent className="p-10 text-center text-sm text-[#5b616e]">
                        Belum ada laporan insiden yang tersedia.
                    </CardContent>
                </Card>
            )}

            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Tambah Item Bill of Quantity</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleAdd} className="space-y-4">
                        <div className="grid grid-cols-2 gap-3">
                            <div className="space-y-1.5">
                                <Label htmlFor="kode_material">Kode Material</Label>
                                <Input
                                    id="kode_material"
                                    value={form.data.kode_material}
                                    onChange={(e) => form.setData('kode_material', e.target.value)}
                                    placeholder="MAT-JTR-02"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <Label>Kategori</Label>
                                <Select
                                    value={form.data.kategori}
                                    onValueChange={(val) =>
                                        form.setData('kategori', val as 'Material' | 'Jasa' | 'Sipil')
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Material">Material</SelectItem>
                                        <SelectItem value="Jasa">Jasa</SelectItem>
                                        <SelectItem value="Sipil">Sipil</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="uraian">Uraian Pekerjaan / Material *</Label>
                            <Input
                                id="uraian"
                                value={form.data.uraian}
                                onChange={(e) => form.setData('uraian', e.target.value)}
                                placeholder="Contoh: Tiang Beton 9 Meter 200 daN"
                                required
                            />
                        </div>

                        <div className="grid grid-cols-3 gap-3">
                            <div className="space-y-1.5">
                                <Label htmlFor="volume">Volume *</Label>
                                <Input
                                    id="volume"
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    value={form.data.volume}
                                    onChange={(e) => form.setData('volume', e.target.value)}
                                    required
                                />
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="satuan">Satuan *</Label>
                                <Input
                                    id="satuan"
                                    value={form.data.satuan}
                                    onChange={(e) => form.setData('satuan', e.target.value)}
                                    placeholder="Batang"
                                    required
                                />
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="harga_satuan">Harga Satuan (Rp) *</Label>
                                <Input
                                    id="harga_satuan"
                                    type="number"
                                    min="0"
                                    value={form.data.harga_satuan}
                                    onChange={(e) => form.setData('harga_satuan', e.target.value)}
                                    required
                                />
                            </div>
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setDialogOpen(false)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={form.processing}>
                                {form.processing ? 'Menyimpan...' : 'Tambahkan Item'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
