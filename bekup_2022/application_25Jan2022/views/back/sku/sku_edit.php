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
                <div class="form-group"><label>Kode SKU (*)</label>
                  <?php echo form_input($kode_sku, $sku->kode_sku) ?>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group"><label>Nama SKU (*)</label>
                  <?php echo form_input($sku_nama, $sku->nama_sku) ?>
                </div>
              </div>
            </div>
          </div>
          <?php echo form_input($id_sku, $sku->id_sku) ?>
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
