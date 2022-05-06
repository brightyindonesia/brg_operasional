<div class="modal" id="modal-edit" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <?php echo form_open(base_url('admin/surat/detail_surat_jalan_ubah'),'id="form-edit"'); ?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                    <div id="trx-judul"><h4>Ubah Detail Surat Jalan</h4></div>
                </div>
                <div class="modal-body">
                    <div class="row-fluid">
                        <div class="box-body">
                            <div id="form-pesan-edit"></div>
                            <div class="form-group">
                                <label>Kode Barang</label>
                                <input type="hidden" name="edit-id" id="edit-id">
                                <input type="hidden" name="edit-pilihan" id="edit-pilihan">
                                <input type="text" class="form-control" id="edit-kode" name="edit-kode">
                            </div>

                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text" class="form-control" id="edit-nama" name="edit-nama">
                            </div>

                            <div class="form-group">
                                <label>Jumlah Barang</label>
                                <input type="text" class="form-control" id="edit-jumlah" name="edit-jumlah">
                            </div>

                            <div class="form-group">
                                <label>Satuan Barang</label>
                                <input type="text" class="form-control" id="edit-satuan" name="edit-satuan">
                            </div>

                            <div class="form-group">
                                <label>Keterangan</label>
                                <input type="text" class="form-control" id="edit-keterangan" name="edit-keterangan">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="edit-hapus" class="btn btn-danger"><i class="fa fa-trash" style="margin-right: 5px;"></i>Hapus</button>
                    <button type="button" id="edit-simpan" class="btn btn-success"><i class="fa fa-save" style="margin-right: 5px;"></i>Simpan</button>
                    <a href="#" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-close" style="margin-right: 5px;"></i>Tutup</a>
                </div>
            </div>
        </div>

    </form>
</div>