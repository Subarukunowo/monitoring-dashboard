@extends('layouts.sidebar')

@section('title', '403 - Akses Ditolak')
@section('page_title', 'Akses Ditolak')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen flex flex-col items-center justify-center">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-lg w-full overflow-hidden">

        <div class="bg-red-600 px-6 py-3 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-red-200 text-sm"></i>
            <span class="text-white text-sm font-semibold tracking-wide">Error 403 — Akses Ditolak</span>
        </div>

        <div class="p-8 flex flex-col items-center text-center">

            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-6">
                <i class="fa-solid fa-lock text-red-500 text-3xl"></i>
            </div>

            <h2 class="text-lg font-bold text-gray-800 mb-2">Anda Tidak Punya Izin</h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                Halaman atau fitur ini hanya dapat diakses oleh pengguna dengan
                <strong class="text-gray-700">hak akses tertentu</strong>.<br>
                Hubungi administrator jika Anda merasa ini adalah kesalahan.
            </p>

            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Kembali ke Dashboard
                </a>
                <a href="javascript:history.back()"
                   class="inline-flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Halaman Sebelumnya
                </a>
            </div>

        </div>
    </div>

    <p class="text-xs text-gray-400 mt-5">
        Butuh akses? Hubungi <span class="text-red-500">administrator</span>.
    </p>

</div>
@endsection