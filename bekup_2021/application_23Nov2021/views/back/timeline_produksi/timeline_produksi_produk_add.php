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
              <div class="col-sm-3">
                <div class="form-group"><label>Kode SKU (*)</label>
                  <?php echo form_dropdown('sku', $get_all_sku, $timeline->id_sku, $sku) ?>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group"><label>Sub SKU (*)</label>
                  <?php echo form_input($sub_sku) ?>
                  <?php echo form_input($id_hpp) ?>
                  <?php echo form_input($id_sku) ?>
                </div>
              </div>
              <div class="col-sm-7">
                <div class="form-group"><label>Nama Produk (*)</label>
                  <?php echo form_input($produk_nama) ?>
                </div>
              </div>
              
            </div>
            <div class="row">
              <div class="col-sm-2">
                <div class="form-group"><label>Qty (*)</label>
                  <?php echo form_input($qty) ?>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="form-group"><label>Hpp (*)</label>
                  <?php echo form_input($hpp) ?>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group"><label>Satuan Produk (*)</label>
                  <?php echo form_dropdown('satuan', $get_all_satuan, '', $satuan) ?>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group"><label>Daftar Toko (*)</label>
                  <?php echo form_dropdown('', $get_all_tokpro_data_access, '', $tokpro_access_id) ?>
                </div>
              </div>  
            </div>
          </div>
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
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>select2/dist/css/select2-flat-theme.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>select2/dist/js/select2.full.min.js"></script>

  <script type="text/javascript">
    $("#tokpro-access-id").select2({
      placeholder: "- Pilih Toko -",
      theme: "flat"
    });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
