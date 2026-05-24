<?php
namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::orderBy('nama_area')->paginate(10);
        return view('areas.index', compact('areas'));
    }

    public function create() { return view('areas.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_area' => 'required|string|max:100|unique:areas,nama_area'
        ]);
        Area::create($validated);
        return redirect()->route('areas.index')->with('success', 'Area berhasil ditambahkan!');
    }

    public function edit(Area $area) { return view('areas.edit', compact('area')); }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'nama_area' => "required|string|max:100|unique:areas,nama_area,{$area->id}"
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