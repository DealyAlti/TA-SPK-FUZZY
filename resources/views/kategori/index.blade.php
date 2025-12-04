@extends('layouts.master')

@section('title')
    Daftar Kategori
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Kategori</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('kategori.store') }}')" class="btn btn-success btn-s btn-flat"><i class="fa fa-plus-circle"></i> Tambah Kategori</button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kategori</th>
                        <th>Kapasitas (min–max)</th>
                        <th>Waktu (min–max)</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('kategori.form')


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
                url: '{{ route('kategori.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nama_kategori'},
                {data: 'kapasitas_range', searchable: false, sortable: false},
                {data: 'waktu_range', searchable: false, sortable: false},
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
                            const res = xhr.responseJSON;

                            if (res.errors && res.errors.nama_kategori) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menyimpan',
                                    text: 'Nama Kategori telah digunakan',
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menyimpan',
                                    text: 'Data tidak valid',

                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan pada server',
                                confirmButtonColor: '#d33',
                            });
                        }
                    });

            }
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Kategori');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama_kategori]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Kategori');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_kategori]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=nama_kategori]').val(response.nama_kategori);
                $('#modal-form [name=kapasitas_min]').val(response.kapasitas_min);
                $('#modal-form [name=kapasitas_max]').val(response.kapasitas_max);
                $('#modal-form [name=waktu_min]').val(response.waktu_min);
                $('#modal-form [name=waktu_max]').val(response.waktu_max);
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
                            text: 'Kategori berhasil dihapus',
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

    

</script>


@endpush
