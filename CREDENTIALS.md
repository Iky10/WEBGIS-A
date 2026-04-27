# 🔐 Credentials WebGIS Gedung

## Akun Bawaan (Seeder)

### Admin
| Field    | Value              |
|----------|--------------------|
| Nama     | Admin WebGIS       |
| Email    | `admin@webgis.com` |
| Password | `admin123`         |
| Role     | `admin`            |

### User
| Field    | Value             |
|----------|-------------------|
| Nama     | User Biasa        |
| Email    | `user@webgis.com` |
| Password | `user123`         |
| Role     | `user`            |

## Cara Reset / Seed Ulang

```bash
php artisan migrate:fresh --seed
```

> **Peringatan:** Perintah di atas akan menghapus SEMUA data dan membuat ulang dari awal.

## Registrasi User Baru

User baru bisa mendaftar melalui:
- Halaman Login → klik **"Register a new membership"**
- Atau langsung akses: `http://localhost:8000/register`

User yang baru mendaftar otomatis mendapat role `user` dan diarahkan ke halaman publik.

## Alur Akses (Role-Based)

### User Biasa (role = `user`)
1. Register / Login → Redirect ke **Halaman Publik**
2. Navbar: Beranda | Peta | Daftar Gedung | **Pengajuan Saya** | Logout
3. Fitur: Buat pengajuan gedung, lihat riwayat, lihat detail

### Admin (role = `admin`)
1. Login → Redirect ke **Dashboard Admin** (AdminLTE)
2. Sidebar: Dashboard | Data Gedung | Master Ruangan | Jadwal Ruangan | Pengajuan Gedung | Riwayat Pengajuan
3. Fitur: CRUD gedung, kelola ruangan, kelola jadwal, approve/tolak pengajuan
