@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Laporan</h1>
            <p class="text-gray-500 text-sm">No. Insiden: <span class="font-medium">{{ $laporan->no_insiden }}</span></p>
        </div>
        <a href="{{ route('laporan.show', $laporan) }}" class="text-gray-600 hover:text-blue-600">
            <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    {{-- Error bag global --}}
    @if($errors->any())
        <div class="flex items-start gap-2 mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
            <i class="fa-solid fa-circle-exclamation shrink-0 mt-0.5"></i>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            {{-- ===== DATA INSIDEN ===== --}}
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Data Insiden</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- No Insiden --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Insiden *</label>
                    <input type="text" name="no_insiden" value="{{ old('no_insiden', $laporan->no_insiden) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('no_insiden') border-red-500 bg-red-50 @else border-gray-300 @enderror"
                        required>
                    @error('no_insiden') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Laporan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Laporan *</label>
                    <input type="date" name="tanggal_laporan"
                        value="{{ old('tanggal_laporan', $laporan->tanggal_laporan?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('tanggal_laporan') border-red-500 bg-red-50 @else border-gray-300 @enderror"
                        required>
                    @error('tanggal_laporan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jam Laporan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Laporan</label>
                    <input type="time" name="jam_laporan"
                        value="{{ old('jam_laporan', $laporan->jam_laporan?->format('H:i')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                {{-- Area --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Area *</label>
                    <select name="id_area"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('id_area') border-red-500 bg-red-50 @else border-gray-300 @enderror"
                        required>
                        <option value="">Pilih Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ old('id_area', $laporan->id_area) == $area->id ? 'selected' : '' }}>
                                {{ $area->nama_area }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_area') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pelanggan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
                    <input type="text" name="nama_pelanggan"
                        value="{{ old('nama_pelanggan', $laporan->pelanggan?->nama) }}"
                        placeholder="Nama pelanggan / kosongkan jika umum"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('nama_pelanggan') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                    @error('nama_pelanggan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pengawas (teks bebas) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengawas</label>
                    <input type="text" name="nama_pengawas"
                        value="{{ old('nama_pengawas', $laporan->pengawas?->nama ?? $laporan->nama_pengawas) }}"
                        placeholder="Nama pengawas"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('nama_pengawas') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                    @error('nama_pengawas') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jenis Pekerjaan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pekerjaan</label>
                    <input type="text" name="jenis_pekerjaan"
                        value="{{ old('jenis_pekerjaan', $laporan->jenisPekerjaan?->nama_jenis ?? $laporan->jenis_pekerjaan) }}"
                        placeholder="Contoh: Perbaikan Jaringan"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('jenis_pekerjaan') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                    @error('jenis_pekerjaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="id_status"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('id_status') border-red-500 bg-red-50 @else border-gray-300 @enderror"
                        required>
                        <option value="">Pilih Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('id_status', $laporan->id_status) == $status->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status->status_kerja)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Survei --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Survei</label>
                    <input type="date" name="tanggal_survei"
                        value="{{ old('tanggal_survei', $laporan->tanggal_survei?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai"
                        value="{{ old('tanggal_selesai', $laporan->tanggal_selesai?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                {{-- No SAP --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. SAP / TUG</label>
                    <input type="text" name="no_sap" value="{{ old('no_sap', $laporan->no_sap) }}"
                        placeholder="SAP : XXXXX"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                {{-- Link Maps --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link Maps</label>
                    <input type="url" name="link_maps" value="{{ old('link_maps', $laporan->link_maps) }}"
                        placeholder="https://maps.google.com/..."
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('link_maps') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                    @error('link_maps') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Keterangan --}}
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="4"
                    placeholder="Tuliskan keterangan tambahan jika ada..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition resize-none">{{ old('keterangan', $laporan->keterangan) }}</textarea>
            </div>

            {{-- ===== FOTO & DOKUMEN ===== --}}
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Foto & Dokumen</h2>

                @php
                    $fotoPekerjaan    = $laporan->dokumen->where('tipe', 'pekerjaan')->filter(fn($d) => str_starts_with($d->mime_type, 'image/'));
                    $fotoAdministrasi = $laporan->dokumen->where('tipe', 'administrasi')->filter(fn($d) => str_starts_with($d->mime_type, 'image/'));
                    $dokumenList      = $laporan->dokumen->where('tipe', 'dokumen');
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- ── FOTO PEKERJAAN ── --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Foto Pekerjaan
                            <span class="text-xs text-gray-400 font-normal ml-1">(bisa lebih dari satu)</span>
                        </label>

                        @if($fotoPekerjaan->count())
                            <div class="flex flex-wrap gap-2 mb-3" id="existing-foto-pekerjaan">
                                @foreach($fotoPekerjaan as $foto)
                                    <div class="relative" id="wrap-foto-{{ $foto->id }}">
                                        <img src="{{ '/storage/' . $foto->path_file }}"
                                            alt="{{ $foto->nama_file }}"
                                            class="w-20 h-20 object-cover rounded-lg border border-gray-200 transition"
                                            onerror="this.style.display='none'"
                                            id="img-foto-{{ $foto->id }}">
                                        {{-- checkbox tersembunyi, diisi via JS --}}
                                        <input type="checkbox" name="hapus_dokumen[]"
                                            value="{{ $foto->id }}"
                                            class="hidden"
                                            id="chk-{{ $foto->id }}">
                                        <button type="button"
                                            onclick="tandaiHapus({{ $foto->id }})"
                                            id="btn-{{ $foto->id }}"
                                            class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center leading-none shadow transition"
                                            title="Hapus foto ini">✕</button>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-400 mb-2">
                                <i class="fa-solid fa-circle-info mr-1"></i>Klik ✕ untuk menandai hapus. Klik ↩ untuk membatalkan.
                            </p>
                        @endif

                        <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-camera text-gray-400 text-xl mb-1"></i>
                            <p class="text-sm text-gray-500">Upload foto pekerjaan baru</p>
                            <p class="text-xs text-gray-400">JPG, PNG, WEBP — maks. 5MB/file</p>
                            <input type="file" name="foto_pekerjaan[]" multiple accept="image/*" class="hidden" id="inp-foto-pekerjaan">
                        </label>
                        <div id="prev-foto-pekerjaan" class="flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    {{-- ── FOTO ADMINISTRASI ── --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Foto Administrasi
                            <span class="text-xs text-gray-400 font-normal ml-1">(bisa lebih dari satu)</span>
                        </label>

                        @if($fotoAdministrasi->count())
                            <div class="flex flex-wrap gap-2 mb-3" id="existing-foto-administrasi">
                                @foreach($fotoAdministrasi as $foto)
                                    <div class="relative" id="wrap-foto-{{ $foto->id }}">
                                        <img src="{{ '/storage/' . $foto->path_file }}"
                                            alt="{{ $foto->nama_file }}"
                                            class="w-20 h-20 object-cover rounded-lg border border-gray-200 transition"
                                            onerror="this.style.display='none'"
                                            id="img-foto-{{ $foto->id }}">
                                        <input type="checkbox" name="hapus_dokumen[]"
                                            value="{{ $foto->id }}"
                                            class="hidden"
                                            id="chk-{{ $foto->id }}">
                                        <button type="button"
                                            onclick="tandaiHapus({{ $foto->id }})"
                                            id="btn-{{ $foto->id }}"
                                            class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center leading-none shadow transition"
                                            title="Hapus foto ini">✕</button>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-400 mb-2">
                                <i class="fa-solid fa-circle-info mr-1"></i>Klik ✕ untuk menandai hapus. Klik ↩ untuk membatalkan.
                            </p>
                        @endif

                        <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-file-image text-gray-400 text-xl mb-1"></i>
                            <p class="text-sm text-gray-500">Upload foto administrasi baru</p>
                            <p class="text-xs text-gray-400">JPG, PNG, WEBP — maks. 5MB/file</p>
                            <input type="file" name="foto_administrasi[]" multiple accept="image/*" class="hidden" id="inp-foto-administrasi">
                        </label>
                        <div id="prev-foto-administrasi" class="flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    {{-- ── DOKUMEN PENDUKUNG ── --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dokumen Pendukung
                            <span class="text-xs text-gray-400 font-normal ml-1">(PDF, Word, Excel)</span>
                        </label>

                        @if($dokumenList->count())
                            <ul class="mb-3 divide-y divide-gray-100 border border-gray-200 rounded-lg overflow-hidden">
                                @foreach($dokumenList as $dok)
                                    <li class="flex items-center justify-between px-4 py-2.5 bg-gray-50 hover:bg-gray-100 transition"
                                        id="wrap-dok-{{ $dok->id }}">
                                        <a href="{{ '/storage/' . $dok->path_file }}" target="_blank"
                                            class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-600 min-w-0">
                                            <i class="fa-solid fa-file-lines text-blue-400 shrink-0"></i>
                                            <span class="truncate">{{ $dok->nama_file }}</span>
                                            @if($dok->ukuran_file)
                                                <span class="text-xs text-gray-400 shrink-0">({{ number_format($dok->ukuran_file / 1024, 0) }} KB)</span>
                                            @endif
                                        </a>
                                        <input type="checkbox" name="hapus_dokumen[]"
                                            value="{{ $dok->id }}"
                                            class="hidden"
                                            id="chk-{{ $dok->id }}">
                                        <button type="button"
                                            onclick="tandaiHapusDok({{ $dok->id }})"
                                            id="btn-{{ $dok->id }}"
                                            class="ml-4 shrink-0 flex items-center gap-1 text-xs text-red-500 hover:text-red-700 font-medium transition"
                                            title="Hapus dokumen ini">
                                            <i class="fa-solid fa-xmark"></i> Hapus
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-file-arrow-up text-gray-400 text-xl mb-1"></i>
                            <p class="text-sm text-gray-500">Upload dokumen baru</p>
                            <p class="text-xs text-gray-400">PDF, DOC, DOCX, XLS, XLSX — maks. 10MB/file</p>
                            <input type="file" name="dokumen[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden" id="inp-dokumen">
                        </label>
                        <div id="prev-dokumen" class="mt-2 space-y-1"></div>
                    </div>

                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('laporan.show', $laporan) }}"
                    class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function tandaiHapus(id) {
        const chk = document.getElementById('chk-' + id);
        const img = document.getElementById('img-foto-' + id);
        const btn = document.getElementById('btn-' + id);
        chk.checked = !chk.checked;
        if (chk.checked) {
            img.classList.add('opacity-40', 'ring-2', 'ring-red-400', 'rounded-lg');
            btn.textContent = '↩';
            btn.classList.remove('bg-red-500', 'hover:bg-red-600');
            btn.classList.add('bg-gray-400', 'hover:bg-gray-500');
            btn.title = 'Batalkan hapus';
        } else {
            img.classList.remove('opacity-40', 'ring-2', 'ring-red-400', 'rounded-lg');
            btn.textContent = '✕';
            btn.classList.remove('bg-gray-400', 'hover:bg-gray-500');
            btn.classList.add('bg-red-500', 'hover:bg-red-600');
            btn.title = 'Hapus foto ini';
        }
    }

    function tandaiHapusDok(id) {
        const chk = document.getElementById('chk-' + id);
        const row = document.getElementById('wrap-dok-' + id);
        const btn = document.getElementById('btn-' + id);
        chk.checked = !chk.checked;
        if (chk.checked) {
            row.classList.add('opacity-50', 'line-through', 'bg-red-50');
            btn.innerHTML = '<i class="fa-solid fa-rotate-left"></i> Batal';
            btn.classList.remove('text-red-500', 'hover:text-red-700');
            btn.classList.add('text-gray-400', 'hover:text-gray-600');
        } else {
            row.classList.remove('opacity-50', 'line-through', 'bg-red-50');
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Hapus';
            btn.classList.remove('text-gray-400', 'hover:text-gray-600');
            btn.classList.add('text-red-500', 'hover:text-red-700');
        }
    }

    function setupFotoInput(inputId, previewId) {
        const input = document.getElementById(inputId);
        const wrap  = document.getElementById(previewId);
        input.addEventListener('change', function () {
            wrap.innerHTML = '';
            [...this.files].forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `<img src="${e.target.result}" class="w-20 h-20 object-cover rounded-lg border border-blue-200">`;
                    wrap.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    function setupDokumenInput(inputId, previewId) {
        const input = document.getElementById(inputId);
        const wrap  = document.getElementById(previewId);
        input.addEventListener('change', function () {
            wrap.innerHTML = '';
            [...this.files].forEach(file => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 text-sm text-gray-600 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5';
                row.innerHTML = `
                    <i class="fa-solid fa-file-lines text-blue-400 shrink-0"></i>
                    <span class="truncate">${file.name}</span>
                    <span class="ml-auto text-xs text-blue-500 shrink-0">Baru</span>`;
                wrap.appendChild(row);
            });
        });
    }

    setupFotoInput('inp-foto-pekerjaan',    'prev-foto-pekerjaan');
    setupFotoInput('inp-foto-administrasi', 'prev-foto-administrasi');
    setupDokumenInput('inp-dokumen',        'prev-dokumen');
</script>
@endsection