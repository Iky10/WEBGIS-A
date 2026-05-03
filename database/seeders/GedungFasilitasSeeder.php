<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;
use App\Models\GedungFasilitas;

class GedungFasilitasSeeder extends Seeder
{
    /**
     * Seed data fasilitas/ruangan awal.
     * Data diambil dari DB rekan tim (newpbl6) dengan koordinat ruangan riil.
     */
    public function run()
    {
        $trpl = Gedung::where('nama_gedung', 'like', 'TRPL%')->first();
        $pos  = Gedung::where('nama_gedung', 'Pos Sekuriti')->first();

        // Foto sengaja dikosongkan — silakan upload sendiri via UI Admin → Edit Ruangan.
        if ($trpl) {
            GedungFasilitas::updateOrCreate(
                [
                    'gedung_id'      => $trpl->id,
                    'nama_fasilitas' => 'K.101',
                ],
                [
                    'kategori'      => 'Ruang Kelas',
                    'keterangan'    => 'Ruang Kelas TRPL',
                    'latitude'      => -0.53540327,
                    'longitude'     => 117.12416594,
                    'foto_ruangan'  => null,
                    'is_aktif'      => true,
                ]
            );
        }

        if ($pos) {
            GedungFasilitas::updateOrCreate(
                [
                    'gedung_id'      => $pos->id,
                    'nama_fasilitas' => 'Pos 1',
                ],
                [
                    'kategori'      => 'Post Penjagaan',
                    'keterangan'    => 'Pos Sekuriti 1',
                    'latitude'      => -0.53526464,
                    'longitude'     => 117.12329830,
                    'foto_ruangan'  => null,
                    'is_aktif'      => true,
                ]
            );
        }
    }
}
