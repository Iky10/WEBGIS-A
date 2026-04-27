# Dokumentasi Credentials dan Workflows WEBGIS-A

Berikut adalah panduan lengkap kredensial sistem dan alur kerja (workflows) pada aplikasi WebGIS Pengajuan Penggunaan Gedung.

## 1. Credentials Default

Sistem ini memiliki dua peran utama: **Admin** dan **User**. Anda dapat menggunakan akun berikut untuk testing, atau mendaftar (register) sebagai pengguna baru.

### 🔑 Admin
*   **Email:** `admin@webgis.com`
*   **Password:** `admin123`
*   **Akses:** Mengelola master data gedung, menyetujui/menolak pengajuan, serta mengelola webgis secara keseluruhan.

### 👤 User
*   **Email:** `user@webgis.com`
*   **Password:** `user123`
*   **Akses:** Melihat peta, mencari gedung, melihat detail fasilitas, dan mengajukan penggunaan gedung yang tersedia.

---

## 2. Workflows Pengajuan Penggunaan Gedung

### Skenario 1: Registrasi Pengguna Baru
1. Pengunjung masuk ke halaman WebGIS.
2. Klik tombol **Register** pada navigasi (atau akses `/register`).
3. Isi data: Nama, Email, Password, dan Konfirmasi Password.
4. Setelah sukses mendaftar, sistem otomatis memberikan role `user` dan mengarahkan pengguna ke halaman peta dengan keadaan sudah *login*.

### Skenario 2: Pengajuan Gedung oleh User
1. Pengguna (dalam keadaan sudah login) melihat peta kampus.
2. Pengguna mencari dan memilih gedung yang ingin diajukan.
   *(Catatan: Gedung seperti Rektorat, Koperasi, dan Ruang Dosen tidak dapat diajukan karena atribut `bisa_diajukan = false`.)*
3. Pengguna mengklik tombol **Ajukan Penggunaan** yang akan mengarah ke formulir `/pengajuan-gedung/create`.
4. Formulir hanya akan menampilkan gedung-gedung yang valid untuk diajukan dalam dropdown.
5. Pengguna mengisi detail pengajuan (Tanggal Mulai, Tanggal Selesai, Kegiatan, dan Lampiran PDF Surat Permohonan).
6. Setelah di-*submit*, pengajuan akan berstatus `Pending`. Pengguna diarahkan ke halaman **Riwayat Pengajuan**.

### Skenario 3: Admin Meninjau Pengajuan
1. Admin login ke sistem menggunakan kredensial admin.
2. Admin membuka dashboard **Pengajuan Gedung**.
3. Admin melihat pengajuan berstatus `Pending`, memeriksa dokumen lampiran, dan memastikan jadwal tidak bentrok.
4. Admin mengubah status menjadi `Disetujui` atau `Ditolak` (disertai catatan atau alasan jika perlu).
5. (Opsional/Jika SMTP aktif) Sistem otomatis mengirim email notifikasi ke pemohon mengenai perubahan status.

### Skenario 4: Melihat Status oleh User
1. Pengguna dapat kembali ke menu **Riwayat Pengajuan Saya** pada navigasi peta publik.
2. Pengguna melihat status terbaru apakah disetujui atau ditolak, dan bisa melihat riwayat pengajuan sebelumnya.
