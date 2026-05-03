<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PengajuanRuangan;
use App\Models\User;
use App\Http\Controllers\PengajuanRuanganController;
use Illuminate\Support\Facades\Auth;

echo "=== Test Endpoint notifikasiPending ===" . PHP_EOL . PHP_EOL;

// Login sebagai admin
$admin = User::where('role', 'admin')->first();
if (!$admin) {
    echo "Admin tidak ditemukan, abort." . PHP_EOL;
    exit;
}
Auth::login($admin);

// Cek count diproses
echo "[1] Count pengajuan diproses: " . PengajuanRuangan::diproses()->count() . PHP_EOL;
echo PHP_EOL;

// Sample pengajuan terbaru
echo "[2] 5 Pengajuan terbaru (status diproses):" . PHP_EOL;
$samples = PengajuanRuangan::with('ruangan.gedung')
    ->diproses()
    ->latest()
    ->limit(5)
    ->get();
foreach ($samples as $p) {
    echo "    - {$p->kode_pengajuan} | {$p->nama_pemohon} | {$p->nama_kegiatan}" . PHP_EOL;
    echo "      Ruangan: " . optional($p->ruangan)->nama_fasilitas . PHP_EOL;
    echo "      Tanggal: {$p->tanggal_mulai} | Created: " . $p->created_at->diffForHumans() . PHP_EOL;
}
echo PHP_EOL;

// Test method via HTTP request simulator
echo "[3] Test via controller method directly:" . PHP_EOL;
$repo = app(\App\Repositories\PengajuanRuanganRepository::class);
$controller = new PengajuanRuanganController($repo);

try {
    $response = $controller->notifikasiPending();
    $data = json_decode($response->getContent(), true);
    echo "    HTTP Status: " . $response->status() . PHP_EOL;
    echo "    Count: " . $data['count'] . PHP_EOL;
    echo "    Items count: " . count($data['items']) . PHP_EOL;
    if (count($data['items']) > 0) {
        $first = $data['items'][0];
        echo "    First item keys: " . implode(', ', array_keys($first)) . PHP_EOL;
        echo "    First item kode: " . $first['kode'] . PHP_EOL;
        echo "    First item ruangan: " . $first['ruangan'] . PHP_EOL;
        echo "    First item gedung: " . $first['gedung'] . PHP_EOL;
        echo "    First item tanggal_mulai: " . $first['tanggal_mulai'] . PHP_EOL;
        echo "    First item is_urgent: " . ($first['is_urgent'] ? 'YES' : 'NO') . PHP_EOL;
        echo "    First item url: " . $first['url'] . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo "    ERROR: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;
