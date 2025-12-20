<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingHarian extends Model
{
    protected $table = 'training_harian'; // ✅ FIX PENTING
    protected $fillable = [
        'tanggal',
        'id_produk',
        'penjualan',
        'hasil_produksi',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

}
