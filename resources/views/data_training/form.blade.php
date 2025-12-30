<div class="modal fade" id="modal-form">
    <div class="modal-dialog">
        <form id="form-training" class="modal-content" method="post" action="">
            @csrf
            @method('post')

            <input type="hidden" name="id_produk" id="id_produk">

            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Penjualan (Admin)</label>
                    <input type="number" name="penjualan" class="form-control" min="0" value="0">
                </div>

                <div class="form-group">
                    <label>Hasil Produksi (Kepala Produksi)</label>
                    <input type="number" name="hasil_produksi" class="form-control" min="0" value="0">
                </div>

                <p class="text-muted">
                    * Stok akhir akan dihitung otomatis oleh sistem.
                </p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
            </div>

        </form>
    </div>
</div>
