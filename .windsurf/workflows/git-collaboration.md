---
description: Rules dan aturan kolaborasi Git untuk project WEBGIS-A
---

# Git Collaboration Rules — WEBGIS-A

## Aturan Dasar
1. **JANGAN langsung push ke `main`** — Selalu buat branch baru untuk setiap fitur.
2. **Selalu `git pull origin main`** sebelum mulai kerja dan sebelum push.
3. **Commit message harus deskriptif** — Gunakan prefix: `feat:`, `fix:`, `style:`, `refactor:`, `docs:`, `chore:`.

## Naming Convention Branch
- Fitur baru: `fitur/nama-fitur` (contoh: `fitur/pengajuan-gedung`)
- Bug fix: `fix/deskripsi-bug` (contoh: `fix/error-peta-marker`)
- Hotfix urgent: `hotfix/deskripsi`

## Alur Kerja Standard
```bash
# 1. Pastikan main terbaru
git checkout main
git pull origin main

# 2. Buat branch dari main
git checkout -b fitur/nama-fitur

# 3. Kerjakan kode...

# 4. Commit perubahan
git add .
git commit -m "feat: deskripsi singkat perubahan"

# 5. Push branch
git push origin fitur/nama-fitur

# 6. Buat Pull Request di GitHub
```

## Pola Kode Project Ini
- **Model**: `app/Models/` — Eloquent + SoftDeletes + HasFactory
- **Controller**: `app/Http/Controllers/` — Extends AppBaseController, inject Repository
- **Repository**: `app/Repositories/` — Extends BaseRepository
- **Views**: `resources/views/` — AdminLTE, LaravelCollective Form builder
- **Routes**: `routes/web.php` — Resource routes di auth group

## File yang TIDAK BOLEH di-commit
- `.env` (credential database, app key)
- `vendor/` (dependencies, install via `composer install`)
- `node_modules/` (install via `npm install`)
- `storage/` logs dan cache
