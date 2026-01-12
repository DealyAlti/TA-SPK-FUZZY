<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeputusanProduksi extends Model
{
    use HasFactory;
    protected $table = 'keputusan_produksi';
    protected $primaryKey = 'id_keputusan';

    protected $fillable = [
        'id_produk',
        'id_hasil_prediksi',
        'id_user',
        'tanggal',
        'jumlah_saran',
        'jumlah_keputusan',
        'pakai_saran',
        'diputuskan_pada',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'pakai_saran' => 'boolean',
        'diputuskan_pada' => 'datetime',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function hasilPrediksi()
    {
        return $this->belongsTo(HasilPrediksi::class, 'id_hasil_prediksi', 'id_hasil_prediksi');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user', 'id');
    }
}
