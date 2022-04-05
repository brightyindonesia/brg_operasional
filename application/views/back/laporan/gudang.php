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
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="alert alert-info">
            <h3 style="margin-top: -5px"><b>PERHATIAN!!</b></h3>
            <p style="font-size: 16px">Menu dibawah ini digunakan untuk keperluan export / laporan data. <b>Pilih SKU</b> terlebih dahulu sebelum mengklik tombol <b>Cetak</b></p>
          </div>  
        </div>
        
        <div class="col-sm-3">
          <div class="box box-primary no-border">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <div class="form-group"><label>Pilih SKU</label>
                      <?php 
                        echo form_dropdown('sku', $get_sku, '', $sku);
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-9">
          <div class="box box-primary no-border">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Stok Produk</label>
                    <button id="btn-stok-produk" class="btn btn-sm btn-success form-control" style="float: right;"><i class="fa fa-file-excel-o" style="margin-right: 5px"></i> Cetak</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="col-sm-8">
            
          </div> -->
        </div>
      </div>

      
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>
  $(document).ready( function () {
    $('#btn-stok-produk').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        var sku = $('#sku').val();
        var users = <?php echo $this->session->userdata('id_users'); ?>;

        window.open("<?php echo base_url() ?>admin/laporan/export_stok_produk/"+users+"/"+sku,+"_self");
      }
    });
  });
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
