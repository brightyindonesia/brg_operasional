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
        <p style="font-size: 16px">Sebelum memasukan Data Import Purchase Order <b>WAJIB</b> melakukan <b>Backup Database</b> terlebih dahulu!!</p>
      </div>
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php echo validation_errors() ?>
      <div class="row">
        <div class="col-sm-2">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Filter Import</h3>
            </div>

            <div class="box-body">
              <div class="form-group">
                <div class="radio">
                  <label>
                  <input type="radio" name="filter_keyword" id="filter-keyword" value="no" checked>
                    Don't use keywords
                  </label>
                </div>

                <div class="radio">
                  <label>
                  <input type="radio" name="filter_keyword" id="filter-keyword" value="yes">
                    Using Keywords
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-10">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#po" data-toggle="tab">Import Purchase Order</a></li>
              <li><a href="#backup" data-toggle="tab">Backup Database</a></li>
            </ul>
            <div class="tab-content">
              <div class="active po tab-pane" id="po">
                <?php include('tab_content/content_import_po.php'); ?>
              </div>

              <div class="tab-pane" id="backup">
                <?php include('tab_content/content_import_backup.php'); ?>  
              </div>               
            </div>
          </div>
        </div>
      </div>

      <div id="message-validasi-tabel"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <script type="text/javascript">
    $('#import-po').click(function(e){
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

      var imports = document.getElementById('impor_po');
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
      csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      // alert(panjangArray);
      
      if(imports.files['length'] == 0){
        Toast.fire({
          icon: 'error',
          title: 'Terjadi Kesalahan!. File Import PO harus diisi!'
        });
      }else{
        var formData = new FormData();
        formData.append('impor_po', $('#impor_po')[0].files[0]);  
        // formData.append('keyword', $('#filter-keyword').val());      
        formData.append('keyword', $('input[type=radio][name=filter_keyword]:checked').attr('value'));      
        formData.append([csrfName], csrfHash); 

        $("#modal-proses").modal('show');
        $.ajax({ 
          url:"<?php echo base_url()?>admin/masuk/proses_import_po_new",
          method:"post",
          dataType: 'JSON', 
          // data:{img: JS_image,vendor:vendor, penerima:penerima, sku: sku, kategori: kategori, ongkir: ongkir, remarks: remarks, nomor_request: nomor_request, dt_id: JS_id, dt_qty: JS_qty, dt_harga: JS_harga, dt_jml: JS_jumlah, dt_diskon: JS_diskon, dt_pajak: JS_pajak, length: panjangArray, [csrfName]: csrfHash},
          data: formData,
          contentType: false,
          processData: false,
          success:function(data)  {  
            // alert(data);
            $("#modal-proses").modal('hide');
            document.getElementById("impor_po").value = null;
            if (data.validasi) {
              $("#modal-proses").modal('hide');
              if (data.pesan_error) {
                $('#message-validasi-tabel').html('<div class="alert alert-danger">'+data.pesan_error+'</div>');
              }
              document.getElementById("impor_po").value = null;
              Toast.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: data.validasi
              })
            }

            if (data.sukses) {
              $("#modal-proses").modal('hide');
              if (data.pesan_error) {
                $('#message-validasi-tabel').html('<div class="alert alert-danger">'+data.pesan_error+'</div>');
              }
              document.getElementById("impor_po").value = null;
              // Toast.fire({
              //   icon: 'success',
              //   title: 'Sukses!',
              //   text: data.sukses,
              // }).then(function(){
              //   window.location.replace("<?php echo base_url()?>admin/dashboard");
              // });
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              })
              // .then(function(){
              //   window.location.replace("<?php echo base_url()?>admin/keluar/data_sementara");
              // });
            }
            
          },
          error: function(data){
            console.log(data.responseText);
            $("#modal-proses").modal('hide');
            document.getElementById("impor_po").value = null;
          } 
        });
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
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
