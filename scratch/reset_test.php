<?php
// Reset test record back to diproses
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::capture());

$p = App\Models\PengajuanGedung::find(1);
$p->update(['status' => 'diproses', 'catatan_admin' => null]);
echo "Reset ID 1 to diproses: OK" . PHP_EOL;

foreach (App\Models\PengajuanGedung::all() as $r) {
    echo "ID:{$r->id} Status:{$r->status}" . PHP_EOL;
}
