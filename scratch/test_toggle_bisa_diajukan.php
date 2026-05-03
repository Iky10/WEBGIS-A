<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GedungFasilitas;

echo "=== Test Toggle bisa_diajukan ===" . PHP_EOL . PHP_EOL;

$ruangan = GedungFasilitas::where('nama_fasilitas', 'K.101')->first();
if (!$ruangan) {
    echo "K.101 tidak ditemukan, abort." . PHP_EOL;
    exit;
}

echo "Sebelum: bisa_diajukan = " . ($ruangan->bisa_diajukan ? 'TRUE' : 'FALSE') . PHP_EOL;

// Toggle 1: false -> true
$ruangan->bisa_diajukan = !$ruangan->bisa_diajukan;
$ruangan->save();
echo "Toggle 1: bisa_diajukan = " . ($ruangan->fresh()->bisa_diajukan ? 'TRUE' : 'FALSE') . PHP_EOL;

// Toggle 2: revert
$ruangan->bisa_diajukan = !$ruangan->bisa_diajukan;
$ruangan->save();
echo "Toggle 2 (revert): bisa_diajukan = " . ($ruangan->fresh()->bisa_diajukan ? 'TRUE' : 'FALSE') . PHP_EOL;

echo PHP_EOL . "Test scope chain:" . PHP_EOL;
$count = GedungFasilitas::bisaDiajukan()->aktif()->count();
echo "GedungFasilitas::bisaDiajukan()->aktif()->count() = $count" . PHP_EOL;

echo PHP_EOL . "Test fillable mass-assignment:" . PHP_EOL;
$test = new GedungFasilitas([
    'nama_fasilitas' => 'TEST_TEMP',
    'kategori' => 'Lab',
    'gedung_id' => 1,
    'is_aktif' => false,
    'bisa_diajukan' => true,
]);
echo "  is_aktif (mass-assigned): " . ($test->is_aktif ? 'TRUE' : 'FALSE') . PHP_EOL;
echo "  bisa_diajukan (mass-assigned): " . ($test->bisa_diajukan ? 'TRUE' : 'FALSE') . PHP_EOL;

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;
