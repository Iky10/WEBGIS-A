---
trigger: always_on
---

Standar kualitas kode untuk project WEBGIS-A:

1. WAJIB mengikuti pola Repository Pattern yang sudah ada — JANGAN query langsung di Controller kecuali untuk relasi sederhana.
2. Clean code: DRY (Don't Repeat Yourself), setiap function satu tanggung jawab.
3. Error handling: cek empty() sebelum akses model, return Flash::error() + redirect jika tidak ditemukan.
4. Validasi input: gunakan Request class (CreateXxxRequest / UpdateXxxRequest), JANGAN validasi manual di controller.
5. Penamaan konsisten:
   - Model: PascalCase singular (PengajuanGedung)
   - Table: snake_case plural (pengajuan_gedungs)
   - Controller: PascalCase + Controller (PengajuanGedungController)
   - View folder: snake_case plural (pengajuan_gedungs/)
   - Route resource: snake_case plural (pengajuan_gedungs)
6. Migration: SELALU tambahkan softDeletes() dan timestamps().
7. File .env TIDAK BOLEH di-commit — sudah ada di .gitignore.
