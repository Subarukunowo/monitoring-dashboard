import * as React from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Building2,
    Calculator,
    Camera,
    CheckCircle2,
    ClipboardList,
    FileCheck2,
    FolderOpen,
    Headset,
    LayoutGrid,
    LogOut,
    MapPin,
    Menu,
    ShieldCheck,
    Table2,
    UserCheck,
    Users,
    Wrench,
    X,
} from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import type { PageProps } from '@/types';

interface NavLinkItem {
    label: string;
    href: string;
    matchPrefix: string;
    icon: React.ComponentType<{ className?: string }>;
    badgeCount?: number;
}

export function MaintainNowLogo() {
    return (
        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-[6px] border border-[#e3e6ec] bg-white p-1">
            <img
                src="/assets/images/logo-pln.svg"
                alt="Logo PLN"
                className="h-full w-full object-contain"
            />
        </div>
    );
}

export default function AppLayout({
    title,
    subtitle,
    actions,
    children,
}: {
    title: string;
    subtitle?: string;
    actions?: React.ReactNode;
    children: React.ReactNode;
}) {
    const { props } = usePage<PageProps>();
    const user = props.auth?.user;
    const isAdmin = Boolean(user?.is_admin);
    const navMeta = props.navMeta;
    const currentPath = props.currentPath || '/';
    const [mobileOpen, setMobileOpen] = React.useState(false);

    const isActive = (prefix: string) => {
        if (prefix === '/dashboard') {
            return currentPath === '/' || currentPath === '/dashboard';
        }

        return currentPath === prefix || currentPath.startsWith(prefix + '/');
    };

    const overviewLinks: NavLinkItem[] = [
        {
            label: 'Ringkasan',
            href: '/dashboard',
            matchPrefix: '/dashboard',
            icon: LayoutGrid,
        },
    ];

    const workLinks: NavLinkItem[] = [
        {
            label: 'Laporan Insiden',
            href: '/laporan',
            matchPrefix: '/laporan',
            icon: ClipboardList,
            badgeCount: navMeta?.overdueCount ? navMeta.overdueCount : undefined,
        },
        {
            label: 'RAB',
            href: '/rab',
            matchPrefix: '/rab',
            icon: Calculator,
        },
        {
            label: 'Bill of Quantity',
            href: '/bill-of-quantity',
            matchPrefix: '/bill-of-quantity',
            icon: Table2,
        },
        {
            label: 'Tutup Kontrak',
            href: '/tutup-kontrak',
            matchPrefix: '/tutup-kontrak',
            icon: FileCheck2,
            badgeCount: navMeta?.readyToCloseCount ? navMeta.readyToCloseCount : undefined,
        },
        {
            label: 'Dok. Pekerjaan',
            href: '/dokumentasi-pekerjaan',
            matchPrefix: '/dokumentasi-pekerjaan',
            icon: Camera,
        },
        {
            label: 'Dok. Administrasi',
            href: '/dokumentasi-administrasi',
            matchPrefix: '/dokumentasi-administrasi',
            icon: FolderOpen,
        },
    ];

    const adminMasterLinks: NavLinkItem[] = [
        { label: 'Area Distribusi', href: '/areas', matchPrefix: '/areas', icon: MapPin },
        { label: 'Pelanggan', href: '/pelanggans', matchPrefix: '/pelanggans', icon: Users },
        { label: 'Pengawas', href: '/pengawas', matchPrefix: '/pengawas', icon: UserCheck },
        { label: 'Jenis Pekerjaan', href: '/jenis_pekerjaan', matchPrefix: '/jenis_pekerjaan', icon: Wrench },
        { label: 'Status Kerja', href: '/status', matchPrefix: '/status', icon: Building2 },
    ];

    const supportLinks: NavLinkItem[] = [
        {
            label: 'Hubungi Developer',
            href: '/contact',
            matchPrefix: '/contact',
            icon: Headset,
            badgeCount: isAdmin && navMeta?.unreadMessagesCount ? navMeta.unreadMessagesCount : undefined,
        },
    ];

    const handleLogout = () => {
        router.post('/logout');
    };

    const renderNavSection = (label: string, items: NavLinkItem[]) => (
        <div className="mt-5 first:mt-2">
            <div className="px-2.5 pb-1.5 text-xs font-medium text-[#667085]">{label}</div>
            <div className="space-y-0.5">
                {items.map((item) => {
                    const active = isActive(item.matchPrefix);
                    const Icon = item.icon;

                    return (
                        <Link
                            key={item.href}
                            href={item.href}
                            onClick={() => setMobileOpen(false)}
                            aria-current={active ? 'page' : undefined}
                            className={`flex h-8 items-center gap-2.5 rounded-[6px] px-2.5 text-sm transition-colors ${
                                active
                                    ? 'bg-[#e3e7ee] font-medium text-[#0a0a0a]'
                                    : 'font-normal text-[#4b5563] hover:bg-[#e9ecf1] hover:text-[#0a0a0a]'
                            }`}
                        >
                            <Icon className={`h-4 w-4 shrink-0 ${active ? 'text-[#0a0a0a]' : 'text-[#5b616e]'}`} />
                            <span className="truncate">{item.label}</span>
                            {item.badgeCount !== undefined && item.badgeCount > 0 && (
                                <span className="ml-auto rounded-full bg-[#e2e6ed] px-1.5 py-0.5 text-[11px] font-medium text-[#0a0a0a] tabular-nums leading-none">
                                    {item.badgeCount}
                                </span>
                            )}
                        </Link>
                    );
                })}
            </div>
        </div>
    );

    const sidebarContent = (
        <div className="flex h-full flex-col px-3 py-4">
            <div className="flex items-center justify-between px-2 pb-3 border-b border-[#e3e6ec]">
                <Link href="/dashboard" className="flex items-center gap-2.5 min-w-0">
                    <MaintainNowLogo />
                    <div className="min-w-0">
                        <div className="text-sm font-semibold tracking-tight text-[#0a0a0a] truncate">
                            MaintainNow
                        </div>
                        <div className="text-[11px] text-[#5b616e] truncate">
                            PLN Distribusi Batam
                        </div>
                    </div>
                </Link>
                <button
                    type="button"
                    onClick={() => setMobileOpen(false)}
                    className="rounded-[6px] p-1 text-[#5b616e] hover:bg-[#e9ecf1] lg:hidden"
                    aria-label="Tutup menu"
                >
                    <X className="h-4 w-4" />
                </button>
            </div>

            <nav className="flex-1 overflow-y-auto py-2" aria-label="Navigasi utama">
                {renderNavSection('Operasional', overviewLinks)}
                {renderNavSection('Manajemen Pekerjaan', workLinks)}
                {isAdmin && renderNavSection('Master Data (Admin)', adminMasterLinks)}
                {renderNavSection('Dukungan', supportLinks)}
            </nav>

            {user && (
                <div className="mt-auto pt-3 border-t border-[#e3e6ec]">
                    <div className="flex items-center gap-2.5 rounded-[6px] px-2 py-2">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#141414] text-xs font-semibold text-white">
                            {user.name.slice(0, 2).toUpperCase()}
                        </div>
                        <div className="min-w-0 flex-1">
                            <div className="flex items-center gap-1.5">
                                <span className="truncate text-sm font-medium text-[#0a0a0a]">
                                    {user.name}
                                </span>
                                <Badge variant={isAdmin ? 'info' : 'secondary'} className="px-1.5 py-0 text-[11px]">
                                    {isAdmin ? 'Admin' : 'Petugas'}
                                </Badge>
                            </div>
                            <p className="truncate text-xs text-[#5b616e]">{user.email}</p>
                        </div>
                        <button
                            type="button"
                            onClick={handleLogout}
                            title="Keluar"
                            aria-label="Keluar dari akun"
                            className="rounded-[6px] p-1.5 text-[#5b616e] hover:bg-[#e9ecf1] hover:text-[#c4242a] transition-colors cursor-pointer"
                        >
                            <LogOut className="h-4 w-4" />
                        </button>
                    </div>
                    <p className="mt-1 px-2 text-[11px] leading-snug text-[#667085]">
                        Data simulasi (dummy), bukan kegiatan resmi instansi pemerintah.
                    </p>
                </div>
            )}
        </div>
    );

    return (
        <div className="min-h-screen bg-[#f2f4f7] text-[#0a0a0a] lg:grid lg:grid-cols-[248px_minmax(0,1fr)]">
            {/* Desktop Sidebar (Open Plane) */}
            <aside className="hidden lg:sticky lg:top-0 lg:block lg:h-screen lg:border-r lg:border-[#e3e6ec] lg:bg-[#f2f4f7]">
                {sidebarContent}
            </aside>

            {/* Mobile Top Bar & Sheet */}
            <div className="sticky top-0 z-30 flex h-12 items-center justify-between border-b border-[#e3e6ec] bg-white px-4 lg:hidden">
                <button
                    type="button"
                    onClick={() => setMobileOpen(true)}
                    className="inline-flex h-8 w-8 items-center justify-center rounded-[6px] border border-[#d0d5dd] bg-white text-[#0a0a0a]"
                    aria-label="Buka navigasi"
                >
                    <Menu className="h-4 w-4" />
                </button>
                <span className="text-sm font-semibold text-[#0a0a0a] truncate">{title}</span>
                {user ? (
                    <Badge variant={isAdmin ? 'info' : 'secondary'} className="text-[11px]">
                        {isAdmin ? 'Admin' : 'Petugas'}
                    </Badge>
                ) : (
                    <span className="w-8" />
                )}
            </div>

            {mobileOpen && (
                <div className="fixed inset-0 z-50 lg:hidden">
                    <div
                        className="fixed inset-0 bg-black/40"
                        onClick={() => setMobileOpen(false)}
                    />
                    <aside className="fixed inset-y-0 left-0 w-72 border-r border-[#e3e6ec] bg-[#f2f4f7]">
                        {sidebarContent}
                    </aside>
                </div>
            )}

            {/* Main Canvas */}
            <main className="min-w-0 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div className="mx-auto max-w-[1240px]">
                    {/* Title row */}
                    <div className="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h1 className="text-2xl font-semibold tracking-[-0.02em] text-[#0a0a0a]">
                                {title}
                            </h1>
                            <p className="mt-1 text-sm text-[#5b616e]">
                                {subtitle || props.todayLabel}
                                {user && (
                                    <span className="ml-2 inline-flex items-center gap-1 text-xs text-[#667085]">
                                        ·{' '}
                                        {isAdmin ? (
                                            <>
                                                <ShieldCheck className="h-3.5 w-3.5 text-[#1d5fbf]" /> Mode Administrator
                                            </>
                                        ) : (
                                            'Mode Petugas Lapangan'
                                        )}
                                    </span>
                                )}
                            </p>
                        </div>
                        {actions && <div className="flex flex-wrap items-center gap-2">{actions}</div>}
                    </div>

                    {/* Flash Alerts */}
                    {props.flash?.success && (
                        <Alert variant="positive" className="mb-5">
                            <CheckCircle2 className="h-4 w-4 mt-0.5 shrink-0 text-[#15803d]" />
                            <AlertDescription>{props.flash.success}</AlertDescription>
                        </Alert>
                    )}

                    {props.flash?.error && (
                        <Alert variant="destructive" className="mb-5">
                            <AlertDescription>{props.flash.error}</AlertDescription>
                        </Alert>
                    )}

                    {children}
                </div>
            </main>
        </div>
    );
}
