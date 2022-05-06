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
                <div id="hasil_tiket" class="card-body">
                  <table width="100%" border="0" class="table table-bordered table-responsive">
                    <tr>
                      <td width="20%" style="background-color: #f5f5f5;font-weight: bold;">Nomor Pesanan</td>
                      <td width="1%">:</td>
                      <td>
                        <?php 
                          echo form_input($id, $tiket->nomor_tiket);
                        ?>
                        <div id="pesanan">
                          <?php echo $get_pesanan->nomor_pesanan ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nomor Resi</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="nomor-resi">
                          <?php echo $get_pesanan->nomor_resi ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nama Kurir</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="nama-kurir">
                          <?php echo $get_pesanan->nama_kurir ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nama Toko</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="nama-toko">
                          <?php echo $get_pesanan->nama_toko ?>
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
                        <div id="nama-penerima">
                          <?php echo $get_pesanan->nama_penerima ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Alamat Penerima</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="alamat-penerima">
                          <?php echo $get_pesanan->alamat_penerima ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Kabupaten</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="kabupaten">
                          <?php echo $get_pesanan->kabupaten ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Provinsi</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="provinsi">
                          <?php echo $get_pesanan->provinsi ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Nomor Handphone</td>
                      <td width="1%">:</td>
                      <td>
                        <div id="hp-penerima">
                          <?php echo $get_pesanan->hp_penerima ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Judul Kasus</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo form_input($judul, $tiket->judul_kasus) ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Kategori Kasus</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo form_dropdown('kasus', $get_all_kasus, $tiket->id_kategori_kasus, $kasus) ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Level Kasus</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo form_dropdown('level', $get_all_level, $tiket->id_level_kasus, $level) ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Status Tiket</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo form_dropdown('status', $get_all_status, $tiket->id_status_tiket, $status) ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Masukan Pesan Tiket</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <textarea id="pesan" class="form-control"><?php echo $tiket->pesan_tiket ?></textarea>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">PIC</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo form_dropdown('users', $get_all_users, $tiket->handled_by, $users) ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Upload Gambar</td>
                      <td width="1%">:</td>
                      <td>
                        <input type="file" name="photo" id="photo" class="form-control" onchange="photoPreview(this,'preview')" accept=".jpg,.jpeg,.png"/>
                        <p class="help-block">Maximum file size is 2Mb</p>
                        <b>Photo Preview</b><br>
                        <img id="preview" src="<?php echo base_url() ?>uploads/gambar_kasus/<?php echo $tiket->nama_gambar ?>" width="350px"/>
                      </td>
                    </tr>
                  </table>

                  <div class="table-responsive">
                    <table id="table-tiket" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th style="text-align: center">No.</th>
                          <th style="text-align: center">Nama Produk</th>
                          <th style="text-align: center">Qty</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php 
                          $no = 1;
                          foreach ($get_produk_pesanan as $val_produk) {
                        ?>
                          <tr align="center">
                            <td>
                              <?php echo $no ?>
                            </td>

                            <td>
                              <?php echo $val_produk->nama_produk ?>
                            </td>

                            <td>
                              <?php echo $val_produk->qty ?>
                            </td>
                          </tr>
                        <?php    
                            $no++;
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>

                  <div class="box-footer">
                    <button type="submit" id="tiket_ubah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
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

    $("#table-tiket").DataTable();

    function photoPreview(photo,idpreview)
    {
      var gb = photo.files;
      for (var i = 0; i < gb.length; i++)
      {
        var gbPreview = gb[i];
        var imageType = /image.*/;
        var preview=document.getElementById(idpreview);
        var reader = new FileReader();
        // console.log(gbPreview);
        if (gbPreview.type.match(imageType))
        {
          if (gbPreview.size > 2000000) {
            //jika ukuran data tidak sesuai
            alert("Ukuran gambar tidak boleh lebih dari 2 MB");
            document.getElementById("photo").value = null;
            document.getElementById(idpreview).src = null;

          }else{
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
          }
        }else{
            //jika tipe data tidak sesuai
            alert("Tipe file tidak sesuai. Gambar harus bertipe .png, .gif atau .jpg.");
            document.getElementById("photo").value = null;
        }
      }
    }

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

    $('#tiket_ubah').click(function(e){
      e.preventDefault();

      var nomor_tiket = document.getElementById('nomor-tiket').value;
      var judul = document.getElementById('judul').value;
      var pesan = document.getElementById('pesan').value;
      var kasus = document.getElementById('kasus').value;
      var status = document.getElementById('status').value;
      var level = document.getElementById('level').value;
      var users = document.getElementById('users').value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      if(kasus == '' && pesan == '' && kasus == '' && level == '' && users == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Data masih kosong!'
        });
      }else if(pesan == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Pesan Tiket harap diisi!'
        });
      }else if(judul == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Judul Kasus harap diisi!'
        });
      }else if(kasus == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap pilih Kategori Kasus!'
        });
      }else if(level == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap pilih Level Kasus!'
        });
      }else if(users == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap pilih PIC!'
        });
      }else if(status == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap pilih Status Tiket!'
        });
      }else{
        var formData = new FormData();
        formData.append('photo', $('#photo')[0].files[0]);         
        formData.append('judul', judul);      
        formData.append('pesan', pesan);      
        formData.append('kasus', kasus);      
        formData.append('status', status);      
        formData.append('level', level);      
        formData.append('users', users);      
        formData.append('nomor_tiket', nomor_tiket);    
        formData.append([csrfName], csrfHash); 
        $.ajax({ 
          url:"<?php echo base_url()?>admin/tiket/tiket_ubah_proses",
          method:"post",
          dataType: 'JSON', 
          data:formData,
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

            if (data.none) {
              Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: data.none
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/tiket");
              });
            }

            if (data.sukses) {
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/tiket");
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
