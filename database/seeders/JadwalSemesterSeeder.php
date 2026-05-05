<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;
use App\Models\JadwalSemester;

class JadwalSemesterSeeder extends Seeder
{
    /**
     * Seed jadwal semester TRPL (5 semester: 2, 3, 4, 5, 6) TA 2025/2026.
     * Data diambil dari DB rekan tim (newpbl6).
     *
     * CATATAN PENTING:
     * - Seeder ini SENGAJA TIDAK dipanggil dari DatabaseSeeder secara default.
     * - File fisik PDF/JPG belum tersedia (perlu di-copy manual dari laptop rekan tim).
     * - Untuk demo PBL, upload jadwal sendiri via UI Admin → Jadwal Semester.
     * - Jika file fisik sudah ada, jalankan: php artisan db:seed --class=JadwalSemesterSeeder
     */
    public function run()
    {
        $trpl = Gedung::where('nama_gedung', 'like', 'TRPL%')->first();

        if (!$trpl) {
            return;
        }

        // Skip otomatis jika file fisik untuk semester pertama belum ada.
        $sampleFile = public_path('images/jadwal_semester/1777551208_semester2_images.jpg');
        if (!file_exists($sampleFile)) {
            $this->command->warn('JadwalSemesterSeeder dilewati: file fisik jadwal belum ada di public/images/jadwal_semester/');
            return;
        }

        $jadwals = [
            [
                'semester'     => 2,
                'tahun_ajaran' => '2025/2026',
                'file_jadwal'  => 'images/jadwal_semester/1777551208_semester2_images.jpg',
                'keterangan'   => 'Jadwal TRPL Semester 2',
            ],
            [
                'semester'     => 3,
                'tahun_ajaran' => '2025/2026',
                'file_jadwal'  => 'images/jadwal_semester/1777551889_semester3_TRPL1-cover.jpg',
                'keterangan'   => 'Jadwal TRPL Semester 3',
            ],
            [
                'semester'     => 4,
                'tahun_ajaran' => '2025/2026',
                'file_jadwal'  => 'images/jadwal_semester/1777546049_semester4_images.jpg',
                'keterangan'   => 'Jadwal TRPL Semester 4',
            ],
            [
                'semester'     => 5,
                'tahun_ajaran' => '2025/2026',
                'file_jadwal'  => 'images/jadwal_semester/1777265687_semester5_kunci_jawaban_modul5_peta.pdf',
                'keterangan'   => 'Jadwal TRPL Semester 5',
            ],
            [
                'semester'     => 6,
                'tahun_ajaran' => '2025/2026',
                'file_jadwal'  => 'images/jadwal_semester/1777265631_semester6_soal_modul5_peta_spasial.pdf',
                'keterangan'   => 'Jadwal TRPL Semester 6',
            ],
        ];

        foreach ($jadwals as $data) {
            JadwalSemester::updateOrCreate(
                [
                    'gedung_id'    => $trpl->id,
                    'semester'     => $data['semester'],
                    'tahun_ajaran' => $data['tahun_ajaran'],
                ],
                array_merge($data, ['gedung_id' => $trpl->id])
            );
        }
    }
}
