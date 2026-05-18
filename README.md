# TARIKSIS (TARIK data Sistem Informasi rS)

TARIKSIS adalah aplikasi berbasis web yang dirancang khusus untuk mempermudah proses penarikan data (data extraction) dari Sistem Informasi Manajemen Rumah Sakit (SIMRS) guna keperluan pelaporan, analisis, dan integrasi data lainnya.

## Tujuan Project
Project ini bertujuan untuk menyediakan antarmuka yang intuitif dan cepat bagi staf rumah sakit (khususnya unit Farmasi dan Laboratorium) dalam mengekstraksi data operasional tanpa harus melakukan query manual ke database utama secara langsung.

## Fitur Utama
- **Penarikan Data Farmasi**:
    - Penarikan Data Rawat Inap
    - Penarikan Data Rawat Jalan
    - Penerimaan Obat dan BHP Farmasi
    - Pemberian Obat dan BHP
- **Penarikan Data Laboratorium**:
    - Waktu Tunggu Hasil Lab Rawat Jalan
    - Waktu Tunggu Hasil Lab Rawat Inap
    - Waktu Tunggu Hasil Lab Gabungan
- **Ekspor Data**: Mendukung ekspor hasil tarikan data ke format **Excel** dan **PDF**.
- **Dashboard**: Visualisasi ringkas mengenai status penarikan data.

## SOP Pengembangan (Standard Operating Procedure)

Untuk menjaga kualitas kode dan konsistensi tampilan, setiap pengembang **WAJIB** mengikuti aturan berikut:

### 1. Arsitektur & Logika (SRP & Repository Pattern)
- **Single Responsibility Principle (SRP)**: Pisahkan logika database dari Controller.
- **Repository Pattern**: Semua query database (khususnya query kompleks ke database SIMRS) harus diletakkan di dalam folder `app/Repositories/`.
- **Controller**: Hanya bertugas menerima request, memanggil repository, dan mengembalikan view. Jangan ada query mentah (Raw SQL/Query Builder) di dalam Controller.

### 2. Struktur Folder View
Struktur folder `resources/views` harus disusun berdasarkan unit kerja secara hirarkis:
- Gunakan folder per unit (misal: `farmasi`, `laboratorium`, `radiologi`).
- Di dalam folder unit, buat sub-folder per fitur/modul (misal: `ralan`, `ranap`).
- Setiap fitur wajib memiliki file `index.blade.php` untuk tampilan utama dan `pdf.blade.php` untuk template ekspor PDF.
- Contoh: `resources/views/laboratorium/ralan/index.blade.php`.

### 3. Standar UI/UX (Premium Aesthetics)
- **Empty State**: Dilarang menggunakan halaman kosong. Gunakan komponen "Empty State" dengan ikon SVG dan instruksi yang jelas bagi pengguna sebelum data ditarik.
- **Konsistensi Visual**: Gunakan skema warna Olive Green (`primary`) untuk elemen utama.
- **Feedback Visual**: Gunakan indikator warna untuk metrik kritis (misal: merah untuk durasi tunggu yang lama, hijau untuk yang cepat).
- **Modern Components**: Gunakan card dengan *rounded-2xl/3xl*, bayangan halus (*shadow-sm*), dan efek hover pada tabel.

### 4. Keamanan & Database
- **Read-Only Focus**: Seluruh query penarikan data harus bersifat `SELECT`. Dilarang melakukan `INSERT`, `UPDATE`, atau `DELETE` pada tabel utama SIMRS tanpa persetujuan tim IT Senior.
- **Database Reference**: Gunakan `sik.sql` sebagai panduan struktur sebelum membangun query.

### 5. Sistem Logging (Audit Trail)
- **Pencatatan Aktivitas**: Setiap fitur penarikan data **WAJIB** melakukan pencatatan ke tabel `ExtractionLog` saat user mengeksekusi tombol "Tarik Data".
- **Parameter Log**: Data yang harus dicatat meliputi `username`, `filter_date`, dan `extraction_type` (Nama modul yang ditarik).
- **Tujuan**: Memastikan transparansi penggunaan data dan sinkronisasi jumlah transaksi pada dashboard utama.

### 6. Standar Ekspor File
- **Feedback Unduhan**: Dilarang menggunakan link unduhan langsung (`<a>` href). Gunakan fungsi global `handleDownload(url, filename)`.
- **Fitur Feedback**: Fungsi ini secara otomatis menampilkan:
    - Loader "Tunggu sebentar" saat file sedang disiapkan.
    - Notifikasi "Berhasil" jika unduhan selesai.
    - Notifikasi "Gagal" beserta alasan teknisnya jika terjadi error pada server.

### 7. Dokumentasi Bantuan & Penjelasan Sumber Data Halaman
- **Kewajiban Dokumentasi**: Setiap penambahan fitur atau modul baru **WAJIB** dilengkapi dengan dokumentasi bantuan interaktif langsung di halaman terkait (menggunakan ikon bantuan hijau `i` di samping judul utama dan pop-up modal informasi yang menarik).
- **Isi Dokumentasi Bantuan**:
    - **Deskripsi Fitur**: Penjelasan fungsionalitas menu agar mudah dipahami oleh staf non-IT.
    - **Formula Kalkulasi (Jika Ada)**: Sertakan formula matematika dasar serta contoh penulisan formula Excel (seperti `=AVERAGE(...)` atau `=F2-E2`) yang ramah pengguna. *Jika halaman tidak memiliki kalkulasi matematis/formula Excel, bagian ini tidak perlu dipaksakan.*
    - **Pemetaan Basis Data (SIMRS Khanza)**: Cantumkan tabel transparan yang memetakan kolom tampilan UI ke nama tabel SQL, nama kolom SQL, serta keterangan filter kueri yang digunakan. Ini sangat penting untuk memfasilitasi audit trail dan transparansi data.

## Teknologi yang Digunakan
- **Framework**: [Laravel 10](https://laravel.com)
- **Frontend Logic**: [Alpine.js](https://alpinejs.dev)
- **Styling**: [Tailwind CSS](https://tailwindcss.com) (via CDN/Vite)
- **Database**: MySQL / MariaDB (Terhubung ke Database SIMRS Khanza)
- **Ekspor Excel**: [Laravel Excel (Maatwebsite)](https://laravel-excel.com/)
- **Ekspor PDF**: [Laravel-DomPDF](https://github.com/barryvdh/laravel-dompdf)
- **Icons**: SVG Icons (Heroicons style)

## ⚠️ Peringatan Penting (PERHATIKAN!)

> [!CAUTION]
> **JANGAN PERNAH** menjalankan perintah `php artisan migrate:fresh` atau `php artisan migrate:rollback` pada lingkungan produksi atau jika aplikasi sudah terhubung ke database utama SIMRS.

Aplikasi ini terhubung langsung ke **Database Utama SIMRS**. Menjalankan perintah migrasi yang bersifat destruktif dapat menghapus seluruh tabel di database rumah sakit yang sedang berjalan.

### Konfigurasi Database
Pastikan file `.env` telah dikonfigurasi dengan benar:
- Gunakan user database yang memiliki akses **READ-ONLY** jika memungkinkan untuk menghindari perubahan data yang tidak sengaja pada tabel utama SIMRS.
- Pastikan host dan port database dapat dijangkau dari server aplikasi.

## Referensi Struktur Database
Untuk mempelajari struktur tabel, relasi, dan skema database utama SIMRS Khanza, silakan merujuk pada file `sik.sql` yang telah disediakan di root directory project ini. File ini dapat digunakan sebagai referensi lokal untuk membangun query baru tanpa harus menebak struktur tabel langsung di database produksi.

## Cara Instalasi
1. Clone repository ini.
2. Jalankan `composer install`.
3. Salin `.env.example` ke `.env` dan sesuaikan koneksi database.
4. Jalankan `php artisan key:generate`.
5. Jalankan `npm install` dan `npm run dev` (jika menggunakan Vite).

---
Made by IT Staff RSIA IBI Surabaya © 2026
