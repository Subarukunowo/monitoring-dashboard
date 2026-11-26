# Changelog

Semua perubahan penting pada proyek **MaintainNow** dicatat pada dokumen ini.

## [2.0.0] - 2026-11-27

### Ditambahkan
- Migrasi antarmuka penuh ke **Inertia.js + React + TypeScript + shadcn/ui** dengan tema **Achromatic Ledger** (tipografi Geist & Geist Mono, tabular-nums, hierarki data berfokus keterbacaan, tanpa dekorasi generik).
- Dukungan pemisahan peran (**Administrator** & **Petugas Lapangan**) melalui kolom `users.role`, middleware `EnsureAdmin`, serta seeder akun demo (`UserSeeder`).
- Modul **RAB** (`/rab`) lengkap dengan filter status, nomor SAP/TUG, dan persetujuan Admin.
- Modul **Bill of Quantity (BQ)** (`/bill-of-quantity`) dengan tabel `bq_items` dan tombol sinkronisasi total biaya ke nilai RAB.
- Modul **Tutup Kontrak** (`/tutup-kontrak`) lengkap dengan checklist 5 syarat kelengkapan serta aksi tutup/buka kontrak oleh Admin.
- Modul **Dokumentasi Pekerjaan** (`/dokumentasi-pekerjaan`) dan **Dokumentasi Administrasi** (`/dokumentasi-administrasi`) lengkap dengan galeri, unggah, unduh, pratinjau dialog, dan hapus berkas.
- Halaman CRUD Master Data yang sebelumnya belum tersedia (`areas`, `pelanggans`, `pengawas`, `jenis_pekerjaan`, `status`).
- Persistensi pesan dukungan pada tabel `contact_messages` di modul **Hubungi Developer**.
- Paket pengujian otomatis PHPUnit (Feature & Unit Test) beserta pelaporan cakupan kode Xdebug.

### Diperbaiki
- Memperbaiki validasi `unique:pelanggans` di `PelangganController` menjadi `unique:pelanggan` sesuai nama tabel basis data.
- Memperbaiki kueri pencarian tanggal `DATE_FORMAT` di `LaporanController` agar kompatibel dengan SQLite saat pengujian dan MySQL saat produksi.
- Memperbaiki `LaporanSeeder` agar menyimpan kolom `nama` pelanggan dengan benar.
- Memperbaiki halaman `/contact` agar aman diakses oleh tamu maupun pengguna terautentikasi tanpa error `null` pada `auth()->user()`.
- Memperbaiki migrasi `add_dokumen_enum` dan `change_pending_to_open_in_status_table` agar aman dijalankan pada driver SQLite maupun MySQL.
