-- ============================================================================
-- SISTEM INFORMASI PERPUSTAKAAN (MODERN GREEN ELEGANT)
-- Database Architecture & Initial Seed Data
-- Target Engine: MySQL 8.0+ / MariaDB 10.4+ (InnoDB Engine)
-- Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- Timezone: Asia/Jakarta (+07:00)
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- ----------------------------------------------------------------------------
-- 1. DATABASE INITIALIZATION
-- ----------------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `perpustakaan` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `perpustakaan`;

-- ----------------------------------------------------------------------------
-- 2. TABLE: users (Manajemen Pengguna & Hak Akses)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'petugas', 'viewer') NOT NULL DEFAULT 'petugas',
    `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    `last_login` DATETIME NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_username` (`username`),
    UNIQUE KEY `uk_users_email` (`email`),
    INDEX `idx_users_role_status` (`role`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. TABLE: santri (Data Siswa / Santri Pesantren)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `santri`;
CREATE TABLE `santri` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nis` VARCHAR(30) NOT NULL,
    `nisn` VARCHAR(30) NULL DEFAULT NULL,
    `nama` VARCHAR(120) NOT NULL,
    `jenis_kelamin` ENUM('L', 'P') NOT NULL DEFAULT 'L',
    `kelas` VARCHAR(50) NOT NULL,
    `kamar` VARCHAR(50) NOT NULL,
    `tahun_masuk` YEAR NOT NULL,
    `nama_wali` VARCHAR(100) NULL DEFAULT NULL,
    `no_hp` VARCHAR(20) NULL DEFAULT NULL,
    `alamat` TEXT NULL DEFAULT NULL,
    `status` ENUM('aktif', 'nonaktif', 'lulus', 'keluar') NOT NULL DEFAULT 'aktif',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_santri_nis` (`nis`),
    INDEX `idx_santri_nama` (`nama`),
    INDEX `idx_santri_kelas_status` (`kelas`, `status`),
    INDEX `idx_santri_kamar` (`kamar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. TABLE: kategori (Taksonomi / Kategori Buku)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(100) NOT NULL,
    `deskripsi` VARCHAR(255) NULL DEFAULT NULL,
    `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_kategori_nama` (`nama`),
    INDEX `idx_kategori_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. TABLE: rak (Lokasi Fisik Rak Penyimpanan Buku)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `rak`;
CREATE TABLE `rak` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_rak` VARCHAR(30) NOT NULL,
    `nama_rak` VARCHAR(100) NOT NULL,
    `lokasi` VARCHAR(100) NOT NULL,
    `deskripsi` VARCHAR(255) NULL DEFAULT NULL,
    `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_rak_kode` (`kode_rak`),
    INDEX `idx_rak_lokasi` (`lokasi`),
    INDEX `idx_rak_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. TABLE: buku (Katalog Buku & Manajemen Inventaris Stok)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `buku`;
CREATE TABLE `buku` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_buku` VARCHAR(50) NOT NULL,
    `isbn` VARCHAR(30) NULL DEFAULT NULL,
    `judul` VARCHAR(255) NOT NULL,
    `penulis` VARCHAR(150) NOT NULL,
    `penerbit` VARCHAR(150) NOT NULL,
    `tahun_terbit` SMALLINT UNSIGNED NOT NULL,
    `kategori_id` INT UNSIGNED NOT NULL,
    `rak_id` INT UNSIGNED NOT NULL,
    `jumlah_total` INT NOT NULL DEFAULT 0,
    `jumlah_tersedia` INT NOT NULL DEFAULT 0,
    `jumlah_dipinjam` INT NOT NULL DEFAULT 0,
    `jumlah_rusak` INT NOT NULL DEFAULT 0,
    `jumlah_hilang` INT NOT NULL DEFAULT 0,
    `cover` VARCHAR(255) NULL DEFAULT NULL,
    `deskripsi` TEXT NULL DEFAULT NULL,
    `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_buku_kode` (`kode_buku`),
    INDEX `idx_buku_isbn` (`isbn`),
    INDEX `idx_buku_judul` (`judul`),
    INDEX `idx_buku_penulis` (`penulis`),
    INDEX `idx_buku_kategori` (`kategori_id`),
    INDEX `idx_buku_rak` (`rak_id`),
    INDEX `idx_buku_status` (`status`),
    CONSTRAINT `fk_buku_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_buku_rak` FOREIGN KEY (`rak_id`) REFERENCES `rak` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `chk_buku_stok_nonnegatif` CHECK (
        `jumlah_total` >= 0 AND 
        `jumlah_tersedia` >= 0 AND 
        `jumlah_dipinjam` >= 0 AND 
        `jumlah_rusak` >= 0 AND 
        `jumlah_hilang` >= 0
    ),
    CONSTRAINT `chk_buku_stok_keseimbangan` CHECK (
        `jumlah_total` = (`jumlah_tersedia` + `jumlah_dipinjam` + `jumlah_rusak` + `jumlah_hilang`)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. TABLE: peminjaman (Header Transaksi Sirkulasi)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `peminjaman`;
CREATE TABLE `peminjaman` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_transaksi` VARCHAR(50) NOT NULL,
    `santri_id` INT UNSIGNED NOT NULL,
    `tanggal_pinjam` DATE NOT NULL,
    `tanggal_jatuh_tempo` DATE NOT NULL,
    `tanggal_kembali` DATE NULL DEFAULT NULL,
    `status` ENUM('dipinjam', 'sebagian_dikembalikan', 'selesai', 'terlambat', 'dibatalkan') NOT NULL DEFAULT 'dipinjam',
    `denda` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `catatan` TEXT NULL DEFAULT NULL,
    `petugas_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_peminjaman_kode` (`kode_transaksi`),
    INDEX `idx_peminjaman_santri` (`santri_id`),
    INDEX `idx_peminjaman_status` (`status`),
    INDEX `idx_peminjaman_tanggal_pinjam` (`tanggal_pinjam`),
    INDEX `idx_peminjaman_jatuh_tempo` (`tanggal_jatuh_tempo`),
    INDEX `idx_peminjaman_petugas` (`petugas_id`),
    CONSTRAINT `fk_peminjaman_santri` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_peminjaman_petugas` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. TABLE: peminjaman_detail (Detail Item Buku per Transaksi)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `peminjaman_detail`;
CREATE TABLE `peminjaman_detail` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `peminjaman_id` INT UNSIGNED NOT NULL,
    `buku_id` INT UNSIGNED NOT NULL,
    `tanggal_kembali` DATE NULL DEFAULT NULL,
    `kondisi_pinjam` ENUM('baik', 'rusak_ringan') NOT NULL DEFAULT 'baik',
    `kondisi_kembali` ENUM('baik', 'rusak_ringan', 'rusak_berat', 'hilang') NULL DEFAULT NULL,
    `denda` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `status` ENUM('dipinjam', 'kembali', 'hilang') NOT NULL DEFAULT 'dipinjam',
    `catatan` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_peminjaman_detail_peminjaman` (`peminjaman_id`),
    INDEX `idx_peminjaman_detail_buku` (`buku_id`),
    INDEX `idx_peminjaman_detail_status` (`status`),
    CONSTRAINT `fk_peminjaman_detail_header` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_peminjaman_detail_buku` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. TABLE: settings (Konfigurasi Dinamis Aplikasi)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(60) NOT NULL,
    `setting_value` TEXT NOT NULL,
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'sirkulasi',
    `deskripsi` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_settings_key` (`setting_key`),
    INDEX `idx_settings_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 10. TABLE: activity_logs (Audit Trail Aktivitas Sistem)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NULL DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_activity_logs_user` (`user_id`),
    INDEX `idx_activity_logs_module` (`module`),
    INDEX `idx_activity_logs_action` (`action`),
    INDEX `idx_activity_logs_created_at` (`created_at`),
    CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- SEED DATA (DATA AWAL PRODUKSI & PENGUJIAN)
-- Password Default:
--   admin    -> password
--   petugas  -> password
--   viewer   -> password
-- (Menggunakan PHP password_hash default BCRYPT cost 10)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================================================

-- 1. USERS
INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `role`, `status`, `last_login`, `created_at`) VALUES
(1, 'Administrator Perpustakaan', 'admin@pesantren.sch.id', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'aktif', NULL, NOW()),
(2, 'Ust. Ridwan Kamil', 'ridwan@pesantren.sch.id', 'petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', 'aktif', NULL, NOW()),
(3, 'Pengawas Perpustakaan', 'viewer@pesantren.sch.id', 'viewer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'viewer', 'aktif', NULL, NOW());

-- 2. SETTINGS
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `deskripsi`) VALUES
('nama_perpustakaan', 'Perpustakaan Baitul Hikmah', 'profil', 'Nama resmi perpustakaan institusi'),
('alamat_perpustakaan', 'Komplek Pesantren Darul Ulum, Jl. Kyai Mojo No. 45', 'profil', 'Alamat fisik perpustakaan'),
('telepon_perpustakaan', '081234567890', 'profil', 'Nomor telepon kontak perpustakaan'),
('email_perpustakaan', 'perpustakaan@pesantren.sch.id', 'profil', 'Alamat surel kontak resmi perpustakaan'),
('denda_per_hari', '1000', 'sirkulasi', 'Nominal denda keterlambatan pengembalian per buku per hari (Rupiah)'),
('maksimal_pinjaman', '3', 'sirkulasi', 'Batas maksimal buku yang dapat dipinjam bersamaan oleh seorang santri'),
('lama_peminjaman', '7', 'sirkulasi', 'Masa durasi standar peminjaman buku dalam satuan hari'),
('denda_rusak_ringan', '5000', 'sirkulasi', 'Biaya ganti rugi pemeliharaan untuk buku kondisi rusak ringan'),
('denda_rusak_berat', '20000', 'sirkulasi', 'Biaya penanganan atau reparasi buku kondisi rusak berat');

-- 3. KATEGORI
INSERT INTO `kategori` (`id`, `nama`, `deskripsi`, `status`) VALUES
(1, 'Al-Qur\'an & Tafsir', 'Kitab tafsir, ilmu tajwid, dan kajian mushaf', 'aktif'),
(2, 'Hadits & Mushthalah', 'Kutubus Sittah, syarah hadits, dan dirayah hadits', 'aktif'),
(3, 'Fiqih & Ushul Fiqih', 'Hukum Islam lintas madzhab dan kaidah ushul fiqih', 'aktif'),
(4, 'Akidah & Akhlak', 'Tauhid Ahlus Sunnah, tasawuf akhlaki, dan tazkiyatun nafs', 'aktif'),
(5, 'Tarikh & Sejarah Islam', 'Sirah Nabawiyah, tarikh khulafa, dan peradaban Islam', 'aktif'),
(6, 'Bahasa & Sastra Arab', 'Nahwu, Shorof, Balaghah, dan mu\'jam bahasa', 'aktif'),
(7, 'Sains & Teknologi', 'Matematika, IPA, sains alam, dan teknologi informasi', 'aktif'),
(8, 'Pengembangan Diri', 'Manajemen diri, produktivitas, kepemimpinan, dan motivasi', 'aktif'),
(9, 'Pelajaran Umum', 'Buku paket kurikulum pendidikan nasional & wawasan umum', 'aktif');

-- 4. RAK
INSERT INTO `rak` (`id`, `kode_rak`, `nama_rak`, `lokasi`, `deskripsi`, `status`) VALUES
(1, 'RAK-A01', 'Koleksi Kitab Tafsir & Al-Qur\'an', 'Lantai 1 - Sayap Timur', 'Koleksi rujukan kajian Al-Qur\'an', 'aktif'),
(2, 'RAK-A02', 'Koleksi Hadits & Syarah', 'Lantai 1 - Sayap Timur', 'Kutub hadits mu\'tabarah', 'aktif'),
(3, 'RAK-B01', 'Fiqih Syafi\'iyah & Perbandingan', 'Lantai 1 - Sayap Barat', 'Kitab fiqih klasik dan kontemporer', 'aktif'),
(4, 'RAK-B02', 'Akidah, Akhlak & Tasawuf', 'Lantai 1 - Sayap Barat', 'Buku penanaman akidah dan keteladanan salaf', 'aktif'),
(5, 'RAK-C01', 'Sejarah Islam & Biografi Ulama', 'Lantai 2 - Sayap Timur', 'Sirah dan biografi tokoh peradaban Islam', 'aktif'),
(6, 'RAK-C02', 'Linguistik Arab & Kamus', 'Lantai 2 - Sayap Timur', 'Gramatika Arab dan sastra', 'aktif'),
(7, 'RAK-D01', 'Sains, Matematika & Komputer', 'Lantai 2 - Sayap Barat', 'Buku ilmu pengetahuan terapan', 'aktif'),
(8, 'RAK-D02', 'Pengembangan Diri & Kepemimpinan', 'Lantai 2 - Sayap Barat', 'Koleksi literasi inspiratif santri', 'aktif');

-- 5. BUKU
-- Menjaga integritas stok: total = tersedia + dipinjam + rusak + hilang
INSERT INTO `buku` (`id`, `kode_buku`, `isbn`, `judul`, `penulis`, `penerbit`, `tahun_terbit`, `kategori_id`, `rak_id`, `jumlah_total`, `jumlah_tersedia`, `jumlah_dipinjam`, `jumlah_rusak`, `jumlah_hilang`, `cover`, `deskripsi`, `status`) VALUES
(1, 'BUK-2026-0001', '978-602-05-2331-5', 'Atomic Habits: Perubahan Kecil yang Memberikan Hasil Luar Biasa', 'James Clear', 'Gramedia Pustaka Utama', 2020, 8, 8, 10, 8, 2, 0, 0, NULL, 'Panduan praktis membangun kebiasaan baik dan menghentikan kebiasaan buruk.', 'aktif'),
(2, 'BUK-2026-0002', '978-979-30-7112-2', 'Fiqih Muamalah Kontemporer', 'Prof. Dr. H. Wahbah Az-Zuhaili', 'Darul Fikr / Gema Insani', 2018, 3, 3, 8, 7, 1, 0, 0, NULL, 'Kajian hukum transaksi keuangan dan muamalat dalam perspektif syariat modern.', 'aktif'),
(3, 'BUK-2026-0003', '978-979-01-5233-1', 'Sirah Nabawiyah: Ar-Rahiq Al-Makhtum', 'Syaikh Shafiyyurrahman Al-Mubarakfuri', 'Pustaka Al-Kautsar', 2019, 5, 5, 12, 12, 0, 0, 0, NULL, 'Rekam jejak sejarah perjalanan hidup Rasulullah SAW terlengkap bersanad shahih.', 'aktif'),
(4, 'BUK-2026-0004', '978-602-12-3401-9', 'Tafsir Al-Qur\'an Al-Azhim (Ibnu Katsir) Juz 1-5', 'Al-Hafizh Ibnu Katsir', 'Pustaka Imam Syafi\'i', 2021, 1, 1, 6, 6, 0, 0, 0, NULL, 'Karya tafsir rujukan bil-ma\'tsur yang paling otoritatif di kalangan umat Islam.', 'aktif'),
(5, 'BUK-2026-0005', '978-979-26-1188-4', 'Riyadhus Shalihin: Meniti Jalan Keshalihan', 'Imam An-Nawawi', 'Darul Haq', 2020, 2, 2, 15, 15, 0, 0, 0, NULL, 'Kumpulan hadits pilihan berisi bimbingan budi pekerti, ibadah, dan muamalah harian.', 'aktif'),
(6, 'BUK-2026-0006', '978-623-21-6890-3', 'Ensiklopedia Sains Islami untuk Generasi Qur\'ani', 'Tim Ilmuwan Muslim', 'Kamil Pustaka', 2022, 7, 7, 5, 5, 0, 0, 0, NULL, 'Menelusuri keterkaitan ayat-ayat kauniyah Al-Qur\'an dengan temuan sains modern.', 'aktif'),
(7, 'BUK-2026-0007', '978-979-59-2420-3', 'Panduan Lengkap Tata Bahasa Arab (Nahwu Wadhikh)', 'Ali Al-Jarim & Musthafa Amin', 'Darul Ma\'arif', 2017, 6, 6, 20, 20, 0, 0, 0, NULL, 'Metode aplikatif mempelajari sintaksis bahasa Arab untuk santri tingkat pemula hingga madya.', 'aktif');

-- 6. SANTRI
INSERT INTO `santri` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `kelas`, `kamar`, `tahun_masuk`, `nama_wali`, `no_hp`, `alamat`, `status`) VALUES
(1, '20260012', '0087654321', 'Ahmad Fauzi Rahman', 'L', 'VII-A', 'Abu Bakar Ash-Shiddiq / 01', 2024, 'H. Rahman Hakim', '081399887711', 'Jl. Merdeka No. 12, Banyumas', 'aktif'),
(2, '20260015', '0087654322', 'Muhammad Zaky Al-Farisi', 'L', 'VIII-B', 'Umar bin Khattab / 03', 2023, 'Drs. Subhan Yusuf', '081255443322', 'Jl. Diponegoro No. 89, Cilacap', 'aktif'),
(3, '20260020', '0087654323', 'Fathimah Az-Zahra', 'P', 'VII-C', 'Khadijah Al-Kubra / 02', 2024, 'Hj. Siti Aminah', '085712345678', 'Jl. Pemuda No. 44, Magelang', 'aktif'),
(4, '20260028', '0087654324', 'Ibrahim Abdullah Sani', 'L', 'IX-A', 'Utsman bin Affan / 04', 2022, 'Ir. H. Sani Basuki', '081809876543', 'Jl. Pahlawan No. 7, Purwokerto', 'aktif'),
(5, '20260035', '0087654325', 'Aisyah Humaira', 'P', 'VIII-A', 'Aisyah Radhiyallahu Anha / 01', 2023, 'K.H. Lukman Harun', '081333445566', 'Jl. Kauman No. 15, Kebumen', 'aktif');

-- 7. TRANSAKSI SIRKULASI CONTOH (Header-Detail)
-- Transaksi 1: Sedang dipinjam oleh Ahmad Fauzi Rahman (Meminjam 2 buku)
-- Dipinjam tanggal 2026-10-01, jatuh tempo 2026-10-08
INSERT INTO `peminjaman` (`id`, `kode_transaksi`, `santri_id`, `tanggal_pinjam`, `tanggal_jatuh_tempo`, `tanggal_kembali`, `status`, `denda`, `catatan`, `petugas_id`, `created_at`) VALUES
(1, 'TRX-20261001-0001', 1, '2026-10-01', '2026-10-08', NULL, 'dipinjam', 0.00, 'Peminjaman referensi tugas halaqah mingguan', 2, '2026-10-01 08:30:00');

INSERT INTO `peminjaman_detail` (`id`, `peminjaman_id`, `buku_id`, `tanggal_kembali`, `kondisi_pinjam`, `kondisi_kembali`, `denda`, `status`, `catatan`) VALUES
(1, 1, 1, NULL, 'baik', NULL, 0.00, 'dipinjam', 'Buku Atomic Habits'),
(2, 1, 2, NULL, 'baik', NULL, 0.00, 'dipinjam', 'Buku Fiqih Muamalah');

-- Transaksi 2: Sedang dipinjam oleh Muhammad Zaky (Meminjam 1 buku Atomic Habits)
INSERT INTO `peminjaman` (`id`, `kode_transaksi`, `santri_id`, `tanggal_pinjam`, `tanggal_jatuh_tempo`, `tanggal_kembali`, `status`, `denda`, `catatan`, `petugas_id`, `created_at`) VALUES
(2, 'TRX-20261002-0002', 2, '2026-10-02', '2026-10-09', NULL, 'dipinjam', 0.00, 'Pinjam mandiri', 2, '2026-10-02 14:15:00');

INSERT INTO `peminjaman_detail` (`id`, `peminjaman_id`, `buku_id`, `tanggal_kembali`, `kondisi_pinjam`, `kondisi_kembali`, `denda`, `status`, `catatan`) VALUES
(3, 2, 1, NULL, 'baik', NULL, 0.00, 'dipinjam', 'Buku Atomic Habits eksemplar ke-2');

-- 8. INITIAL ACTIVITY LOGS
INSERT INTO `activity_logs` (`user_id`, `action`, `module`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'SEED_DATABASE', 'SYSTEM', 'Inisialisasi skema database dan master data awal sistem perpustakaan', '127.0.0.1', 'CLI / Installer', NOW()),
(2, 'PEMINJAMAN', 'TRANSAKSI', 'Membuat transaksi baru TRX-20261001-0001 untuk santri Ahmad Fauzi Rahman (2 buku)', '127.0.0.1', 'Web Browser - Desktop Petugas', '2026-10-01 08:30:05'),
(2, 'PEMINJAMAN', 'TRANSAKSI', 'Membuat transaksi baru TRX-20261002-0002 untuk santri Muhammad Zaky Al-Farisi (1 buku)', '127.0.0.1', 'Web Browser - Mobile Petugas', '2026-10-02 14:15:10');
