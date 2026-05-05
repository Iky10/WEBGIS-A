<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gedungs', function (Blueprint $table) {
            $table->time('jam_buka')->nullable()->after('bisa_diajukan');
            $table->time('jam_tutup')->nullable()->after('jam_buka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gedungs', function (Blueprint $table) {
            $table->dropColumn(['jam_buka', 'jam_tutup']);
        });
    }
};
