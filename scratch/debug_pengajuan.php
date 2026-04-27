<?php
/**
 * Debug script: test updateStatus flow without browser
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a dummy request to bootstrap the app properly
$bootstrapRequest = Illuminate\Http\Request::capture();
$kernel->handle($bootstrapRequest);

// Now test the flow
echo "=== PENGAJUAN GEDUNG DEBUG ===" . PHP_EOL;

// 1. Check database records
$records = App\Models\PengajuanGedung::all();
echo "\n--- All Pengajuan Records ---" . PHP_EOL;
foreach ($records as $r) {
    echo "ID: {$r->id} | Kode: {$r->kode_pengajuan} | Status: {$r->status} | Email: {$r->email_pemohon}" . PHP_EOL;
}

// 2. Find a "diproses" record to test
$testRecord = App\Models\PengajuanGedung::where('status', 'diproses')->first();
if (!$testRecord) {
    echo "\n!!! NO 'diproses' records found. All may already be approved/rejected." . PHP_EOL;
    echo "This means the form won't even appear (line 92 check: status === 'diproses')" . PHP_EOL;
    
    // Check if there are any records at all
    $allStatuses = App\Models\PengajuanGedung::pluck('status', 'id');
    echo "\nAll statuses: " . json_encode($allStatuses) . PHP_EOL;
} else {
    echo "\nFound test record ID: {$testRecord->id} Status: {$testRecord->status}" . PHP_EOL;
    
    // 3. Test the actual update logic (without email)
    echo "\n--- Testing direct update ---" . PHP_EOL;
    $testRecord->update([
        'status' => 'disetujui',
        'catatan_admin' => 'Test dari debug script'
    ]);
    $testRecord->refresh();
    echo "After update - Status: {$testRecord->status} | Catatan: {$testRecord->catatan_admin}" . PHP_EOL;
    
    // Reset it back for testing
    $testRecord->update(['status' => 'diproses', 'catatan_admin' => null]);
    echo "Reset back to diproses" . PHP_EOL;
}

// 4. Test email sending
echo "\n--- Testing Mail ---" . PHP_EOL;
try {
    $config = config('mail');
    echo "Mail driver: {$config['default']}" . PHP_EOL;
    echo "Mail host: " . config('mail.mailers.smtp.host') . PHP_EOL;
    echo "Mail port: " . config('mail.mailers.smtp.port') . PHP_EOL;
    echo "Mail username: " . config('mail.mailers.smtp.username') . PHP_EOL;
    echo "Mail encryption: " . config('mail.mailers.smtp.encryption') . PHP_EOL;
    
    // Check queue connection
    echo "Queue connection: " . config('queue.default') . PHP_EOL;
    
    if ($testRecord) {
        $testRecord->load('gedung');
        $mail = new App\Mail\PengajuanStatusMail($testRecord);
        Illuminate\Support\Facades\Mail::to('test@test.com')->send($mail);
        echo "Mail sent successfully!" . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo "Mail ERROR: " . get_class($e) . " - " . $e->getMessage() . PHP_EOL;
}

// 5. Check route generation
echo "\n--- Testing Route Generation ---" . PHP_EOL;
try {
    $url = route('pengajuan_gedungs.update-status', 1);
    echo "Route URL: {$url}" . PHP_EOL;
} catch (\Throwable $e) {
    echo "Route ERROR: " . $e->getMessage() . PHP_EOL;
}

echo "\n=== DEBUG COMPLETE ===" . PHP_EOL;
