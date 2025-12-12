<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokHarianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stok_harian', function (Blueprint $table) {
            $table->increments('id_stok_harian');
            $table->unsignedInteger('id_produk');
            $table->date('tanggal');

            $table->double('stok_awal')->default(0);
            $table->double('stok_akhir')->default(0);

            $table->timestamps();

            $table->unique(['id_produk','tanggal']);

            $table->foreign('id_produk')
                ->references('id_produk')
                ->on('produk')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stok_harian');
    }
}
