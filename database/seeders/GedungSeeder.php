<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;

class GedungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $gedungs = [
            [
                'nama_gedung' => 'Gedung Rektorat',
                'alamat' => 'Jl. Soekarno Hatta, Samarinda',
                'deskripsi' => 'Gedung utama pusat administrasi kampus yang digunakan untuk kegiatan rektorat dan perkantoran.',
                'fungsi' => 'Perkantoran',
                'jumlah_lantai' => 3,
                'tahun_berdiri' => 2010,
                'kondisi' => 'Baik',
                'x' => -0.4948,
                'y' => 117.1557,
            ],
            [
                'nama_gedung' => 'Gedung Aula Utama',
                'alamat' => 'Jl. Soekarno Hatta, Samarinda',
                'deskripsi' => 'Aula serbaguna untuk kegiatan seminar, wisuda, dan acara besar kampus.',
                'fungsi' => 'Aula Serbaguna',
                'jumlah_lantai' => 2,
                'tahun_berdiri' => 2012,
                'kondisi' => 'Baik',
                'x' => -0.4952,
                'y' => 117.1562,
            ],
            [
                'nama_gedung' => 'Gedung Fakultas Teknik',
                'alamat' => 'Jl. Soekarno Hatta, Samarinda',
                'deskripsi' => 'Gedung perkuliahan Fakultas Teknik dengan laboratorium dan ruang kelas.',
                'fungsi' => 'Perkuliahan',
                'jumlah_lantai' => 4,
                'tahun_berdiri' => 2015,
                'kondisi' => 'Baik',
                'x' => -0.4945,
                'y' => 117.1570,
            ],
        ];

        foreach ($gedungs as $gedung) {
            Gedung::create($gedung);
        }
    }
}
