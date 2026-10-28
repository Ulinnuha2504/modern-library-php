# 📚 Sistem Informasi Perpustakaan (Modern Green Elegant)

![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Architecture](https://img.shields.io/badge/Architecture-Modular%20MVC-success)

Sistem Informasi Perpustakaan berbasis web dengan arsitektur **PHP 8+ Native Modular MVC** dan basis data relasional **MySQL/MariaDB**. Dirancang khusus untuk operasional perpustakaan sekolah dan pesantren dengan antarmuka bertema **Modern Green Elegant** yang bersih, responsif, dan mudah digunakan.

---

## 🌟 Fitur Utama

* **Katalog Buku & Manajemen Stok:** Kontrol ketat integritas stok (`total = tersedia + dipinjam + rusak + hilang`), pencarian instan AJAX, filter kategori & rak, serta riwayat sirkulasi per buku.
* **Keanggotaan Santri:** Manajemen data santri terstruktur (NIS unik, kelas, kamar asrama, kontak wali) dilengkapi status keaktifan.
* **Sirkulasi Peminjaman (Multi-Buku):** Peminjaman fleksibel hingga beberapa buku per transaksi menggunakan *database transaction* (ACID) dan *pessimistic locking* (`SELECT ... FOR UPDATE`) untuk mencegah *race condition*.
* **Meja Pengembalian & Denda Dinamis:** Asesmen kondisi buku saat kembali (Baik, Rusak Ringan, Rusak Berat, Hilang) serta kalkulasi denda otomatis berdasarkan hari keterlambatan dan kerusakan fisik.
* **Dashboard & Visualisasi Data:** Metrik statistik *real-time* dan grafik interaktif bertenaga Chart.js (tren peminjaman 6 bulan, buku terpopuler, dan distribusi kategori).
* **Pusat Laporan Terpadu:** Rekapitulasi buku, santri, peminjaman, dan kas denda dengan tampilan siap cetak resmi (*print-friendly*) dan ekspor format CSV/Excel.
* **Keamanan & Kontrol Akses (RBAC):** Otentikasi berbasis *password hash* (BCRYPT), proteksi CSRF token di seluruh formulir dan permintaan Fetch API, filter hak akses (Admin, Petugas, Viewer), serta *Activity Log* (*audit trail*) yang tidak dapat dihapus.

---

## 🏗️ Struktur Proyek (Modular MVC)

```text
perpustakaan/
├── app/
│   ├── Core/           # Framework kernel (Router, Database, Model, Controller, Session)
│   ├── Controllers/    # Layer pengendali logika alur aplikasi
│   ├── Middleware/     # Filter otentikasi, otorisasi RBAC, dan validasi CSRF
│   ├── Models/         # Abstraksi data dan query relasional
│   ├── Services/       # Logika bisnis sirkulasi dan pencatatan audit log
│   └── Helpers/        # Fungsi bantuan global (URL, sanitasi, format rupiah)
├── config/             # Konfigurasi aplikasi dan database
├── database/           # Skema DDL dan seeder data awal (database.sql)
├── public/             # Dokumen root web server (Front Controller & assets statis)
├── resources/views/    # Antarmuka pengguna (layouts, modular components, views)
├── scripts/            # Skrip pemeliharaan berkala (backup otomatis database)
└── tests/              # Test suite pengujian otomatis (13 skenario uji)
```

---

## 💻 Kebutuhan Sistem

* PHP >= 8.1 (Ekstensi aktif: `pdo_mysql`, `mbstring`, `gd`, `json`)
* MySQL >= 8.0 atau MariaDB >= 10.4
* Web Server: Apache (dengan modul `mod_rewrite`) atau Nginx
* Web Browser modern (Chrome, Firefox, Safari, Edge)

---

## 🚀 Panduan Instalasi Lokal

### 1. Kloning Repositori
```bash
git clone https://github.com/username-anda/sistem-informasi-perpustakaan.git
cd sistem-informasi-perpustakaan
```

### 2. Impor Basis Data
Buat basis data baru bernama `perpustakaan` di phpMyAdmin atau MySQL CLI, lalu impor berkas skema:
```bash
mysql -u root -p perpustakaan < database/database.sql
```

### 3. Konfigurasi Lingkungan (.env)
Salin berkas `.env.example` menjadi `.env`, lalu sesuaikan kredensial basis data Anda:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=perpustakaan
DB_USER=root
DB_PASS=

APP_URL=http://localhost/sistem-informasi-perpustakaan/public
APP_TIMEZONE=Asia/Jakarta
```

### 4. Jalankan Aplikasi
* **Menggunakan Virtual Host (Disarankan):** Arahkan *Document Root* ke folder `public/`.
* **Menggunakan Built-in PHP Server (Pengujian Cepat):**
  ```bash
  php -S localhost:8000 -t public
  ```
  Lalu buka `http://localhost:8000` pada peramban.

---

## 🔐 Akun Bawaan (Default Credentials)

| Role | Username | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `password` | Akses penuh (Pengguna, Pengaturan, Sirkulasi, Log) |
| **Petugas** | `petugas` | `password` | Operasional transaksi, data buku, santri, dan laporan |
| **Viewer** | `viewer` | `password` | Khusus melihat data dan mencetak laporan (*read-only*) |

> ⚠️ *Penting: Segera ganti kata sandi bawaan ini setelah aplikasi dipasang di lingkungan produksi.*

---

## 📄 Lisensi

Proyek ini dirilis di bawah lisensi [MIT License](LICENSE).
