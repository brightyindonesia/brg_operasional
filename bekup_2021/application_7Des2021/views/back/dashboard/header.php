<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Produk</span>
        <span class="info-box-number"><?php echo $get_total_produk ?></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="<?php echo base_url('admin/produk') ?>" style="margin-left: 10px;">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Toko</span>
        <span class="info-box-number"><?php echo $get_total_toko ?></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="<?php echo base_url('admin/toko') ?>" style="margin-left: 10px;">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-yellow"><i class="fa fa-archive"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Bahan Produksi</span>
        <span class="info-box-number"><?php echo $get_total_bahan ?></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="<?php echo base_url('admin/bahan_kemas') ?>" style="margin-left: 10px;">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->
  </div>

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-yellow"><i class="fa fa-archive"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">Vendor</span>
        <span class="info-box-number"><?php echo $get_total_bahan ?></span>
      </div>
      <!-- /.info-box-content -->

      <div class="info-box-footer">
        <a href="<?php echo base_url('admin/bahan_kemas') ?>" style="margin-left: 10px;">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <!-- <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-orange">
      <div class="inner">
        <h3><?php echo $get_total_menu ?></h3>
        <p>Menu</p>
      </div>
      <div class="icon"><i class="fa fa-list"></i></div>
      <a href="<?php echo base_url('admin/menu') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-aqua">
      <div class="inner">
        <h3><?php echo $get_total_submenu ?></h3>
        <p>SubMenu</p>
      </div>
      <div class="icon"><i class="fa fa-list"></i></div>
      <a href="<?php echo base_url('admin/submenu') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-red">
      <div class="inner">
        <h3><?php echo $get_total_user ?></h3>
        <p>User</p>
      </div>
      <div class="icon"><i class="fa fa-user"></i></div>
      <a href="<?php echo base_url('admin/auth') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-green">
      <div class="inner">
        <h3><?php echo $get_total_usertype ?></h3>
        <p>Usertype</p>
      </div>
      <div class="icon"><i class="fa fa-legal"></i></div>
      <a href="<?php echo base_url('admin/usertype') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div> -->
</div>
<!-- /.row -->

<!-- row -->
<div class="row">
  <div class="col-sm-8">
    <!-- TABLE: LATEST ORDERS PRODUK -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Produk Status List</h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="table-responsive">
          <table id="table-produk-status" class="table no-margin">
            <thead>
            <tr>
              <th>SKU</th>
              <th>Sub SKU</th>
              <th>Nama Produk</th>
              <th>Status</th>
              <th>Stok</th>
            </tr>
            </thead>
            <tbody>
              <tr>
                <td><a href="pages/examples/invoice.html">OR9842</a></td>
                <td>Call of Duty IV</td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">Shipped</span></td>
                <td>
                  40
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.table-responsive -->
      </div>
      <!-- /.box-body -->
      <div class="box-footer clearfix">
        <a href="<?php echo base_url('admin/masuk/request') ?>" class="btn btn-sm btn-success btn-flat pull-left"><i class="fa fa-cloud-download" style="margin-right: 5px;"></i> Open Purchase Order</a>
        <a href="<?php echo base_url('admin/produk') ?>" class="btn btn-sm btn-primary btn-flat pull-right"><i class="fa fa-table" style="margin-right: 5px;"></i> More Data</a>
      </div>
      <!-- /.box-footer -->
    </div>
    <!-- /.box -->
  </div>

  <div class="col-sm-4">
    <!-- TABLE: LATEST ORDERS BAHAN PRODUKSI -->
    <div class="box box-warning">
      <div class="box-header with-border">
        <h3 class="box-title">Bahan Produksi Status List</h3>

        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
          </button>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="table-responsive">
          <table id="table-bahan-status" class="table no-margin">
            <thead>
            <tr>
              <th>SKU</th>
              <th>Nama Bahan Produksi</th>
              <th>Status</th>
              <th>Stok</th>
            </tr>
            </thead>
            <tbody>
              <tr>
                <td><a href="pages/examples/invoice.html">OR9842</a></td>
                <td>Call of Duty IV</td>
                <td><span class="label label-success">Shipped</span></td>
                <td>
                  40
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.table-responsive -->
      </div>
      <!-- /.box-body -->
      <div class="box-footer clearfix">
        <a href="<?php echo base_url('admin/masuk/request') ?>" class="btn btn-sm btn-success btn-flat pull-left"><i class="fa fa-cloud-download" style="margin-right: 5px;"></i> Open Purchase Order</a>
        <a href="<?php echo base_url('admin/bahan_kemas') ?>" class="btn btn-sm btn-primary btn-flat pull-right"><i class="fa fa-table" style="margin-right: 5px;"></i> More Data</a>
      </div>
      <!-- /.box-footer -->
    </div>
    <!-- /.box -->
  </div>
</div>
<!-- /.row -->
