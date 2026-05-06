<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalSemesterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_semester', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gedung_fasilitas_id');
            $table->integer('semester'); // 1, 2, 3, 4, 5, 6, 7, 8
            $table->string('tahun_ajaran')->nullable(); // e.g. "2025/2026"
            $table->string('file_jadwal'); // path to PNG/PDF file
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('gedung_fasilitas_id')->references('id')->on('gedung_fasilitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_semester');
    }
}
