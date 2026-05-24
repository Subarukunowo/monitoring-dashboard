<?php
namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::orderBy('id')->get(); // Status biasanya fixed, tidak perlu paginate
        return view('status.index', compact('statuses'));
    }

    public function create() { return view('status.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'status_kerja' => 'required|in:pending,on_progress,completed|unique:status,status_kerja'
        ]);
        Status::create($validated);
        return redirect()->route('status.index')->with('success', 'Status berhasil ditambahkan!');
    }

    public function edit(Status $status) { return view('status.edit', compact('status')); }

    public function update(Request $request, Status $status)
    {
        $validated = $request->validate([
            'status_kerja' => "required|in:pending,on_progress,completed|unique:status,status_kerja,{$status->id}"
        ]);
        $status->update($validated);
        return redirect()->route('status.index')->with('success', 'Status berhasil diperbarui!');
    }

    public function destroy(Status $status)
    {
        if ($status->laporan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus status yang masih digunakan!');
        }
        $status->delete();
        return redirect()->route('status.index')->with('success', 'Status berhasil dihapus!');
    }
}