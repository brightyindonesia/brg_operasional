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

                    <div class="form-group"><label>Pilih Toko</label>
                      <?php echo form_dropdown('toko', $get_all_toko, '', $toko) ?>
                      <p class="help-block"><i>*) Data Kosong = Semua Data</i></p>
                    </div>

                    <div class="form-group"><label>Pilih Provinsi</label>
                      <?php echo form_dropdown('provinsi', $get_all_provinsi, 'semua', $provinsi) ?>
                    </div>

                    <div class="form-group"><label>Pilih Kabupaten</label>
                      <select name="kotkab" class="form-control" id="kotkab" required="" style="width:100%">
                        <option value="semua" selected="selected">- Semua Data -</option>
                      </select>
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
                    <label>Format Gabung.in</label>
                    <button id="btn-gabungin" class="btn btn-sm btn-success form-control" style="float: right;"><i class="fa fa-file-excel-o" style="margin-right: 5px"></i> Cetak</button>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Format Google Contacts</label>
                    <button id="btn-google-contacts" class="btn btn-sm btn-success form-control" style="float: right;"><i class="fa fa-file-excel-o" style="margin-right: 5px"></i> Cetak</button>
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

  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>select2/dist/css/select2-flat-theme.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>select2/dist/js/select2.full.min.js"></script>

  <script>
  $(document).ready( function () {
    //Initialize Select2 Elements
    $("#toko").select2({
      theme: "flat",
      closeOnSelect: false
    });

    $('#provinsi').on('change', function(){
          var provinsi = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (provinsi != '') {
            // console.log(provinsi);
            $.ajax({
              url: "<?php echo base_url()?>admin/laporan/get_id_provinsi",
              type: "post",
              data: {'provinsi': provinsi, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                $('#kotkab').html(data);
                $("#provinsi option[value='']").remove();
                // $("#example3").find('tbody').empty(); //add this line
              },
              error: function(){
                alert('Error ....');
              }
            });
          }
         });

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

    $('#btn-gabungin').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        var periodik = document.getElementById("range-date-data").value;
        var provinsi = document.getElementById("provinsi").value;
        var kotkab = document.getElementById("kotkab").value;
        var toko = $('#toko').select2('data').map(function(elem){ 
                            return elem.id 
                        });
        if (toko.length > 0) {
          var fix_toko = toko;
        }else{
          var fix_toko = 'semua';
        }
        var users = <?php echo $this->session->userdata('id_users'); ?>;

        window.open("<?php echo base_url() ?>admin/laporan/export_gabungin/"+provinsi+"/"+kotkab+"/"+fix_toko+"/"+users+"/"+periodik,+"_self");
      }
    });

    $('#btn-google-contacts').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        var periodik = document.getElementById("range-date-data").value;
        var provinsi = document.getElementById("provinsi").value;
        var kotkab = document.getElementById("kotkab").value;
        var toko = $('#toko').select2('data').map(function(elem){ 
                            return elem.id 
                        });
        if (toko.length > 0) {
          var fix_toko = toko;
        }else{
          var fix_toko = 'semua';
        }
        var users = <?php echo $this->session->userdata('id_users'); ?>;

        window.open("<?php echo base_url() ?>admin/laporan/export_google_contacts/"+provinsi+"/"+kotkab+"/"+fix_toko+"/"+users+"/"+periodik,+"_self");
      }
    });
  });
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
