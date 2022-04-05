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
          // echo form_open($action);
        ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Produksi</label>
                    <?php echo form_input($nomor_request, $timeline->no_timeline_produksi) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $timeline->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Vendor</label>
                    <?php echo form_input($nama_vendor, $timeline->nama_vendor) ?>
                  </div>
              </div>
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Purchase Order</label>
                    <?php echo form_input($nomor_request, $timeline->no_po) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, $timeline->id_kategori_po, $kategori) ?>
                  </div>

                  <div class="form-group"><label>Jumlah Produksi / Terkirim</label>
                    <?php echo form_input($qty, $timeline->total_produksi." / ".$timeline->total_produksi_jadi) ?>
                  </div>
              </div>
            </div>

            <?php echo form_input($id, $timeline->no_timeline_produksi) ?>
            <?php echo form_input($id_po, $timeline->no_po) ?>
            <?php echo form_input($id_sku, $timeline->id_sku) ?>
            <?php echo form_input($qty_produksi, $timeline->total_kuantitas_po) ?>
          </div>

          <div class="box-body">
            <div class="table-responsive">
              <table id="datatable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th style="text-align: center">No</th>
                    <th style="text-align: center">Jenis Timeline</th>
                    <th style="text-align: center">Nama Produksi</th>
                    <th style="text-align: center">Tanggal Awal</th>
                    <th style="text-align: center">Tanggal Akhir</th>
                    <th style="text-align: center">Durasi</th>
                    <th style="text-align: center">Keterangan</th>
                    <th style="text-align: center">Pemesanan</th>
                    <th style="text-align: center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $no = 1; foreach($detail as $data){
                    $diff = abs(strtotime($data->end_date_detail_timeline_produksi) - strtotime($data->start_date_detail_timeline_produksi));
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                    $edit = '';

                    if ($timeline->status_timeline_produksi == 0) {
                      $hapus = '<a href="'.base_url('admin/timeline_produksi/hapus_detail/'.base64_encode($data->id_detail_timeline_produksi)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                    }else{
                      $hapus = '';
                    }

                    if ($data->id_jenis_timeline == 1) {
                      $status = '<a href="#" class="btn btn-sm btn-info"><i class="fa fa-info" style="margin-right:5px;"></i>'.$data->nama_jenis_timeline.'</a>';
                    }elseif ($data->id_jenis_timeline == 2) {
                      $status = '<a href="#" class="btn btn-sm btn-primary"><i class="fa fa-industry" style="margin-right:5px;"></i>'.$data->nama_jenis_timeline.'</a>';
                    }elseif ($data->id_jenis_timeline == 3) {
                      $cek_posi = $this->lib_timeline_produksi->check_posi_by_detail_timeline($data->id_detail_timeline_produksi);

                      if ($cek_posi == 0) {
                        $edit = '<a href="'.base_url('admin/timeline_produksi/add_posi_delivery/'.base64_encode($data->id_detail_timeline_produksi)).'" class="btn btn-sm btn-success"><i class="fa fa-pencil"></i></a>';
                      }else if ($cek_posi == 1) {
                        $edit = '';
                      }
                      $status = '<a href="#" class="btn btn-sm btn-success"><i class="fa fa-truck" style="margin-right:5px;"></i>'.$data->nama_jenis_timeline.'</a>';
                    }elseif ($data->id_jenis_timeline == 4) {
                      $status = '<a href="#" class="btn btn-sm btn-warning"><i class="fa fa-exchange" style="margin-right:5px;"></i>'.$data->nama_jenis_timeline.'</a>';
                    }

                    if ($data->qty_detail_timeline_produksi == '' || $data->qty_detail_timeline_produksi == 0) {
                      $pemesanan = "-";
                    }else{
                      $pemesanan = $data->qty_detail_timeline_produksi;
                    }
                    
                    // $history = '<a href="'.base_url('admin/masuk/produksi_history/'.base64_encode($data->no_po)).'" class="btn btn-sm btn-primary"><i class="fa fa-hourglass-half"></i></a>';
                  ?>
                    <tr>
                      <td style="text-align: center"><?php echo $no++ ?></td>
                      <td style="text-align: center"><?php echo $status ?></td>
                      <td style="text-align: center"><?php echo $data->nama_bahan_kemas ?></td>
                      <td style="text-align: center"><?php echo date('d/m/Y', strtotime($data->start_date_detail_timeline_produksi)) ?></td>
                      <td style="text-align: center"><?php echo date('d/m/Y', strtotime($data->end_date_detail_timeline_produksi)) ?></td>
                      <td style="text-align: center"><?php echo $days ?> Hari</td>
                      <td style="text-align: center"><?php echo $data->ket_detail_timeline_produksi ?></td>
                      <td style="text-align: center"><?php echo $pemesanan ?></td>
                      <td style="text-align: center"><?php echo $edit.' '.$hapus ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th style="text-align: center">No</th>
                    <th style="text-align: center">Jenis Timeline</th>
                    <th style="text-align: center">Nama Produksi</th>
                    <th style="text-align: center">Tanggal Awal</th>
                    <th style="text-align: center">Tanggal Akhir</th>
                    <th style="text-align: center">Durasi</th>
                    <th style="text-align: center">Keterangan</th>
                    <th style="text-align: center">Pemesanan</th>
                    <th style="text-align: center">Action</th>
                  </tr>
                </tfoot>
              </table>
            </div>

            <br>
            
            <div class="form-group">
              <div class="table-responsive">
                <label>Daftar Bahan Produksi </label>
                  <table id="example3" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th style="text-align: center">No</th>
                        <th style="text-align: center">Bahan Kemas</th>
                        <th style="text-align: center">Jumlah</th>
                        <th style="text-align: center">Jumlah Sisa</th>
                        <th style="text-align: center">Jumlah Terpakai</th>
                        <th style="text-align: center">Sisa</th>
                        <th style="text-align: center">Selisih / Reject</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php 
                        $i_bahan = 1;
                        foreach ($bahan_kemas as $val_bahan) {
                      ?>
                        <tr>
                          <td style="text-align: center"><?php echo $i_bahan ?></td>
                          <td style="text-align: center"><?php echo $val_bahan->nama_bahan_kemas ?></td>
                          <td style="text-align: center"><?php echo $val_bahan->qty_selisih_detail_timeline_produksi ?></td>
                          <td style="text-align: center"><?php echo $val_bahan->qty_detail_timeline_produksi ?></td>
                          <td style="text-align: center"><?php echo $val_bahan->terpakai_detail_timeline_produksi ?></td>
                          <td style="text-align: center"><?php echo $val_bahan->sisa_detail_timeline_produksi ?></td>
                          <td style="text-align: center"><?php echo $val_bahan->selisih_detail_timeline_produksi ?></td>
                        </tr>
                      <?php 
                          $i_bahan++;
                        }
                      ?>
                    </tbody>

                    <tfoot>
                      <tr>
                        <th style="text-align: center">No</th>
                        <th style="text-align: center">Bahan Kemas</th>
                        <th style="text-align: center">Jumlah</th>
                        <th style="text-align: center">Jumlah Sisa</th>
                        <th style="text-align: center">Jumlah Terpakai</th>
                        <th style="text-align: center">Sisa</th>
                        <th style="text-align: center">Selisih / Reject</th>
                      </tr>
                    </tfoot>
                  </table>
              </div>
            </div>
          </div>
          <?php 
            if ($timeline->status_timeline_produksi == 0) {
          ?>  
          <div class="box-footer">
            <!-- <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button> -->
            <a href="<?php echo base_url('admin/timeline_produksi/history_proses/'.base64_encode($timeline->no_timeline_produksi)) ?>" onclick="return confirm('Are you sure?');" class="btn btn-success"><i class="fa fa-save"></i> Save</a>
            <a href="<?php echo base_url('admin/timeline_produksi/timeline') ?>" class="btn btn-primary"><i class="fa fa-table"></i> Kembali ke Data</a>
            <!-- <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button> -->
          </div>
          <?php 
            }else{
          ?>
          <div class="box-footer">
            <!-- <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button> -->
            <a href="<?php echo base_url('admin/timeline_produksi/timeline') ?>" class="btn btn-primary"><i class="fa fa-table"></i> Kembali ke Data</a>
            <!-- <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button> -->
          </div>
          <?php 
            }
          ?>
      </div>
      <?php 
        // echo form_close();
      ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  
  <script type="text/javascript">
    $(document).ready( function () {
      $('#datatable').DataTable();
      $('#example3').DataTable();
    });
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    // $('#example3').DataTable({
    //   "paging": false,
    //   "lengthChange": false,
    //   "searching": false,
    //   "ordering": false,
    //   "info": false,
    //   "autoWidth": false
    // });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
