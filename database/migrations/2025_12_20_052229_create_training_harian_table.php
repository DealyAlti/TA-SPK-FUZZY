<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('training_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedInteger('id_produk'); // ✅ FIX
            $table->integer('penjualan')->default(0);
            $table->integer('hasil_produksi')->default(0);
            $table->timestamps();

            $table->unique(['tanggal', 'id_produk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_harian');
    }
};
