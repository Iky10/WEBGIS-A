<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GedungFasilitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Verifikasi Drop is_aktif ===" . PHP_EOL . PHP_EOL;

// 1. Cek schema kolom
echo "[1] Schema gedung_fasilitas:" . PHP_EOL;
$columns = Schema::getColumnListing('gedung_fasilitas');
foreach ($columns as $col) {
    $marker = $col === 'is_aktif' ? ' <- SHOULD NOT EXIST!' : '';
    echo "    - $col$marker" . PHP_EOL;
}
$hasIsAktif = in_array('is_aktif', $columns);
echo "    Result: " . ($hasIsAktif ? 'FAIL (is_aktif masih ada)' : 'PASS (is_aktif sudah dihapus)') . PHP_EOL;
echo PHP_EOL;

// 2. Cek scope aktif() sudah hilang
echo "[2] Scope methods di Model:" . PHP_EOL;
$reflection = new ReflectionClass(GedungFasilitas::class);
$methods = array_filter(array_map(fn($m) => $m->name, $reflection->getMethods()), fn($n) => str_starts_with($n, 'scope'));
foreach ($methods as $m) {
    echo "    - $m" . PHP_EOL;
}
$hasAktif = in_array('scopeAktif', $methods);
echo "    Result: " . ($hasAktif ? 'FAIL (scopeAktif masih ada)' : 'PASS (scopeAktif sudah dihapus)') . PHP_EOL;
echo PHP_EOL;

// 3. Cek query form pengajuan tetap work
echo "[3] Query form pengajuan (cuma bisa_diajukan filter):" . PHP_EOL;
$count = GedungFasilitas::bisaDiajukan()->with('gedung')->count();
echo "    Total ruangan bisa diajukan: $count" . PHP_EOL;
$ruangans = GedungFasilitas::bisaDiajukan()->with('gedung')->get(['id', 'nama_fasilitas']);
foreach ($ruangans as $r) {
    echo "    - " . $r->nama_fasilitas . PHP_EOL;
}
echo PHP_EOL;

// 4. Cek Model fillable + casts
echo "[4] Model fillable & casts:" . PHP_EOL;
$model = new GedungFasilitas();
echo "    Fillable: " . implode(', ', $model->getFillable()) . PHP_EOL;
echo "    Casts keys: " . implode(', ', array_keys($model->getCasts())) . PHP_EOL;
$fillableHasIsAktif = in_array('is_aktif', $model->getFillable());
$castsHasIsAktif = isset($model->getCasts()['is_aktif']);
echo "    Result: " . ((!$fillableHasIsAktif && !$castsHasIsAktif) ? 'PASS' : 'FAIL') . PHP_EOL;

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;
