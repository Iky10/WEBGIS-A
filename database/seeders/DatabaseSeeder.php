<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Urutan penting: parent table → child table (karena ada foreign keys).
        $this->call([
            // Konfigurasi aplikasi
            AppSettingSeeder::class,

            // Data master
            UserSeeder::class,
            GedungSeeder::class,

            // Data turunan (butuh gedung)
            GedungFasilitasSeeder::class,

            // Data turunan (butuh fasilitas)
            JadwalRuanganSeeder::class,

            // JadwalSemesterSeeder TIDAK dipanggil di sini karena butuh file fisik PDF/JPG.
            // Jalankan manual jika file fisik sudah tersedia:
            //   php artisan db:seed --class=JadwalSemesterSeeder

            // Data turunan (butuh user + gedung)
            PengajuanGedungSeeder::class,
        ]);
    }
}
