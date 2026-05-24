<?php
namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::orderBy('nama')->paginate(10);
        return view('pelanggans.index', compact('pelanggans'));
    }

    public function create() { return view('pelanggans.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pelanggan' => 'nullable|numeric|unique:pelanggans,no_pelanggan',
            'nama' => 'required|string|max:150',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_pelanggan' => 'boolean'
        ]);
        Pelanggan::create($validated);
        return redirect()->route('pelanggans.index')->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    public function edit(Pelanggan $pelanggan) { return view('pelanggans.edit', compact('pelanggan')); }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'no_pelanggan' => "nullable|numeric|unique:pelanggans,no_pelanggan,{$pelanggan->id}",
            'nama' => 'required|string|max:150',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_pelanggan' => 'boolean'
        ]);
        $pelanggan->update($validated);
        return redirect()->route('pelanggans.index')->with('success', 'Pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        if ($pelanggan->laporan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus pelanggan yang masih memiliki laporan!');
        }
        $pelanggan->delete();
        return redirect()->route('pelanggans.index')->with('success', 'Pelanggan berhasil dihapus!');
    }
}