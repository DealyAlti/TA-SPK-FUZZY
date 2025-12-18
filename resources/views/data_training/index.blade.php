@extends('layouts.master')

@section('title', 'Data Training')

@section('breadcrumb')
    @parent
    <li class="active">Data Training</li>
@endsection

@section('content')

{{-- =========================
   ✅ HEADER ACTION: GENERATE
========================= --}}
<div class="row">
    <div class="col-lg-12">
        <div class="box box-solid-red">
            <div class="box-header with-border header-solid-red">
                <h3 class="box-title">
                    <i class="fa fa-database"></i> Generate Data Training Harian
                </h3>
            </div>

            <div class="box-body">

                @if(session('info'))
                    <div class="alert alert-info modern-alert">
                        <i class="fa fa-info-circle"></i> {{ session('info') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success modern-alert">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('training.generateHarian') }}" class="form-inline form-generate">
                    @csrf

                    <div class="form-group">
                        <label class="label-inline">Tanggal</label>
                        <input type="date"
                               name="tanggal"
                               class="form-control input-solid"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <button class="btn btn-solid-red btn-lgx" style="margin-left:10px;">
                        <i class="fa fa-refresh"></i> Generate Data Training
                    </button>
                </form>

                <div class="hint-red">
                    <i class="fa fa-lightbulb-o"></i>
                    Data diambil otomatis dari penjualan, produksi aktual, dan stok harian.
                </div>

            </div>
        </div>
    </div>
</div>

{{-- =========================
   ✅ PILIH PRODUK + ACTION
========================= --}}
<div class="row">
    <div class="col-lg-12">
        <div class="box box-solid-red">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-cubes"></i> Pilih Produk
                </h3>
            </div>

            <div class="box-body">
                <div class="row row-actions">

                    {{-- dropdown --}}
                    <div class="col-md-4">
                        <label class="label-block">Produk</label>
                        <select id="produk" class="form-control select-solid">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produk as $p)
                                <option value="{{ $p->id_produk }}">{{ $p->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- tambah --}}
                    <div class="col-md-4">
                        <label class="label-block">&nbsp;</label>
                        <button id="btn-add"
                                class="btn btn-solid-red btn-block btn-lgx"
                                disabled
                                onclick="addForm('{{ route('training.store') }}')">
                            <i class="fa fa-plus-circle"></i> Tambah Data Training
                        </button>
                    </div>

                    {{-- panduan --}}
                    <div class="col-md-4">
                        <label class="label-block">&nbsp;</label>
                        <button id="btn-import-guide"
                                class="btn btn-outline-red btn-block btn-lgx"
                                disabled>
                            <i class="fa fa-info-circle"></i> Panduan Import & Upload
                        </button>
                    </div>

                </div>

                <div class="divider-soft"></div>

                <div class="mini-note">
                    <i class="fa fa-hand-pointer-o"></i>
                    Pilih produk terlebih dahulu untuk menampilkan data training & mengaktifkan tombol aksi.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================
   ✅ TABLE DATA TRAINING
========================= --}}
<div class="row">
    <div class="col-lg-12">
        <div class="box box-solid-red">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-list"></i> Daftar Data Training
                </h3>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-modern" id="table-training" width="100%">
                        <thead>
                            <tr>
                                <th style="width:70px;">No</th>
                                <th style="width:140px;">Tanggal</th>
                                <th>Penjualan</th>
                                <th>Hasil Produksi</th>
                                <th>Stok Akhir</th>
                                <th style="width:170px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="table-hint">
                    <i class="fa fa-shield"></i> Tips: gunakan import template agar format data konsisten.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL FORM TAMBAH/EDIT DATA TRAINING --}}
@include('data_training.form')

{{-- =========================
   ✅ MODAL IMPORT
========================= --}}
<div class="modal fade" id="modal-import">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-modern">

            <div class="modal-header modal-header-red">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity:1;color:#fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-upload"></i> Impor Data Training
                </h4>
            </div>

            <div class="modal-body" style="padding: 22px;">

                <div class="step-card">
                    <div class="step-badge">1</div>
                    <div class="step-content">
                        <h4>Download file template</h4>
                        <p class="muted">
                            Gunakan <b>template sesuai produk</b>. Jangan pakai template produk lain.
                        </p>

                        <button id="btn-download-template" class="btn btn-solid-red" disabled>
                            <i class="fa fa-download"></i> Download Template
                        </button>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-badge">2</div>
                    <div class="step-content">
                        <h4>Isi data di template</h4>
                        <p class="muted">Jangan menghapus/mengubah nama kolom.</p>
                        <ul class="rule-list">
                            <li><b>tanggal</b>: format tanggal bebas (Excel serial / DD-MM-YYYY / dst)</li>
                            <li><b>penjualan</b>: jumlah unit terjual per hari</li>
                            <li><b>stok_barang_jadi</b>: stok akhir di hari tersebut</li>
                            <li><b>hasil_produksi</b>: jumlah produksi aktual di hari tersebut</li>
                        </ul>
                    </div>
                </div>

                <div class="step-card">
                    <div class="step-badge">3</div>
                    <div class="step-content">
                        <h4>Upload file</h4>
                        <p class="muted">Pilih file yang sudah diisi, lalu upload ke sistem.</p>

                        <form id="form-import-template" action="{{ route('training.import') }}"
                              method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id_produk" id="import_produk_id">

                            <label class="btn btn-outline-red" style="margin-bottom:0;">
                                <i class="fa fa-file-excel-o"></i> Pilih File Excel
                                <input type="file"
                                       name="file"
                                       accept=".xlsx,.xls,.csv"
                                       style="display:none;"
                                       onchange="document.getElementById('form-import-template').submit()">
                            </label>
                        </form>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-default btn-lgx" data-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

@endsection

{{-- =========================
   ✅ CSS KHUSUS HALAMAN
========================= --}}
@push('css')
<style>
    /* ========= Theme Solid Red (page) ========= */
    :root{
        --r:#d90429;
        --r2:#b80322;
        --soft:#fff1f3;
        --bd:#e5e7eb;
        --tx:#1f2937;
        --mut:#6b7280;
    }

    .box-solid-red{ border-radius:14px !important; overflow:hidden; }
    .header-solid-red{
        background: var(--r) !important;
        color:#fff !important;
        border-bottom:none !important;
    }
    .header-solid-red .box-title{ color:#fff !important; font-weight:800 !important; }

    .modern-alert{ border-radius:12px !important; }

    .form-generate .label-inline{
        font-weight:800;
        margin-right:8px;
        color: var(--tx);
    }

    .input-solid{
        height:44px !important;
        border-radius:12px !important;
        border:1px solid var(--bd) !important;
    }

    .btn-lgx{ height:48px; border-radius:12px !important; font-weight:800 !important; }

    .btn-solid-red{
        background: var(--r) !important;
        border:1px solid var(--r) !important;
        color:#fff !important;
        box-shadow: 0 8px 18px rgba(217,4,41,.18);
    }
    .btn-solid-red:hover{
        background: var(--r2) !important;
        border-color: var(--r2) !important;
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(217,4,41,.25);
    }
    .btn-solid-red:disabled{
        opacity:.55 !important;
        cursor:not-allowed !important;
        transform:none !important;
        box-shadow:none !important;
    }

    .btn-outline-red{
        background:#fff !important;
        border:1px solid var(--r) !important;
        color: var(--r) !important;
        font-weight:800 !important;
        height:48px;
        border-radius:12px !important;
    }
    .btn-outline-red:hover{
        background: var(--soft) !important;
        color: var(--r2) !important;
        border-color: var(--r2) !important;
        transform: translateY(-2px);
    }
    .btn-outline-red:disabled{
        opacity:.55 !important;
        cursor:not-allowed !important;
        transform:none !important;
    }

    .hint-red{
        margin-top:12px;
        padding:12px 14px;
        border-radius:12px;
        background: var(--soft);
        border:1px solid rgba(217,4,41,.15);
        color: var(--tx);
        font-weight:600;
    }

    .row-actions{ display:flex; align-items:flex-end; gap:12px; }
    .row-actions > [class*="col-"]{ padding-left:0; padding-right:0; }

    .label-block{ font-weight:800; color:var(--tx); margin-bottom:8px; display:block; }

    .select-solid{
        height:48px !important;
        border-radius:12px !important;
        border:1px solid var(--bd) !important;
        font-weight:600;
    }

    .divider-soft{ height:1px; background: #f1f5f9; margin:18px 0; }

    .mini-note{
        padding:10px 12px;
        border-radius:12px;
        background:#fff;
        border:1px dashed var(--bd);
        color: var(--mut);
        font-weight:600;
    }

    /* table */
    .table-modern thead th{
        background: var(--r) !important;
        color:#fff !important;
        border:none !important;
        text-transform:uppercase;
        font-size:12px;
        letter-spacing:.6px;
    }
    .table-modern tbody tr:hover{
        background: var(--soft) !important;
    }
    .table-hint{
        margin-top:12px;
        color: var(--mut);
        font-weight:600;
        font-size:12.5px;
    }

    /* modal */
    .modal-modern{ border-radius:16px !important; overflow:hidden; }
    .modal-header-red{
        background: var(--r) !important;
        color:#fff !important;
        border-bottom:none !important;
    }
    .modal-header-red .modal-title{ color:#fff !important; font-weight:900; }

    .step-card{
        display:flex;
        gap:14px;
        border:1px solid var(--bd);
        border-radius:14px;
        padding:16px;
        background:#fff;
        margin-bottom:14px;
    }
    .step-badge{
        width:34px; height:34px;
        border-radius:10px;
        background: var(--r);
        color:#fff;
        font-weight:900;
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 34px;
        box-shadow: 0 10px 20px rgba(217,4,41,.18);
    }
    .step-content h4{ margin:0 0 6px; font-weight:900; color:var(--tx); }
    .muted{ color:var(--mut); margin:0 0 10px; font-weight:600; }
    .rule-list{ margin:0; padding-left:18px; color:var(--tx); font-weight:600; }
    .rule-list li{ margin-bottom:6px; }

    /* responsive */
    @media (max-width: 991px){
        .row-actions{ flex-direction:column; align-items:stretch; }
        .row-actions > [class*="col-"]{ width:100%; }
    }
</style>
@endpush

{{-- =========================
   ✅ SCRIPTS
========================= --}}
@push('scripts')
<script>
    let table = null;
    let selectedProduct = null;

    $(function () {

        // ====== PILIH PRODUK ======
        $('#produk').change(function () {
            selectedProduct = $(this).val();

            const enable = !!selectedProduct;
            $('#btn-add').prop('disabled', !enable);
            $('#btn-import-guide').prop('disabled', !enable);
            $('#btn-download-template').prop('disabled', !enable);
            $('#import_produk_id').val(selectedProduct || '');

            if (!selectedProduct) {
                if (table) table.clear().draw();
                return;
            }

            const url = '{{ route("training.data", ":id") }}'.replace(':id', selectedProduct);

            if (table) {
                table.ajax.url(url).load();
            } else {
                table = $('#table-training').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    pageLength: 10,
                    ajax: { url: url },
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, orderable: false},
                        {data: 'tanggal'},
                        {data: 'penjualan'},
                        {data: 'hasil_produksi'},
                        {data: 'stok_akhir'},
                        {data: 'aksi', searchable: false, orderable: false},
                    ],
                    language: {
                        processing: "Memuat...",
                        emptyTable: "Belum ada data training untuk produk ini",
                        paginate: { previous: "‹", next: "›" }
                    }
                });
            }
        });

        // ====== SUBMIT MODAL FORM (TAMBAH/EDIT) ======
        $('#modal-form form').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const url  = form.attr('action');

            $.post(url, form.serialize())
                .done(function (res) {
                    $('#modal-form').modal('hide');
                    if (table) table.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data training berhasil disimpan'
                    });
                })
                .fail(function (xhr) {
                    let msg = 'Terjadi kesalahan pada server';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: msg
                    });
                });
        });

        // ====== BUKA MODAL PANDUAN IMPORT ======
        $('#btn-import-guide').on('click', function () {
            if (!selectedProduct) {
                Swal.fire('Pilih produk terlebih dahulu', '', 'warning');
                return;
            }
            $('#modal-import').modal('show');
        });

        // ====== DOWNLOAD TEMPLATE ======
        $('#btn-download-template').on('click', function () {
            if (!selectedProduct) return;
            const url = '{{ route("training.template", ":id") }}'.replace(':id', selectedProduct);
            window.location = url;
        });
    });

    // ====== TAMBAH DATA ======
    function addForm(url) {
        if (!selectedProduct) return;

        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Data Training');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#id_produk').val(selectedProduct);
    }

    // ====== DELETE DATA (dipanggil dari kolom aksi) ======
    function deleteData(url) {
        Swal.fire({
            title: 'Hapus data?',
            text: 'Data yang sudah dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.post(url, {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'delete'
            })
            .done(() => {
                if (table) table.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data training berhasil dihapus' });
            })
            .fail(() => {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat menghapus data' });
            });
        });
    }
</script>
@endpush
