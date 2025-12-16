@extends('layouts.master')

@section('title','Dashboard')

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@push('css')
<style>
:root{
    --red:#b91c1c;
    --red-dark:#991b1b;
    --red-soft:#fee2e2;

    --text:#111827;
    --muted:#6b7280;
    --border:#e5e7eb;
    --bg:#f9fafb;

    --radius:16px;
    --shadow:0 10px 25px rgba(17,24,39,.06);
    --shadow2:0 16px 35px rgba(17,24,39,.10);
}

.dashboard-wrap{
    display:flex;
    flex-direction:column;
    gap:18px;
}

/* HERO */
.hero{
    background:#fff;
    border:1px solid var(--border);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    padding:20px 22px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:18px;
}
.hero-left h2{
    margin:0;
    font-size:20px;
    font-weight:800;
    color:var(--text);
    letter-spacing:-.2px;
}
.hero-left p{
    margin:6px 0 0;
    color:var(--muted);
    font-size:13px;
}
.hero-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    justify-content:flex-end;
}

.btn-solid{
    border:none !important;
    border-radius:12px !important;
    padding:10px 14px !important;
    font-weight:800 !important;
    font-size:13px !important;
    display:inline-flex !important;
    align-items:center !important;
    gap:8px !important;
    transition:.2s ease;
    box-shadow:0 10px 18px rgba(185,28,28,.18);
}
.btn-solid:hover{
    transform:translateY(-1px);
    box-shadow:0 14px 26px rgba(185,28,28,.22);
}
.btn-red{
    background:var(--red) !important;
    color:#fff !important;
}
.btn-outline-red{
    background:#fff !important;
    color:var(--red) !important;
    border:2px solid var(--red) !important;
    box-shadow:none !important;
}
.btn-outline-red:hover{
    background:var(--red-soft) !important;
}

/* KPI */
.kpi-grid{
    display:grid;
    grid-template-columns:repeat(4, minmax(0, 1fr));
    gap:14px;
}
.kpi{
    background:#fff;
    border:1px solid var(--border);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    padding:18px 18px;
    position:relative;
    overflow:hidden;
    transition:.2s ease;
}
.kpi:hover{
    transform:translateY(-2px);
    box-shadow:var(--shadow2);
}
.kpi::before{
    content:'';
    position:absolute;
    top:0;left:0;right:0;
    height:6px;
    background:var(--red);
}
.kpi .kpi-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
}
.kpi .kpi-icon{
    width:44px;height:44px;
    border-radius:14px;
    background:var(--red-soft);
    color:var(--red);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}
.kpi .kpi-label{
    margin-top:10px;
    color:var(--muted);
    font-weight:700;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.6px;
}
.kpi .kpi-value{
    margin-top:4px;
    font-size:26px;
    font-weight:900;
    color:var(--text);
}
.kpi .kpi-sub{
    margin-top:6px;
    font-size:12px;
    color:var(--muted);
}

/* SECTION */
.section-box{
    background:#fff;
    border:1px solid var(--border);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    overflow:hidden;
}
.section-head{
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
}
.section-title{
    margin:0;
    font-weight:900;
    color:var(--text);
    font-size:16px;
    display:flex;
    align-items:center;
    gap:10px;
}
.section-title .dot{
    width:10px;height:10px;
    border-radius:999px;
    background:var(--red);
}
.section-body{
    padding:18px;
}

/* TABLE */
.table-modern{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
}
.table-modern thead th{
    background:#fff5f5;
    color:#7f1d1d;
    font-weight:900;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.6px;
    padding:12px 12px;
    border-bottom:1px solid var(--border);
}
.table-modern tbody td{
    padding:12px 12px;
    border-bottom:1px solid #f1f5f9;
    font-size:13px;
    color:#374151;
    vertical-align:middle;
}
.table-modern tbody tr:hover{
    background:#fffafa;
}
.badge-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 12px;
    border-radius:999px;
    font-weight:900;
    font-size:12px;
    min-width:92px;
}
.badge-red{ background:var(--red); color:#fff; }
.badge-gray{ background:#e5e7eb; color:#4b5563; }

/* RESPONSIVE */
@media (max-width: 992px){
    .kpi-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px){
    .hero{ flex-direction:column; align-items:flex-start; }
    .hero-actions{ width:100%; justify-content:flex-start; }
    .kpi-grid{ grid-template-columns:1fr; }
}
/* CHECKLIST + ALERT */
.flex-row{ display:flex; gap:12px; flex-wrap:wrap; }
.check-item{
  display:flex; align-items:center; justify-content:space-between;
  gap:12px; padding:12px 14px;
  border:1px solid var(--border);
  border-radius:14px;
  background:#fff;
}
.check-left{ display:flex; align-items:center; gap:10px; }
.check-icon{
  width:34px; height:34px; border-radius:12px;
  display:flex; align-items:center; justify-content:center;
  background:var(--red-soft); color:var(--red);
  font-size:16px;
}
.check-done .check-icon{ background:#dcfce7; color:#166534; }
.check-title{ font-weight:900; color:var(--text); font-size:13px; }
.check-hint{ color:var(--muted); font-size:12px; margin-top:2px; }

.alert-card{
  border-radius:16px;
  padding:14px 16px;
  border:1px solid var(--border);
  background:#fff;
}
.alert-warn{
  border-color:#fecaca;
  background:#fff7ed;
}
.alert-danger{
  border-color:#fecaca;
  background:#fff1f2;
}
.alert-title{ font-weight:900; color:var(--text); font-size:13px; margin:0; }
.alert-text{ color:var(--muted); font-size:12px; margin:6px 0 0; }

.mini-stat{
  display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;
}
.mini-pill{
  background:#fff5f5;
  border:1px solid #fecaca;
  color:#7f1d1d;
  font-weight:900;
  border-radius:999px;
  padding:8px 12px;
  font-size:12px;
}
/* AKURASI BANNER */
.accuracy-box{
    background:#eef9ff;
    border:1px solid #bae6fd;
    border-radius:14px;
    padding:18px 20px;
    text-align:center;
}
.accuracy-title{
    font-size:18px;
    font-weight:900;
    color:#0f172a;
}
.accuracy-value{
    font-size:26px;
    font-weight:900;
    color:#0369a1;
    margin:6px 0;
}
.accuracy-desc{
    font-size:13px;
    color:#475569;
}

</style>
@endpush

@section('content')

<div class="dashboard-wrap">

    {{-- HERO / QUICK ACTIONS --}}
    <div class="hero">
        <div class="hero-left">
            <h2>Ringkasan Operasional Produksi</h2>
            <p>Kelola penjualan, prediksi, dan input produksi aktual. Semua angka dalam satuan Kg.</p>
        </div>

        <div class="hero-actions">
            <a href="{{ route('prediksi.index') }}" class="btn btn-solid btn-red">
                <i class="fa fa-magic"></i> Mulai Prediksi
            </a>
            <a href="{{ route('penjualan.index') }}" class="btn btn-solid btn-outline-red">
                <i class="fa fa-money"></i> Input Penjualan
            </a>
            <a href="{{ route('prediksi.riwayat') }}" class="btn btn-solid btn-outline-red">
                <i class="fa fa-history"></i> Riwayat Prediksi
            </a>
        </div>
    </div>

    {{-- KPI --}}
    <div class="kpi-grid">
        <div class="kpi">
            <div class="kpi-top">
                <div class="kpi-icon"><i class="fa fa-cubes"></i></div>
            </div>
            <div class="kpi-label">Total Produk</div>
            <div class="kpi-value">{{ $totalProduk }}</div>
            <div class="kpi-sub">Jumlah item produk aktif</div>
        </div>

        <div class="kpi">
            <div class="kpi-top">
                <div class="kpi-icon"><i class="fa fa-tags"></i></div>
            </div>
            <div class="kpi-label">Kategori</div>
            <div class="kpi-value">{{ $totalKategori }}</div>
            <div class="kpi-sub">Kategori produk tersedia</div>
        </div>

        <div class="kpi">
            <div class="kpi-top">
                <div class="kpi-icon"><i class="fa fa-database"></i></div>
            </div>
            <div class="kpi-label">Data Training</div>
            <div class="kpi-value">{{ $totalTraining }}</div>
            <div class="kpi-sub">Total record data training</div>
        </div>

        <div class="kpi">
            <div class="kpi-top">
                <div class="kpi-icon"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="kpi-label">Prediksi Terverifikasi</div>
            <div class="kpi-value">{{ $verifikasiCount }}</div>
            <div class="kpi-sub">Jumlah prediksi yang sudah diinput aktual</div>
        </div>
    </div>
    {{-- AKURASI / ERROR --}}
    <div class="section-box">
        <div class="section-body">
            <div class="accuracy-box">
                <div class="accuracy-title">
                    Estimasi Nilai Kesalahan (Error)
                </div>

                <div class="accuracy-value">
                    {{ number_format($errorPercent, 2) }} %
                </div>

                <div class="accuracy-desc">
                    Dihitung menggunakan rumus <b>Mean Absolute Error (MAE)</b>.
                    <br>
                    Semakin kecil nilai error maka hasil prediksi semakin baik.
                </div>
            </div>
        </div>
    </div>

    {{-- CHECKLIST + AKURASI + PERINGATAN --}}
    <div class="row">
        <div class="col-md-6">
            <div class="section-box">
                <div class="section-head">
                    <h3 class="section-title"><span class="dot"></span> Checklist Operasional Harian</h3>
                </div>
                <div class="section-body">
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($checklist as $c)
                            <div class="check-item {{ $c['done'] ? 'check-done' : '' }}">
                                <div class="check-left">
                                    <div class="check-icon">
                                        <i class="fa {{ $c['done'] ? 'fa-check' : 'fa-clock-o' }}"></i>
                                    </div>
                                    <div>
                                        <div class="check-title">{{ $c['label'] }}</div>
                                        <div class="check-hint">{{ $c['hint'] }}</div>
                                    </div>
                                </div>
                                <a href="{{ $c['url'] }}" class="btn btn-solid btn-outline-red" style="padding:8px 12px !important;">
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="section-box">
                <div class="section-head">
                    <h3 class="section-title"><span class="dot"></span> Peringatan</h3>
                </div>
                <div class="section-body">
                    @if(count($warnings) === 0)
                        <div class="alert-card" style="background:#f0fdf4;border-color:#bbf7d0;">
                            <p class="alert-title" style="color:#166534;margin:0;">Aman ✅</p>
                            <p class="alert-text" style="color:#166534;">Tidak ada peringatan hari ini.</p>
                        </div>
                    @else
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            @foreach($warnings as $w)
                                <div class="alert-card {{ $w['type']==='danger' ? 'alert-danger' : 'alert-warn' }}">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                                        <div>
                                            <p class="alert-title">{{ $w['title'] }}</p>
                                            <p class="alert-text">{{ $w['text'] }}</p>
                                        </div>
                                        <a href="{{ $w['url'] }}" class="btn btn-solid btn-outline-red" style="padding:8px 12px !important;">
                                            <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- TABLES --}}
    <div class="row">
        <div class="col-md-6">
            <div class="section-box">
                <div class="section-head">
                    <h3 class="section-title"><span class="dot"></span> Prediksi Terbaru</h3>
                    <a href="{{ route('prediksi.riwayat') }}" class="btn btn-solid btn-outline-red" style="padding:8px 12px !important;">
                        <i class="fa fa-arrow-right"></i> Lihat Semua
                    </a>
                </div>
                <div class="section-body" style="padding-top:10px;">
                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Prediksi</th>
                                    <th>Aktual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestPrediksi as $p)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                                    <td><strong>{{ $p->produk->nama_produk }}</strong></td>
                                    <td><span class="badge-pill badge-red">{{ number_format($p->jumlah_produksi) }} kg</span></td>
                                    <td>
                                        @if($p->hasil_aktual !== null)
                                            <span class="badge-pill badge-red">{{ number_format($p->hasil_aktual) }} kg</span>
                                        @else
                                            <span class="badge-pill badge-gray">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;color:#9ca3af;padding:18px;">
                                        Belum ada data prediksi.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="section-box">
                <div class="section-head">
                    <h3 class="section-title"><span class="dot"></span> Penjualan Terbaru</h3>
                    <a href="{{ route('penjualan.riwayat') }}" class="btn btn-solid btn-outline-red" style="padding:8px 12px !important;">
                        <i class="fa fa-arrow-right"></i> Lihat Semua
                    </a>
                </div>
                <div class="section-body" style="padding-top:10px;">
                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSales as $s)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}</td>
                                    <td><strong>{{ $s->produk->nama_produk }}</strong></td>
                                    <td><span class="badge-pill badge-red">{{ number_format($s->jumlah) }} kg</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" style="text-align:center;color:#9ca3af;padding:18px;">
                                        Belum ada data penjualan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
