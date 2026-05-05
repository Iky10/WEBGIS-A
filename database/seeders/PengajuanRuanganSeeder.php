<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanRuangan;
use App\Models\User;
use App\Models\GedungFasilitas;

class PengajuanRuanganSeeder extends Seeder
{
    /**
     * Seed data contoh pengajuan ruangan.
     * Pastikan GedungFasilitasSeeder sudah dijalankan dulu (ada ruangan K.101).
     */
    public function run()
    {
        $admin   = User::where('email', 'admin@webgis.com')->first();
        $user    = User::where('email', 'user@webgis.com')->first();
        $ruangan = GedungFasilitas::where('nama_fasilitas', 'K.101')->first();

        if (!$ruangan) {
            return;
        }

        // Pengajuan dari Admin (status: diproses)
        if ($admin) {
            PengajuanRuangan::firstOrCreate(
                ['kode_pengajuan' => 'PR-20260426-001'],
                [
                    'gedung_fasilitas_id' => $ruangan->id,
                    'user_id'             => $admin->id,
                    'nama_pemohon'        => 'Admin WebGIS',
                    'email_pemohon'       => 'admin@webgis.com',
                    'no_telepon'          => '081234567890',
                    'asal_instansi'       => 'Politani Samarinda',
                    'jenis_kegiatan'      => 'Seminar',
                    'nama_kegiatan'       => 'Test Seminar Nasional',
                    'tanggal_mulai'       => '2026-05-10',
                    'tanggal_selesai'     => '2026-05-10',
                    'jam_mulai'           => '08:00',
                    'jam_selesai'         => '12:00',
                    'jumlah_peserta'      => 50,
                    'keperluan'           => 'Pengujian fitur pengajuan ruangan',
                    'status'              => 'diproses',
                ]
            );
        }

        // Pengajuan dari User Biasa (status: diproses)
        if ($user) {
            PengajuanRuangan::firstOrCreate(
                ['kode_pengajuan' => 'PR-20260426-002'],
                [
                    'gedung_fasilitas_id' => $ruangan->id,
                    'user_id'             => $user->id,
                    'nama_pemohon'        => 'User Biasa',
                    'email_pemohon'       => 'user@webgis.com',
                    'no_telepon'          => '089876543210',
                    'asal_instansi'       => 'PT Contoh Mandiri',
                    'jenis_kegiatan'      => 'Workshop',
                    'nama_kegiatan'       => 'Workshop Flutter Development',
                    'tanggal_mulai'       => '2026-05-15',
                    'tanggal_selesai'     => '2026-05-15',
                    'jam_mulai'           => '09:00',
                    'jam_selesai'         => '16:00',
                    'jumlah_peserta'      => 30,
                    'keperluan'           => 'Pelatihan pengembangan aplikasi mobile',
                    'status'              => 'diproses',
                ]
            );
        }
    }
}
