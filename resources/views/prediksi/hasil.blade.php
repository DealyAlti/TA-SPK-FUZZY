@extends('layouts.master')

@section('title', 'Hasil Prediksi')

@section('breadcrumb')
    @parent
    <li class="active">Hasil Prediksi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box box-hasil">
            <div class="box-body">
                {{-- ================== DATA PRODUK ================== --}}
                <p class="produk-box">
                    <span class="produk-label">Produk :</span>
                    <span class="produk-name">{{ $hasil['produk']->nama_produk }}</span>
                </p>

                <p class="info-item">
                    <strong>Tanggal Prediksi :</strong>
                    {{ \Carbon\Carbon::parse($hasil['input']['tanggal'])->format('d/m/Y') }}
                </p>

                {{-- ================== VARIABEL INPUT ================== --}}
                <h3 class="section-title">Variabel Input</h3>

                <ul class="list-unstyled variabel-input">
                    <li>
                        <span class="label text-right">Penjualan</span>
                        <span class="colon">:</span>
                        <span class="value">{{ $hasil['input']['penjualan'] }} kg</span>
                    </li>
                    <li>
                        <span class="label text-right">Waktu Produksi</span>
                        <span class="colon">:</span>
                        <span class="value">{{ $hasil['input']['waktu_produksi'] }} jam</span>
                    </li>
                    <li>
                        <span class="label text-right">Stok Barang Jadi</span>
                        <span class="colon">:</span>
                        <span class="value">{{ $hasil['input']['stok_barang_jadi'] }} kg</span>
                    </li>
                    <li>
                        <span class="label text-right">Kapasitas Produksi</span>
                        <span class="colon">:</span>
                        <span class="value">{{ $hasil['input']['kapasitas_produksi'] }} kg</span>
                    </li>
                </ul>


                {{-- ================== OUTPUT ================== --}}
                <h3 class="section-title">Hasil Output</h3>

                <p class="output-box">
                    <span class="output-label">Jumlah Produksi</span>
                    <span class="output-value">{{ $hasil['z_bulat'] }} kg</span>
                </p>

                {{-- ================== TOMBOL ================== --}}
                <div class="btn-wrap">
                    <a href="{{ route('prediksi.detail') }}" class="btn btn-danger btn-hasil">
                        <i class="fa fa-search"></i> Lihat Perhitungan Lengkap
                    </a>

                    <a href="{{ route('prediksi.index') }}" class="btn btn-default btn-hasil">
                        Prediksi Baru
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    /* ================= PRODUK HIGHLIGHT ================= */
    .produk-box {
        background: #fff6f6;
        border-left: 6px solid #b00000;
        padding: 10px 14px;
        border-radius: 10px;
        margin-bottom: 18px;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .produk-label {
        font-size: 15px;
        font-weight: 600;
        color: #333;
    }

    .produk-name {
        font-size: 16px;
        font-weight: 700;
        color: #b00000;
    }   
    .box-hasil {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        max-width: 650px;
        margin: 0 auto;           /* buat center */
        padding-bottom: 20px;
    }
    /* --- STYLE SECTION TITLE --- */
    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-top: 25px;
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 2px solid #e5e5e5;
        letter-spacing: 0.3px;
    }

    .variabel-input li {
        display: flex;
        padding-bottom: 6px;
        margin-bottom: 6px;
        border-bottom: 1px dashed #ddd;
    }

    /* Label rata kiri */
    .variabel-input .label {
        min-width: 180px;   /* atur lebar kolom label */
        text-align: left;   /* rata kiri */
        font-weight: 600;
        font-size: 15px;
    }

    /* Posisi titik dua tetap sejajar */
    .variabel-input .colon {
        min-width: 25px;
        text-align: center;
        font-weight: bold;
    }

    /* Nilai */
    .variabel-input .value {
        margin-left: 4px;
        font-size: 15px;
        font-weight: 600;
    }


    .variabel-input span {
        color: #555;
    }

    .output-box {
        background: #fdf3f3;
        padding: 18px 20px;
        border-radius: 12px;
        border-left: 6px solid #d73b3b;
        margin-top: 5px;
        margin-bottom: 20px;
        text-align: center;
    }
    .output-label {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #333;
    }
    .output-value {
        display: block;
        font-size: 32px;
        font-weight: bold;
        color: #b00000;
        margin-top: 6px;
    }

    .btn-wrap {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 15px;
    }
    .btn-hasil {
        border-radius: 10px;
        padding: 10px 20px;
        font-size: 15px;
    }
</style>
@endpush
