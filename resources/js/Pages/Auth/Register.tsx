import * as React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { Eye, EyeOff, Lock, User } from 'lucide-react';
import { MaintainNowLogo } from '@/Layouts/AppLayout';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function Register() {
    const [showPass, setShowPass] = React.useState(false);
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <div className="min-h-screen bg-[#f2f4f7] flex flex-col items-center justify-center px-4 py-10">
            <Head title="Daftar Akun" />

            <div className="w-full max-w-[400px]">
                <div className="mb-6 flex items-center gap-3">
                    <MaintainNowLogo />
                    <div>
                        <h1 className="text-xl font-semibold tracking-tight text-[#0a0a0a]">
                            MaintainNow
                        </h1>
                        <p className="text-xs text-[#5b616e]">
                            Pendaftaran Akun Petugas Lapangan
                        </p>
                    </div>
                </div>

                <Card>
                    <CardContent className="p-6">
                        <div className="mb-5">
                            <h2 className="text-base font-semibold text-[#0a0a0a]">Buat Akun Baru</h2>
                            <p className="mt-1 text-xs text-[#5b616e]">
                                Akun baru terdaftar sebagai petugas operasional lapangan.
                            </p>
                        </div>

                        {(errors.name || errors.password) && (
                            <Alert variant="destructive" className="mb-4 py-2.5">
                                <AlertDescription>{errors.name || errors.password}</AlertDescription>
                            </Alert>
                        )}

                        <form onSubmit={submit} className="space-y-4">
                            <div className="space-y-1.5">
                                <Label htmlFor="name">Nama Pengguna</Label>
                                <div className="relative">
                                    <User className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#667085]" />
                                    <Input
                                        id="name"
                                        type="text"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="Masukkan nama pengguna"
                                        autoFocus
                                        className="pl-9"
                                    />
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <Label htmlFor="password">Password</Label>
                                <div className="relative">
                                    <Lock className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#667085]" />
                                    <Input
                                        id="password"
                                        type={showPass ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Minimal 8 karakter"
                                        className="pl-9 pr-9"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPass((p) => !p)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-[#667085]"
                                        aria-label="Toggle password"
                                    >
                                        {showPass ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                    </button>
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <Label htmlFor="password_confirmation">Konfirmasi Password</Label>
                                <div className="relative">
                                    <Lock className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#667085]" />
                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        placeholder="Ulangi password"
                                        className="pl-9"
                                    />
                                </div>
                            </div>

                            <Button type="submit" className="w-full" disabled={processing}>
                                {processing ? 'Mendaftarkan...' : 'Daftar Sekarang'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <p className="mt-4 text-center text-xs text-[#5b616e]">
                    Sudah punya akun?{' '}
                    <Link href="/login" className="font-medium text-[#1d5fbf] hover:underline">
                        Masuk di sini
                    </Link>
                </p>
            </div>
        </div>
    );
}
