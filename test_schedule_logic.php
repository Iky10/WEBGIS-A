<?php
// Autoload
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\GedungFasilitas;
use App\Models\JadwalRuangan;

echo "Current Time (App): " . now()->format('Y-m-d H:i:s') . "\n";
echo "Current Day (Indo): " . (function(){
    $hariMap = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];
    return $hariMap[now()->format('l')];
})() . "\n\n";

// Get first facility
$fas = GedungFasilitas::first();
if (!$fas) {
    echo "No facilities found.\n";
    exit;
}

echo "Testing for Room: " . $fas->nama_fasilitas . " (ID: " . $fas->id . ")\n";

// Clear old schedules for testing
JadwalRuangan::where('gedung_fasilitas_id', $fas->id)->delete();

// 1. Create schedule for NOW
$now = now();
$start = $now->copy()->subHour()->format('H:i:s');
$end = $now->copy()->addHour()->format('H:i:s');
$hariIndo = (function(){
    $hariMap = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];
    return $hariMap[now()->format('l')];
})();

JadwalRuangan::create([
    'gedung_fasilitas_id' => $fas->id,
    'nama_kegiatan' => 'Tes Sekarang',
    'hari' => $hariIndo,
    'jam_mulai' => $start,
    'jam_selesai' => $end,
]);

echo "Created schedule: $start - $end on $hariIndo\n";
echo "Dynamic Status (is_aktif): " . ($fas->is_aktif ? 'ACTIVE' : 'INACTIVE') . " (Expected: ACTIVE)\n\n";

// 2. Clear and create schedule for FUTURE
JadwalRuangan::where('gedung_fasilitas_id', $fas->id)->delete();
$startFut = $now->copy()->addHours(2)->format('H:i:s');
$endFut = $now->copy()->addHours(4)->format('H:i:s');

JadwalRuangan::create([
    'gedung_fasilitas_id' => $fas->id,
    'nama_kegiatan' => 'Tes Nanti',
    'hari' => $hariIndo,
    'jam_mulai' => $startFut,
    'jam_selesai' => $endFut,
]);

echo "Created schedule: $startFut - $endFut on $hariIndo\n";
echo "Dynamic Status (is_aktif): " . ($fas->is_aktif ? 'ACTIVE' : 'INACTIVE') . " (Expected: INACTIVE)\n";
