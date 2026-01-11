<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToHasilPrediksiDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hasil_prediksi_detail', function (Blueprint $table) {
            $table->foreign('id_hasil_prediksi')
                ->references('id_hasil_prediksi')
                ->on('hasil_prediksi')
                ->onUpdate('restrict')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hasil_prediksi_detail', function (Blueprint $table) {
            $table->dropForeign(['id_hasil_prediksi']);
        });
    }
}
