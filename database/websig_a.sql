-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 04, 2026 at 03:55 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `websig_a`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `key`, `value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'semester_aktif', 'genap', '2026-05-03 01:21:39', '2026-05-03 01:21:39', NULL),
(2, 'tahun_ajaran_aktif', '2025/2026', '2026-05-03 01:21:39', '2026-05-03 01:21:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gambar_gedungs`
--

CREATE TABLE `gambar_gedungs` (
  `id` bigint UNSIGNED NOT NULL,
  `gedung_id` bigint UNSIGNED NOT NULL,
  `nama_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gambar_gedungs`
--

INSERT INTO `gambar_gedungs` (`id`, `gedung_id`, `nama_file`, `path_foto`, `keterangan`, `urutan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 8, 'ac milan anthena.jpg', 'images/gedung/galeri/1777857475_0_ac milan anthena.jpg', '', 0, '2026-05-04 01:17:55', '2026-05-04 01:17:55', NULL),
(2, 9, 'direktorat.jpg', 'images/gedung/galeri/1777858506_0_direktorat.jpg', '', 0, '2026-05-04 01:35:06', '2026-05-04 01:35:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gambar_vegetasis`
--

CREATE TABLE `gambar_vegetasis` (
  `id` bigint UNSIGNED NOT NULL,
  `vegetasi_id` bigint UNSIGNED NOT NULL,
  `nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path_foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gambar_vegetasis`
--

INSERT INTO `gambar_vegetasis` (`id`, `vegetasi_id`, `nama_file`, `path_foto`, `keterangan`, `urutan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'sasana piulang.jpg', 'images/vegetasi/galeri/1777909209_sasana piulang.jpg', NULL, 0, '2026-05-04 15:40:09', '2026-05-04 15:40:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gedungs`
--

CREATE TABLE `gedungs` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_gedung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `x` decimal(11,8) NOT NULL,
  `y` decimal(11,8) NOT NULL,
  `foto_utama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bisa_diajukan` tinyint(1) NOT NULL DEFAULT '1',
  `jam_buka` time DEFAULT NULL,
  `jam_tutup` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gedungs`
--

INSERT INTO `gedungs` (`id`, `nama_gedung`, `alamat`, `deskripsi`, `x`, `y`, `foto_utama`, `bisa_diajukan`, `jam_buka`, `jam_tutup`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Gedung Auditorium', 'Kampus Politani Samarinda', 'Gedung untuk seminar, wisuda, dan acara besar', -0.50100000, 117.10100000, 'images/gedung/utama/auditorium.png', 1, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:18', '2026-05-04 01:33:18'),
(2, 'Gedung Serbaguna', 'Kampus Politani Samarinda', 'Gedung untuk workshop, pelatihan, dan rapat', -0.50200000, 117.10200000, 'images/gedung/utama/serbaguna.png', 1, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:34', '2026-05-04 01:33:34'),
(3, 'Gedung Pertemuan', 'Kampus Politani Samarinda', 'Ruang pertemuan untuk rapat dan diskusi', -0.50300000, 117.10300000, 'images/gedung/utama/pertemuan.png', 1, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:29', '2026-05-04 01:33:29'),
(4, 'Gedung Rektorat', 'Kampus Politani Samarinda', 'Gedung perkantoran pimpinan kampus', -0.50400000, 117.10400000, 'images/gedung/utama/rektorat.png', 0, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:32', '2026-05-04 01:33:32'),
(5, 'Gedung Koperasi', 'Kampus Politani Samarinda', 'Koperasi kampus', -0.50500000, 117.10500000, 'images/gedung/utama/koperasi.png', 0, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:21', '2026-05-04 01:33:21'),
(6, 'TRPL ( Teknologi Rekayasa Perangkat Lunak )', 'Jl. Samratulangi, Sungai Keledang, Kec. Samarinda Seberang, Kota Samarinda, Kalimantan Timur 75131', 'Gedung TRPL ( Teknologi Rekayasa Perangkat Lunak )', -0.53542925, 117.12428420, NULL, 1, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:43', '2026-05-04 01:33:43'),
(7, 'Pos Sekuriti', 'Jl. Samratulangi, Sungai Keledang, Kec. Samarinda Seberang, Kota Samarinda, Kalimantan Timur 75131', 'Pos Sekuriti', -0.53526042, 117.12329623, NULL, 0, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:38', '2026-05-04 01:33:38'),
(8, 'Tes', 'tes', 'tes', -0.53572383, 117.12242243, 'images/gedung/utama/1777857475_ac milan anthena.jpg', 1, '09:17:00', '09:24:00', '2026-05-04 01:17:55', '2026-05-04 01:33:40', '2026-05-04 01:33:40'),
(9, 'Direktorat', 'samratulangi', 'direktorat', -0.53606620, 117.12356983, 'images/gedung/utama/1777858506_direktorat.jpg', 1, '07:30:00', '17:30:00', '2026-05-04 01:35:06', '2026-05-04 01:35:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gedung_fasilitas`
--

CREATE TABLE `gedung_fasilitas` (
  `id` bigint UNSIGNED NOT NULL,
  `gedung_id` bigint UNSIGNED NOT NULL,
  `nama_fasilitas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `foto_ruangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bisa_diajukan` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'TRUE jika ruangan boleh diajukan user untuk penggunaan ad-hoc',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gedung_fasilitas`
--

INSERT INTO `gedung_fasilitas` (`id`, `gedung_id`, `nama_fasilitas`, `kategori`, `keterangan`, `latitude`, `longitude`, `foto_ruangan`, `bisa_diajukan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, 'K.101', 'Ruang Kelas', 'Ruang Kelas TRPL', -0.53540327, 117.12416594, NULL, 0, '2026-05-03 01:21:39', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(2, 7, 'Pos 1', 'Post Penjagaan', 'Pos Sekuriti 1', -0.53526464, 117.12329830, NULL, 0, '2026-05-03 01:21:39', '2026-05-04 01:33:01', '2026-05-04 01:33:01'),
(3, 6, 'Auditorium TRPL', 'Ruang Kelas', 'Auditorium besar untuk seminar, workshop, dan acara besar', -0.53548000, 117.12420000, NULL, 1, '2026-05-03 05:47:52', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(4, 6, 'Auditorium TRPL Lantai 2', 'Auditorium', '[DUMMY-TEST] Auditorium kapasitas besar untuk wisuda', -0.53550000, 117.12420000, NULL, 1, '2026-05-03 06:49:46', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(5, 6, 'RKU Teknik Sipil', 'Ruang Kuliah Umum (RKU)', '[DUMMY-TEST] RKU multi-prodi', -0.53560000, 117.12430000, NULL, 1, '2026-05-03 06:49:46', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(6, 6, 'Ruang Seminar Kebun Raya', 'Ruang Seminar', '[DUMMY-TEST] Ruang seminar dengan view kebun raya', -0.53570000, 117.12440000, NULL, 1, '2026-05-03 06:49:46', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(7, 6, 'Lab Komputer 3', 'Laboratorium', '[DUMMY-TEST] Lab dengan 40 PC i7', -0.53580000, 117.12450000, NULL, 1, '2026-05-03 06:49:46', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(8, 6, 'Lab Bahasa', 'Laboratorium', '[DUMMY-TEST] Lab bahasa dengan headphone & soundproof', -0.53590000, 117.12460000, NULL, 1, '2026-05-03 06:49:46', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(9, 6, 'Ruang Sidang Direktur', 'Ruangan Sekretariatan / Administrasi', '[DUMMY-TEST] Ruang sidang formal', -0.53600000, 117.12470000, NULL, 1, '2026-05-03 06:49:46', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(10, 7, 'JARKOM', 'Ruang Kuliah Umum', 'tes', -0.53548547, 117.12423264, NULL, 1, '2026-05-04 01:23:16', '2026-05-04 01:33:04', '2026-05-04 01:33:04'),
(11, 8, 'meja', 'Ruang Kuliah Umum', NULL, -0.53725949, 117.12309793, 'images/ruangan/1777858095_download.jpeg', 1, '2026-05-04 01:28:15', '2026-05-04 01:33:09', '2026-05-04 01:33:09'),
(12, 9, 'ruang kemahasiswaan', 'Ruangan Sekretariatan / Administrasi', 'kemahasiswaan', -0.53596343, 117.12361305, 'images/ruangan/1777858572_direktorat.jpg', 1, '2026-05-04 01:36:12', '2026-05-04 01:36:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_ruangans`
--

CREATE TABLE `jadwal_ruangans` (
  `id` bigint UNSIGNED NOT NULL,
  `gedung_fasilitas_id` bigint UNSIGNED NOT NULL,
  `nama_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hari` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwal_ruangans`
--

INSERT INTO `jadwal_ruangans` (`id`, `gedung_fasilitas_id`, `nama_kegiatan`, `hari`, `jam_mulai`, `jam_selesai`, `keterangan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Perkuliahan', 'Senin', '08:40:00', '15:00:00', NULL, '2026-05-03 01:21:39', '2026-05-04 01:32:47', '2026-05-04 01:32:47'),
(2, 1, 'Perkuliahan', 'Selasa', '07:30:00', '16:00:00', NULL, '2026-05-03 01:21:39', '2026-05-04 01:32:42', '2026-05-04 01:32:42'),
(6, 10, 'nyantai', 'Senin', '08:30:00', '16:00:00', 'tes', '2026-05-04 01:24:46', '2026-05-04 01:32:49', '2026-05-04 01:32:49'),
(7, 10, 'nyantai', 'Selasa', '07:30:00', '16:00:00', 'tes', '2026-05-04 01:24:46', '2026-05-04 01:32:44', '2026-05-04 01:32:44'),
(8, 10, 'nyantai', 'Rabu', '07:30:00', '16:00:00', 'tes', '2026-05-04 01:24:46', '2026-05-04 01:32:36', '2026-05-04 01:32:36'),
(9, 10, 'nyantai', 'Kamis', '07:30:00', '16:00:00', 'tes', '2026-05-04 01:24:46', '2026-05-04 01:32:28', '2026-05-04 01:32:28'),
(10, 10, 'nyantai', 'Jumat', '07:30:00', '16:00:00', 'tes', '2026-05-04 01:24:46', '2026-05-04 01:29:40', '2026-05-04 01:29:40'),
(11, 11, 'bealajar', 'Senin', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:28:56', '2026-05-04 01:30:37', '2026-05-04 01:30:37'),
(12, 11, 'bealajar', 'Selasa', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:28:56', '2026-05-04 01:30:34', '2026-05-04 01:30:34'),
(13, 11, 'bealajar', 'Rabu', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:28:56', '2026-05-04 01:30:25', '2026-05-04 01:30:25'),
(14, 11, 'bealajar', 'Kamis', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:28:56', '2026-05-04 01:30:31', '2026-05-04 01:30:31'),
(15, 11, 'bealajar', 'Jumat', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:28:56', '2026-05-04 01:30:28', '2026-05-04 01:30:28'),
(16, 11, 'bealajar', 'Senin', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:30:53', '2026-05-04 01:32:52', '2026-05-04 01:32:52'),
(17, 11, 'bealajar', 'Senin', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:30:53', '2026-05-04 01:32:55', '2026-05-04 01:32:55'),
(18, 11, 'bealajar', 'Rabu', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:30:53', '2026-05-04 01:32:39', '2026-05-04 01:32:39'),
(19, 11, 'bealajar', 'Kamis', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:30:53', '2026-05-04 01:32:33', '2026-05-04 01:32:33'),
(20, 11, 'bealajar', 'Jumat', '07:30:00', '16:00:00', 'mm', '2026-05-04 01:30:53', '2026-05-04 01:32:24', '2026-05-04 01:32:24'),
(21, 12, 'ngurus', 'Senin', '07:30:00', '16:00:00', 'apa aja', '2026-05-04 01:36:44', '2026-05-04 01:37:20', '2026-05-04 01:37:20'),
(22, 12, 'ngurus', 'Selasa', '07:30:00', '16:00:00', 'apa aja', '2026-05-04 01:36:44', '2026-05-04 01:36:44', NULL),
(23, 12, 'ngurus', 'Rabu', '07:30:00', '16:00:00', 'apa aja', '2026-05-04 01:36:44', '2026-05-04 01:36:44', NULL),
(24, 12, 'ngurus', 'Kamis', '07:30:00', '16:00:00', 'apa aja', '2026-05-04 01:36:44', '2026-05-04 01:36:44', NULL),
(25, 12, 'ngurus', 'Jumat', '07:30:00', '16:00:00', 'apa aja', '2026-05-04 01:36:44', '2026-05-04 01:36:44', NULL),
(26, 12, 'bealajar', 'Senin', '07:30:00', '16:00:00', NULL, '2026-05-04 01:38:51', '2026-05-04 01:38:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_semester`
--

CREATE TABLE `jadwal_semester` (
  `id` bigint UNSIGNED NOT NULL,
  `gedung_id` bigint UNSIGNED NOT NULL,
  `semester` int NOT NULL,
  `tahun_ajaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_jadwal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwal_semester`
--

INSERT INTO `jadwal_semester` (`id`, `gedung_id`, `semester`, `tahun_ajaran`, `file_jadwal`, `keterangan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 9, 1, '2024/2025', 'images/jadwal_semester/1777858763_semester1_direktorat.jpg', 'mm', '2026-05-04 01:39:23', '2026-05-04 01:39:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_03_11_131511_create_gedungs_table', 1),
(6, '2026_03_11_133126_create_gambar_gedungs_table', 1),
(7, '2026_03_11_155655_add_foto_utama_to_gedungs_table', 1),
(8, '2026_04_13_155849_create_gedung_fasilitas_table', 1),
(9, '2026_04_15_134801_create_jadwal_ruangans_table', 1),
(10, '2026_04_15_134941_add_status_to_gedung_fasilitas_table', 1),
(11, '2026_04_23_005025_remove_unused_columns_from_gedungs_table', 1),
(12, '2026_04_23_105014_update_xy_precision_in_gedungs_table', 1),
(13, '2026_04_23_111300_add_map_and_image_to_gedung_fasilitas_table', 1),
(14, '2026_04_24_100000_create_jadwal_semester_table', 1),
(15, '2026_04_24_133913_alter_jadwal_semester_to_gedung_id', 1),
(16, '2026_04_26_140000_create_pengajuan_ruangans_table', 1),
(17, '2026_04_26_160000_add_role_to_users_table', 1),
(18, '2026_04_27_032200_add_bisa_diajukan_to_gedungs_table', 1),
(19, '2026_04_29_221955_add_jam_operasional_to_gedungs_table', 1),
(20, '2026_05_02_220000_create_app_settings_table', 1),
(21, '2026_05_03_130000_add_dibatalkan_to_pengajuan_ruangans_status', 2),
(22, '2026_05_03_140000_add_bisa_diajukan_to_gedung_fasilitas_table', 3),
(23, '2026_05_03_150000_drop_is_aktif_from_gedung_fasilitas_table', 4),
(24, '2026_05_04_233034_create_vegetasis_table', 5),
(25, '2026_05_04_233048_create_gambar_vegetasis_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_ruangans`
--

CREATE TABLE `pengajuan_ruangans` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_pengajuan` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gedung_fasilitas_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nama_pemohon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_pemohon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `asal_instansi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kegiatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `jumlah_peserta` int DEFAULT NULL,
  `keperluan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('diproses','disetujui','ditolak','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diproses',
  `catatan_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengajuan_ruangans`
--

INSERT INTO `pengajuan_ruangans` (`id`, `kode_pengajuan`, `gedung_fasilitas_id`, `user_id`, `nama_pemohon`, `email_pemohon`, `no_telepon`, `asal_instansi`, `jenis_kegiatan`, `nama_kegiatan`, `tanggal_mulai`, `tanggal_selesai`, `jam_mulai`, `jam_selesai`, `jumlah_peserta`, `keperluan`, `status`, `catatan_admin`, `approved_by`, `approved_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PR-20260426-001', 1, 1, 'Admin WebGIS', 'admin@webgis.com', '081234567890', 'Politani Samarinda', 'Seminar', 'Test Seminar Nasional', '2026-05-10', '2026-05-10', '08:00:00', '12:00:00', 50, 'Pengujian fitur pengajuan ruangan', 'diproses', NULL, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:49', '2026-05-04 01:33:49'),
(2, 'PR-20260426-002', 1, 2, 'User Biasa', 'user@webgis.com', '089876543210', 'PT Contoh Mandiri', 'Workshop', 'Workshop Flutter Development', '2026-05-15', '2026-05-15', '09:00:00', '16:00:00', 30, 'Pelatihan pengembangan aplikasi mobile', 'diproses', NULL, NULL, NULL, '2026-05-03 01:21:39', '2026-05-04 01:33:49', '2026-05-04 01:33:49'),
(3, 'PR-20260503-001', 1, 2, 'Test User', 'user@webgis.com', '081234567890', 'Politeknik Negeri', 'Seminar', 'Test Seminar Antigravity', '2026-05-04', '2026-05-04', '10:00:00', '12:00:00', 30, 'Testing automation oleh Antigravity', 'disetujui', NULL, 1, '2026-05-03 01:59:13', '2026-05-03 01:55:30', '2026-05-04 01:33:49', '2026-05-04 01:33:49'),
(4, 'PR-20260503-002', 1, 2, 'User Biasa', 'user@webgis.com', '08123456789', 'Politan Samarinda', 'Seminar', 'Testing Regression UX', '2026-06-01', '2026-06-01', '09:00:00', '12:00:00', NULL, NULL, 'diproses', NULL, NULL, NULL, '2026-05-03 03:13:24', '2026-05-04 01:33:49', '2026-05-04 01:33:49'),
(5, 'PR-20260503-003', 1, 2, 'User Biasa', 'user@webgis.com', '081234567890', 'Politani Samarinda', 'Seminar', 'Regression Test 2026-05-03T03:17:43.776Z', '2026-07-01', '2026-07-01', '09:00:00', '12:00:00', NULL, 'Testing flow utama', 'diproses', NULL, NULL, NULL, '2026-05-03 03:17:44', '2026-05-04 01:33:49', '2026-05-04 01:33:49'),
(6, 'PR-20260503-004', 1, 2, 'Riki', 'user@webgis.com', '08123456789', 'Politani Samarinda', 'Seminar', 'Seminar UX Improvement', '2026-12-01', '2026-12-01', '08:00:00', '10:00:00', NULL, 'Keperluan verifikasi UX', 'diproses', NULL, NULL, NULL, '2026-05-03 03:38:24', '2026-05-04 01:33:49', '2026-05-04 01:33:49'),
(7, 'PR-20260503-005', 1, 2, 'User Biasa', 'user@webgis.com', '08123456789', 'Politeknik Negeri', 'Seminar', 'Final Regression Test V4', '2027-01-15', '2027-01-15', '14:00:00', '16:00:00', NULL, 'Verifikasi UX improvement', 'dibatalkan', NULL, NULL, NULL, '2026-05-03 04:06:34', '2026-05-04 01:33:49', '2026-05-04 01:33:49'),
(8, 'PR-20260504-001', 12, 2, 'User Biasa', 'user@webgis.com', '0999999', 'politani', 'Lainnya', 'bealajar', '2026-05-04', '2026-05-04', '10:42:00', '10:45:00', 10, 'ingin belajar', 'diproses', NULL, NULL, NULL, '2026-05-04 01:43:01', '2026-05-04 01:43:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin WebGIS', 'admin@webgis.com', 'admin', NULL, '$2y$10$c5Y/q6B4uyZU7Pzn.n14cerKn/SikJJ0t.hrHrN.6uFtAEDQh7hVe', NULL, '2026-05-03 01:21:39', '2026-05-03 01:21:39'),
(2, 'User Biasa', 'user@webgis.com', 'user', NULL, '$2y$10$l5pK5yYwn/1GQ/ulJGMeu.sXv75W2q1eE3.YQHp0YnCwhaGZ8AiK6', NULL, '2026-05-03 01:21:39', '2026-05-03 01:21:39'),
(3, 'aku123456@gmail.com', 'aku123456@gmail.com', 'user', NULL, '$2y$10$VcTmlcTdNI.d2UUMvtkU3.QZ/.yjjH4/rxNMveejGVUetoaMToNh2', NULL, '2026-05-04 01:10:11', '2026-05-04 01:10:11');

-- --------------------------------------------------------

--
-- Table structure for table `vegetasis`
--

CREATE TABLE `vegetasis` (
  `id` bigint UNSIGNED NOT NULL,
  `gedung_id` bigint UNSIGNED NOT NULL,
  `nama_vegetasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `foto_utama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vegetasis`
--

INSERT INTO `vegetasis` (`id`, `gedung_id`, `nama_vegetasi`, `kategori`, `keterangan`, `latitude`, `longitude`, `foto_utama`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 9, 'pohon pisang', 'Pohon', '123', -0.53594532, 117.12341730, 'images/vegetasi/1777909209_Gatau.jpg', '2026-05-04 15:40:09', '2026-05-04 15:40:09', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_settings_key_unique` (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gambar_gedungs`
--
ALTER TABLE `gambar_gedungs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gambar_gedungs_gedung_id_foreign` (`gedung_id`);

--
-- Indexes for table `gambar_vegetasis`
--
ALTER TABLE `gambar_vegetasis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gambar_vegetasis_vegetasi_id_foreign` (`vegetasi_id`);

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
-- Indexes for table `jadwal_semester`
--
ALTER TABLE `jadwal_semester`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jadwal_semester_gedung_id_foreign` (`gedung_id`);

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
-- Indexes for table `pengajuan_ruangans`
--
ALTER TABLE `pengajuan_ruangans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pengajuan_ruangans_kode_pengajuan_unique` (`kode_pengajuan`),
  ADD KEY `pengajuan_ruangans_user_id_foreign` (`user_id`),
  ADD KEY `pengajuan_ruangans_approved_by_foreign` (`approved_by`),
  ADD KEY `pr_overlap_idx` (`gedung_fasilitas_id`,`status`,`tanggal_mulai`,`tanggal_selesai`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vegetasis`
--
ALTER TABLE `vegetasis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vegetasis_gedung_id_foreign` (`gedung_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gambar_gedungs`
--
ALTER TABLE `gambar_gedungs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gambar_vegetasis`
--
ALTER TABLE `gambar_vegetasis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gedungs`
--
ALTER TABLE `gedungs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `gedung_fasilitas`
--
ALTER TABLE `gedung_fasilitas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `jadwal_ruangans`
--
ALTER TABLE `jadwal_ruangans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `jadwal_semester`
--
ALTER TABLE `jadwal_semester`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pengajuan_ruangans`
--
ALTER TABLE `pengajuan_ruangans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vegetasis`
--
ALTER TABLE `vegetasis`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gambar_gedungs`
--
ALTER TABLE `gambar_gedungs`
  ADD CONSTRAINT `gambar_gedungs_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`);

--
-- Constraints for table `gambar_vegetasis`
--
ALTER TABLE `gambar_vegetasis`
  ADD CONSTRAINT `gambar_vegetasis_vegetasi_id_foreign` FOREIGN KEY (`vegetasi_id`) REFERENCES `vegetasis` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `jadwal_semester`
--
ALTER TABLE `jadwal_semester`
  ADD CONSTRAINT `jadwal_semester_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_ruangans`
--
ALTER TABLE `pengajuan_ruangans`
  ADD CONSTRAINT `pengajuan_ruangans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pengajuan_ruangans_gedung_fasilitas_id_foreign` FOREIGN KEY (`gedung_fasilitas_id`) REFERENCES `gedung_fasilitas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengajuan_ruangans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vegetasis`
--
ALTER TABLE `vegetasis`
  ADD CONSTRAINT `vegetasis_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
