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
          <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <?php 
                  echo form_open($action);
                ?>
                <div class="card-body">
                  <table width="100%" border="0" class="table table-bordered table-responsive">
                    <tr>
                      <td width="20%" style="background-color: #f5f5f5;font-weight: bold;">Nomor Retur</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->nomor_retur ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td width="20%" style="background-color: #f5f5f5;font-weight: bold;">Nomor Pesanan</td>
                      <td width="1%">:</td>
                      <td>
                        <?php  echo form_input($nomor_retur, $retur->nomor_retur); ?>
                        <div>
                          <?php echo $retur->nomor_pesanan ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nomor Resi</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->nomor_resi ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nama Kurir</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->nama_kurir ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nama Toko</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->nama_toko ?>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <hr width="100%">
                  <table width="100%" border="0" class="table table-bordered table-responsive">
                    <tr>
                      <td width="20%" style="background-color: #f5f5f5;font-weight: bold;">Nama Penerima</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->nama_penerima ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Alamat Penerima</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->alamat_penerima ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Kabupaten</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->kabupaten ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Provinsi</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->provinsi ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nomor Handphone</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo $retur->hp_penerima ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Masukan Keterangan Retur</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo form_textarea($keterangan, $retur->keterangan_retur) ?>
                        </div>
                      </td>
                    </tr>
                  </table>

                  <div class="table-responsive">
                    <table id="table-resi-retur" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th style="text-align: center">No.</th>
                          <th style="text-align: center">Nama Produk</th>
                          <th style="text-align: center">Qty</th>
                          <th width="5%" style="text-align: center">Keterangan</th>
                          <th width="5%" align="center" >Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php
                          $i = 1; 
                          foreach ($fix as $val) {
                        ?>
                        <tr>
                          <td style="text-align: center"><?php echo $i ?></td>
                          <td style="text-align: center"><?php echo $val->nama_produk ?></td>
                          <td style="text-align: center">
                            <input type="button" class="btn-increment-decrement" value="-" onclick="decrement_quantity(<?php echo $val->id_produk ?>)"> &nbsp;
                            <input type="hidden" id="input-quantity-max-<?php echo $val->id_produk ?>" class="input-quantity" value="<?php echo $val->qty ?>" style="width: 60px;height: 23px;padding-left: 23px;">
                            <input type="hidden" name="produk[]" class="input-quantity" value="<?php echo $val->id_produk ?>" style="width: 60px;height: 23px;padding-left: 23px;">
                            <input type="text" id="input-quantity-<?php echo $val->id_produk ?>" name="qty[]" oninput="checkValue(this);" min="1" max="<?php echo $val->qty ?>" class="input-quantity" value="<?php echo $val->qty ?>" style="width: 60px;height: 23px;padding-left: 23px;"> &nbsp;
                            <input type="button" class="btn-increment-decrement" value="+" onclick="increment_quantity(<?php echo $val->id_produk ?>)"/> &nbsp;
                              
                          </td>
                          <td>
                            <textarea name="keterangan_produk[]" class="form-control"></textarea>
                          </td>
                          <td> 
                            <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> 
                          </td>
                        </tr>
                        <?php 
                            $i++;
                          }
                        ?>
                      </tbody>                      
                    </table>
                  </div>

                  <div class="box-footer">
                    <button type="submit" id="retur_tambah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
                    <a href="<?php echo base_url('admin/retur/data_retur') ?>" class="btn btn-primary"><i class="fa fa-table"></i> Kembali ke Data</a>
                  </div>
                  <?php 
                    echo form_close();
                  ?>
                </div>
              </div>
            </div>
          </div>
          
      </div>

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
    $('#table-resi-retur').DataTable();
    // this checks the value and updates it on the control, if needed
    function checkValue(sender) {
        let min = sender.min;
        let max = sender.max;
        let value = parseInt(sender.value);
        if (value>max) {
            sender.value = min;
        } else if (value<min) {
            sender.value = max;
        }
    }

    function increment_quantity(id) {
        var qty = document.getElementById('input-quantity-'+id).value
        var max = document.getElementById('input-quantity-max-'+id).value
        if (qty < max) {
          var newQuantity = parseInt(qty)+1;
          $('#input-quantity-'+id).val(newQuantity);
        }
    }

    function decrement_quantity(id) {
        var qty = document.getElementById('input-quantity-'+id).value
        if(qty > 1) 
        {
          var newQuantity = parseInt(qty) - 1;
          $('#input-quantity-'+id).val(newQuantity);
        }
    }

    $(document).on('click', '#hps_row', function(){
        $(this).closest('tr').remove();
    });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
