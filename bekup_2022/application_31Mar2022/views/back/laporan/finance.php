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
            <p style="font-size: 16px">Menu dibawah ini digunakan untuk keperluan export / laporan data. <b>Pilih Tanggal</b> terlebih dahulu sebelum mengklik tombol <b>Cetak</b></p>
          </div>  
        </div>
        
        <div class="col-sm-2">
          <div class="box box-primary no-border">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <div class="form-group"><label>Pilih Tanggal</label>
                      <input type="text" name="periodik_crm" class="form-control float-right" id="range-date-data">
                    </div>                    
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-10">
          <div class="box box-primary no-border">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Data Penjualan</label>
                    <button id="btn-data-sales" class="btn btn-sm btn-success form-control" style="float: right;"><i class="fa fa-file-excel-o" style="margin-right: 5px"></i> Cetak</button>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Data HPP Penjualan</label>
                    <button id="btn-data-hpp" class="btn btn-sm btn-success form-control" style="float: right;"><i class="fa fa-file-excel-o" style="margin-right: 5px"></i> Cetak</button>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Data Bruto Penjualan</label>
                    <button id="btn-data-bruto" class="btn btn-sm btn-success form-control" style="float: right;"><i class="fa fa-file-excel-o" style="margin-right: 5px"></i> Cetak</button>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Data Revenue Penjualan</label>
                    <button id="btn-data-margin" class="btn btn-sm btn-success form-control" style="float: right;"><i class="fa fa-file-excel-o" style="margin-right: 5px"></i> Cetak</button>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Data Pending Payment Penjualan</label>
                    <button id="btn-data-pending" class="btn btn-sm btn-success form-control" style="float: right;"><i class="fa fa-file-excel-o" style="margin-right: 5px"></i> Cetak</button>
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
    $('#range-date-data').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          'This Years'  : [moment().startOf('years'), moment().endOf('years')],
          'Last Years'  : [moment().subtract(1, 'years').startOf('years'), moment().subtract(1, 'years').endOf('years')],
        },
        startDate: moment(),
        endDate  : moment(),
        // startDate: moment().subtract(29, 'days'),
        // endDate  : moment(),

        locale: {
          format: 'YYYY-MM-DD'
        }
      },

      function (start, end) {
        $('#range-date-full span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
      }
    )

    $('#btn-data-sales').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        var periodik = document.getElementById("range-date-data").value;
        var users = <?php echo $this->session->userdata('id_users'); ?>;

        window.open("<?php echo base_url() ?>admin/laporan/export_data_penjualan/"+users+"/"+periodik,+"_self");
      }
    });

    $('#btn-data-hpp').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        var periodik = document.getElementById("range-date-data").value;
        var users = <?php echo $this->session->userdata('id_users'); ?>;

        window.open("<?php echo base_url() ?>admin/laporan/export_data_hpp/"+users+"/"+periodik,+"_self");
      }
    });

    $('#btn-data-bruto').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        var periodik = document.getElementById("range-date-data").value;
        var users = <?php echo $this->session->userdata('id_users'); ?>;

        window.open("<?php echo base_url() ?>admin/laporan/export_data_bruto/"+users+"/"+periodik,+"_self");
      }
    });

    $('#btn-data-margin').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        var periodik = document.getElementById("range-date-data").value;
        var users = <?php echo $this->session->userdata('id_users'); ?>;

        window.open("<?php echo base_url() ?>admin/laporan/export_data_margin/"+users+"/"+periodik,+"_self");
      }
    });

    $('#btn-data-pending').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        var periodik = document.getElementById("range-date-data").value;
        var users = <?php echo $this->session->userdata('id_users'); ?>;

        window.open("<?php echo base_url() ?>admin/laporan/export_data_pending/"+users+"/"+periodik,+"_self");
      }
    });
  });
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
