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
Route::get('/api/gedung/{id}', [App\Http\Controllers\PublikController::class, 'apiDetail'])->name('api.gedung.detail');

// ── AUTH ──────────────────────────────────────────────────────
Auth::routes();

// ── ADMIN (wajib login) ───────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    Route::get('/home',            [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/admin/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');

    // Hapus foto galeri — harus SEBELUM resource gedungs
    Route::delete('gedungs/foto/{id}', [App\Http\Controllers\GedungController::class, 'destroyFoto'])
        ->name('gedungs.foto.destroy');

    // Gedung CRUD
    Route::resource('gedungs', App\Http\Controllers\GedungController::class);

    // Gambar Gedung
    Route::resource('gambar_gedungs', App\Http\Controllers\GambarGedungController::class);

    // WebGIS Admin
    Route::get('/webgis', [App\Http\Controllers\WebGisController::class, 'index'])->name('webgis.index');

});

// YOGMA HADIR
// Iki Mengetik
// Tidak Konflik (yosa)