<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom audit trail di tabel pengajuan_gedungs:
 *  - approved_by: user_id admin yang menyetujui/menolak (FK ke users)
 *  - approved_at: timestamp keputusan diambil
 *
 * Tujuan: tracking siapa & kapan keputusan dibuat untuk akuntabilitas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_gedungs', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by')->nullable()->after('catatan_admin');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            $table->foreign('approved_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_gedungs', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'approved_at']);
        });
    }
};
