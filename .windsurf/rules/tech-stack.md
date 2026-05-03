---
trigger: always_on
---

Tech stack project WEBGIS-A yang WAJIB diikuti tanpa pengecualian:

BACKEND:
- Laravel (PHP) — framework utama
- Eloquent ORM — untuk database interaction
- Repository Pattern (BaseRepository → XxxRepository) — untuk data access layer
- LaravelCollective HTML — untuk Form builder (Form::open, Form::text, dll)
- Flash (Laracasts Flash) — untuk notifikasi sukses/error
- SoftDeletes — untuk semua model (data tidak pernah benar-benar dihapus)

FRONTEND (Admin Panel):
- AdminLTE 3 — template admin dashboard
- Blade Templates — templating engine Laravel
- Bootstrap 4 — CSS framework (bawaan AdminLTE)
- Font Awesome — untuk ikon di sidebar dan UI
- DataTables — untuk tabel data interaktif
- Leaflet.js — untuk peta WebGIS

DATABASE:
- MySQL atau SQLite — RDBMS
- Laravel Migrations — untuk schema versioning

JANGAN gunakan atau tambahkan library/framework di luar daftar ini tanpa persetujuan tim.
