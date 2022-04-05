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
              <div class="col-sm-12">
                  <div class="form-group"><label>Nomor Produksi (*)</label>
                    <?php echo form_input($nomor_request, str_replace("PO","TML",$timeline->no_po)) ?>
                  </div>

                  <div class="form-group"><label>Nomor Purchase Order (*)</label>
                    <?php echo form_input($nomor_request, $timeline->no_po) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $timeline->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, $timeline->id_kategori_po, $kategori) ?>
                  </div>

                  <div class="form-group"><label>Nama Vendor (*)</label>
                    <?php echo form_input($nama_vendor, $timeline->nama_vendor) ?>
                  </div>

                  <div class="form-group"><label>Jumlah Produksi (*)</label>
                    <?php echo form_input($qty, $timeline->total_kuantitas_po) ?>
                  </div>
              </div>
            </div>

            <?php echo form_input($id, str_replace("PO","TML",$timeline->no_po)) ?>
            <?php echo form_input($id_po, $timeline->no_po) ?>
            <?php echo form_input($id_sku, $timeline->id_sku) ?>
            <?php echo form_input($qty_produksi, $timeline->total_kuantitas_po) ?>
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

  <?php $this->load->view('back/template/footer'); ?>

  <script type="text/javascript">
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    $('#example3').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": false,
      "info": false,
      "autoWidth": false
    });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
