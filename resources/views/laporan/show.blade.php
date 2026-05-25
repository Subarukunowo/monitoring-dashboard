@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('laporan.index') }}" class="text-gray-500 hover:text-blue-600 transition">
                        <i class="fa-solid fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Detail Laporan</h1>
                        <p class="text-sm text-gray-500">No. Insiden: <span class="font-mono font-semibold text-blue-700">{{ $laporan->no_insiden }}</span></p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('laporan.edit', $laporan) }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition flex items-center">
                        <i class="fa-solid fa-pen-to-square mr-2"></i>Edit
                    </a>
                    <form action="{{ route('laporan.destroy', $laporan) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus laporan ini?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition flex items-center">
                            <i class="fa-solid fa-trash mr-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-6 py-6">

        @php
            $statusBadge = [
                'open'        => ['bg' => 'bg-blue-100',   'text' => 'text-blue-800',   'label' => 'Open'],
                'on_progress' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Sedang Dikerjakan'],
                'completed'   => ['bg' => 'bg-green-100',  'text' => 'text-green-800',  'label' => 'Selesai'],
            ][$laporan->status->status_kerja] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Unknown'];

            $isOverdue = $laporan->tanggal_selesai
                && $laporan->status->status_kerja !== 'completed'
                && \Carbon\Carbon::parse($laporan->tanggal_selesai)->isPast();

            $fotoPekerjaan    = $laporan->dokumen->where('tipe', 'pekerjaan');
            $fotoAdministrasi = $laporan->dokumen->where('tipe', 'administrasi');
            $dokumenList      = $laporan->dokumen->where('tipe', 'dokumen');
        @endphp

        {{-- Status + Tanggal --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <span class="px-3 py-1 {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} text-sm font-semibold rounded-full">
                {{ $statusBadge['label'] }}
            </span>
            @if($isOverdue)
                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-full flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> Terlambat
                </span>
            @endif
            <span class="text-sm text-gray-500">
                <i class="fa-regular fa-calendar mr-1"></i>
                Dilaporkan: {{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->isoFormat('D MMMM Y') }}
                @if($laporan->jam_laporan)
                    &bull; {{ \Carbon\Carbon::parse($laporan->jam_laporan)->format('H:i') }} WIB
                @endif
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===== KOLOM KIRI ===== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Lokasi & Penugasan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-location-dot text-blue-600 mr-2"></i>Lokasi & Penugasan
                        </h3>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Area</label>
                            <p class="mt-1 text-gray-800 font-medium">{{ $laporan->area->nama_area }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Pengawas</label>
                            <p class="mt-1 text-gray-800 font-medium">{{ $laporan->pengawas->nama ?? '-' }}</p>
                            @if($laporan->pengawas?->no_telepon)
                                <p class="text-sm text-gray-500 mt-0.5"><i class="fa-solid fa-phone mr-1"></i>{{ $laporan->pengawas->no_telepon }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Jenis Pekerjaan</label>
                            <p class="mt-1 text-gray-800 font-medium">{{ $laporan->jenisPekerjaan->nama_jenis ?? '-' }}</p>
                        </div>
                        @if($laporan->link_maps)
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Link Maps</label>
                            <a href="{{ $laporan->link_maps }}" target="_blank"
                               class="mt-1 flex items-center gap-1.5 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fa-solid fa-map-location-dot"></i>
                                Buka di Maps
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Informasi Pelanggan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-user text-blue-600 mr-2"></i>Informasi Pelanggan
                        </h3>
                    </div>
                    <div class="p-5">
                        @if($laporan->pelanggan)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-medium text-gray-500 uppercase">Nama</label>
                                    <p class="mt-1 text-gray-800 font-medium">{{ $laporan->pelanggan->nama }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500 uppercase">No. Pelanggan</label>
                                    <p class="mt-1 text-gray-800 font-mono">{{ $laporan->pelanggan->no_pelanggan ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500 uppercase">Telepon</label>
                                    <p class="mt-1 text-gray-800">{{ $laporan->pelanggan->no_telepon ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500 uppercase">Status</label>
                                    <p class="mt-1">
                                        @if($laporan->pelanggan->status_pelanggan)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fa-solid fa-check mr-1"></i>Pelanggan Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                Non-Pelanggan
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-xs font-medium text-gray-500 uppercase">Alamat</label>
                                    <p class="mt-1 text-gray-700">{{ $laporan->pelanggan->alamat ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 italic">Tidak ada data pelanggan (Laporan Umum)</p>
                        @endif
                    </div>
                </div>

                {{-- Detail Pekerjaan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-clipboard-list text-blue-600 mr-2"></i>Detail Pekerjaan
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Tanggal Survei</label>
                                <p class="mt-1 text-gray-800">
                                    {{ $laporan->tanggal_survei ? \Carbon\Carbon::parse($laporan->tanggal_survei)->isoFormat('D MMMM Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</label>
                                <p class="mt-1 {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                    {{ $laporan->tanggal_selesai ? \Carbon\Carbon::parse($laporan->tanggal_selesai)->isoFormat('D MMMM Y') : '-' }}
                                    @if($isOverdue)
                                        <span class="text-xs block text-red-500">Melewati deadline</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Durasi</label>
                                <p class="mt-1 text-gray-800">
                                    @if($laporan->tanggal_survei && $laporan->tanggal_selesai)
                                        {{ \Carbon\Carbon::parse($laporan->tanggal_survei)->diffInDays($laporan->tanggal_selesai) }} hari
                                    @else -
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Keterangan</label>
                            <div class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $laporan->keterangan ?? 'Tidak ada keterangan' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informasi Keuangan --}}
                @if($laporan->nilai_rab || $laporan->no_sap)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-calculator text-blue-600 mr-2"></i>Informasi Keuangan
                        </h3>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($laporan->nilai_rab)
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Nilai RAB</label>
                            <p class="mt-1 text-lg font-bold text-gray-800">Rp {{ number_format($laporan->nilai_rab, 0, ',', '.') }}</p>
                        </div>
                        @endif
                        @if($laporan->no_sap)
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">No. SAP / TUG</label>
                            <p class="mt-1 text-gray-800 font-mono">{{ $laporan->no_sap }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- ===== FOTO PEKERJAAN ===== --}}
                @if($fotoPekerjaan->count())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-camera text-blue-600 mr-2"></i>Foto Pekerjaan
                        </h3>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-medium">
                            {{ $fotoPekerjaan->count() }} foto
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($fotoPekerjaan as $index => $foto)
                                <button type="button"
                                    onclick="bukaLightbox({{ $index }}, 'pekerjaan')"
                                    class="relative group focus:outline-none">
                                    <img src="{{ Storage::url($foto->path_file) }}"
                                         alt="{{ $foto->nama_file }}"
                                         class="w-full h-28 object-cover rounded-lg border border-gray-200 group-hover:opacity-90 transition">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/25 rounded-lg transition">
                                        <i class="fa-solid fa-magnifying-glass-plus text-white text-xl opacity-0 group-hover:opacity-100 transition drop-shadow"></i>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-3"><i class="fa-solid fa-circle-info mr-1"></i>Klik foto untuk memperbesar</p>
                    </div>
                </div>
                @endif

                {{-- ===== FOTO ADMINISTRASI ===== --}}
                @if($fotoAdministrasi->count())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-file-image text-blue-600 mr-2"></i>Foto Administrasi
                        </h3>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-medium">
                            {{ $fotoAdministrasi->count() }} foto
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($fotoAdministrasi as $index => $foto)
                                <button type="button"
                                    onclick="bukaLightbox({{ $index }}, 'administrasi')"
                                    class="relative group focus:outline-none">
                                    <img src="{{ Storage::url($foto->path_file) }}"
                                         alt="{{ $foto->nama_file }}"
                                         class="w-full h-28 object-cover rounded-lg border border-gray-200 group-hover:opacity-90 transition">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/25 rounded-lg transition">
                                        <i class="fa-solid fa-magnifying-glass-plus text-white text-xl opacity-0 group-hover:opacity-100 transition drop-shadow"></i>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-3"><i class="fa-solid fa-circle-info mr-1"></i>Klik foto untuk memperbesar</p>
                    </div>
                </div>
                @endif

            </div>

            {{-- ===== KOLOM KANAN ===== --}}
            <div class="space-y-6">

                {{-- Dokumen Pendukung --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-paperclip text-blue-600 mr-2"></i>Dokumen Pendukung
                        </h3>
                        @if($dokumenList->count())
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-medium">{{ $dokumenList->count() }}</span>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($dokumenList->count())
                            <div class="space-y-2">
                                @foreach($dokumenList as $doc)
                                <a href="{{ Storage::url($doc->path_file) }}" target="_blank"
                                   class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition group">
                                    <div class="shrink-0">
                                        @if($doc->mime_type === 'application/pdf')
                                            <i class="fa-regular fa-file-pdf text-red-500 text-xl"></i>
                                        @elseif(str_contains($doc->mime_type ?? '', 'word') || str_contains($doc->mime_type ?? '', 'document'))
                                            <i class="fa-regular fa-file-word text-blue-500 text-xl"></i>
                                        @elseif(str_contains($doc->mime_type ?? '', 'excel') || str_contains($doc->mime_type ?? '', 'spreadsheet'))
                                            <i class="fa-regular fa-file-excel text-green-500 text-xl"></i>
                                        @else
                                            <i class="fa-regular fa-file text-gray-500 text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate group-hover:text-blue-700">{{ $doc->nama_file }}</p>
                                        @if($doc->ukuran_file)
                                            <p class="text-xs text-gray-400">{{ number_format($doc->ukuran_file / 1024, 1) }} KB</p>
                                        @endif
                                    </div>
                                    <i class="fa-solid fa-download text-gray-400 group-hover:text-blue-500 shrink-0"></i>
                                </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 text-sm text-center py-4">Belum ada dokumen terlampir</p>
                        @endif
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-clock text-blue-600 mr-2"></i>Metadata
                        </h3>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-800">{{ $laporan->created_at?->isoFormat('D MMM Y, HH:mm') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Terakhir Diperbarui</span>
                            <span class="text-gray-800">{{ $laporan->updated_at?->isoFormat('D MMM Y, HH:mm') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ID Laporan</span>
                            <span class="text-gray-800 font-mono">#{{ $laporan->id }}</span>
                        </div>
                    </div>
                </div>

                {{-- Aksi Cepat --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800">Aksi Cepat</h3>
                    </div>
                    <div class="p-5 space-y-2">
                        <a href="{{ route('laporan.edit', $laporan) }}"
                           class="flex w-full items-center px-4 py-2.5 text-sm text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square mr-2"></i>Edit Laporan
                        </a>
                        <button onclick="window.print()"
                                class="flex w-full items-center px-4 py-2.5 text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <i class="fa-solid fa-print mr-2"></i>Cetak / PDF
                        </button>
                        <a href="{{ route('laporan.index') }}"
                           class="flex w-full items-center px-4 py-2.5 text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                            <i class="fa-solid fa-list mr-2"></i>Kembali ke Daftar
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ===== LIGHTBOX ===== --}}
<div id="lightbox"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/85"
     onclick="if(event.target===this) tutupLightbox()">

    {{-- Kontrol atas --}}
    <div class="absolute top-4 left-0 right-0 flex justify-between items-center px-6 z-10">
        <span id="lb-caption" class="text-white text-sm opacity-80 truncate max-w-xs"></span>
        <div class="flex items-center gap-3">
            <a id="lb-download" href="#" download
               class="text-white hover:text-blue-300 transition text-lg" title="Unduh">
                <i class="fa-solid fa-download"></i>
            </a>
            <button onclick="tutupLightbox()" class="text-white hover:text-gray-300 transition text-2xl" title="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    {{-- Tombol prev --}}
    <button id="lb-prev"
            onclick="geserLightbox(-1)"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center
                   bg-white/10 hover:bg-white/25 text-white rounded-full transition text-xl">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    {{-- Gambar --}}
    <img id="lb-img" src="" alt=""
         class="max-w-[90vw] max-h-[85vh] object-contain rounded-lg shadow-2xl select-none">

    {{-- Tombol next --}}
    <button id="lb-next"
            onclick="geserLightbox(1)"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center
                   bg-white/10 hover:bg-white/25 text-white rounded-full transition text-xl">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    {{-- Counter --}}
    <div id="lb-counter"
         class="absolute bottom-5 left-1/2 -translate-x-1/2 text-white text-sm opacity-60"></div>
</div>

@php
    // Kirim data foto ke JS sebagai JSON
    $fotosPekerjaan = $fotoPekerjaan->map(fn($f) => [
        'src'      => Storage::url($f->path_file),
        'caption'  => $f->nama_file,
        'download' => Storage::url($f->path_file),
    ])->values();

    $fotosAdministrasi = $fotoAdministrasi->map(fn($f) => [
        'src'      => Storage::url($f->path_file),
        'caption'  => $f->nama_file,
        'download' => Storage::url($f->path_file),
    ])->values();
@endphp

<script>
    const galeri = {
        pekerjaan:    @json($fotosPekerjaan),
        administrasi: @json($fotosAdministrasi),
    };

    let lbTipe   = null;
    let lbIndex  = 0;

    function bukaLightbox(index, tipe) {
        lbTipe  = tipe;
        lbIndex = index;
        renderLightbox();
        const lb = document.getElementById('lightbox');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function tutupLightbox() {
        const lb = document.getElementById('lightbox');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function geserLightbox(arah) {
        const arr = galeri[lbTipe];
        lbIndex   = (lbIndex + arah + arr.length) % arr.length;
        renderLightbox();
    }

    function renderLightbox() {
        const arr  = galeri[lbTipe];
        const item = arr[lbIndex];
        document.getElementById('lb-img').src          = item.src;
        document.getElementById('lb-caption').textContent = item.caption;
        document.getElementById('lb-download').href    = item.download;
        document.getElementById('lb-counter').textContent = (lbIndex + 1) + ' / ' + arr.length;

        // Sembunyikan prev/next jika hanya 1 foto
        const showNav = arr.length > 1;
        document.getElementById('lb-prev').style.display = showNav ? '' : 'none';
        document.getElementById('lb-next').style.display = showNav ? '' : 'none';
    }

    // Keyboard: ESC tutup, ← → navigasi
    document.addEventListener('keydown', function (e) {
        const lb = document.getElementById('lightbox');
        if (lb.classList.contains('hidden')) return;
        if (e.key === 'Escape')      tutupLightbox();
        if (e.key === 'ArrowLeft')   geserLightbox(-1);
        if (e.key === 'ArrowRight')  geserLightbox(1);
    });
</script>
@endsection