<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFotoUtamaToGedungsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('gedungs', function (Blueprint $table) {
        $table->string('foto_utama')->nullable()->after('y');
    });
}

public function down()
{
    Schema::table('gedungs', function (Blueprint $table) {
        $table->dropColumn('foto_utama');
    });
}
}
