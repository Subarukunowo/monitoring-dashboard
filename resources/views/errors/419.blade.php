@extends('layouts.sidebar')

@section('title', '419 - Sesi Berakhir')
@section('page_title', 'Sesi Berakhir')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen flex flex-col items-center justify-center">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-lg w-full overflow-hidden">

        <div class="bg-orange-500 px-6 py-3 flex items-center gap-2">
            <i class="fa-regular fa-clock text-orange-100 text-sm"></i>
            <span class="text-white text-sm font-semibold tracking-wide">Error 419 — Sesi Berakhir</span>
        </div>

        <div class="p-8 flex flex-col items-center text-center">

            <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-hourglass-end text-orange-500 text-3xl"></i>
            </div>

            <h2 class="text-lg font-bold text-gray-800 mb-2">Sesi Anda Telah Berakhir</h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                Halaman ini sudah kadaluarsa karena <strong class="text-gray-700">sesi tidak aktif terlalu lama</strong>
                atau token CSRF tidak valid.<br>
                Silakan muat ulang halaman dan coba lagi.
            </p>

            <div class="flex gap-3">
                <a href="javascript:location.reload()"
                   class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                    Muat Ulang Halaman
                </a>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Ke Dashboard
                </a>
            </div>

        </div>
    </div>

    <p class="text-xs text-gray-400 mt-5">
        Masalah berlanjut? Coba <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form-419').submit();"
            class="text-orange-500 hover:underline">logout</a> lalu login kembali.
    </p>

    <form id="logout-form-419" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

</div>
@endsection