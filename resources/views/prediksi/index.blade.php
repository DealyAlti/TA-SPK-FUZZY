@extends('layouts.master')

@section('title', 'Prediksi Jumlah Produksi')

@section('breadcrumb')
    @parent
    <li class="active">Prediksi</li>
@endsection

@section('content')

<div class="row">

    {{-- ================== FORM INPUT PREDIKSI ================== --}}
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Input Data Prediksi</h3>
            </div>
            <div class="box-body">

                {{-- error validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin-bottom:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="form-horizontal" action="{{ route('prediksi.hitung') }}" method="post">
                    @csrf

                    {{-- PRODUK (SAMA KAYA DI HALAMAN DATA TRAINING) --}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Produk</label>
                        <div class="col-sm-4">
                            <select id="produk"
                                    name="id_produk"
                                    class="form-control select-produk"
                                    required>
                                <option value="">Pilih Produk</option>
                                @foreach ($produk as $p)
                                    <option value="{{ $p->id_produk }}"
                                        {{ old('id_produk') == $p->id_produk ? 'selected' : '' }}>
                                        {{ $p->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- TANGGAL PREDIKSI --}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tanggal Prediksi</label>
                        <div class="col-sm-4">
                            <input type="date"
                                   name="tanggal"
                                   class="form-control"
                                   value="{{ old('tanggal', date('Y-m-d')) }}"
                                   required>
                        </div>
                    </div>

                    {{-- PENJUALAN & WAKTU PRODUKSI --}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Penjualan (kg)</label>
                        <div class="col-sm-4">
                            <input type="number"
                                   name="penjualan"
                                   class="form-control"
                                   value="{{ old('penjualan') }}"
                                   min="0"
                                   required>
                        </div>

                        <label class="col-sm-2 control-label">Waktu Produksi (jam)</label>
                        <div class="col-sm-4">
                            <input type="number"
                                   step="0.01"
                                   name="waktu_produksi"
                                   class="form-control"
                                   value="{{ old('waktu_produksi') }}"
                                   min="0"
                                   required>
                        </div>
                    </div>

                    {{-- STOK & KAPASITAS PRODUKSI --}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Stok Barang Jadi (kg)</label>
                        <div class="col-sm-4">
                            <input type="number"
                                   name="stok_barang_jadi"
                                   class="form-control"
                                   value="{{ old('stok_barang_jadi') }}"
                                   min="0"
                                   required>
                        </div>

                        <label class="col-sm-2 control-label">Kapasitas Produksi (kg)</label>
                        <div class="col-sm-4">
                            <input type="number"
                                   name="kapasitas_produksi"
                                   class="form-control"
                                   value="{{ old('kapasitas_produksi') }}"
                                   min="0"
                                   required>
                        </div>
                    </div>

                    {{-- TOMBOL HITUNG --}}
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-4">
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fa fa-calculator"></i> Hitung Prediksi
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

@endsection

@push('css')
<style>
    /* ========== SERAGAMKAN SEMUA INPUT & SELECT ========== */
    .form-control,
    .select-produk {
        height: 48px !important;       /* tinggi sama */
        font-size: 15px !important;     /* ukuran font sama */
        padding: 8px 14px !important;   /* ruang di dalam */
        border-radius: 10px !important; /* sudut membulat */
    }

    /* tombol juga disamain */
    .btn-danger {
        height: 48px;
        font-size: 15px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }
</style>
@endpush

