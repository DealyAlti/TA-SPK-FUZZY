@extends('layouts.master')

@section('title','Riwayat Prediksi')

@section('breadcrumb')
    @parent
    <li class="active">Riwayat Prediksi</li>
@endsection

@section('content')
<div class="row">
<div class="col-lg-12">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Riwayat Prediksi</h3>
        </div>

        <div class="box-body">

            {{-- FILTER --}}
            <form class="form-inline" method="GET" style="margin-bottom:15px;">
                <div class="form-group">
                    <select name="id_produk" class="form-control">
                        <option value="">Semua Produk</option>
                        @foreach($produk as $p)
                            <option value="{{ $p->id_produk }}"
                                {{ request('id_produk') == $p->id_produk ? 'selected' : '' }}>
                                {{ $p->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-danger">
                    <i class="fa fa-filter"></i> Filter
                </button>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Penjualan</th>
                            <th>Waktu</th>
                            <th>Stok</th>
                            <th>Kapasitas</th>
                            <th>Prediksi</th>
                            <th>Aktual</th>
                            <th style="width:200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $r->produk->nama_produk }}</td>
                            <td>{{ number_format($r->penjualan) }} kg</td>
                            <td>{{ number_format($r->waktu_produksi, 2) }} jam</td>
                            <td>{{ number_format($r->stok_barang_jadi) }} kg</td>
                            <td>{{ number_format($r->kapasitas_produksi) }} kg</td>
                            <td><b>{{ number_format($r->jumlah_produksi) }} kg</b></td>
                            <td>
                                @if(!is_null($r->hasil_aktual))
                                    <span class="label label-success">{{ number_format($r->hasil_aktual) }} kg</span>
                                @else
                                    <span class="label label-default">Belum</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-xs btn-primary"
                                   href="{{ route('prediksi.riwayat.formAktual', $r->id_hasil_prediksi) }}">
                                   <i class="fa fa-edit"></i> Input Aktual
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Belum ada riwayat prediksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $riwayat->links() }}

        </div>
    </div>

</div>
</div>
@endsection
