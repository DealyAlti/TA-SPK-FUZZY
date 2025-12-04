<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TambahForeignKeyToDataTrainingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_training', function (Blueprint $table) {
                        // Pastikan kolom id_produk sudah ada
            if (!Schema::hasColumn('data_training', 'id_produk')) {
                $table->unsignedInteger('id_produk');
            }

            // Tambahkan foreign key
            $table->foreign('id_produk', 'fk_data_training_produk')
                  ->references('id_produk')
                  ->on('produk')
                  ->onDelete('cascade')       // kalau produk dihapus → data training ikut terhapus
                  ->onUpdate('cascade');
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
            $table->dropForeign('fk_data_training_produk');
        });
    }
}
