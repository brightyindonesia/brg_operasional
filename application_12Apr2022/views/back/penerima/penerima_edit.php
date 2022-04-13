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
        <?php echo form_open($action) ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group"><label>Nama Penerima (*)</label>
                  <?php echo form_input($penerima_nama, $penerima->nama_penerima) ?>
                </div>

                <div class="form-group"><label>No. Handphone Penerima</label>
                  <?php echo form_input($penerima_hp, $penerima->no_hp_penerima) ?>
                </div>

                <div class="form-group"><label>No. Telepon Penerima</label>
                  <?php echo form_input($penerima_telpon, $penerima->no_telpon_penerima) ?>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group"><label>Alamat Penerima</label>
                  <?php echo form_textarea($penerima_alamat, $penerima->alamat_penerima) ?>
                </div>
              </div>
            </div>
          </div>
          <?php echo form_input($id_penerima, $penerima->id_penerima) ?>
          <div class="box-footer">
            <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          </div>
          <!-- /.box-body -->
        <?php echo form_close() ?>
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
