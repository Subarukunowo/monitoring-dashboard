@extends('layouts.sidebar')

@section('title', '503 - Layanan Tidak Tersedia')
@section('page_title', 'Maintenance')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen flex flex-col items-center justify-center">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-lg w-full overflow-hidden">

        <div class="bg-blue-600 px-6 py-3 flex items-center gap-2">
            <i class="fa-solid fa-screwdriver-wrench text-blue-200 text-sm"></i>
            <span class="text-white text-sm font-semibold tracking-wide">Error 503 — Sedang Maintenance</span>
        </div>

        <div class="p-8 flex flex-col items-center text-center">

            <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-gear text-blue-500 text-3xl animate-spin" style="animation-duration: 3s;"></i>
            </div>

            <h2 class="text-lg font-bold text-gray-800 mb-2">Sistem Sedang Maintenance</h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                MaintainNow sedang dalam proses pemeliharaan terjadwal untuk
                <strong class="text-gray-700">meningkatkan performa dan keamanan</strong> sistem.<br>
                Kami akan segera kembali online.
            </p>

            {{-- Progress --}}
            <div class="w-full mb-6">
                <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                    <span>Status Maintenance</span>
                    <span class="font-medium text-blue-600">Sedang berlangsung...</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full animate-pulse" style="width: 60%"></div>
                </div>
            </div>

            {{-- Retry countdown --}}
            <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 mb-6 w-full text-sm text-blue-700">
                <i class="fa-regular fa-clock mr-2"></i>
                Halaman akan otomatis di-refresh dalam
                <strong id="countdown">30</strong> detik.
            </div>

            <a href="javascript:location.reload()"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                <i class="fa-solid fa-rotate-right text-xs"></i>
                Cek Sekarang
            </a>

        </div>
    </div>

    <p class="text-xs text-gray-400 mt-5">
        Informasi lebih lanjut? Hubungi <span class="text-blue-500">administrator</span>.
    </p>

</div>

@push('scripts')
<script>
    let secs = 30;
    const el = document.getElementById('countdown');
    const timer = setInterval(() => {
        secs--;
        if (el) el.textContent = secs;
        if (secs <= 0) { clearInterval(timer); location.reload(); }
    }, 1000);
</script>
@endpush
@endsection