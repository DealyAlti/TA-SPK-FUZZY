@extends('layouts.master')

@section('title','Penjualan')

@section('breadcrumb')
    @parent
    <li class="active">Penjualan</li>
@endsection

@push('css')
<style>
/* ===== PAGE: Penjualan (Riwayat) ===== */
.box-modern{
    border:none !important;
    border-radius:18px !important;
    overflow:hidden;
    box-shadow:0 12px 30px rgba(0,0,0,.08) !important;
}
.box-modern .box-header{
    background:#b71c1c !important;
    color:#fff !important;
    border-bottom:none !important;
    padding:18px 20px !important;
}
.box-modern .box-title,
.box-modern .box-title i{ color:#fff !important; font-weight:900 !important; }

.header-actions{
    display:flex; align-items:center; justify-content:space-between;
    gap:12px; flex-wrap:wrap;
}
.btn-pill{
    border-radius:999px !important;
    padding:10px 16px !important;
    font-weight:900 !important;
    border:none !important;
    display:inline-flex; align-items:center; gap:10px;
}
.btn-add{
    background:#fff !important;
    color:#b71c1c !important;
    box-shadow:0 10px 22px rgba(0,0,0,.12);
}
.btn-add:hover{ transform: translateY(-1px); }

.body-soft{ padding:20px !important; background:#fff; }

.filter-card{
    background:#fff;
    border:1px solid #eef2f7;
    border-radius:16px;
    padding:12px 12px;
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
    box-shadow:0 8px 18px rgba(0,0,0,.05);
    margin-bottom:14px;
}
.filter-card .form-control{
    height:44px !important;
    border-radius:12px !important;
    border:2px solid #e5e7eb !important;
    padding:10px 12px !important;
}
.btn-filter{
    background:#b71c1c !important;
    color:#fff !important;
}
.btn-reset{
    background:#f3f4f6 !important;
    color:#111827 !important;
}

.table-modern thead th{
    background:#991b1b !important;
    color:#fff !important;
    border:none !important;
    font-weight:900 !important;
    text-transform:uppercase;
    letter-spacing:.4px;
    font-size:12px;
    white-space:nowrap;
}
.table-modern{
    border-radius:16px;
    overflow:hidden;
}
.table-modern tbody td{
    vertical-align:middle !important;
}
.table-modern tbody tr:hover{
    background:#fdecec !important;
}
.badge-soft{
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    background:#f9fafb;
    border:1px solid #e5e7eb;
    font-weight:900;
    color:#374151;
    white-space:nowrap;
}

/* tombol aksi kecil */

.btn-icon-primary{
    background:#2563eb !important; color:#fff !important;
    box-shadow:0 10px 18px rgba(37,99,235,.25);
}
.btn-icon-primary:hover{ transform: translateY(-1px); }

/* pagination laravel */
.pagination>li>a, .pagination>li>span{
    border-radius:10px !important;
    margin:0 3px;
}
</style>
@endpush

@section('content')
<div class="row">
<div class="col-lg-12">

    @if(session('success'))
        <div class="alert alert-success" style="border-radius:14px;">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="box box-modern">
        <div class="box-header">
            <div class="header-actions">
                <h3 class="box-title">
                    <i class="fa fa-history"></i> Riwayat Penjualan
                </h3>

                <a href="{{ route('penjualan.index') }}" class="btn btn-pill btn-add">
                    <i class="fa fa-plus"></i> Tambah Penjualan
                </a>
            </div>
        </div>

        <div class="box-body body-soft">

            {{-- FILTER TANGGAL --}}
            <form class="filter-card" method="GET">
                <div class="form-group" style="margin:0;">
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>

                <div class="form-group" style="margin:0;">
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>

                <button class="btn btn-pill btn-filter" type="submit">
                    <i class="fa fa-filter"></i> Filter
                </button>

                <a href="{{ route('penjualan.riwayat') }}" class="btn btn-pill btn-reset">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-modern">
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
                                <td>
                                    <span class="text-center">
                                        {{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>{{ number_format($r->total_terjual) }} kg</td>
                                <td>{{ $r->total_item }}</td>
                                <td class="text-center">
                                    <a class="btn btn-icon btn-icon-primary"
                                       title="Lihat Detail"
                                       href="{{ route('penjualan.detail', $r->tanggal) }}">
                                        <i class="fa fa-eye"> Detail</i>
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

            <div style="margin-top:12px;">
                {{ $riwayat->links() }}
            </div>

        </div>
    </div>

</div>
</div>
@endsection
