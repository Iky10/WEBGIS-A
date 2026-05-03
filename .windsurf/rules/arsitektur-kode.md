---
trigger: always_on
---

Arsitektur kode project WEBGIS-A yang WAJIB diikuti:

1. MODEL (app/Models/):
   - Gunakan Eloquent, extends Model
   - WAJIB trait: SoftDeletes, HasFactory
   - WAJIB definisikan: $table, $fillable, $casts, static $rules
   - Relasi: hasMany(), belongsTo() sesuai kebutuhan
   - Contoh referensi: Gedung.php, JadwalRuangan.php

2. REPOSITORY (app/Repositories/):
   - Extends BaseRepository
   - Definisikan $fieldSearchable
   - Implement getFieldsSearchable() dan model()
   - JANGAN taruh business logic di repository — hanya data access
   - Contoh referensi: GedungRepository.php

3. CONTROLLER (app/Http/Controllers/):
   - Extends AppBaseController
   - Inject Repository via constructor
   - CRUD methods: index, create, store, show, edit, update, destroy
   - Gunakan Flash::success() dan Flash::error() untuk notifikasi
   - Contoh referensi: JadwalRuanganController.php

4. REQUEST (app/Http/Requests/):
   - Buat CreateXxxRequest dan UpdateXxxRequest untuk validasi
   - Contoh referensi: CreateGedungRequest.php

5. VIEWS (resources/views/):
   - @extends('layouts.app')
   - Partial files: fields.blade.php, table.blade.php
   - Gunakan Form:: facade (LaravelCollective) untuk form fields
   - @include('adminlte-templates::common.errors') untuk error display
   - Contoh referensi: jadwal_ruangans/

6. ROUTING (routes/web.php):
   - Route publik di luar middleware group
   - Route admin di dalam Route::middleware(['auth'])->group()
   - Gunakan Route::resource() untuk CRUD

7. SIDEBAR (resources/views/layouts/menu.blade.php):
   - Gunakan Font Awesome icons (fas fa-xxx)
   - Active state: Request::is('xxx*')

JANGAN buat pola baru yang berbeda dari yang sudah ada di codebase.
