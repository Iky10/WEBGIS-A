<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed data user: 1 admin + 1 user biasa.
     *
     * Pakai updateOrCreate (bukan firstOrCreate) supaya role + password
     * SELALU di-reset saat seeder dijalankan ulang. Berguna untuk:
     * - Development environment yang konsisten antar anggota tim.
     * - Memperbaiki database yang user-nya pernah ada sebelum kolom 'role'
     *   ditambah (default jadi 'user', perlu di-update ke 'admin').
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@webgis.com'],
            [
                'name' => 'Admin WebGIS',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@webgis.com'],
            [
                'name' => 'User Biasa',
                'password' => bcrypt('user123'),
                'role' => 'user',
            ]
        );
    }
}
