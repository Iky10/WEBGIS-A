<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drop kolom `is_aktif` dari tabel `gedung_fasilitas`.
 *
 * Rationale (per keputusan kolaborasi tim):
 *   - Field ini practically dead code: ZERO data dengan is_aktif=false
 *   - Tidak ada workflow real yang trigger toggle off (Politani tidak track
 *     ruangan rusak via flag ini)
 *   - Setelah penambahan `bisa_diajukan`, semantic untuk hide-from-form sudah
 *     ter-cover oleh flag baru
 *   - Toggle UI di admin panel decorative (zero usage tercatat)
 *
 *   Catatan untuk tim:
 *   - File ini menyentuh domain Iky10 (gedung_fasilitas migration awal)
 *   - Original migration: 2026_04_15_134941_add_status_to_gedung_fasilitas_table.php
 *   - Down migration di sini akan re-create kolom dengan default true
 *     supaya data tidak hilang permanent kalau perlu rollback
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('gedung_fasilitas', function (Blueprint $table) {
            $table->dropColumn('is_aktif');
        });
    }

    public function down(): void
    {
        Schema::table('gedung_fasilitas', function (Blueprint $table) {
            $table->boolean('is_aktif')
                ->default(true)
                ->after('keterangan');
        });
    }
};
