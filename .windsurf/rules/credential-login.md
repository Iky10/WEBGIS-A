---
trigger: model_decision
description: Aturan login dan credential — gunakan saat task melibatkan login, autentikasi, testing user-flow, atau apapun yang butuh kredensial.
---

Aturan login dan credential untuk project WEBGIS-A:

1. JANGAN PERNAH menebak email atau password untuk login.
2. WAJIB baca file `database/seeders/UserSeeder.php` terlebih dahulu untuk mendapatkan credential yang benar.
3. Credential saat ini:
   - Admin: email `admin@webgis.com`, password `admin123`
   - User: email `user@webgis.com`, password `user123`
4. Jika UserSeeder berubah, selalu baca ulang file tersebut sebelum login.
5. Jika credential dari seeder tidak berhasil (misal: database sudah di-seed ulang dengan data berbeda), tanya user — JANGAN coba-coba nebak.
