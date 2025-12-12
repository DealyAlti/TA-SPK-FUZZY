@extends('layouts.master')

@section('title', 'Data Training')

@section('breadcrumb')
    @parent
    <li class="active">Data Training</li>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h4>Generate Data Training Harian</h4>
            </div>
            <div class="box-body">
                @if(session('info'))
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('training.generateHarian') }}" class="form-inline">
                    @csrf

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date"
                               name="tanggal"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <button class="btn btn-danger" style="margin-left:10px;">
                        <i class="fa fa-refresh"></i> Generate Data Training
                    </button>
                </form>

                <small class="text-muted">
                    Data diambil otomatis dari penjualan, produksi aktual, dan stok harian.
                </small>

            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- ========== PILIH PRODUK ========== --}}
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h4>Pilih Produk</h4>
            </div>
            <div class="box-body">
                <div class="row" style="gap: 10px;">

                    {{-- DROPDOWN PRODUK --}}
                    <div class="col-md-4">
                        <div class="form-group" style="margin-bottom:0;">
                            <select id="produk" class="form-control select-produk">
                                <option value="">Pilih Produk</option>
                                @foreach ($produk as $p)
                                    <option value="{{ $p->id_produk }}">{{ $p->nama_produk }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    {{-- TOMBOL TAMBAH DATA TRAINING HARIAN --}}
                    <div class="col-md-4">
                        <button id="btn-add"
                                class="btn btn-danger btn-block btn-aksi"
                                disabled
                                onclick="addForm('{{ route('training.store') }}')">
                            <i class="fa fa-plus-circle"></i> Tambah Data Training (Harian)
                        </button>
                    </div>

                    <div class="col-md-4">
                        <button id="btn-import-guide"
                                class="btn btn-warning btn-block btn-aksi"
                                disabled>
                            <i class="fa fa-info-circle"></i> Panduan Import & Upload
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ========== DATA TRAINING ========== --}}
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h4>Daftar Data Training</h4>
            </div>
            <div class="box-body">
                <table class="table table-bordered" id="table-training">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Penjualan</th>
                            <th>Hasil Produksi</th>
                            <th>Stok Akhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL FORM TAMBAH/EDIT DATA TRAINING --}}
@include('data_training.form')

{{-- MODAL PANDUAN IMPORT + DOWNLOAD TEMPLATE + UPLOAD --}}
<div class="modal fade" id="modal-import">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Impor Data Training</h4>
            </div>

            <div class="modal-body" style="padding: 25px;">

                {{-- LANGKAH 1 --}}
                <div class="step" style="margin-bottom:25px;">
                    <h4>
                        <span class="badge bg-primary" style="background:#3c8dbc;">1</span>
                        Download file template
                    </h4>
                    <p>
                        Untuk mengimpor data dengan benar, gunakan <b>template yang sesuai dengan produk yang dipilih</b>.
                        Jangan gunakan template milik produk lain, karena sistem akan menolak file tersebut.
                    </p>

                    <button id="btn-download-template" class="btn btn-info" disabled>
                        <i class="fa fa-download"></i> Download File Template
                    </button>
                    <hr>
                </div>

                {{-- LANGKAH 2 --}}
                <div class="step" style="margin-bottom:25px;">
                    <h4>
                        <span class="badge bg-primary" style="background:#3c8dbc;">2</span>
                        Isi data di file template
                    </h4>
                    <p>
                        Pastikan data yang diisi sudah sesuai ketentuan. Jangan menghapus atau mengganti nama kolom.
                    </p>
                    <ul>
                        <li><b>tanggal</b> : boleh berupa tanggal Excel (serial) atau teks tanggal (DD/MM/YYYY, dsb)</li>
                        <li><b>penjualan</b> : jumlah unit terjual per hari</li>
                        <li><b>stok_barang_jadi</b> : stok akhir pada hari tersebut</li>
                        <li><b>hasil_produksi</b> : jumlah yang diproduksi pada hari tersebut</li>
                    </ul>
                    <hr>
                </div>

                {{-- LANGKAH 3 --}}
                <div class="step">
                    <h4>
                        <span class="badge bg-primary" style="background:#3c8dbc;">3</span>
                        Upload file
                    </h4>
                    <p>Pilih file template yang sudah diisi, kemudian upload ke sistem.</p>

                    <form id="form-import-template" action="{{ route('training.import') }}"
                          method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id_produk" id="import_produk_id">

                        <label class="btn btn-warning" style="margin-bottom:0;">
                            <i class="fa fa-upload"></i> Pilih File Excel
                            <input type="file" name="file" accept=".xlsx,.xls,.csv"
                                   style="display:none;"
                                   onchange="document.getElementById('form-import-template').submit()">
                        </label>
                    </form>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
@push('css')
<style>
    /* dropdown produk */
    .select-produk {
        height: 48px;
        font-size: 15px;
        padding: 8px 14px;
        border-radius: 10px;
    }

    /* tombol merah & oranye */
    .btn-aksi {
        height: 48px;
        font-size: 15px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    /* supaya row rata tengah (opsional) */
    @media (min-width: 768px) {
        .row.row-pilih-produk {
            display: flex;
            align-items: center;
        }
    }
</style>
@endpush


<script>
    let table = null;
    let selectedProduct = null;

    $(function () {

        // ========= PILIH PRODUK =========
        $('#produk').change(function () {
            selectedProduct = $(this).val();

            const enable = !!selectedProduct;
            $('#btn-add').prop('disabled', !enable);
            $('#btn-import-guide').prop('disabled', !enable);
            $('#btn-download-template').prop('disabled', !enable);
            $('#import_produk_id').val(selectedProduct || '');

            if (!selectedProduct) {
                if (table) {
                    table.clear().draw();
                }
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
                    ajax: { url: url },
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, sortable: false},
                        {data: 'tanggal'},
                        {data: 'penjualan'},
                        {data: 'hasil_produksi'},
                        {data: 'stok_akhir'},
                        {data: 'aksi', searchable: false, sortable: false},
                    ]
                });

            }
        });

        // ========= SUBMIT FORM MODAL (TAMBAH/EDIT) =========
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

        // ========= BUKA MODAL PANDUAN IMPORT =========
        $('#btn-import-guide').on('click', function () {
            if (!selectedProduct) {
                Swal.fire('Pilih produk terlebih dahulu', '', 'warning');
                return;
            }
            $('#modal-import').modal('show');
        });

        // ========= DOWNLOAD TEMPLATE =========
        $('#btn-download-template').on('click', function () {
            if (!selectedProduct) return;

            const url = '{{ route("training.template", ":id") }}'.replace(':id', selectedProduct);
            window.location = url;
        });
    });

    // ========= FUNGSI TAMBAH & EDIT =========
    function addForm(url) {
        if (!selectedProduct) return;

        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Data Training');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');  // selalu POST
        $('#id_produk').val(selectedProduct);
    }
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

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data training berhasil dihapus'
            });
        })
        .fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tidak dapat menghapus data'
            });
        });
    });
}

</script>
@endpush
