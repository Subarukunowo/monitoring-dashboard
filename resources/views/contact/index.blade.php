@extends('layouts.sidebar')

@section('title', 'Hubungi Developer - MaintainNow')
@section('page_title', 'Hubungi Developer')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
<div class="max-w-3xl">

    {{-- Profil Developer --}}
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Developer</p>
    <div class="bg-white rounded-xl border border-gray-200 p-8 flex flex-col items-center text-center mb-6">
        <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-2xl font-semibold mb-4">
            SMK {{-- Ganti dengan inisial nama Anda --}}
        </div>
        <h2 class="text-xl font-semibold text-gray-800 mb-1">Sandy Mulia Kesuma</h2> 
        <p class="text-sm text-gray-500 mb-4">Developer &middot; MaintainNow</p>
        <span class="inline-flex items-center gap-2 bg-green-50 text-green-700 text-xs px-4 py-1.5 rounded-full">
            <i class="fa-solid fa-circle-check text-green-500"></i>
            Tersedia untuk dihubungi
        </span>
    </div>

    {{-- Informasi Kontak --}}
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Informasi Kontak</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">

        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-lg shrink-0">
                <i class="fa-regular fa-envelope"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Email</p>
                <a href="mailto:dev@sandymuliakesuma@gmail.com"
                   class="text-sm font-medium text-blue-600 hover:underline">
                    dev@sandymuliakesuma@gmail.com 
                </a>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600 text-lg shrink-0">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">WhatsApp</p>
                <a href="https://wa.me/628217835961" target="_blank" {{-- Ganti nomor Anda --}}
                   class="text-sm font-medium text-green-600 hover:underline">
                    +62 821-7835-961
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 text-lg shrink-0">
                <i class="fa-brands fa-github"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">GitHub</p>
                <a href="https://github.com/Subarukunowo" target="_blank" {{-- Ganti username Anda --}}
                   class="text-sm font-medium text-purple-600 hover:underline">
                    github.com/Subarukunowo
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 text-lg shrink-0">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Waktu respons</p>
                <p class="text-sm font-medium text-gray-700">1–2 hari kerja</p>
            </div>
        </div>

    </div>

    {{-- Form Kirim Pesan --}}
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Kirim Pesan</p>

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">
            <i class="fa-solid fa-circle-check text-green-500"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <form action="{{ route('contact.send') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-600 mb-1.5" for="nama">Nama Anda</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ auth()->user()->name ?? '' }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-50 outline-none cursor-not-allowed"
                    readonly
                >
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1.5" for="email">Email Anda</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ auth()->user()->email ?? '' }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-50 outline-none cursor-not-allowed"
                    readonly
                >
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1.5" for="topik">Topik <span class="text-red-400">*</span></label>
                <input
                    type="text"
                    id="topik"
                    name="topik"
                    value="{{ old('topik') }}"
                    placeholder="cth: Bug halaman login, Request fitur baru…"
                    class="w-full px-4 py-2.5 border {{ $errors->has('topik') ? 'border-red-400' : 'border-gray-300' }} rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    required
                >
                @error('topik')
                    <p class="text-xs text-red-500 mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1.5" for="pesan">Pesan <span class="text-red-400">*</span></label>
                <textarea
                    id="pesan"
                    name="pesan"
                    rows="5"
                    placeholder="Jelaskan masalah atau pertanyaan Anda secara detail…"
                    class="w-full px-4 py-2.5 border {{ $errors->has('pesan') ? 'border-red-400' : 'border-gray-300' }} rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none resize-none"
                    required
                >{{ old('pesan') }}</textarea>
                @error('pesan')
                    <p class="text-xs text-red-500 mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    <i class="fa-solid fa-paper-plane"></i>
                    Kirim Pesan
                </button>
            </div>

        </form>
    </div>

    {{-- Catatan --}}
    <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 text-sm text-blue-700">
        <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
        <p class="m-0">Halaman ini digunakan untuk melaporkan <strong>bug</strong>, mengajukan <strong>pertanyaan teknis</strong>, atau mengusulkan <strong>fitur baru</strong> untuk sistem MaintainNow.</p>
    </div>

</div>
</div>
@endsection