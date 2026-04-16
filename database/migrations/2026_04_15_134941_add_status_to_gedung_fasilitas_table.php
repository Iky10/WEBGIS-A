<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToGedungFasilitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gedung_fasilitas', function (Blueprint $table) {
            $table->boolean('is_aktif')->default(true)->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gedung_fasilitas', function (Blueprint $table) {
            $table->dropColumn('is_aktif');
        });
    }
}