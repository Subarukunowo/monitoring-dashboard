<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::withCount('laporan')->orderBy('id')->get()->map(fn ($s) => [
            'id' => $s->id,
            'status_kerja' => $s->status_kerja,
            'label' => $s->label,
            'laporan_count' => $s->laporan_count,
        ]);

        return Inertia::render('Master/Status', [
            'statuses' => $statuses,
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Status', [
            'statuses' => Status::withCount('laporan')->orderBy('id')->get()->map(fn ($s) => [
                'id' => $s->id,
                'status_kerja' => $s->status_kerja,
                'label' => $s->label,
                'laporan_count' => $s->laporan_count,
            ]),
            'openCreate' => true,
        ]);
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
        return Inertia::render('Master/Status', [
            'statuses' => Status::withCount('laporan')->orderBy('id')->get()->map(fn ($s) => [
                'id' => $s->id,
                'status_kerja' => $s->status_kerja,
                'label' => $s->label,
                'laporan_count' => $s->laporan_count,
            ]),
            'editingStatus' => $status,
        ]);
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
