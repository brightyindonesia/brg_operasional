<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#gudang-penjualan" data-toggle="tab">Gudang</a></li>
    <li><a href="#toko-penjualan" data-toggle="tab">Toko</a></li>
  </ul>
  <div class="tab-content">
    <div class="active gudang-penjualan tab-pane" id="gudang-penjualan">
      <div class="row">
        <div class="col-sm-12">
          <!-- TABLE: SKU KELUAR TOKO -->
          <div class="box no-border">
            <div class="col-sm-3">
              <div class="box-header">
                <div class="form-group">
                  <label>Pilih Gudang</label>
                  <?php echo form_dropdown('', $get_all_gudang_penjualan, '', $gudang_penjualan_id) ?>
                </div>
              </div>
            </div>

            <div class="col-sm-9">
              <div class="box-header with-border">
                <h3 class="box-title">SKU Keluar List</h3>
            
                <div class="box-tools pull-right">
                  <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button> -->
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <div class="table-responsive">
                  <table id="table-sku-gudang-keluar-penjualan" class="table no-margin">
                    <thead>
                    <tr>
                      <th>SKU</th>
                      <th>Nama Produk</th>
                      <th>Total Qty</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->
              </div>
              <!-- /.box-body -->
            </div>
          </div>
          <!-- /.box -->
        </div>
      </div> 
    </div>

    <div class="tab-pane toko-penjualan" id="toko-penjualan">
      <div class="row">
        <div class="col-sm-12">
          <!-- TABLE: SKU KELUAR TOKO -->
          <div class="box no-border">
            <div class="col-sm-3">
              <div class="box-header">
                <div class="form-group">
                  <label>Pilih Toko</label>
                  <?php echo form_dropdown('', $get_all_toko_penjualan, '', $toko_penjualan_id) ?>
                </div>
              </div>
            </div>

            <div class="col-sm-9">
              <div class="box-header with-border">
                <h3 class="box-title">SKU Keluar List</h3>
            
                <div class="box-tools pull-right">
                  <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button> -->
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <div class="table-responsive">
                  <table id="table-sku-keluar-penjualan" class="table no-margin">
                    <thead>
                    <tr>
                      <th>SKU</th>
                      <th>Nama Produk</th>
                      <th>Total Qty</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->
              </div>
              <!-- /.box-body -->
            </div>
          </div>
          <!-- /.box -->
        </div>
      </div>
    </div>       
  </div>
</div>

<div class="row">
  <div class="col-sm-6">
    <!-- Dasbor Protok 2 -->
    <figure class="highcharts-figure">
        <div id="container-protok2-penjualan"></div>
    </figure>
  </div>

  <div class="col-sm-6">
    <!-- Dasbor Prokur 2 -->
    <figure class="highcharts-figure">
        <div id="container-prokur2-penjualan"></div>
    </figure>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <!-- Dasbor Protok 2 -->
    <figure class="highcharts-figure">
        <div id="container-prosku2-penjualan"></div>
    </figure>
  </div>
</div>