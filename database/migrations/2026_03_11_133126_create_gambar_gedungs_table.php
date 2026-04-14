<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGambarGedungsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gambar_gedungs', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('gedung_id')->unsigned();
            $table->string('nama_file');
            $table->string('path_foto');
            $table->string('keterangan');
            $table->integer('urutan');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('gedung_id')->references('id')->on('gedungs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('gambar_gedungs');
    }
}
