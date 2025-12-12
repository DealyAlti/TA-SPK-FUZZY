<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $fillable = [
        'id_kategori', 'nama_produk', 'kode_produk', 'satuan', 'stok'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function dataTraining()
    {
        return $this->hasMany(DataTraining::class, 'id_produk');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_produk', 'id_produk');
    }



}
