<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariabelFuzzyToKategoriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kategori', function (Blueprint $table) {
            // kapasitas produksi (misal wadah / bak per hari)
            $table->integer('kapasitas_min')->nullable();
            $table->integer('kapasitas_max')->nullable();

            // waktu produksi (misal jam per batch)
            $table->integer('waktu_min')->nullable();
            $table->integer('waktu_max')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->dropColumn(['kapasitas_min', 'kapasitas_max', 'waktu_min', 'waktu_max']);
        });
    }
}
