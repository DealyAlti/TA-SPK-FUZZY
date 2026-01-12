<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeputusanProduksiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keputusan_produksi', function (Blueprint $table) {
                        $table->increments('id_keputusan');

            $table->unsignedInteger('id_produk');
            $table->unsignedInteger('id_hasil_prediksi')->nullable();

            // ✅ harus bigint unsigned karena users.id bigint unsigned
            $table->unsignedBigInteger('id_user')->nullable();

            $table->date('tanggal');

            $table->double('jumlah_saran')->default(0);
            $table->double('jumlah_keputusan')->default(0);

            $table->boolean('pakai_saran')->default(false);
            $table->timestamp('diputuskan_pada')->nullable();

            $table->timestamps();

            $table->unique(['tanggal', 'id_produk']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keputusan_produksi');
    }
}
