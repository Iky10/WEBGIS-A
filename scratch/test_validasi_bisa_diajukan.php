<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GedungFasilitas;
use App\Models\User;
use App\Http\Requests\CreatePengajuanRuanganRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Facades\Auth;

echo "=== Test Validasi bisa_diajukan ===" . PHP_EOL . PHP_EOL;

// Login sebagai user untuk throttle check
$user = User::where('role', 'user')->first();
if (!$user) {
    echo "User tidak ditemukan, abort." . PHP_EOL;
    exit;
}
Auth::login($user);

// Helper untuk run validasi
function runValidation($input) {
    $request = CreatePengajuanRuanganRequest::create('/test', 'POST', $input);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $rules = $request->rules();
    $validator = ValidatorFacade::make($input, $rules, $request->messages());

    // Trigger withValidator
    $request->withValidator($validator);
    $validator->fails();

    return $validator->errors()->all();
}

// Cari ruangan dengan kombinasi flag berbeda
$auditorium = GedungFasilitas::where('nama_fasilitas', 'Auditorium TRPL')->first(); // is_aktif=T, bisa_diajukan=T
$k101       = GedungFasilitas::where('nama_fasilitas', 'K.101')->first();             // is_aktif=T, bisa_diajukan=F
$pos1       = GedungFasilitas::where('nama_fasilitas', 'Pos 1')->first();             // is_aktif=T, bisa_diajukan=F

if (!$auditorium || !$k101 || !$pos1) {
    echo "Seed data tidak lengkap. Jalankan: php artisan db:seed --class=GedungFasilitasSeeder" . PHP_EOL;
    exit;
}

$tanggalEsok = now()->addDay()->toDateString();
$baseInput = [
    'nama_pemohon'   => 'Test User',
    'email_pemohon'  => 'test@example.com',
    'no_telepon'     => '081234567890',
    'asal_instansi'  => 'Politani Samarinda',
    'jenis_kegiatan' => 'Seminar',
    'nama_kegiatan'  => 'Test Seminar',
    'tanggal_mulai'  => $tanggalEsok,
    'tanggal_selesai'=> $tanggalEsok,
    'jam_mulai'      => '09:00',
    'jam_selesai'    => '11:00',
    'jumlah_peserta' => 50,
    'keperluan'      => 'Testing',
];

// Test 1: Auditorium TRPL (is_aktif=T, bisa_diajukan=T) -> harus PASS
echo "[Test 1] Auditorium TRPL (bisa_diajukan=T)" . PHP_EOL;
$input1 = array_merge($baseInput, ['gedung_fasilitas_id' => $auditorium->id]);
$errors1 = runValidation($input1);
if (empty($errors1)) {
    echo "   RESULT: PASS (no errors) - sesuai expected" . PHP_EOL;
} else {
    echo "   RESULT: FAIL - errors: " . implode('; ', $errors1) . PHP_EOL;
}
echo PHP_EOL;

// Test 2: K.101 (is_aktif=T, bisa_diajukan=F) -> harus FAIL dengan pesan spesifik
echo "[Test 2] K.101 (bisa_diajukan=F)" . PHP_EOL;
$input2 = array_merge($baseInput, ['gedung_fasilitas_id' => $k101->id]);
$errors2 = runValidation($input2);
$expected = 'tidak dibuka untuk pengajuan publik';
$found = false;
foreach ($errors2 as $err) {
    if (strpos($err, $expected) !== false) { $found = true; break; }
}
if ($found) {
    echo "   RESULT: PASS (rejected dengan pesan benar)" . PHP_EOL;
    echo "   Errors: " . implode('; ', $errors2) . PHP_EOL;
} else {
    echo "   RESULT: FAIL - expected error tidak muncul" . PHP_EOL;
    echo "   Actual errors: " . implode('; ', $errors2) . PHP_EOL;
}
echo PHP_EOL;

// Test 3: Pos 1 (is_aktif=T, bisa_diajukan=F) -> harus FAIL juga
echo "[Test 3] Pos 1 (bisa_diajukan=F)" . PHP_EOL;
$input3 = array_merge($baseInput, ['gedung_fasilitas_id' => $pos1->id]);
$errors3 = runValidation($input3);
$found = false;
foreach ($errors3 as $err) {
    if (strpos($err, $expected) !== false) { $found = true; break; }
}
if ($found) {
    echo "   RESULT: PASS (rejected dengan pesan benar)" . PHP_EOL;
} else {
    echo "   RESULT: FAIL - expected error tidak muncul" . PHP_EOL;
    echo "   Actual errors: " . implode('; ', $errors3) . PHP_EOL;
}
echo PHP_EOL;

// Test 4: Set Auditorium ke is_aktif=false sementara, validasi harus reject dengan pesan beda
echo "[Test 4] Auditorium TRPL dengan is_aktif=F (sementara)" . PHP_EOL;
$auditorium->is_aktif = false;
$auditorium->save();

$input4 = array_merge($baseInput, ['gedung_fasilitas_id' => $auditorium->id]);
$errors4 = runValidation($input4);
$expected_inactive = 'sedang tidak aktif';
$found = false;
foreach ($errors4 as $err) {
    if (strpos($err, $expected_inactive) !== false) { $found = true; break; }
}
if ($found) {
    echo "   RESULT: PASS (rejected karena is_aktif=false)" . PHP_EOL;
} else {
    echo "   RESULT: FAIL - expected 'sedang tidak aktif' tidak muncul" . PHP_EOL;
    echo "   Actual errors: " . implode('; ', $errors4) . PHP_EOL;
}

// Restore is_aktif
$auditorium->is_aktif = true;
$auditorium->save();
echo "   (Restored: is_aktif=true)" . PHP_EOL;

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;
