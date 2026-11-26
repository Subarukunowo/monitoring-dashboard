import * as React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import {
    ArrowLeft,
    Download,
    ExternalLink,
    Pencil,
    Printer,
    Trash2,
} from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { RabStatusBadge, StatusBadge } from '@/components/StatusBadge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { formatDateId, formatRupiah } from '@/lib/utils';

interface DokItem {
    id: number;
    tipe: string;
    nama_file: string;
    url: string;
    mime_type: string;
    ukuran_file: number | null;
}

interface BqItemRow {
    id: number;
    kode_material: string | null;
    uraian: string;
    kategori: string;
    satuan: string;
    volume: number;
    harga_satuan: number;
    subtotal: number;
}

interface Props {
    laporan: {
        id: number;
        no_insiden: string;
        tanggal_laporan: string | null;
        jam_laporan: string | null;
        tanggal_survei: string | null;
        tanggal_selesai: string | null;
        nilai_rab: number | null;
        status_rab: string;
        rab_catatan: string | null;
        no_sap: string | null;
        keterangan: string | null;
        link_maps: string | null;
        is_closed: boolean;
        closed_at: string | null;
        closed_by_name: string | null;
        catatan_penutupan: string | null;
        created_at: string | null;
        updated_at: string | null;
        is_overdue: boolean;
        area: { id: number; nama_area: string } | null;
        pelanggan: {
            id: number;
            no_pelanggan: string | null;
            nama: string;
            no_telepon: string | null;
            alamat: string | null;
            status_pelanggan: number;
        } | null;
        pengawas: { id: number; nama: string; no_telepon: string | null } | null;
        jenis_pekerjaan: { id: number; nama_jenis: string } | null;
        status: { id: number; status_kerja: string; label: string } | null;
        dokumen: DokItem[];
        bq_items: BqItemRow[];
    };
}

export default function LaporanShow({ laporan }: Props) {
    const [previewDoc, setPreviewDoc] = React.useState<DokItem | null>(null);

    const fotoPekerjaan = laporan.dokumen.filter((d) => d.tipe === 'pekerjaan');
    const fotoAdministrasi = laporan.dokumen.filter((d) => d.tipe === 'administrasi');
    const dokumenList = laporan.dokumen.filter((d) => d.tipe === 'dokumen');

    const handleDelete = () => {
        if (window.confirm(`Hapus laporan insiden ${laporan.no_insiden} beserta seluruh lampirannya?`)) {
            router.delete(`/laporan/${laporan.id}`);
        }
    };

    const bqSum = laporan.bq_items.reduce((acc, item) => acc + Number(item.subtotal), 0);

    return (
        <AppLayout
            title={`Insiden ${laporan.no_insiden}`}
            subtitle={`Dilaporkan ${formatDateId(laporan.tanggal_laporan)}${laporan.jam_laporan ? ` · ${laporan.jam_laporan} WIB` : ''}`}
            actions={
                <>
                    <Button asChild variant="outline">
                        <Link href="/laporan">
                            <ArrowLeft className="h-4 w-4" />
                            Daftar
                        </Link>
                    </Button>
                    <Button type="button" variant="outline" onClick={() => window.print()}>
                        <Printer className="h-4 w-4" />
                        Cetak
                    </Button>
                    <Button asChild variant="outline">
                        <Link href={`/laporan/${laporan.id}/edit`}>
                            <Pencil className="h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button type="button" variant="destructive" onClick={handleDelete}>
                        <Trash2 className="h-4 w-4" />
                        Hapus
                    </Button>
                </>
            }
        >
            <Head title={`Insiden ${laporan.no_insiden}`} />

            {/* Status summary strip */}
            <div className="mb-5 flex flex-wrap items-center gap-2">
                <StatusBadge statusKerja={laporan.status?.status_kerja} label={laporan.status?.label} />
                {laporan.is_overdue && <Badge variant="negative">Melewati Deadline</Badge>}
                {laporan.is_closed ? (
                    <Badge variant="positive">Kontrak Ditutup</Badge>
                ) : (
                    <Badge variant="outline">Kontrak Terbuka</Badge>
                )}
                <RabStatusBadge statusRab={laporan.status_rab} />
            </div>

            <Tabs defaultValue="ringkasan">
                <TabsList>
                    <TabsTrigger value="ringkasan">Ringkasan Pekerjaan</TabsTrigger>
                    <TabsTrigger value="dokumentasi">
                        Dokumentasi ({laporan.dokumen.length})
                    </TabsTrigger>
                    <TabsTrigger value="rab">
                        RAB & Bill of Quantity ({laporan.bq_items.length})
                    </TabsTrigger>
                </TabsList>

                <TabsContent value="ringkasan">
                    <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div className="space-y-4 lg:col-span-2">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Lokasi & Penugasan</CardTitle>
                                </CardHeader>
                                <CardContent className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <div className="text-xs text-[#5b616e]">Area Distribusi</div>
                                        <div className="mt-0.5 font-medium">{laporan.area?.nama_area ?? '—'}</div>
                                    </div>
                                    <div>
                                        <div className="text-xs text-[#5b616e]">Pengawas</div>
                                        <div className="mt-0.5 font-medium">{laporan.pengawas?.nama ?? '—'}</div>
                                    </div>
                                    <div>
                                        <div className="text-xs text-[#5b616e]">Jenis Pekerjaan</div>
                                        <div className="mt-0.5 font-medium">
                                            {laporan.jenis_pekerjaan?.nama_jenis ?? '—'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-xs text-[#5b616e]">No. SAP / TUG</div>
                                        <div className="mt-0.5 font-mono text-sm">{laporan.no_sap ?? '—'}</div>
                                    </div>
                                    {laporan.link_maps && (
                                        <div className="sm:col-span-2">
                                            <a
                                                href={laporan.link_maps}
                                                target="_blank"
                                                rel="noreferrer"
                                                className="inline-flex items-center gap-1.5 text-sm font-medium text-[#1d5fbf] hover:underline"
                                            >
                                                Buka Titik Lokasi di Maps
                                                <ExternalLink className="h-3.5 w-3.5" />
                                            </a>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Informasi Pelanggan</CardTitle>
                                </CardHeader>
                                <CardContent className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <div className="text-xs text-[#5b616e]">Nama Pelanggan / Pelapor</div>
                                        <div className="mt-0.5 font-medium">
                                            {laporan.pelanggan?.nama ?? 'Umum / Non-Pelanggan'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-xs text-[#5b616e]">No. Pelanggan (ID PLN)</div>
                                        <div className="mt-0.5 font-mono text-sm">
                                            {laporan.pelanggan?.no_pelanggan ?? '—'}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-xs text-[#5b616e]">Telepon</div>
                                        <div className="mt-0.5">{laporan.pelanggan?.no_telepon ?? '—'}</div>
                                    </div>
                                    <div>
                                        <div className="text-xs text-[#5b616e]">Kategori</div>
                                        <div className="mt-0.5">
                                            {laporan.pelanggan?.status_pelanggan
                                                ? 'Pelanggan Resmi PLN'
                                                : 'Non-Pelanggan / Umum'}
                                        </div>
                                    </div>
                                    <div className="sm:col-span-2">
                                        <div className="text-xs text-[#5b616e]">Alamat</div>
                                        <div className="mt-0.5 text-[#0a0a0a]">{laporan.pelanggan?.alamat ?? '—'}</div>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Keterangan Lapangan</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="whitespace-pre-wrap text-sm text-[#0a0a0a]">
                                        {laporan.keterangan || 'Belum ada keterangan tambahan.'}
                                    </p>
                                </CardContent>
                            </Card>
                        </div>

                        <div className="space-y-4">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Jadwal & Nilai</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-3 text-sm tabular-nums">
                                    <div className="flex justify-between">
                                        <span className="text-[#5b616e]">Tanggal Survei</span>
                                        <span>{formatDateId(laporan.tanggal_survei)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-[#5b616e]">Target Selesai</span>
                                        <span className={laporan.is_overdue ? 'font-medium text-[#c4242a]' : ''}>
                                            {formatDateId(laporan.tanggal_selesai)}
                                        </span>
                                    </div>
                                    <div className="flex justify-between border-t border-[#eef0f4] pt-3">
                                        <span className="text-[#5b616e]">Nilai RAB</span>
                                        <span className="font-bold">{formatRupiah(laporan.nilai_rab)}</span>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Metadata & Kontrak</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-2.5 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-[#5b616e]">ID Record</span>
                                        <span className="font-mono">#{laporan.id}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-[#5b616e]">Dibuat</span>
                                        <span>{laporan.created_at ?? '—'}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-[#5b616e]">Diperbarui</span>
                                        <span>{laporan.updated_at ?? '—'}</span>
                                    </div>
                                    {laporan.is_closed && (
                                        <div className="mt-3 rounded-[6px] border border-[#bbf7d0] bg-[#f2fbf5] p-3 text-xs text-[#04250e]">
                                            Ditutup pada {laporan.closed_at} oleh {laporan.closed_by_name ?? 'Admin'}.
                                            {laporan.catatan_penutupan && (
                                                <div className="mt-1">{laporan.catatan_penutupan}</div>
                                            )}
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </TabsContent>

                <TabsContent value="dokumentasi">
                    <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>Foto Pekerjaan ({fotoPekerjaan.length})</CardTitle>
                            </CardHeader>
                            <CardContent>
                                {fotoPekerjaan.length > 0 ? (
                                    <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                        {fotoPekerjaan.map((foto) => (
                                            <button
                                                key={foto.id}
                                                type="button"
                                                onClick={() => setPreviewDoc(foto)}
                                                className="overflow-hidden rounded-[6px] border border-[#e3e6ec] text-left cursor-pointer"
                                            >
                                                <img
                                                    src={foto.url}
                                                    alt={foto.nama_file}
                                                    className="h-28 w-full object-cover"
                                                />
                                                <div className="truncate px-2 py-1 text-[11px] text-[#5b616e]">
                                                    {foto.nama_file}
                                                </div>
                                            </button>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-sm text-[#5b616e]">Belum ada foto pekerjaan.</p>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Foto Administrasi & Dokumen ({fotoAdministrasi.length + dokumenList.length})</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {fotoAdministrasi.length > 0 && (
                                    <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                        {fotoAdministrasi.map((foto) => (
                                            <button
                                                key={foto.id}
                                                type="button"
                                                onClick={() => setPreviewDoc(foto)}
                                                className="overflow-hidden rounded-[6px] border border-[#e3e6ec] text-left cursor-pointer"
                                            >
                                                <img
                                                    src={foto.url}
                                                    alt={foto.nama_file}
                                                    className="h-24 w-full object-cover"
                                                />
                                                <div className="truncate px-2 py-1 text-[11px] text-[#5b616e]">
                                                    {foto.nama_file}
                                                </div>
                                            </button>
                                        ))}
                                    </div>
                                )}

                                {dokumenList.length > 0 && (
                                    <div className="divide-y divide-[#eef0f4] rounded-[6px] border border-[#e3e6ec]">
                                        {dokumenList.map((doc) => (
                                            <a
                                                key={doc.id}
                                                href={doc.url}
                                                target="_blank"
                                                rel="noreferrer"
                                                className="flex items-center justify-between px-3 py-2 text-sm hover:bg-[#f7f8fa]"
                                            >
                                                <span className="truncate pr-2">{doc.nama_file}</span>
                                                <Download className="h-4 w-4 shrink-0 text-[#5b616e]" />
                                            </a>
                                        ))}
                                    </div>
                                )}

                                {fotoAdministrasi.length === 0 && dokumenList.length === 0 && (
                                    <p className="text-sm text-[#5b616e]">Belum ada dokumen administrasi.</p>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>

                <TabsContent value="rab">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0">
                            <div>
                                <CardTitle>Rincian Bill of Quantity & RAB</CardTitle>
                                <p className="mt-0.5 text-xs text-[#5b616e]">
                                    Total item BQ: {formatRupiah(bqSum)} · Nilai RAB tersimpan: {formatRupiah(laporan.nilai_rab)}
                                </p>
                            </div>
                            <Button asChild variant="outline" size="sm">
                                <Link href={`/bill-of-quantity?laporan_id=${laporan.id}`}>
                                    Kelola di Modul BQ
                                </Link>
                            </Button>
                        </CardHeader>
                        <CardContent className="p-0">
                            {laporan.bq_items.length > 0 ? (
                                <Table>
                                    <TableHeader>
                                        <TableRow className="hover:bg-transparent">
                                            <TableHead>Kode</TableHead>
                                            <TableHead>Uraian</TableHead>
                                            <TableHead>Kategori</TableHead>
                                            <TableHead className="text-right">Volume</TableHead>
                                            <TableHead className="text-right">Harga Satuan</TableHead>
                                            <TableHead className="text-right">Subtotal</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {laporan.bq_items.map((item) => (
                                            <TableRow key={item.id}>
                                                <TableCell className="font-mono text-xs">{item.kode_material ?? '—'}</TableCell>
                                                <TableCell>{item.uraian}</TableCell>
                                                <TableCell className="text-[#5b616e]">{item.kategori}</TableCell>
                                                <TableCell className="text-right">
                                                    {item.volume} {item.satuan}
                                                </TableCell>
                                                <TableCell className="text-right">{formatRupiah(item.harga_satuan)}</TableCell>
                                                <TableCell className="text-right font-semibold">
                                                    {formatRupiah(item.subtotal)}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            ) : (
                                <div className="p-8 text-center text-sm text-[#5b616e]">
                                    Belum ada item Bill of Quantity untuk insiden ini.
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>

            <Dialog open={Boolean(previewDoc)} onOpenChange={(open) => !open && setPreviewDoc(null)}>
                <DialogContent className="max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>{previewDoc?.nama_file}</DialogTitle>
                    </DialogHeader>
                    {previewDoc && (
                        <div className="space-y-3">
                            <img
                                src={previewDoc.url}
                                alt={previewDoc.nama_file}
                                className="max-h-[70vh] w-full rounded-[6px] object-contain bg-[#f2f4f7]"
                            />
                            <div className="flex justify-end">
                                <Button asChild variant="outline" size="sm">
                                    <a href={previewDoc.url} download>
                                        <Download className="h-4 w-4" />
                                        Unduh File
                                    </a>
                                </Button>
                            </div>
                        </div>
                    )}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
