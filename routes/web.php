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
    Route::post('gedung_fasilitas/{id}/toggle-status', [App\Http\Controllers\GedungFasilitasController::class, 'toggleStatus'])->name('gedung_fasilitas.toggle-status');
    Route::delete('gedung_fasilitas/bulk-delete', [App\Http\Controllers\GedungFasilitasController::class, 'bulkDelete'])->name('gedung_fasilitas.bulk-delete');
    Route::resource('gedung_fasilitas', App\Http\Controllers\GedungFasilitasController::class);
    Route::resource('jadwal_ruangans', App\Http\Controllers\JadwalRuanganController::class);
    Route::resource('jadwal_semester', App\Http\Controllers\JadwalSemesterController::class);
    Route::get('/webgis', [App\Http\Controllers\WebGisController::class, 'index'])->name('webgis.index');

    // Pengajuan Ruangan — route khusus admin
    Route::get('pengajuan_ruangans', [App\Http\Controllers\PengajuanRuanganController::class, 'index'])->name('pengajuan_ruangans.index');
    Route::patch('pengajuan_ruangans/{id}/status', [App\Http\Controllers\PengajuanRuanganController::class, 'updateStatus'])->name('pengajuan_ruangans.update-status');
    Route::delete('pengajuan_ruangans/bulk-delete', [App\Http\Controllers\PengajuanRuanganController::class, 'bulkDelete'])->name('pengajuan_ruangans.bulk-delete');
    Route::delete('pengajuan_ruangans/{pengajuan_ruangan}', [App\Http\Controllers\PengajuanRuanganController::class, 'destroy'])->name('pengajuan_ruangans.destroy');

});


