import * as React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { Eye, EyeOff, Lock, User } from 'lucide-react';
import { MaintainNowLogo } from '@/Layouts/AppLayout';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function Login() {
    const [showPassword, setShowPassword] = React.useState(false);
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        password: '',
        remember: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/login');
    };

    const fillDemo = (name: string) => {
        setData({ name, password: 'password123', remember: false });
    };

    return (
        <div className="min-h-screen bg-[#f2f4f7] flex flex-col items-center justify-center px-4 py-10">
            <Head title="Masuk" />

            <div className="w-full max-w-[400px]">
                <div className="mb-6 flex items-center gap-3">
                    <MaintainNowLogo />
                    <div>
                        <h1 className="text-xl font-semibold tracking-tight text-[#0a0a0a]">
                            MaintainNow
                        </h1>
                        <p className="text-xs text-[#5b616e]">
                            Sistem Monitoring Pemeliharaan Distribusi PLN
                        </p>
                    </div>
                </div>

                <Card>
                    <CardContent className="p-6">
                        <div className="mb-5">
                            <h2 className="text-base font-semibold text-[#0a0a0a]">Masuk ke Workspace</h2>
                            <p className="mt-1 text-xs text-[#5b616e]">
                                Gunakan akun petugas lapangan atau administrator Anda.
                            </p>
                        </div>

                        {(errors.name || errors.password) && (
                            <Alert variant="destructive" className="mb-4 py-2.5">
                                <AlertDescription>
                                    {errors.name || errors.password}
                                </AlertDescription>
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
                                        placeholder="contoh: admin atau petugas"
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
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Masukkan password"
                                        className="pl-9 pr-9"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword((prev) => !prev)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-[#667085] hover:text-[#0a0a0a]"
                                        aria-label="Tampilkan atau sembunyikan password"
                                    >
                                        {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                    </button>
                                </div>
                            </div>

                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="remember"
                                    checked={data.remember}
                                    onCheckedChange={(checked) => setData('remember', checked === true)}
                                />
                                <Label htmlFor="remember" className="text-xs font-normal text-[#5b616e] cursor-pointer">
                                    Ingat sesi saya
                                </Label>
                            </div>

                            <Button type="submit" className="w-full" disabled={processing}>
                                {processing ? 'Memproses...' : 'Masuk'}
                            </Button>
                        </form>

                        <div className="mt-5 border-t border-[#eef0f4] pt-4">
                            <p className="mb-2 text-xs text-[#5b616e]">Akses cepat akun demo:</p>
                            <div className="grid grid-cols-2 gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={() => fillDemo('petugas')}
                                >
                                    Isi Akun Petugas
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={() => fillDemo('admin')}
                                >
                                    Isi Akun Admin
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <p className="mt-4 text-center text-xs text-[#5b616e]">
                    Belum punya akun?{' '}
                    <Link href="/register" className="font-medium text-[#1d5fbf] hover:underline">
                        Daftar akun petugas
                    </Link>
                </p>
                <p className="mt-3 text-center text-[11px] leading-relaxed text-[#667085]">
                    Disclaimer: Seluruh data pada aplikasi ini bersifat dummy (simulasi) dan bukan kegiatan yang direncanakan oleh instansi pemerintah terkait.
                </p>
            </div>
        </div>
    );
}
