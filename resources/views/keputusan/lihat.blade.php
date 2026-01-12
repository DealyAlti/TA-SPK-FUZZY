@extends('layouts.master')

@section('title','Keputusan Produksi')

@section('breadcrumb')
@parent
<li class="active">Keputusan Produksi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">

        <div class="box">
            <div class="box-header with-border">
                <form method="GET" action="{{ route('keputusan.lihat') }}" class="form-inline">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                               value="{{ $tanggal }}">
                    </div>
                    <button class="btn btn-default btn-flat" type="submit">
                        <i class="fa fa-search"></i> Tampilkan
                    </button>
                </form>
            </div>

            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Produk</th>
                            <th width="18%">Hasil Saran</th>
                            <th width="18%">Keputusan</th>
                            <th width="20%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($data as $i => $row)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $row->produk->nama_produk ?? '-' }}</td>
                            <td>{{ number_format($row->jumlah_saran, 0, ',', '.') }}</td>
                            <td>
                                <span class="label label-danger" style="font-size:12px;">
                                    {{ number_format($row->jumlah_keputusan, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @if($row->pakai_saran)
                                    <span class="label label-success">Pakai saran</span>
                                @else
                                    <span class="label label-warning">Manual</span>
                                @endif
                                <span class="text-muted">
                                    • {{ $row->diputuskan_pada ? $row->diputuskan_pada->format('H:i') : '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                Belum ada keputusan produksi untuk tanggal {{ $tanggal }}.
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
