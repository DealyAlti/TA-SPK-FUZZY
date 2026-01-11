@extends('layouts.master')

@section('title','Training Harian')

@section('breadcrumb')
@parent
<li class="active">Training Harian</li>
@endsection

@push('css')
<style>
:root{
    --red:#b91c1c; --red2:#991b1b; --soft:#fee2e2;
    --text:#111827; --muted:#6b7280; --border:#e5e7eb;
    --radius:16px; --shadow:0 10px 25px rgba(17,24,39,.06);
}
.box-soft{
    border:1px solid var(--border);
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    overflow:hidden;
}
.box-head{
    background:linear-gradient(135deg, var(--red) 0%, var(--red2) 100%);
    color:#fff;
    padding:16px 18px;
    display:flex;align-items:center;justify-content:space-between;gap:12px;
}
.box-head h3{margin:0;font-weight:900;font-size:16px;}
.box-body{padding:16px 18px;background:#fff;}
.btn-red{
    background:var(--red)!important;color:#fff!important;
    border:none!important;border-radius:12px!important;
    font-weight:900!important;padding:10px 14px!important;
    box-shadow:0 10px 18px rgba(185,28,28,.18);
}
.btn-red:hover{background:var(--red2)!important;}
.btn-outline-red{
    background:#fff!important;color:var(--red)!important;
    border:2px solid var(--red)!important;border-radius:12px!important;
    font-weight:900!important;padding:10px 14px!important;
}
.btn-outline-red:hover{background:var(--soft)!important;}
.filter-row{display:flex;gap:10px;flex-wrap:wrap;align-items:end;margin-bottom:14px;}
.table-modern thead{background:#fff5f5;color:#7f1d1d;}
.table-modern thead th{font-weight:900;text-transform:uppercase;font-size:12px;letter-spacing:.6px;border-bottom:1px solid var(--border);}
.badge-pill{
  display:inline-flex;align-items:center;justify-content:center;
  padding:6px 12px;border-radius:999px;font-weight:900;font-size:12px;
  min-width:90px;
}
.badge-red{background:var(--red);color:#fff;}
.badge-gray{background:#e5e7eb;color:#4b5563;}
/* ===== Samain tinggi input & button ===== */
.input-h48{
    height:48px !important;
    border-radius:12px !important;
}

/* Biar tombol juga fix tinggi 48 */
.btn-h48{
    height:48px !important;
    border-radius:12px !important;
    display:inline-flex !important;
    align-items:center !important;
    justify-content:center !important;
}

/* ===== Layout import: sejajar file + button ===== */
.import-row{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
    align-items:center;
    margin-bottom:14px;
}

/* Kolom file: lebih lebar */
.import-col-file{
    min-width:380px;
    flex:1 1 520px;
}

/* Kolom tombol import */
.import-col-btn{
    flex:0 0 auto;
}

/* Mobile: biar enak */
@media (max-width: 767px){
    .import-col-file{min-width:100%; flex:1 1 100%;}
    .import-col-btn{width:100%;}
    .import-col-btn .btn{width:100%;}
}
</style>
@endpush

@section('content')
<div class="row">
<div class="col-lg-12">

    <div class="box-soft">
        <div class="box-head">
            <h3><i class="fa fa-calendar"></i> Training Harian (Import Template → Draft → Generate)</h3>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a class="btn btn-outline-red" href="{{ route('training.harian.template', ['tanggal'=>$tanggal]) }}">
                    <i class="fa fa-download"></i> Download Template
                </a>
            </div>
        </div>

        <div class="box-body">

            {{-- FILTER TANGGAL --}}
            <form method="GET" class="filter-row">
                <div style="min-width:240px;">
                    <label style="font-weight:900;color:#111827;">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control input-h48" value="{{ $tanggal }}">
                </div>
                <div>
                  <button class="btn btn-outline-red btn-h48" type="submit">
                      <i class="fa fa-search"></i> Tampilkan
                  </button>
                </div>
            </form>

            {{-- IMPORT --}}
            <form method="POST"
                  action="{{ route('training.harian.import') }}"
                  enctype="multipart/form-data"
                  class="import-row">
              @csrf

              <div class="import-col-file">
                  <label style="font-weight:900;color:#111827;">Import Template (xlsx)</label>
                  <input type="file" name="file" class="form-control input-h48" required>
                  <small class="text-muted">
                      Isi tanggal di <b>B1</b>, lalu isi Penjualan &amp; Hasil Produksi. Import ulang akan <b>UPDATE</b> draft.
                  </small>
              </div>

              <div class="import-col-btn">
                  <button class="btn btn-red btn-h48" type="submit" {{ $sudahGenerate ? 'disabled' : '' }}>
                      <i class="fa fa-upload"></i> Import Draft
                  </button>
              </div>

              @if($sudahGenerate)
                  <div class="text-muted" style="padding-bottom:10px;">
                      <i class="fa fa-lock"></i> Tanggal ini sudah digenerate → draft terkunci.
                  </div>
              @endif

            </form>

            {{-- TABLE DRAFT --}}
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th style="width:60px;">No</th>
                            <th>Produk</th>
                            <th style="width:160px;text-align:center;">Stok</th>
                            <th style="width:160px;text-align:center;">Penjualan</th>
                            <th style="width:180px;text-align:center;">Hasil Produksi</th>
                            <th style="width:160px;text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produk as $i => $p)
                            @php
                                $d = $draftMap[$p->id_produk] ?? null;
                            @endphp
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td><b>{{ $p->nama_produk }}</b></td>
                                <td style="text-align:center;">
                                    {{ number_format($stokAwalMap[$p->id_produk] ?? 0) }} kg
                                </td>
                                <td style="text-align:center;">
                                    {{ $d ? number_format($d->penjualan) : 0 }} kg
                                </td>
                                <td style="text-align:center;">
                                    {{ $d ? number_format($d->hasil_produksi) : 0 }} kg
                                </td>
                                <td style="text-align:center;">
                                    @if($d)
                                        <span class="badge-pill badge-red">ADA DRAFT</span>
                                    @else
                                        <span class="badge-pill badge-gray">KOSONG</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Belum ada produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- GENERATE --}}
            <form id="form-generate" method="POST" action="{{ route('training.harian.generate') }}" style="margin-top:14px;">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <button id="btn-generate" type="button" class="btn btn-red" {{ $sudahGenerate ? 'disabled' : '' }}>
                    <i class="fa fa-cogs"></i> Generate ke Data Training &amp; Update Stok
                </button>
            </form>

        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {

        // ===== NOTIF VALIDASI LARAVEL =====
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        @endif

        // ===== FLASH MESSAGE: ERROR =====
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: `{!! nl2br(e(session('error'))) !!}`
            });
        @endif

        // ===== FLASH MESSAGE: INFO =====
        @if (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                html: `{!! nl2br(e(session('info'))) !!}`
            });
        @endif

        // ===== FLASH MESSAGE: SUCCESS =====
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                html: `{!! nl2br(e(session('success'))) !!}`
            });
        @endif
    });
</script>
@endpush

@push('scripts')
<script>
    $(function () {

        // ===== NOTIF VALIDASI LARAVEL =====
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        @endif

        // ===== FLASH MESSAGE: ERROR =====
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: `{!! nl2br(e(session('error'))) !!}`
            });
        @endif

        // ===== FLASH MESSAGE: INFO =====
        @if (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                html: `{!! nl2br(e(session('info'))) !!}`
            });
        @endif

        // ===== FLASH MESSAGE: SUCCESS =====
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                html: `{!! nl2br(e(session('success'))) !!}`
            });
        @endif

        // ==========================
        // ✅ SWEETALERT CONFIRM GENERATE
        // ==========================
        $('#btn-generate').on('click', function () {
            Swal.fire({
                title: 'Generate Data Training?',
                html: `
                    <div style="text-align:left">
                        <b>Tanggal:</b> {{ $tanggal }}<br>
                        Setelah generate:<br>
                        • Draft akan <b>terkunci</b><br>
                        • Data masuk ke <b>Data Training</b><br>
                        • Stok akan <b>diupdate</b>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Generate',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#b91c1c',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-generate').submit();
                }
            });
        });

    });
</script>
@endpush
