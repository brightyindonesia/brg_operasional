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
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Request (*)</label>
                    <?php echo form_input($nomor_request, $request->no_request) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $request->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, $request->id_kategori_po, $kategori) ?>
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
              <?php 
                    // $provinsi = provinsi();
                    // foreach ($provinsi as $indeks => $value) {
                    //   echo $indeks."<br>";

                    // }

                    // $kabupaten = kabupaten("Aceh");
                    // foreach ($kabupaten as $indeks) {
                    //   echo $indeks."<br>";
                    // }
              ?>
            </div>

            <!-- FORM PRODUK TAMBAH -->
            <div class="row">
              <div class="col-sm-12">
                 <div class="form-group"><label>Pilih Bahan Kemas (*)</label>
                    <?php echo form_dropdown('bahan_kemas', $get_all_bahan_kemas, '', $bahan_kemas) ?>
                  </div>
              </div>
            </div>
            <?php echo form_input($id, $request->no_request) ?>
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
              <input type="hidden" name="id_bahan_kemas" id="in_id" value="" class="form-control">
              <input type="hidden" name="sku_bahan_kemas" id="in_sku" value="" class="form-control">

              <div class="col-sm-3">
                <div class="form-group">
                  <label>Nama Bahan Kemas</label>
                  <input type="text" readonly name="nama_bahan_kemas" id="in_bahan_kemas" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-1">
                <div class="form-group">
                  <label>Qty</label>
                  <input type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" name="qty" oninput="cal();" id="in_qty" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Harga</label>
                  <input type="text" name="harga" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" id="in_harga"  oninput="cal();" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Jumlah</label>
                  <input type="text" readonly name="jumlah" id="in_jumlah" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-1">
                <div class="form-group">
                  <label>Diskon (%)</label>
                  <input type="text" name="diskon" id="in_diskon" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-1">
                <div class="form-group">
                  <label>Pajak (%)</label>
                  <input type="text" name="pajak" id="in_pajak" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Tambah</label>
                  <button type="button" id="add_bahan_kemas" class="btn btn-success btn-sm form-control">
                    <i class="fa fa-plus"></i>                  
                  </button>
                </div>
              </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Daftar Request Bahan Kemas </label>
                      <table id="example3" class="table table-bordered table-striped">
                      <thead>
                        <tr align="center">
                          <th width="15%">Kode SKU</th>
                          <th>Nama Bahan</th>
                          <th width="5%">Qty</th>
                          <th>Harga</th>
                          <th>Jumlah</th>
                          <th>Diskon (%)</th>
                          <th>Pajak (%)</th>
                          <th width="1%">Aksi</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php 
                          foreach ($daftar_bahan_kemas as $row) {
                        ?>
                        <tr>
                          <td>
                            <input type="hidden" id="dt_id[]" name="dt_id[]" value="<?php echo $row->id_bahan_kemas; ?>">
                            <?php echo $row->kode_sku_bahan_kemas; ?>
                          </td>
                          <td>
                            <?php echo $row->nama_bahan_kemas; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="<?php echo $row->kuantitas_request; ?>">
                            <?php echo $row->kuantitas_request; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_harga[]" name="dt_harga[]" value="<?php echo $row->harga_request; ?>">
                            <?php echo $row->harga_request; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_jumlah[]" name="dt_jumlah[]" value="<?php echo $row->kuantitas_request * $row->harga_request; ?>">
                            <?php echo $row->kuantitas_request * $row->harga_request; ?>
                          </td>

                          <td>
                            <input type="hidden" id="dt_diskon[]" name="dt_diskon[]" value="<?php echo $row->diskon_request; ?>">
                            <?php echo $row->diskon_request; ?>
                          </td>

                          <td>
                            <input type="hidden" id="dt_pajak[]" name="dt_pajak[]" value="<?php echo $row->pajak_request; ?>">
                            <?php echo $row->pajak_request; ?>
                          </td>
                          <td>
                            <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button>
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
                          <th>Aksi</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
          </div>
          <div class="box-footer">
            <button type="submit" id="request-ubah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
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
    // submit form masuk
    $('#request-ubah').click(function(e){
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
      if (vendor == '' && penerima == '' && sku == '' && dt_id == '' && dt_qty == '' && dt_harga == '' && dt_jumlah == '' && dt_diskon == '' && dt_pajak == '') {
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
        $.ajax({ 
          url:"<?php echo base_url()?>admin/masuk/request_edit_proses",
          method:"post",
          dataType: 'JSON', 
          data:{vendor:vendor, penerima:penerima, sku: sku, ongkir: ongkir, remarks: remarks, nomor_request: nomor_request, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_diskon: JS_diskon, dt_pajak: JS_pajak, length: panjangArray, [csrfName]: csrfHash},
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
                window.location.replace("<?php echo base_url()?>admin/masuk/request");
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

    function cal() {
      var qty = document.getElementById('in_qty').value; 
      var harga = document.getElementById('in_harga').value;
      var result = document.getElementById('in_jumlah');
      var myResult = qty * harga;
      result.value = myResult;
    }

    // function val_qty() {
    //   var myBox1 = document.getElementById('in_qty').value; 
    //   var myBox2 = document.getElementById('in_stok').value;
    //   var result = myBox2 - myBox1;
    //   if (result < 0)
    //   {
    //     document.getElementById("hasil_val").style.display = "block";
    //     document.getElementById("in_qty").style.backgroundColor = "red";
    //     document.getElementById("in_qty").style.color = "#fff";
    //     document.getElementById("hasil_val").innerHTML = "<div class='alert alert-danger'>Stok hanya tersedia " + myBox2 + "</div>";
    //     // $("#submit_form_keluar").prop("disabled",true);
    //     $("#add_produk").prop("disabled",true);
    //   }else{
    //     document.getElementById("hasil_val").style.display = "none";
    //     document.getElementById("hasil_val").innerHTML = "";
    //     document.getElementById("in_qty").style.backgroundColor = "";
    //     document.getElementById("in_qty").style.color = "";
    //     // $("#submit_form_keluar").prop("disabled",false);
    //     $("#add_produk").prop("disabled",false);
    //   }
    // }

    $(document).ready(function(){
        // barang masuk row
        $('#add_bahan_kemas').click(function(){
          var id_bahan_kemas = $('#in_id').val();
          var kode_sku = $('#in_sku').val();
          var hpp  = $('#in_hpp').val();
          var nama_bahan_kemas = $('#in_bahan_kemas').val();
          var qty = $('#in_qty').val();
          var harga = $('#in_harga').val();
          var jumlah = $('#in_jumlah').val();
          var diskon = $('#in_diskon').val();
          var pajak = $('#in_pajak').val();

          var html = '<tr>';
          html += '<td> <input type="hidden" id="dt_id[]" name="dt_id[]" value="'+id_bahan_kemas+'">'+kode_sku+'</td>'; 
          html += '<td> '+nama_bahan_kemas+' </td>'; 
          html += '<td> <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="'+qty+'">'+qty+'</td>';
          html += '<td> <input type="hidden" id="dt_harga[]" name="dt_harga[]" value="'+harga+'">'+harga+'</td>';
          html += ' <td> <input type="hidden" id="dt_jumlah[]" name="dt_jumlah[]" value="'+jumlah+'">'+jumlah+'</td>';
          html += '<td> <input type="hidden" id="dt_hpp[]" name="dt_diskon[]" value="'+diskon+'">'+diskon+' %</td>';
          html += '<td> <input type="hidden" id="dt_hpp[]" name="dt_pajak[]" value="'+pajak+'">'+pajak+' %</td>';
          html +=  '<td> <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> </tr>';
          
          $('tbody').append(html);

          // clear input data
          $('#in_id').val('');
          $('#in_sku').val('');
          $('#in_bahan_kemas').val('');
          $('#in_harga').val('');
          $('#in_qty').val('');
          $('#in_jumlah').val('');
          $('#in_diskon').val('');
          $('#in_pajak').val('');
          
          $("#bahan-kemas").val("").trigger("change.select2");
        });

        $(document).on('click', '#hps_row', function(){
            $(this).closest('tr').remove();
        });

        $('#vendor').on('change', function(){
          var vendor = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (vendor != '') {
            // console.log(provinsi);
            $.ajax({
              url: "<?php echo base_url()?>admin/masuk/get_id_vendor",
              type: "post",
              data: {'vendor': vendor, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                $('#bahan-kemas').html(data);
                $("#vendor option[value='']").remove();
                $("#example3").find('tbody').empty(); //add this line
              },
              error: function(){
                alert('Error ....');
              }
            });
          }else{
            $('#bahan-kemas').html('<option value="">- Pilih Bahan Kemas -</option>');
          }
         });

        $('#bahan-kemas').on('change', function(){
          var bahan_kemas = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (bahan_kemas != '') {
            $.ajax({
              url: "<?php echo base_url()?>admin/masuk/get_id_bahan_kemas",
              type: "post",
              data: {'bahan_kemas': bahan_kemas, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                document.getElementById("in_id").value = data.id_bahan_kemas;
                document.getElementById("in_sku").value = data.kode_sku_bahan_kemas;
                document.getElementById("in_bahan_kemas").value = data.nama_bahan_kemas;
                document.getElementById("in_qty").value = 0;
                document.getElementById("in_harga").value = 0;
                document.getElementById("in_jumlah").value = 0;
                document.getElementById("in_diskon").value = 0;
                document.getElementById("in_pajak").value = 0;
              },
              error: function(){
                alert('Error ....');
              }
            });
          }else{
            // clear input data
            $('#in_id').val('');
            $('#in_bahan_kemas').val('');
            $('#in_harga').val('');
            $('#in_qty').val('');
            $('#in_sku').val('');
            $('#in_jumlah').val('');
            $('#in_stok').val('');
            $('#in_diskon').val('');
            $('#in_pajak').val('');
          }
         });
    }); 

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })

     // $('input[type="checkbox"].minimal').on('ifClicked', function() {
     //      var checked=$(this).is(':checked');
     //      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
     //          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
     //      if(checked){
     //            $('#nomor-request').val('');
     //      }else{
     //          $.ajax({
     //          url: "<?php echo base_url()?>admin/masuk/generate_nomor",
     //          type: "post",
     //          data: {[csrfName]: csrfHash},
     //          dataType: 'JSON',
     //          success: function(data){
     //            $('#nomor-request').val(data);
     //          },
     //          error: function(){
     //            alert('Error ....');
     //          }
     //        });
     //      }
     //  });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
