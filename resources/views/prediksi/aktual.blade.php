@extends('layouts.master')

@section('title','Input Hasil Aktual')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('prediksi.riwayat') }}">Riwayat Prediksi</a></li>
    <li class="active">Input Aktual</li>
@endsection

@section('content')
<div class="row">
<div class="col-lg-8">

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
        <div class="box-header with-border">
            <h3 class="box-title">Input Hasil Aktual</h3>
        </div>

        <form method="POST" action="{{ route('prediksi.riwayat.aktual', $prediksi->id_hasil_prediksi) }}">
            @csrf
            <div class="box-body">

                <table class="table table-bordered">
                    <tr>
                        <th style="width:200px;">Produk</th>
                        <td>{{ $prediksi->produk->nama_produk }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ \Carbon\Carbon::parse($prediksi->tanggal)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Prediksi</th>
                        <td><b>{{ number_format($prediksi->jumlah_produksi) }} kg</b></td>
                    </tr>
                </table>

                <div class="form-group">
                    <label>Hasil Aktual (kg)</label>
                    <input type="number"
                           name="hasil_aktual"
                           class="form-control"
                           min="0"
                           required
                           value="{{ old('hasil_aktual', $prediksi->hasil_aktual) }}">
                    <small class="text-muted">
                        Setelah disimpan, stok produk akan otomatis bertambah sesuai hasil aktual.
                    </small>
                </div>

            </div>

            <div class="box-footer">
                <a href="{{ route('prediksi.riwayat') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <button class="btn btn-primary">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </form>

    </div>

</div>
</div>
@endsection
