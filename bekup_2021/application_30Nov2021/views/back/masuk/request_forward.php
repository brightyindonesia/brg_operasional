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

        ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Request (*)</label>
                    <?php echo form_input($nomor_request, $request->no_request) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $request->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('sku', $get_all_kategori, $request->id_kategori_po, $kategori) ?>
                  </div>

                  <div class="form-group"><label>Harga Ongkos Kirim</label>
                    <?php echo form_input($ongkir) ?>
                  </div>
              </div>

              <div class="col-sm-6">
                  <div class="form-group"><label>Nama Vendor (*)</label>
                    <?php echo form_dropdown('vendor', $get_all_vendor, $request->id_vendor, $vendor) ?>
                  </div>

                  <div class="form-group"><label>Nama Penerima (*)</label>
                    <?php echo form_dropdown('penerima', $get_all_penerima, $request->id_penerima, $penerima) ?>
                  </div>

                  <div class="form-group"><label>Remarks</label>
                    <?php echo form_textarea($remarks, $request->remarks); ?>
                  </div>
              </div>
            </div>
            <?php echo form_input($id, $request->no_request) ?>
            <?php echo form_input($id_vendor, $request->id_vendor) ?>
            <?php echo form_input($id_sku, $request->id_sku) ?>
            <?php echo form_input($id_kategori, $request->id_kategori_po) ?>
            <?php echo form_input($id_penerima, $request->id_penerima) ?>
          </div>
          <!-- Input -->
          <div id="dataInput">
          </div>
          <div class="box-body">
            <div class="row">
              <div id="hasil_val" style="display: none;" class="col-sm-12">
              </div>              
            </div>
            <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Daftar Request Bahan Kemas </label>
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
                          $i = 0;
                          foreach ($daftar_bahan_kemas as $row) {
                        ?>
                        <tr class="multipp">
                          <td>
                            <input align="right" type="hidden" id="dt_id[<?php echo $i; ?>]" name="dt_id[]" value="<?php echo $row->id_bahan_kemas; ?>">
                            <?php echo $row->kode_sku_bahan_kemas; ?>
                          </td>
                          <td>
                            <?php echo $row->nama_bahan_kemas; ?>
                          </td>
                          <td>
                            <input size="5" style="text-align: center;" type="hidden" id="dt_qty[<?php echo $i; ?>]" name="dt_qty[]" value="<?php echo $row->kuantitas_request; ?>">
                            <?php echo $row->kuantitas_request; ?>
                          </td>
                          <td>
                            <input size="5" style="text-align: center;" type="text" id="dt_harga[<?php echo $i; ?>]" name="dt_harga[]" value="<?php echo $row->harga_request; ?>">
                          </td>

                          <td>
                            <input size="5" style="text-align: center;" type="hidden" id="dt_jumlah[<?php echo $i; ?>]" name="dt_jumlah[]" value="<?php echo $row->kuantitas_request * $row->harga_request; ?>">
                            <input disabled size="5" style="text-align: center;" type="text" id="dt_total[<?php echo $i; ?>]" name="dt_total[]" value="<?php echo $row->kuantitas_request * $row->harga_request; ?>">
                          </td>

                          <td>
                            <input size="5" style="text-align: center;" type="text" id="dt_diskon[<?php echo $i; ?>]" name="dt_diskon[]" value="<?php echo $row->diskon_request; ?>">
                          </td>

                          <td>
                            <input size="5" style="text-align: center;" type="text" id="dt_pajak[<?php echo $i; ?>]" name="dt_pajak[]" value="<?php echo $row->pajak_request; ?>">
                          </td>
                        </tr>
                        <?php
                            $i++;
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
          </div>
          <div class="box-footer">
            <button type="submit" id="request-forward" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          </div>
          <!-- /.box-body -->
      </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- Select2 -->

  <script type="text/javascript">
    function getTotal() {
      for (var i=0,n=document.querySelectorAll(".multipp").length; i<n;i++) {
        var qty = document.getElementById('dt_qty['+i+']').value;
        var harga = document.getElementById('dt_harga['+i+']').value;
        var subtotal = qty*harga;
        document.getElementById('dt_jumlah['+i+']').value=subtotal;
        document.getElementById('dt_total['+i+']').value=subtotal;
      }    
    }
    window.onload=function() {
      for (var i=0,n=document.querySelectorAll(".multipp").length; i<n;i++) {
        document.getElementById('dt_harga['+i+']').onkeyup=getTotal;
      }
      getTotal();
    }
    // submit form masuk
    $('#request-forward').click(function(e){
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

      var nomor_request = document.getElementById('nomor-request').value;
      var kategori = document.getElementById('kategori').value;
      var vendor = document.getElementById('vendor').value;
      var penerima = document.getElementById('penerima').value;
      var sku = document.getElementById('sku').value;
      var ongkir = document.getElementById('ongkir').value;
      var remarks = document.getElementById('remarks').value;
      var dt_id =  $("input[name='dt_id[]']")
            .map(function(){return $(this).val();}).get();
      var dt_qty =  $("input[name='dt_qty[]']")
            .map(function(){return $(this).val();}).get();
      var dt_harga =  $("input[name='dt_harga[]']")
            .map(function(){return $(this).val();}).get();
      var dt_jumlah =  $("input[name='dt_jumlah[]']")
            .map(function(){return $(this).val();}).get(); 
      var dt_diskon =  $("input[name='dt_diskon[]']")
            .map(function(){return $(this).val();}).get();        
      var dt_pajak =  $("input[name='dt_pajak[]']")
            .map(function(){return $(this).val();}).get();      
      var JS_id = JSON.stringify(dt_id);
      var JS_qty = JSON.stringify(dt_qty);
      var JS_harga = JSON.stringify(dt_harga);
      var JS_jumlah = JSON.stringify(dt_jumlah);
      var JS_diskon = JSON.stringify(dt_diskon);
      var JS_pajak = JSON.stringify(dt_pajak);
      var panjangArray = dt_id.length;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      if (vendor == '' && penerima == '' && kategori == '' && sku == '' && dt_id == '' && dt_qty == '' && dt_harga == '' && dt_jumlah == '' && dt_diskon == '' && dt_pajak == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else if(vendor == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Vendor harap dipilih!'
        });
      }else if(penerima == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Penerima harap dipilih!'
        });
      }else if(kategori == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Kategori harap dipilih!'
        });
      }else if(sku == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama SKU harap dipilih!'
        });
      }else if(nomor_request == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Pesanan harus diisi!'
        });
      }else if(dt_id == '' || dt_qty == '' || dt_harga == '' || dt_jumlah == ''){
        Toast.fire({
          icon: 'error',
          title: 'Daftar Request Bahan Kemas tidak berisi data!'
        });
      }else{
        var formData = new FormData();
        formData.append('vendor', vendor);
        formData.append('penerima', penerima);
        formData.append('sku', sku);
        formData.append('kategori', kategori);
        formData.append('ongkir', ongkir);
        formData.append('remarks', remarks);        
        formData.append('nomor_request', nomor_request);
        formData.append('dt_id', JS_id);
        formData.append('dt_qty', JS_qty);
        formData.append('dt_harga', JS_harga);
        formData.append('dt_jml', JS_jumlah);
        formData.append('dt_diskon', JS_diskon);
        formData.append('dt_pajak', JS_pajak);
        formData.append('length', panjangArray);
        formData.append([csrfName], csrfHash); 

        $.ajax({ 
          url:"<?php echo base_url()?>admin/masuk/request_forward_proses",
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
                window.location.replace("<?php echo base_url()?>admin/masuk/purchase");
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
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
