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
      <div class="row">
        <div class="col-sm-3">
          <div class="box box-primary">
            <div class="box-header">
              <div class="row">
                <?php echo form_open_multipart($action_impor) ?>
                <div class="col-sm-12">
                  <div class="form-group"><label>Upload File Impor Keyword Kurir (*)</label>
                    <input type="file" name="import" id="import" class="form-control" accept=".xlsx,.xls">
                  </div>

                  <div class="form-group">
                    <button type="submit" onClick="return confirm('Ketika melakukan Import semua data akan hilang. Apakah Anda yakin ?');" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
                    <a href="<?php echo $format_kurir ?>" class="btn btn-info"><i class="fa fa-file-excel-o"></i> <?php echo $btn_import ?></a>
                  </div>
                </div>
                <?php echo form_close(); ?>
              </div>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3 id="total-kurir"></h3>

              <p>Total Kurir</p>
            </div>
            <div class="icon">
              <i class="fa fa-shopping-cart"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-primary">
            <div class="inner">
              <h3 id="total-keyword"></h3>

              <p>Total Keyword Kurir</p>
            </div>
            <div class="icon">
              <i class="fa fa-key"></i>
            </div>
          </div>
        </div>

        <div class="col-sm-9">
          <div class="box box-primary">
            <div class="box-header">
              <a href="<?php echo $add_action_kurir ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $btn_add ?></a> 
              <a href="<?php echo $export_action_kurir ?>" class="btn btn-success"><i class="fa fa-file-excel-o"></i> <?php echo $btn_export ?></a> 
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table id="table-keyword" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">#</th>
                      <th style="text-align: center">Nama Kurir</th>
                      <th style="text-align: center">Action</th>
                      <th width="1%" style="text-align: center">
                        <input type="checkbox" id="master">
                      </th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <button type="button" style="float: right;" id="btn-delete-pilih" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus Dipilih</button>
            </div>
          </div>
          <!-- /.box -->    
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
                url:'<?php echo base_url()?>admin/keyword/dasbor_list_kurir_count/',
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
                  document.getElementById("total-kurir").innerHTML=data.kurir;
                  document.getElementById("total-keyword").innerHTML=data.keyword;
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
                      url: "<?php echo base_url()?>admin/keyword/kurir_hapus_dipilih",
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
                            window.location.replace("<?php echo base_url()?>admin/keyword/kurir");
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

    // Detail Datatable Ajax
    function format ( d ) {
        // `d` is the original data object for the row
        return d.detail+'</table>';


    }

    var table = $('#table-keyword').DataTable({
        "iDisplayLength":50,
        "processing": false,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "bAutoWidth": false,
        'ajax': {
            'url': '<?php echo base_url()?>admin/keyword/get_data_kurir',
            'data': function(d){
              dasbor_list_count();
            }
        },
        'columns': [
            {
                "className"     : 'details-control',
                "orderable"     :  false,
                "data"          :  null,
                "defaultContent":  ''
            },
            { data: "kurir"},
            { data: "action"},
            { data: "select"},
            // { data: "hapus"},
        ],
        columnDefs: [
          { className: 'text-center', 
            targets: [0, 1, 2, 3] 
          },
          {
            orderable: false, 
            targets: [2,3]
          }
        ]
    });

    // Add event listener for opening and closing details
    $('#table-keyword').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });
  });
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
