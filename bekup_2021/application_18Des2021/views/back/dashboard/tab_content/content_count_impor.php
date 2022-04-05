<!-- DASBOR: STATUS PENJUALAN -->
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Jumlah Status Penjualan</h3>
    <a href="<?php echo base_url('admin/keluar/data_penjualan') ?>" class="btn btn-sm btn-success btn-flat pull-right"><i class="fa fa-table" style="margin-right: 5px;"></i> More Data</a>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="info-box">
      <span class="info-box-icon bg-aqua"><i class="fa fa-cloud-upload"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Penjualan</span>
        <span class="info-box-number" id="total-penjualan"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataPenjualan('semua', 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="info-box">
      <span class="info-box-icon bg-yellow"><i class="fa fa-hourglass-half"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Pending Payment</span>
        <span class="info-box-number" id="jumlah-pending-penjualan"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataPenjualan(1, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->
    <div class="info-box">
      <span class="info-box-icon bg-blue"><i class="fa fa-money"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Transfer</span>
        <span class="info-box-number" id="jumlah-transfer-penjualan"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataPenjualan(2, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->

    <div class="info-box">
      <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Pembayaran Diterima</span>
        <span class="info-box-number" id="jumlah-diterima-penjualan"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataPenjualan(3, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->
     <div class="info-box">
        <span class="info-box-icon bg-red"><i class="fa fa-exchange"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Retur</span>
          <span class="info-box-number" id="jumlah-retur-penjualan"></span>
        </div>
        <!-- /.info-box-content -->

        <div class="info-box-footer">
        <a href="#" onclick="tabelDataPenjualan(4, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
      </div>
      <!-- /.info-box -->
  </div>
  <!-- /.box-body -->
</div>

<!-- DASBOR: STATUS RESI -->
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Jumlah Status Resi</h3>
    <a href="<?php echo base_url('admin/resi') ?>" class="btn btn-sm btn-success btn-flat pull-right"><i class="fa fa-table" style="margin-right: 5px;"></i> More Data</a>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="info-box">
      <span class="info-box-icon bg-aqua"><i class="fa fa-bolt"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Resi</span>
        <span class="info-box-number" id="total-resi"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataResi('semua', 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="info-box">
      <span class="info-box-icon bg-yellow"><i class="fa fa-times"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Belum Diproses</span>
        <span class="info-box-number" id="jumlah-belum-resi"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataResi(0, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->

    <div class="info-box">
      <span class="info-box-icon bg-blue"><i class="fa fa-hourglass-half"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Sedang Diproses</span>
        <span class="info-box-number" id="jumlah-sedang-resi"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataResi(1, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->

    <div class="info-box">
      <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Sudah Diproses</span>
        <span class="info-box-number" id="jumlah-sudah-resi"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataResi(2, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->

    <div class="info-box">
      <span class="info-box-icon bg-red"><i class="fa fa-exchange"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Retur</span>
        <span class="info-box-number" id="jumlah-retur-resi"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataResi(3, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->

<!-- DASBOR: STATUS RETUR -->
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Jumlah Status Retur</h3>
    <a href="<?php echo base_url('admin/retur/data_retur') ?>" class="btn btn-sm btn-success btn-flat pull-right"><i class="fa fa-table" style="margin-right: 5px;"></i> More Data</a>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="info-box">
      <span class="info-box-icon bg-aqua"><i class="fa fa-rotate-left"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Retur</span>
        <span class="info-box-number" id="total-retur"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataRetur('semua', 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="info-box">
      <span class="info-box-icon bg-yellow"><i class="fa fa-hourglass-half"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Sedang Diproses</span>
        <span class="info-box-number" id="jumlah-sedang-retur"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataRetur(0, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->

    <div class="info-box">
      <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Sudah Diproses</span>
        <span class="info-box-number"id="jumlah-sudah-retur"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataRetur(1, 'impor');" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->

    <div class="info-box">
      <span class="info-box-icon bg-blue"><i class="fa fa-bullhorn"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Sudah Difollow Up</span>
        <span class="info-box-number" id="jumlah-sudah-follow-retur"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataRetur('semua', 'impor', 0);" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->

    <div class="info-box">
      <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Belum Difollow Up</span>
        <span class="info-box-number" id="jumlah-belum-follow-retur"></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="#" onclick="tabelDataRetur('semua', 'impor', 1);" style="margin-left: 10px;">View Data <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.box-body -->
</div>
<!-- /.box -->
