<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create pengajuan_ruangans table.
 *
 * Pengajuan penggunaan RUANGAN spesifik (granular — bukan gedung utuh).
 * FK ke gedung_fasilitas (= tabel ruangan). Gedung induk diakses via
 * relasi $pengajuan->ruangan->gedung.
 *
 * Kolom audit trail: approved_by (user_id admin) + approved_at (waktu keputusan).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_ruangans', function (Blueprint $table) {
            $table->id();

            // Identifikasi
            $table->string('kode_pengajuan', 32)->unique();
            $table->foreignId('gedung_fasilitas_id')
                  ->constrained('gedung_fasilitas')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Data pemohon
            $table->string('nama_pemohon');
            $table->string('email_pemohon');
            $table->string('no_telepon', 20);
            $table->string('asal_instansi');

            // Data kegiatan
            $table->string('jenis_kegiatan');
            $table->string('nama_kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('jumlah_peserta')->nullable();
            $table->text('keperluan')->nullable();

            // Status & keputusan admin
            $table->enum('status', ['diproses', 'disetujui', 'ditolak'])->default('diproses');
            $table->text('catatan_admin')->nullable();

            // Audit trail
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreign('approved_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Index untuk query cek bentrok (overlap) yang sering dipakai
            // Nama index dipendekkan karena MySQL membatasi identifier max 64 chars
            $table->index(
                ['gedung_fasilitas_id', 'status', 'tanggal_mulai', 'tanggal_selesai'],
                'pr_overlap_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_ruangans');
    }
};
