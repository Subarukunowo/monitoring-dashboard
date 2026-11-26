<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelanggans = Pelanggan::withCount('laporan')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn ($sub) => $sub
                    ->where('nama', 'like', "%{$s}%")
                    ->orWhere('no_pelanggan', 'like', "%{$s}%")
                    ->orWhere('alamat', 'like', "%{$s}%")
                );
            })
            ->when($request->filled('tipe') && $request->tipe !== 'all', function ($q) use ($request) {
                $q->where('status_pelanggan', $request->tipe === 'resmi' ? 1 : 0);
            })
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Master/Pelanggans', [
            'pelanggans' => $pelanggans,
            'filters' => $request->only(['search', 'tipe']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Pelanggans', [
            'pelanggans' => Pelanggan::withCount('laporan')->orderBy('nama')->paginate(12),
            'filters' => [],
            'openCreate' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_pelanggan' => 'nullable|numeric|unique:pelanggan,no_pelanggan',
            'nama' => 'required|string|max:150',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_pelanggan' => 'boolean',
        ]);

        $validated['status_pelanggan'] = $request->boolean('status_pelanggan', true);
        Pelanggan::create($validated);

        return redirect()->route('pelanggans.index')->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    public function edit(Pelanggan $pelanggan)
    {
        return Inertia::render('Master/Pelanggans', [
            'pelanggans' => Pelanggan::withCount('laporan')->orderBy('nama')->paginate(12),
            'filters' => [],
            'editingPelanggan' => $pelanggan,
        ]);
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'no_pelanggan' => ['nullable', 'numeric', Rule::unique('pelanggan', 'no_pelanggan')->ignore($pelanggan->id)],
            'nama' => 'required|string|max:150',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_pelanggan' => 'boolean',
        ]);

        $validated['status_pelanggan'] = $request->boolean('status_pelanggan', true);
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
