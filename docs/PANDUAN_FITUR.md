# Panduan Fitur — MaintainNow

Dokumen ini merangkum alur kerja harian untuk Petugas Lapangan dan Administrator pada aplikasi **MaintainNow**.

---

## 1. Alur Petugas Lapangan (`petugas`)

1. **Masuk ke Sistem (`/login`)**: Gunakan kredensial petugas (`petugas` / `password123`) atau daftar akun baru di `/register`.
2. **Memantau Ringkasan (`/dashboard`)**:
   - Periksa kartu KPI atas untuk melihat jumlah insiden aktif, keterlambatan deadline, dan total nilai RAB.
   - Klik area pada panel *Distribusi per Area* atau gunakan kolom pencarian untuk menyaring work order.
3. **Mencatat & Memperbarui Laporan Insiden (`/laporan`)**:
   - Gunakan filter kartu status (*Semua*, *Terbuka*, *Sedang Dikerjakan*, *Ditunda*, *Selesai*) untuk memfokuskan pekerjaan.
   - Buka `/laporan/create` untuk mencatat insiden baru beserta foto pekerjaan, foto administrasi, dan dokumen pendukung.
4. **Menyusun Bill of Quantity & Mengajukan RAB (`/bill-of-quantity`, `/rab`)**:
   - Pilih nomor insiden pada halaman **Bill of Quantity**, tambahkan rincian material/jasa/sipil, lalu klik **Sinkronkan ke Nilai RAB**.
   - Buka halaman **RAB**, klik **Atur RAB**, masukkan nomor SAP/TUG jika sudah terbit, dan ubah status menjadi **Diajukan ke Admin**.
5. **Mengelola Dokumentasi (`/dokumentasi-pekerjaan`, `/dokumentasi-administrasi`)**:
   - Unggah foto hasil lapangan atau berkas administrasi langsung ke work order terkait dan periksa pratinjau gambar.
6. **Menghubungi Developer (`/contact`)**:
   - Kirim laporan bug atau permintaan fitur dan pantau status penanganannya.

---

## 2. Alur Administrator (`admin`)

Selain seluruh kemampuan petugas di atas, Administrator memiliki kontrol penuh atas tata kelola sistem:

1. **Verifikasi & Persetujuan RAB (`/rab`)**:
   - Filter RAB berstatus *Diajukan*, periksa kecocokan nilai terhadap item BQ, lalu setel status menjadi **Disetujui**.
2. **Penutupan Kontrak (`/tutup-kontrak`)**:
   - Periksa 5 indikator kesiapan kontrak per insiden (*Selesai*, *SAP*, *RAB*, *Foto Kerja*, *Dok. Admin*).
   - Klik **Tutup Kontrak** untuk mengarsipkan berita acara penutupan, atau **Buka Lagi** jika ada revisi.
3. **Manajemen Master Data (`/areas`, `/pelanggans`, `/pengawas`, `/jenis_pekerjaan`, `/status`)**:
   - Tambah, ubah, dan hapus data referensi wilayah distribusi, pelanggan PLN, pengawas, jenis pekerjaan, dan status kerja. Sistem secara otomatis melindungi data referensi yang masih tertaut ke laporan aktif.
4. **Tindak Lanjut Kotak Masuk Dukungan (`/contact`)**:
   - Perbarui status pesan dari petugas/pengguna (`baru` -> `dibaca` -> `selesai`).
