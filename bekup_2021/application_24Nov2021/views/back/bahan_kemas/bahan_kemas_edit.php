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
              <div class="col-sm-4">
                <div class="form-group"><label>Kode SKU (*)</label>
                  <?php echo form_input($kode_sku, $bahan_kemas->kode_sku_bahan_kemas) ?>
                </div>

                <div class="form-group"><label>Nama Bahan Kemas (*)</label>
                  <?php echo form_input($bahan_kemas_nama, $bahan_kemas->nama_bahan_kemas) ?>
                </div>

                <div class="form-group"><label>Qty (*)</label>
                  <?php echo form_input($qty, $bahan_kemas->qty_bahan_kemas) ?>
                </div>
              </div>
              <div class="col-sm-8">
                <div class="form-group"><label>Keterangan (*)</label>
                  <?php echo form_textarea($keterangan, $bahan_kemas->keterangan) ?>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group"><label>Vendor Tersedia:</label>
                  <p>
                    <?php
                    if($get_all_venmas_data_access_old == NULL)
                    {
                      echo '<button class="btn btn-sm btn-danger">No Data</button>';
                    }
                    else
                    {
                      foreach($get_all_venmas_data_access_old as $data_access)
                      {
                        $string = chunk_split($data_access->nama_vendor, 50, "</button> ");
                        echo '<button class="btn btn-sm btn-success">'.$string;
                      }
                    }
                    ?>
                  </p>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group"><label>Satuan Bahan Kemas (*)</label>
                  <?php echo form_dropdown('satuan', $get_all_satuan, $bahan_kemas->id_satuan, $satuan) ?>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group"><label>Daftar Vendor (*)</label>
                  <?php echo form_dropdown('', $get_all_combobox_venmas_data_access, '', $venmas_access_id) ?>
                </div>
              </div>  
            </div>
          </div>
          <?php echo form_input($id_bahan_kemas, $bahan_kemas->id_bahan_kemas) ?>
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
    $("#venmas-access-id").select2({
      placeholder: "- Pilih Toko -",
      theme: "flat"
    });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
