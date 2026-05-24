<?php
namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function store(Request $request, Laporan $laporan)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:pekerjaan,administrasi',
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx' // max 10MB
        ]);

        $file = $validated['file'];
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('dokumen/' . $laporan->id, $filename, 'public');

        Dokumen::create([
            'id_laporan' => $laporan->id,
            'tipe' => $validated['tipe'],
            'nama_file' => $file->getClientOriginalName(),
            'path_file' => $path,
            'mime_type' => $file->getMimeType(),
            'ukuran_file' => $file->getSize(),
        ]);

        return back()->with('success', 'Dokumen berhasil diupload!');
    }

    public function destroy(Dokumen $dokumen)
    {
        if (Storage::disk('public')->exists($dokumen->path_file)) {
            Storage::disk('public')->delete($dokumen->path_file);
        }
        $dokumen->delete();
        return back()->with('success', 'Dokumen berhasil dihapus!');
    }
}