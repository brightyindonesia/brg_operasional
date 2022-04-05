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
      <?php echo validation_errors() ?>
      <div class="box box-primary">
        <?php 
          echo form_open($action);
        ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Surat Jalan (*)</label>
                    <?php echo form_input($nomor_surat_jalan) ?>
                  </div>

                  <div class="form-group"><label>Nama Surat Jalan (*)</label>
                    <?php echo form_input($nama_surat_jalan) ?>
                  </div>

                  <div class="form-group"><label>Keterangan Surat Jalan</label>
                    <?php echo form_textarea($keterangan, '') ?>
                  </div>
              </div>

              <div class="col-sm-6">
                  <div class="form-group"><label>Tanggal Surat Jalan (*)</label>
                    <input type="text" name="periodik" class="form-control float-right" id="date">
                  </div>

                  <div class="form-group"><label>Kepada Penerima (*)</label>
                    <?php echo form_input($kepada_surat_jalan) ?>
                  </div>

                  <div class="form-group"><label>Pilih Nama Penerima (*)</label>
                    <?php echo form_dropdown('penerima', $get_all_penerima, '', $penerima) ?>
                  </div>
              </div>
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" id="produksi_add" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          </div>
      </div>
      <?php 
        echo form_close();
      ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>.
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- bootstrap datepicker -->
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

  <script type="text/javascript">
    //Initialize Select2 Elements
    $(document).ready( function () {
      $("#date").datepicker({
        format: "yyyy/mm/dd"
      }).datepicker("setDate", new Date());
    });

    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
