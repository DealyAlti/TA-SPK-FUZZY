<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPrediksi extends Model
{
    use HasFactory;
    protected $table = 'hasil_prediksi';
    protected $primaryKey = 'id_hasil_prediksi';

    protected $fillable = [
        'id_produk',
        'tanggal',
        'penjualan',
        'waktu_produksi',
        'stok_barang_jadi',
        'kapasitas_produksi',
        'jumlah_produksi',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
    public function detail()
    {
        return $this->hasOne(HasilPrediksiDetail::class, 'id_hasil_prediksi', 'id_hasil_prediksi');
    }

}
