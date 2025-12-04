@extends('layouts.master')

@section('title', 'Prediksi Jumlah Produksi')

@section('breadcrumb')
    @parent
    <li class="active">Prediksi</li>
@endsection

@section('content')

<div class="row">

    {{-- ================== FORM INPUT PREDIKSI ================== --}}
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Input Data Prediksi</h3>
            </div>
            <div class="box-body">

                <form class="form-horizontal" action="{{ route('prediksi.hitung') }}" method="post">
                    @csrf

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Produk</label>
                        <div class="col-sm-4">
                            <select name="id_produk" class="form-control" required>
                                <option value="">-- pilih produk --</option>
                                @foreach ($produk as $p)
                                    <option value="{{ $p->id_produk }}"
                                        {{ old('id_produk', optional($hasil['produk'] ?? null)->id_produk) == $p->id_produk ? 'selected' : '' }}>
                                        {{ $p->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tanggal Prediksi</label>
                        <div class="col-sm-4">
                            <input type="date" name="tanggal" class="form-control"
                                   value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Penjualan (kg)</label>
                        <div class="col-sm-4">
                            <input type="number" name="penjualan" class="form-control"
                                   value="{{ old('penjualan', $hasil['input']['penjualan'] ?? '') }}" required>
                        </div>

                        <label class="col-sm-2 control-label">Waktu Produksi (jam)</label>
                        <div class="col-sm-4">
                            <input type="number" step="0.01" name="waktu_produksi" class="form-control"
                                   value="{{ old('waktu_produksi', $hasil['input']['waktu_produksi'] ?? '') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Stok Barang Jadi (kg)</label>
                        <div class="col-sm-4">
                            <input type="number" name="stok_barang_jadi" class="form-control"
                                   value="{{ old('stok_barang_jadi', $hasil['input']['stok_barang_jadi'] ?? '') }}" required>
                        </div>

                        <label class="col-sm-2 control-label">Kapasitas Produksi (kg)</label>
                        <div class="col-sm-4">
                            <input type="number" name="kapasitas_produksi" class="form-control"
                                   value="{{ old('kapasitas_produksi', $hasil['input']['kapasitas_produksi'] ?? '') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-4">
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fa fa-calculator"></i> Hitung Prediksi
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ================== HASIL PERHITUNGAN ================== --}}
    @if ($hasil)
    <div class="col-lg-12">

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Perhitungan Fuzzy Tsukamoto</h3>
            </div>
            <div class="box-body">

                {{-- Ringkasan input & output --}}
                <h4>Data Input & Hasil Prediksi</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Penjualan (kg)</th>
                            <th>Waktu Produksi (jam)</th>
                            <th>Stok Barang Jadi (kg)</th>
                            <th>Kapasitas Produksi (kg)</th>
                            <th>Jumlah Produksi (hasil)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $hasil['produk']->nama_produk }}</td>
                            <td>{{ $hasil['input']['penjualan'] }}</td>
                            <td>{{ $hasil['input']['waktu_produksi'] }}</td>
                            <td>{{ $hasil['input']['stok_barang_jadi'] }}</td>
                            <td>{{ $hasil['input']['kapasitas_produksi'] }}</td>
                            <td><b>{{ number_format($hasil['z_akhir'], 2) }}</b></td>
                        </tr>
                    </tbody>
                </table>

                <br>

                {{-- RANGKA MIN–MAX --}}
                <h4>Rentang Nilai (Min–Max)</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Variabel</th>
                            <th>Min</th>
                            <th>Max</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Penjualan</td><td>{{ $hasil['minmax']['penjualan']['min'] }}</td><td>{{ $hasil['minmax']['penjualan']['max'] }}</td></tr>
                        <tr><td>Waktu Produksi</td><td>{{ $hasil['minmax']['waktu']['min'] }}</td><td>{{ $hasil['minmax']['waktu']['max'] }}</td></tr>
                        <tr><td>Stok Barang Jadi</td><td>{{ $hasil['minmax']['stok']['min'] }}</td><td>{{ $hasil['minmax']['stok']['max'] }}</td></tr>
                        <tr><td>Kapasitas Produksi</td><td>{{ $hasil['minmax']['kapasitas']['min'] }}</td><td>{{ $hasil['minmax']['kapasitas']['max'] }}</td></tr>
                        <tr><td>Jumlah Produksi</td><td>{{ $hasil['minmax']['produksi']['min'] }}</td><td>{{ $hasil['minmax']['produksi']['max'] }}</td></tr>
                    </tbody>
                </table>

                <br>

                {{-- FUNGSI KEANGGOTAAN – pakai min–max DINAMIS --}}
                <h4>Fungsi Keanggotaan</h4>

                @php
                    $pmin = $hasil['minmax']['penjualan']['min'];
                    $pmax = $hasil['minmax']['penjualan']['max'];
                    $wmin = $hasil['minmax']['waktu']['min'];
                    $wmax = $hasil['minmax']['waktu']['max'];
                    $smin = $hasil['minmax']['stok']['min'];
                    $smax = $hasil['minmax']['stok']['max'];
                    $kmin = $hasil['minmax']['kapasitas']['min'];
                    $kmax = $hasil['minmax']['kapasitas']['max'];
                    $zmin = $hasil['minmax']['produksi']['min'];
                    $zmax = $hasil['minmax']['produksi']['max'];
                @endphp

                <pre>
μ Rendah(a) Penjualan =
  ⎧ 1                             ; a ≤ {{ $pmin }}
  ⎨ ({{ $pmax }} - a) / ({{ $pmax }} - {{ $pmin }}) ; {{ $pmin }} < a < {{ $pmax }}
  ⎩ 0                             ; a ≥ {{ $pmax }}

μ Tinggi(a) Penjualan =
  ⎧ 0                             ; a ≤ {{ $pmin }}
  ⎨ (a - {{ $pmin }}) / ({{ $pmax }} - {{ $pmin }}) ; {{ $pmin }} < a < {{ $pmax }}
  ⎩ 1                             ; a ≥ {{ $pmax }}


μ Cepat(b) Waktu Produksi =
  ⎧ 1                             ; b ≤ {{ $wmin }}
  ⎨ ({{ $wmax }} - b) / ({{ $wmax }} - {{ $wmin }}) ; {{ $wmin }} < b < {{ $wmax }}
  ⎩ 0                             ; b ≥ {{ $wmax }}

μ Lama(b) Waktu Produksi =
  ⎧ 0                             ; b ≤ {{ $wmin }}
  ⎨ (b - {{ $wmin }}) / ({{ $wmax }} - {{ $wmin }}) ; {{ $wmin }} < b < {{ $wmax }}
  ⎩ 1                             ; b ≥ {{ $wmax }}


μ Sedikit(c) Stok Barang Jadi =
  ⎧ 1                             ; c ≤ {{ $smin }}
  ⎨ ({{ $smax }} - c) / ({{ $smax }} - {{ $smin }}) ; {{ $smin }} < c < {{ $smax }}
  ⎩ 0                             ; c ≥ {{ $smax }}

μ Banyak(c) Stok Barang Jadi =
  ⎧ 0                             ; c ≤ {{ $smin }}
  ⎨ (c - {{ $smin }}) / ({{ $smax }} - {{ $smin }}) ; {{ $smin }} < c < {{ $smax }}
  ⎩ 1                             ; c ≥ {{ $smax }}


μ Rendah(d) Kapasitas Produksi =
  ⎧ 1                             ; d ≤ {{ $kmin }}
  ⎨ ({{ $kmax }} - d) / ({{ $kmax }} - {{ $kmin }}) ; {{ $kmin }} < d < {{ $kmax }}
  ⎩ 0                             ; d ≥ {{ $kmax }}

μ Tinggi(d) Kapasitas Produksi =
  ⎧ 0                             ; d ≤ {{ $kmin }}
  ⎨ (d - {{ $kmin }}) / ({{ $kmax }} - {{ $kmin }}) ; {{ $kmin }} < d < {{ $kmax }}
  ⎩ 1                             ; d ≥ {{ $kmax }}


(opsional) Fungsi keanggotaan output Jumlah Produksi (Banyak/Sedikit) juga memakai {{ $zmin }} dan {{ $zmax }} dengan pola linear yang sama.
                </pre>

                <br>

                {{-- Fuzzyfikasi numeric --}}
                <h4>Nilai μ Untuk Input Saat Ini</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Variabel</th>
                            <th>Himpunan</th>
                            <th>μ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td rowspan="2">Penjualan</td><td>Rendah</td><td>{{ number_format($hasil['mu']['penjualan']['rendah'], 6) }}</td></tr>
                        <tr><td>Tinggi</td><td>{{ number_format($hasil['mu']['penjualan']['tinggi'], 6) }}</td></tr>

                        <tr><td rowspan="2">Waktu Produksi</td><td>Cepat</td><td>{{ number_format($hasil['mu']['waktu']['cepat'], 6) }}</td></tr>
                        <tr><td>Lama</td><td>{{ number_format($hasil['mu']['waktu']['lama'], 6) }}</td></tr>

                        <tr><td rowspan="2">Stok Barang Jadi</td><td>Sedikit</td><td>{{ number_format($hasil['mu']['stok']['sedikit'], 6) }}</td></tr>
                        <tr><td>Banyak</td><td>{{ number_format($hasil['mu']['stok']['banyak'], 6) }}</td></tr>

                        <tr><td rowspan="2">Kapasitas Produksi</td><td>Rendah</td><td>{{ number_format($hasil['mu']['kapasitas']['rendah'], 6) }}</td></tr>
                        <tr><td>Tinggi</td><td>{{ number_format($hasil['mu']['kapasitas']['tinggi'], 6) }}</td></tr>
                    </tbody>
                </table>

                <br>

                {{-- Rule & nilai αi, zi --}}
                <h4>Rule, Nilai α dan z</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Rule</th>
                            <th>IF Penjualan</th>
                            <th>AND Waktu</th>
                            <th>AND Stok</th>
                            <th>AND Kapasitas</th>
                            <th>THEN Jumlah Produksi</th>
                            <th>α</th>
                            <th>z</th>
                            <th>α . z</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hasil['rules'] as $r)
                            <tr>
                                <td>{{ $r['kode'] }}</td>
                                <td>{{ ucfirst($r['P']) }}</td>
                                <td>{{ ucfirst($r['W']) }}</td>
                                <td>{{ ucfirst($r['S']) }}</td>
                                <td>{{ ucfirst($r['K']) }}</td>
                                <td>{{ ucfirst($r['out']) }}</td>
                                <td>{{ number_format($r['alpha'], 6) }}</td>
                                <td>{{ number_format($r['z'], 4) }}</td>
                                <td>{{ number_format($r['alpha'] * $r['z'], 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <br>

                {{-- Defuzzifikasi --}}
                <h4>Defuzzifikasi (Rata-rata Terbobot)</h4>
                <p>
                    \( Z = \dfrac{\sum (\alpha_i \cdot z_i)}{\sum \alpha_i} \)
                </p>
                <p>
                    \(\sum (\alpha_i \cdot z_i) = {{ number_format($hasil['sum_alpha_z'], 4) }}\),
                    \(\sum \alpha_i = {{ number_format($hasil['sum_alpha'], 6) }}\)
                </p>
                <p>
                    <b>Hasil:</b> \( Z = {{ number_format($hasil['z_akhir'], 2) }} \)
                </p>

            </div>
        </div>
    </div>
    @endif

</div>

@endsection
