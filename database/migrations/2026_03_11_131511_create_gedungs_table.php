<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGedungsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gedungs', function (Blueprint $table) {
            $table->id('id');
            $table->string('nama_gedung');
            $table->text('alamat');
            $table->text('deskripsi');
            $table->string('fungsi');
            $table->integer('jumlah_lantai');
            $table->integer('tahun_berdiri');
            $table->string('kondisi');
            $table->decimal('x');
            $table->decimal('y');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('gedungs');
    }
}
