import * as React from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { Download, FileText, Plus, RotateCcw, Search, Trash2 } from 'lucide-react';
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
import type { Paginated } from '@/types';

interface DocItem {
    id: number;
    tipe: string;
    nama_file: string;
    url: string;
    mime_type: string | null;
    is_image: boolean;
    ukuran_file: number | null;
    uploaded_at: string | null;
    laporan_id: number;
    no_insiden: string;
    area: string;
    pelanggan: string;
}

interface Props {
    mode: 'pekerjaan' | 'administrasi';
    title: string;
    items: Paginated<DocItem>;
    laporans: Array<{ id: number; no_insiden: string }>;
    areas: Array<{ id: number; nama_area: string }>;
    filters: {
        laporan_id?: string;
        area?: string;
        search?: string;
    };
}

export default function DokumentasiIndex({ mode, title, items, laporans, areas, filters }: Props) {
    const baseRoute = mode === 'pekerjaan' ? '/dokumentasi-pekerjaan' : '/dokumentasi-administrasi';
    const [search, setSearch] = React.useState(filters.search ?? '');
    const [uploadOpen, setUploadOpen] = React.useState(false);
    const [preview, setPreview] = React.useState<DocItem | null>(null);
    const [targetLaporanId, setTargetLaporanId] = React.useState(
        filters.laporan_id || String(laporans[0]?.id ?? ''),
    );

    const uploadForm = useForm<{
        tipe: 'pekerjaan' | 'administrasi' | 'dokumen';
        file: File | null;
    }>({
        tipe: mode === 'pekerjaan' ? 'pekerjaan' : 'administrasi',
        file: null,
    });

    const applyFilter = (next: Record<string, string | undefined>) => {
        const merged = { ...filters, ...next };
        const clean: Record<string, string> = {};
        Object.entries(merged).forEach(([k, v]) => {
            if (v && v !== 'all') clean[k] = v;
        });
        router.get(baseRoute, clean, { preserveState: true, preserveScroll: true });
    };

    const submitUpload = (e: React.FormEvent) => {
        e.preventDefault();
        if (!targetLaporanId || !uploadForm.data.file) return;
        uploadForm.post(`/laporan/${targetLaporanId}/dokumen`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                uploadForm.reset('file');
                setUploadOpen(false);
            },
        });
    };

    const handleDelete = (id: number) => {
        if (window.confirm('Hapus file dokumentasi ini secara permanen?')) {
            router.delete(`/dokumen/${id}`, { preserveScroll: true });
        }
    };

    return (
        <AppLayout
            title={title}
            subtitle={
                mode === 'pekerjaan'
                    ? 'Arsip foto lapangan sebelum, saat pengerjaan, dan sesudah perbaikan.'
                    : 'Arsip foto administrasi, berita acara, serta dokumen pendukung kontrak.'
            }
            actions={
                laporans.length > 0 && (
                    <Button type="button" onClick={() => setUploadOpen(true)}>
                        <Plus className="h-4 w-4" />
                        Unggah {mode === 'pekerjaan' ? 'Foto' : 'Dokumen'}
                    </Button>
                )
            }
        >
            <Head title={title} />

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
                        placeholder="Cari nama file, No. Insiden, atau pelanggan..."
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
                    value={filters.laporan_id ?? 'all'}
                    onValueChange={(val) => applyFilter({ laporan_id: val === 'all' ? undefined : val })}
                >
                    <SelectTrigger className="w-[180px]">
                        <SelectValue placeholder="Semua Insiden" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Insiden</SelectItem>
                        {laporans.map((l) => (
                            <SelectItem key={l.id} value={String(l.id)}>
                                Insiden {l.no_insiden}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>

                <Button type="submit" variant="secondary">
                    Filter
                </Button>

                {(filters.search || filters.area || filters.laporan_id) && (
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => {
                            setSearch('');
                            router.get(baseRoute);
                        }}
                    >
                        <RotateCcw className="h-3.5 w-3.5" />
                        Reset
                    </Button>
                )}
            </form>

            {items.data.length > 0 ? (
                <>
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        {items.data.map((doc) => (
                            <Card key={doc.id} className="overflow-hidden flex flex-col">
                                {doc.is_image ? (
                                    <button
                                        type="button"
                                        onClick={() => setPreview(doc)}
                                        className="h-44 w-full overflow-hidden bg-[#f2f4f7] cursor-pointer"
                                    >
                                        <img
                                            src={doc.url}
                                            alt={doc.nama_file}
                                            className="h-full w-full object-cover"
                                        />
                                    </button>
                                ) : (
                                    <a
                                        href={doc.url}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="flex h-44 w-full flex-col items-center justify-center bg-[#f4f5f7] px-4 text-center hover:bg-[#e9ecf1]"
                                    >
                                        <FileText className="mb-2 h-8 w-8 text-[#1d5fbf]" />
                                        <span className="line-clamp-2 text-xs font-medium text-[#0a0a0a]">
                                            {doc.nama_file}
                                        </span>
                                    </a>
                                )}

                                <CardContent className="flex flex-1 flex-col justify-between p-3.5">
                                    <div>
                                        <div className="flex items-center justify-between gap-2">
                                            <Link
                                                href={`/laporan/${doc.laporan_id}`}
                                                className="font-mono text-xs font-medium text-[#1d5fbf] hover:underline"
                                            >
                                                #{doc.no_insiden}
                                            </Link>
                                            <Badge variant="outline" className="text-[11px]">
                                                {doc.tipe}
                                            </Badge>
                                        </div>
                                        <p className="mt-1 truncate text-sm font-medium text-[#0a0a0a]">
                                            {doc.nama_file}
                                        </p>
                                        <p className="mt-0.5 truncate text-xs text-[#5b616e]">
                                            {doc.pelanggan} · {doc.area}
                                        </p>
                                    </div>
                                    <div className="mt-3 flex items-center justify-between border-t border-[#eef0f4] pt-2.5 text-xs text-[#5b616e]">
                                        <span>
                                            {doc.ukuran_file
                                                ? `${Math.max(1, Math.round(doc.ukuran_file / 1024))} KB`
                                                : doc.uploaded_at ?? '—'}
                                        </span>
                                        <div className="flex items-center gap-2">
                                            <a
                                                href={doc.url}
                                                download
                                                className="text-[#0a0a0a] hover:text-[#1d5fbf]"
                                                title="Unduh"
                                            >
                                                <Download className="h-3.5 w-3.5" />
                                            </a>
                                            <button
                                                type="button"
                                                onClick={() => handleDelete(doc.id)}
                                                className="text-[#c4242a] hover:opacity-75 cursor-pointer"
                                                title="Hapus"
                                            >
                                                <Trash2 className="h-3.5 w-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                    <div className="mt-2">
                        <PaginationBar paginated={items} />
                    </div>
                </>
            ) : (
                <Card>
                    <CardContent className="p-12 text-center">
                        <p className="text-sm font-medium text-[#0a0a0a]">
                            Belum ada berkas {title.toLowerCase()}
                        </p>
                        <p className="mt-1 text-xs text-[#5b616e]">
                            Unggah foto atau dokumen pendukung ke salah satu work order untuk menampilkannya di sini.
                        </p>
                        {laporans.length > 0 && (
                            <Button type="button" className="mt-4" onClick={() => setUploadOpen(true)}>
                                <Plus className="h-4 w-4" />
                                Unggah Sekarang
                            </Button>
                        )}
                    </CardContent>
                </Card>
            )}

            <Dialog open={uploadOpen} onOpenChange={setUploadOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Unggah ke {title}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={submitUpload} className="space-y-4">
                        <div className="space-y-1.5">
                            <Label>Pilih Insiden</Label>
                            <Select value={targetLaporanId} onValueChange={setTargetLaporanId}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih No. Insiden" />
                                </SelectTrigger>
                                <SelectContent>
                                    {laporans.map((l) => (
                                        <SelectItem key={l.id} value={String(l.id)}>
                                            Insiden {l.no_insiden}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        {mode === 'administrasi' && (
                            <div className="space-y-1.5">
                                <Label>Kategori Berkas</Label>
                                <Select
                                    value={uploadForm.data.tipe}
                                    onValueChange={(val) =>
                                        uploadForm.setData('tipe', val as 'administrasi' | 'dokumen')
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="administrasi">Foto Administrasi</SelectItem>
                                        <SelectItem value="dokumen">Dokumen PDF / Office</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        )}

                        <div className="space-y-1.5">
                            <Label htmlFor="doc_file">Pilih File *</Label>
                            <Input
                                id="doc_file"
                                type="file"
                                accept={
                                    mode === 'pekerjaan'
                                        ? 'image/*'
                                        : 'image/*,.pdf,.doc,.docx,.xls,.xlsx'
                                }
                                onChange={(e) => uploadForm.setData('file', e.target.files?.[0] ?? null)}
                                required
                            />
                            {uploadForm.errors.file && (
                                <p className="text-xs text-[#c4242a]">{uploadForm.errors.file}</p>
                            )}
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setUploadOpen(false)}>
                                Batal
                            </Button>
                            <Button type="submit" disabled={uploadForm.processing || !uploadForm.data.file}>
                                {uploadForm.processing ? 'Mengunggah...' : 'Unggah File'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog open={Boolean(preview)} onOpenChange={(open) => !open && setPreview(null)}>
                <DialogContent className="max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>{preview?.nama_file}</DialogTitle>
                    </DialogHeader>
                    {preview && (
                        <img
                            src={preview.url}
                            alt={preview.nama_file}
                            className="max-h-[70vh] w-full rounded-[6px] object-contain bg-[#f2f4f7]"
                        />
                    )}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
