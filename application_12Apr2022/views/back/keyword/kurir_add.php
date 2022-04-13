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
        <li><?php echo $module_kurir ?></li>
        <li class="active"><?php echo $page_title ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php echo validation_errors() ?>
      <div class="box box-primary">
        <?php 
          // echo form_open($action) 
        ?>
          <div class="box-body">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group"><label>Nama Kurir (*)</label>
                  <?php echo form_dropdown('kurir', $get_all_kurir, '', $kurir_nama); ?>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group"><label>Keyword Kurir: (*)</label>
                  <br>
                    <?php 
                        // if ($arr_keys == '') {
                        //   $keys = '';
                        // }else{
                        //   $keys = implode(",", $arr_keys);
                        // }
                        echo form_input($keys_kurir) 
                    ?>
                </div>
              </div>
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" id="btn-simpan" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
            <a href="<?php echo base_url('admin/keyword/kurir') ?>" class="btn btn-info"><i class="fa fa-table"></i> Back to Data</a>
          </div>
          <!-- /.box-body -->
        <!-- <?php echo form_close() ?> -->
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
  <script>
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
    
    $("#keys-kurir").tagsInput();

    $('#btn-simpan').click(function(e){
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

        var keys = document.getElementById('keys-kurir').value;
        var kurir = document.getElementById('nama-kurir').value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        if (keys == '') {
          Toast.fire({
            icon: 'error',
            title: 'Perhatian!',
            text: 'Keyword Kurir Tidak Boleh Kosong!'
          })
        }else if (kurir == '') {
          Toast.fire({
            icon: 'error',
            title: 'Perhatian!',
            text: 'Nama Kurir Tidak Boleh Kosong!'
          })
        }else{
          $.ajax({ 
              url:"<?php echo base_url()?>admin/keyword/kurir_tambah_proses",
              method:"post",
              dataType: 'JSON', 
              data:{keys: keys, kurir:kurir, [csrfName]: csrfHash},
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
                    window.location.replace("<?php echo base_url()?>admin/keyword/kurir");
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
