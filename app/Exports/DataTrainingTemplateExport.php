<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataTrainingTemplateExport implements FromArray, WithHeadings
{
    protected $produk;

    public function __construct($produk)
    {
        $this->produk = $produk;
    }

    public function headings(): array
    {
        return [
            ["PRODUCT: {$this->produk->nama_produk} (ID: {$this->produk->id_produk})"],
            ['tanggal', 'penjualan', 'stok_barang_jadi', 'hasil_produksi'],
        ];
    }

    public function array(): array
    {
        return [];
    }
}
