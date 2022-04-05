<div class="row">
  <div class="col-sm-2">
    <div class="form-group">
      <label>Kode Barang</label>
      <input type="text" name="kode_barang" id="in_kode" value="" class="form-control">
    </div>
  </div>

  <div class="col-sm-2">
    <div class="form-group">
      <label>Nama Barang</label>
      <input type="text" name="nama_barang" id="in_nama" value="" class="form-control">
    </div>
  </div>

  <div class="col-sm-1">
    <div class="form-group">
      <label>Jumlah</label>
      <input type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" name="qty" id="in_qty" class="form-control">
    </div>
  </div>

  <div class="col-sm-2">
    <div class="form-group">
      <label>Satuan Barang</label>
      <input type="text" name="satuan_barang" id="in_satuan" value="" class="form-control">
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group">
      <label>Keterangan</label>
      <input type="text" name="keterangan" id="in_keterangan" value="" class="form-control">
    </div>
  </div>

  <div class="col-sm-2">
    <div class="form-group">
      <label>Tambah</label>
      <button type="button" id="add_produk" class="btn btn-success btn-sm form-control">
        <i class="fa fa-plus"></i>                  
      </button>
    </div>
  </div>
</div>