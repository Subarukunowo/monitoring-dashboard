<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MaintainNow')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        mark { background: #fef08a; padding: 0 2px; border-radius: 2px; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">
<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 flex flex-col">

        {{-- Logo --}}
        <div class="p-6 flex items-center border-b border-gray-100">
            <div class="text-2xl font-bold text-gray-800">
                <i class="fa-solid fa-gear text-red-500 mr-2"></i>MaintainNow
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4">

            {{-- Overview --}}
            <div class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Overview</div>
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-3 transition
                    {{ request()->routeIs('dashboard') 
                        ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600 font-medium' 
                        : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 border-l-4 border-transparent' }}">
                <i class="fa-solid fa-border-all w-6"></i> Overview
            </a>

            {{-- Work Management --}}
            <div class="px-4 mt-6 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Work Management</div>

            <a href="{{ route('laporan.index') }}"
                class="flex items-center px-4 py-2.5 transition
                    {{ request()->routeIs('laporan.*') 
                        ? 'bg-blue-50 text-blue-600 border-l-4 border-blue-600 font-medium' 
                        : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 border-l-4 border-transparent' }}">
                <i class="fa-solid fa-clipboard-list w-6"></i> Laporan
            </a>

            <a href="#"
                class="flex items-center px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-blue-600 border-l-4 border-transparent transition">
                <i class="fa-solid fa-calculator w-6"></i> RAB
            </a>

            <a href="#"
                class="flex items-center px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-blue-600 border-l-4 border-transparent transition">
                <i class="fa-solid fa-file-signature w-6"></i> Tutup Kontrak
            </a>

            {{-- Dokumentasi (accordion) --}}
            <div x-data="{ open: {{ request()->routeIs('dokumentasi.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-blue-600 border-l-4 border-transparent transition">
                    <i class="fa-solid fa-folder w-6"></i> Dokumentasi
                    <i class="fa-solid fa-chevron-down ml-auto text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-transition class="pl-10 text-sm">
                    <a href="#" class="block py-1.5 text-gray-500 hover:text-blue-600">Dokumentasi Pekerjaan</a>
                    <a href="#" class="block py-1.5 text-gray-500 hover:text-blue-600">Dokumentasi Administrasi</a>
                </div>
            </div>

            <a href="#"
                class="flex items-center px-4 py-2.5 text-gray-600 hover:bg-gray-50 hover:text-blue-600 border-l-4 border-transparent transition">
                <i class="fa-solid fa-table-list w-6"></i> Bill of Quantity (BQ)
            </a>

        </nav>

        {{-- Bottom: User info --}}
        <div class="p-4 border-t border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-700 truncate">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>

    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between shrink-0">
            <h1 class="text-xl font-bold text-gray-800">@yield('page_title', 'MaintainNow')</h1>
            <div class="flex items-center gap-3 text-sm text-gray-500">
                <i class="fa-regular fa-calendar"></i>
                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>

    </div>

</div>

{{-- Alpine.js untuk accordion --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>