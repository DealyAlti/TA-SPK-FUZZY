@extends('layouts.master')

@section('title','Dashboard Kepala Produksi')

@section('breadcrumb')
@parent
<li class="active">Dashboard</li>
@endsection

@push('css')
<style>
:root{
    --red:#b91c1c; --red-soft:#fee2e2;
    --text:#111827; --muted:#6b7280;
    --border:#e5e7eb; --radius:16px;
    --shadow:0 10px 25px rgba(17,24,39,.06);
}
.wrap{display:flex;flex-direction:column;gap:18px;}
.hero{background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:20px 22px;display:flex;align-items:center;justify-content:space-between;gap:18px;}
.hero h2{margin:0;font-size:20px;font-weight:900;color:var(--text);}
.hero p{margin:6px 0 0;color:var(--muted);font-size:13px;}
.btn-solid{border:none!important;border-radius:12px!important;padding:10px 14px!important;font-weight:900!important;font-size:13px!important;display:inline-flex!important;align-items:center!important;gap:8px!important;background:var(--red)!important;color:#fff!important;box-shadow:0 10px 18px rgba(185,28,28,.18);}
.btn-outline{background:#fff!important;color:var(--red)!important;border:2px solid var(--red)!important;box-shadow:none!important;}
.kpi-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
.kpi{background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:18px;position:relative;overflow:hidden;}
.kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:6px;background:var(--red);}
.kpi .label{color:var(--muted);font-weight:800;font-size:12px;text-transform:uppercase;letter-spacing:.6px;margin-top:10px;}
.kpi .value{font-size:26px;font-weight:900;color:var(--text);margin-top:4px;}
.section{background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.head{padding:16px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;}
.head h3{margin:0;font-weight:900;color:var(--text);font-size:16px;}
.body{padding:18px;}
.table-modern{width:100%;border-collapse:separate;border-spacing:0;}
.table-modern thead th{background:#fff5f5;color:#7f1d1d;font-weight:900;font-size:12px;text-transform:uppercase;letter-spacing:.6px;padding:12px;border-bottom:1px solid var(--border);}
.table-modern tbody td{padding:12px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#374151;vertical-align:middle;}
.badge{display:inline-flex;align-items:center;justify-content:center;padding:6px 12px;border-radius:999px;font-weight:900;font-size:12px;min-width:92px;}
.badge-red{background:var(--red);color:#fff;}
.badge-gray{background:#e5e7eb;color:#4b5563;}
@media(max-width:768px){
    .hero{flex-direction:column;align-items:flex-start}
    .kpi-grid{grid-template-columns:1fr}
}
</style>
@endpush

@section('content')
<div class="wrap">

    <div class="hero">
        <div>
            <h2>Ringkasan Saran Produksi</h2>
            <p>Fokus: melihat saran jumlah produksi (prediksi) untuk setiap produk. (Kepala Produksi)</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('prediksi.riwayat') }}" class="btn-solid btn-outline" style="padding:8px 12px!important;">
                <i class="fa fa-check-circle"></i> Riwayat Perhitungan
            </a>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi">
            <div class="label">Jumlah Saran Hari Ini</div>
            <div class="value">{{ $totalPrediksiHariIni }}</div>
        </div>
        <div class="kpi">
            <div class="label">Total Saran Tersimpan</div>
            <div class="value">{{ $totalPrediksi }}</div>
        </div>
    </div>

    <div class="section">
        <div class="head">
            <h3><i class="fa fa-calendar"></i> Saran Produksi Hari Ini</h3>
            <a href="{{ route('prediksi.riwayat') }}" class="btn-solid btn-outline" style="padding:8px 12px!important;">
                <i class="fa fa-arrow-right"></i> Lihat Semua
            </a>
        </div>
        <div class="body">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Saran Produksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($prediksiHariIni as $r)
                        <tr>
                            <td><b>{{ $r->produk->nama_produk }}</b></td>
                            <td>
                                <span class="badge badge-red">
                                    {{ number_format($r->jumlah_produksi) }} kg
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align:center;color:#9ca3af;padding:18px;">
                                Belum ada saran produksi untuk hari ini.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="head">
            <h3><i class="fa fa-history"></i> Saran Produksi Terbaru</h3>
            <a href="{{ route('prediksi.riwayat') }}" class="btn-solid btn-outline" style="padding:8px 12px!important;">
                <i class="fa fa-arrow-right"></i> Ke Riwayat
            </a>
        </div>
        <div class="body">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Saran Produksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($latestPrediksi as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td><b>{{ $p->produk->nama_produk }}</b></td>
                            <td>
                                <span class="badge badge-red">
                                    {{ number_format($p->jumlah_produksi) }} kg
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center;color:#9ca3af;padding:18px;">
                                Belum ada data saran produksi.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
