<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengawasController;
use App\Http\Controllers\JenisPekerjaanController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

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

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::resource('areas', AreaController::class);
    Route::resource('pelanggans', PelangganController::class);
    Route::resource('pengawas', PengawasController::class);
    Route::resource('jenis_pekerjaan', JenisPekerjaanController::class);
    Route::resource('status', StatusController::class);

    // Work Order / Laporan
    Route::resource('laporan', LaporanController::class);

    // Dokumen
    Route::post('laporan/{laporan}/dokumen', [DokumenController::class, 'store'])->name('laporan.dokumen.store');
    Route::delete('dokumen/{dokumen}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
});