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
          echo form_open($action);
        ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Produksi (*)</label>
                    <?php echo form_input($nomor_request, str_replace("PO","TML",$timeline->no_po)) ?>
                  </div>

                  <div class="form-group"><label>Nomor PO Bahan Produksi (*)</label>
                    <?php echo form_dropdown('po', $get_all_po, '', $po) ?>
                  </div>

                  <div class="form-group"><label>Nama SKU (*)</label>
                    <?php echo form_dropdown('sku', $get_all_sku, $timeline->id_sku, $sku) ?>
                  </div>

                  <div class="form-group"><label>Nama Vendor (*)</label>
                    <?php echo form_input($nama_vendor, $timeline->nama_vendor) ?>
                  </div>

                  <div class="form-group"><label>Start - End Date (*)</label>
                    <input type="text" name="periodik" class="form-control float-right" id="range-date">
                  </div>
              </div>

              <div class="col-sm-6">
                  <div class="form-group"><label>Nomor Purchase Order (*)</label>
                    <?php echo form_input($nomor_request, $timeline->no_po) ?>
                  </div>

                  <div class="form-group"><label>Nama Kategori (*)</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, $timeline->id_kategori_po, $kategori) ?>
                  </div>

                  <div class="form-group"><label>Jumlah Produksi / Terkirim (*)</label>
                    <?php echo form_input($qty, $timeline->total_produksi." / ".$timeline->total_produksi_jadi) ?>
                  </div>

                  <div class="form-group"><label>Keterangan</label>
                    <?php echo form_textarea($keterangan, '') ?>
                  </div>
              </div>
            </div>

            <!-- FORM PRODUK TAMBAH -->
            <div class="row">
              <div class="col-sm-12">
                 <div class="form-group"><label>Nama Bahan Produksi (*)</label>
                    <?php echo form_dropdown('bahan', '', '', $bahan) ?>
                  </div>
              </div>
            </div>
            <input type="hidden" name="po_bahan" id="in-po-bahan" value="" class="form-control">

            <!-- Pilih Bahan Produksi dari PO -->
            <div class="row">
              <div id="hasil_val" style="display: none;" class="col-sm-12">
              </div>              
            </div>
            <div class="row">
              <input type="hidden" name="in_id" id="in_id" value="" class="form-control">
              <!-- <input type="hidden" name="harga_bahan" id="in_po" value="" class="form-control"> -->
              <input type="hidden" name="stok" id="in_stok" value="" class="form-control">

              <div class="col-sm-5">
                <div class="form-group">
                  <label>Nama Bahan Produksi</label>
                  <input type="text" readonly name="nama_barang" id="in_bahan" value="" class="form-control">
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
                  <label>Harga Bahan Produksi</label>
                  <input type="text" readonly name="harga" id="in_harga" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" value="" class="form-control">
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
                  <button type="button" id="add_bahan" class="btn btn-success btn-sm form-control">
                    <i class="fa fa-plus"></i>                  
                  </button>
                </div>
              </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Daftar Bahan Produksi </label>
                      <table id="example3" class="table table-bordered table-striped">
                      <thead>
                        <tr align="center">
                          <th width="65%">Nama Bahan</th>
                          <th width="5%">Qty</th>
                          <th>Harga</th>
                          <th>Jumlah</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      
                      <tbody>
                        <?php
                          $i = 0; 
                          foreach ($bahan_kemas as $row) {
                        ?>
                        <tr>
                          <td>
                            <input type="hidden" id="dt_po_bahan[]" name="dt_po_bahan[]" value="<?php echo $row->no_po; ?>">
                            <input type="hidden" id="dt_id_bahan[]" name="dt_id_bahan[]" value="<?php echo $row->id_bahan_kemas; ?>">
                            <!-- <input type="hidden" id="dt_qty_bahan[]" name="dt_qty_bahan[]" value="<?php echo $row->kuantitas_po; ?>"> -->
                            <input type="hidden" id="dt_harga_bahan<?php echo $i ?>" name="dt_harga_bahan[]" value="<?php echo $row->harga_po; ?>">
                            <?php echo $row->nama_bahan_kemas; ?>
                          </td>
                          <td>
                            <input type="text" style="width: 100px;text-align: center;" type="text" oninput="checkValue(this);calculate('dt_qty_bahan<?php echo $i ?>', 'dt_harga_bahan<?php echo $i ?>', 'td_jumlah<?php echo $i ?>');" min="1" max="<?php echo $row->kuantitas_po - $row->selisih_po_produksi; ?>" name="dt_qty_bahan[]" id="dt_qty_bahan<?php echo $i ?>" value="<?php echo $row->kuantitas_po - $row->selisih_po_produksi; ?>">
                          </td>
                          <td>
                            <?php echo $row->harga_po; ?>
                          </td>
                          <td>
                            <div id="td_jumlah<?php echo $i ?>">
                              <?php echo ($row->kuantitas_po - $row->selisih_po_produksi) * $row->harga_po; ?>
                            </div>
                          </td>
                          <td>
                            &nbsp;
                            <!-- <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> -->
                          </td>
                        </tr>
                        <?php
                            $i++;
                          }
                        ?>
                      </tbody>

                      <tfoot>
                        <tr align="center">
                          <th>Nama Bahan</th>
                          <th>Qty</th>
                          <th>Harga</th>
                          <th>Jumlah</th>
                          <th>Action</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>

            <?php echo form_input($id, str_replace("PO","TML",$timeline->no_po)) ?>
            <?php echo form_input($id_po, $timeline->no_po) ?>
            <?php echo form_input($id_sku, $timeline->id_sku) ?>
            <?php echo form_input($qty_produksi, $timeline->total_kuantitas_po) ?>
          </div>
          <div class="box-footer">
            <button type="submit" id="bahan-industry" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
          </div>
      </div>
      <?php 
        echo form_close();
      ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>

  <script type="text/javascript">
    window.onload = function() {
      // $("#example3").find('tbody').empty(); //add this line
    }

    $('#range-date').daterangepicker({
        locale: {
          format: 'YYYY/MM/DD'
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

    function calculate(qtyId, HargaId, jumlahTD)
    {
       qty = document.getElementById(qtyId).value;
       harga = document.getElementById(HargaId).value;

       total = qty * harga;
       if(isNaN(total))
       {
        total = 0;
       }

       document.getElementById(jumlahTD).innerHTML = total;
    }

    // this checks the value and updates it on the control, if needed
    function checkValue(sender) {
        let min = sender.min;
        let max = sender.max;
        let value = parseInt(sender.value);
        if (value>max) {
            sender.value = max;
        } else if (value<min) {
            sender.value = max;
        }
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
        $("#add_bahan").prop("disabled",true);
      }else{
        document.getElementById("hasil_val").style.display = "none";
        document.getElementById("hasil_val").innerHTML = "";
        document.getElementById("in_qty").style.backgroundColor = "";
        document.getElementById("in_qty").style.color = "";
        // $("#submit_form_keluar").prop("disabled",false);
        $("#add_bahan").prop("disabled",false);
      }
    }

    $('#po').on('change', function(){
      var po = $(this).val();
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      if (po != '') {
        // console.log(provinsi);
        $.ajax({
          url: "<?php echo base_url()?>admin/timeline_produksi/get_no_po",
          type: "post",
          data: {'po': po, [csrfName]: csrfHash},
          dataType: 'JSON',
          success: function(data){
            $('#bahan').html(data.select);
            document.getElementById("in-po-bahan").value = data.po_bahan;
            $("#po option[value='']").remove();
            
          },
          error: function(){
            alert('Error ....');
          }
        });
      }else{
        $('#produk').html('<option value="">- Pilih Produk -</option>');
      }
     });

    $('#bahan').on('change', function(){
      var bahan = $(this).val();
      var nomor_po = document.getElementById('in-po-bahan').value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      if (bahan != '' && nomor_po != '') {
        $.ajax({
          url: "<?php echo base_url()?>admin/timeline_produksi/get_id_bahan",
          type: "post",
          data: {'po': nomor_po, 'bahan': bahan, [csrfName]: csrfHash},
          dataType: 'JSON',
          success: function(data){
            document.getElementById("in_id").value = data.id_bahan_kemas;
            document.getElementById("in_bahan").value = data.nama_bahan_kemas;
            document.getElementById("in_qty").value = data.fix_sisa_stok_pabrik;
            $("#in_qty").attr("max", data.sisa_fix_stok_pabrik);
            document.getElementById("in_harga").value = data.harga_po;
            document.getElementById("in_stok").value = data.fix_sisa_stok_pabrik;
            document.getElementById("in_jumlah").value = data.harga_po * data.fix_sisa_stok_pabrik;
            // $("#bahan option[value='']").remove();
          },
          error: function(){
            alert('Error ....');
          }
        });
      }else{
        // clear input data
        $('#in_id').val('');
        $('#in_po').val('');
        $('#in_bahan').val('');
        $('#in_harga').val('');
        $('#in_qty').val('');
        $('#in_jumlah').val('');
        $('#in_stok').val('');
      }
     });

     $(document).on('click', '#hps_row', function(){
        $(this).closest('tr').remove();
     });

    // barang masuk row
    $('#add_bahan').click(function(){
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

      var id_bahan = $('#in_id').val();
      var nama_bahan = $('#in_bahan').val();
      var po = $('#in-po-bahan').val();
      var qty = $('#in_qty').val();
      var stok = $('#in_stok').val();
      var harga = $('#in_harga').val();
      var jumlah = $('#in_jumlah').val();
      var count = $('#example3 tbody tr').length;

      if (id_bahan == '' || nama_bahan == '' || po == '' || qty == '' || stok == '' || harga == '' || jumlah == '') {
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
        html += '<input type="hidden" id="dt_po[]" name="dt_po[]" value="'+po+'">'; 
        html += '<td> <input type="hidden" id="dt_id[]" name="dt_id[]" value="'+id_bahan+'">'+nama_bahan+'</td>'; 
        html += '<td> <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="'+qty+'"> <input type="hidden" id="dt_stok[]" name="dt_stok[]" value="'+stok+'">'+qty+'</td>';
        html += '<td> <input type="hidden" id="dt_harga[]" name="dt_harga[]" value="'+harga+'">'+harga+'</td>';
        html += ' <td> <input type="hidden" id="dt_jumlah[]" name="dt_jumlah[]" value="'+jumlah+'">'+jumlah+'</td>';
        html +=  '<td> <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> </tr>';
        
        if (count > 0) {
          $('#example3 tbody tr:last').after(html);
        }else{
          $('tbody').append(html);
        }      

        // clear input data
        $('#in_id').val('');
        $('#in_stok').val('');
        $('#in_bahan').val('');
        $('#in_harga').val('');
        $('#in_qty').val('');
        $('#in_jumlah').val('');
        $("#bahan").val("").trigger("change.select2");
      }
    });

    // submit form masuk
    $('#bahan-industry').click(function(e){
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

      var nomor_po = document.getElementById('nomor-po').value;
      var nomor_produksi = document.getElementById('nomor-produksi').value;
      var keterangan = document.getElementById('keterangan').value;
      var qty = document.getElementById('qty-produksi').value;
      var sku = document.getElementById('sku').value;
      var date = document.getElementById('range-date').value;
      var dt_po_bahan =  $("input[name='dt_po_bahan[]']")
            .map(function(){return $(this).val();}).get();
      var dt_id_bahan =  $("input[name='dt_id_bahan[]']")
            .map(function(){return $(this).val();}).get();
      var dt_qty_bahan =  $("input[name='dt_qty_bahan[]']")
            .map(function(){return $(this).val();}).get();
      var dt_harga_bahan =  $("input[name='dt_harga_bahan[]']")
            .map(function(){return $(this).val();}).get();
      var dt_jumlah_bahan =  $("input[name='dt_jumlah_bahan[]']")
            .map(function(){return $(this).val();}).get(); 
      var JS_po_bahan = JSON.stringify(dt_po_bahan);
      var JS_id_bahan = JSON.stringify(dt_id_bahan);
      var JS_qty_bahan = JSON.stringify(dt_qty_bahan);
      var JS_harga_bahan = JSON.stringify(dt_harga_bahan);
      var JS_jumlah_bahan = JSON.stringify(dt_jumlah_bahan);
      var panjangArray_bahan = dt_id_bahan.length;
      // Bahan Kemas
      var dt_po =  $("input[name='dt_po[]']")
            .map(function(){return $(this).val();}).get();
      var dt_id =  $("input[name='dt_id[]']")
            .map(function(){return $(this).val();}).get();
      var dt_qty =  $("input[name='dt_qty[]']")
            .map(function(){return $(this).val();}).get();
      var dt_stok =  $("input[name='dt_stok[]']")
            .map(function(){return $(this).val();}).get();
      var dt_harga =  $("input[name='dt_harga[]']")
            .map(function(){return $(this).val();}).get();
      var dt_jumlah =  $("input[name='dt_jumlah[]']")
            .map(function(){return $(this).val();}).get(); 
      var JS_po = JSON.stringify(dt_po);
      var JS_id = JSON.stringify(dt_id);
      var JS_qty = JSON.stringify(dt_qty);
      var JS_stok = JSON.stringify(dt_stok);
      var JS_harga = JSON.stringify(dt_harga);
      var JS_jumlah = JSON.stringify(dt_jumlah);
      var panjangArray = dt_id.length;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      if (nomor_po == '' && nomor_produksi == '' && keterangan == '' && sku == '' && dt_id == '' && dt_po == '' && dt_qty == '' && dt_stok == '' && dt_harga == '' && dt_jumlah == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else if(dt_id == '' || dt_po == '' || dt_qty == '' || dt_stok == '' || dt_harga == '' || dt_jumlah == ''){
        Toast.fire({
          icon: 'error',
          title: 'Daftar Bahan Produksi tidak berisi data!'
        });
      }else{
        $.ajax({ 
          url:"<?php echo base_url()?>admin/timeline_produksi/industry_proses",
          method:"post",
          dataType: 'JSON', 
          data:{nomor_po:nomor_po, nomor_produksi: nomor_produksi, keterangan: keterangan, qty: qty, sku: sku, date: date, dt_id: JS_id, dt_po: JS_po, dt_qty: JS_qty, dt_stok: JS_stok, dt_harga: JS_harga, dt_jumlah: JS_jumlah, dt_id_bahan: JS_id_bahan, dt_po_bahan: JS_po_bahan, dt_qty_bahan: JS_qty_bahan, dt_harga_bahan: JS_harga_bahan, dt_jumlah_bahan: JS_jumlah_bahan, length: panjangArray, length_bahan: panjangArray_bahan, [csrfName]: csrfHash},
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
                window.location.replace("<?php echo base_url()?>admin/timeline_produksi/history/"+data.produksi);
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
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
