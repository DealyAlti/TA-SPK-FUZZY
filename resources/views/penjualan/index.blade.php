@extends('layouts.master')

@section('title','Tambah Penjualan')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('penjualan.riwayat') }}">Penjualan</a></li>
    <li class="active">Tambah</li>
@endsection

@section('content')
<div class="row">
<div class="col-lg-10 col-lg-offset-1">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-bottom:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="box">
        <div class="box-header with-border" style="display:flex;align-items:center;justify-content:space-between;">
            <h3 class="box-title"><i class="fa fa-plus"></i> Input Penjualan Harian</h3>
            <a href="{{ route('penjualan.riwayat') }}" class="btn btn-default btn-sm">
                &laquo; Kembali
            </a>
        </div>

        <div class="box-body">

            <form method="POST" action="{{ route('penjualan.store') }}">
                @csrf

                <div class="form-group" style="max-width:260px;">
                    <label>Tanggal</label>
                    <input type="date"
                           name="tanggal"
                           class="form-control"
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           required>
                </div>

                <hr>

                <p style="margin-bottom:10px;">
                    Isi jumlah terjual (kg). Kalau tidak ada penjualan, biarkan 0/kosong.
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Produk</th>
                                <th style="width:170px;">Stok Saat Ini</th>
                                <th style="width:220px;">Jumlah Terjual (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produk as $i => $p)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>
                                        <b>{{ $p->nama_produk }}</b>
                                        <input type="hidden" name="produk[{{ $i }}][id_produk]" value="{{ $p->id_produk }}">
                                    </td>
                                    <td>{{ number_format($p->stok) }} kg</td>
                                    <td>
                                        <input type="number"
                                               name="produk[{{ $i }}][jumlah]"
                                               class="form-control"
                                               min="0"
                                               step="0.01"
                                               value="{{ old("produk.$i.jumlah", 0) }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-danger">
                    <i class="fa fa-save"></i> Simpan Penjualan
                </button>

            </form>

        </div>
    </div>

</div>
</div>
@endsection
@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin-bottom:0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
