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
                <div class="form-group"><label>Nama Vendor (*)</label>
                  <?php echo form_input($vendor_nama, $vendor->nama_vendor) ?>
                </div>

                <div class="form-group"><label>No. Handphone Vendor</label>
                  <?php echo form_input($vendor_hp, $vendor->no_hp_vendor) ?>
                </div>

                <div class="form-group"><label>No. Telepon Vendor</label>
                  <?php echo form_input($vendor_telpon, $vendor->no_telpon_vendor) ?>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group"><label>Alamat Vendor</label>
                  <?php echo form_textarea($vendor_alamat, $vendor->alamat_vendor) ?>
                </div>
              </div>
            </div>
          </div>
          <?php echo form_input($id_vendor, $vendor->id_vendor) ?>
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
