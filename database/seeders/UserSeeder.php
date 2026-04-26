<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed data user: 1 admin + 1 user biasa
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@webgis.com'],
            [
                'name' => 'Admin WebGIS',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@webgis.com'],
            [
                'name' => 'User Biasa',
                'password' => bcrypt('user123'),
                'role' => 'user',
            ]
        );
    }
}
