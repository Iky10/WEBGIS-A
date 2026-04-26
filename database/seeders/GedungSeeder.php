<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;

class GedungSeeder extends Seeder
{
    /**
     * Seed data gedung
     */
    public function run()
    {
        Gedung::firstOrCreate(
            ['nama_gedung' => 'Gedung Test'],
            [
                'alamat' => 'Samarinda',
                'deskripsi' => 'Gedung untuk pengujian',
                'x' => -0.50000000,
                'y' => 117.10000000,
            ]
        );
    }
}
