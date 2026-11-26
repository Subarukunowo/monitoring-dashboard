<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\JenisPekerjaanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengawasController;
use App\Http\Controllers\RabController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TutupKontrakController;
use Illuminate\Support\Facades\Route;

// ── GUEST ONLY ─────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// ── AUTHENTICATED ──────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Work Order / Laporan
    Route::resource('laporan', LaporanController::class);

    // Dokumen per laporan
    Route::post('laporan/{laporan}/dokumen', [DokumenController::class, 'store'])->name('laporan.dokumen.store');
    Route::delete('dokumen/{dokumen}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');

    // Fitur operasional (sebelumnya coming-soon)
    Route::get('/rab', [RabController::class, 'index'])->name('rab.index');
    Route::patch('/rab/{laporan}', [RabController::class, 'update'])->name('rab.update');

    Route::get('/bill-of-quantity', [BqController::class, 'index'])->name('bq.index');
    Route::post('/bill-of-quantity/{laporan}/items', [BqController::class, 'store'])->name('bq.items.store');
    Route::put('/bill-of-quantity/items/{bqItem}', [BqController::class, 'update'])->name('bq.items.update');
    Route::delete('/bill-of-quantity/items/{bqItem}', [BqController::class, 'destroy'])->name('bq.items.destroy');
    Route::post('/bill-of-quantity/{laporan}/sync-rab', [BqController::class, 'syncToRab'])->name('bq.sync-rab');

    Route::get('/tutup-kontrak', [TutupKontrakController::class, 'index'])->name('tutup-kontrak.index');
    Route::get('/dokumentasi-pekerjaan', [DokumentasiController::class, 'pekerjaan'])->name('dokumentasi.pekerjaan');
    Route::get('/dokumentasi-administrasi', [DokumentasiController::class, 'administrasi'])->name('dokumentasi.administrasi');

    // Redirect legacy coming-soon URLs to real modules
    Route::get('/coming-soon/{feature}', function (string $feature) {
        return match ($feature) {
            'rab' => redirect()->route('rab.index'),
            'tutup-kontrak' => redirect()->route('tutup-kontrak.index'),
            'dokumentasi-pekerjaan' => redirect()->route('dokumentasi.pekerjaan'),
            'dokumentasi-administrasi' => redirect()->route('dokumentasi.administrasi'),
            'bill-of-quantity' => redirect()->route('bq.index'),
            default => abort(404),
        };
    })->name('coming-soon');

    // Admin-only Master Data & Contract Closure Approval
    Route::middleware('admin')->group(function () {
        Route::resource('areas', AreaController::class)->except(['show']);
        Route::resource('pelanggans', PelangganController::class)->except(['show']);
        Route::resource('pengawas', PengawasController::class)->parameters(['pengawas' => 'pengawas'])->except(['show']);
        Route::resource('jenis_pekerjaan', JenisPekerjaanController::class)->except(['show']);
        Route::resource('status', StatusController::class)->except(['show']);

        Route::post('/tutup-kontrak/{laporan}/close', [TutupKontrakController::class, 'close'])->name('tutup-kontrak.close');
        Route::post('/tutup-kontrak/{laporan}/reopen', [TutupKontrakController::class, 'reopen'])->name('tutup-kontrak.reopen');
        Route::patch('/contact/{contactMessage}/status', [ContactController::class, 'updateStatus'])->name('contact.status');
    });
});

// ── CONTACT DEVELOPER ─────────────────────────────────
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
