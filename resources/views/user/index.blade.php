@extends('layouts.master')

@section('title')
    Daftar Pengguna
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar Pengguna</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('user.store') }}')" class="btn btn-success btn-s btn-flat">
                    <i class="fa fa-plus-circle"></i> Tambah Pengguna
                </button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-bordered" id="users-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Hak Akses</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('user.form') {{-- pastikan form sudah bersih dari field toko --}}
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('#users-table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('user.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'name'},
                {data: 'email'},
                {data: 'level'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                .done((response) => {
                    $('#modal-form').modal('hide');
                    table.ajax.reload();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data pengguna berhasil disimpan.',
                    });
                })
                .fail((errors) => {
                    if (errors.status === 422) {
                        let response = errors.responseJSON.errors;
                        let errorMessage = response.name?.[0] || 
                                           response.email?.[0] || 
                                           response.password?.[0] || 
                                           'Data tidak valid.';

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: errorMessage,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Tidak dapat menyimpan data.',
                        });
                    }
                });
            }
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Tambah Pengguna');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=name]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Pengguna');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=name]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=name]').val(response.name);
                $('#modal-form [name=email]').val(response.email);
                $('#modal-form select[name=level]').val(response.level);
            });
    }

    function deleteData(url) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done(() => {
                    table.ajax.reload();
                    Swal.fire('Berhasil', 'Pengguna berhasil dihapus', 'success');
                })
                .fail(() => {
                    Swal.fire('Gagal', 'Tidak dapat menghapus data', 'error');
                });
            }
        });
    }
</script>
@endpush
