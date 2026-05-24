<?php
namespace App\Http\Controllers;

use App\Models\Pengawas;
use Illuminate\Http\Request;

class PengawasController extends Controller
{
    public function index()
    {
        $pengawass = Pengawas::orderBy('nama')->paginate(10);
        return view('pengawas.index', compact('pengawass'));
    }

    public function create() { return view('pengawas.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20'
        ]);
        Pengawas::create($validated);
        return redirect()->route('pengawas.index')->with('success', 'Pengawas berhasil ditambahkan!');
    }

    public function edit(Pengawas $pengawas) { return view('pengawas.edit', compact('pengawas')); }

    public function update(Request $request, Pengawas $pengawas)
    {
        $validated = $request->validate([
            'nama' => "required|string|max:100",
            'no_telepon' => 'nullable|string|max:20'
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