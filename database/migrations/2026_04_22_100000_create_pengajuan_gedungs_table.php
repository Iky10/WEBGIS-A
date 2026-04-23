<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanGedungsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_gedungs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gedung_id');
            $table->unsignedBigInteger('user_id');
            $table->string('nama_pemohon');
            $table->string('email_pemohon');
            $table->string('no_telepon')->nullable();
            $table->string('asal_instansi')->nullable();
            $table->string('jenis_kegiatan');
            $table->string('nama_kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('jumlah_peserta')->nullable();
            $table->text('keperluan')->nullable();
            $table->enum('status', ['diproses', 'disetujui', 'ditolak'])->default('diproses');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('gedung_id')->references('id')->on('gedungs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan_gedungs');
    }
}
