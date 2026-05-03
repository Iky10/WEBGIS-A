<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameKeteranganToKeperluanOnPengajuanGedungsTable extends Migration
{
    /**
     * Rename kolom `keterangan` → `keperluan` agar konsisten dengan model, form, dan view.
     * Sebelumnya terjadi inkonsistensi: kolom DB bernama `keterangan`, sedangkan fillable,
     * form input, dan view menggunakan `keperluan` → data user untuk kolom ini tidak tersimpan.
     */
    public function up()
    {
        Schema::table('pengajuan_gedungs', function (Blueprint $table) {
            $table->renameColumn('keterangan', 'keperluan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('pengajuan_gedungs', function (Blueprint $table) {
            $table->renameColumn('keperluan', 'keterangan');
        });
    }
}
