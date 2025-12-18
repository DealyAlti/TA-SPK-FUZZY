@extends('layouts.master')

@section('title','Detail Penjualan')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('penjualan.riwayat') }}">Riwayat Penjualan</a></li>
    <li class="active">Detail</li>
@endsection

@push('css')
<style>
/* ===== PAGE: Detail Penjualan (samain style) ===== */
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

/* tombol pill */
.btn-pill{
    border-radius:999px !important;
    padding:10px 16px !important;
    font-weight:900 !important;
    border:none !important;
    display:inline-flex; align-items:center; gap:10px;
}
.btn-back{
    background:#fff !important;
    color:#b71c1c !important;
    box-shadow:0 10px 22px rgba(0,0,0,.12);
}
.btn-back:hover{ transform: translateY(-1px); }

.body-soft{ padding:20px !important; background:#fff; }

.badge-date{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:6px 12px;
    border-radius:999px;
    background:rgba(255,255,255,.14);
    border:1px solid rgba(255,255,255,.25);
    font-weight:900;
}

/* table */
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
.table-modern tbody td{ vertical-align:middle !important; }
.table-modern tbody tr:hover{ background:#fdecec !important; }

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
</style>
@endpush

@section('content')
<div class="row">
<div class="col-lg-12">

    <div class="box box-modern">
        <div class="box-header">
            <div class="header-actions">
                <h3 class="box-title">
                    <i class="fa fa-calendar"></i>
                    Detail Penjualan
                    <span class="badge-date">
                        {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                    </span>
                </h3>

                <a href="{{ route('penjualan.riwayat') }}" class="btn btn-pill btn-back">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="box-body body-soft">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-modern">
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
                                <td><b>{{ $r->produk->nama_produk }}</b></td>
                                <td>
                                    <span class="badge-soft">{{ number_format($r->jumlah) }} kg</span>
                                </td>
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
