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
            <div class="form-group"><label>No. Resi (*)</label>
              <?php echo form_input($no_resi) ?>
            </div>

            <div class="table-responsive">
              <table id="table-resi-harian" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th style="text-align: center">No.</th>
                    <th style="text-align: center">Tanggal</th>
                    <th style="text-align: center">Nomor Pesanan</th>
                    <th style="text-align: center">Nomor Resi</th>
                    <th style="text-align: center">Nama Kurir</th>
                    <th style="text-align: center">Status Resi</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <div class="box-footer">
          </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script type="text/javascript">
    window.onload = function() {
      $("#no-resi").focus();
    }
    function refresh_table(){
        $('#table-resi-harian').DataTable().ajax.reload();
    }

    $(document).ready( function () {
      $('#table-resi-harian').DataTable({
          'processing': true,
          'serverSide': true,
          'ajax': {
              'url': '<?php echo base_url()?>admin/resi/get_data_admin'
          },
          'columns': [
              { data: "no"},
              { data: "tanggal"},
              { data: "nomor_pesanan"},
              { data: "nomor_resi"},
              { data: "nama_kurir"},
              { data: "status"},
          ],
          columnDefs: [
            { className: 'text-left', 
              targets: [2, 3, 4] 
            },
            { className: 'text-center', 
              targets: [0, 1, 5] 
            }
          ],
      });
    });

    const Toast = Swal.mixin({
        toast: false,
        position: 'center',
        showConfirmButton: false,
        // confirmButtonColor: '#86ccca',
        timer: 950,
        timerProgressBar: false,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })

    function cekResi() {
      var resi = document.getElementById("no-resi").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
            url: "<?php echo base_url()?>admin/resi/tambah_proses",
            type: "post",
            data: {'resi': resi, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  
              // console.log(data);
              if (data.validasi) {
                // Toast.fire({
                //   icon: 'error',
                //   title: 'Perhatian!',
                //   text: data.validasi
                // })
                toastr.error(data.validasi)
              }else{
                refresh_table();
                toastr.success(data.sukses)
                // Toast.fire({
                //   icon: 'success',
                //   title: 'Sukses!',
                //   text: data.sukses,
                // })
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
      $('#no-resi').val('');
    }
  </script>
</div>
<!-- ./wrapper -->

</body>
</html>
