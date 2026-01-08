@extends('layouts.master')

@section('title', 'Saran Jumlah Produksi')

@section('breadcrumb')
    @parent
    <li class="active">Saran</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">

        <div class="box box-prediksi">
            <div class="box-header header-red">
                <h3 class="box-title">
                    <span class="title-icon"><i class="fa fa-calculator"></i></span>
                    Input Data Perhitungan
                </h3>

                <p class="header-sub">
                    Isi data sesuai kondisi hari ini untuk menghasilkan saran jumlah produksi.
                    <span class="sub-note">
                        <b>Catatan:</b> “Penjualan” di halaman ini = <b>pesanan masuk</b>. Sedangkan “Penjualan” pada template Excel Data Training = <b>jumlah terjual</b>.
                    </span>
                </p>
            </div>

            <div class="box-body body-soft">

                {{-- error validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger modern-alert">
                        <div class="alert-title">
                            <i class="fa fa-exclamation-triangle"></i> Periksa kembali input
                        </div>
                        <ul style="margin-bottom:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('prediksi.hitung') }}" method="post" id="formPrediksi">
                    @csrf

                    <div class="row">
                        {{-- PRODUK --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Produk</label>
                                <select id="produk" name="id_produk" class="form-control input-modern" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($produk as $p)
                                        <option value="{{ $p->id_produk }}"
                                                data-stok="{{ (int)($p->stok ?? 0) }}"
                                                {{ old('id_produk') == $p->id_produk ? 'selected' : '' }}>
                                            {{ $p->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- TANGGAL --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Tanggal Perhitungan</label>
                                <input type="date"
                                       name="tanggal"
                                       class="form-control input-modern"
                                       value="{{ old('tanggal', date('Y-m-d')) }}"
                                       required>
                            </div>
                        </div>

                        {{-- PENJUALAN (pesanan masuk) --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    Penjualan (kg)
                                    <span class="hint">
                                        = pesanan masuk hari ini (beda dengan Data Training: jumlah terjual)
                                    </span>
                                </label>
                                <input type="number"
                                       name="penjualan"
                                       class="form-control input-modern"
                                       value="{{ old('penjualan') }}"
                                       min="0"
                                       required>
                            </div>
                        </div>

                        {{-- WAKTU --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Waktu Produksi (jam)</label>
                                <input type="number"
                                       step="0.01"
                                       name="waktu_produksi"
                                       class="form-control input-modern"
                                       value="{{ old('waktu_produksi') }}"
                                       min="0"
                                       required>
                            </div>
                        </div>

                        {{-- STOK BARANG JADI (AUTO, READONLY) --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    Stok Barang Jadi (kg)
                                    <span class="hint">diambil otomatis dari stok produk</span>
                                </label>

                                {{-- tampilkan readonly --}}
                                <input type="number"
                                       id="stok_display"
                                       class="form-control input-modern"
                                       value="{{ old('stok_barang_jadi', 0) }}"
                                       min="0"
                                       readonly>

                                {{-- tetap dikirim ke controller --}}
                                <input type="hidden"
                                       id="stok_barang_jadi"
                                       name="stok_barang_jadi"
                                       value="{{ old('stok_barang_jadi', 0) }}">
                            </div>
                        </div>

                        {{-- KAPASITAS --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Kapasitas Produksi (kg)</label>
                                <input type="number"
                                       name="kapasitas_produksi"
                                       class="form-control input-modern"
                                       value="{{ old('kapasitas_produksi') }}"
                                       min="0"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-prediksi">
                            <i class="fa fa-calculator"></i> Hitung
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
@endsection


@push('css')
<style>
/* ====== CARD / BOX ====== */
.box-prediksi{
    border: none !important;
    border-radius: 18px !important;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(0,0,0,.08) !important;
}

.header-red{
    background: #b71c1c !important;
    color: #fff !important;
    border-bottom: none !important;
    padding: 22px 24px !important;
}

.header-red .box-title{
    margin: 0 !important;
    font-weight: 900 !important;
    font-size: 20px !important;
    letter-spacing: -.2px;
    display:flex;
    align-items:center;
    gap:12px;
    color:#fff !important;
}

.title-icon{
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: rgba(255,255,255,.16);
    display:flex;
    align-items:center;
    justify-content:center;
}

.header-sub{
    margin: 10px 0 0;
    opacity: .95;
    font-weight: 600;
    font-size: 13px;
    line-height: 1.4;
}

.sub-note{
    display:block;
    margin-top:6px;
    opacity:.95;
}

.body-soft{
    background: #fff;
    padding: 24px !important;
}

/* ====== INPUT ====== */
.input-modern{
    height: 48px !important;
    border-radius: 12px !important;
    border: 2px solid #e5e7eb !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    transition: all .2s ease;
}

.input-modern:focus{
    border-color: #b71c1c !important;
    box-shadow: 0 0 0 4px rgba(183,28,28,.12) !important;
    outline: none !important;
}

.form-group label{
    font-weight: 800 !important;
    color: #374151 !important;
    margin-bottom: 8px !important;
}

.hint{
    display:inline-block;
    margin-left:8px;
    font-weight:700;
    font-size:12px;
    color:#6b7280;
}

/* readonly look */
#stok_display[readonly]{
    background:#f9fafb !important;
}

/* ====== ALERT ====== */
.modern-alert{
    border: none !important;
    border-left: 5px solid #b71c1c !important;
    border-radius: 14px !important;
    padding: 14px 16px !important;
    background: #fdecec !important;
    color: #7f1d1d !important;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
}

.modern-alert .alert-title{
    font-weight: 900;
    margin-bottom: 8px;
}

/* ====== ACTION BUTTON ====== */
.form-actions{
    margin-top: 10px;
    display:flex;
    justify-content:flex-start;
}

.btn-prediksi{
    height: 48px !important;
    border-radius: 999px !important;
    padding: 10px 18px !important;
    font-weight: 900 !important;
    border: none !important;
    background: #b71c1c !important;
    color: #fff !important;
    box-shadow: 0 10px 22px rgba(183,28,28,.28) !important;
    display:inline-flex;
    align-items:center;
    gap:10px;
    transition: all .2s ease;
}

.btn-prediksi:hover{
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(183,28,28,.35) !important;
}

/* mobile */
@media (max-width: 768px){
    .header-red{ padding: 18px 16px !important; }
    .body-soft{ padding: 16px !important; }
    .form-actions{ justify-content: stretch; }
    .btn-prediksi{ width:100%; justify-content:center; }
}
</style>
@endpush

@push('scripts')
<script>
(function(){
    function setStokFromSelected(){
        var select = document.getElementById('produk');
        var opt = select && select.options[select.selectedIndex];
        var stok = 0;

        if (opt && opt.dataset && opt.dataset.stok !== undefined) {
            stok = parseInt(opt.dataset.stok || '0', 10);
            if (isNaN(stok) || stok < 0) stok = 0;
        }

        document.getElementById('stok_display').value = stok;
        document.getElementById('stok_barang_jadi').value = stok;
    }

    document.addEventListener('DOMContentLoaded', function(){
        setStokFromSelected();
        var select = document.getElementById('produk');
        if (select) select.addEventListener('change', setStokFromSelected);
    });
})();
</script>
@endpush
