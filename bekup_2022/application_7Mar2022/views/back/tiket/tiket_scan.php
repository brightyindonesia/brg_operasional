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
                <div class="form-group"><label>No. Resi atau No. Pesanan (*)</label>
                  <?php echo form_input($nomor) ?>
                </div>    
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <div id="hasil_retur" style="display: none;" class="card-body">
                  <table width="100%" border="0" class="table table-bordered table-responsive">
                    <tr>
                      <td width="20%" style="background-color: #f5f5f5;font-weight: bold;">Nomor Pesanan</td>
                      <td width="1%">:</td>
                      <td>
                        <input type="hidden" id="resi">
                        <input type="hidden" id="nomor-pesanan">
                        <input type="hidden" id="status">
                        <input type="hidden" id="status-resi">
                        <div id="pesanan"></div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nomor Resi</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="nomor-resi"></div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nama Kurir</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="nama-kurir">
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nama Toko</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="nama-toko">
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
                        <div id="nama-penerima"></div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Alamat Penerima</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="alamat-penerima"></div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Kabupaten</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="kabupaten">
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Provinsi</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="provinsi"></div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nomor Handphone</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="hp-penerima"></div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Masukan Keterangan Retur</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <textarea id="keterangan" class="form-control"></textarea>
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
                        </tr>
                      </thead>
                    </table>
                  </div>

                  <div class="box-footer">
                    <button type="submit" id="retur_tambah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
                  </div>
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
    window.onload = function() {
      $("#nomor").focus();
    }

    // $('#table-resi-retur').DataTable();

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

    // $('#retur_tambah').click(function(e){
    //   e.preventDefault();

    //   var pesanan = document.getElementById('nomor-pesanan').value;
    //   var resi = document.getElementById('resi').value;
    //   var status_resi = document.getElementById('status-resi').value;
    //   var keterangan = document.getElementById('keterangan').value;
    //   var status = document.getElementById('status').value;
    //   var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
    //   csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    //   if(keterangan == '') {
    //     Toast.fire({
    //       icon: 'error',
    //       title: 'Terjadi Kesalahan!. Keterangan Retur harap diisi!'
    //     });
    //   }else if(pesanan == ''){
    //     Toast.fire({
    //       icon: 'error',
    //       title: 'Terjadi Kesalahan!. Nomor Pesanan masih kosong!'
    //     });
    //   }else{
    //     $.ajax({ 
    //       url:"<?php echo base_url()?>admin/retur/retur_produk_proses",
    //       method:"post",
    //       dataType: 'JSON', 
    //       data:{resi: resi, status_resi: status_resi, status:status, nomor_pesanan: pesanan, keterangan: keterangan, [csrfName]: csrfHash},
    //       success:function(data)  {  
    //         // alert(data);
    //         if (data.validasi) {
    //           Toast.fire({
    //             icon: 'error',
    //             title: 'Perhatian!',
    //             text: data.validasi
    //           })
    //         }

    //         if (data.sukses) {
    //           Toast.fire({
    //             icon: 'success',
    //             title: 'Sukses!',
    //             text: data.sukses,
    //           }).then(function(){
    //             window.location.replace("<?php echo base_url()?>admin/retur/retur_produk");
    //           });
    //         }
            
    //       },
    //       error: function(data){
    //         console.log(data.responseText);
    //         // Toast.fire({
    //         //   type: 'warning',
    //         //   title: 'Perhatian!',
    //         //   text: data.responseText
    //         // });

    //       } 
    //     });
    //   }
    // });

    function cekNomor() {
      var nomor = document.getElementById("nomor").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
            url: "<?php echo base_url()?>admin/tiket/scan_proses",
            type: "post",
            data: {'nomor': nomor, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  
              if (data.validasi) {
                toastr.error(data.validasi)
                // document.getElementById("hasil_retur").style.display = "none";
                // document.getElementById("nomor-pesanan").value = "";
                // document.getElementById("resi").value = "";
                // document.getElementById("pesanan").innerHTML = "";
                // document.getElementById("nomor-resi").innerHTML = "";
                // document.getElementById("nama-kurir").innerHTML = "";
                // document.getElementById("nama-toko").innerHTML = "";
                // document.getElementById("nama-penerima").innerHTML = "";
                // document.getElementById("alamat-penerima").innerHTML = "";
                // document.getElementById("kabupaten").innerHTML = "";
                // document.getElementById("provinsi").innerHTML = "";
                // document.getElementById("hp-penerima").innerHTML = "";
                // document.getElementById("status").value = "";
                // document.getElementById("status-resi").value = "";
              }else if(data.sukses){
                toastr.success(data.sukses)
                // document.getElementById("hasil_retur").style.display = "block";
                // document.getElementById("nomor-pesanan").value = data.nomor_pesanan;
                // document.getElementById("pesanan").innerHTML = data.nomor_pesanan;
                // document.getElementById("nomor-resi").innerHTML = data.nomor_resi;
                // document.getElementById("resi").value = data.nomor_resi;
                // document.getElementById("status-resi").value = data.status_resi;
                // document.getElementById("nama-kurir").innerHTML = data.nama_kurir;
                // document.getElementById("nama-toko").innerHTML = data.nama_toko;
                // document.getElementById("nama-penerima").innerHTML = data.nama_penerima;
                // document.getElementById("alamat-penerima").innerHTML = data.alamat_penerima;
                // document.getElementById("kabupaten").innerHTML = data.kabupaten;
                // document.getElementById("provinsi").innerHTML = data.provinsi;
                // document.getElementById("hp-penerima").innerHTML = data.hp_penerima;
                // document.getElementById("status").value = data.status;
                // $('#table-resi-retur').DataTable({
                //     'aaData' : data.table,          // this is input parameter for your function
                //     "bDestroy": true,
                //     'columns': [
                //         { data: 'no'},
                //         { data: 'nama_produk'},
                //         { data: 'qty'},
                //     ],
                //     columnDefs: [
                //       { className: 'text-center', 
                //         targets: [0, 1, 2] 
                //       }
                //     ],
                // });
              }
              
            },
            error: function(data){
              console.log(data.responseText);

            }  
          });
      $('#nomor').val('');
    }
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
