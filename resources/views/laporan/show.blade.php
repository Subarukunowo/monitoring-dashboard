@extends('layouts.app') {{-- Atau gunakan layout utama Anda --}}

@section('content')
<div class="min-h-screen bg-gray-50">
    
    <!-- Header -->
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
                    <a href="{{ route('laporan.edit', $laporan) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition flex items-center">
                        <i class="fa-solid fa-pen-to-square mr-2"></i>Edit
                    </a>
                    <form action="{{ route('laporan.destroy', $laporan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition flex items-center">
                            <i class="fa-solid fa-trash mr-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-6 py-6">
        
        <!-- Status Badge + Tanggal -->
        <div class="flex flex-wrap items-center gap-4 mb-6">
            @php
                $statusBadge = [
                    'pending' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Pending'],
                    'on_progress' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'On Progress'],
                    'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Completed']
                ][$laporan->status->status_kerja] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Unknown'];
            @endphp
            <span class="px-3 py-1 {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} text-sm font-semibold rounded-full">
                {{ $statusBadge['label'] }}
            </span>
            <span class="text-sm text-gray-500">
                <i class="fa-regular fa-calendar mr-1"></i>
                Dilaporkan: {{ \Carbon\Carbon::parse($laporan->tanggal_laporan)->isoFormat('D MMMM Y') }} 
                @if($laporan->jam_laporan) • {{ \Carbon\Carbon::parse($laporan->jam_laporan)->format('H:i') }} WIB @endif
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- KOLOM KIRI: Info Utama -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 📍 Lokasi & Penugasan -->
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
                            <p class="mt-1 text-gray-800 font-medium">
                                {{ $laporan->pengawas->nama ?? '-' }}
                                @if($laporan->pengawas?->no_telepon)
                                <br><span class="text-sm text-gray-500"><i class="fa-solid fa-phone mr-1"></i>{{ $laporan->pengawas->no_telepon }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-medium text-gray-500 uppercase">Jenis Pekerjaan</label>
                            <p class="mt-1 text-gray-800 font-medium">{{ $laporan->jenisPekerjaan->nama_jenis ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- 👤 Informasi Pelanggan -->
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

                <!-- 📋 Detail Pekerjaan -->
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
                                <p class="mt-1 text-gray-800">{{ $laporan->tanggal_survei ? \Carbon\Carbon::parse($laporan->tanggal_survei)->isoFormat('D MMMM Y') : '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</label>
                                <p class="mt-1 text-gray-800">{{ $laporan->tanggal_selesai ? \Carbon\Carbon::parse($laporan->tanggal_selesai)->isoFormat('D MMMM Y') : '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Durasi</label>
                                <p class="mt-1 text-gray-800">
                                    @if($laporan->tanggal_survei && $laporan->tanggal_selesai)
                                        {{ \Carbon\Carbon::parse($laporan->tanggal_survei)->diffInDays($laporan->tanggal_selesai) }} hari
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Keterangan -->
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Keterangan</label>
                            <div class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $laporan->keterangan ?? 'Tidak ada keterangan' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 💰 Informasi Keuangan -->
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

            </div>

            <!-- KOLOM KANAN: Dokumen & Metadata -->
            <div class="space-y-6">
                
                <!-- 📎 Dokumen Terlampir -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 flex items-center">
                            <i class="fa-solid fa-paperclip text-blue-600 mr-2"></i>Dokumen
                        </h3>
                        @if($laporan->dokumen->count() > 0)
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">{{ $laporan->dokumen->count() }}</span>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($laporan->dokumen->count() > 0)
                        <div class="space-y-3">
                            @foreach($laporan->dokumen as $doc)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-shrink-0">
                                    @if(str_contains($doc->mime_type, 'image'))
                                        <i class="fa-regular fa-image text-blue-500 text-lg"></i>
                                    @elseif($doc->mime_type === 'application/pdf')
                                        <i class="fa-regular fa-file-pdf text-red-500 text-lg"></i>
                                    @else
                                        <i class="fa-regular fa-file text-gray-500 text-lg"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $doc->nama_file }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $doc->tipe === 'pekerjaan' ? '🔧 Pekerjaan' : '📄 Administrasi' }} 
                                        • {{ round($doc->ukuran_file / 1024, 1) }} KB
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($doc->uploaded_at)->format('d M Y') }}</p>
                                </div>
                                <a href="{{ asset('storage/' . $doc->path_file) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-sm text-center py-4">Belum ada dokumen terlampir</p>
                        @endif
                    </div>
                    <!-- Upload Form (jika ingin upload langsung dari sini) -->
                    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                        <form action="{{ route('laporan.dokumen.store', $laporan) }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                            @csrf
                            <select name="tipe" class="text-sm border border-gray-300 rounded px-2 py-1.5 focus:ring-2 focus:ring-blue-500">
                                <option value="pekerjaan">Pekerjaan</option>
                                <option value="administrasi">Administrasi</option>
                            </select>
                            <input type="file" name="file" class="text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition">
                                <i class="fa-solid fa-upload"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 🕐 Metadata -->
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
                            <span class="text-gray-500">Terakhir Diupdate</span>
                            <span class="text-gray-800">{{ $laporan->updated_at?->isoFormat('D MMM Y, HH:mm') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ID Laporan</span>
                            <span class="text-gray-800 font-mono">{{ $laporan->id }}</span>
                        </div>
                    </div>
                </div>

                <!-- ⚡ Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800">Aksi Cepat</h3>
                    </div>
                    <div class="p-5 space-y-2">
                        <a href="{{ route('laporan.edit', $laporan) }}" class="block w-full text-left px-4 py-2.5 text-sm text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition flex items-center">
                            <i class="fa-solid fa-pen-to-square mr-2"></i>Edit Laporan
                        </a>
                        <button onclick="window.print()" class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition flex items-center">
                            <i class="fa-solid fa-print mr-2"></i>Cetak / PDF
                        </button>
                        <a href="{{ route('laporan.index') }}" class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition flex items-center">
                            <i class="fa-solid fa-list mr-2"></i>Kembali ke Daftar
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection`