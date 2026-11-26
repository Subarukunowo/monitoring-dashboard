<?php

namespace App\Http\Controllers;

use App\Models\Pengawas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PengawasController extends Controller
{
    public function index(Request $request)
    {
        $pengawass = Pengawas::withCount('laporan')
            ->when($request->filled('search'), fn ($q) => $q->where('nama', 'like', '%'.$request->search.'%'))
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Master/Pengawas', [
            'pengawass' => $pengawass,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Pengawas', [
            'pengawass' => Pengawas::withCount('laporan')->orderBy('nama')->paginate(12),
            'filters' => [],
            'openCreate' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        Pengawas::create($validated);

        return redirect()->route('pengawas.index')->with('success', 'Pengawas berhasil ditambahkan!');
    }

    public function edit(Pengawas $pengawas)
    {
        return Inertia::render('Master/Pengawas', [
            'pengawass' => Pengawas::withCount('laporan')->orderBy('nama')->paginate(12),
            'filters' => [],
            'editingPengawas' => $pengawas,
        ]);
    }

    public function update(Request $request, Pengawas $pengawas)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        $pengawas->update($validated);

        return redirect()->route('pengawas.index')->with('success', 'Pengawas berhasil diperbarui!');
    }

    public function destroy(Pengawas $pengawas)
    {
        if ($pengawas->laporan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus pengawas yang masih memiliki laporan!');
        }

        $pengawas->delete();

        return redirect()->route('pengawas.index')->with('success', 'Pengawas berhasil dihapus!');
    }
}
