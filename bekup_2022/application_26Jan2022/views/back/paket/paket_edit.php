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
                  <div class="form-group"><label>Nama Paket</label>
                    <?php echo form_input($paket_nama, $paket->nama_paket) ?>
                  </div>
              </div>
            </div>

            <!-- FORM PRODUK TAMBAH -->
            <div class="row">
              <div class="col-sm-12">
                 <div class="form-group"><label>Nama Produk (*)</label>
                    <?php echo form_dropdown('produk', $get_all_produk, '', $produk) ?>
                  </div>
                  <?php echo form_input($id_paket, $paket->id_paket) ?>
              </div>
            </div>
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
              <input type="hidden" name="id_produk" id="in_id" value="" class="form-control">
              <input type="hidden" name="sku_produk" id="in_sku" value="" class="form-control">
              <input type="hidden" name="stok" id="in_stok" value="" class="form-control">

              <div class="col-sm-9">
                <div class="form-group">
                  <label>Nama Produk</label>
                  <input type="text" readonly name="nama_barang" id="in_produk" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-1">
                <div class="form-group">
                  <label>Qty</label>
                  <input type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" name="qty" oninput="val_qty()" id="in_qty" class="form-control">
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
                    <label>Daftar Produk</label>
                      <table id="example3" class="table table-bordered table-striped">
                      <thead>
                        <tr align="center">
                          <th width="15%">Kode SKU</th>
                          <th>Nama Produk</th>
                          <th width="5%">Qty</th>
                          <th width="1%">Aksi</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php  
                          foreach ($get_all_pakduk as $row) {
                        ?>
                        <tr>
                          <td>
                            <input type="hidden" id="dt_id[]" name="dt_id[]" value="<?php echo $row->id_produk; ?>">
                            <?php echo $row->sub_sku; ?>
                          </td>
                          <td>
                            <?php echo $row->nama_produk; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="<?php echo $row->qty_pakduk; ?>">
                            <?php echo $row->qty_pakduk; ?>
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
                          <th>Aksi</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
          </div>
          <div class="box-footer">
            <button type="submit" id="paket_ubah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
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
    $('#paket_ubah').click(function(e){
      const Toast = Swal.mixin({
        toast: false,
        position: 'center',
        showConfirmButton: false,
        // confirmButtonColor: '#86ccca',
        timer: 950,
        timerProgressBar: false,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      e.preventDefault();

      var id = document.getElementById('id-paket').value;
      var nama = document.getElementById('nama-paket').value;
      var dt_id =  $("input[name='dt_id[]']")
            .map(function(){return $(this).val();}).get();
      var dt_qty =  $("input[name='dt_qty[]']")
            .map(function(){return $(this).val();}).get();     
      var JS_id = JSON.stringify(dt_id);
      var JS_qty = JSON.stringify(dt_qty);
      var panjangArray = dt_id.length;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      if (nama == '' && dt_id == '' && dt_qty == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else if(nama == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Paket harap diisi!'
        });
      }else{
        $.ajax({ 
          url:"<?php echo base_url()?>admin/paket/ubah_proses",
          method:"post",
          dataType: 'JSON', 
          data:{id:id, nama:nama, dt_id: JS_id, dt_qty: JS_qty, length: panjangArray, [csrfName]: csrfHash},
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
                window.location.replace("<?php echo base_url()?>admin/paket");
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
    
    // window.onload = function() {
    //   $("#example3").find('tbody').empty(); //add this line
    // };

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
        $("#add_produk").prop("disabled",true);
      }else{
        document.getElementById("hasil_val").style.display = "none";
        document.getElementById("hasil_val").innerHTML = "";
        document.getElementById("in_qty").style.backgroundColor = "";
        document.getElementById("in_qty").style.color = "";
        // $("#submit_form_keluar").prop("disabled",false);
        $("#add_produk").prop("disabled",false);
      }
    }

    $(document).ready(function(){
        // barang masuk row
        $('#add_produk').click(function(){
          var id_produk = $('#in_id').val();
          var kode_sku  = $('#in_sku').val();
          var nama_produk = $('#in_produk').val();  
          var qty       = $('#in_qty').val();

          var html = '<tr>';
          html += '<td> <input type="hidden" id="dt_id[]" name="dt_id[]" value="'+id_produk+'">'+kode_sku+'</td>'; 
          html += '<td> '+nama_produk+' </td>'; 
          html += '<td> <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="'+qty+'">'+qty+'</td>';
          html +=  '<td> <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> </tr>';
          
          $('tbody').append(html);

          // clear input data
          $('#in_id').val('');
          $('#in_sku').val('');
          $('#in_stok').val('');
          $('#in_produk').val('');
          $('#in_qty').val('');
          $("#produk").val("").trigger("change.select2");
        });

        $(document).on('click', '#hps_row', function(){
            $(this).closest('tr').remove();
        });

        $('#produk').on('change', function(){
          var produk = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (produk != '') {
            $.ajax({
              url: "<?php echo base_url()?>admin/keluar/get_id_produk",
              type: "post",
              data: {'produk': produk, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                document.getElementById("in_id").value = data.id_produk;
                document.getElementById("in_sku").value = data.sub_sku;
                document.getElementById("in_produk").value = data.nama_produk;
                document.getElementById("in_qty").value = 0;
                document.getElementById("in_stok").value = data.qty_produk;
              },
              error: function(){
                alert('Error ....');
              }
            });
          }else{
            // clear input data
            $('#in_id').val('');
            $('#in_produk').val('');
            $('#in_qty').val('');
            $('#in_sku').val('');
            $('#in_stok').val('');
          }
         });
    }); 
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
