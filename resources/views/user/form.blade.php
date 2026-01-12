<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal" data-toggle="validator" autocomplete="off">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"></h4>
                </div>

                <div class="modal-body">
                    {{-- Nama --}}
                    <div class="form-group row">
                        <label for="name" class="col-lg-2 col-lg-offset-1 control-label">Nama</label>
                        <div class="col-lg-6">
                            <input type="text" name="name" id="name" class="form-control" required autofocus
                                oninvalid="this.setCustomValidity('Nama harus diisi.')"
                                oninput="this.setCustomValidity('')">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="form-group row">
                        <label for="email" class="col-lg-2 col-lg-offset-1 control-label">Email</label>
                        <div class="col-lg-6">
                            <input type="email" name="email" id="email" class="form-control" required
                                oninvalid="this.setCustomValidity(
                                    this.validity.valueMissing ? 'Email harus diisi.' :
                                    this.validity.typeMismatch ? 'Email harus mengandung @ dan format valid.' : ''
                                )"
                                oninput="this.setCustomValidity('')">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="form-group row">
                        <label for="password" class="col-lg-2 col-lg-offset-1 control-label">Password</label>
                        <div class="col-lg-6">
                            <input type="password" name="password" id="password" class="form-control"
                                   placeholder="Password (isi saat tambah / ganti)">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-lg-2 col-lg-offset-1 control-label">
                            Konfirmasi Password
                        </label>
                        <div class="col-lg-6">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control" placeholder="Konfirmasi Password">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    {{-- Level / Hak Akses --}}
                    <div class="form-group row">
                        <label for="level" class="col-lg-2 col-lg-offset-1 control-label">Hak Akses</label>
                        <div class="col-lg-6">
                            <select name="level" id="level" class="form-control" required
                                oninvalid="this.setCustomValidity('Silahkan pilih hak akses.')"
                                oninput="this.setCustomValidity('')">
                                <option value="">-- Pilih Hak Akses --</option>
                                <option value="0">Direktur Utama</option>
                                <option value="1">Kepala Produksi</option>
                                <option value="2">Admin</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    {{-- div-toko DIHAPUS --}}
                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-sm btn-flat btn-warning" data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
