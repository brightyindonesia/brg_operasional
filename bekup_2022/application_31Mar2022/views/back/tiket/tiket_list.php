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
      <div class="row">
        <div class="col-sm-3">
          <div class="box box-primary">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group"><label>Kategori Kasus</label>
                    <?php echo form_dropdown('kasus', $get_all_kasus, 'semua', $kasus) ?>
                  </div>

                  <div class="form-group"><label>Level Kasus</label>
                    <?php echo form_dropdown('level', $get_all_level, 'semua', $level) ?>
                  </div>

                  <div class="form-group"><label>Status Tiket</label>
                    <?php echo form_dropdown('status', $get_all_status, 'semua', $status) ?>
                  </div>

                  <div class="form-group"><label>Pilih Tanggal</label>
                    <input type="text" name="periodik" class="form-control float-right" id="range-date">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3 id="total-tiket"></h3>

              <p>Total Tiket</p>
            </div>
            <div class="icon">
              <i class="fa fa-ticket"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3 id="total-terbuka"></h3>

              <p>Tiket Terbuka</p>
            </div>
            <div class="icon">
              <i class="fa fa-check"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3 id="total-pending"></h3>

              <p>Tiket Pending</p>
            </div>
            <div class="icon">
              <i class="fa fa-times"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-primary">
            <div class="inner">
              <h3 id="total-ditutup"></h3>

              <p>Tiket Ditutup</p>
            </div>
            <div class="icon">
              <i class="fa fa-minus-circle"></i>
            </div>
          </div>
        </div>

        <div class="col-sm-9">
          <div class="box box-primary">
            <div class="box-header">
              <a href="<?php echo $add_action ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $btn_add ?></a> 
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                  <table id="table-tiket" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th style="text-align: center">#</th>
                        <th style="text-align: center">Tanggal</th>
                        <th style="text-align: center">Nomor Tiket</th>
                        <th style="text-align: center">Nomor Pesanan</th>
                        <th style="text-align: center">Kategori Kasus</th>
                        <th style="text-align: center">Level Kasus</th>
                        <th style="text-align: center">Status Tiket</th>
                        <th style="text-align: center">Action</th>
                        <th style="text-align: center">
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
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- DataTables -->
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>
  $('#master').on('click', function(e) {
     if($(this).is(':checked',true))  
     {
        $(".sub_chk").prop('checked', true);  
     } else {  
        $(".sub_chk").prop('checked',false);  
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
                  url: "<?php echo base_url()?>admin/tiket/hapus_dipilih",
                  type: 'POST',
                  dataType: 'JSON', 
                  data: {ids: join_selected_values, [csrfName]: csrfHash},
                  success: function (data) {
                    // console.log(data);
                    if (data.none) {
                      Toast.fire({
                        icon: 'error',
                        title: 'Perhatian!',
                        text: data.none
                      }).then(function(){
                        window.location.replace("<?php echo base_url()?>admin/tiket");
                      });
                    }

                    if (data.sukses) {
                      Toast.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: data.sukses,
                      }).then(function(){
                        window.location.replace("<?php echo base_url()?>admin/tiket");
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
        startDate: moment().startOf('month'),
        endDate  : moment().endOf('month'),
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

  function refresh_table(){
      $('#table-tiket').DataTable().ajax.reload();
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

      var kasus = document.getElementById("kasus").value;
      var level = document.getElementById("level").value;
      var status = document.getElementById("status").value;
      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
              url:'<?php echo base_url()?>admin/tiket/dasbor_list_count/',
              type: "post",
              data: {kasus: kasus, level: level, status: status, periodik: periodik, [csrfName]: csrfHash},
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
                document.getElementById("total-tiket").innerHTML=data.total;
                document.getElementById("total-terbuka").innerHTML=data.terbuka;
                document.getElementById("total-pending").innerHTML=data.pending;
                document.getElementById("total-ditutup").innerHTML=data.ditutup;
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

  $(document).ready( function () {
    $('#kasus').on('change', function(){
      refresh_table();
    });

    $('#level').on('change', function(){
      refresh_table();
    });

    $('#status').on('change', function(){
      refresh_table();
    });

    $('#range-date').on('change', function(){
      refresh_table();
    });

    // $('#btn-pilih').click(function(){
    //     var kurir = $('#kurir').val();
    //     if (kurir != '') {
    //         refresh_table();
    //     }else{
    //         $('#table-resi').dataTable().fnReloadAjax();
    //     }
    // });

    // Detail Datatable Ajax
    function format ( d ) {
        // `d` is the original data object for the row
        // return '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'+
        //     '<tr>'+
        //         '<td width="20%">Status Transaksi</td>'+
        //         '<td width="1%">:</td>'+
        //         '<td>'+d.status+'</td>'+
        //     '</tr>'+
        // '</table>';
        return d.detail;
    }

    var table = $('#table-tiket').DataTable({
          "iDisplayLength":50,
          'processing': false,
          'serverSide': true,
          "responsive": true,
          "autoWidth": false,
          "bAutoWidth": false,
          'ajax': {
              'url': '<?php echo base_url()?>admin/tiket/get_data_tiket',
              'data': function (d) {
                d.kasus = $('#kasus').val();
                d.level = $('#level').val();
                d.periodik = $('#range-date').val();
                d.status = $('#status').val();
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
              { data: "tanggal"},
              { data: "nomor_tiket"},
              { data: "nomor_pesanan"},
              { data: "nama_kategori_kasus"},
              { data: "nama_level_kasus"},
              { data: "nama_status_tiket"},
              { data: "action"},
              { data: "select"},
          ],
          columnDefs: [
            { className: 'text-center', 
              targets: [0, 1, 2, 3, 4, 5, 6, 7, 8] 
            },
            {
              orderable: false, 
              targets: [7,8]
            }
          ]
      });

      // Add event listener for opening and closing details
      $('#table-tiket').on('click', 'td.details-control', function () {
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
