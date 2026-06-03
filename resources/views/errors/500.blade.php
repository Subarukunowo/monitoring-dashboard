@extends('layouts.sidebar')

@section('title', '500 - Kesalahan Server')
@section('page_title', 'Kesalahan Server')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen flex flex-col items-center justify-center">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-lg w-full overflow-hidden">

        <div class="bg-gray-800 px-6 py-3 flex items-center gap-2">
            <i class="fa-solid fa-bug text-gray-400 text-sm"></i>
            <span class="text-white text-sm font-semibold tracking-wide">Error 500 — Kesalahan Server</span>
        </div>

        <div class="p-8 flex flex-col items-center text-center">

            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-server text-gray-500 text-3xl"></i>
            </div>

            <div class="text-6xl font-black text-gray-300 mb-2 leading-none">500</div>

            <h2 class="text-lg font-bold text-gray-800 mb-2">Terjadi Kesalahan pada Server</h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
                Server mengalami masalah internal dan tidak dapat memproses permintaan Anda saat ini.
                <strong class="text-gray-700">Tim teknis sudah diberitahu</strong> dan sedang menangani masalah ini.
            </p>

            {{-- Error detail (hanya tampil di mode debug) --}}
            @if(config('app.debug') && isset($exception))
                <div class="w-full bg-gray-900 rounded-lg p-4 mb-6 text-left overflow-x-auto">
                    <p class="text-red-400 text-xs font-mono mb-1">{{ get_class($exception) }}</p>
                    <p class="text-gray-300 text-xs font-mono">{{ $exception->getMessage() }}</p>
                    <p class="text-gray-500 text-xs font-mono mt-1">
                        {{ $exception->getFile() }}:{{ $exception->getLine() }}
                    </p>
                </div>
            @endif

            <div class="flex gap-3">
                <a href="javascript:location.reload()"
                   class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                    Coba Lagi
                </a>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    <i class="fa-solid fa-house text-xs"></i>
                    Ke Dashboard
                </a>
            </div>

        </div>
    </div>

    <p class="text-xs text-gray-400 mt-5">
        Jika masalah berlanjut, hubungi <span class="text-gray-600">administrator sistem</span>.
    </p>

</div>
@endsection