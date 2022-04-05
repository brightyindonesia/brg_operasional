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
          <div class="box-header">
            <a href="<?php echo base_url('admin/surat/surat_packing_detail_hapus_all/'.base64_encode($cek_surat->no_surat_packing)) ?> " onClick="return confirm('Are you sure?');" class="btn btn-md btn-danger"><i class="fa fa-trash" style="margin-right: 5px;"></i> Hapus Semua Data Detail Surat Packing</a>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Surat Packing (*)</label>
                    <?php echo form_input($nomor_surat_packing) ?>
                  </div>

                  <div class="form-group"><label>Nama Surat Packing (*)</label>
                    <?php echo form_input($nama_surat_packing, $cek_surat->nama_surat_packing) ?>
                  </div>

                  <div class="form-group"><label>Keterangan Surat Packing</label>
                    <?php echo form_textarea($keterangan, $cek_surat->keterangan_surat_packing) ?>
                  </div>
              </div>

              <div class="col-sm-6">
                  <div class="form-group"><label>Tanggal Surat Packing (*)</label>
                    <input type="text" name="periodik" class="form-control float-right" value="<?php echo date('Y/m/d', strtotime($cek_surat->tgl_surat_packing)) ?>" id="date">
                  </div>

                  <div class="form-group"><label>Kepada Penerima (*)</label>
                    <?php echo form_input($kepada_surat_packing, $cek_surat->kepada_surat_packing) ?>
                  </div>

                  <div class="form-group"><label>Pilih Nama Penerima (*)</label>
                    <?php echo form_dropdown('penerima', $get_all_penerima, $cek_surat->id_penerima, $penerima) ?>
                  </div>
              </div>
            </div>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-2">
                <div class="form-group">
                  <label>Kode Barang</label>
                  <input type="text" name="kode_barang" id="in_kode" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Nama Barang</label>
                  <input type="text" name="nama_barang" id="in_nama" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-1">
                <div class="form-group">
                  <label>Jumlah</label>
                  <input type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" name="qty" id="in_qty" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Satuan Barang</label>
                  <input type="text" name="satuan_barang" id="in_satuan" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <label>Keterangan</label>
                  <input type="text" name="keterangan" id="in_keterangan" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Tambah</label>
                  <button type="button" id="add_produk" class="btn btn-success btn-sm form-control">
                    <i class="fa fa-plus"></i>                  
                  </button>
                </div>
              </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Daftar Barang</label>
                      <table id="example3" class="table table-bordered table-striped">
                      <thead>
                        <tr align="center">
                          <th width="15%">Kode Barang</th>
                          <th>Nama Barang</th>
                          <th>Jumlah</th>
                          <th>Satuan Barang</th>
                          <th>Keterangan</th>
                          <th width="1%">Aksi</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php 
                          foreach ($barang as $val_bar) {
                        ?>
                        <tr>
                            <td> <input type="hidden" id="dt_kode[]" name="dt_kode[]" value="<?php echo $val_bar->kode_barang_surat_packing ?>"><?php echo $val_bar->kode_barang_surat_packing ?></td>
                            <td> <input type="hidden" id="dt_nama[]" name="dt_nama[]" value="<?php echo $val_bar->nama_barang_surat_packing ?>"><?php echo $val_bar->nama_barang_surat_packing ?></td> 
                            <td> <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="<?php echo $val_bar->jumlah_barang_surat_packing ?>"><?php echo $val_bar->jumlah_barang_surat_packing ?></td>
                            <td> <input type="hidden" id="dt_satuan[]" name="dt_satuan[]" value="<?php echo $val_bar->satuan_barang_surat_packing ?>"><?php echo $val_bar->satuan_barang_surat_packing ?></td>
                            <td> <input type="hidden" id="dt_keterangan[]" name="dt_keterangan[]" value="<?php echo $val_bar->keterangan_barang_surat_packing ?>"><?php echo $val_bar->keterangan_barang_surat_packing ?></td>
                            <td> <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> 
                        </tr>
                        <?php
                          }
                        ?>
                      </tbody>

                      <tfoot>
                        <tr align="center">
                          <th>Kode Barang</th>
                          <th>Nama Barang</th>
                          <th>Jumlah</th>
                          <th>Satuan Barang</th>
                          <th>Keterangan</th>
                          <th>Aksi</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
          </div>
          <div class="box-footer">
            <button type="submit" id="surat_packing_add" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          </div>
      </div>
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

    $('#surat_packing_add').click(function(e){
      const Toast = Swal.mixin({
        toast: false,
        position: 'center',
        showConfirmButton: false,
        // confirmButtonColor: '#86ccca',
        timer: 3000,
        timerProgressBar: false,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      e.preventDefault();

      var nomor_surat = document.getElementById('nomor-surat-packing').value;
      var nama_surat = document.getElementById('nama-surat-packing').value;
      var date = document.getElementById('date').value;
      var kepada_surat = document.getElementById('kepada-surat-packing').value;
      var keterangan = document.getElementById('keterangan').value;
      var penerima = document.getElementById('penerima').value;
      var dt_kode =  $("input[name='dt_kode[]']")
            .map(function(){return $(this).val();}).get();
      var dt_nama =  $("input[name='dt_nama[]']")
            .map(function(){return $(this).val();}).get();
      var dt_qty =  $("input[name='dt_qty[]']")
            .map(function(){return $(this).val();}).get();
      var dt_satuan =  $("input[name='dt_satuan[]']")
            .map(function(){return $(this).val();}).get(); 
      var dt_keterangan =  $("input[name='dt_keterangan[]']")
            .map(function(){return $(this).val();}).get();            
      var JS_kode = JSON.stringify(dt_kode);
      var JS_nama = JSON.stringify(dt_nama);
      var JS_qty = JSON.stringify(dt_qty);
      var JS_satuan = JSON.stringify(dt_satuan);
      var JS_keterangan = JSON.stringify(dt_keterangan);
      var panjangArray = dt_kode.length;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      if (nomor_surat == '' && nama_surat == '' && kepada_surat == '' && keterangan == '' && penerima == '' && dt_kode == '' && dt_nama == '' && dt_qty == '' && dt_satuan == '' && dt_keterangan == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else if(nomor_surat == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Surat harap diisi!'
        });
      }else if(nama_surat == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Surat harap diisi!'
        });
      }else if(kepada_surat == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Kepada Surat harap diisi!'
        });
      }else if(penerima == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Penerima harap dipilih!'
        });
      }else if(dt_kode == '' || dt_nama == '' || dt_qty == '' || dt_satuan == ''){
        Toast.fire({
          icon: 'error',
          title: 'Daftar Barang tidak berisi data!'
        });
      }else{
        $.ajax({ 
          url:"<?php echo base_url()?>admin/surat/proses_surat_packing_ubah",
          method:"post",
          dataType: 'JSON', 
          data:{nomor_surat: nomor_surat, nama_surat: nama_surat, kepada_surat: kepada_surat, keterangan: keterangan, penerima: penerima, date:date, dt_kode: JS_kode, dt_nama: JS_nama, dt_qty: JS_qty, dt_satuan: JS_satuan, dt_keterangan: JS_keterangan, length: panjangArray, [csrfName]: csrfHash},
          success:function(data)  {  
            // alert(data);
            if (data.validasi) {
              Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: data.validasi
              })
            }

            if (data.sukses) {
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/surat/surat_packing_ubah/"+data.nomor);
              });
            }
            
          },
          error: function(data){
            console.log(data.responseText);
            // Toast.fire({
            //   type: 'warning',
            //   title: 'Perhatian!',
            //   text: data.responseText
            // });

          } 
        });
      }
    });

    // barang masuk row
    $('#add_produk').click(function(){
      const Toast = Swal.mixin({
        toast: false,
        position: 'center',
        showConfirmButton: false,
        // confirmButtonColor: '#86ccca',
        timer: 3000,
        timerProgressBar: false,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      var kode = $('#in_kode').val();
      var nama = $('#in_nama').val();
      var qty  = $('#in_qty').val();
      var satuan = $('#in_satuan').val();
      var keterangan = $('#in_keterangan').val();

      if (kode == '' || nama == '' || qty == '' || satuan == '') {
        Toast.fire({
          icon: 'error',
          title: 'Perhatian!',
          text: 'Barang masih kosong!. Silahkan diisi!'
        })
      }else if(qty == 0){
        Toast.fire({
          icon: 'error',
          title: 'Perhatian!',
          text: 'Qty Produk masih kosong!. Silahkan diisi!'
        })
      }else{
        var html = '<tr>';
        html += '<td> <input type="hidden" id="dt_kode[]" name="dt_kode[]" value="'+kode+'">'+kode+'</td>'; 
        html += '<td> <input type="hidden" id="dt_nama[]" name="dt_nama[]" value="'+nama+'"> '+nama+' </td>'; 
        html += '<td> <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="'+qty+'">'+qty+'</td>';
        html += '<td> <input type="hidden" id="dt_satuan[]" name="dt_satuan[]" value="'+satuan+'">'+satuan+'</td>';
        html += ' <td> <input type="hidden" id="dt_keterangan[]" name="dt_keterangan[]" value="'+keterangan+'">'+keterangan+'</td>';
        html +=  '<td> <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> </tr>';
        
        $('tbody').append(html);

        // clear input data
        $('#in_kode').val('');
        $('#in_nama').val('');
        $('#in_satuan').val('');
        $('#in_keterangan').val('');
        $('#in_qty').val('');
      }
    });

    //Initialize Select2 Elements
    $(document).ready( function () {
      $("#date").datepicker({
        format: "yyyy/mm/dd"
      });
    });

    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
