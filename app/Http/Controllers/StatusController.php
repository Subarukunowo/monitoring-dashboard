<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::orderBy('id')->get();
        return view('status.index', compact('statuses'));
    }

    public function create()
    {
        return view('status.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'status_kerja' => [
                'required',
                Rule::in(['pending', 'on_progress', 'on_hold', 'completed']),
                Rule::unique('status', 'status_kerja'),
            ],
        ]);

        Status::create($validated);

        return redirect()->route('status.index')
            ->with('success', 'Status berhasil ditambahkan!');
    }

    public function edit(Status $status)
    {
        return view('status.edit', compact('status'));
    }

    public function update(Request $request, Status $status)
    {
        $validated = $request->validate([
            'status_kerja' => [
                'required',
                Rule::in(['pending', 'on_progress', 'on_hold', 'completed']),
                Rule::unique('status', 'status_kerja')->ignore($status->id),
            ],
        ]);

        $status->update($validated);

        return redirect()->route('status.index')
            ->with('success', 'Status berhasil diperbarui!');
    }

    public function destroy(Status $status)
    {
        if ($status->laporan()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus status yang masih digunakan oleh laporan!');
        }

        $status->delete();

        return redirect()->route('status.index')
            ->with('success', 'Status berhasil dihapus!');
    }
}