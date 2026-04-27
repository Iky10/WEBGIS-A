<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;

class GedungSeeder extends Seeder
{
    /**
     * Seed data gedung awal lengkap dengan foto dan variasi bisa_diajukan.
     */
    public function run()
    {
        // Gedung yang BISA diajukan
        $bisaDiajukan = [
            [
                'nama_gedung'    => 'Gedung Auditorium',
                'alamat'         => 'Kampus Politani Samarinda',
                'deskripsi'      => 'Gedung untuk seminar, wisuda, dan acara besar',
                'x'              => -0.50100000,
                'y'              => 117.10100000,
                'bisa_diajukan'  => true,
                'foto_utama'     => 'images/gedung/utama/auditorium.png',
            ],
            [
                'nama_gedung'    => 'Gedung Serbaguna',
                'alamat'         => 'Kampus Politani Samarinda',
                'deskripsi'      => 'Gedung untuk workshop, pelatihan, dan rapat',
                'x'              => -0.50200000,
                'y'              => 117.10200000,
                'bisa_diajukan'  => true,
                'foto_utama'     => 'images/gedung/utama/serbaguna.png',
            ],
            [
                'nama_gedung'    => 'Gedung Pertemuan',
                'alamat'         => 'Kampus Politani Samarinda',
                'deskripsi'      => 'Ruang pertemuan untuk rapat dan diskusi',
                'x'              => -0.50300000,
                'y'              => 117.10300000,
                'bisa_diajukan'  => true,
                'foto_utama'     => 'images/gedung/utama/pertemuan.png',
            ],
        ];

        // Gedung yang TIDAK bisa diajukan
        $tidakBisaDiajukan = [
            [
                'nama_gedung'    => 'Gedung Rektorat',
                'alamat'         => 'Kampus Politani Samarinda',
                'deskripsi'      => 'Gedung perkantoran pimpinan kampus',
                'x'              => -0.50400000,
                'y'              => 117.10400000,
                'bisa_diajukan'  => false,
                'foto_utama'     => 'images/gedung/utama/rektorat.png',
            ],
            [
                'nama_gedung'    => 'Gedung Koperasi',
                'alamat'         => 'Kampus Politani Samarinda',
                'deskripsi'      => 'Koperasi kampus',
                'x'              => -0.50500000,
                'y'              => 117.10500000,
                'bisa_diajukan'  => false,
                'foto_utama'     => 'images/gedung/utama/koperasi.png',
            ],
        ];

        foreach (array_merge($bisaDiajukan, $tidakBisaDiajukan) as $data) {
            Gedung::updateOrCreate(
                ['nama_gedung' => $data['nama_gedung']],
                $data
            );
        }
    }
}
