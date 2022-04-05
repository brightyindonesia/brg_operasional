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

      <div class="box box-primary">
        <!-- <div class="box-header">
          <a href="<?php echo $add_action ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $btn_add ?></a>
        </div> -->
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table id="datatable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="text-align: center">No</th>
                  <th style="text-align: center">Tanggal</th>
                  <th style="text-align: center">No. Purchase Order</th>
                  <th style="text-align: center">Nama Vendor</th>
                  <th style="text-align: center">Nama SKU</th>
                  <th style="text-align: center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach($get_all as $data){
                  // action
                  $add = '<a href="'.base_url('admin/timeline_produksi/tambah/'.base64_encode($data->no_po)).'" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></a>';
                  // $history = '<a href="'.base_url('admin/masuk/produksi_history/'.base64_encode($data->no_po)).'" class="btn btn-sm btn-primary"><i class="fa fa-hourglass-half"></i></a>';
                ?>
                  <tr>
                    <td style="text-align: center"><?php echo $no++ ?></td>
                    <td style="text-align: center"><?php echo $data->tgl_po ?></td>
                    <td style="text-align: center"><?php echo $data->no_po ?></td>
                    <td style="text-align: center"><?php echo $data->nama_vendor ?></td>
                    <td style="text-align: center"><?php echo $data->nama_sku ?></td>
                    <td style="text-align: center"> <?php echo $add ?></td>
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <th style="text-align: center">No</th>
                  <th style="text-align: center">Tanggal</th>
                  <th style="text-align: center">No. Purchase Order</th>
                  <th style="text-align: center">Nama Vendor</th>
                  <th style="text-align: center">Nama SKU</th>
                  <th style="text-align: center">Action</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>
  $(document).ready( function () {
    $('#datatable').DataTable();
  } );
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
