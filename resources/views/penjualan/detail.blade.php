@extends('layouts.master')

@section('title','Detail Penjualan')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('penjualan.riwayat') }}">Riwayat Penjualan</a></li>
    <li class="active">Detail</li>
@endsection

@section('content')
<div class="row">
<div class="col-lg-12">

    <div class="box">
        <div class="box-header with-border" style="display:flex;align-items:center;justify-content:space-between;">
            <h3 class="box-title">
                <i class="fa fa-calendar"></i>
                Detail Penjualan Tanggal: <b>{{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</b>
            </h3>

            <a href="{{ route('penjualan.riwayat') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="box-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah Terjual</th>
                            <th>Stok Saat Itu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($list as $r)
                            <tr>
                                <td>{{ $r->produk->nama_produk }}</td>
                                <td><b>{{ number_format($r->jumlah) }} kg</b></td>
                                <td>
                                    {{ $r->stok_saat_itu !== null ? number_format($r->stok_saat_itu).' kg' : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data penjualan pada tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
</div>
@endsection
