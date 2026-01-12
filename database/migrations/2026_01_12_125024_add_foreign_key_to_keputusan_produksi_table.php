<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToKeputusanProduksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keputusan_produksi', function (Blueprint $table) {
             $table->foreign('id_produk')
                ->references('id_produk')
                ->on('produk')
                ->onUpdate('restrict')
                ->onDelete('cascade');

            $table->foreign('id_hasil_prediksi')
                ->references('id_hasil_prediksi')
                ->on('hasil_prediksi')
                ->onUpdate('restrict')
                ->onDelete('set null');

            // ✅ users.id = bigint unsigned, id_user juga sudah unsignedBigInteger
            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->onUpdate('restrict')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keputusan_produksi', function (Blueprint $table) {
            $table->dropForeign(['id_produk']);
            $table->dropForeign(['id_hasil_prediksi']);
            $table->dropForeign(['id_user']);
        });
    }
}
