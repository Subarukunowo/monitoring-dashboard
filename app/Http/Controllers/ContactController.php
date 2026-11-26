<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdmin = ($user?->role ?? 'user') === 'admin';

        $messages = $isAdmin
            ? ContactMessage::orderByDesc('created_at')->limit(20)->get()
            : ($user
                ? ContactMessage::where('user_id', $user->id)->orderByDesc('created_at')->limit(10)->get()
                : collect());

        return Inertia::render('Contact/Index', [
            'messages' => $messages,
        ]);
    }

    public function send(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'topik' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string', 'max:5000'],
            'nama' => [$user ? 'nullable' : 'required', 'string', 'max:150'],
            'email' => [$user ? 'nullable' : 'required', 'email', 'max:255'],
        ]);

        $nama = $user?->name ?? $validated['nama'];
        $email = $user?->email ?? $validated['email'];

        ContactMessage::create([
            'user_id' => $user?->id,
            'nama' => $nama,
            'email' => $email,
            'topik' => $validated['topik'],
            'pesan' => $validated['pesan'],
            'status' => 'baru',
        ]);

        Log::info('Contact form submitted', [
            'from' => $email,
            'name' => $nama,
            'topik' => $validated['topik'],
        ]);

        return redirect()
            ->route('contact.index')
            ->with('success', 'Pesan berhasil dikirim. Terima kasih, akan dibalas dalam 1–2 hari kerja.');
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'status' => 'required|in:baru,dibaca,selesai',
        ]);

        $contactMessage->update($validated);

        return back()->with('success', 'Status pesan diperbarui.');
    }
}
