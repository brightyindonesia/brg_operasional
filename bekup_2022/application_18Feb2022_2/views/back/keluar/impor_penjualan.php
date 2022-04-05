<?php $this->load->view('back/template/meta'); ?>
<div class="wrapper">

  <?php $this->load->view('back/template/navbar'); ?>
  <?php $this->load->view('back/template/sidebar'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><?php echo $page_title ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><?php echo $module ?></li>
        <li class="active"><?php echo $page_title ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="alert alert-info">
        <h3 style="margin-top: -5px"><b>PERHATIAN!!</b></h3>
        <p style="font-size: 16px">Sebelum memasukan Data Import Penjualan <b>WAJIB</b> melakukan <b>Backup Database</b> terlebih dahulu!!</p>
      </div>
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php echo validation_errors() ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#penjualan" data-toggle="tab">Import Penjualan</a></li>
              <li><a href="#shopee" data-toggle="tab">Import Shopee</a></li>
              <li><a href="#tokped" data-toggle="tab">Import Tokopedia</a></li>
              <li><a href="#tiktok" data-toggle="tab">Import Tiktok</a></li>
              <li><a href="#blibli" data-toggle="tab">Import BliBli</a></li>
              <li><a href="#lazada" data-toggle="tab">Import Lazada</a></li>
              <li><a href="#backup" data-toggle="tab">Backup Database</a></li>
            </ul>
            <div class="tab-content">
              <div class="active penjualan tab-pane" id="penjualan">
                <?php include('tab_content/content_import_penjualan.php'); ?>                
              </div>

              <div class="shopee tab-pane" id="shopee">        
              </div>

              <div class="tokped tab-pane" id="tokped">        
              </div>

              <div class="tiktok tab-pane" id="tiktok">        
              </div>

              <div class="blibli tab-pane" id="blibli">        
              </div>

              <div class="lazada tab-pane" id="lazada">        
              </div>

              <div class="backup tab-pane" id="backup">
                <?php include('tab_content/content_import_backup.php'); ?>  
              </div>               
            </div>
          </div>
        </div>
      </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>

</div>
<!-- ./wrapper -->

</body>
</html>
