-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:8111
-- Generation Time: Apr 16, 2026 at 07:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `newpbl6`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gedung_id` bigint(20) UNSIGNED NOT NULL,
  `nama_fasilitas` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `lokasi` varchar(255) DEFAULT NULL,
  `status` enum('aktif','maintenance','rusak') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `gedung_id`, `nama_fasilitas`, `deskripsi`, `kategori`, `jumlah`, `lokasi`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Toilet', 'Fasilitas toilet untuk mahasiswa dan dosen', 'toilet', 4, 'Setiap lantai', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(2, 1, 'Lift', 'Lift untuk akses antar lantai', 'lift', 1, 'Lantai 1-3', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(3, 1, 'Air Conditioner', 'AC untuk kenyamanan ruangan', 'ac', 8, 'Semua ruang kuliah', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(4, 1, 'Proyektor', 'Proyektor untuk presentasi', 'proyektor', 6, 'Ruang kuliah', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(5, 2, 'Toilet', 'Fasilitas toilet untuk pengunjung', 'toilet', 3, 'Lantai 1 dan 2', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(6, 2, 'WiFi', 'Internet nirkabel untuk akses online', 'wifi', 1, 'Seluruh gedung', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(7, 2, 'Komputer', 'Komputer untuk akses digital', 'komputer', 20, 'Ruang multimedia', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(8, 2, 'Printer', 'Printer untuk mencetak dokumen', 'printer', 2, 'Lantai 1', 'maintenance', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(9, 3, 'Toilet', 'Fasilitas toilet untuk acara', 'toilet', 6, 'Setiap lantai', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(10, 3, 'Air Conditioner', 'AC untuk kenyamanan acara', 'ac', 12, 'Aula dan seminar', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(11, 3, 'Sound System', 'Sistem audio untuk acara', 'sound_system', 1, 'Aula', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51'),
(12, 3, 'Parkir', 'Area parkir kendaraan', 'parking', 50, 'Area depan gedung', 'aktif', '2026-04-05 08:13:51', '2026-04-05 08:13:51');

-- --------------------------------------------------------

--
-- Table structure for table `gambar_gedungs`
--

CREATE TABLE `gambar_gedungs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gedung_id` bigint(20) UNSIGNED NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_foto` varchar(255) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `urutan` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gambar_gedungs`
--

INSERT INTO `gambar_gedungs` (`id`, `gedung_id`, `nama_file`, `path_foto`, `keterangan`, `urutan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'gambar iki.jpg', 'gedung/galeri/1773243116_0_gambar iki.jpg', '', 0, '2026-03-11 07:31:56', '2026-03-11 07:32:29', '2026-03-11 07:32:29'),
(2, 2, 'e7d39fb70c949b40b1b819580d8ce08a.jpg', 'gedung/galeri/1773243643_0_e7d39fb70c949b40b1b819580d8ce08a.jpg', '', 0, '2026-03-11 07:40:43', '2026-03-11 08:12:24', '2026-03-11 08:12:24'),
(3, 3, 'coki.jpg', 'gedung/galeri/1773273563_0_coki.jpg', '', 0, '2026-03-11 15:59:23', '2026-03-11 15:59:23', NULL),
(4, 10, 'Screenshot (35).png', 'gedung/galeri/1776058369_0_Screenshot (35).png', '', 0, '2026-04-12 21:32:49', '2026-04-12 21:32:49', NULL),
(5, 10, 'gambar iki.jpg', 'gedung/galeri/1776124680_0_gambar iki.jpg', '', 1, '2026-04-13 15:58:00', '2026-04-13 15:58:00', NULL),
(6, 11, 'TRGS.jpg', 'gedung/galeri/1776125331_0_TRGS.jpg', '', 0, '2026-04-13 16:08:51', '2026-04-13 16:08:51', NULL),
(7, 11, 'Mushola.jpg', 'gedung/galeri/1776125351_0_Mushola.jpg', '', 1, '2026-04-13 16:09:11', '2026-04-13 16:09:11', NULL),
(8, 12, 'direktorat.jpg', 'gedung/galeri/1776266744_0_direktorat.jpg', '', 0, '2026-04-15 15:25:44', '2026-04-15 15:29:49', '2026-04-15 15:29:49'),
(9, 12, 'gedung H.jpg', 'gedung/galeri/1776267513_0_gedung H.jpg', '', 0, '2026-04-15 15:38:33', '2026-04-15 15:41:06', '2026-04-15 15:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `gedungs`
--

CREATE TABLE `gedungs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_gedung` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `deskripsi` text NOT NULL,
  `fungsi` varchar(255) NOT NULL,
  `jumlah_lantai` int(11) NOT NULL,
  `tahun_berdiri` int(11) NOT NULL,
  `kondisi` varchar(255) NOT NULL,
  `x` decimal(8,2) NOT NULL,
  `y` decimal(8,2) NOT NULL,
  `foto_utama` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gedungs`
--

INSERT INTO `gedungs` (`id`, `nama_gedung`, `alamat`, `deskripsi`, `fungsi`, `jumlah_lantai`, `tahun_berdiri`, `kondisi`, `x`, `y`, `foto_utama`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TRPL', 'gunung panjang', 'tes', 'Pendidikan', 2, 2003, 'Baik', -0.53, 117.12, NULL, '2026-03-11 07:31:16', '2026-03-11 07:32:25', '2026-03-11 07:32:25'),
(2, 'TRPL', 'samratulangi', 'tes', 'Pendidikan', 2, 2003, 'Baik', 0.02, 116.54, 'gedung/utama/1773245954_stiker.jpg', '2026-03-11 07:35:16', '2026-03-11 15:58:39', '2026-03-11 15:58:39'),
(3, 'BTP', 'samratulangi', 'coba lagi', 'Perkantoran', 3, 2003, 'Baik', -0.54, 117.12, 'gedung/utama/1773245639_831663635115650.jpg', '2026-03-11 08:13:59', '2026-03-11 19:44:23', '2026-03-11 19:44:23'),
(4, 'pp', 'gunung panjang', 'tes 3', 'Pendidikan', 1, 1999, 'Sedang', -0.53, 117.12, 'gedung/utama/1773272342_JUSTIN.jpeg', '2026-03-11 15:39:02', '2026-03-11 19:44:25', '2026-03-11 19:44:25'),
(5, 'BTP', 'rumah fedi', 'tes', 'Pendidikan', 2, 2002, 'Baik', -0.54, 117.18, 'gedung/utama/1773294066_hajime.jpeg', '2026-03-11 19:45:53', '2026-03-12 00:49:48', '2026-03-12 00:49:48'),
(6, 'TRPL', 'gunung panjang', 'misalanya', 'Pendidikan', 1, 2022, 'Rusak', -0.53, 117.12, 'gedung/utama/1773299517_garok.jpg', '2026-03-11 23:11:57', '2026-03-12 00:49:51', '2026-03-12 00:49:51'),
(7, 'Direktorat', 'samratulangi', 'tempat direktorat', 'Perkantoran', 5, 2001, 'Baik', -0.53, 117.12, 'gedung/utama/1773305360_direktorat.jpg', '2026-03-12 00:49:20', '2026-04-13 15:51:45', '2026-04-13 15:51:45'),
(8, 'politani', 'samratulangi', 'kampus', 'Pendidikan', 2, 2003, 'Baik', -0.53, 117.12, 'gedung/utama/1774146496_TRGS.jpg', '2026-03-21 18:28:16', '2026-04-13 15:51:41', '2026-04-13 15:51:41'),
(9, 'BTP', 't', 't', 'Komersial', 1, 2009, 'Baik', -0.56, 117.15, 'gedung/utama/1775400126_BTP.jpg', '2026-04-05 06:42:06', '2026-04-13 15:51:48', '2026-04-13 15:51:48'),
(10, 'Pengelolaan Hutan', '123', '123', 'Pendidikan', 1, 2001, 'Sedang', -0.54, 117.13, 'gedung/utama/1776058369_Screenshot 2026-02-19 103234.png', '2026-04-12 21:32:49', '2026-04-12 21:32:49', NULL),
(11, 'TRPL', 'tes', 'tes', 'Lainnya', 1, 2001, 'Rusak', -0.53, 117.12, 'gedung/utama/1776125331_AD.jpg', '2026-04-13 16:08:51', '2026-04-15 15:43:35', '2026-04-15 15:43:35'),
(12, 'MESJID', 'SHOLAT WOY', 'SHOLAT SEBELUM DIMUSNAHKAN', 'Lainnya', 3, 1990, 'Baik', -0.55, 117.11, 'gedung/utama/1776314487_RK.jpg', '2026-04-15 15:25:44', '2026-04-16 04:41:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gedung_fasilitas`
--

CREATE TABLE `gedung_fasilitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gedung_id` bigint(20) UNSIGNED NOT NULL,
  `nama_fasilitas` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gedung_fasilitas`
--

INSERT INTO `gedung_fasilitas` (`id`, `gedung_id`, `nama_fasilitas`, `kategori`, `keterangan`, `is_aktif`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 11, 'Jarkom', 'Kelas', 'belajar', 1, '2026-04-15 06:09:25', '2026-04-15 06:09:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_ruangans`
--

CREATE TABLE `jadwal_ruangans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gedung_fasilitas_id` bigint(20) UNSIGNED NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `hari` varchar(255) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwal_ruangans`
--

INSERT INTO `jadwal_ruangans` (`id`, `gedung_fasilitas_id`, `nama_kegiatan`, `hari`, `jam_mulai`, `jam_selesai`, `keterangan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'bealajar', 'Rabu', '23:00:00', '23:59:00', 'belajar', '2026-04-15 06:10:27', '2026-04-15 15:23:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_03_11_131511_create_gedungs_table', 2),
(6, '2026_03_11_132809_create_gambar_gedungs_table', 3),
(7, '2026_03_11_133126_create_gambar_gedungs_table', 4),
(8, '2026_03_11_155655_add_foto_utama_to_gedungs_table', 5),
(9, '2026_04_06_000001_create_sub_ruangan_table', 6),
(10, '2026_04_06_000002_create_fasilitas_table', 6),
(11, '2026_04_13_155849_create_gedung_fasilitas_table', 7),
(12, '2026_04_15_000001_create_ruangans_table', 8),
(13, '2026_04_15_000002_create_ruangan_fotos_table', 9),
(14, '2026_04_15_000003_create_ruangan_fasilitas_table', 10),
(15, '2026_04_15_134801_create_jadwal_ruangans_table', 11),
(16, '2026_04_15_134941_add_status_to_gedung_fasilitas_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ruangans`
--

CREATE TABLE `ruangans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gedung_id` bigint(20) UNSIGNED NOT NULL,
  `nama_ruangan` varchar(255) NOT NULL,
  `jenis_ruangan` varchar(255) NOT NULL,
  `lantai` varchar(255) NOT NULL,
  `kapasitas` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `x` decimal(10,8) DEFAULT NULL,
  `y` decimal(10,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ruangans`
--

INSERT INTO `ruangans` (`id`, `gedung_id`, `nama_ruangan`, `jenis_ruangan`, `lantai`, `kapasitas`, `deskripsi`, `x`, `y`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 11, 'jarkom', 'Kelas', '1', '2', '2', NULL, NULL, '2026-04-15 05:32:38', '2026-04-15 05:32:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ruangan_fasilitas`
--

CREATE TABLE `ruangan_fasilitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ruangan_id` bigint(20) UNSIGNED NOT NULL,
  `nama_fasilitas` varchar(255) NOT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ruangan_fotos`
--

CREATE TABLE `ruangan_fotos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ruangan_id` bigint(20) UNSIGNED NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_foto` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ruangan_fotos`
--

INSERT INTO `ruangan_fotos` (`id`, `ruangan_id`, `nama_file`, `path_foto`, `keterangan`, `urutan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'RK.jpg', 'ruangan/fotos/1776259958_0_RK.jpg', '', 0, '2026-04-15 05:32:38', '2026-04-15 05:32:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sub_ruangan`
--

CREATE TABLE `sub_ruangan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gedung_id` bigint(20) UNSIGNED NOT NULL,
  `nama_ruangan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `lantai` int(11) NOT NULL,
  `kapasitas` int(11) DEFAULT NULL,
  `jenis_ruangan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_ruangan`
--

INSERT INTO `sub_ruangan` (`id`, `gedung_id`, `nama_ruangan`, `deskripsi`, `lantai`, `kapasitas`, `jenis_ruangan`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ruang Kuliah 101', NULL, 1, 50, 'kelas', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(2, 1, 'Ruang Kuliah 102', NULL, 1, 50, 'kelas', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(3, 1, 'Laboratorium Komputer', NULL, 2, 30, 'laboratorium', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(4, 1, 'Ruang Dosen', NULL, 2, 20, 'kantor', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(5, 2, 'Ruang Baca Utama', NULL, 1, 100, 'perpustakaan', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(6, 2, 'Ruang Referensi', NULL, 2, 40, 'perpustakaan', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(7, 2, 'Ruang Multimedia', NULL, 3, 25, 'multimedia', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(8, 3, 'Aula Serbaguna', NULL, 1, 200, 'aula', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(9, 3, 'Ruang Seminar', NULL, 2, 80, 'seminar', '2026-04-05 08:13:33', '2026-04-05 08:13:33'),
(10, 3, 'Kantin', NULL, 1, 150, 'kantin', '2026-04-05 08:13:33', '2026-04-05 08:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Muhammad Rizky', 'iki098765@gmail.com', NULL, '$2y$10$f0VC2E4x6FhXisH/fd5STeFMwsfJch4t09UDQD/O6J29b2Pnpif8W', NULL, '2026-03-11 05:16:40', '2026-03-11 05:16:40'),
(2, 'Admin', 'admin@gmail.com', NULL, '$2y$10$NL7netaxHXbcHBemNgzFxOgWkjXhY80aWFDiSTa0Mvr5T0.t.xE0G', NULL, '2026-03-11 16:33:39', '2026-03-11 16:33:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fasilitas_gedung_id_foreign` (`gedung_id`);

--
-- Indexes for table `gambar_gedungs`
--
ALTER TABLE `gambar_gedungs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gambar_gedungs_gedung_id_foreign` (`gedung_id`);

--
-- Indexes for table `gedungs`
--
ALTER TABLE `gedungs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gedung_fasilitas`
--
ALTER TABLE `gedung_fasilitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gedung_fasilitas_gedung_id_foreign` (`gedung_id`);

--
-- Indexes for table `jadwal_ruangans`
--
ALTER TABLE `jadwal_ruangans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwal_ruangans_gedung_fasilitas_id_foreign` (`gedung_fasilitas_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `ruangans`
--
ALTER TABLE `ruangans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ruangans_gedung_id_foreign` (`gedung_id`);

--
-- Indexes for table `ruangan_fasilitas`
--
ALTER TABLE `ruangan_fasilitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ruangan_fasilitas_ruangan_id_foreign` (`ruangan_id`);

--
-- Indexes for table `ruangan_fotos`
--
ALTER TABLE `ruangan_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ruangan_fotos_ruangan_id_foreign` (`ruangan_id`);

--
-- Indexes for table `sub_ruangan`
--
ALTER TABLE `sub_ruangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_ruangan_gedung_id_foreign` (`gedung_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `gambar_gedungs`
--
ALTER TABLE `gambar_gedungs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `gedungs`
--
ALTER TABLE `gedungs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `gedung_fasilitas`
--
ALTER TABLE `gedung_fasilitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jadwal_ruangans`
--
ALTER TABLE `jadwal_ruangans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ruangans`
--
ALTER TABLE `ruangans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ruangan_fasilitas`
--
ALTER TABLE `ruangan_fasilitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ruangan_fotos`
--
ALTER TABLE `ruangan_fotos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sub_ruangan`
--
ALTER TABLE `sub_ruangan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD CONSTRAINT `fasilitas_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gambar_gedungs`
--
ALTER TABLE `gambar_gedungs`
  ADD CONSTRAINT `gambar_gedungs_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`);

--
-- Constraints for table `gedung_fasilitas`
--
ALTER TABLE `gedung_fasilitas`
  ADD CONSTRAINT `gedung_fasilitas_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_ruangans`
--
ALTER TABLE `jadwal_ruangans`
  ADD CONSTRAINT `jadwal_ruangans_gedung_fasilitas_id_foreign` FOREIGN KEY (`gedung_fasilitas_id`) REFERENCES `gedung_fasilitas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ruangans`
--
ALTER TABLE `ruangans`
  ADD CONSTRAINT `ruangans_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`);

--
-- Constraints for table `ruangan_fasilitas`
--
ALTER TABLE `ruangan_fasilitas`
  ADD CONSTRAINT `ruangan_fasilitas_ruangan_id_foreign` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangans` (`id`);

--
-- Constraints for table `ruangan_fotos`
--
ALTER TABLE `ruangan_fotos`
  ADD CONSTRAINT `ruangan_fotos_ruangan_id_foreign` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangans` (`id`);

--
-- Constraints for table `sub_ruangan`
--
ALTER TABLE `sub_ruangan`
  ADD CONSTRAINT `sub_ruangan_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
