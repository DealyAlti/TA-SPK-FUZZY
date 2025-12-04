<div class="modal fade" id="modal-form">
    <div class="modal-dialog">
        <form class="modal-content" method="post" action="">
            @csrf
            @method('post')

            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>

            <div class="modal-body">

                <input type="hidden" name="id_produk" id="id_produk">

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Penjualan</label>
                    <input type="number" name="penjualan" class="form-control" value="0" min="0">
                </div>

                <div class="form-group">
                    <label>Stok Barang Jadi</label>
                    <input type="number" name="stok_barang_jadi" class="form-control" value="0" min="0" required>
                </div>

                <div class="form-group">
                    <label>Hasil Produksi</label>
                    <input type="number" name="hasil_produksi" class="form-control" value="0" min="0">
                </div>


            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>
