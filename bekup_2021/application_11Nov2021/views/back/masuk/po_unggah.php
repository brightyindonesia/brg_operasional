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
              <div class="form-group"><label>Tanggal Bukti Transfer PO (*)</label>
                <input type="text" class="form-control" id="tgl_bukti">
              </div>
              
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group"><label>Keterangan Bukti Transfer PO (*)</label>
                <?php echo form_textarea($keterangan_unggah, ''); ?>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group"><label>Upload Bukti Transfer (*)</label>
                <input type="file" name="photo" id="photo" onchange="photoPreview(this,'preview')"/>
                <p class="help-block">Maximum file size is 2Mb</p>
                <b>Photo Preview</b><br>
                <img id="preview" width="350px"/>
              </div>
            </div>
          </div>
        </div>

        <div class="box-footer">
          <button type="submit" id="po-unggah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
          <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          <a href="<?php echo base_url('admin/masuk/purchase') ?>" class="btn btn-primary"><i class="fa fa-table" style="margin-right: 5px"></i> Kembali ke Data</a>
        </div>
      </div>

      <div class="box box-primary">
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Purchase Order (*)</label>
                    <?php echo form_input($nomor_po, $po->no_po) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $po->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('sku', $get_all_kategori, $po->id_kategori_po, $kategori) ?>
                  </div>

                  <div class="form-group"><label>Harga Ongkos Kirim</label>
                    <?php echo form_input($ongkir) ?>
                  </div>
              </div>

              <div class="col-sm-6">
                  <div class="form-group"><label>Nama Vendor (*)</label>
                    <?php echo form_dropdown('vendor', $get_all_vendor, $po->id_vendor, $vendor) ?>
                  </div>

                  <div class="form-group"><label>Nama Penerima (*)</label>
                    <?php echo form_dropdown('penerima', $get_all_penerima, $po->id_penerima, $penerima) ?>
                  </div>

                  <div class="form-group"><label>Remarks</label>
                    <?php echo form_textarea($remarks, $po->remarks_po); ?>
                  </div>
              </div>
            </div>
            <?php echo form_input($id, $po->no_po) ?>
            <?php echo form_input($id_vendor, $po->id_vendor) ?>
            <?php echo form_input($id_sku, $po->id_sku) ?>
            <?php echo form_input($id_kategori, $po->id_kategori_po) ?>
            <?php echo form_input($id_penerima, $po->id_penerima) ?>
          </div>
          <!-- Input -->
          <div id="dataInput">
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Daftar PO Bahan Kemas </label>
                    <table id="example3" class="table table-bordered table-striped">
                    <thead>
                      <tr align="center">
                        <th width="10%">Kode SKU</th>
                        <th width="35%">Nama Bahan</th>
                        <th width="5%">Qty</th>
                        <th width="5%">Harga</th>
                        <th width="5%">Jumlah</th>
                        <th width="5%">Diskon (%)</th>
                        <th width="5%">Pajak (%)</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php 
                        foreach ($daftar_bahan_kemas as $row) {
                      ?>
                      <tr class="multipp">
                        <td>
                          <?php echo $row->kode_sku_bahan_kemas; ?>
                        </td>
                        <td>
                          <?php echo $row->nama_bahan_kemas; ?>
                        </td>
                        <td>
                          <?php echo $row->kuantitas_po; ?>
                        </td>
                        <td>
                          <?php echo $row->harga_po; ?>
                        </td>
                        <td>
                          <?php echo $row->harga_po * $row->kuantitas_po; ?>
                        </td>

                        <td>
                          <?php echo $row->diskon_po; ?>
                        </td>

                        <td>
                          <?php echo $row->pajak_po; ?>
                        </td>
                      </tr>
                      <?php
                        }
                      ?>
                    </tbody>

                    <tfoot>
                      <tr align="center">
                        <th>Kode SKU</th>
                        <th>Nama Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Diskon (%)</th>
                        <th>Pajak (%)</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label>Daftar Bukti Unggah TF PO </label>
                    <table id="example2" class="table table-bordered table-striped">
                    <thead>
                      <tr align="center">
                        <th width="10%">Tanggal</th>
                        <th width="35%">Nomor PO</th>
                        <th>Keterangan Bukti TF PO</th>
                        <th width="8%">Action</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php
                        foreach ($daftar_unggah_po as $val_unggah) {
                      ?>
                        <tr>
                          <td>
                            <?php echo date('d-m-Y', strtotime($val_unggah->tgl_bukti_tf_po)); ?>
                          </td>
                          <td>
                            <?php echo $val_unggah->no_po; ?>
                          </td>
                          <td>
                            <?php echo $val_unggah->keterangan_bukti_tf_po; ?>
                          </td>
                          <td>
                            <a href="<?php echo base_url('admin/masuk/img_blob/'.base64_encode($val_unggah->no_po)) ?>" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-search"></i></a>
                            <a href="<?php echo base_url('admin/masuk/unggah_delete/'.base64_encode($val_unggah->id_bukti_tf_po)) ?>" onClick="return confirm('Are you sure?');" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i></a>
                          </td>
                        </tr>
                      <?php
                        }
                      ?>
                    </tbody>

                    <tfoot>
                      <tr align="center">
                        <th>Tanggal</th>
                        <th>Nomor PO</th>
                        <th>Keterangan Bukti TF PO</th>
                        <th>Action</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <!-- bootstrap datepicker -->
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript">
    $(document).ready( function () {
      $("#tgl_bukti").datepicker({
        format: "yyyy-mm-dd"
      }).datepicker("setDate", new Date());
    });

    function photoPreview(photo,idpreview)
    {
      var gb = photo.files;
      for (var i = 0; i < gb.length; i++)
      {
        var gbPreview = gb[i];
        var imageType = /image.*/;
        var preview=document.getElementById(idpreview);
        var reader = new FileReader();
        if (gbPreview.type.match(imageType))
        {
          //jika tipe data sesuai
          preview.file = gbPreview;
          reader.onload = (function(element)
          {
            return function(e)
            {
              element.src = e.target.result;
            };
          })(preview);
          //membaca data URL gambar
          reader.readAsDataURL(gbPreview);
        }else{
            //jika tipe data tidak sesuai
            alert("Tipe file tidak sesuai. Gambar harus bertipe .png, .gif atau .jpg.");
          }
      }
    }
    // submit form masuk
    $('#po-unggah').click(function(e){
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

      var image = document.getElementById('photo');
      var nomor_po = document.getElementById('nomor-po').value;
      var tanggal = document.getElementById('tgl_bukti').value;
      var keterangan = document.getElementById('keterangan-unggah').value;
      var JS_image = JSON.stringify(image.files[0]);
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      if (vendor == '' && penerima == '' && kategori == '' && sku == '' && dt_id == '' && dt_qty == '' && dt_harga == '' && dt_jumlah == '' && dt_diskon == '' && dt_pajak == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else if(nomor_po == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Pesanan harus diisi!'
        });
      }else if(image.files['length'] == 0){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Bukti Transfer harus diisi!'
        });
      }else if(keterangan == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Keterangan Bukti Transfer harus diisi!'
        });
      }else{
        var formData = new FormData();
        formData.append('photo', $('#photo')[0].files[0]);      
        formData.append('nomor_po', nomor_po);
        formData.append('tanggal', tanggal);
        formData.append('keterangan', keterangan);
        formData.append([csrfName], csrfHash); 

        $.ajax({ 
          url:"<?php echo base_url()?>admin/masuk/proses_purchase_unggah_bukti",
          method:"post",
          dataType: 'JSON', 
          // data:{img: JS_image,vendor:vendor, penerima:penerima, sku: sku, kategori: kategori, ongkir: ongkir, remarks: remarks, nomor_request: nomor_request, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_diskon: JS_diskon, dt_pajak: JS_pajak, length: panjangArray, [csrfName]: csrfHash},
          data: formData,
          contentType: false,
          processData: false,
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
                window.location.replace("<?php echo base_url()?>admin/masuk/purchase_unggah_bukti/"+data.no_po);
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

    $('#example2').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": false,
      "info": false,
      "autoWidth": true
    });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
