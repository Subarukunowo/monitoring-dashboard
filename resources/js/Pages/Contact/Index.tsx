import * as React from 'react';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { ExternalLink, Mail, Phone, Send } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { formatDateId } from '@/lib/utils';
import type { PageProps } from '@/types';

interface MessageItem {
    id: number;
    nama: string;
    email: string;
    topik: string;
    pesan: string;
    status: string;
    created_at: string;
}

export default function ContactIndex({ messages }: { messages: MessageItem[] }) {
    const { props } = usePage<PageProps>();
    const user = props.auth?.user;
    const isAdmin = Boolean(user?.is_admin);

    const form = useForm({
        nama: user?.name ?? '',
        email: user?.email ?? '',
        topik: '',
        pesan: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/contact', {
            preserveScroll: true,
            onSuccess: () => form.reset('topik', 'pesan'),
        });
    };

    const statusBadge = (status: string) => {
        if (status === 'selesai') return <Badge variant="positive">Selesai</Badge>;
        if (status === 'dibaca') return <Badge variant="info">Dibaca</Badge>;

        return <Badge variant="warning">Baru</Badge>;
    };

    return (
        <AppLayout
            title="Hubungi Developer"
            subtitle="Laporkan kendala teknis, kesalahan data, atau usulkan fitur baru untuk MaintainNow."
        >
            <Head title="Hubungi Developer" />

            <div className="grid grid-cols-1 gap-5 lg:grid-cols-12">
                <div className="space-y-4 lg:col-span-5">
                    <Card>
                        <CardHeader>
                            <CardTitle>Kontak Tim Pengembang</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <div className="font-semibold text-[#0a0a0a]">Sandy Mulia Kesuma</div>
                                <div className="text-xs text-[#5b616e]">Lead Developer · MaintainNow</div>
                            </div>

                            <div className="space-y-2.5 border-t border-[#eef0f4] pt-3 text-sm">
                                <a
                                    href="mailto:sandymuliakesuma@gmail.com"
                                    className="flex items-center gap-2.5 text-[#0a0a0a] hover:text-[#1d5fbf]"
                                >
                                    <Mail className="h-4 w-4 text-[#5b616e]" />
                                    sandymuliakesuma@gmail.com
                                </a>
                                <a
                                    href="https://wa.me/628217835961"
                                    target="_blank"
                                    rel="noreferrer"
                                    className="flex items-center gap-2.5 text-[#0a0a0a] hover:text-[#1d5fbf]"
                                >
                                    <Phone className="h-4 w-4 text-[#5b616e]" />
                                    +62 821-7835-961 (WhatsApp)
                                    <ExternalLink className="h-3 w-3 text-[#667085]" />
                                </a>
                                <a
                                    href="https://github.com/Subarukunowo"
                                    target="_blank"
                                    rel="noreferrer"
                                    className="flex items-center gap-2.5 text-[#0a0a0a] hover:text-[#1d5fbf]"
                                >
                                    <ExternalLink className="h-4 w-4 text-[#5b616e]" />
                                    github.com/Subarukunowo
                                </a>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>
                                {isAdmin ? `Kotak Masuk Dukungan (${messages.length})` : 'Riwayat Pesan Anda'}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {messages.length > 0 ? (
                                <div className="divide-y divide-[#eef0f4]">
                                    {messages.map((msg) => (
                                        <div key={msg.id} className="py-3 first:pt-0 last:pb-0">
                                            <div className="flex items-start justify-between gap-2">
                                                <div className="font-medium text-sm text-[#0a0a0a]">
                                                    {msg.topik}
                                                </div>
                                                {statusBadge(msg.status)}
                                            </div>
                                            <p className="mt-1 text-xs text-[#5b616e] line-clamp-2">{msg.pesan}</p>
                                            <div className="mt-2 flex items-center justify-between text-[11px] text-[#667085]">
                                                <span>
                                                    {msg.nama} · {formatDateId(msg.created_at)}
                                                </span>
                                                {isAdmin && (
                                                    <Select
                                                        value={msg.status}
                                                        onValueChange={(val) =>
                                                            router.patch(
                                                                `/contact/${msg.id}/status`,
                                                                { status: val },
                                                                { preserveScroll: true },
                                                            )
                                                        }
                                                    >
                                                        <SelectTrigger className="h-7 w-[110px] text-xs">
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectItem value="baru">Baru</SelectItem>
                                                            <SelectItem value="dibaca">Dibaca</SelectItem>
                                                            <SelectItem value="selesai">Selesai</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-xs text-[#5b616e]">Belum ada riwayat pesan dukungan.</p>
                            )}
                        </CardContent>
                    </Card>
                </div>

                <Card className="lg:col-span-7">
                    <CardHeader>
                        <CardTitle>Kirim Laporan Bug atau Permintaan Fitur</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-4">
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-1.5">
                                    <Label htmlFor="nama">Nama Pengirim</Label>
                                    <Input
                                        id="nama"
                                        value={form.data.nama}
                                        onChange={(e) => form.setData('nama', e.target.value)}
                                        disabled={Boolean(user)}
                                        required
                                    />
                                </div>
                                <div className="space-y-1.5">
                                    <Label htmlFor="email">Email</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={form.data.email}
                                        onChange={(e) => form.setData('email', e.target.value)}
                                        disabled={Boolean(user)}
                                        required
                                    />
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <Label htmlFor="topik">Topik *</Label>
                                <Input
                                    id="topik"
                                    value={form.data.topik}
                                    onChange={(e) => form.setData('topik', e.target.value)}
                                    placeholder="Contoh: Sinkronisasi BQ ke RAB atau kendala unggah foto"
                                    required
                                />
                                {form.errors.topik && (
                                    <p className="text-xs text-[#c4242a]">{form.errors.topik}</p>
                                )}
                            </div>

                            <div className="space-y-1.5">
                                <Label htmlFor="pesan">Detail Pesan *</Label>
                                <Textarea
                                    id="pesan"
                                    rows={6}
                                    value={form.data.pesan}
                                    onChange={(e) => form.setData('pesan', e.target.value)}
                                    placeholder="Jelaskan kronologi masalah, nomor insiden terkait, atau fitur yang dibutuhkan..."
                                    required
                                />
                                {form.errors.pesan && (
                                    <p className="text-xs text-[#c4242a]">{form.errors.pesan}</p>
                                )}
                            </div>

                            <Button type="submit" disabled={form.processing}>
                                <Send className="h-4 w-4" />
                                {form.processing ? 'Mengirim...' : 'Kirim Pesan'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
