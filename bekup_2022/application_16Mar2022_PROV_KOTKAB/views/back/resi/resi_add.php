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
      <div class="row">
        <div class="col-sm-3">
          <div class="info-box">
          <span class="info-box-icon bg-aqua"><i class="fa fa-bolt"></i></span>

          <div class="info-box-content">
            <span class="info-box-text">Total Resi</span>
            <span class="info-box-number" id="total-resi"></span>
          </div>
          <!-- /.info-box-content -->

          <div class="info-box-footer">
            <a href="javascript:void(0)" onclick="tabelScanAdmin('semua');" style="margin-left: 10px;">Click Me <i class="fa fa-eye"></i></a>
          </div>
        </div>
        
        <div class="box box-primary no-border">
            <div class="box-body">
              <div class="form-group"><label>No. Resi (*)</label>
                <?php echo form_input($no_resi) ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-9">
          <div class="row">
            <div class="col-sm-3">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Sudah Diproses</span>
                  <span class="info-box-number" id="total-sudah"></span>
                </div>
                <!-- /.info-box-content -->

                <div class="info-box-footer">
                  <a href="javascript:void(0)" onclick="tabelScanAdmin(2);" style="margin-left: 10px;">Click Me <i class="fa fa-eye"></i></a>
                </div>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-hourglass-2"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Sedang Diproses</span>
                  <span class="info-box-number" id="total-proses"></span>
                </div>
                <!-- /.info-box-content -->

                <div class="info-box-footer">
                  <a href="javascript:void(0)" onclick="tabelScanAdmin(1);" style="margin-left: 10px;">Click Me <i class="fa fa-eye"></i></a>
                </div>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Belum Diproses</span>
                  <span class="info-box-number" id="total-belum"></span>
                </div>
                <!-- /.info-box-content -->

                <div class="info-box-footer">
                  <a href="javascript:void(0)" onclick="tabelScanAdmin(0);" style="margin-left: 10px;">Click Me <i class="fa fa-eye"></i></a>
                </div>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-minus-circle"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Retur</span>
                  <span class="info-box-number" id="total-gagal"></span>
                </div>
                <!-- /.info-box-content -->

                <div class="info-box-footer">
                  <a href="javascript:void(0)" onclick="tabelScanAdmin(3);" style="margin-left: 10px;">Click Me <i class="fa fa-eye"></i></a>
                </div>
              </div>
            </div>
          </div>
          <div class="alert alert-info">
            <h3 style="margin-top: -5px"><b>PERHATIAN!!</b></h3>
            <p style="font-size: 16px">Untuk menampilkan Data pada Tabel silahkan klik '<b>Click Me</b>' untuk setiap Status Resinya</p>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="box box-primary no-border">
                <div class="box-body">
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
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script type="text/javascript">
    window.onload = function() {
      $("#no-resi").focus();
      dasbor_list_count();
      if ($.fn.DataTable.isDataTable("#table-resi-harian")) {
        $('#table-resi-harian').DataTable().clear().destroy();
        $('#table-resi-harian').dataTable().fnDestroy();
      }
    }
    function refresh_table(){
        $('#table-resi-harian').DataTable().ajax.reload();
    }

    function tabelScanAdmin(status) {
      if ($.fn.DataTable.isDataTable("#table-resi-harian")) {
        $('#table-resi-harian').DataTable().clear().destroy();
        $('#table-resi-harian').dataTable().fnDestroy();
      }

      $('#table-resi-harian').DataTable({
          "iDisplayLength":100,
          'processing': true,
          'serverSide': true,
          "responsive": true,
          "autoWidth": false,
          "bAutoWidth": false,
          'ajax': {
              'url': '<?php echo base_url()?>admin/resi/get_data_admin',
              'data': function(d){
                d.status = status;
              },
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
    }

    function dasbor_list_count()
    {
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

        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
                url:'<?php echo base_url()?>admin/resi/dasbor_list_count_admin/',
                type: "post",
                data: {[csrfName]: csrfHash},
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
                  document.getElementById("total-resi").innerHTML=data.total;
                  document.getElementById("total-sudah").innerHTML=data.sudah;
                  document.getElementById("total-proses").innerHTML=data.proses;
                  document.getElementById("total-belum").innerHTML=data.belum;
                  document.getElementById("total-gagal").innerHTML=data.gagal;
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
        return false;
    }

    // $(document).ready( function () {
    //   $('#table-resi-harian').DataTable({
    //       'processing': true,
    //       'serverSide': true,
    //       "responsive": true,
    //       "autoWidth": false,
    //       "bAutoWidth": false,
    //       'ajax': {
    //           'url': '<?php echo base_url()?>admin/resi/get_data_admin'
    //       },
    //       'columns': [
    //           { data: "no"},
    //           { data: "tanggal"},
    //           { data: "nomor_pesanan"},
    //           { data: "nomor_resi"},
    //           { data: "nama_kurir"},
    //           { data: "status"},
    //       ],
    //       columnDefs: [
    //         { className: 'text-left', 
    //           targets: [2, 3, 4] 
    //         },
    //         { className: 'text-center', 
    //           targets: [0, 1, 5] 
    //         }
    //       ],
    //   });
    // });

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
      if ($.fn.DataTable.isDataTable("#table-resi-harian")) {
        $('#table-resi-harian').DataTable().clear().destroy();
        $('#table-resi-harian').dataTable().fnDestroy();
      }

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
              }else if (data.validasi_dobel) {
                toastr.warning(data.validasi_dobel)
              }else{
                // refresh_table();
                toastr.success(data.sukses)
                dasbor_list_count();
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
