<div class="box box-primary no-border">
  <div class="box-header">
    <a href="<?php echo $add_action ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $btn_add ?></a> 
    <a onclick="export_penjualan('impor');" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Data</a>
    <a onclick="sinkron_harga('impor');" class="btn btn-warning"><i class="fa fa-refresh"></i> <?php echo $btn_sinkron_total_harga ?></a>
    <a onclick="hapus_by_date('impor');" class="btn btn-danger"><i class="fa fa-trash"></i> Delete by Date</a>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="table-responsive">
      <table id="table-penjualan-impor" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th style="text-align: center">#</th>
            <th style="text-align: center">Tanggal</th>
            <th width="27%" style="text-align: center">No. Pesanan</th>
            <th style="text-align: center">Toko</th>
            <th style="text-align: center">Kurir</th>
            <th style="text-align: center">No. Resi</th>
            <th style="text-align: center">Action</th>
            <th width="1%" style="text-align: center">
              <input type="checkbox" id="master">
            </th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <!-- /.box-body -->
  <div class="box-footer">
    <button type="button" style="float: right;" id="btn-delete-pilih" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus Dipilih</button>
  </div>
</div>
<!-- /.box -->  