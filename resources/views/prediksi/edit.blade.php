@extends('layouts.master')

@section('title','Edit Riwayat Perhitungan')

@section('breadcrumb')
	@parent
	<li><a href="{{ route('prediksi.riwayat') }}">Riwayat Perhitungan</a></li>
	<li class="active">Edit</li>
@endsection

@push('css')
<style>
	.box-prediksi{
		border:none !important;
		border-radius:18px !important;
		overflow:hidden;
		box-shadow:0 12px 30px rgba(0,0,0,.08) !important;
	}
	.header-red{
		background:#b71c1c !important;
		color:#fff !important;
		padding:22px 24px !important;
	}
	.header-red .box-title{
		margin:0 !important;
		font-weight:900 !important;
		font-size:20px !important;
		display:flex;
		align-items:center;
		gap:12px;
		color:#fff !important;
	}
	.body-soft{ background:#fff; padding:24px !important; }

	.input-modern{
		height:48px !important;
		border-radius:12px !important;
		border:2px solid #e5e7eb !important;
		padding:10px 14px !important;
		font-size:14px !important;
		box-shadow:0 2px 8px rgba(0,0,0,.04);
	}
	.input-modern:focus{
		border-color:#b71c1c !important;
		box-shadow:0 0 0 4px rgba(183,28,28,.12) !important;
		outline:none !important;
	}
	.form-group label{
		font-weight:800 !important;
		color:#374151 !important;
		margin-bottom:8px !important;
	}
	.btn-save{
		height:48px !important;
		border-radius:999px !important;
		padding:10px 18px !important;
		font-weight:900 !important;
		border:none !important;
		background:#b71c1c !important;
		color:#fff !important;
		box-shadow:0 10px 22px rgba(183,28,28,.28) !important;
		display:inline-flex;
		align-items:center;
		gap:10px;
	}
	.btn-back{
		height:48px !important;
		border-radius:999px !important;
		padding:10px 18px !important;
		font-weight:900 !important;
		border:2px solid #b71c1c !important;
		background:#fff !important;
		color:#b71c1c !important;
		display:inline-flex;
		align-items:center;
		gap:10px;
	}
</style>
@endpush

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="box box-prediksi">
			<div class="box-header header-red">
				<h3 class="box-title">
					<i class="fa fa-edit"></i> Edit Riwayat Perhitungan
				</h3>
				<p style="margin:10px 0 0; opacity:.9; font-weight:600;">
					Produk: <b>{{ $prediksi->produk->nama_produk }}</b> • Tanggal: <b>{{ \Carbon\Carbon::parse($prediksi->tanggal)->format('d/m/Y') }}</b>
				</p>
			</div>

			<div class="box-body body-soft">
				@if ($errors->any())
					<div class="alert alert-danger">
						<ul style="margin-bottom:0;">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<form action="{{ route('prediksi.update', $prediksi->id_hasil_prediksi) }}" method="post">
					@csrf
					@method('PUT')

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Penjualan / Pesanan Masuk (kg)</label>
								<input type="number" name="penjualan" class="form-control input-modern"
									   value="{{ old('penjualan', $prediksi->penjualan) }}" min="0" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Waktu Produksi Tersedia (jam)</label>
								<input type="number" step="0.01" name="waktu_produksi" class="form-control input-modern"
									   value="{{ old('waktu_produksi', $prediksi->waktu_produksi) }}" min="0" required>
							</div>
						</div>

						<div class="col-md-6">
                            <div class="form-group">
                                <label>Stok Barang Jadi (snapshot)</label>
                                <input type="number" class="form-control input-modern"
                                    value="{{ $stokSnapshot }}" readonly>
                                <small class="text-muted">Stok ini mengikuti data saat riwayat dibuat (tidak ikut berubah).</small>
                            </div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Kapasitas Produksi (kg)</label>
								<input type="number" name="kapasitas_produksi" class="form-control input-modern"
									   value="{{ old('kapasitas_produksi', $prediksi->kapasitas_produksi) }}" min="0" required>
							</div>
						</div>
					</div>

					<div style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;">
						<a href="{{ route('prediksi.riwayat') }}" class="btn btn-back">
							<i class="fa fa-arrow-left"></i> Kembali
						</a>

						<button type="submit" class="btn btn-save"
								onclick="return confirm('Simpan perubahan dan hitung ulang saran produksi?');">
							<i class="fa fa-save"></i> Simpan Perubahan
						</button>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
@endsection
