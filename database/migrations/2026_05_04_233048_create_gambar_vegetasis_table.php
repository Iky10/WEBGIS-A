<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGambarVegetasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gambar_vegetasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vegetasi_id');
            $table->string('nama_file');
            $table->string('path_foto');
            $table->string('keterangan')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vegetasi_id')->references('id')->on('vegetasis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gambar_vegetasis');
    }
}
