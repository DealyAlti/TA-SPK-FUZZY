<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilPrediksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hasil_prediksi', function (Blueprint $table) {
            $table->increments('id_hasil_prediksi');
            $table->unsignedInteger('id_produk');
            $table->date('tanggal'); 
            $table->double('penjualan');
            $table->double('waktu_produksi');
            $table->double('stok_barang_jadi');
            $table->double('kapasitas_produksi');
            $table->double('jumlah_produksi'); 
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
        Schema::dropIfExists('hasil_prediksi');
    }
}
