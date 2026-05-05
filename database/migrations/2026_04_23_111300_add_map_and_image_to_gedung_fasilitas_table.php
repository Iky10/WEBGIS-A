<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMapAndImageToGedungFasilitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gedung_fasilitas', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('keterangan');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('foto_ruangan')->nullable()->after('longitude');
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
            $table->dropColumn(['latitude', 'longitude', 'foto_ruangan']);
        });
    }
}
