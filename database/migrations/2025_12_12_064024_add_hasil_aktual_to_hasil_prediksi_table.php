<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasilAktualToHasilPrediksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hasil_prediksi', function (Blueprint $table) {
            $table->double('hasil_aktual')->nullable()->after('jumlah_produksi');
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
            $table->dropColumn('hasil_aktual');
        });
    }
}
