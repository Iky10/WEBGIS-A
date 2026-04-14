<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGedungFasilitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gedung_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gedung_id');
            $table->string('nama_fasilitas');
            $table->string('kategori'); // Kelas, Laboratorium, Ruangan, dll
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('gedung_id')->references('id')->on('gedungs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gedung_fasilitas');
    }
}
