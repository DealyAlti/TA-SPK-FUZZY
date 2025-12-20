<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToTrainingHarianTable extends Migration
{
    public function up(): void
    {
        Schema::table('training_harian', function (Blueprint $table) {
            $table->foreign('id_produk')
                  ->references('id_produk')
                  ->on('produk')
                  ->onUpdate('restrict')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('training_harian', function (Blueprint $table) {
            $table->dropForeign(['id_produk']);
        });
    }
}
