<?php
/**
 * FULL end-to-end test of updateStatus via HTTP simulation.
 * This simulates exactly what the browser does.
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Bootstrap with a request
$bootstrapReq = Illuminate\Http\Request::capture();
$kernel->handle($bootstrapReq);

echo "=== END-TO-END APPROVAL TEST ===" . PHP_EOL;

// Step 1: Get CSRF token and login
echo "\n[1] Getting CSRF token..." . PHP_EOL;
$token = csrf_token();
echo "Token: " . substr($token, 0, 20) . "..." . PHP_EOL;

// Step 2: Login as admin
echo "\n[2] Logging in as admin..." . PHP_EOL;
$user = App\Models\User::where('email', 'admin@webgis.com')->first();
if (!$user) {
    echo "!!! Admin user not found!" . PHP_EOL;
    exit(1);
}
echo "User: {$user->name} | Role: {$user->role} | isAdmin: " . ($user->isAdmin() ? 'YES' : 'NO') . PHP_EOL;
Auth::login($user);
echo "Auth::check(): " . (Auth::check() ? 'YES' : 'NO') . PHP_EOL;
echo "Auth::user()->role: " . Auth::user()->role . PHP_EOL;

// Step 3: Check current status
echo "\n[3] Checking current pengajuan records..." . PHP_EOL;
$records = App\Models\PengajuanGedung::orderBy('id')->get();
foreach ($records as $r) {
    echo "  ID:{$r->id} | {$r->kode_pengajuan} | Status:{$r->status}" . PHP_EOL;
}

// Step 4: Find a 'diproses' record
$testRecord = App\Models\PengajuanGedung::where('status', 'diproses')->first();
if (!$testRecord) {
    echo "\n!!! NO diproses records found." . PHP_EOL;
    exit(1);
}

echo "\n[4] Testing updateStatus on ID:{$testRecord->id} ({$testRecord->kode_pengajuan})..." . PHP_EOL;

// Step 5: Call the controller method directly (simulating what the form does)
$controller = app()->make(App\Http\Controllers\PengajuanGedungController::class);
$request = new Illuminate\Http\Request();
$request->merge([
    'status' => 'disetujui',
    'catatan_admin' => 'Disetujui via test script'
]);
$request->setMethod('PATCH');

try {
    $response = $controller->updateStatus($request, $testRecord->id);
    echo "Response type: " . get_class($response) . PHP_EOL;
    echo "Response status: " . $response->getStatusCode() . PHP_EOL;
    if ($response->getStatusCode() == 302) {
        echo "Redirect to: " . $response->headers->get('Location') . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo "EXCEPTION: " . get_class($e) . " - " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}

// Step 6: Verify the database
$testRecord->refresh();
echo "\n[5] After update - ID:{$testRecord->id} Status:{$testRecord->status} Catatan:{$testRecord->catatan_admin}" . PHP_EOL;

if ($testRecord->status === 'disetujui') {
    echo "\n✅ SUCCESS: Status berhasil diubah ke 'disetujui'!" . PHP_EOL;
} else {
    echo "\n❌ FAILED: Status masih '{$testRecord->status}'!" . PHP_EOL;
}

// Step 7: Check logs for our message
echo "\n[6] Checking latest log..." . PHP_EOL;
$logFile = storage_path('logs/laravel.log');
$logContent = file_get_contents($logFile);
$lines = explode("\n", $logContent);
$lastLines = array_slice($lines, -10);
foreach ($lastLines as $line) {
    if (strpos($line, 'Pengajuan') !== false || strpos($line, 'Email') !== false) {
        echo "  LOG: " . trim($line) . PHP_EOL;
    }
}

echo "\n=== TEST COMPLETE ===" . PHP_EOL;
