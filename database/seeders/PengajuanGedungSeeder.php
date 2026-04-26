<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanGedung;
use App\Models\User;
use App\Models\Gedung;

class PengajuanGedungSeeder extends Seeder
{
    /**
     * Seed data contoh pengajuan gedung
     */
    public function run()
    {
        $admin = User::where('email', 'admin@webgis.com')->first();
        $user = User::where('email', 'user@webgis.com')->first();
        $gedung = Gedung::first();

        if (!$gedung) {
            return;
        }

        // Pengajuan dari Admin
        if ($admin) {
            PengajuanGedung::firstOrCreate(
                ['kode_pengajuan' => 'PG-20260426-001'],
                [
                    'gedung_id' => $gedung->id,
                    'user_id' => $admin->id,
                    'nama_pemohon' => 'Admin WebGIS',
                    'email_pemohon' => 'admin@webgis.com',
                    'no_telepon' => '081234567890',
                    'asal_instansi' => 'Politani Samarinda',
                    'jenis_kegiatan' => 'Seminar',
                    'nama_kegiatan' => 'Test Seminar Nasional',
                    'tanggal_mulai' => '2026-05-01',
                    'tanggal_selesai' => '2026-05-01',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '12:00',
                    'jumlah_peserta' => 50,
                    'keperluan' => 'Pengujian fitur pengajuan',
                    'status' => 'diproses',
                ]
            );
        }

        // Pengajuan dari User Biasa
        if ($user) {
            PengajuanGedung::firstOrCreate(
                ['kode_pengajuan' => 'PG-20260426-002'],
                [
                    'gedung_id' => $gedung->id,
                    'user_id' => $user->id,
                    'nama_pemohon' => 'User Biasa',
                    'email_pemohon' => 'user@webgis.com',
                    'no_telepon' => '089876543210',
                    'asal_instansi' => 'PT Contoh Mandiri',
                    'jenis_kegiatan' => 'Workshop',
                    'nama_kegiatan' => 'Workshop Flutter Development',
                    'tanggal_mulai' => '2026-05-15',
                    'tanggal_selesai' => '2026-05-15',
                    'jam_mulai' => '09:00',
                    'jam_selesai' => '16:00',
                    'jumlah_peserta' => 30,
                    'keperluan' => 'Pelatihan pengembangan aplikasi mobile',
                    'status' => 'diproses',
                ]
            );
        }
    }
}
