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
      <div class="alert alert-info">
        <h3 style="margin-top: -5px"><b>PERHATIAN!!</b></h3>
        <p style="font-size: 16px">Sebelum memasukan Data Import Request For Quotation dan Purchase Order <b>WAJIB</b> melakukan <b>Backup Database</b> terlebih dahulu!!</p>
      </div>
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php echo validation_errors() ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#rfq" data-toggle="tab">Import Request For Quotation</a></li>
              <li><a href="#po" data-toggle="tab">Import Purchase Order</a></li>
              <li><a href="#backup" data-toggle="tab">Backup Database</a></li>
            </ul>
            <div class="tab-content">
              <div class="active rfq tab-pane" id="rfq">
                <?php include('tab_content/content_import_rfq.php'); ?>                
              </div>

              <div class="po tab-pane" id="po">
                <?php include('tab_content/content_import_po.php'); ?>                
              </div>

              <div class="tab-pane" id="backup">
                <?php include('tab_content/content_import_backup.php'); ?>  
              </div>               
            </div>
          </div>
        </div>
      </div>

      <div class="box box-primary">
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Request (*)</label>
                    <?php echo form_input($nomor_request) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, '', $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('sku', $get_all_kategori, '', $kategori) ?>
                  </div>

                  <div class="form-group"><label>Harga Ongkos Kirim</label>
                    <?php echo form_input($ongkir) ?>
                  </div>

                  <div class="form-group"><label>Tip</label>
                    <?php echo form_input($tip) ?>
                  </div>
              </div>

              <div class="col-sm-6">
                  <div class="form-group"><label>Nama Vendor (*)</label>
                    <?php echo form_dropdown('vendor', $get_all_vendor, '', $vendor) ?>
                  </div>

                  <div class="form-group"><label>Nama Penerima (*)</label>
                    <?php echo form_dropdown('penerima', $get_all_penerima, '', $penerima) ?>
                  </div>

                  <div class="form-group"><label>Remarks</label>
                    <?php echo form_textarea($remarks, ''); ?>
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
                    <?php echo form_dropdown('bahan_kemas', '', '', $bahan_kemas) ?>
                  </div>
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
                  <!-- <input type="text" name="harga" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" id="in_harga"  oninput="cal();" value="" class="form-control"> -->
                  <input type="text" name="harga" id="in_harga"  oninput="cal();" value="" class="form-control">
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
                          <th>Diskon</th>
                          <th>Pajak</th>
                          <th width="1%">Aksi</th>
                        </tr>
                      </thead>

                      <tbody>
                      </tbody>

                      <tfoot>
                        <tr align="center">
                          <th>Kode SKU</th>
                          <th>Nama Produk</th>
                          <th>Qty</th>
                          <th>Harga</th>
                          <th>Jumlah</th>
                          <th>Diskon</th>
                          <th>Pajak</th>
                          <th>Aksi</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="">TTD Finance (*)</label>
                    <select name="finance" id="finance" class="form-control" required>
                      <option selected disabled>-- Pilih -- </option>
                      <option value="Leni Wahyuni">Leni Wahyuni</option>
                      <option value="Melinda Nur Wijayanti">Melinda Nur Wijayanti</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="">TTD FAT Manager</label>
                    <select name="fat_manager" id="fat_manager" class="form-control">
                      <option value="">-- Pilih -- </option>
                      <option value="Setia Wardhana">Setia Wardhana</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="">TTD CEO</label>
                    <select name="ceo" id="ceo" class="form-control">
                      <option value="">-- Pilih -- </option>
                      <option value="M. Hadiyatullah">M. Hadiyatullah</option>
                    </select>
                  </div>
                </div>
              </div>
          </div>
          <div class="box-footer">
            <button type="submit" id="request-tambah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
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

  <!-- CK Editor -->
  <script src="<?php echo base_url('assets/plugins/') ?>ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(function () {
      // Replace the <textarea id="editor1"> with a CKEditor
      // instance, using default configuration.
      CKEDITOR.replace('remarks')
      //bootstrap WYSIHTML5 - text editor
      $('.textarea').wysihtml5()
    })

    window.onload = function(){
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
            url: "<?php echo base_url()?>admin/masuk/generate_nomor",
            type: "post",
            data: {[csrfName]: csrfHash},
            dataType: 'JSON',
            success: function(data){
              $('#nomor-request').val(data);
            },
            error: function(){
              alert('Error ....');
            }
          });
    }

    $('#in_harga').keypress(function(event) {
        var $this = $(this);
        // this next line...
        if ((event.which != 46 || $this.val().indexOf('.') != -1) &&
           ((event.which < 48 || event.which > 57) &&
           (event.which != 0 && event.which != 8))) {
               event.preventDefault();
        }

        var text = $(this).val();
        // this next line...
        if ((event.which == 46) && (text.indexOf('.') == -1)) {
            setTimeout(function() {
                if ($this.val().substring($this.val().indexOf('.')).length > 3) {
                    $this.val($this.val().substring(0, $this.val().indexOf('.') + 3));
                }
            }, 1);
        }

        if ((text.indexOf(',') != -1) &&
            (text.substring(text.indexOf('.')).length > 2) &&
            (event.which != 0 && event.which != 8) &&
            ($(this)[0].selectionStart >= text.length - 2)) {
                event.preventDefault();
        }      
    });

    $('#restoredb').click(function(e){
      const Toast = Swal.mixin({
        toast: false,
        position: 'center',
        showConfirmButton: true,
        // confirmButtonColor: '#86ccca',
        // timer: 3000,
        timerProgressBar: false,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      e.preventDefault();

      var restore = document.getElementById('restore_db');
      var JS_restore = JSON.stringify(restore.files[0]);
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      
      if(restore.files['length'] == 0){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. File DB harus diisi!'
        });
      }else{
        var formData = new FormData();
        formData.append('restore_db', $('#restore_db')[0].files[0]);      
        formData.append([csrfName], csrfHash); 

        $("#modal-proses").modal('show');
        $.ajax({ 
          url:"<?php echo base_url()?>admin/masuk/restore_db",
          method:"post",
          dataType: 'JSON', 
          // data:{img: JS_image,vendor:vendor, penerima:penerima, sku: sku, kategori: kategori, ongkir: ongkir, remarks: remarks, nomor_request: nomor_request, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_diskon: JS_diskon, dt_pajak: JS_pajak, length: panjangArray, [csrfName]: csrfHash},
          data: formData,
          contentType: false,
          processData: false,
          success:function(data)  {  
            // alert(data);
            if (data.validasi) {
              $("#modal-proses").modal('hide');
              Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: data.validasi
              })
            }

            if (data.sukses) {
              $("#modal-proses").modal('hide');
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/dashboard");
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

    // submit form masuk
    $('#request-tambah').click(function(e){
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
      var kategori = document.getElementById('kategori').value;
      var sku = document.getElementById('sku').value;
      var ongkir = document.getElementById('ongkir').value;
      // var remarks = document.getElementById('remarks').value;
      var remarks = CKEDITOR.instances.remarks.getData(); 
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

      var finance = document.getElementById('finance').value;
      var fat_manager = document.getElementById('fat_manager').value;
      var ceo = document.getElementById('ceo').value;

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
      }else if(sku == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama SKU harap dipilih!'
        });
      }else if(kategori == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Kategori harap dipilih!'
        });
      }else if(nomor_request == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Pesanan harus diisi!'
        });
      }else if(finance == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. TTD Finance harus diisi!'
        });
      }else if(dt_id == '' || dt_qty == '' || dt_harga == '' || dt_jumlah == ''){
        Toast.fire({
          icon: 'error',
          title: 'Daftar Request Bahan Kemas tidak berisi data!'
        });
      }else{
        $.ajax({ 
          url:"<?php echo base_url()?>admin/masuk/request_proses",
          method:"post",
          dataType: 'JSON', 
          data:{vendor:vendor, sku: sku, penerima : penerima, kategori: kategori, ongkir: ongkir, remarks: remarks, nomor_request: nomor_request, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_diskon: JS_diskon, dt_pajak: JS_pajak, length: panjangArray, [csrfName]: csrfHash, finance: finance, fat_manager: fat_manager, ceo: ceo},
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

          if (id_bahan_kemas == '' || kode_sku == '' ||  hpp == '' ||  nama_bahan_kemas == '' ||  qty == '' ||  harga == '' ||  jumlah == '' ||  diskon == '' ||  pajak == '') {
            Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: 'Bahan Kemas masih kosong!. Silahkan diisi!'
            })
          }else if(qty == 0){
            Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: 'Qty Bahan Kemas masih kosong!. Silahkan diisi!'
            })
          }else{
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
          }
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
