@extends('layouts.sidebar')

@section('title', ($pageTitle ?? 'Segera Hadir') . ' - MaintainNow')
@section('page_title', $pageTitle ?? 'Segera Hadir')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center text-center px-6">

    <img
        src="{{ asset('assets/images/under-construction.png') }}"
        alt="Fitur dalam pengembangan"
        class="w-72 h-auto mb-8"
    >

    <h1 class="text-2xl font-bold text-gray-800 mb-3">
        Fitur ini sedang dalam pengembangan
    </h1>
    <p class="text-gray-400 text-sm max-w-sm">
        <strong class="text-gray-600">{{ $pageTitle ?? 'Halaman ini' }}</strong>
        belum tersedia saat ini. Silakan kembali ke dashboard atau coba fitur lainnya.
    </p>

    <a href="{{ route('dashboard') }}"
       class="mt-8 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-3 rounded-lg transition">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        Kembali ke Dashboard
    </a>

</div>
@endsection