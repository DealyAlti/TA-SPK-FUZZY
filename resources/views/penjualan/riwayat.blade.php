@extends('layouts.master')

@section('title','Riwayat Penjualan')

@section('breadcrumb')
    @parent
    <li class="active">Penjualan</li>
@endsection

@section('content')
<div class="row">
<div class="col-lg-12">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="box">
        <div class="box-header with-border" style="display:flex;align-items:center;justify-content:space-between;">
            <h3 class="box-title">
                <i class="fa fa-history"></i> Riwayat Penjualan
            </h3>

            <a href="{{ route('penjualan.index') }}" class="btn btn-danger btn-sm">
                <i class="fa fa-plus"></i> Tambah Penjualan
            </a>
        </div>

        <div class="box-body">

            {{-- FILTER TANGGAL --}}
            <form class="form-inline" method="GET" style="margin-bottom:15px;">
                <div class="form-group" style="margin-right:8px;">
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="Dari">
                </div>

                <div class="form-group" style="margin-right:8px;">
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="Sampai">
                </div>

                <button class="btn btn-danger">
                    <i class="fa fa-filter"></i> Filter
                </button>

                <a href="{{ route('penjualan.riwayat') }}" class="btn btn-default" style="margin-left:6px;">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Total Terjual</th>
                            <th>Total Item</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ number_format($r->total_terjual) }} kg</td>
                                <td>{{ $r->total_item }}</td>
                                <td>
                                    <a class="btn btn-primary btn-sm"
                                       href="{{ route('penjualan.detail', $r->tanggal) }}">
                                        <i class="fa fa-eye"></i> DETAIL
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data penjualan.</td>
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
