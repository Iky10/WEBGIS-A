<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateXyPrecisionInGedungsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gedungs', function (Blueprint $table) {
            $table->decimal('x', 11, 8)->change();
            $table->decimal('y', 11, 8)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gedungs', function (Blueprint $table) {
            $table->decimal('x')->change();
            $table->decimal('y')->change();
        });
    }
}
