<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"></h4>
                </div>

                <div class="modal-body">
                    {{-- Nama Kategori --}}
                    <div class="form-group row">
                        <label for="nama_kategori"
                               class="col-lg-3 control-label">
                            Kategori
                        </label>
                        <div class="col-lg-6">
                            <input type="text"
                                   name="nama_kategori"
                                   id="nama_kategori"
                                   class="form-control"
                                   required
                                   oninvalid="this.setCustomValidity('Kategori harus diisi.')"
                                   oninput="this.setCustomValidity('')">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    {{-- Kapasitas Produksi --}}
                    <div class="form-group row">
                        <label class="col-lg-3 control-label">
                            Kapasitas Produksi (kg)
                        </label>
                        <div class="col-lg-3">
                            <input type="number"
                                   name="kapasitas_min"
                                   id="kapasitas_min"
                                   class="form-control"
                                   placeholder="Min"
                                   min="0">
                            <span class="help-block with-errors"></span>
                        </div>
                        <div class="col-lg-3">
                            <input type="number"
                                   name="kapasitas_max"
                                   id="kapasitas_max"
                                   class="form-control"
                                   placeholder="Max"
                                   min="0">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>

                    {{-- Waktu Produksi --}}
                    <div class="form-group row">
                        <label class="col-lg-3 control-label">
                            Waktu Produksi (jam)
                        </label>
                        <div class="col-lg-3">
                            <input type="number"
                                   name="waktu_min"
                                   id="waktu_min"
                                   class="form-control"
                                   placeholder="Min"
                                   min="0">
                            <span class="help-block with-errors"></span>
                        </div>
                        <div class="col-lg-3">
                            <input type="number"
                                   name="waktu_max"
                                   id="waktu_max"
                                   class="form-control"
                                   placeholder="Max"
                                   min="0">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-primary">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-flat btn-warning"
                            data-dismiss="modal">
                        <i class="fa fa-arrow-circle-left"></i> Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
