# MaintainNow — Dashboard Monitoring Pemeliharaan Distribusi PLN

Sistem informasi manajemen work order, insiden jaringan distribusi, rencana anggaran biaya (RAB), Bill of Quantity (BQ), penutupan kontrak, serta arsip dokumentasi lapangan berbasis **Laravel 12**, **Inertia.js**, **React + TypeScript**, **Tailwind CSS v4**, dan **shadcn/ui**.

---

## Fitur Utama

| Modul | Akses | Deskripsi |
| --- | --- | --- |
| **Ringkasan Operasional** (`/dashboard`) | Admin & Petugas | KPI status pekerjaan, indikator keterlambatan SLA, nilai total RAB, grafik tren insiden 6 bulan, distribusi beban per area, dan daftar work order terbaru. |
| **Laporan Insiden** (`/laporan`) | Admin & Petugas | CRUD lengkap laporan gangguan/pekerjaan, filter status dan area, pencarian lintas relasi, tautan Google Maps, serta unggah foto pekerjaan, foto administrasi, dan dokumen pendukung. |
| **Rencana Anggaran Biaya (RAB)** (`/rab`) | Admin & Petugas | Pengelolaan nilai RAB, nomor SAP/TUG, pengajuan status (`draft` / `diajukan`), dan persetujuan (`disetujui` oleh Admin). |
| **Bill of Quantity (BQ)** (`/bill-of-quantity`) | Admin & Petugas | Rincian item material, jasa, dan pekerjaan sipil per insiden dengan kalkulasi subtotal otomatis dan sinkronisasi satu klik ke nilai RAB laporan. |
| **Tutup Kontrak** (`/tutup-kontrak`) | Admin & Petugas | Daftar periksa kelengkapan administrasi (status selesai, nomor SAP, nilai RAB, foto pekerjaan, dokumen administrasi) serta proses tutup/buka kontrak oleh Admin. |
| **Dokumentasi Pekerjaan & Administrasi** (`/dokumentasi-pekerjaan`, `/dokumentasi-administrasi`) | Admin & Petugas | Galeri terpusat foto lapangan dan berkas administrasi dengan filter area/insiden, pratinjau lightbox, unduh, unggah langsung, dan hapus. |
| **Master Data** (`/areas`, `/pelanggans`, `/pengawas`, `/jenis_pekerjaan`, `/status`) | **Admin** | Pengelolaan Area Distribusi, Pelanggan PLN/Non-Pelanggan, Pengawas Lapangan, Jenis Pekerjaan, dan Status Kerja. |
| **Hubungi Developer** (`/contact`) | Publik / Auth | Formulir pelaporan kendala teknis & saran fitur yang tersimpan ke basis data dengan kotak masuk pembaruan status untuk Admin. |

---

## Peran Pengguna (Role)

1. **Administrator (`role: admin`)**
   - Akses penuh seluruh modul operasional, persetujuan RAB (`disetujui`), penutupan/pembukaan kembali kontrak, manajemen status kotak masuk dukungan, dan CRUD seluruh Master Data.
2. **Petugas Lapangan (`role: user`)**
   - Akses dashboard operasional, pembuatan/pembaruan laporan insiden, penyusunan Bill of Quantity, pengajuan RAB, pengunggahan dokumentasi lapangan/administrasi, dan melihat kesiapan tutup kontrak (tanpa akses ubah Master Data atau persetujuan kontrak).

---

## Teknologi yang Digunakan

- **Backend**: PHP 8.2+, Laravel 12, Inertia.js Laravel (`inertiajs/inertia-laravel`)
- **Frontend**: React 19, TypeScript, `@inertiajs/react`, Tailwind CSS v4, Radix UI + **shadcn/ui**, Lucide Icons
- **Database**: MySQL (`pln_dashboard_monitoring` untuk lokal/produksi) & SQLite `:memory:` untuk pengujian PHPUnit
- **Pengujian & Kualitas Kode**: PHPUnit 11 + Xdebug Code Coverage, Laravel Pint, TypeScript (`tsc`), ESLint 9

---

## Instalasi & Menjalankan Secara Lokal

1. **Instal dependensi PHP dan JavaScript:**
   ```bash
   composer install
   npm install
   ```

2. **Konfigurasi lingkungan (`.env`):**
   Pastikan database MySQL aktif dan sesuaikan `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pln_dashboard_monitoring
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Migrasi, Seeder, Storage Link, dan Build Aset:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   ```

4. **Jalankan server pengembangan:**
   ```bash
   php artisan serve
   ```
   Buka `http://127.0.0.1:8000` di peramban.

---

## Akun Demo Bawaan (Seeder)

| Peran | Nama Pengguna | Password |
| --- | --- | --- |
| **Administrator** | `admin` | `password123` |
| **Petugas Lapangan** | `petugas` | `password123` |

---

## Linter & Pengujian (Test Coverage)

```bash
# Linter PHP (Laravel Pint)
php vendor/bin/pint --test

# Typecheck & Linter Frontend (TypeScript + ESLint)
npm run typecheck
npm run lint

# Build Produksi Vite
npm run build

# Pengujian PHPUnit dengan Laporan Cakupan Kode (Xdebug)
php -d zend_extension=php_xdebug.dll -d xdebug.mode=coverage vendor/phpunit/phpunit/phpunit --coverage-text
```

---

## Dokumentasi Lanjutan

- [Arsitektur Sistem & Skema Basis Data](docs/ARSITEKTUR.md)
- [Panduan Penggunaan Fitur](docs/PANDUAN_FITUR.md)
- [Catatan Perubahan (Changelog)](CHANGELOG.md)
