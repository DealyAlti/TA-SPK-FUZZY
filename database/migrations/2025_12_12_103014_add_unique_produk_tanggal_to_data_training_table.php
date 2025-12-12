<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueProdukTanggalToDataTrainingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_training', function (Blueprint $table) {
            $table->unique(['id_produk', 'tanggal'], 'dt_produk_tanggal_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_training', function (Blueprint $table) {
             $table->dropUnique('dt_produk_tanggal_unique');
        });
    }
}
