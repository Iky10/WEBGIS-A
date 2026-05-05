<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GedungFasilitas;
use App\Models\JadwalRuangan;

class JadwalRuanganSeeder extends Seeder
{
    /**
     * Seed jadwal reguler ruangan.
     * Data diambil dari DB rekan tim (newpbl6) untuk ruang K.101.
     */
    public function run()
    {
        $k101 = GedungFasilitas::where('nama_fasilitas', 'K.101')->first();

        if (!$k101) {
            return;
        }

        $jadwals = [
            [
                'hari'          => 'Senin',
                'nama_kegiatan' => 'Perkuliahan',
                'jam_mulai'     => '08:40:00',
                'jam_selesai'   => '15:00:00',
                'keterangan'    => null,
            ],
            [
                'hari'          => 'Selasa',
                'nama_kegiatan' => 'Perkuliahan',
                'jam_mulai'     => '07:30:00',
                'jam_selesai'   => '16:00:00',
                'keterangan'    => null,
            ],
        ];

        foreach ($jadwals as $data) {
            JadwalRuangan::updateOrCreate(
                [
                    'gedung_fasilitas_id' => $k101->id,
                    'hari'                => $data['hari'],
                    'jam_mulai'           => $data['jam_mulai'],
                ],
                array_merge($data, ['gedung_fasilitas_id' => $k101->id])
            );
        }
    }
}
