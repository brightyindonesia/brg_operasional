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
                  <div class="form-group"><label>Nomor Produksi (*)</label>
                    <?php echo form_input($nomor_request, str_replace("PO","TML",$produksi->no_po)) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $produksi->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, '', $kategori) ?>
                  </div>
              </div>

              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Purchase Order (*)</label>
                    <?php echo form_dropdown('list_po', '', '', $list_po) ?>
                  </div>
              </div>
            </div>

            <!-- FORM PRODUK TAMBAH -->
            <div class="row">
              <div class="col-sm-12">
                 <div class="form-group"><label>Pilih Bahan Kemas (*)</label>
                    <?php echo form_dropdown('bahan_kemas','', '', $bahan_kemas) ?>
                  </div>
              </div>
            </div>
            <?php echo form_input($id, str_replace("PO","TML",$produksi->no_po)) ?>
            <?php echo form_input($id_po, $produksi->no_po) ?>
            <?php echo form_input($id_sku, $produksi->id_sku) ?>
            <?php echo form_input($qty_produksi, $produksi->total_kuantitas_po) ?>
          </div>

          <div class="box-body">
            <div class="row">
              <div id="hasil_val" style="display: none;" class="col-sm-12">
              </div>              
            </div>
            <div class="row">
              <input type="hidden" id="in_po" value="" class="form-control">
              <input type="hidden" id="in_stok" value="" class="form-control">
              <input type="hidden" id="in_id" value="" class="form-control">
              <input type="hidden" id="in_sku" value="" class="form-control">

              <div class="col-sm-5">
                <div class="form-group">
                  <label>Nama Bahan Kemas</label>
                  <input type="text" readonly name="nama_bahan_kemas" id="in_bahan_kemas" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-1">
                <div class="form-group">
                  <label>Qty</label>
                  <input type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" name="qty" oninput="cal();val_qty();" id="in_qty" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Harga</label>
                  <input type="text" name="harga" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" id="in_harga" readonly value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Jumlah</label>
                  <input type="text" readonly name="jumlah" id="in_jumlah" value="" class="form-control">
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
                    <label>Daftar Bahan Kemas Purchase Order </label>
                      <table id="example3" class="table table-bordered table-striped">
                      <thead>
                        <tr align="center">
                          <th width="15%">Kode SKU</th>
                          <th>Nama Bahan</th>
                          <th width="5%">Qty</th>
                          <th>Harga</th>
                          <th>Jumlah</th>
                          <th width="1%">Aksi</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php 
                          foreach ($daftar_bahan_kemas as $row) {
                        ?>
                        <tr>
                          <td>
                            <input type="hidden" id="dt_po[]" name="dt_po[]" value="<?php echo $row->no_po; ?>">
                            <input type="hidden" id="dt_id[]" name="dt_id[]" value="<?php echo $row->id_bahan_kemas; ?>">
                            <?php echo $row->kode_sku_bahan_kemas; ?>
                          </td>
                          <td>
                            <?php echo $row->nama_bahan_kemas; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="<?php echo $row->kuantitas_po; ?>">
                            <?php echo $row->kuantitas_po; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_harga[]" name="dt_harga[]" value="<?php echo $row->harga_po; ?>">
                            <?php echo $row->harga_po; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_jumlah[]" name="dt_jumlah[]" value="<?php echo $row->kuantitas_po * $row->harga_po; ?>">
                            <?php echo $row->kuantitas_po * $row->harga_po; ?>
                          </td>
                          <td>
                            &nbsp;
                            <!-- <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> -->
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
                          <th>Aksi</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
          </div>

          <div class="box-footer">
            <button type="submit" id="produksi_add" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          </div>
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
      var nomor_produksi = document.getElementById('nomor-produksi').value;
      var qty_produksi = document.getElementById('qty_produksi').value;
      var sku = document.getElementById('sku').value;
      var dt_po =  $("input[name='dt_po[]']")
            .map(function(){return $(this).val();}).get();
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
      var JS_po = JSON.stringify(dt_po);
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
      if (dt_po == '' && dt_id == '' && dt_qty == '' && dt_harga == '' && dt_jumlah == '' && dt_diskon == '' && dt_pajak == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else{
        $.ajax({ 
          url:"<?php echo base_url()?>admin/produksi/proses_produksi_add",
          method:"post",
          dataType: 'JSON', 
          data:{nomor_produksi:nomor_produksi, nomor_request: nomor_request, qty_prod: qty_produksi, sku: sku, dt_po: JS_po, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_diskon: JS_diskon, dt_pajak: JS_pajak, length: panjangArray, [csrfName]: csrfHash},
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
                window.location.replace("<?php echo base_url()?>admin/produksi/daftar");
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

    function val_qty() {
      var myBox1 = document.getElementById('in_qty').value; 
      var myBox2 = document.getElementById('in_stok').value;
      var result = myBox2 - myBox1;
      if (result < 0)
      {
        document.getElementById("hasil_val").style.display = "block";
        document.getElementById("in_qty").style.backgroundColor = "red";
        document.getElementById("in_qty").style.color = "#fff";
        document.getElementById("hasil_val").innerHTML = "<div class='alert alert-danger'>Stok hanya tersedia " + myBox2 + "</div>";
        // $("#submit_form_keluar").prop("disabled",true);
        $("#add_bahan_kemas").prop("disabled",true);
      }else{
        document.getElementById("hasil_val").style.display = "none";
        document.getElementById("hasil_val").innerHTML = "";
        document.getElementById("in_qty").style.backgroundColor = "";
        document.getElementById("in_qty").style.color = "";
        // $("#submit_form_keluar").prop("disabled",false);
        $("#add_bahan_kemas").prop("disabled",false);
      }
    }

    $(document).ready(function(){
        $(document).on('click', '#hps_row', function(){
            $(this).closest('tr').remove();
        });

        $('#kategori').on('change', function(){
          var kategori = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (kategori != '') {
            // console.log(provinsi);
            $.ajax({
              url: "<?php echo base_url()?>admin/produksi/get_id_kategori",
              type: "post",
              data: {'kategori': kategori, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                $('#list-po').html(data);
                $("#kategori option[value='']").remove();
              },
              error: function(data){
                console.log(data.responseText);
              }
            });
          }else{
            $('#list-po').html('<option value="">- Pilih Bahan Kemas -</option>');
          }
         });

        $('#list-po').on('change', function(){
          var po = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (po != '') {
            // console.log(provinsi);
            $.ajax({
              url: "<?php echo base_url()?>admin/produksi/get_id_po",
              type: "post",
              data: {'po': po, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                $('#bahan-kemas').html(data);
                $("#list-po option[value='']").remove();
              },
              error: function(data){
                console.log(data.responseText);
              }
            });
          }else{
            $('#bahan-kemas').html('<option value="">- Pilih Bahan Kemas -</option>');
          }
         });

        $('#bahan-kemas').on('change', function(){
          var bahan = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (bahan != '') {
            // console.log(provinsi);
            $.ajax({
              url: "<?php echo base_url()?>admin/produksi/get_id_bahan_kemas",
              type: "post",
              data: {'bahan': bahan, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                document.getElementById("in_po").value = data.no_po;
                document.getElementById("in_stok").value = data.kuantitas_po - data.selisih_po_produksi;
                document.getElementById("in_id").value = data.id_bahan_kemas;
                document.getElementById("in_sku").value = data.kode_sku_bahan_kemas;
                document.getElementById("in_bahan_kemas").value = data.nama_bahan_kemas;
                document.getElementById("in_qty").value = 0;
                document.getElementById("in_harga").value = data.harga_po;
                document.getElementById("in_jumlah").value = 0;
              },
              error: function(data){
                console.log(data.responseText);
              }
            });
          }
         });

        $('#add_bahan_kemas').click(function(){
          var no_po = $('#in_po').val();
          var id_bahan_kemas = $('#in_id').val();
          var kode_sku = $('#in_sku').val();
          var nama_bahan_kemas = $('#in_bahan_kemas').val();
          var qty = $('#in_qty').val();
          var harga = $('#in_harga').val();
          var jumlah = $('#in_jumlah').val();

          var html = '<tr>';
          html += '<td> <input type="hidden" id="dt_po[]" name="dt_po[]" value="'+no_po+'"> <input type="hidden" id="dt_id[]" name="dt_id[]" value="'+id_bahan_kemas+'">'+kode_sku+'</td>'; 
          html += '<td> '+nama_bahan_kemas+' </td>'; 
          html += '<td> <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="'+qty+'">'+qty+'</td>';
          html += '<td> <input type="hidden" id="dt_harga[]" name="dt_harga[]" value="'+harga+'">'+harga+'</td>';
          html += ' <td> <input type="hidden" id="dt_jumlah[]" name="dt_jumlah[]" value="'+jumlah+'">'+jumlah+'</td>';
          html +=  '<td> <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> </tr>';
          
          $('tbody').append(html);

          // clear input data
          $('#in_po').val('');
          $('#in_id').val('');
          $('#in_sku').val('');
          $('#in_bahan_kemas').val('');
          $('#in_harga').val('');
          $('#in_qty').val('');
          $('#in_stok').val('');
          $('#in_jumlah').val('');
          
          $("#bahan-kemas").val("").trigger("change.select2");
        });
    }); 

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
