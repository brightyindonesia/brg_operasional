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

      <div class="box box-primary">
        <div class="box-header">
          <a href="<?php echo $add_action ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $btn_add ?></a>
          <a id="btn-ekspor" class="btn btn-success"><i class="fa fa-file-excel-o"></i> <?php echo $btn_export ?></a> 
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table id="datatable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="text-align: center">No</th>
                  <th style="text-align: center">Nama Gudang</th>
                  <th style="text-align: center">Alamat Gudang</th>
                  <th style="text-align: center">Action</th>
                  <th style="text-align: center">
                    <input type="checkbox" id="master">
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach($get_all as $data){
                  // action
                  $edit = '<a href="'.base_url('admin/gudang/ubah/'.$data->id_gudang).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
                  $delete = '<a href="'.base_url('admin/gudang/hapus/'.$data->id_gudang).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                ?>
                  <tr>
                    <td style="text-align: center"><?php echo $no++ ?></td>
                    <td style="text-align: center"><?php echo $data->nama_gudang ?></td>
                    <td style="text-align: center"><?php echo $data->alamat_gudang ?></td>
                    <td style="text-align: center"><?php echo $edit ?> <?php echo $delete ?></td>
                    <td style="text-align: center">
                      <input type="checkbox" class="sub_chk" data-id="<?php echo $data->id_gudang ?>">
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <th style="text-align: center">No</th>
                  <th style="text-align: center">Nama Gudang</th>
                  <th style="text-align: center">Alamat Gudang</th>
                  <th style="text-align: center">Action</th>
                  <th style="text-align: center">
                    #
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          <button type="button" style="float: right;" id="btn-delete-pilih" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus Dipilih</button>
        </div>
      </div>
      <!-- /.box -->
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
  $(document).ready( function () {
    $('#datatable').DataTable();
    
    $('#master').on('click', function(e) {
     if($(this).is(':checked',true))  
     {
        $(".sub_chk").prop('checked', true);  
     } else {  
        $(".sub_chk").prop('checked',false);  
     }  
    });

    $('#btn-ekspor').click(function(){
      var check = confirm("Are you sure you ?");  
      if(check == true){
        window.open("<?php echo base_url() ?>admin/gudang/export_gudang");
      }
    });

    $('#btn-delete-pilih').on('click', function(e) {
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

          var allVals = [];  
          $(".sub_chk:checked").each(function() {  
              allVals.push($(this).attr('data-id'));
          });  

          if(allVals.length <=0)  
          {  
              alert("Data not selected!");  
          }  else {  

              var check = confirm("Are you sure you want to delete this row?");  
              if(check == true){  

                  var join_selected_values = allVals.join(","); 
                  var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
                  csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
                  $.ajax({
                      url: "<?php echo base_url()?>admin/gudang/hapus_dipilih",
                      type: 'POST',
                      dataType: 'JSON', 
                      data: {ids: join_selected_values, [csrfName]: csrfHash},
                      success: function (data) {
                        // console.log(data);
                        if (data.sukses) {
                          Toast.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: data.sukses,
                          }).then(function(){
                            window.location.replace("<?php echo base_url()?>admin/gudang");
                          });
                        }
                        // console.log(data);
                        // $(".sub_chk:checked").each(function() {  
                        //     $(this).parents("tr").remove();
                        // });
                        // alert("Item Deleted successfully.");
                      },  
                      error: function (data) {
                          alert(data.responseText);
                      }
                  });

                // $.each(allVals, function( index, value ) {
                //     $('table tr').filter("[data-row-id='" + value + "']").remove();
                // });
              }  
          }  
      });
  });
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
