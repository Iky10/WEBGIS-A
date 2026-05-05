<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom `bisa_diajukan` ke tabel `gedung_fasilitas`.
 *
 * Rationale:
 *   Saat ini kolom `is_aktif` dipakai ganda:
 *     - Ruangan tidak operasional (sedang perbaikan)
 *     - Ruangan tidak boleh diajukan user (misal kelas reguler)
 *
 *   Pemisahan semantic:
 *     - `is_aktif`       → ruangan operasional / tidak (untuk tampilan peta, dll)
 *     - `bisa_diajukan`  → ruangan boleh diajukan user untuk penggunaan ad-hoc
 *                          (default: FALSE — admin harus opt-in per ruangan)
 *
 *   Contoh use case Politani:
 *     - K.101 (Ruang Kelas TRPL) → is_aktif=true, bisa_diajukan=false
 *       (operasional tapi tidak boleh diajukan — sudah dipakai jadwal reguler)
 *     - Auditorium TRPL → is_aktif=true, bisa_diajukan=true
 *       (boleh diajukan user untuk seminar, acara, dll)
 *     - RKU PHH → is_aktif=true, bisa_diajukan=true
 *
 *   NOTE: Default FALSE karena kita ingin admin secara EKSPLISIT memilih
 *         ruangan mana yang boleh diajukan, bukan default semuanya bisa.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('gedung_fasilitas', function (Blueprint $table) {
            $table->boolean('bisa_diajukan')
                ->default(false)
                ->after('is_aktif')
                ->comment('TRUE jika ruangan boleh diajukan user untuk penggunaan ad-hoc');
        });
    }

    public function down(): void
    {
        Schema::table('gedung_fasilitas', function (Blueprint $table) {
            $table->dropColumn('bisa_diajukan');
        });
    }
};
