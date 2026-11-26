<?php

namespace App\Http\Controllers;

use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JenisPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $jenisPekerjaans = JenisPekerjaan::withCount('laporan')
            ->when($request->filled('search'), fn ($q) => $q->where('nama_jenis', 'like', '%'.$request->search.'%'))
            ->orderBy('nama_jenis')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Master/JenisPekerjaan', [
            'jenisPekerjaans' => $jenisPekerjaans,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/JenisPekerjaan', [
            'jenisPekerjaans' => JenisPekerjaan::withCount('laporan')->orderBy('nama_jenis')->paginate(12),
            'filters' => [],
            'openCreate' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:150|unique:jenis_pekerjaan,nama_jenis',
        ]);

        JenisPekerjaan::create($validated);

        return redirect()->route('jenis_pekerjaan.index')->with('success', 'Jenis pekerjaan berhasil ditambahkan!');
    }

    public function edit(JenisPekerjaan $jenisPekerjaan)
    {
        return Inertia::render('Master/JenisPekerjaan', [
            'jenisPekerjaans' => JenisPekerjaan::withCount('laporan')->orderBy('nama_jenis')->paginate(12),
            'filters' => [],
            'editingJenis' => $jenisPekerjaan,
        ]);
    }

    public function update(Request $request, JenisPekerjaan $jenisPekerjaan)
    {
        $validated = $request->validate([
            'nama_jenis' => "required|string|max:150|unique:jenis_pekerjaan,nama_jenis,{$jenisPekerjaan->id}",
        ]);

        $jenisPekerjaan->update($validated);

        return redirect()->route('jenis_pekerjaan.index')->with('success', 'Jenis pekerjaan berhasil diperbarui!');
    }

    public function destroy(JenisPekerjaan $jenisPekerjaan)
    {
        if ($jenisPekerjaan->laporan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus jenis pekerjaan yang masih digunakan!');
        }

        $jenisPekerjaan->delete();

        return redirect()->route('jenis_pekerjaan.index')->with('success', 'Jenis pekerjaan berhasil dihapus!');
    }
}
