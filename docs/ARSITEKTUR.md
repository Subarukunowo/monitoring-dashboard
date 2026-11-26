# Arsitektur Sistem & Skema Basis Data — MaintainNow

Dokumen ini menjelaskan arsitektur aplikasi, alur otorisasi berbasis peran, serta skema relasi basis data pada **MaintainNow**.

---

## 1. Arsitektur Aplikasi

MaintainNow menggunakan pola **Monolith Modern (Laravel 12 + Inertia.js + React TypeScript)**:

```mermaid
flowchart LR
    Browser["Peramban (React + shadcn/ui)"] -->|"Inertia XHR / HTTP"| Middleware["Middleware: auth, admin, HandleInertiaRequests"]
    Middleware --> Controller["Laravel Controllers"]
    Controller -->|"Eloquent ORM"| DB[("MySQL / SQLite")]
    Controller -->|"Storage Public Disk"| Files["storage/app/public/laporan & dokumen"]
    Controller -->|"Inertia::render()"| Browser
```

### Pembagian Direktori Utama

- `app/Http/Controllers/` — Logika bisnis dashboard, laporan, RAB, BQ, tutup kontrak, dokumentasi, master data, autentikasi, dan kontak.
- `app/Http/Middleware/` — `HandleInertiaRequests` (berbagi sesi user, flash message, ringkasan antrean navigasi) dan `EnsureAdmin` (proteksi rute administrator).
- `app/Models/` — Model Eloquent (`User`, `Laporan`, `Area`, `Pelanggan`, `Pengawas`, `JenisPekerjaan`, `Status`, `Dokumen`, `BqItem`, `ContactMessage`).
- `resources/js/Layouts/AppLayout.tsx` — Shell aplikasi (*Open Plane* Achromatic Ledger) dengan pemisahan navigasi Petugas dan Administrator.
- `resources/js/components/ui/` — Komponen primitif **shadcn/ui** berbasis Radix UI dan Tailwind CSS v4.
- `resources/js/Pages/` — Halaman React TypeScript untuk setiap modul.

---

## 2. Skema Relasi Basis Data (ERD)

```mermaid
erDiagram
    USERS ||--o{ LAPORAN : "menutup kontrak (closed_by)"
    USERS ||--o{ CONTACT_MESSAGES : "mengirim"
    AREAS ||--o{ LAPORAN : "memiliki"
    PELANGGAN ||--o{ LAPORAN : "melaporkan"
    PENGAWAS ||--o{ LAPORAN : "mengawasi"
    JENIS_PEKERJAAN ||--o{ LAPORAN : "mengkategorikan"
    STATUS ||--o{ LAPORAN : "menentukan status"
    LAPORAN ||--o{ DOKUMEN : "melampirkan"
    LAPORAN ||--o{ BQ_ITEMS : "merinci"

    USERS {
        bigint id PK
        string name UK
        string email
        string role
        string password
    }

    LAPORAN {
        bigint id PK
        string no_insiden UK
        date tanggal_laporan
        time jam_laporan
        bigint id_pelanggan FK
        bigint id_area FK
        bigint id_pengawas FK
        bigint id_jenis_pekerjaan FK
        bigint id_status FK
        date tanggal_survei
        decimal nilai_rab
        string status_rab
        string no_sap
        date tanggal_selesai
        boolean is_closed
        timestamp closed_at
        bigint closed_by FK
    }

    BQ_ITEMS {
        bigint id PK
        bigint id_laporan FK
        string kode_material
        string uraian
        string kategori
        string satuan
        decimal volume
        decimal harga_satuan
        decimal subtotal
    }

    DOKUMEN {
        bigint id PK
        bigint id_laporan FK
        enum tipe
        string nama_file
        string path_file
        string mime_type
        int ukuran_file
    }
```

---

## 3. Matriks Hak Akses (User vs Admin)

| Kemampuan | Petugas (`user`) | Administrator (`admin`) |
| --- | :---: | :---: |
| Ringkasan Operasional & Grafik | Ya | Ya |
| CRUD Laporan Insiden & Upload Berkas | Ya | Ya |
| Susun Item Bill of Quantity & Sinkron RAB | Ya | Ya |
| Ajukan RAB (`draft` / `diajukan`) | Ya | Ya |
| Setujui RAB (`disetujui`) | Tidak | Ya |
| Lihat Checklist Tutup Kontrak | Ya | Ya |
| Tutup & Buka Kembali Kontrak | Tidak | Ya |
| Kelola Dokumentasi Pekerjaan & Administrasi | Ya | Ya |
| CRUD Master Data (Area, Pelanggan, Pengawas, Jenis, Status) | Tidak (HTTP 403) | Ya |
| Ubah Status Kotak Masuk Dukungan | Tidak (HTTP 403) | Ya |
