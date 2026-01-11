<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilPrediksiDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hasil_prediksi_detail', function (Blueprint $table) {
            $table->increments('id_hasil_prediksi_detail');
            $table->unsignedInteger('id_hasil_prediksi');
            // simpan snapshot detail
            $table->longText('detail_minmax')->nullable();
            $table->longText('detail_mu')->nullable();
            $table->longText('detail_rules')->nullable();
            $table->double('detail_sum_alpha')->nullable();
            $table->double('detail_sum_alpha_z')->nullable();
            $table->double('detail_z_akhir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hasil_prediksi_detail');
    }
}
