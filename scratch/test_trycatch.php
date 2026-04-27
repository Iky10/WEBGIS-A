<?php
/**
 * Test: Does ShouldQueue + sync queue + bad SMTP get caught by try-catch?
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$pengajuan = App\Models\PengajuanGedung::with('gedung')->find(1);

echo "Testing try-catch with ShouldQueue mail on sync queue..." . PHP_EOL;

try {
    Illuminate\Support\Facades\Mail::to($pengajuan->email_pemohon)
        ->send(new App\Mail\PengajuanStatusMail($pengajuan));
    echo "Mail dispatched OK" . PHP_EOL;
} catch (\Throwable $e) {
    echo "CAUGHT by try-catch: " . get_class($e) . " - " . substr($e->getMessage(), 0, 100) . PHP_EOL;
}

echo "Script continued after try-catch (this means catch works)" . PHP_EOL;

// Now test updateStatus logic exactly
echo "\n--- Simulating updateStatus logic ---" . PHP_EOL;
$validated = [
    'status' => 'disetujui',
    'catatan_admin' => 'Test simulasi'
];

$pengajuan2 = App\Models\PengajuanGedung::with('gedung')->findOrFail(2);
$pengajuan2->update($validated);
echo "DB Update: OK (status={$pengajuan2->status})" . PHP_EOL;

try {
    Illuminate\Support\Facades\Mail::to($pengajuan2->email_pemohon)
        ->send(new App\Mail\PengajuanStatusMail($pengajuan2));
} catch (\Throwable $e) {
    Illuminate\Support\Facades\Log::warning('Gagal mengirim email: ' . $e->getMessage());
    echo "Email failed but caught. Continuing..." . PHP_EOL;
}

echo "Flash and redirect would happen here. Status in DB: {$pengajuan2->fresh()->status}" . PHP_EOL;

// Reset
$pengajuan2->update(['status' => 'diproses', 'catatan_admin' => null]);
echo "Reset done." . PHP_EOL;
