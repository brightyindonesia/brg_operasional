<div class="row">
  <div class="col-sm-12">
    <!-- TABLE: SKU KELUAR TOKO -->
    <div class="box no-border">
      <div class="col-sm-3">
        <div class="box-header">
          <label>Pilih Toko</label>
          <?php echo form_dropdown('toko_impor', '', '', $toko_impor) ?>
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