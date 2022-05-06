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
                <div class="form-group"><label>Masukan No. Resi atau No. Pesanan (*)</label>
                  <?php echo form_input($nomor) ?>
                </div>    
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <div id="hasil_tiket" style="display: none;" class="card-body">
                  <table width="100%" border="0" class="table table-bordered table-responsive">
                    <tr>
                      <td width="20%" style="background-color: #f5f5f5;font-weight: bold;">Nomor Pesanan</td>
                      <td width="1%">:</td>
                      <td>
                        <input type="hidden" id="nomor-pesanan">
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
                      <td style="background-color: #f5f5f5;font-weight: bold;">Kategori Rating</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo form_dropdown('', $get_all_kategori, '', $kategori_rating_id) ?>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">Masukan Rating</td>
                      <td width="1%">:</td>
                      <td>
                        <input id="rating-ulasan" type="number" class="rating" min="0" max="5" step="1">
                      </td>
                    </tr>

                    <tr>
                      <td style="background-color: #f5f5f5;font-weight: bold;">PIC</td>
                      <td width="1%">:</td>
                      <td>
                        <div>
                          <?php echo form_dropdown('users', $get_all_users, '', $users) ?>
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
                        <img id="preview" width="350px"/>
                      </td>
                    </tr>
                  </table>

                  <div class="table-responsive">
                    <table id="table-rating" class="table table-bordered table-striped">
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
                    <button type="submit" id="tiket_tambah" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
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
  <!-- Rating Bootstrap -->
  <script src="<?php echo base_url('assets/plugins/') ?>rating-bootstrap/js/star-rating.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>rating-bootstrap/js/star-rating.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>rating-bootstrap/themes/krajee-fa/theme.js" type="text/javascript"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>rating-bootstrap/themes/krajee-svg/theme.js" type="text/javascript"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>rating-bootstrap/themes/krajee-gly/theme.js" type="text/javascript"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>rating-bootstrap/themes/krajee-uni/theme.js" type="text/javascript"></script>

  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>

  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>select2/dist/css/select2-flat-theme.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>select2/dist/js/select2.full.min.js"></script>
  <script type="text/javascript">
    $("#kategori-rating-id").select2({
      placeholder: "- Pilih Kategori -",
      theme: "flat",
      closeOnSelect: false
    });

    window.onload = function() {
      $("#nomor").focus();
    }

    $('#rating-ulasan').rating();

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

    $('#tiket_tambah').click(function(e){
      e.preventDefault();

      var pesanan = document.getElementById('nomor-pesanan').value;
      var rating = document.getElementById('rating-ulasan').value;
      var photo = document.getElementById('photo');
      var kategori = $("#kategori-rating-id").val();
      var users = document.getElementById('users').value;
      var created = <?php echo $this->session->userdata('id_users'); ?>;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      if(kategori.length == 0 && users == '' && rating == 0) {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Data masih kosong!'
        });
      }else if(kategori.length == 0) {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap pilih Kategori Rating!'
        });
      }else if(rating == 0) {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap isi Rating Pesanan!'
        });
      }else if(users == '') {
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Harap pilih PIC!'
        });
      }else if(pesanan == ''){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. Nomor Pesanan masih kosong!'
        });
      }else if(photo.files['length'] == 0){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. File Gambar harus diisi!'
        });
      }else{
        var formData = new FormData();
        formData.append('photo', $('#photo')[0].files[0]);  
        // formData.append('keyword', $('#filter-keyword').val());      
        formData.append('nomor_pesanan', pesanan);       
        formData.append('rating', rating);       
        formData.append('kategori', kategori);         
        formData.append('users', users);          
        formData.append('created', created);          
        formData.append([csrfName], csrfHash); 
        $.ajax({ 
          url:"<?php echo base_url()?>admin/rating/rating_tambah_proses",
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

            if (data.sukses) {
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/rating");
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

    function cekNomor() {
      var nomor = document.getElementById("nomor").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $("#modal-proses").modal('show');
      $.ajax({
            url: "<?php echo base_url()?>admin/rating/scan_proses",
            type: "post",
            data: {'nomor': nomor, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  

              if (data.exist) {
                Toast.fire({
                  icon: 'error',
                  title: 'Perhatian!',
                  text: data.exist
                }).then(function(){
                  window.location.replace("<?php echo base_url()?>admin/rating/ubah/"+data.id_rating);
                });
              }

              if (data.validasi) {
                toastr.error(data.validasi)
                document.getElementById("hasil_tiket").style.display = "none";
                document.getElementById("nomor-pesanan").value = "";
                document.getElementById("pesanan").innerHTML = "";
                document.getElementById("nomor-resi").innerHTML = "";
                document.getElementById("nama-kurir").innerHTML = "";
                document.getElementById("nama-toko").innerHTML = "";
                document.getElementById("nama-penerima").innerHTML = "";
                document.getElementById("alamat-penerima").innerHTML = "";
                document.getElementById("kabupaten").innerHTML = "";
                document.getElementById("provinsi").innerHTML = "";
                document.getElementById("hp-penerima").innerHTML = "";
                $("#modal-proses").modal('hide');
              }

              if(data.sukses){
                toastr.success(data.sukses)
                document.getElementById("hasil_tiket").style.display = "block";
                document.getElementById("nomor-pesanan").value = data.nomor_pesanan;
                document.getElementById("pesanan").innerHTML = data.nomor_pesanan;
                document.getElementById("nomor-resi").innerHTML = data.nomor_resi;
                document.getElementById("nama-kurir").innerHTML = data.nama_kurir;
                document.getElementById("nama-toko").innerHTML = data.nama_toko;
                document.getElementById("nama-penerima").innerHTML = data.nama_penerima;
                document.getElementById("alamat-penerima").innerHTML = data.alamat_penerima;
                document.getElementById("kabupaten").innerHTML = data.kabupaten;
                document.getElementById("provinsi").innerHTML = data.provinsi;
                document.getElementById("hp-penerima").innerHTML = data.hp_penerima;
                $('#table-rating').DataTable({
                    'aaData' : data.table,          // this is input parameter for your function
                    "bDestroy": true,
                    'autoWidth': false,
                    'columns': [
                        { data: 'no'},
                        { data: 'nama_produk'},
                        { data: 'qty'},
                    ],
                    columnDefs: [
                      { className: 'text-center', 
                        targets: [0, 1, 2] 
                      }
                    ],
                });
                $("#modal-proses").modal('hide');
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
