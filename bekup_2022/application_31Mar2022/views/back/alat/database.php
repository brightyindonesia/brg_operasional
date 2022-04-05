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
      <div class="row">
        <div class="col-sm-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Backup dan Restore Database</h3>
            </div>

            <div class="box-body">
              <div class="form-group">
                <a href="<?php echo $backup_db_action ?>" class="btn btn-primary"><i class="fa fa-database"></i> <?php echo $btn_backup ?></a>
              </div>
              <div class="form-group"><label>Upload File Database (*)</label>
                <input type="file" name="restore_db" id="restore_db" class="form-control" accept=".sql">
              </div>
              <div class="form-group">
                <button type="submit" name="button" id="restoredb" class="btn btn-primary"><i class="fa fa-database"></i> <?php echo $btn_restore ?></button>
                <button type="reset" name="button" class="btn btn-danger"><i class="fa fa-refresh"></i> <?php echo $btn_reset ?></button>
              </div>
              <!-- /.box-body -->
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
  <script>
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
          url:"<?php echo base_url()?>admin/alat/restore_db",
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
          } 
        });
      }
    });
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
