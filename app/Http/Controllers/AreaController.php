<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::withCount('laporan')
            ->when($request->filled('search'), fn ($q) => $q->where('nama_area', 'like', '%'.$request->search.'%'))
            ->orderBy('nama_area')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Master/Areas', [
            'areas' => $areas,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Areas', [
            'areas' => Area::withCount('laporan')->orderBy('nama_area')->paginate(12),
            'filters' => [],
            'openCreate' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_area' => 'required|string|max:100|unique:areas,nama_area',
        ], [
            'nama_area.required' => 'Nama area wajib diisi.',
            'nama_area.unique' => 'Nama area sudah terdaftar.',
        ]);

        Area::create($validated);

        return redirect()->route('areas.index')->with('success', 'Area berhasil ditambahkan!');
    }

    public function edit(Area $area)
    {
        return Inertia::render('Master/Areas', [
            'areas' => Area::withCount('laporan')->orderBy('nama_area')->paginate(12),
            'filters' => [],
            'editingArea' => $area,
        ]);
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'nama_area' => "required|string|max:100|unique:areas,nama_area,{$area->id}",
        ], [
            'nama_area.required' => 'Nama area wajib diisi.',
            'nama_area.unique' => 'Nama area sudah terdaftar.',
        ]);

        $area->update($validated);

        return redirect()->route('areas.index')->with('success', 'Area berhasil diperbarui!');
    }

    public function destroy(Area $area)
    {
        if ($area->laporan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus area yang masih memiliki laporan!');
        }

        $area->delete();

        return redirect()->route('areas.index')->with('success', 'Area berhasil dihapus!');
    }
}
