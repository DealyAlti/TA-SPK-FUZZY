<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokHarian extends Model
{
    protected $table = 'stok_harian';
    protected $primaryKey = 'id_stok_harian';

    protected $fillable = [
        'id_produk', 'tanggal', 'stok_awal', 'stok_akhir'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
