@extends('layouts.sidebar')

@section('title', '404 - Halaman Tidak Ditemukan')
@section('page_title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen flex flex-col items-center justify-center">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-lg w-full overflow-hidden">

        <div class="bg-yellow-500 px-6 py-3 flex items-center gap-2">
            <i class="fa-solid fa-circle-question text-yellow-100 text-sm"></i>
            <span class="text-white text-sm font-semibold tracking-wide">Error 404 — Halaman Tidak Ditemukan</span>
        </div>

        <div class="p-8 flex flex-col items-center text-center">

            <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-map-location-dot text-yellow-500 text-3xl"></i>
            </div>

            <div class="text-6xl font-black text-yellow-400 mb-2 leading-none">404</div>

            <h2 class="text-lg font-bold text-gray-800 mb-2">Halaman Tidak Ditemukan</h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                Halaman yang Anda cari tidak ada atau telah dipindahkan.<br>
                Pastikan URL yang dimasukkan <strong class="text-gray-700">sudah benar</strong>.
            </p>

            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 mb-6 w-full text-left">
                <span class="text-xs text-gray-400 block mb-0.5">URL yang diminta:</span>
                <code class="text-xs text-red-500 break-all">{{ request()->url() }}</code>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <i class="fa-solid fa-house text-xs"></i>
                    Ke Dashboard
                </a>
                <a href="javascript:history.back()"
                   class="inline-flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Kembali
                </a>
            </div>

        </div>
    </div>

</div>
@endsection