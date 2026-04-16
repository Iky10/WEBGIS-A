<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\GedungFasilitas;
use App\Models\JadwalRuangan;

$deletedF = GedungFasilitas::whereDoesntHave('gedung')->delete();
echo "Deleted $deletedF orphaned facilities.\n";

$deletedJ = JadwalRuangan::whereDoesntHave('fasilitas')
    ->orWhereHas('fasilitas', function($q) {
        $q->whereDoesntHave('gedung');
    })
    ->delete();
echo "Deleted $deletedJ orphaned schedules.\n";
