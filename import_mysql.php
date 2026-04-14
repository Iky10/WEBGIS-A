<?php
// Script untuk import data demo ke MySQL
// Pastikan database sudah dibuat sebelumnya (default: newpbl6)

// 1. Load .env secara manual
function getEnvValue($key, $default = null) {
    static $env = null;
    if ($env === null) {
        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $value) = explode('=', $line, 2) + [null, null];
                if ($name) $env[trim($name)] = trim($value, '"\' ');
            }
        }
    }
    return isset($env[$key]) ? $env[$key] : $default;
}

$host = getEnvValue('DB_HOST', '127.0.0.1');
$port = getEnvValue('DB_PORT', '3306');
$db   = getEnvValue('DB_DATABASE', 'newpbl6');
$user = getEnvValue('DB_USERNAME', 'root');
$pass = getEnvValue('DB_PASSWORD', '');

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Terhubung ke MySQL database: $db\n";
} catch (PDOException $e) {
    die("❌ Gagal terhubung ke MySQL: " . $e->getMessage() . "\nPastikan database '$db' sudah dibuat.\n");
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

// 2. DDL MySQL
$ddl = "
DROP TABLE IF EXISTS `gambar_gedungs`;
DROP TABLE IF EXISTS `gedungs`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gedungs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_gedung` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `deskripsi` text NOT NULL,
  `fungsi` varchar(255) NOT NULL,
  `jumlah_lantai` int(11) NOT NULL,
  `tahun_berdiri` int(11) NOT NULL,
  `kondisi` varchar(255) NOT NULL,
  `x` double NOT NULL,
  `y` double NOT NULL,
  `foto_utama` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `gambar_gedungs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gedung_id` bigint(20) unsigned NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_foto` varchar(255) NOT NULL,
  `keterangan` text NOT NULL,
  `urutan` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gambar_gedungs_gedung_id_foreign` (`gedung_id`),
  CONSTRAINT `gambar_gedungs_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedungs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$pdo->exec($ddl);
echo "✅ Struktur tabel berhasil dibuat di MySQL.\n";

// 3. Insert Data Gedungs
$gedungs = [
    [1, 'TRPL', 'gunung panjang', 'tes', 'Pendidikan', 2, 2003, 'Baik', -0.53, 117.12, null, '2026-03-11 07:31:16', '2026-03-11 07:32:25', '2026-03-11 07:32:25'],
    [2, 'TRPL', 'samratulangi', 'tes', 'Pendidikan', 2, 2003, 'Baik', 0.02, 116.54, 'gedung/utama/1773245954_stiker.jpg', '2026-03-11 07:35:16', '2026-03-11 15:58:39', '2026-03-11 15:58:39'],
    [3, 'BTP', 'samratulangi', 'coba lagi', 'Perkantoran', 3, 2003, 'Baik', -0.54, 117.12, 'gedung/utama/1773245639_831663635115650.jpg', '2026-03-11 08:13:59', '2026-03-11 19:44:23', '2026-03-11 19:44:23'],
    [4, 'pp', 'gunung panjang', 'tes 3', 'Pendidikan', 1, 1999, 'Sedang', -0.53, 117.12, 'gedung/utama/1773272342_JUSTIN.jpeg', '2026-03-11 15:39:02', '2026-03-11 19:44:25', '2026-03-11 19:44:25'],
    [5, 'BTP', 'rumah fedi', 'tes', 'Pendidikan', 2, 2002, 'Baik', -0.54, 117.18, 'gedung/utama/1773294066_hajime.jpeg', '2026-03-11 19:45:53', '2026-03-12 00:49:48', '2026-03-12 00:49:48'],
    [6, 'TRPL', 'gunung panjang', 'misalanya', 'Pendidikan', 1, 2022, 'Rusak', -0.53, 117.12, 'gedung/utama/1773299517_garok.jpg', '2026-03-11 23:11:57', '2026-03-12 00:49:51', '2026-03-12 00:49:51'],
    [7, 'Direktorat', 'samratulangi', 'tempat direktorat', 'Perkantoran', 5, 2001, 'Baik', -0.53, 117.12, 'gedung/utama/1773305360_direktorat.jpg', '2026-03-12 00:49:20', '2026-03-12 00:49:20', null],
    [8, 'politani', 'samratulangi', 'kampus', 'Pendidikan', 2, 2003, 'Baik', -0.53, 117.12, 'gedung/utama/1774146496_TRGS.jpg', '2026-03-21 18:28:16', '2026-03-21 18:28:16', null],
];

$stmt = $pdo->prepare("INSERT INTO gedungs (id,nama_gedung,alamat,deskripsi,fungsi,jumlah_lantai,tahun_berdiri,kondisi,x,y,foto_utama,created_at,updated_at,deleted_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
foreach ($gedungs as $row) {
    $stmt->execute($row);
}
echo "✅ Data gedungs berhasil diimport.\n";

// 4. Insert Data Gambar Gedungs
$gambar = [
    [1, 1, 'gambar iki.jpg', 'gedung/galeri/1773243116_0_gambar iki.jpg', '', 0, '2026-03-11 07:31:56', '2026-03-11 07:32:29', '2026-03-11 07:32:29'],
    [2, 2, 'e7d39fb70c949b40b1b819580d8ce08a.jpg', 'gedung/galeri/1773243643_0_e7d39fb70c949b40b1b819580d8ce08a.jpg', '', 0, '2026-03-11 07:40:43', '2026-03-11 08:12:24', '2026-03-11 08:12:24'],
    [3, 3, 'coki.jpg', 'gedung/galeri/1773273563_0_coki.jpg', '', 0, '2026-03-11 15:59:23', '2026-03-11 15:59:23', null],
];
$stmt2 = $pdo->prepare("INSERT INTO gambar_gedungs (id,gedung_id,nama_file,path_foto,keterangan,urutan,created_at,updated_at,deleted_at) VALUES (?,?,?,?,?,?,?,?,?)");
foreach ($gambar as $row) {
    $stmt2->execute($row);
}
echo "✅ Data gambar_gedungs berhasil diimport.\n";

// 5. Insert Users
$users = [
    [1, 'Muhammad Rizky', 'iki098765@gmail.com', null, '$2y$10$f0VC2E4x6FhXisH/fd5STeFMwsfJch4t09UDQD/O6J29b2Pnpif8W', null, '2026-03-11 05:16:40', '2026-03-11 05:16:40'],
    [2, 'Admin', 'admin@gmail.com', null, '$2y$10$NL7netaxHXbcHBemNgzFxOgWkjXhY80aWFDiSTa0Mvr5T0.t.xE0G', null, '2026-03-11 16:33:39', '2026-03-11 16:33:39'],
];
$stmt3 = $pdo->prepare("INSERT INTO users (id,name,email,email_verified_at,password,remember_token,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)");
foreach ($users as $row) {
    $stmt3->execute($row);
}
echo "✅ Data users berhasil diimport.\n";

// 6. Insert Migrations
$migrations = [
    [1, '2014_10_12_000000_create_users_table', 1],
    [2, '2014_10_12_100000_create_password_resets_table', 1],
    [3, '2019_08_19_000000_create_failed_jobs_table', 1],
    [4, '2019_12_14_000001_create_personal_access_tokens_table', 1],
    [5, '2026_03_11_131511_create_gedungs_table', 2],
    [6, '2026_03_11_132809_create_gambar_gedungs_table', 3],
    [7, '2026_03_11_133126_create_gambar_gedungs_table', 4],
    [8, '2026_03_11_155655_add_foto_utama_to_gedungs_table', 5],
];
$stmt4 = $pdo->prepare("INSERT INTO migrations (id,migration,batch) VALUES (?,?,?)");
foreach ($migrations as $row) {
    $stmt4->execute($row);
}
echo "✅ Data migrations berhasil diimport.\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

echo "\n🎉 Import MySQL selesai!\n";
echo "✅ Sekarang jalankan: php artisan serve\n";
