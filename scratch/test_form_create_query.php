<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GedungFasilitas;

echo "=== Test Query Form Create ===" . PHP_EOL . PHP_EOL;

// Replikasi query yang dipakai di PengajuanRuanganController::create()
$ruangans = GedungFasilitas::bisaDiajukan()
    ->aktif()
    ->with('gedung')
    ->orderBy('nama_fasilitas')
    ->get();

echo "Total ruangan untuk form (bisa_diajukan + aktif): " . $ruangans->count() . PHP_EOL;
echo PHP_EOL;

foreach ($ruangans as $r) {
    echo "- " . $r->nama_fasilitas . " (gedung: " . optional($r->gedung)->nama_gedung . ")" . PHP_EOL;
    echo "  status_dipakai: " . $r->status_dipakai . PHP_EOL;
    echo "  jam_buka: " . optional($r->gedung)->jam_buka . " | jam_tutup: " . optional($r->gedung)->jam_tutup . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;
