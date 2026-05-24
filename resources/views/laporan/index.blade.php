@extends('layouts.sidebar')

@section('title', 'Daftar Laporan')
@section('page_title', 'Laporan')

@section('content')
<div class="p-6 bg-gray-50 min-h-full">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Laporan</h2>
            <p class="text-sm text-gray-500 mt-0.5">Total {{ $laporans->total() }} laporan tercatat</p>
        </div>
        <a href="{{ route('laporan.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
            <i class="fa-solid fa-plus"></i>
            Buat Laporan Baru
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
            <i class="fa-solid fa-circle-check text-green-500"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Grid Cards --}}
    @if($laporans->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($laporans as $laporan)
                @php $badge = $laporan->status_badge; @endphp
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col">

                    {{-- Status + Tanggal --}}
                    <div class="flex items-center justify-between px-4 pt-4 pb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                        <span class="text-xs text-gray-400">
                            <i class="fa-regular fa-calendar mr-1"></i>
                            {{ $laporan->tanggal_laporan?->format('d M Y') ?? '—' }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="px-4 py-3 flex-1">
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-0.5">No. Insiden</p>
                        <p class="text-base font-bold text-gray-800 mb-3">{{ $laporan->no_insiden }}</p>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fa-regular fa-user w-4 text-gray-400 shrink-0"></i>
                            <span class="truncate">
                                {{ $laporan->pelanggan?->nama ?? 'Umum / Non-Pelanggan' }}
                            </span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-4 pb-4 pt-2 border-t border-gray-100 mt-auto">
                        <a href="{{ route('laporan.show', $laporan) }}"
                            class="flex items-center justify-center gap-2 w-full py-2 rounded-xl text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                            <i class="fa-regular fa-eye text-lg"></i>
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $laporans->links() }}
        </div>

    @else
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                <i class="fa-regular fa-folder-open text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-gray-700 font-semibold text-lg mb-1">Belum ada laporan</h3>
            <p class="text-gray-400 text-sm mb-6">Mulai dengan membuat laporan insiden pertama.</p>
            <a href="{{ route('laporan.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
                <i class="fa-solid fa-plus"></i>
                Buat Laporan Baru
            </a>
        </div>
    @endif

</div>
@endsection