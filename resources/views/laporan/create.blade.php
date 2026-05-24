@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Laporan Baru</h1>
            <p class="text-gray-500 text-sm">Isi semua field yang wajib diisi (*)</p>
        </div>
        <a href="{{ route('laporan.index') }}" class="text-gray-600 hover:text-blue-600">
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
        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            {{-- ===== DATA INSIDEN ===== --}}
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Data Insiden</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- No Insiden --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Insiden *</label>
                    <input type="text" name="no_insiden" value="{{ old('no_insiden') }}"
                        placeholder="Contoh: INS-2025-001"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('no_insiden') border-red-500 bg-red-50 @else border-gray-300 @enderror"
                        required>
                    @error('no_insiden') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Laporan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Laporan *</label>
                    <input type="date" name="tanggal_laporan" value="{{ old('tanggal_laporan', date('Y-m-d')) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('tanggal_laporan') border-red-500 bg-red-50 @else border-gray-300 @enderror"
                        required>
                    @error('tanggal_laporan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jam Laporan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Laporan</label>
                    <input type="time" name="jam_laporan" value="{{ old('jam_laporan') }}"
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
                            <option value="{{ $area->id }}" {{ old('id_area') == $area->id ? 'selected' : '' }}>
                                {{ $area->nama_area }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_area') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pelanggan (teks bebas) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
                    <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}"
                        placeholder="Nama pelanggan / kosongkan jika umum"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition
                            @error('nama_pelanggan') border-red-500 bg-red-50 @else border-gray-300 @enderror">
                    @error('nama_pelanggan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pengawas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengawas</label>
                    <select name="id_pengawas"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        <option value="">Pilih Pengawas</option>
                        @foreach($pengawass as $pengawas)
                            <option value="{{ $pengawas->id }}" {{ old('id_pengawas') == $pengawas->id ? 'selected' : '' }}>
                                {{ $pengawas->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis Pekerjaan (teks bebas) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pekerjaan</label>
                    <input type="text" name="jenis_pekerjaan" value="{{ old('jenis_pekerjaan') }}"
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
                            <option value="{{ $status->id }}" {{ old('id_status') == $status->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status->status_kerja)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Survei --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Survei</label>
                    <input type="date" name="tanggal_survei" value="{{ old('tanggal_survei') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                {{-- No SAP --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. SAP / TUG</label>
                    <input type="text" name="no_sap" value="{{ old('no_sap') }}"
                        placeholder="SAP : XXXXX"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

            </div>

            {{-- Keterangan --}}
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="4"
                    placeholder="Tuliskan keterangan tambahan jika ada..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition resize-none">{{ old('keterangan') }}</textarea>
            </div>

            {{-- ===== FOTO & DOKUMEN ===== --}}
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5">Foto & Dokumen</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- ── FOTO PEKERJAAN ── --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Foto Pekerjaan
                            <span class="text-xs text-gray-400 font-normal ml-1">(bisa lebih dari satu)</span>
                        </label>
                        <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-camera text-gray-400 text-xl mb-1"></i>
                            <p class="text-sm text-gray-500">Upload foto pekerjaan</p>
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
                        <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-file-image text-gray-400 text-xl mb-1"></i>
                            <p class="text-sm text-gray-500">Upload foto administrasi</p>
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
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <i class="fa-solid fa-file-arrow-up text-gray-400 text-xl mb-1"></i>
                            <p class="text-sm text-gray-500">Upload dokumen pendukung</p>
                            <p class="text-xs text-gray-400">PDF, DOC, DOCX, XLS, XLSX — maks. 10MB/file</p>
                            <input type="file" name="dokumen[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden" id="inp-dokumen">
                        </label>
                        <div id="prev-dokumen" class="mt-2 space-y-1"></div>
                    </div>

                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('laporan.index') }}"
                    class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Simpan Laporan
                </button>
            </div>

        </form>
    </div>
</div>

{{-- Preview script --}}
<script>
    function previewGambar(inputId, previewId) {
        document.getElementById(inputId).addEventListener('change', function () {
            const wrap = document.getElementById(previewId);
            wrap.innerHTML = '';
            [...this.files].forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e => {
                    wrap.insertAdjacentHTML('beforeend', `
                        <div class="relative">
                            <img src="${e.target.result}" class="w-20 h-20 object-cover rounded-lg border border-blue-200">
                            <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full px-1.5 leading-5">Baru</span>
                        </div>`);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    function previewDokumen(inputId, previewId) {
        document.getElementById(inputId).addEventListener('change', function () {
            const wrap = document.getElementById(previewId);
            wrap.innerHTML = '';
            [...this.files].forEach(file => {
                wrap.insertAdjacentHTML('beforeend', `
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                        <i class="fa-solid fa-file-lines text-blue-400"></i>
                        <span class="truncate">${file.name}</span>
                        <span class="ml-auto text-xs text-blue-500 shrink-0">Baru</span>
                    </div>`);
            });
        });
    }

    previewGambar('inp-foto-pekerjaan', 'prev-foto-pekerjaan');
    previewGambar('inp-foto-administrasi', 'prev-foto-administrasi');
    previewDokumen('inp-dokumen', 'prev-dokumen');
</script>
@endsection