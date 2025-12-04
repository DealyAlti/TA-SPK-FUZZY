<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BuatDataTrainingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_training', function (Blueprint $table) {
            $table->increments('id_data_training');
            $table->unsignedInteger('id_produk');
            $table->date('tanggal');
            $table->integer('penjualan')->default(0);
            $table->integer('hasil_produksi')->default(0);
            $table->integer('stok_barang_jadi')->default(0);
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
        Schema::dropIfExists('data_training');
    }
}
