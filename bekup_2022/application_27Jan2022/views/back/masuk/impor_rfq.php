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
        <p style="font-size: 16px">Sebelum memasukan Data Import Request For Quotation <b>WAJIB</b> melakukan <b>Backup Database</b> terlebih dahulu!!</p>
      </div>
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php echo validation_errors() ?>
      <div class="row">
        <div class="col-sm-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#rfq" data-toggle="tab">Import Request For Quotation</a></li>
              <li><a href="#backup" data-toggle="tab">Backup Database</a></li>
            </ul>
            <div class="tab-content">
              <div class="active rfq tab-pane" id="rfq">
                <?php include('tab_content/content_import_rfq.php'); ?>                
              </div>
              <div class="tab-pane" id="backup">
                <?php include('tab_content/content_import_backup.php'); ?>  
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
  <script type="text/javascript">
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
