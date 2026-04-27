<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBisaDiajukanToGedungsTable extends Migration
{
    /**
     * Gedung tertentu (rektorat, koperasi, dll) tidak bisa diajukan.
     * Kolom boolean ini menjadi filter pada form pengajuan.
     */
    public function up()
    {
        Schema::table('gedungs', function (Blueprint $table) {
            $table->boolean('bisa_diajukan')->default(true)->after('foto_utama');
        });
    }

    public function down()
    {
        Schema::table('gedungs', function (Blueprint $table) {
            $table->dropColumn('bisa_diajukan');
        });
    }
}
