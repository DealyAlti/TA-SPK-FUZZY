<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTraining extends Model
{
    use HasFactory;

    protected $table = 'data_training';
    protected $primaryKey = 'id_data_training';

    protected $fillable = [
        'id_produk',
        'tanggal',
        'penjualan',
        'hasil_produksi',
        'stok_barang_jadi',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
