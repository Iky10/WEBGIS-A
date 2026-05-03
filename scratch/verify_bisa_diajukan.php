<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GedungFasilitas;

echo "=== Verifikasi bisa_diajukan ===" . PHP_EOL . PHP_EOL;

$rows = GedungFasilitas::with('gedung')->get(['id', 'gedung_id', 'nama_fasilitas', 'kategori', 'is_aktif', 'bisa_diajukan']);
foreach ($rows as $r) {
    $bd = $r->bisa_diajukan ? '✓' : '✗';
    $ak = $r->is_aktif ? '✓' : '✗';
    $g  = optional($r->gedung)->nama_gedung ?? '?';
    echo sprintf("#%d | %-25s | gedung: %-25s | aktif=%s | bisa_diajukan=%s" . PHP_EOL,
        $r->id, $r->nama_fasilitas, $g, $ak, $bd);
}

echo PHP_EOL;
echo "Total ruangan: " . GedungFasilitas::count() . PHP_EOL;
echo "Total bisa diajukan + aktif: " . GedungFasilitas::bisaDiajukan()->aktif()->count() . PHP_EOL;
