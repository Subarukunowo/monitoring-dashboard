<?php

namespace App\Http\Middleware;

use App\Models\ContactMessage;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'user',
                    'is_admin' => ($user->role ?? 'user') === 'admin',
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'navMeta' => $user ? [
                'overdueCount' => Laporan::whereHas('status', fn ($q) => $q->where('status_kerja', '!=', 'completed'))
                    ->whereNotNull('tanggal_selesai')
                    ->whereDate('tanggal_selesai', '<', now()->toDateString())
                    ->count(),
                'readyToCloseCount' => Laporan::where('is_closed', false)
                    ->whereHas('status', fn ($q) => $q->where('status_kerja', 'completed'))
                    ->count(),
                'unreadMessagesCount' => ($user->role ?? 'user') === 'admin'
                    ? ContactMessage::where('status', 'baru')->count()
                    : 0,
            ] : null,
            'currentPath' => '/'.ltrim($request->path(), '/'),
            'todayLabel' => now()->locale('id')->isoFormat('dddd, D MMMM Y'),
        ];
    }
}
