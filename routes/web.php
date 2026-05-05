<?php

use Illuminate\Support\Facades\Route;

// ── PUBLIK (tanpa login) ──────────────────────────────────────
// Halaman utama langsung peta layar penuh
Route::get('/',            [App\Http\Controllers\PublikController::class, 'home'])        ->name('publik.home');
Route::get('/peta',        [App\Http\Controllers\PublikController::class, 'peta'])        ->name('publik.peta');
Route::get('/gedung',      [App\Http\Controllers\PublikController::class, 'gedung'])      ->name('publik.gedung');
Route::get('/gedung/{id}', [App\Http\Controllers\PublikController::class, 'detailGedung'])->name('publik.gedung.detail');

// API GeoJSON untuk Leaflet (publik)
Route::get('/webgis/geojson', [App\Http\Controllers\WebGisController::class, 'geojson'])->name('webgis.geojson');
Route::get('/webgis/geojson-ruangan', [App\Http\Controllers\WebGisController::class, 'geojsonRuangan'])->name('webgis.geojson.ruangan');
Route::get('/webgis/geojson-vegetasi', [App\Http\Controllers\WebGisController::class, 'geojsonVegetasi'])->name('webgis.geojson.vegetasi');
Route::get('/api/gedung/{id}', [App\Http\Controllers\PublikController::class, 'apiDetail'])->name('api.gedung.detail');
Route::get('/api/gedung/{id}/jadwal-semester', [App\Http\Controllers\PublikController::class, 'apiJadwalSemester'])->name('api.gedung.jadwal-semester');

// ── AUTH ──────────────────────────────────────────────────────
Auth::routes();

// ── WAJIB LOGIN (semua user) ──────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/home',            [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/admin/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');

    // Pengajuan Ruangan — route untuk semua user yang login
    Route::get('pengajuan-ruangan/riwayat', [App\Http\Controllers\PengajuanRuanganController::class, 'riwayat'])->name('pengajuan_ruangans.riwayat');
    Route::get('pengajuan_ruangans/create', [App\Http\Controllers\PengajuanRuanganController::class, 'create'])->name('pengajuan_ruangans.create');
    Route::post('pengajuan_ruangans', [App\Http\Controllers\PengajuanRuanganController::class, 'store'])->name('pengajuan_ruangans.store');
    Route::get('pengajuan_ruangans/{pengajuan_ruangan}', [App\Http\Controllers\PengajuanRuanganController::class, 'show'])->name('pengajuan_ruangans.show');

    // AJAX: live availability check (dipakai di form create)
    Route::post('pengajuan_ruangans/cek-ketersediaan', [App\Http\Controllers\PengajuanRuanganController::class, 'cekKetersediaan'])->name('pengajuan_ruangans.cek-ketersediaan');

    // User: batalkan pengajuan miliknya (hanya saat status 'diproses')
    Route::patch('pengajuan_ruangans/{id}/cancel', [App\Http\Controllers\PengajuanRuanganController::class, 'cancel'])->name('pengajuan_ruangans.cancel');

});

// ── ADMIN ONLY (wajib login + role admin) ─────────────────────
Route::middleware(['auth', 'admin'])->group(function () {

    // Hapus foto galeri — harus SEBELUM resource gedungs
    Route::delete('gedungs/foto/{id}', [App\Http\Controllers\GedungController::class, 'destroyFoto'])
        ->name('gedungs.foto.destroy');

    // Gedung CRUD
    Route::resource('gedungs', App\Http\Controllers\GedungController::class);

    // Gambar Gedung
    Route::resource('gambar_gedungs', App\Http\Controllers\GambarGedungController::class);

    // WebGIS Admin
    Route::post('gedung_fasilitas/{id}/toggle-bisa-diajukan', [App\Http\Controllers\GedungFasilitasController::class, 'toggleBisaDiajukan'])->name('gedung_fasilitas.toggle-bisa-diajukan');
    Route::delete('gedung_fasilitas/bulk-delete', [App\Http\Controllers\GedungFasilitasController::class, 'bulkDelete'])->name('gedung_fasilitas.bulk-delete');
    Route::resource('gedung_fasilitas', App\Http\Controllers\GedungFasilitasController::class);
    Route::resource('jadwal_ruangans', App\Http\Controllers\JadwalRuanganController::class);
    Route::resource('jadwal_semester', App\Http\Controllers\JadwalSemesterController::class);
    
    // Vegetasi CRUD
    Route::delete('vegetasis/foto/{id}', [App\Http\Controllers\VegetasiController::class, 'deleteImage'])
        ->name('vegetasis.foto.destroy');
    Route::resource('vegetasis', App\Http\Controllers\VegetasiController::class);

    Route::get('/webgis', [App\Http\Controllers\WebGisController::class, 'index'])->name('webgis.index');
    
    // Semester Aktif Settings
    Route::get('/semester-aktif', [App\Http\Controllers\SemesterAktifController::class, 'index'])->name('semester_aktif.index');
    Route::post('/semester-aktif', [App\Http\Controllers\SemesterAktifController::class, 'update'])->name('semester_aktif.update');

    // Pengajuan Ruangan — route khusus admin
    Route::get('pengajuan_ruangans', [App\Http\Controllers\PengajuanRuanganController::class, 'index'])->name('pengajuan_ruangans.index');
    Route::patch('pengajuan_ruangans/{id}/status', [App\Http\Controllers\PengajuanRuanganController::class, 'updateStatus'])->name('pengajuan_ruangans.update-status');
    Route::delete('pengajuan_ruangans/bulk-delete', [App\Http\Controllers\PengajuanRuanganController::class, 'bulkDelete'])->name('pengajuan_ruangans.bulk-delete');
    Route::delete('pengajuan_ruangans/{pengajuan_ruangan}', [App\Http\Controllers\PengajuanRuanganController::class, 'destroy'])->name('pengajuan_ruangans.destroy');

    // Manajemen User & Admin (CRUD; tanpa show karena edit sudah cukup)
    Route::resource('users', App\Http\Controllers\UserController::class)->except(['show']);

});

// API publik (tanpa login): dipakai untuk peta & dashboard
Route::get('/api/semester-aktif', [App\Http\Controllers\SemesterAktifController::class, 'apiGetSemesterAktif'])->name('api.semester-aktif');

// ── DEV ONLY: Quick login switcher (testing tanpa form login) ──
// AKTIF HANYA jika APP_DEBUG=true. Return 404 di production.
if (config('app.debug')) {
    Route::get('dev/login-as/{role}', function (string $role) {
        $email = match ($role) {
            'admin' => 'admin@webgis.com',
            'user'  => 'user@webgis.com',
            default => null,
        };
        if (!$email) abort(404);

        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return redirect('/')->with('error', 'User dev belum di-seed. Jalankan: php artisan db:seed --class=UserSeeder');
        }

        \Illuminate\Support\Facades\Auth::login($user);
        return redirect()->intended('/')->with('success', 'Login sebagai ' . $user->name . ' (DEV)');
    })->name('dev.login-as');

    Route::get('dev/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/')->with('success', 'Logout berhasil (DEV)');
    })->name('dev.logout');
}

