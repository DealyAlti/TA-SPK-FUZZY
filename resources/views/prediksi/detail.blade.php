@extends('layouts.master')

@section('title', 'Detail Perhitungan')

@section('breadcrumb')
    @parent
    <li class="active">Detail Perhitungan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        @php
            $from = request('from');                 // 'hasil' atau 'riwayat'
            $hasSession = session()->has('hasil_prediksi'); // kalau buka dari riwayat biasanya false
        @endphp

        @if($from === 'riwayat' || !$hasSession)
            <a href="{{ route('prediksi.riwayat') }}" class="btn btn-default" style="margin-bottom:10px;">
                &laquo; Kembali ke Riwayat Saran
            </a>
        @else
            <a href="{{ route('prediksi.hasil') }}" class="btn btn-default" style="margin-bottom:10px;">
                &laquo; Kembali ke Ringkasan
            </a>
        @endif

            {{-- ================== HASIL PERHITUNGAN ================== --}}
            @if ($hasil)
            <div class="col-lg-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Perhitungan Fuzzy Tsukamoto</h3>
                    </div>
                    <div class="box-body">

                        {{-- Ringkasan input & output --}}
                        <h4>Data Input & Hasil Saran</h4>
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
                                    <td><b>???</b></td>
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

                        {{-- ================== FUNGSI & DERAJAT KEANGGOTAAN ================== --}}
                            @php
                                $pmin = $hasil['minmax']['penjualan']['min'];
                                $pmax = $hasil['minmax']['penjualan']['max'];

                                $wmin = $hasil['minmax']['waktu']['min'];
                                $wmax = $hasil['minmax']['waktu']['max'];

                                $smin = $hasil['minmax']['stok']['min'];
                                $smax = $hasil['minmax']['stok']['max'];

                                $kmin = $hasil['minmax']['kapasitas']['min'];
                                $kmax = $hasil['minmax']['kapasitas']['max'];

                                $a   = $hasil['input']['penjualan'];
                                $b   = $hasil['input']['waktu_produksi'];
                                $c   = $hasil['input']['stok_barang_jadi'];
                                $d   = $hasil['input']['kapasitas_produksi'];
                            @endphp

                            {{-- ================== 1. PENJUALAN ================== --}}
                            <h4>Fungsi Keanggotaan Penjualan</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Himpunan</th>
                                        <th>Fungsi Keanggotaan</th>
                                        <th>Batas Interval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Penjualan Rendah --}}
                                    <tr>
                                        <td rowspan="3">Penjualan Rendah</td>
                                        <td>1</td>
                                        <td>a ≤ {{ $pmin }}</td>
                                    </tr>
                                    <tr>
                                        <td>({{ $pmax }} - a) / ({{ $pmax }} - {{ $pmin }})</td>
                                        <td>{{ $pmin }} &lt; a &lt; {{ $pmax }}</td>
                                    </tr>
                                    <tr>
                                        <td>0</td>
                                        <td>a ≥ {{ $pmax }}</td>
                                    </tr>

                                    {{-- Penjualan Tinggi --}}
                                    <tr>
                                        <td rowspan="3">Penjualan Tinggi</td>
                                        <td>0</td>
                                        <td>a ≤ {{ $pmin }}</td>
                                    </tr>
                                    <tr>
                                        <td>(a - {{ $pmin }}) / ({{ $pmax }} - {{ $pmin }})</td>
                                        <td>{{ $pmin }} &lt; a &lt; {{ $pmax }}</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>a ≥ {{ $pmax }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4>Derajat Keanggotaan Penjualan (a = {{ $a }} kg)</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Himpunan</th>
                                            <th>Penjelasan</th>
                                            <th>Perhitungan</th>
                                            <th>Nilai μ</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        {{-- μ Rendah --}}
                                        <tr>
                                            <td>μ Rendah(a)</td>
                                            @if ($a <= $pmin)
                                                <td>Karena a ≤ {{ $pmin }}, maka μ Rendah(a) = 1</td>
                                                <td>1</td>
                                                <td>{{ number_format($hasil['mu']['penjualan']['rendah'], 9) }}</td>
                                            @elseif ($a >= $pmax)
                                                <td>Karena a ≥ {{ $pmax }}, maka μ Rendah(a) = 0</td>
                                                <td>0</td>
                                                <td>{{ number_format($hasil['mu']['penjualan']['rendah'], 9) }}</td>
                                            @else
                                                <td>Karena {{ $pmin }} &lt; a &lt; {{ $pmax }}, digunakan rumus μ Rendah(a) = ({{ $pmax }} - a) / ({{ $pmax }} - {{ $pmin }})</td>
                                                <td>= ({{ $pmax - $a }}) / ({{ $pmax - $pmin }})</td>
                                                <td>{{ number_format($hasil['mu']['penjualan']['rendah'], 9) }}</td>
                                            @endif
                                        </tr>

                                        {{-- μ Tinggi --}}
                                        <tr>
                                            <td>μ Tinggi(a)</td>
                                            @if ($a <= $pmin)
                                                <td>Karena a ≤ {{ $pmin }}, maka μ Tinggi(a) = 0</td>
                                                <td>0</td>
                                                <td>{{ number_format($hasil['mu']['penjualan']['tinggi'], 9) }}</td>
                                            @elseif ($a >= $pmax)
                                                <td>Karena a ≥ {{ $pmax }}, maka μ Tinggi(a) = 1</td>
                                                <td>1</td>
                                                <td>{{ number_format($hasil['mu']['penjualan']['tinggi'], 9) }}</td>
                                            @else
                                                <td>Karena {{ $pmin }} &lt; a &lt; {{ $pmax }}, digunakan rumus μ Tinggi(a) = (a - {{ $pmin }}) / ({{ $pmax }} - {{ $pmin }})</td>
                                                <td>= ({{ $a - $pmin }}) / ({{ $pmax - $pmin }})</td>
                                                <td>{{ number_format($hasil['mu']['penjualan']['tinggi'], 9) }}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>


                            {{-- ================== 2. WAKTU PRODUKSI ================== --}}
                            <h4>Fungsi Keanggotaan Waktu Produksi</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Himpunan</th>
                                        <th>Fungsi Keanggotaan</th>
                                        <th>Batas Interval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Cepat --}}
                                    <tr>
                                        <td rowspan="3">Waktu Cepat</td>
                                        <td>1</td>
                                        <td>b ≤ {{ $wmin }}</td>
                                    </tr>
                                    <tr>
                                        <td>({{ $wmax }} - b) / ({{ $wmax }} - {{ $wmin }})</td>
                                        <td>{{ $wmin }} &lt; b &lt; {{ $wmax }}</td>
                                    </tr>
                                    <tr>
                                        <td>0</td>
                                        <td>b ≥ {{ $wmax }}</td>
                                    </tr>

                                    {{-- Lama --}}
                                    <tr>
                                        <td rowspan="3">Waktu Lama</td>
                                        <td>0</td>
                                        <td>b ≤ {{ $wmin }}</td>
                                    </tr>
                                    <tr>
                                        <td>(b - {{ $wmin }}) / ({{ $wmax }} - {{ $wmin }})</td>
                                        <td>{{ $wmin }} &lt; b &lt; {{ $wmax }}</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>b ≥ {{ $wmax }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <h4>Derajat Keanggotaan Waktu Produksi (b = {{ $b }} jam)</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Himpunan</th>
                                            <th>Penjelasan</th>
                                            <th>Perhitungan</th>
                                            <th>Nilai μ</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        {{-- μ Cepat --}}
                                        <tr>
                                            <td>μ Cepat(b)</td>
                                            @if ($b <= $wmin)
                                                <td>Karena b ≤ {{ $wmin }}, maka μ Cepat(b) = 1</td>
                                                <td>1</td>
                                                <td>{{ number_format($hasil['mu']['waktu']['cepat'], 9) }}</td>
                                            @elseif ($b >= $wmax)
                                                <td>Karena b ≥ {{ $wmax }}, maka μ Cepat(b) = 0</td>
                                                <td>0</td>
                                                <td>{{ number_format($hasil['mu']['waktu']['cepat'], 9) }}</td>
                                            @else
                                                <td>Karena {{ $wmin }} &lt; b &lt; {{ $wmax }}, digunakan rumus μ Cepat(b) = ({{ $wmax }} - b) / ({{ $wmax }} - {{ $wmin }})</td>
                                                <td>= {{ $wmax - $b }} / {{ $wmax - $wmin }}</td>
                                                <td>{{ number_format($hasil['mu']['waktu']['cepat'], 9) }}</td>
                                            @endif
                                        </tr>

                                        {{-- μ Lama --}}
                                        <tr>
                                            <td>μ Lama(b)</td>
                                            @if ($b <= $wmin)
                                                <td>Karena b ≤ {{ $wmin }}, maka μ Lama(b) = 0</td>
                                                <td>0</td>
                                                <td>{{ number_format($hasil['mu']['waktu']['lama'], 9) }}</td>
                                            @elseif ($b >= $wmax)
                                                <td>Karena b ≥ {{ $wmax }}, maka μ Lama(b) = 1</td>
                                                <td>1</td>
                                                <td>{{ number_format($hasil['mu']['waktu']['lama'], 9) }}</td>
                                            @else
                                                <td>Karena {{ $wmin }} &lt; b &lt; {{ $wmax }}, digunakan rumus μ Lama(b) = (b - {{ $wmin }}) / ({{ $wmax }} - {{ $wmin }})</td>
                                                <td>= {{ $b - $wmin }} / {{ $wmax - $wmin }}</td>
                                                <td>{{ number_format($hasil['mu']['waktu']['lama'], 9) }}</td>
                                            @endif
                                        </tr>

                                    </tbody>
                                </table>


                            {{-- ================== 3. STOK BARANG JADI ================== --}}
                            <h4>Fungsi Keanggotaan Stok Barang Jadi</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Himpunan</th>
                                        <th>Fungsi Keanggotaan</th>
                                        <th>Batas Interval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Sedikit --}}
                                    <tr>
                                        <td rowspan="3">Stok Sedikit</td>
                                        <td>1</td>
                                        <td>c ≤ {{ $smin }}</td>
                                    </tr>
                                    <tr>
                                        <td>({{ $smax }} - c) / ({{ $smax }} - {{ $smin }})</td>
                                        <td>{{ $smin }} &lt; c &lt; {{ $smax }}</td>
                                    </tr>
                                    <tr>
                                        <td>0</td>
                                        <td>c ≥ {{ $smax }}</td>
                                    </tr>

                                    {{-- Banyak --}}
                                    <tr>
                                        <td rowspan="3">Stok Banyak</td>
                                        <td>0</td>
                                        <td>c ≤ {{ $smin }}</td>
                                    </tr>
                                    <tr>
                                        <td>(c - {{ $smin }}) / ({{ $smax }} - {{ $smin }})</td>
                                        <td>{{ $smin }} &lt; c &lt; {{ $smax }}</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>c ≥ {{ $smax }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        <h4>Derajat Keanggotaan Stok Barang Jadi (c = {{ $c }} kg)</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Himpunan</th>
                                            <th>Penjelasan</th>
                                            <th>Perhitungan</th>
                                            <th>Nilai μ</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        {{-- μ Sedikit --}}
                                        <tr>
                                            <td>μ Sedikit(c)</td>
                                            @if ($c <= $smin)
                                                <td>Karena c ≤ {{ $smin }}, maka μ Sedikit(c) = 1</td>
                                                <td>1</td>
                                                <td>{{ number_format($hasil['mu']['stok']['sedikit'], 9) }}</td>
                                            @elseif ($c >= $smax)
                                                <td>Karena c ≥ {{ $smax }}, maka μ Sedikit(c) = 0</td>
                                                <td>0</td>
                                                <td>{{ number_format($hasil['mu']['stok']['sedikit'], 9) }}</td>
                                            @else
                                                <td>Karena {{ $smin }} &lt; c &lt; {{ $smax }}, digunakan rumus μ Sedikit(c) = ({{ $smax }} - c) / ({{ $smax }} - {{ $smin }})</td>
                                                <td>= {{ $smax - $c }} / {{ $smax - $smin }}</td>
                                                <td>{{ number_format($hasil['mu']['stok']['sedikit'], 9) }}</td>
                                            @endif
                                        </tr>

                                        {{-- μ Banyak --}}
                                        <tr>
                                            <td>μ Banyak(c)</td>
                                            @if ($c <= $smin)
                                                <td>Karena c ≤ {{ $smin }}, maka μ Banyak(c) = 0</td>
                                                <td>0</td>
                                                <td>{{ number_format($hasil['mu']['stok']['banyak'], 9) }}</td>
                                            @elseif ($c >= $smax)
                                                <td>Karena c ≥ {{ $smax }}, maka μ Banyak(c) = 1</td>
                                                <td>1</td>
                                                <td>{{ number_format($hasil['mu']['stok']['banyak'], 9) }}</td>
                                            @else
                                                <td>Karena {{ $smin }} &lt; c &lt; {{ $smax }}, digunakan rumus μ Banyak(c) = (c - {{ $smin }}) / ({{ $smax }} - {{ $smin }})</td>
                                                <td>= {{ $c - $smin }} / {{ $smax - $smin }}</td>
                                                <td>{{ number_format($hasil['mu']['stok']['banyak'], 9) }}</td>
                                            @endif
                                        </tr>

                                    </tbody>
                                </table>

                            {{-- ================== 4. KAPASITAS PRODUKSI ================== --}}
                            <h4>Fungsi Keanggotaan Kapasitas Produksi</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Himpunan</th>
                                        <th>Fungsi Keanggotaan</th>
                                        <th>Batas Interval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Rendah --}}
                                    <tr>
                                        <td rowspan="3">Kapasitas Rendah</td>
                                        <td>1</td>
                                        <td>d ≤ {{ $kmin }}</td>
                                    </tr>
                                    <tr>
                                        <td>({{ $kmax }} - d) / ({{ $kmax }} - {{ $kmin }})</td>
                                        <td>{{ $kmin }} &lt; d &lt; {{ $kmax }}</td>
                                    </tr>
                                    <tr>
                                        <td>0</td>
                                        <td>d ≥ {{ $kmax }}</td>
                                    </tr>

                                    {{-- Tinggi --}}
                                    <tr>
                                        <td rowspan="3">Kapasitas Tinggi</td>
                                        <td>0</td>
                                        <td>d ≤ {{ $kmin }}</td>
                                    </tr>
                                    <tr>
                                        <td>(d - {{ $kmin }}) / ({{ $kmax }} - {{ $kmin }})</td>
                                        <td>{{ $kmin }} &lt; d &lt; {{ $kmax }}</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>d ≥ {{ $kmax }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        <h4>Derajat Keanggotaan Kapasitas Produksi (d = {{ $d }} kg)</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Himpunan</th>
                                            <th>Penjelasan</th>
                                            <th>Perhitungan</th>
                                            <th>Nilai μ</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        {{-- μ Rendah --}}
                                        <tr>
                                            <td>μ Rendah(d)</td>
                                            @if ($d <= $kmin)
                                                <td>Karena d ≤ {{ $kmin }}, maka μ Rendah(d) = 1</td>
                                                <td>1</td>
                                                <td>{{ number_format($hasil['mu']['kapasitas']['rendah'], 9) }}</td>
                                            @elseif ($d >= $kmax)
                                                <td>Karena d ≥ {{ $kmax }}, maka μ Rendah(d) = 0</td>
                                                <td>0</td>
                                                <td>{{ number_format($hasil['mu']['kapasitas']['rendah'], 9) }}</td>
                                            @else
                                                <td>Karena {{ $kmin }} &lt; d &lt; {{ $kmax }}, digunakan rumus μ Rendah(d) = ({{ $kmax }} - d) / ({{ $kmax }} - {{ $kmin }})</td>
                                                <td>= {{ $kmax - $d }} / {{ $kmax - $kmin }}</td>
                                                <td>{{ number_format($hasil['mu']['kapasitas']['rendah'], 9) }}</td>
                                            @endif
                                        </tr>

                                        {{-- μ Tinggi --}}
                                        <tr>
                                            <td>μ Tinggi(d)</td>
                                            @if ($d <= $kmin)
                                                <td>Karena d ≤ {{ $kmin }}, maka μ Tinggi(d) = 0</td>
                                                <td>0</td>
                                                <td>{{ number_format($hasil['mu']['kapasitas']['tinggi'], 9) }}</td>
                                            @elseif ($d >= $kmax)
                                                <td>Karena d ≥ {{ $kmax }}, maka μ Tinggi(d) = 1</td>
                                                <td>1</td>
                                                <td>{{ number_format($hasil['mu']['kapasitas']['tinggi'], 9) }}</td>
                                            @else
                                                <td>Karena {{ $kmin }} &lt; d &lt; {{ $kmax }}, digunakan rumus μ Tinggi(d) = (d - {{ $kmin }}) / ({{ $kmax }} - {{ $kmin }})</td>
                                                <td>= {{ $d - $kmin }} / {{ $kmax - $kmin }}</td>
                                                <td>{{ number_format($hasil['mu']['kapasitas']['tinggi'], 9) }}</td>
                                            @endif
                                        </tr>

                                    </tbody>
                                </table>


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

                        @php
                            $zmin = $hasil['minmax']['produksi']['min'];
                            $zmax = $hasil['minmax']['produksi']['max'];
                            $deltaZ = $zmax - $zmin;
                        @endphp

                        <h4>Fungsi Keanggotaan Output Jumlah Produksi (Monoton)</h4>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Himpunan</th>
                                    <th>Fungsi Keanggotaan</th>
                                    <th>Batas Interval</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Sedikit --}}
                                <tr>
                                    <td rowspan="3">Jumlah Produksi Sedikit</td>
                                    <td>1</td>
                                    <td>z ≤ {{ $zmin }}</td>
                                </tr>
                                <tr>
                                    <td>({{ $zmax }} - z) / ({{ $zmax }} - {{ $zmin }})</td>
                                    <td>{{ $zmin }} &lt; z &lt; {{ $zmax }}</td>
                                </tr>
                                <tr>
                                    <td>0</td>
                                    <td>z ≥ {{ $zmax }}</td>
                                </tr>

                                {{-- Banyak --}}
                                <tr>
                                    <td rowspan="3">Jumlah Produksi Banyak</td>
                                    <td>0</td>
                                    <td>z ≤ {{ $zmin }}</td>
                                </tr>
                                <tr>
                                    <td>(z - {{ $zmin }}) / ({{ $zmax }} - {{ $zmin }})</td>
                                    <td>{{ $zmin }} &lt; z &lt; {{ $zmax }}</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>z ≥ {{ $zmax }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4>Penalaran Monoton pada Konsekuen (Invers Fungsi)</h4>
                        <ol>
                            <li>
                                Untuk konsekuen <b>Sedikit</b>:
                                <br>
                                μ<sub>Sedikit</sub>(z) = ({{ $zmax }} − z) / ({{ $zmax }} − {{ $zmin }})
                                <br>
                                Jika μ<sub>Sedikit</sub>(z) = α, maka
                                <br>
                                z = {{ $zmax }} − α ({{ $deltaZ }})
                            </li>
                            <li>
                                Untuk konsekuen <b>Banyak</b>:
                                <br>
                                μ<sub>Banyak</sub>(z) = (z − {{ $zmin }}) / ({{ $zmax }} − {{ $zmin }})
                                <br>
                                Jika μ<sub>Banyak</sub>(z) = α, maka
                                <br>
                                z = {{ $zmin }} + α ({{ $deltaZ }})
                            </li>
                        </ol>

                        <br>
                        {{-- ===================== RULE, NILAI α, z DAN α.z ===================== --}}
                        <h4>Rule, Nilai α dan z</h4>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Rule</th>
                                    <th>IF Penjualan</th>
                                    <th>AND Waktu Produksi</th>
                                    <th>AND Stok Barang Jadi</th>
                                    <th>AND Kapasitas Produksi</th>
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
                                        <td>{{ number_format($r['z'], 6) }}</td>
                                        <td>{{ number_format($r['alpha'] * $r['z'], 6) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <br>

                        {{-- Defuzzifikasi --}}
                        @php
                            // siapkan notasi simbolik dan numerik untuk tiap rule
                            $termsSymbolic = [];   // α1 × z1 + α2 × z2 + ...
                            $termsNumeric  = [];   // nilai α × nilai z
                            $alphaSymbols  = [];   // α1 + α2 + α3 + ...

                            foreach ($hasil['rules'] as $index => $r) {
                                $i = $index + 1;

                                $termsSymbolic[] = "α{$i} × z{$i}";
                                $termsNumeric[]  = number_format($r['alpha'], 9) . " × " . number_format($r['z'], 9);
                                $alphaSymbols[]  = "α{$i}";
                            }

                            $topSymbolic    = implode(' + ', $termsSymbolic);
                            $bottomSymbolic = implode(' + ', $alphaSymbols);
                            $topNumeric     = implode(' + ', $termsNumeric);

                            $sumAlphaZ = $hasil['sum_alpha_z'];
                            $sumAlpha  = $hasil['sum_alpha'];
                            $zAkhir    = $hasil['z_akhir'];
                            $zBulat    = round($zAkhir);
                        @endphp

                        <h4>Defuzzifikasi</h4>

                        <p>
                            Z = ({{ $topSymbolic }}) / ({{ $bottomSymbolic }})<br><br>
                            Z = ({{ $topNumeric }}) / {{ number_format($sumAlpha, 9) }}<br><br>
                            Z = {{ number_format($sumAlphaZ, 9) }} / {{ number_format($sumAlpha, 9) }}<br><br>
                            Z = {{ number_format($zAkhir, 9) }}<br><br>
                            Z dibulatkan menjadi <b>{{ $zBulat }}</b>
                        </p>

                    </div>
                </div>
            </div>
            @endif
    </div>
</div>
@endsection
