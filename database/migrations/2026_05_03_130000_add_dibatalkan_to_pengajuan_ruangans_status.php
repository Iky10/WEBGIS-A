<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Tambah nilai 'dibatalkan' ke ENUM status di pengajuan_ruangans.
 *
 * Diperlukan untuk fitur "User Cancel Pengajuan" — user bisa batalkan
 * pengajuannya sendiri selama status masih 'diproses'.
 *
 * Pakai raw SQL karena Laravel Schema builder tidak native support
 * ALTER TYPE untuk ENUM di MySQL.
 */
return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `pengajuan_ruangans`
            MODIFY COLUMN `status` ENUM('diproses','disetujui','ditolak','dibatalkan')
            NOT NULL DEFAULT 'diproses'
        ");
    }

    public function down(): void
    {
        // Pastikan tidak ada row dengan status 'dibatalkan' sebelum rollback
        DB::table('pengajuan_ruangans')
            ->where('status', 'dibatalkan')
            ->update(['status' => 'ditolak']);

        DB::statement("
            ALTER TABLE `pengajuan_ruangans`
            MODIFY COLUMN `status` ENUM('diproses','disetujui','ditolak')
            NOT NULL DEFAULT 'diproses'
        ");
    }
};
