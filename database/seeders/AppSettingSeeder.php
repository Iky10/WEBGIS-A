<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Seed pengaturan awal aplikasi.
     * Nilai diambil dari DB rekan tim (newpbl6) per 30 April 2026.
     */
    public function run()
    {
        $settings = [
            'semester_aktif'     => 'genap',
            'tahun_ajaran_aktif' => '2025/2026',
        ];

        foreach ($settings as $key => $value) {
            AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
