<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#gudang" data-toggle="tab">Gudang</a></li>
    <li><a href="#toko" data-toggle="tab">Toko</a></li>
  </ul>
  <div class="tab-content">
    <div class="active gudang tab-pane" id="gudang">
      <div class="row">
        <div class="col-sm-12">
          <!-- TABLE: SKU KELUAR GUDANG -->
          <div class="box no-border">
            <div class="col-sm-3">
              <div class="box-header">
                <!-- <label>Pilih Gudang</label>
                <?php echo form_dropdown('toko_impor', '', '', $toko_impor) ?> -->

                <div class="form-group">
                  <label>Pilih Gudang</label>
                  <?php echo form_dropdown('', $get_all_gudang_impor, '', $gudang_impor_id) ?>
                </div>

                <div class="form-group">
                  <button class="btn btn-sm btn-danger" id="reset-gudang-impor"><i class="fa fa-refresh"></i> Reset</button>
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
                  <table id="table-sku-gudang-keluar-impor" class="table no-margin">
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

    <div class="tab-pane" id="toko">
      <div class="row">
        <div class="col-sm-12">
          <!-- TABLE: SKU KELUAR TOKO -->
          <div class="box no-border">
            <div class="col-sm-3">
              <div class="box-header">
                <!-- <label>Pilih Toko</label>
                <?php echo form_dropdown('toko_impor', '', '', $toko_impor) ?> -->

                <div class="form-group">
                  <label>Pilih Toko</label>
                  <?php echo form_dropdown('', $get_all_toko_impor, '', $toko_impor_id) ?>
                </div>

                <div class="form-group">
                  <button class="btn btn-sm btn-danger" id="reset-toko-impor"><i class="fa fa-reset"></i> Reset</button>
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
                  <table id="table-sku-keluar-impor" class="table no-margin">
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
        <div id="container-protok2"></div>
    </figure>
  </div>

  <div class="col-sm-6">
    <!-- Dasbor Prokur 2 -->
    <figure class="highcharts-figure">
        <div id="container-prokur2"></div>
    </figure>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <!-- Dasbor Protok 2 -->
    <figure class="highcharts-figure">
        <div id="container-prosku2"></div>
    </figure>
  </div>
</div>
