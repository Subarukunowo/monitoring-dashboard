<?php
namespace App\Http\Controllers;

use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;

class JenisPekerjaanController extends Controller
{
    public function index()
    {
        $jenisPekerjaans = JenisPekerjaan::orderBy('nama_jenis')->paginate(10);
        return view('jenis_pekerjaan.index', compact('jenisPekerjaans'));
    }

    public function create() { return view('jenis_pekerjaan.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:150|unique:jenis_pekerjaan,nama_jenis'
        ]);
        JenisPekerjaan::create($validated);
        return redirect()->route('jenis_pekerjaan.index')->with('success', 'Jenis pekerjaan berhasil ditambahkan!');
    }

    public function edit(JenisPekerjaan $jenisPekerjaan) { 
        return view('jenis_pekerjaan.edit', compact('jenisPekerjaan')); 
    }

    public function update(Request $request, JenisPekerjaan $jenisPekerjaan)
    {
        $validated = $request->validate([
            'nama_jenis' => "required|string|max:150|unique:jenis_pekerjaan,nama_jenis,{$jenisPekerjaan->id}"
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