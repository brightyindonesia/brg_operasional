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
      <!-- BARIS DATA MASTER -->
      <div class="row">
        <div class="col-sm-3">
          <div class="box box-primary">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group"><label>Pilih Tanggal</label>
                    <input type="text" name="periodik" class="form-control float-right" id="range-date">
                  </div>

                  <div class="form-group"><label>Pilih Usertype</label>
                    <?php echo form_dropdown('usertype', $get_all_usertype, '', $usertype) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3 id="total-report-crm"></h3>

              <p>Total Report</p>
            </div>
            <div class="icon">
              <i class="fa fa-file-pdf-o"></i>
            </div>
          </div>
        </div>

        <div class="col-sm-9">
          <div class="box box-primary">
            <div class="box-header">
              
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="table-crm" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">#</th>
                      <th style="text-align: center">Tanggal</th>
                      <th style="text-align: center">Nama</th>
                      <th style="text-align: center">Report Data</th>
                      <th style="text-align: center">Action</th>
                      <th width="1%" style="text-align: center">
                        <input type="checkbox" id="master">
                      </th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="box-footer">
              <button type="button" style="float: right;" id="btn-delete-pilih" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus Dipilih</button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    $('#range-date').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          'This Years'  : [moment().startOf('years'), moment().endOf('years')],
          'Last Years'  : [moment().subtract(1, 'years').startOf('years'), moment().subtract(1, 'years').endOf('years')],
        },
        startDate: moment().startOf('years'),
        endDate  : moment().endOf('years'),
        // startDate: moment().subtract(29, 'days'),
        // endDate  : moment(),

        locale: {
          format: 'YYYY-MM-DD'
        }
      },

      function (start, end) {
        $('#range-date-full span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
      }
    )

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

        var periodik = document.getElementById("range-date").value;
        var usertype = document.getElementById("usertype").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
                url:'<?php echo base_url()?>admin/laporan/dasbor_list_count/',
                type: "post",
                data: {periodik:periodik, usertype: usertype, [csrfName]: csrfHash},
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
                  document.getElementById("total-report-crm").innerHTML=data.report;
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
                      url: "<?php echo base_url()?>admin/laporan/master_hapus_dipilih",
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
                            window.location.replace("<?php echo base_url()?>admin/laporan/master");
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

    function refresh_table(){
        $('#table-crm').DataTable().ajax.reload();
    }

    $('#range-date').on('change', function(){
      refresh_table();
    });

    $('#usertype').on('change', function(){
      refresh_table();
    });

    // Detail Datatable Ajax
    function format ( d ) {
        // `d` is the original data object for the row
        return d.detail+'</table>';
    }

    var table = $('#table-crm').DataTable({
        "iDisplayLength":50,
        "processing": false,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "bAutoWidth": false,
        'ajax': {
            'url': '<?php echo base_url()?>admin/laporan/get_data',
            'data': function(d){
              dasbor_list_count();
              d.usertype = $('#usertype').val();
              d.periodik = $('#range-date').val();
            }
        },
        'columns': [
            {
                "className"     : 'details-control',
                "orderable"     :  false,
                "data"          :  null,
                "defaultContent":  ''
            },
            { data: "tanggal"},
            { data: "nama"},
            { data: "report"},
            { data: "action"},
            { data: "select"},
            // { data: "hapus"},
        ],
        columnDefs: [
          { className: 'text-center', 
            targets: [0, 1, 2, 3, 4] 
          },
          {
            orderable: false, 
            targets: [4,5]
          }
        ]
    });

    // Add event listener for opening and closing details
    $('#table-crm').on('click', 'td.details-control', function () {
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
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
