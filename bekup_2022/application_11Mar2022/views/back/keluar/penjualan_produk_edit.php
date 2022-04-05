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
                  <div class="form-group"><label>Nomor Pesanan (*)</label>
                    <?php echo form_input($nomor_pesanan, $penjualan->nomor_pesanan) ?>
                  </div>

                  <div class="form-group"><label>Nama Toko (*)</label>
                    <?php echo form_dropdown('toko', $get_all_toko, $penjualan->id_toko, $toko) ?>
                  </div>

                  <div class="form-group"><label>Kurir Ekspedisi (*)</label>
                    <?php echo form_dropdown('kurir', $get_all_kurir, $penjualan->id_kurir, $kurir) ?>
                  </div>

                  <div class="form-group"><label>Nomor Resi</label>
                    <?php echo form_input($nomor_resi, $penjualan->nomor_resi) ?>
                  </div>

                  <div class="form-group"><label>Biaya Ongkir</label>
                    <?php echo form_input($ongkir, $penjualan->ongkir) ?>
                  </div>

                  <div class="form-group"><label>Biaya Admin</label>
                    <?php echo form_input($biaya_admin, $penjualan->biaya_admin) ?>
                  </div>

                  <div class="form-group"><label>Jumlah Diterima</label>
                    <?php echo form_input($diterima, $penjualan->jumlah_diterima) ?>
                  </div>

                  <div class="form-group"><label>Status Transaksi (*)</label>
                    <?php echo form_dropdown('status', $get_all_status, $penjualan->id_status_transaksi, $status) ?>
                  </div>
              </div>
              <div class="col-sm-6">
                  <div class="form-group"><label>Nama Penerima (*)</label>
                    <?php echo form_input($nama_penerima, $penjualan->nama_penerima) ?>
                  </div>

                  <div class="form-group"><label>No. HP Penerima</label>
                    <?php echo form_input($hp_penerima, $penjualan->hp_penerima) ?>
                  </div>

                  <?php 
                    if ($penjualan->provinsi == '') {
                  ?>
                    <div class="form-group"><label>Provinsi (*)</label>
                      <?php echo form_dropdown('provinsi', $get_all_provinsi, '', $provinsi) ?>
                    </div>

                    <div class="form-group"><label>Kabupaten (*)</label>
                      <?php echo form_dropdown('kabupaten', '', '', $kabupaten) ?>
                    </div>
                  <?php    
                    }else{
                  ?>
                    <div class="form-group"><label>Provinsi (*)</label>
                      <?php echo form_dropdown('provinsi', $get_all_provinsi, $penjualan->provinsi, $provinsi) ?>
                    </div>

                    <div class="form-group"><label>Kabupaten (*)</label>
                      <?php echo form_dropdown('kabupaten', $get_all_kabupaten, $penjualan->kabupaten, $kabupaten) ?>
                    </div>
                  <?php
                    }
                  ?>

                  <div class="form-group"><label>Alamat Penerima (*)</label>
                    <?php echo form_textarea($alamat, $penjualan->alamat_penerima); ?>
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
                 <div class="form-group"><label>Nama Produk (*)</label>
                    <?php echo form_dropdown('produk', $get_all_produk, '', $produk) ?>
                  </div>
              </div>
            </div>
            <?php echo form_input($id, $penjualan->nomor_pesanan) ?>
            <?php echo form_input($id_kurir, $penjualan->id_kurir) ?>
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
              <input type="hidden" name="hpp_produk" id="in_hpp" value="" class="form-control">
              <input type="hidden" name="stok" id="in_stok" value="" class="form-control">

              <div class="col-sm-3">
                <div class="form-group">
                  <label>Nama Barang</label>
                  <input type="text" readonly name="nama_barang" id="in_produk" value="" class="form-control">
                </div>
              </div>

              <div class="col-sm-1">
                <div class="form-group">
                  <label>Qty</label>
                  <input type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" name="qty" oninput="cal();val_qty()" id="in_qty" class="form-control">
                </div>
              </div>

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Harga Jual</label>
                  <input type="text" name="harga" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" id="in_harga"  oninput="cal();val_qty()" value="" class="form-control">
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
                  <label>HPP Produk</label>
                  <input type="text" readonly name="jumlah" id="in_jumlah_hpp" value="" class="form-control">
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
                          <th>Harga</th>
                          <th>Jumlah</th>
                          <th>HPP Produk</th>
                          <th width="1%">Aksi</th>                        
                        </tr>
                      </thead>

                      <tbody>
                        <?php  
                          foreach ($daftar_produk as $row) {
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
                            <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="<?php echo $row->qty; ?>">
                            <?php echo $row->qty; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_harga[]" name="dt_harga[]" value="<?php echo $row->harga; ?>">
                            <?php echo $row->harga; ?>
                          </td>
                          <td>
                            <input type="hidden" id="dt_jumlah[]" name="dt_jumlah[]" value="<?php echo $row->qty * $row->harga; ?>">
                            <?php echo $row->qty * $row->harga; ?>
                          </td>

                          <td>
                            <input type="hidden" id="dt_jumlah_hpp[]" name="dt_jumlah_hpp[]" value="<?php echo $row->qty * $row->hpp; ?>">
                            <input type="hidden" id="dt_hpp[]" name="dt_hpp[]" value="<?php echo $row->hpp; ?>">
                            <?php echo $row->qty * $row->hpp; ?>
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
                          <th>HPP Produk</th>
                          <th>Aksi</th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
          </div>
          <div class="box-footer">
            <button type="submit" id="penjualan_ubah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
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
    $('#penjualan_ubah').click(function(e){
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

      var toko = document.getElementById('toko').value;
      var kurir = document.getElementById('kurir').value;
      var ongkir = document.getElementById('ongkir').value;
      var admin = document.getElementById('biaya-admin').value;
      var status = document.getElementById('status-transaksi').value;
      var diterima = document.getElementById('diterima').value;
      var resi = document.getElementById('nomor-resi').value; 
      var no_pesanan = document.getElementById('nomor-pesanan').value;
      var nama_penerima = document.getElementById('nama-penerima').value;
      var hp_penerima = document.getElementById('hp-penerima').value;
      var provinsi = document.getElementById('provinsi').value;
      var kabupaten = document.getElementById('kabupaten').value;
      var alamat = document.getElementById('alamat').value;
      var produk = document.getElementById('produk').value;
      var dt_id =  $("input[name='dt_id[]']")
            .map(function(){return $(this).val();}).get();
      var dt_qty =  $("input[name='dt_qty[]']")
            .map(function(){return $(this).val();}).get();
      var dt_harga =  $("input[name='dt_harga[]']")
            .map(function(){return $(this).val();}).get();
      var dt_jumlah =  $("input[name='dt_jumlah[]']")
            .map(function(){return $(this).val();}).get(); 
      var dt_hpp =  $("input[name='dt_hpp[]']")
            .map(function(){return $(this).val();}).get();        
      var dt_jumlah_hpp =  $("input[name='dt_jumlah_hpp[]']")
            .map(function(){return $(this).val();}).get();      
      var JS_id = JSON.stringify(dt_id);
      var JS_qty = JSON.stringify(dt_qty);
      var JS_harga = JSON.stringify(dt_harga);
      var JS_jumlah = JSON.stringify(dt_jumlah);
      var JS_hpp = JSON.stringify(dt_hpp);
      var JS_jumlah_hpp = JSON.stringify(dt_jumlah_hpp);
      var panjangArray = dt_id.length;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      if (status == '' && toko == '' && kurir == '' && ongkir == '' && no_pesanan == '' && nama_penerima == '' && provinsi == '' && alamat == '' && dt_id == '' && dt_qty == '' && dt_harga == '' && dt_jumlah == '' && dt_hpp == '' && dt_jumlah_hpp == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap diisi!'
        });
      }else if(toko == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Toko harap dipilih!'
        });
      }else if(kurir == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Kurur harap dipilih!'
        });
      }else if(status == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Status Transaksi harap dipilih!'
        });
      }else if(no_pesanan == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Pesanan harus diisi!'
        });
      }else if(nama_penerima == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nama Penerima harus diisi!'
        });
      }else if(provinsi == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Provinsi harap dipilih!'
        });
      }else if(kabupaten == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Kabupaten harap dipilih!'
        });
      }else if(alamat == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Alamat harus diisi!'
        });
      }else if(dt_id == '' || dt_qty == '' || dt_harga == '' || dt_jumlah == ''){
        Toast.fire({
          icon: 'error',
          title: 'Daftar Produk tidak berisi data!'
        });
      }else{
        if (resi == '') {
          $.ajax({ 
            url:"<?php echo base_url()?>admin/keluar/ubah_proses",
            method:"post",
            dataType: 'JSON', 
            data:{diterima: diterima, status:status, toko:toko, kurir: kurir, ongkir: ongkir, admin: admin, no_pesanan: no_pesanan, nama_penerima: nama_penerima, provinsi: provinsi, kabupaten: kabupaten, alamat: alamat, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_hpp: JS_hpp, dt_jml_hpp: JS_jumlah_hpp, length: panjangArray, hp_penerima: hp_penerima, [csrfName]: csrfHash},
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
                  window.location.replace("<?php echo base_url()?>admin/keluar/data_penjualan");
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
        }else{
          $.ajax({ 
            url:"<?php echo base_url()?>admin/keluar/ubah_proses",
            method:"post",
            dataType: 'JSON', 
            data:{diterima: diterima, status:status, toko:toko, kurir: kurir, ongkir: ongkir, admin: admin, resi: resi, no_pesanan: no_pesanan, nama_penerima: nama_penerima, provinsi: provinsi, kabupaten: kabupaten, alamat: alamat, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_hpp: JS_hpp, dt_jml_hpp: JS_jumlah_hpp, length: panjangArray, hp_penerima: hp_penerima, [csrfName]: csrfHash},
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
                  window.location.replace("<?php echo base_url()?>admin/keluar/data_penjualan");
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
      var hpp = document.getElementById('in_hpp').value;
      var result = document.getElementById('in_jumlah');
      var resultHPP = document.getElementById('in_jumlah_hpp'); 
      var myResult = qty * harga;
      var myResultHPP = qty * hpp;
      result.value = myResult;
      resultHPP.value = myResultHPP;
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
          
          var id_produk = $('#in_id').val();
          var kode_sku = $('#in_sku').val();
          var hpp  = $('#in_hpp').val();
          var jumlah_hpp = $('#in_jumlah_hpp').val();
          var nama_produk = $('#in_produk').val();
          var qty = $('#in_qty').val();
          var harga = $('#in_harga').val();
          var jumlah = $('#in_jumlah').val();

          if (id_produk == '' || kode_sku == '' || hpp == '' || jumlah_hpp == '' || nama_produk == '' || qty == '' || harga == '' || jumlah == '') {
            Toast.fire({
              icon: 'error',
              title: 'Perhatian!',
              text: 'Produk masih kosong!. Silahkan diisi!'
            })
          }else if(qty == 0){
            Toast.fire({
              icon: 'error',
              title: 'Perhatian!',
              text: 'Qty Produk masih kosong!. Silahkan diisi!'
            })
          }else{
            var html = '<tr>';
            html += '<td> <input type="hidden" id="dt_id[]" name="dt_id[]" value="'+id_produk+'">'+kode_sku+'</td>'; 
            html += '<td> '+nama_produk+' </td>'; 
            html += '<td> <input type="hidden" id="dt_qty[]" name="dt_qty[]" value="'+qty+'">'+qty+'</td>';
            html += '<td> <input type="hidden" id="dt_harga[]" name="dt_harga[]" value="'+harga+'">'+harga+'</td>';
            html += ' <td> <input type="hidden" id="dt_jumlah[]" name="dt_jumlah[]" value="'+jumlah+'">'+jumlah+'</td>';
            html += '<td> <input type="hidden" id="dt_hpp[]" name="dt_hpp[]" value="'+hpp+'"> <input type="hidden" id="dt_jumlah_hpp[]" name="dt_jumlah_hpp[]" value="'+jumlah_hpp+'">'+jumlah_hpp+'</td>';
            html +=  '<td> <button type="hidden" class="btn btn-danger btn-sm" id="hps_row">Hapus</button> </td> </tr>';
            
            $('tbody').append(html);

            // clear input data
            $('#in_id').val('');
            $('#in_sku').val('');
            $('#in_hpp').val('');
            $('#in_stok').val('');
            $('#in_produk').val('');
            $('#in_harga').val('');
            $('#in_qty').val('');
            $('#in_ket').val('');
            $('#in_jumlah').val('');
            $('#in_jumlah_hpp').val('');
            $("#produk").val("").trigger("change.select2");
          }
        });

        $(document).on('click', '#hps_row', function(){
            $(this).closest('tr').remove();
        });

        $('#provinsi').on('change', function(){
          var provinsi = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (provinsi != '') {
            // console.log(provinsi);
            $.ajax({
              url: "<?php echo base_url()?>admin/keluar/get_id_provinsi",
              type: "post",
              data: {'provinsi': provinsi, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                $('#kabupaten').html(data);
                $("#provinsi option[value='']").remove();
                // $("#example3").find('tbody').empty(); //add this line
              },
              error: function(){
                alert('Error ....');
              }
            });
          }else{
            $('#kabupaten').html('<option value="">- Pilih Kabupaten -</option>');
          }
         });

        $('#toko').on('change', function(){
          var toko = $(this).val();
          var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
              csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
          if (toko != '') {
            // console.log(provinsi);
            $.ajax({
              url: "<?php echo base_url()?>admin/keluar/get_id_toko",
              type: "post",
              data: {'toko': toko, [csrfName]: csrfHash},
              dataType: 'JSON',
              success: function(data){
                $('#produk').html(data);
                $("#toko option[value='']").remove();
                $("#example3").find('tbody').empty(); //add this line
              },
              error: function(){
                alert('Error ....');
              }
            });
          }else{
            $('#produk').html('<option value="">- Pilih Produk -</option>');
          }
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
                document.getElementById("in_hpp").value = data.hpp_produk;
                document.getElementById("in_produk").value = data.nama_produk;
                document.getElementById("in_qty").value = 0;
                document.getElementById("in_harga").value = 0;
                document.getElementById("in_jumlah").value = 0;
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
            $('#in_harga').val('');
            $('#in_qty').val('');
            $('#in_sku').val('');
            $('#in_hpp').val('');
            $('#in_stok').val('');
            $('#in_jumlah').val('');
            $('#in_jumlah_hpp').val('');
          }
         });
    }); 
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
