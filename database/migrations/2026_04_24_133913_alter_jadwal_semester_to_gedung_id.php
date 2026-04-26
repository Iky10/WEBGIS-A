<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterJadwalSemesterToGedungId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jadwal_semester', function (Blueprint $table) {
            $table->dropForeign(['gedung_fasilitas_id']);
            $table->renameColumn('gedung_fasilitas_id', 'gedung_id');
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
        Schema::table('jadwal_semester', function (Blueprint $table) {
            $table->dropForeign(['gedung_id']);
            $table->renameColumn('gedung_id', 'gedung_fasilitas_id');
            $table->foreign('gedung_fasilitas_id')->references('id')->on('gedung_fasilitas')->onDelete('cascade');
        });
    }
}
