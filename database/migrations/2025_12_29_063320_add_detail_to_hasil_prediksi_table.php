<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailToHasilPrediksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hasil_prediksi', function (Blueprint $table) {
            $table->json('detail_minmax')->nullable();
            $table->json('detail_mu')->nullable();
            $table->json('detail_rules')->nullable();

            $table->double('detail_sum_alpha')->nullable();
            $table->double('detail_sum_alpha_z')->nullable();
            $table->double('detail_z_akhir')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hasil_prediksi', function (Blueprint $table) {
            $table->json('detail_minmax')->nullable();
            $table->json('detail_mu')->nullable();
            $table->json('detail_rules')->nullable();

            $table->double('detail_sum_alpha')->nullable();
            $table->double('detail_sum_alpha_z')->nullable();
            $table->double('detail_z_akhir')->nullable();
        });
    }
}
