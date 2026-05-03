<?php
/**
 * SCRIPT CLEANUP untuk hapus dummy ruangan test.
 * Hapus row dengan keterangan starts with "[DUMMY-TEST]".
 * Pakai forceDelete supaya tidak masuk soft-delete (benar-benar hilang).
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GedungFasilitas;

echo "=== Cleanup Dummy Ruangan ===" . PHP_EOL . PHP_EOL;

$dummies = GedungFasilitas::withTrashed()
    ->where('keterangan', 'like', '[DUMMY-TEST]%')
    ->get();

echo "Found " . $dummies->count() . " dummy ruangan:" . PHP_EOL;
foreach ($dummies as $d) {
    echo "  - {$d->nama_fasilitas} ({$d->kategori})" . PHP_EOL;
}
echo PHP_EOL;

$deleted = 0;
foreach ($dummies as $d) {
    $d->forceDelete();
    $deleted++;
}

echo "Total dihapus: $deleted" . PHP_EOL;
echo "Total ruangan bisa_diajukan tersisa: " . GedungFasilitas::where('bisa_diajukan', true)->count() . PHP_EOL;
echo PHP_EOL;
echo "=== SELESAI ===" . PHP_EOL;
