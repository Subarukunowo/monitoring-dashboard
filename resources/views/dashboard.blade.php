@extends('layouts.sidebar')

@section('title', 'Dashboard - Monitoring')
@section('page_title', 'Overview')

@section('content')
<div class="p-8">

    {{-- Date --}}
    <div class="text-gray-500 mb-4 text-sm">
        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
    </div>

    {{-- 🔍 SEARCH FORM --}}
    <form action="{{ route('dashboard') }}" method="GET" class="mb-6 flex gap-2 max-w-2xl">
        <div class="relative flex-1">
            <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari: No. Insiden, Area, Keterangan, SAP, Pelanggan..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
            Cari
        </button>
        @if(request('search'))
            <a href="{{ route('dashboard') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition" title="Reset">
                <i class="fa-solid fa-xmark"></i>
            </a>
        @endif
    </form>

    @if(request('search'))
        <p class="text-sm text-gray-500 mb-6">
            Menampilkan <span class="font-medium text-gray-700">{{ $searchCount ?? 0 }} hasil</span>
            untuk "<strong>{{ e(request('search')) }}</strong>"
        </p>
    @endif

    {{-- 📊 SECTION 1: Status Overview (CLICKABLE) --}}
    <div class="mb-8">
        <h2 class="text-sm font-semibold text-gray-600 mb-4">
            <i class="fa-solid fa-clipboard-list mr-2"></i>Status Pekerjaan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $statusData = $statusData ?? ['pending' => 0, 'in_progress' => 0, 'on_hold' => 0, 'completed' => 0];
            @endphp
            
            {{-- Pending --}}
            <a href="{{ route('dashboard', array_merge(request()->except('status'), ['status' => 'pending'])) }}"
               class="bg-blue-50 p-4 rounded-lg border border-blue-100 hover:border-blue-300 hover:shadow-md transition block">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-blue-600 font-medium">🟢 Terbuka (Pending)</span>
                    <i class="fa-regular fa-calendar text-blue-400"></i>
                </div>
                <span class="text-2xl font-bold text-blue-700">{{ $statusData['pending'] ?? 0 }}</span>
                <span class="text-xs text-blue-500 block mt-1">Klik untuk filter</span>
            </a>

            {{-- In Progress --}}
            <a href="{{ route('dashboard', array_merge(request()->except('status'), ['status' => 'on_progress'])) }}"
               class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 hover:border-yellow-300 hover:shadow-md transition block">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-yellow-600 font-medium">🟡 Sedang Dikerjakan</span>
                    <i class="fa-regular fa-clock text-yellow-400"></i>
                </div>
                <span class="text-2xl font-bold text-yellow-700">{{ $statusData['in_progress'] ?? 0 }}</span>
            </a>

            {{-- On Hold --}}
            <a href="{{ route('dashboard', array_merge(request()->except('status'), ['status' => 'on_hold'])) }}"
               class="bg-orange-50 p-4 rounded-lg border border-orange-100 hover:border-orange-300 hover:shadow-md transition block">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-orange-600 font-medium">🟠 Ditunda</span>
                    <i class="fa-solid fa-pause text-orange-400"></i>
                </div>
                <span class="text-2xl font-bold text-orange-700">{{ $statusData['on_hold'] ?? 0 }}</span>
            </a>

            {{-- Completed --}}
            <a href="{{ route('dashboard', array_merge(request()->except('status'), ['status' => 'completed'])) }}"
               class="bg-green-50 p-4 rounded-lg border border-green-100 hover:border-green-300 hover:shadow-md transition block">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-green-600 font-medium">✅ Selesai</span>
                    <i class="fa-solid fa-circle-check text-green-400"></i>
                </div>
                <span class="text-2xl font-bold text-green-700">{{ $statusData['completed'] ?? 0 }}</span>
            </a>
        </div>
    </div>

    {{-- 📋 SECTION 2: Work Orders Stats --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-blue-700">
                <i class="fa-solid fa-list-check mr-2"></i>Statistik Pekerjaan
            </h2>
            <a href="{{ route('laporan.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center transition">
                <i class="fa-solid fa-plus mr-2"></i> Buat Laporan Baru
            </a>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $workOrders = $workOrders ?? [
                    'overdue_open' => 0, 'due_today_open' => 0, 'assigned_to_me' => 0,
                    'created_today' => 0, 'created_by_me' => 0, 'due_this_week' => 0
                ];
            @endphp

            {{-- 🔴 Overdue & Pending --}}
            <a href="{{ route('dashboard', array_merge(request()->except('filter'), ['filter' => 'overdue_open'])) }}"
               class="border border-gray-200 rounded-xl p-5 hover:border-red-300 hover:shadow-md transition block">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-blue-800 font-semibold">🔴 Melebihi Deadline & Terbuka</h3>
                    <i class="fa-solid fa-bell text-red-500"></i>
                </div>
                <div class="text-4xl font-bold text-red-600 mb-2">{{ $workOrders['overdue_open'] ?? 0 }}</div>
                <p class="text-gray-500 text-sm">Pekerjaan yang sudah lewat deadline dan masih terbuka</p>
            </a>

            {{-- 🟡 Due Today --}}
            <a href="{{ route('dashboard', array_merge(request()->except('filter'), ['filter' => 'due_today'])) }}"
               class="border border-gray-200 rounded-xl p-5 hover:border-yellow-300 hover:shadow-md transition block">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-blue-800 font-semibold">🟡 Jatuh Tempo Hari Ini</h3>
                    <i class="fa-regular fa-calendar-check text-yellow-500"></i>
                </div>
                <div class="text-4xl font-bold text-yellow-600 mb-2">{{ $workOrders['due_today_open'] ?? 0 }}</div>
                <p class="text-gray-500 text-sm">Pekerjaan yang harus selesai hari ini</p>
            </a>

            {{-- 🟣 Assigned To Me --}}
            <div class="border border-gray-200 rounded-xl p-5">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-blue-800 font-semibold">🟣 Ditugaskan ke Saya</h3>
                    <i class="fa-solid fa-user-check text-purple-500"></i>
                </div>
                <div class="text-4xl font-bold text-purple-600 mb-2">{{ $workOrders['assigned_to_me'] ?? 0 }}</div>
                <p class="text-gray-500 text-sm">Pekerjaan yang ditugaskan khusus kepada Anda</p>
            </div>

            {{-- 🟢 Created Today --}}
            <div class="border border-gray-200 rounded-xl p-5">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-blue-800 font-semibold">🟢 Dibuat Hari Ini</h3>
                    <i class="fa-solid fa-circle-plus text-teal-500"></i>
                </div>
                <div class="text-4xl font-bold text-teal-600 mb-2">{{ $workOrders['created_today'] ?? 0 }}</div>
                <p class="text-gray-500 text-sm">Laporan baru yang dibuat dalam 24 jam terakhir</p>
            </div>

            {{-- 🔵 Created by Me --}}
            <div class="border border-gray-200 rounded-xl p-5">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-blue-800 font-semibold">🔵 Dibuat oleh Saya</h3>
                    <i class="fa-solid fa-user-pen text-indigo-500"></i>
                </div>
                <div class="text-4xl font-bold text-indigo-600 mb-2">{{ $workOrders['created_by_me'] ?? 0 }}</div>
                <p class="text-gray-500 text-sm">Laporan yang Anda buat</p>
            </div>

            {{-- 🟦 Due This Week --}}
            <div class="border border-gray-200 rounded-xl p-5">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-blue-800 font-semibold">🟦 Jatuh Tempo Minggu Ini</h3>
                    <i class="fa-regular fa-calendar-days text-cyan-500"></i>
                </div>
                <div class="text-4xl font-bold text-cyan-600 mb-2">{{ $workOrders['due_this_week'] ?? 0 }}</div>
                <p class="text-gray-500 text-sm">Pekerjaan yang deadline-nya dalam 7 hari ke depan</p>
            </div>
        </div>
    </div>

    {{-- 📑 SECTION 3: Recent Activity --}}
    @php
        $recentReports = $recentReports ?? collect();
    @endphp

    @if($recentReports->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">
                    <i class="fa-solid fa-clock-rotate-left mr-2"></i>Aktivitas Terbaru
                </h2>
                @if(request('search') || request('status') || request('filter'))
                    <span class="text-sm text-gray-500">
                        {{ $recentReports->count() }} hasil
                        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline ml-2">[Reset]</a>
                    </span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Insiden</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Pekerjaan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentReports as $report)
                            @php
                                $statusKey = $report->status->status_kerja ?? 'pending';
                                $statusClass = [
                                    'pending'     => 'bg-blue-100 text-blue-800',
                                    'on_progress' => 'bg-yellow-100 text-yellow-800',
                                    'on_hold'     => 'bg-orange-100 text-orange-800',
                                    'completed'   => 'bg-green-100 text-green-800',
                                ][$statusKey] ?? 'bg-gray-100 text-gray-700';

                                $statusLabel = [
                                    'pending'     => 'Terbuka',
                                    'on_progress' => 'Sedang Dikerjakan',
                                    'on_hold'     => 'Ditunda',
                                    'completed'   => 'Selesai',
                                ][$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));

                                $isOverdue = false;
                                if ($report->tanggal_selesai && $statusKey !== 'completed') {
                                    $isOverdue = \Carbon\Carbon::parse($report->tanggal_selesai)->isPast();
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    @if(request('search'))
                                        {!! preg_replace('/('.preg_quote(request('search'), '/').')/i', '<mark class="bg-yellow-200">$1</mark>', e($report->no_insiden)) !!}
                                    @else
                                        {{ $report->no_insiden }}
                                    @endif
                                    @if($isOverdue)
                                        <span class="ml-2 text-xs text-red-500 font-semibold">⚠ Overdue</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $report->area->nama_area ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $report->jenisPekerjaan->nama_jenis ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm {{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                    {{ $report->tanggal_selesai ? \Carbon\Carbon::parse($report->tanggal_selesai)->locale('id')->isoFormat('D MMMM Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('laporan.show', $report->id) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Lihat Detail">
                                        <i class="fa-regular fa-eye text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="mt-8 text-center py-12 bg-white rounded-lg border border-gray-200">
            <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 mt-3">
                @if(request('search') || request('status') || request('filter'))
                    Tidak ada hasil untuk filter yang dipilih
                @else
                    Belum ada laporan untuk ditampilkan
                @endif
            </p>
            @if(request('search') || request('status') || request('filter'))
                <a href="{{ route('dashboard') }}" class="mt-3 inline-block text-blue-600 hover:underline text-sm">Reset filter</a>
            @endif
        </div>
    @endif

</div>
@endsection