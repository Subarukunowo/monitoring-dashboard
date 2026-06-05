<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Tampilkan halaman kontak developer.
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Proses kiriman pesan dari form kontak.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'topik' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string', 'max:5000'],
        ]);

        // ── Opsi 1: Kirim via email (aktifkan jika mail sudah dikonfigurasi) ──
        // Mail::to('dev@maintainnow.id')->send(new \App\Mail\ContactMail(
        //     auth()->user(),
        //     $validated['topik'],
        //     $validated['pesan'],
        // ));

        // ── Opsi 2: Log saja (untuk development / sebelum mail siap) ──────────
        \Illuminate\Support\Facades\Log::info('Contact form submitted', [
            'from'  => auth()->user()->email,
            'name'  => auth()->user()->name,
            'topik' => $validated['topik'],
            'pesan' => $validated['pesan'],
        ]);

        return redirect()
            ->route('contact.index')
            ->with('success', 'Pesan berhasil dikirim. Terima kasih, akan dibalas dalam 1–2 hari kerja.');
    }
}