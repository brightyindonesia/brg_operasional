<div class="row">
  <div class="col-sm-12">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#imporStatistik" data-toggle="tab">Tanggal Diimpor</a></li>
        <li><a href="#penjualanStatistik" data-toggle="tab">Tanggal Penjualan</a></li>
      </ul>
      <div class="tab-content">
        <div class="active imporStatistik tab-pane" id="imporStatistik">
          <?php include('tab_content/content_dashboard_statistik_impor.php'); ?>                
        </div>

        <div class="tab-pane" id="penjualanStatistik">  
          <?php include('tab_content/content_dashboard_statistik_penjualan.php'); ?>  
        </div>       
      </div>
    </div>
  </div>
</div>
