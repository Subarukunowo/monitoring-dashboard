<?php

namespace App\Http\Controllers;

use App\Models\BqItem;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BqController extends Controller
{
    public function index(Request $request)
    {
        $laporans = Laporan::with(['area', 'jenisPekerjaan'])
            ->withCount('bqItems')
            ->withSum('bqItems', 'subtotal')
            ->orderByDesc('tanggal_laporan')
            ->get(['id', 'no_insiden', 'id_area', 'id_jenis_pekerjaan', 'nilai_rab', 'status_rab']);

        $selectedId = $request->integer('laporan_id') ?: ($laporans->first()?->id ?? 0);
        $selectedLaporan = $selectedId
            ? Laporan::with(['area', 'pelanggan', 'jenisPekerjaan', 'bqItems'])->find($selectedId)
            : null;

        return Inertia::render('Bq/Index', [
            'laporans' => $laporans->map(fn ($l) => [
                'id' => $l->id,
                'no_insiden' => $l->no_insiden,
                'area' => $l->area?->nama_area ?? '—',
                'jenis_pekerjaan' => $l->jenisPekerjaan?->nama_jenis ?? '—',
                'nilai_rab' => $l->nilai_rab !== null ? (float) $l->nilai_rab : null,
                'bq_items_count' => $l->bq_items_count,
                'bq_total' => (float) ($l->bq_items_sum_subtotal ?? 0),
            ]),
            'selectedLaporan' => $selectedLaporan ? [
                'id' => $selectedLaporan->id,
                'no_insiden' => $selectedLaporan->no_insiden,
                'area' => $selectedLaporan->area?->nama_area ?? '—',
                'pelanggan' => $selectedLaporan->pelanggan?->nama ?? 'Umum',
                'jenis_pekerjaan' => $selectedLaporan->jenisPekerjaan?->nama_jenis ?? '—',
                'nilai_rab' => $selectedLaporan->nilai_rab !== null ? (float) $selectedLaporan->nilai_rab : null,
                'status_rab' => $selectedLaporan->status_rab ?? 'draft',
                'items' => $selectedLaporan->bqItems->map(fn (BqItem $item) => [
                    'id' => $item->id,
                    'kode_material' => $item->kode_material,
                    'uraian' => $item->uraian,
                    'kategori' => $item->kategori,
                    'satuan' => $item->satuan,
                    'volume' => (float) $item->volume,
                    'harga_satuan' => (float) $item->harga_satuan,
                    'subtotal' => (float) $item->subtotal,
                ]),
            ] : null,
        ]);
    }

    public function store(Request $request, Laporan $laporan)
    {
        $validated = $request->validate([
            'kode_material' => 'nullable|string|max:50',
            'uraian' => 'required|string|max:255',
            'kategori' => 'required|in:Material,Jasa,Sipil',
            'satuan' => 'required|string|max:30',
            'volume' => 'required|numeric|min:0.01',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $subtotal = round((float) $validated['volume'] * (float) $validated['harga_satuan'], 2);

        $laporan->bqItems()->create([
            ...$validated,
            'subtotal' => $subtotal,
        ]);

        return redirect()->route('bq.index', ['laporan_id' => $laporan->id])
            ->with('success', 'Item Bill of Quantity berhasil ditambahkan.');
    }

    public function update(Request $request, BqItem $bqItem)
    {
        $validated = $request->validate([
            'kode_material' => 'nullable|string|max:50',
            'uraian' => 'required|string|max:255',
            'kategori' => 'required|in:Material,Jasa,Sipil',
            'satuan' => 'required|string|max:30',
            'volume' => 'required|numeric|min:0.01',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $validated['subtotal'] = round((float) $validated['volume'] * (float) $validated['harga_satuan'], 2);
        $bqItem->update($validated);

        return redirect()->route('bq.index', ['laporan_id' => $bqItem->id_laporan])
            ->with('success', 'Item BQ berhasil diperbarui.');
    }

    public function destroy(BqItem $bqItem)
    {
        $laporanId = $bqItem->id_laporan;
        $bqItem->delete();

        return redirect()->route('bq.index', ['laporan_id' => $laporanId])
            ->with('success', 'Item BQ berhasil dihapus.');
    }

    public function syncToRab(Laporan $laporan)
    {
        $total = (float) $laporan->bqItems()->sum('subtotal');
        $laporan->update(['nilai_rab' => $total]);

        return redirect()->route('bq.index', ['laporan_id' => $laporan->id])
            ->with('success', 'Total BQ berhasil disinkronkan ke Nilai RAB.');
    }
}
