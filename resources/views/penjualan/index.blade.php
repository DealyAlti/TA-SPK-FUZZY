@extends('layouts.master')

@section('title','Tambah Penjualan')

@section('breadcrumb')
    @parent
    <li><a href="{{ route('penjualan.riwayat') }}">Penjualan</a></li>
    <li class="active">Tambah</li>
@endsection

@push('css')
<style>
/* ===== PAGE: Tambah Penjualan ===== */
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
.btn-back{
    background:#fff !important;
    color:#b71c1c !important;
    box-shadow:0 10px 22px rgba(0,0,0,.12);
}
.btn-back:hover{ transform: translateY(-1px); }

.body-soft{ padding:20px !important; background:#fff; }

.note-box{
    background:#fdecec;
    border-left:5px solid #b71c1c;
    border-radius:14px;
    padding:12px 14px;
    color:#7f1d1d;
    font-weight:700;
    margin-bottom:14px;
}

.input-modern{
    height:44px !important;
    border-radius:12px !important;
    border:2px solid #e5e7eb !important;
    padding:10px 12px !important;
}
.input-modern:focus{
    border-color:#b71c1c !important;
    box-shadow:0 0 0 4px rgba(183,28,28,.12) !important;
    outline:none !important;
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
.table-modern tbody tr:hover{
    background:#fdecec !important;
}
.btn-save{
    background:#b71c1c !important;
    color:#fff !important;
    box-shadow:0 12px 24px rgba(183,28,28,.28);
}
.btn-save:hover{ transform: translateY(-1px); }

</style>
@endpush

@section('content')
<div class="row">
<div class="col-lg-10 col-lg-offset-1">

    @if ($errors->any())
        <div class="alert alert-danger" style="border-radius:14px;">
            <ul style="margin-bottom:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="box box-modern">
        <div class="box-header">
            <div class="header-actions">
                <h3 class="box-title"><i class="fa fa-plus"></i> Input Penjualan Harian</h3>
                <a href="{{ route('penjualan.riwayat') }}" class="btn btn-pill btn-back">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="box-body body-soft">

            <form method="POST" action="{{ route('penjualan.store') }}">
                @csrf

                <div class="form-group" style="max-width:320px;">
                    <label style="font-weight:900;">Tanggal</label>
                    <input type="date"
                           name="tanggal"
                           class="form-control input-modern"
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           required>
                </div>

                <div class="note-box">
                    <i class="fa fa-info-circle"></i>
                    Isi jumlah terjual (kg). Kalau tidak ada penjualan, biarkan 0.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-modern">
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
                                               class="form-control input-modern"
                                               min="0"
                                               step="0.01"
                                               value="{{ old("produk.$i.jumlah", 0) }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-pill btn-save" type="submit">
                    <i class="fa fa-save"></i> Simpan Penjualan
                </button>

            </form>

        </div>
    </div>

</div>
</div>
@endsection
