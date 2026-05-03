---
description: Workflow untuk mengerjakan fitur Pengajuan Penggunaan Gedung di branch terpisah
---

# Workflow: Fitur Pengajuan Penggunaan Gedung

## 1. Setup Branch
```bash
git pull origin main
git checkout -b fitur/pengajuan-gedung
```

## 2. Buat Migration
// turbo
```bash
php artisan make:migration create_pengajuan_gedungs_table
```
Lalu edit file migration sesuai schema di implementation_plan.md.

## 3. Buat Model
// turbo
```bash
php artisan make:model PengajuanGedung
```
Tambahkan `SoftDeletes`, `HasFactory`, `$fillable`, `$casts`, `$rules`, dan relasi `gedung()`.

## 4. Buat Repository
Buat file `app/Repositories/PengajuanGedungRepository.php` mengikuti pola `GedungRepository.php`.

## 5. Buat Request Validation
// turbo
```bash
php artisan make:request CreatePengajuanGedungRequest
php artisan make:request UpdatePengajuanGedungRequest
```

## 6. Buat Controller
// turbo
```bash
php artisan make:controller PengajuanGedungController
```
Implementasikan CRUD mengikuti pola `JadwalRuanganController.php`.

## 7. Buat Views
Buat folder `resources/views/pengajuan_gedungs/` dengan file:
- `index.blade.php`
- `create.blade.php`
- `edit.blade.php`
- `show.blade.php`
- `fields.blade.php`
- `table.blade.php`

## 8. Update Routing
Tambahkan di `routes/web.php` dalam group auth:
```php
Route::resource('pengajuan_gedungs', App\Http\Controllers\PengajuanGedungController::class);
```

## 9. Update Sidebar
Tambahkan menu di `resources/views/layouts/menu.blade.php`.

## 10. Test
```bash
php artisan migrate
php artisan serve
```
Buka browser dan test CRUD di `/pengajuan_gedungs`.

## 11. Commit & Push
```bash
git add .
git commit -m "feat: tambah fitur pengajuan penggunaan gedung (form, CRUD, migration)"
git push origin fitur/pengajuan-gedung
```

## 12. Buat Pull Request
Buat Pull Request di GitHub dari `fitur/pengajuan-gedung` → `main`.
