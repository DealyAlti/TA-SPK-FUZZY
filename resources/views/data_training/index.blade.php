@extends('layouts.master')

@section('title')
    Data Training
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Data Training</li>
@endsection

@section('content')
<div class="row">

    {{-- BOX PILIH PRODUK (ATAS) --}}
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h4 class="box-title">Pilih Produk</h4>
            </div>
            <div class="box-body">
                <div class="row" style="padding: 10px 0;">
                    <div class="col-md-6">
                        <select id="produk" class="form-control dropdown-big" style="width: 100%;">
                            <option value="">-- pilih produk --</option>
                            @foreach($produk as $item)
                                <option value="{{ $item->id_produk }}">{{ $item->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <button class="btn btn-success btn-block" id="btn-add" disabled
                                style="height: 48px; font-size: 15px; border-radius: 10px;"
                                onclick="addForm('{{ route('training.store') }}')">
                            <i class="fa fa-plus-circle"></i> Tambah Data Training
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BOX DAFTAR DATA TRAINING (BAWAH) --}}
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h4 class="box-title">Daftar Data Training</h4>
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

@includeIf('data_training.form')
@endsection


@push('scripts')
<script>
    let table;
    let selectedProduct = null;

    $(function () {
        // Inisialisasi DataTables
        table = $('#table-training').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: '' // akan di-set setelah pilih produk
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'penjualan'},
                {data: 'hasil_produksi'},
                {data: 'stok_akhir', name: 'stok_barang_jadi'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        // Saat ganti produk
        $('#produk').change(function () {
            selectedProduct = $(this).val();
            $('#btn-add').prop('disabled', !selectedProduct);

            if (selectedProduct) {
                const url = '{{ route("training.data", ":id") }}'.replace(':id', selectedProduct);
                console.log('URL DataTables:', url);
                table.ajax.url(url).load();
            } else {
                table.clear().draw();
                table.ajax.url('');
            }
        });

        // Submit form modal via AJAX
        $('#modal-form form').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const url  = form.attr('action');

            $.post(url, form.serialize())
                .done(function (res) {
                    $('#modal-form').modal('hide');
                    table.ajax.reload();

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
    });

    // Buka modal tambah data
    function addForm(url) {
        if (!selectedProduct) return;

        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Data Training');
        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#id_produk').val(selectedProduct);
    }
    function editForm(url) {
    $('#modal-form').modal('show');
    $('#modal-form .modal-title').text('Edit Data Training');
    $('#modal-form form')[0].reset();
    $('#modal-form form').attr('action', url);
    $('#modal-form [name=_method]').val('put');

    $.get(url)
        .done(response => {
            $('#id_produk').val(response.id_produk);
            $('[name=tanggal]').val(response.tanggal);
            $('[name=penjualan]').val(response.penjualan);
            $('[name=hasil_produksi]').val(response.hasil_produksi);
            $('[name=stok_barang_jadi]').val(response.stok_barang_jadi);
        });
}

</script>

@push('css')
<style>
    .dropdown-big {
        height: 48px !important;
        font-size: 15px !important;
        padding: 8px 14px !important;
        border-radius: 10px !important;
    }
</style>
@endpush


@endpush
