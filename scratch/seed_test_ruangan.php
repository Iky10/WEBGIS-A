<?php
/**
 * SCRIPT TEMPORARY untuk testing search/filter feature.
 * Insert 6 ruangan dummy dengan kategori berbeda untuk trigger filter bar (> 4 ruangan).
 *
 * Cleanup: jalankan scratch/cleanup_test_ruangan.php setelah test selesai.
 * Identifier dummy: keterangan starts with "[DUMMY-TEST]"
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Gedung;
use App\Models\GedungFasilitas;

echo "=== Seed Dummy Ruangan untuk Test Filter ===" . PHP_EOL . PHP_EOL;

$trpl = Gedung::where('nama_gedung', 'like', 'TRPL%')->first();
if (!$trpl) {
    echo "Gedung TRPL tidak ditemukan, abort." . PHP_EOL;
    exit;
}

$dummy = [
    [
        'nama_fasilitas' => 'Auditorium TRPL Lantai 2',
        'kategori'       => 'Auditorium',
        'keterangan'     => '[DUMMY-TEST] Auditorium kapasitas besar untuk wisuda',
        'latitude'       => -0.53550000,
        'longitude'      => 117.12420000,
    ],
    [
        'nama_fasilitas' => 'RKU Teknik Sipil',
        'kategori'       => 'Ruang Kuliah Umum (RKU)',
        'keterangan'     => '[DUMMY-TEST] RKU multi-prodi',
        'latitude'       => -0.53560000,
        'longitude'      => 117.12430000,
    ],
    [
        'nama_fasilitas' => 'Ruang Seminar Kebun Raya',
        'kategori'       => 'Ruang Seminar',
        'keterangan'     => '[DUMMY-TEST] Ruang seminar dengan view kebun raya',
        'latitude'       => -0.53570000,
        'longitude'      => 117.12440000,
    ],
    [
        'nama_fasilitas' => 'Lab Komputer 3',
        'kategori'       => 'Laboratorium',
        'keterangan'     => '[DUMMY-TEST] Lab dengan 40 PC i7',
        'latitude'       => -0.53580000,
        'longitude'      => 117.12450000,
    ],
    [
        'nama_fasilitas' => 'Lab Bahasa',
        'kategori'       => 'Laboratorium',
        'keterangan'     => '[DUMMY-TEST] Lab bahasa dengan headphone & soundproof',
        'latitude'       => -0.53590000,
        'longitude'      => 117.12460000,
    ],
    [
        'nama_fasilitas' => 'Ruang Sidang Direktur',
        'kategori'       => 'Ruangan Sekretariatan / Administrasi',
        'keterangan'     => '[DUMMY-TEST] Ruang sidang formal',
        'latitude'       => -0.53600000,
        'longitude'      => 117.12470000,
    ],
];

$created = 0;
foreach ($dummy as $d) {
    $exists = GedungFasilitas::where('nama_fasilitas', $d['nama_fasilitas'])
        ->where('gedung_id', $trpl->id)
        ->first();

    if ($exists) {
        echo "  [SKIP] {$d['nama_fasilitas']} sudah ada" . PHP_EOL;
        continue;
    }

    GedungFasilitas::create([
        'gedung_id'      => $trpl->id,
        'nama_fasilitas' => $d['nama_fasilitas'],
        'kategori'       => $d['kategori'],
        'keterangan'     => $d['keterangan'],
        'latitude'       => $d['latitude'],
        'longitude'      => $d['longitude'],
        'foto_ruangan'   => null,
        'bisa_diajukan'  => true, // Semua dummy = bisa diajukan untuk test filter
    ]);
    $created++;
    echo "  [OK]   {$d['nama_fasilitas']} ({$d['kategori']})" . PHP_EOL;
}

echo PHP_EOL;
echo "Total dummy ruangan dibuat: $created" . PHP_EOL;
echo "Total ruangan bisa_diajukan sekarang: " . GedungFasilitas::where('bisa_diajukan', true)->count() . PHP_EOL;
echo PHP_EOL;
echo "=== SELESAI ===" . PHP_EOL;
echo "Untuk cleanup: php scratch/cleanup_test_ruangan.php" . PHP_EOL;
