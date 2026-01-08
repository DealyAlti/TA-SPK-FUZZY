@extends('layouts.master')

@section('title', 'Edit Saran Produksi')

@section('breadcrumb')
	@parent
	<li><a href="{{ route('prediksi.riwayat') }}">Riwayat Perhitungan</a></li>
	<li class="active">Edit Saran Produksi</li>
@endsection

@push('css')
<style>
	/* ====== CARD / BOX ====== */
	.box-prediksi{
		border:none !important;
		border-radius:18px !important;
		overflow:hidden;
		box-shadow:0 12px 30px rgba(0,0,0,.08) !important;
	}

	.header-red{
		background:#b71c1c !important;
		color:#fff !important;
		border-bottom:none !important;
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

	.title-icon{
		width:40px;
		height:40px;
		border-radius:999px;
		background:rgba(255,255,255,.16);
		display:flex;
		align-items:center;
		justify-content:center;
	}

	.header-sub{
		margin:10px 0 0;
		opacity:.9;
		font-weight:600;
		font-size:13px;
	}

	.body-soft{
		background:#fff;
		padding:24px !important;
	}

	/* ====== INPUT ====== */
	.input-modern{
		height:48px !important;
		border-radius:12px !important;
		border:2px solid #e5e7eb !important;
		padding:10px 14px !important;
		font-size:14px !important;
		box-shadow:0 2px 8px rgba(0,0,0,.04);
		transition:all .2s ease;
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

	.help-muted{
		color:#6b7280;
		font-size:12px;
		margin-top:6px;
	}

	/* ====== BUTTON ====== */
	.form-actions{
		margin-top:14px;
		display:flex;
		gap:10px;
		flex-wrap:wrap;
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
		transition:all .2s ease;
	}

	.btn-save:hover{
		transform:translateY(-2px);
		box-shadow:0 14px 30px rgba(183,28,28,.35) !important;
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

	.btn-back:hover{
		background:#fee2e2 !important;
	}

	/* ====== SWEETALERT STYLE (kek deleteData) ====== */
	.swal-popup-large{ border-radius:16px; }
	.swal-title-large{ font-weight:900; }
	.swal-content-large{ color:#374151; }
	.swal-button-large{
		border-radius:10px !important;
		padding:10px 18px !important;
		font-weight:900 !important;
	}

	@media (max-width:768px){
		.header-red{ padding:18px 16px !important; }
		.body-soft{ padding:16px !important; }
		.btn-save, .btn-back{ width:100%; justify-content:center; }
	}
</style>
@endpush

@section('content')
<div class="row">
	<div class="col-lg-12">

		<div class="box box-prediksi">
			<div class="box-header header-red">
				<h3 class="box-title">
					<span class="title-icon"><i class="fa fa-edit"></i></span>
					Edit Saran Produksi
				</h3>
				<p class="header-sub">
					Ubah input perhitungan lalu simpan untuk menghitung ulang saran produksi.
				</p>
			</div>

			<div class="box-body body-soft">

				{{-- ERROR VALIDASI --}}
				@if ($errors->any())
					<div class="alert alert-danger" style="border-radius:14px;">
						<b><i class="fa fa-exclamation-triangle"></i> Periksa kembali input:</b>
						<ul style="margin:8px 0 0;">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<form id="form-edit-prediksi"
					  method="POST"
					  action="{{ route('prediksi.update', $prediksi->id_hasil_prediksi) }}">
					@csrf
					@method('PUT')

					<div class="row">

						{{-- PRODUK (READONLY) --}}
						<div class="col-md-6">
							<div class="form-group">
								<label>Produk</label>
								<input type="text"
									   class="form-control input-modern"
									   value="{{ $prediksi->produk->nama_produk }}"
									   readonly>
							</div>
						</div>

						{{-- TANGGAL (READONLY) --}}
						<div class="col-md-6">
							<div class="form-group">
								<label>Tanggal</label>
								<input type="date"
									   class="form-control input-modern"
									   value="{{ \Carbon\Carbon::parse($prediksi->tanggal)->format('Y-m-d') }}"
									   readonly>
							</div>
						</div>

						{{-- PENJUALAN (EDITABLE) --}}
						<div class="col-md-6">
							<div class="form-group">
								<label>Penjualan / Pemesanan (kg)</label>
								<input type="number"
									   name="penjualan"
									   class="form-control input-modern"
									   value="{{ old('penjualan', $prediksi->penjualan) }}"
									   min="0"
									   required>
							</div>
						</div>

						{{-- WAKTU (EDITABLE) --}}
						<div class="col-md-6">
							<div class="form-group">
								<label>Waktu Produksi (jam)</label>
								<input type="number"
									   step="0.01"
									   name="waktu_produksi"
									   class="form-control input-modern"
									   value="{{ old('waktu_produksi', $prediksi->waktu_produksi) }}"
									   min="0"
									   required>
							</div>
						</div>

						{{-- STOK (READONLY SNAPSHOT) --}}
						<div class="col-md-6">
							<div class="form-group">
								<label>Stok Barang Jadi (snapshot) (kg)</label>
								<input type="number"
									   class="form-control input-modern"
									   value="{{ old('stok_barang_jadi', $prediksi->stok_barang_jadi) }}"
									   readonly>
							</div>
						</div>

						{{-- KAPASITAS (EDITABLE) --}}
						<div class="col-md-6">
							<div class="form-group">
								<label>Kapasitas Produksi (kg)</label>
								<input type="number"
									   name="kapasitas_produksi"
									   class="form-control input-modern"
									   value="{{ old('kapasitas_produksi', $prediksi->kapasitas_produksi) }}"
									   min="0"
									   required>
							</div>
						</div>

					</div>

					<div class="form-actions">
						<a href="{{ route('prediksi.riwayat') }}" class="btn btn-back">
							<i class="fa fa-arrow-left"></i> Kembali
						</a>

						{{-- BUTTON BUKAN SUBMIT (biar lewat SweetAlert) --}}
						<button type="button" onclick="confirmUpdate()" class="btn btn-save">
							<i class="fa fa-save"></i> Simpan Perubahan
						</button>
					</div>

				</form>

			</div>
		</div>

	</div>
</div>
@endsection

@push('scripts')
<script>
function confirmUpdate() {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Saran produksi akan dihitung ulang berdasarkan data terbaru.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-edit-prediksi').submit();
        }
    });
}
</script>
@endpush
