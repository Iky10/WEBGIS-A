<?php
// Script untuk import data dari MySQL dump ke SQLite

$sqlFile = __DIR__ . '/database/newpbl6.sql';
$dbFile  = __DIR__ . '/database/database.sqlite';

// Hapus db lama, buat baru
if (file_exists($dbFile)) unlink($dbFile);

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = OFF;');

// DDL SQLite (dibuat manual berdasarkan struktur MySQL dump)
$ddl = <<<SQL

CREATE TABLE IF NOT EXISTS "failed_jobs" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "uuid" TEXT NOT NULL UNIQUE,
  "connection" TEXT NOT NULL,
  "queue" TEXT NOT NULL,
  "payload" TEXT NOT NULL,
  "exception" TEXT NOT NULL,
  "failed_at" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS "gedungs" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "nama_gedung" TEXT NOT NULL,
  "alamat" TEXT NOT NULL,
  "deskripsi" TEXT NOT NULL,
  "fungsi" TEXT NOT NULL,
  "jumlah_lantai" INTEGER NOT NULL,
  "tahun_berdiri" INTEGER NOT NULL,
  "kondisi" TEXT NOT NULL,
  "x" REAL NOT NULL,
  "y" REAL NOT NULL,
  "foto_utama" TEXT DEFAULT NULL,
  "created_at" DATETIME NULL,
  "updated_at" DATETIME NULL,
  "deleted_at" DATETIME NULL
);

CREATE TABLE IF NOT EXISTS "gambar_gedungs" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "gedung_id" INTEGER NOT NULL,
  "nama_file" TEXT NOT NULL,
  "path_foto" TEXT NOT NULL,
  "keterangan" TEXT NOT NULL,
  "urutan" INTEGER NOT NULL,
  "created_at" DATETIME NULL,
  "updated_at" DATETIME NULL,
  "deleted_at" DATETIME NULL,
  FOREIGN KEY ("gedung_id") REFERENCES "gedungs"("id")
);

CREATE TABLE IF NOT EXISTS "migrations" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "migration" TEXT NOT NULL,
  "batch" INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS "password_resets" (
  "email" TEXT NOT NULL,
  "token" TEXT NOT NULL,
  "created_at" DATETIME NULL
);

CREATE TABLE IF NOT EXISTS "personal_access_tokens" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "tokenable_type" TEXT NOT NULL,
  "tokenable_id" INTEGER NOT NULL,
  "name" TEXT NOT NULL,
  "token" TEXT NOT NULL UNIQUE,
  "abilities" TEXT DEFAULT NULL,
  "last_used_at" DATETIME NULL,
  "created_at" DATETIME NULL,
  "updated_at" DATETIME NULL
);

CREATE TABLE IF NOT EXISTS "users" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "name" TEXT NOT NULL,
  "email" TEXT NOT NULL UNIQUE,
  "email_verified_at" DATETIME NULL,
  "password" TEXT NOT NULL,
  "remember_token" TEXT DEFAULT NULL,
  "created_at" DATETIME NULL,
  "updated_at" DATETIME NULL
);

SQL;

$pdo->exec($ddl);
echo "✅ Struktur tabel berhasil dibuat.\n";

// Insert data gedungs
$gedungs = [
    [7, 'Direktorat', 'samratulangi', 'tempat direktorat', 'Perkantoran', 5, 2001, 'Baik', -0.53, 117.12, 'gedung/utama/1773305360_direktorat.jpg', '2026-03-12 00:49:20', '2026-03-12 00:49:20', null],
    [8, 'politani', 'samratulangi', 'kampus', 'Pendidikan', 2, 2003, 'Baik', -0.53, 117.12, 'gedung/utama/1774146496_TRGS.jpg', '2026-03-21 18:28:16', '2026-03-21 18:28:16', null],
];

$stmt = $pdo->prepare("INSERT OR IGNORE INTO gedungs (id,nama_gedung,alamat,deskripsi,fungsi,jumlah_lantai,tahun_berdiri,kondisi,x,y,foto_utama,created_at,updated_at,deleted_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
foreach ($gedungs as $row) {
    $stmt->execute($row);
}

// Insert all gedungs including soft-deleted as demo data
$allGedungs = [
    [1, 'TRPL', 'gunung panjang', 'tes', 'Pendidikan', 2, 2003, 'Baik', -0.53, 117.12, null, '2026-03-11 07:31:16', '2026-03-11 07:32:25', '2026-03-11 07:32:25'],
    [2, 'TRPL', 'samratulangi', 'tes', 'Pendidikan', 2, 2003, 'Baik', 0.02, 116.54, 'gedung/utama/1773245954_stiker.jpg', '2026-03-11 07:35:16', '2026-03-11 15:58:39', '2026-03-11 15:58:39'],
    [3, 'BTP', 'samratulangi', 'coba lagi', 'Perkantoran', 3, 2003, 'Baik', -0.54, 117.12, 'gedung/utama/1773245639_831663635115650.jpg', '2026-03-11 08:13:59', '2026-03-11 19:44:23', '2026-03-11 19:44:23'],
    [4, 'pp', 'gunung panjang', 'tes 3', 'Pendidikan', 1, 1999, 'Sedang', -0.53, 117.12, 'gedung/utama/1773272342_JUSTIN.jpeg', '2026-03-11 15:39:02', '2026-03-11 19:44:25', '2026-03-11 19:44:25'],
    [5, 'BTP', 'rumah fedi', 'tes', 'Pendidikan', 2, 2002, 'Baik', -0.54, 117.18, 'gedung/utama/1773294066_hajime.jpeg', '2026-03-11 19:45:53', '2026-03-12 00:49:48', '2026-03-12 00:49:48'],
    [6, 'TRPL', 'gunung panjang', 'misalanya', 'Pendidikan', 1, 2022, 'Rusak', -0.53, 117.12, 'gedung/utama/1773299517_garok.jpg', '2026-03-11 23:11:57', '2026-03-12 00:49:51', '2026-03-12 00:49:51'],
    [7, 'Direktorat', 'samratulangi', 'tempat direktorat', 'Perkantoran', 5, 2001, 'Baik', -0.53, 117.12, 'gedung/utama/1773305360_direktorat.jpg', '2026-03-12 00:49:20', '2026-03-12 00:49:20', null],
    [8, 'politani', 'samratulangi', 'kampus', 'Pendidikan', 2, 2003, 'Baik', -0.53, 117.12, 'gedung/utama/1774146496_TRGS.jpg', '2026-03-21 18:28:16', '2026-03-21 18:28:16', null],
];

$stmt2 = $pdo->prepare("INSERT OR REPLACE INTO gedungs (id,nama_gedung,alamat,deskripsi,fungsi,jumlah_lantai,tahun_berdiri,kondisi,x,y,foto_utama,created_at,updated_at,deleted_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
foreach ($allGedungs as $row) {
    $stmt2->execute($row);
}
echo "✅ Data gedungs berhasil diimport (" . count($allGedungs) . " baris).\n";

// Insert gambar_gedungs
$gambar = [
    [1, 1, 'gambar iki.jpg', 'gedung/galeri/1773243116_0_gambar iki.jpg', '', 0, '2026-03-11 07:31:56', '2026-03-11 07:32:29', '2026-03-11 07:32:29'],
    [2, 2, 'e7d39fb70c949b40b1b819580d8ce08a.jpg', 'gedung/galeri/1773243643_0_e7d39fb70c949b40b1b819580d8ce08a.jpg', '', 0, '2026-03-11 07:40:43', '2026-03-11 08:12:24', '2026-03-11 08:12:24'],
    [3, 3, 'coki.jpg', 'gedung/galeri/1773273563_0_coki.jpg', '', 0, '2026-03-11 15:59:23', '2026-03-11 15:59:23', null],
];
$stmt3 = $pdo->prepare("INSERT OR REPLACE INTO gambar_gedungs (id,gedung_id,nama_file,path_foto,keterangan,urutan,created_at,updated_at,deleted_at) VALUES (?,?,?,?,?,?,?,?,?)");
foreach ($gambar as $row) {
    $stmt3->execute($row);
}
echo "✅ Data gambar_gedungs berhasil diimport.\n";

// Insert users
$users = [
    [1, 'Muhammad Rizky', 'iki098765@gmail.com', null, '$2y$10$f0VC2E4x6FhXisH/fd5STeFMwsfJch4t09UDQD/O6J29b2Pnpif8W', null, '2026-03-11 05:16:40', '2026-03-11 05:16:40'],
    [2, 'Admin', 'admin@gmail.com', null, '$2y$10$NL7netaxHXbcHBemNgzFxOgWkjXhY80aWFDiSTa0Mvr5T0.t.xE0G', null, '2026-03-11 16:33:39', '2026-03-11 16:33:39'],
];
$stmt4 = $pdo->prepare("INSERT OR REPLACE INTO users (id,name,email,email_verified_at,password,remember_token,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)");
foreach ($users as $row) {
    $stmt4->execute($row);
}
echo "✅ Data users berhasil diimport.\n";

// Insert migrations
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
$stmt5 = $pdo->prepare("INSERT OR REPLACE INTO migrations (id,migration,batch) VALUES (?,?,?)");
foreach ($migrations as $row) {
    $stmt5->execute($row);
}
echo "✅ Data migrations berhasil diimport.\n";

$pdo->exec('PRAGMA foreign_keys = ON;');

// Verify
$count = $pdo->query("SELECT COUNT(*) FROM gedungs WHERE deleted_at IS NULL")->fetchColumn();
echo "\n🎉 Import selesai! Total gedung aktif: $count\n";
echo "📁 Database: $dbFile\n";
echo "✅ Sekarang jalankan: php artisan serve\n";
