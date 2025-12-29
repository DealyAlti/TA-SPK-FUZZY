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
        'hasil_aktual',

        // 🔒 snapshot detail
        'detail_minmax',
        'detail_mu',
        'detail_rules',
        'detail_sum_alpha',
        'detail_sum_alpha_z',
        'detail_z_akhir',
    ];
    protected $casts = [
        'detail_minmax' => 'array',
        'detail_mu'     => 'array',
        'detail_rules'  => 'array',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}
