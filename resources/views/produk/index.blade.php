@extends('layouts.master')

@section('title')
    Daftar Produk
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Produk</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                @if(auth()->user()->level == 0)
                    <div class="btn-group">
                        <button onclick="addForm('{{ route('produk.store') }}')" class="btn btn-success btn-s btn-flat">
                            <i class="fa fa-plus-circle"></i> Tambah Produk
                        </button>
                    </div>
                @endif
            </div>
            <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered">
                        <thead>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
            </div>
        </div>
    </div>
</div>

@includeIf('produk.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('produk.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'nama_kategori'},
                {data: 'stok'}, 
                {data: 'satuan'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    })
                    .fail((xhr) => {
                        if (xhr.status === 422) {
                            let response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: response.message || 'Data tidak valid',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan pada server',
                            });
                        }
                    });

            }
        });

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Produk');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama_produk]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Produk');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_produk]').focus();
        $('#modal-form [name=satuan]').attr('readonly', true);

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=nama_produk]').val(response.nama_produk);
                    $('#modal-form [name=id_kategori]').val(response.id_kategori);
                    $('#modal-form [name=satuan]').val(response.satuan);
                })
            .fail((errors) => {
                alert('Tidak dapat menampilkan data');
                return;
            });
    }

    function deleteData(url) {
        // Show SweetAlert2 confirmation modal before deletion
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan bisa mengembalikan data yang dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            iconColor: '#28a745',  // Change the color of the icon
            customClass: {
                popup: 'swal-popup-large',
                title: 'swal-title-large',
                content: 'swal-content-large',
                confirmButton: 'swal-button-large',
                cancelButton: 'swal-button-large',
            },
            didOpen: () => {
                const swalTitle = document.querySelector('.swal-title');
                const swalContent = document.querySelector('.swal-content');
                swalTitle.style.fontSize = '60px';  // Larger title font size
                swalContent.style.fontSize = '25px';  // Larger content font size
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with deletion if confirmed
                    $.post(url, {
                        '_token': $('[name=csrf-token]').attr('content'),
                        '_method': 'delete'
                    })
                    .done((response) => {
                        table.ajax.reload();
                        // Show success message after deletion
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Produk berhasil dihapus',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4f9b8f',
                            showConfirmButton: true,
                        });
                    })
                    .fail((errors) => {
                        // Show error message if deletion fails
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Tidak dapat menghapus data',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33',
                        });
                    });
            }
        });
    }


    $(document).ready(function () {
        let baseProductName = '';

        // Saat user mengetik di kolom nama produk, simpan teks dasarnya
        $('#nama_produk').on('input', function () {
            let val = $(this).val();
            // Jika ada tanda kurung buka, ambil teks sebelum itu
            baseProductName = val.split(' (')[0];
        });

        // Saat user memilih kategori
        $('#id_kategori').change(function () {
        });
    });

</script>
@endpush
