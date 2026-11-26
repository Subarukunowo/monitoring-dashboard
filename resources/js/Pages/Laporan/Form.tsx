import * as React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, RotateCcw, Save, Upload, X } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';

interface ExistingDoc {
    id: number;
    tipe: string;
    nama_file: string;
    url: string;
    mime_type: string;
    ukuran_file: number | null;
}

interface Props {
    mode: 'create' | 'edit';
    laporan?: {
        id: number;
        no_insiden: string;
        tanggal_laporan: string;
        jam_laporan: string;
        id_area: number;
        id_status: number;
        nama_pengawas: string;
        jenis_pekerjaan: string;
        tanggal_survei: string;
        tanggal_selesai: string;
        nilai_rab: string | number | null;
        no_sap: string;
        link_maps: string;
        keterangan: string;
        no_pelanggan: string;
        nama_pelanggan: string;
        no_telepon: string;
        alamat: string;
        status_pelanggan: boolean;
        dokumen: ExistingDoc[];
    };
    areas: Array<{ id: number; nama_area: string }>;
    statuses: Array<{ id: number; status_kerja: string; label: string }>;
    pengawasSuggestions: string[];
    jenisSuggestions: string[];
}

export default function LaporanForm({
    mode,
    laporan,
    areas,
    statuses,
    pengawasSuggestions,
    jenisSuggestions,
}: Props) {
    const isEdit = mode === 'edit' && Boolean(laporan);

    const { data, setData, post, processing, errors } = useForm<{
        _method?: string;
        no_insiden: string;
        tanggal_laporan: string;
        jam_laporan: string;
        id_area: string;
        id_status: string;
        nama_pengawas: string;
        jenis_pekerjaan: string;
        tanggal_survei: string;
        tanggal_selesai: string;
        nilai_rab: string;
        no_sap: string;
        link_maps: string;
        keterangan: string;
        no_pelanggan: string;
        nama_pelanggan: string;
        no_telepon: string;
        alamat: string;
        status_pelanggan: string;
        foto_pekerjaan: File[];
        foto_administrasi: File[];
        dokumen: File[];
        hapus_dokumen: number[];
    }>({
        ...(isEdit ? { _method: 'PUT' } : {}),
        no_insiden: laporan?.no_insiden ?? '',
        tanggal_laporan: laporan?.tanggal_laporan ?? new Date().toISOString().slice(0, 10),
        jam_laporan: laporan?.jam_laporan ?? '',
        id_area: laporan?.id_area ? String(laporan.id_area) : '',
        id_status: laporan?.id_status ? String(laporan.id_status) : String(statuses[0]?.id ?? ''),
        nama_pengawas: laporan?.nama_pengawas ?? '',
        jenis_pekerjaan: laporan?.jenis_pekerjaan ?? '',
        tanggal_survei: laporan?.tanggal_survei ?? '',
        tanggal_selesai: laporan?.tanggal_selesai ?? '',
        nilai_rab: laporan?.nilai_rab !== null && laporan?.nilai_rab !== undefined ? String(laporan.nilai_rab) : '',
        no_sap: laporan?.no_sap ?? '',
        link_maps: laporan?.link_maps ?? '',
        keterangan: laporan?.keterangan ?? '',
        no_pelanggan: laporan?.no_pelanggan ?? '',
        nama_pelanggan: laporan?.nama_pelanggan ?? '',
        no_telepon: laporan?.no_telepon ?? '',
        alamat: laporan?.alamat ?? '',
        status_pelanggan: laporan?.status_pelanggan === false ? '0' : '1',
        foto_pekerjaan: [],
        foto_administrasi: [],
        dokumen: [],
        hapus_dokumen: [],
    });

    const toggleHapusDokumen = (docId: number) => {
        const exists = data.hapus_dokumen.includes(docId);
        setData(
            'hapus_dokumen',
            exists ? data.hapus_dokumen.filter((id) => id !== docId) : [...data.hapus_dokumen, docId],
        );
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit && laporan) {
            post(`/laporan/${laporan.id}`, { forceFormData: true });
        } else {
            post('/laporan', { forceFormData: true });
        }
    };

    const errorMessages = Object.values(errors);
    const existingDocs = laporan?.dokumen ?? [];

    return (
        <AppLayout
            title={isEdit ? `Edit Laporan ${laporan?.no_insiden}` : 'Buat Laporan Insiden Baru'}
            subtitle="Isi informasi work order, data pelanggan PLN, serta lampiran foto & dokumen."
            actions={
                <Button asChild variant="outline">
                    <Link href={isEdit && laporan ? `/laporan/${laporan.id}` : '/laporan'}>
                        <ArrowLeft className="h-4 w-4" />
                        Kembali
                    </Link>
                </Button>
            }
        >
            <Head title={isEdit ? 'Edit Laporan' : 'Buat Laporan'} />

            {errorMessages.length > 0 && (
                <Alert variant="destructive" className="mb-5">
                    <AlertDescription>
                        <ul className="list-disc list-inside space-y-0.5">
                            {errorMessages.map((msg, i) => (
                                <li key={i}>{msg}</li>
                            ))}
                        </ul>
                    </AlertDescription>
                </Alert>
            )}

            <form onSubmit={submit} className="space-y-5">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Insiden & Penugasan</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div className="space-y-1.5">
                            <Label htmlFor="no_insiden">No. Insiden *</Label>
                            <Input
                                id="no_insiden"
                                value={data.no_insiden}
                                onChange={(e) => setData('no_insiden', e.target.value)}
                                placeholder="Contoh: 925300"
                                required
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="tanggal_laporan">Tanggal Laporan *</Label>
                            <Input
                                id="tanggal_laporan"
                                type="date"
                                value={data.tanggal_laporan}
                                onChange={(e) => setData('tanggal_laporan', e.target.value)}
                                required
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="jam_laporan">Jam Laporan</Label>
                            <Input
                                id="jam_laporan"
                                type="time"
                                value={data.jam_laporan}
                                onChange={(e) => setData('jam_laporan', e.target.value)}
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label>Area Distribusi *</Label>
                            <Select value={data.id_area} onValueChange={(val) => setData('id_area', val)}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih Area" />
                                </SelectTrigger>
                                <SelectContent>
                                    {areas.map((a) => (
                                        <SelectItem key={a.id} value={String(a.id)}>
                                            {a.nama_area}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-1.5">
                            <Label>Status Pekerjaan *</Label>
                            <Select value={data.id_status} onValueChange={(val) => setData('id_status', val)}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    {statuses.map((s) => (
                                        <SelectItem key={s.id} value={String(s.id)}>
                                            {s.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="nama_pengawas">Pengawas Lapangan</Label>
                            <Input
                                id="nama_pengawas"
                                list="pengawas-list"
                                value={data.nama_pengawas}
                                onChange={(e) => setData('nama_pengawas', e.target.value)}
                                placeholder="Nama pengawas"
                            />
                            <datalist id="pengawas-list">
                                {pengawasSuggestions.map((p) => (
                                    <option key={p} value={p} />
                                ))}
                            </datalist>
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="jenis_pekerjaan">Jenis Pekerjaan</Label>
                            <Input
                                id="jenis_pekerjaan"
                                list="jenis-list"
                                value={data.jenis_pekerjaan}
                                onChange={(e) => setData('jenis_pekerjaan', e.target.value)}
                                placeholder="Contoh: Tiang Miring"
                            />
                            <datalist id="jenis-list">
                                {jenisSuggestions.map((j) => (
                                    <option key={j} value={j} />
                                ))}
                            </datalist>
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="tanggal_survei">Tanggal Survei</Label>
                            <Input
                                id="tanggal_survei"
                                type="date"
                                value={data.tanggal_survei}
                                onChange={(e) => setData('tanggal_survei', e.target.value)}
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="tanggal_selesai">Target / Tanggal Selesai</Label>
                            <Input
                                id="tanggal_selesai"
                                type="date"
                                value={data.tanggal_selesai}
                                onChange={(e) => setData('tanggal_selesai', e.target.value)}
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="no_sap">No. SAP / TUG</Label>
                            <Input
                                id="no_sap"
                                value={data.no_sap}
                                onChange={(e) => setData('no_sap', e.target.value)}
                                placeholder="Contoh: SAP : 118873"
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="nilai_rab">Nilai RAB (Rp)</Label>
                            <Input
                                id="nilai_rab"
                                type="number"
                                min="0"
                                step="100"
                                value={data.nilai_rab}
                                onChange={(e) => setData('nilai_rab', e.target.value)}
                                placeholder="0"
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="link_maps">Tautan Google Maps</Label>
                            <Input
                                id="link_maps"
                                type="url"
                                value={data.link_maps}
                                onChange={(e) => setData('link_maps', e.target.value)}
                                placeholder="https://maps.google.com/..."
                            />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Data Pelanggan & Keterangan</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div className="space-y-1.5">
                            <Label htmlFor="no_pelanggan">No. Pelanggan (ID PLN)</Label>
                            <Input
                                id="no_pelanggan"
                                type="number"
                                value={data.no_pelanggan}
                                onChange={(e) => setData('no_pelanggan', e.target.value)}
                                placeholder="Contoh: 151001728632"
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="nama_pelanggan">Nama Pelanggan / Pelapor *</Label>
                            <Input
                                id="nama_pelanggan"
                                value={data.nama_pelanggan}
                                onChange={(e) => setData('nama_pelanggan', e.target.value)}
                                placeholder="Nama lengkap"
                                required
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="no_telepon">No. Telepon</Label>
                            <Input
                                id="no_telepon"
                                value={data.no_telepon}
                                onChange={(e) => setData('no_telepon', e.target.value)}
                                placeholder="0812xxxxxxxx"
                            />
                        </div>

                        <div className="space-y-1.5">
                            <Label>Tipe Pelanggan</Label>
                            <Select
                                value={data.status_pelanggan}
                                onValueChange={(val) => setData('status_pelanggan', val)}
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="1">Pelanggan Resmi PLN</SelectItem>
                                    <SelectItem value="0">Non-Pelanggan / Umum</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-1.5 md:col-span-2">
                            <Label htmlFor="alamat">Alamat Lokasi</Label>
                            <Textarea
                                id="alamat"
                                rows={2}
                                value={data.alamat}
                                onChange={(e) => setData('alamat', e.target.value)}
                                placeholder="Alamat lengkap lokasi kejadian..."
                            />
                        </div>

                        <div className="space-y-1.5 md:col-span-2">
                            <Label htmlFor="keterangan">Keterangan Teknis</Label>
                            <Textarea
                                id="keterangan"
                                rows={3}
                                value={data.keterangan}
                                onChange={(e) => setData('keterangan', e.target.value)}
                                placeholder="Catatan tindak lanjut pekerjaan atau kendala di lapangan..."
                            />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Lampiran Foto & Dokumen</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        {existingDocs.length > 0 && (
                            <div>
                                <p className="mb-2 text-xs font-medium text-[#5b616e]">
                                    Lampiran tersimpan saat ini (klik tombol hapus untuk menandai penghapusan):
                                </p>
                                <div className="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    {existingDocs.map((doc) => {
                                        const marked = data.hapus_dokumen.includes(doc.id);

                                        return (
                                            <div
                                                key={doc.id}
                                                className={`flex items-center justify-between rounded-[6px] border px-3 py-2 text-xs ${
                                                    marked
                                                        ? 'border-[#fecaca] bg-[#fff5f5] line-through opacity-70'
                                                        : 'border-[#e3e6ec] bg-[#f7f8fa]'
                                                }`}
                                            >
                                                <span className="truncate pr-2">
                                                    [{doc.tipe}] {doc.nama_file}
                                                </span>
                                                <button
                                                    type="button"
                                                    onClick={() => toggleHapusDokumen(doc.id)}
                                                    className="shrink-0 text-[#c4242a] hover:underline"
                                                >
                                                    {marked ? (
                                                        <RotateCcw className="h-3.5 w-3.5" />
                                                    ) : (
                                                        <X className="h-3.5 w-3.5" />
                                                    )}
                                                </button>
                                            </div>
                                        );
                                    })}
                                </div>
                                <Separator className="mt-4" />
                            </div>
                        )}

                        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div className="space-y-2">
                                <Label htmlFor="foto_pekerjaan">Foto Pekerjaan</Label>
                                <label
                                    htmlFor="foto_pekerjaan"
                                    className="flex h-24 cursor-pointer flex-col items-center justify-center rounded-[6px] border border-dashed border-[#d0d5dd] bg-[#f7f8fa] px-3 text-center hover:border-[#1d5fbf]"
                                >
                                    <Upload className="mb-1 h-4 w-4 text-[#5b616e]" />
                                    <span className="text-xs text-[#0a0a0a]">Pilih foto pekerjaan</span>
                                    <span className="text-[11px] text-[#667085]">JPG/PNG/WEBP, maks 5MB</span>
                                    <input
                                        id="foto_pekerjaan"
                                        type="file"
                                        multiple
                                        accept="image/*"
                                        className="hidden"
                                        onChange={(e) => setData('foto_pekerjaan', Array.from(e.target.files ?? []))}
                                    />
                                </label>
                                {data.foto_pekerjaan.length > 0 && (
                                    <p className="text-xs text-[#1d5fbf]">{data.foto_pekerjaan.length} file dipilih</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="foto_administrasi">Foto Administrasi</Label>
                                <label
                                    htmlFor="foto_administrasi"
                                    className="flex h-24 cursor-pointer flex-col items-center justify-center rounded-[6px] border border-dashed border-[#d0d5dd] bg-[#f7f8fa] px-3 text-center hover:border-[#1d5fbf]"
                                >
                                    <Upload className="mb-1 h-4 w-4 text-[#5b616e]" />
                                    <span className="text-xs text-[#0a0a0a]">Pilih foto administrasi</span>
                                    <span className="text-[11px] text-[#667085]">JPG/PNG/WEBP, maks 5MB</span>
                                    <input
                                        id="foto_administrasi"
                                        type="file"
                                        multiple
                                        accept="image/*"
                                        className="hidden"
                                        onChange={(e) => setData('foto_administrasi', Array.from(e.target.files ?? []))}
                                    />
                                </label>
                                {data.foto_administrasi.length > 0 && (
                                    <p className="text-xs text-[#1d5fbf]">{data.foto_administrasi.length} file dipilih</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="dokumen">Dokumen Pendukung</Label>
                                <label
                                    htmlFor="dokumen"
                                    className="flex h-24 cursor-pointer flex-col items-center justify-center rounded-[6px] border border-dashed border-[#d0d5dd] bg-[#f7f8fa] px-3 text-center hover:border-[#1d5fbf]"
                                >
                                    <Upload className="mb-1 h-4 w-4 text-[#5b616e]" />
                                    <span className="text-xs text-[#0a0a0a]">Pilih dokumen PDF/Office</span>
                                    <span className="text-[11px] text-[#667085]">PDF/DOC/XLS, maks 10MB</span>
                                    <input
                                        id="dokumen"
                                        type="file"
                                        multiple
                                        accept=".pdf,.doc,.docx,.xls,.xlsx"
                                        className="hidden"
                                        onChange={(e) => setData('dokumen', Array.from(e.target.files ?? []))}
                                    />
                                </label>
                                {data.dokumen.length > 0 && (
                                    <p className="text-xs text-[#1d5fbf]">{data.dokumen.length} file dipilih</p>
                                )}
                            </div>
                        </div>

                        <div className="flex justify-end gap-2 pt-2">
                            <Button asChild variant="outline">
                                <Link href={isEdit && laporan ? `/laporan/${laporan.id}` : '/laporan'}>
                                    Batal
                                </Link>
                            </Button>
                            <Button type="submit" disabled={processing}>
                                <Save className="h-4 w-4" />
                                {processing ? 'Menyimpan...' : isEdit ? 'Simpan Perubahan' : 'Simpan Laporan'}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </form>
        </AppLayout>
    );
}
