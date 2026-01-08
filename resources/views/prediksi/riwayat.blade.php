@extends('layouts.master')

@section('title','Riwayat Perhitungan')

@section('breadcrumb')
	@parent
	<li class="active">Riwayat Perhitungan</li>
@endsection

@push('css')
<style>
	/* ===============================
	   BOX
	=============================== */
	.modern-box{
		border-radius:12px;
		box-shadow:0 2px 12px rgba(0,0,0,.08);
		border:none;
		overflow:hidden;
	}

	/* ===============================
	   FILTER (FIX DROPDOWN)
	=============================== */
	.modern-filter-form{
		display:flex;
		align-items:end;
		gap:12px;
		margin-bottom:16px;
		flex-wrap:wrap;
	}

	.modern-filter-form .filter-left{
		flex:0 0 40%;
		max-width:40%;
	}

	.modern-filter-form .filter-date{
		flex:0 0 22%;
		max-width:22%;
		min-width:170px;
	}

	.modern-filter-form label{
		font-weight:800;
		color:#374151;
		margin-bottom:6px;
		font-size:13px;
	}

	/* FIX dropdown AdminLTE */
	.modern-filter-form select.form-control{
		height:42px !important;
		padding:6px 14px !important;
		font-size:14px !important;
		line-height:1.5 !important;
		border-radius:10px !important;
		border:2px solid #e5e7eb !important;
		background:#fff !important;
		box-shadow:none !important;
		appearance:auto;
		-webkit-appearance:menulist;
		-moz-appearance:menulist;
	}

	.modern-filter-form select.form-control:focus{
		border-color:#b91c1c !important;
		box-shadow:0 0 0 3px rgba(185,28,28,.15) !important;
	}

	/* Date input style */
	.modern-filter-form input[type="date"].form-control{
		height:42px !important;
		padding:6px 14px !important;
		font-size:14px !important;
		line-height:1.5 !important;
		border-radius:10px !important;
		border:2px solid #e5e7eb !important;
		background:#fff !important;
		box-shadow:none !important;
	}

	.modern-filter-form input[type="date"].form-control:focus{
		border-color:#b91c1c !important;
		box-shadow:0 0 0 3px rgba(185,28,28,.15) !important;
	}

	/* Tombol filter merah solid */
	.btn-modern-filter{
		height:42px;
		background:#b91c1c !important;
		color:#fff !important;
		border:none;
		border-radius:10px;
		padding:0 22px;
		font-weight:700;
		box-shadow:0 3px 10px rgba(185,28,28,.35);
	}

	.btn-modern-filter:hover{
		background:#991b1b !important;
	}

	/* ===============================
	   TABLE
	=============================== */
	.modern-table thead{
		background:#b91c1c;
		color:#fff;
	}

	.modern-table thead th{
		padding:15px;
		font-size:12px;
		font-weight:700;
		text-transform:uppercase;
		border:none;
	}

	.modern-table tbody td{
		padding:15px;
		vertical-align:middle;
		border:none;
	}

	.modern-table tbody tr{
		border-bottom:1px solid #f0f0f0;
	}

	.modern-table tbody tr:hover{
		background:#f9fafb;
	}

	/* ===============================
	   BADGE AKTUAL (SERAGAM)
	=============================== */
	.modern-label{
		display:inline-flex;
		align-items:center;
		justify-content:center;
		min-width:90px;
		height:38px;
		padding:0 16px;
		border-radius:999px;
		font-size:14px;
		font-weight:700;
	}

	.modern-label-default{
		background:#e5e7eb;
		color:#4b5563;
	}

	.modern-label-success{
		background:#dc2626;
		color:#fff;
	}

	/* ===============================
	   AKSI (ATAS–BAWAH)
	=============================== */
	td.aksi-links{
		white-space:normal;
	}

	.aksi-link{
		display:flex;
		align-items:center;
		gap:6px;
		margin-bottom:6px;
		font-size:14px;
		font-weight:600;
		text-decoration:none;
		color:#2563eb;
	}

	.aksi-link:last-child{
		margin-bottom:0;
	}

	.aksi-link:hover{
		text-decoration:underline;
	}

	.aksi-link-success{
		color:#6b7280;
		cursor:default;
	}

	/* ===============================
	   RESPONSIVE
	=============================== */
	@media (max-width:768px){
		.modern-filter-form .filter-left,
		.modern-filter-form .filter-date{
			flex:0 0 100%;
			max-width:100%;
		}
		.btn-modern-filter{
			width:100%;
		}
	}
</style>
@endpush

@section('content')
<div class="row">
	<div class="col-lg-12">

		@if(session('success'))
			<div class="alert alert-success">
				<i class="fa fa-check-circle"></i> {{ session('success') }}
			</div>
		@endif

		<div class="box modern-box">
			<div class="box-body">

				{{-- FILTER --}}
				<form method="GET" class="modern-filter-form">
					<div class="filter-left">
						<label>Produk</label>
						<select name="id_produk" class="form-control">
							<option value="">Semua Produk</option>
							@foreach($produk as $p)
								<option value="{{ $p->id_produk }}" {{ request('id_produk') == $p->id_produk ? 'selected' : '' }}>
									{{ $p->nama_produk }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="filter-date">
						<label>Dari Tanggal</label>
						<input type="date" name="from" class="form-control" value="{{ request('from') }}">
					</div>

					<div class="filter-date">
						<label>Sampai Tanggal</label>
						<input type="date" name="to" class="form-control" value="{{ request('to') }}">
					</div>

					<button class="btn btn-modern-filter" type="submit">
						<i class="fa fa-filter"></i> Filter Data
					</button>
				</form>

				{{-- TABLE --}}
				<div class="table-responsive">
					<table class="table modern-table">
						<thead>
							<tr>
								<th>Tanggal</th>
								<th>Produk</th>
								<th>Penjualan</th>
								<th>Waktu</th>
								<th>Stok</th>
								<th>Kapasitas</th>
								<th>Saran Produksi</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							@forelse($riwayat as $r)
								<tr>
									<td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
									<td><strong>{{ $r->produk->nama_produk }}</strong></td>
									<td>{{ number_format($r->penjualan) }} kg</td>
									<td>
										{{ fmod((float)$r->waktu_produksi, 1) == 0
											? (int)$r->waktu_produksi
											: rtrim(rtrim(number_format($r->waktu_produksi, 2, '.', ''), '0'), '.') }}
										jam
									</td>
									<td>{{ number_format($r->stok_barang_jadi) }} kg</td>
									<td>{{ number_format($r->kapasitas_produksi) }} kg</td>
									<td><strong style="color:#2563eb">{{ number_format($r->jumlah_produksi) }} kg</strong></td>

									<td class="aksi-links">
                                        @if(auth()->user()->level == 0)
                                            <a class="aksi-link" href="{{ route('prediksi.detailById',['id'=>$r->id_hasil_prediksi,'from'=>'riwayat']) }}">
                                                <i class="fa fa-calculator"></i> Perhitungan
                                            </a>

                                            <a class="aksi-link" href="{{ route('prediksi.edit', $r->id_hasil_prediksi) }}">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        @endif

                                        @if(auth()->user()->level == 2)
                                            <span style="color:#9ca3af;font-weight:600;">-</span>
                                        @endif
                                    </td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center">Belum ada data</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				{{-- pagination + bawa query filter --}}
				{{ $riwayat->appends(request()->query())->links() }}

			</div>
		</div>

	</div>
</div>
@endsection
