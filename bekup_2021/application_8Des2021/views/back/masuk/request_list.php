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
                  <div class="form-group"><label>Nama Vendor</label>
                    <?php echo form_dropdown('vendor', $get_all_vendor, '', $vendor) ?>
                  </div>

                  <div class="form-group"><label>Jenis Kategori PO</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, '', $kategori) ?>
                  </div>

                  <div class="form-group"><label>Status Request For Quoatation</label>
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
              <h3 id="total-request"></h3>

              <p>Total</p>
            </div>
            <div class="icon">
              <i class="fa fa-history"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3 id="total-sudah"></h3>

              <p>Sudah diproses</p>
            </div>
            <div class="icon">
              <i class="fa fa-check"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3 id="total-proses"></h3>

              <p>Sedang diproses</p>
            </div>
            <div class="icon">
              <i class="fa fa-hourglass-2"></i>
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
            <table id="table-request" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="text-align: center">#</th>
                  <th style="text-align: center">Tanggal</th>
                  <th style="text-align: center">No. Request</th>
                  <th style="text-align: center">Nama Vendor</th>
                  <th style="text-align: center" width="25%">Action</th>
                  <!-- <th style="text-align: center">Action</th> -->
                </tr>
              </thead>
            </table>
            <!-- <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">No</th>
                      <th style="text-align: center">Tanggal</th>
                      <th style="text-align: center">No. Request</th>
                      <th style="text-align: center">Nama Vendor</th>
                      <th style="text-align: center">Nama SKU</th>
                      <th style="text-align: center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; foreach($get_all as $data){
                      // action
                      $print = '<a href="'.base_url('admin/masuk/request_print/'.base64_encode($data->no_request)).'" class="btn btn-sm btn-primary"><i class="fa fa-print"></i></a>';
                      if ($data->status_request == 0) {
                        $forward = '<a href="'.base_url('admin/masuk/request_forward/'.base64_encode($data->no_request)).'" class="btn btn-sm btn-success"><i class="fa fa-send"></i></a>';
                        $edit = '<a href="'.base_url('admin/masuk/request_edit/'.base64_encode($data->no_request)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
                        $delete = '<a href="'.base_url('admin/masuk/request_delete/'.base64_encode($data->no_request)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                      }else{
                        $forward  = '';
                        $edit     = '';
                        $delete   = '';
                      }
                    ?>
                      <tr>
                        <td style="text-align: center"><?php echo $no++ ?></td>
                        <td style="text-align: center"><?php echo $data->tgl_request ?></td>
                        <td style="text-align: center"><?php echo $data->no_request ?></td>
                        <td style="text-align: center"><?php echo $data->nama_vendor ?></td>
                        <td style="text-align: center"><?php echo $data->nama_sku ?></td>
                        <td style="text-align: center"><?php echo $print ?> <?php echo $edit ?> <?php echo $delete ?> <?php echo $forward ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th style="text-align: center">No</th>
                      <th style="text-align: center">Tanggal</th>
                      <th style="text-align: center">No. Request</th>
                      <th style="text-align: center">Nama Vendor</th>
                      <th style="text-align: center">Nama SKU</th>
                      <th style="text-align: center">Action</th>
                    </tr>
                  </tfoot>
                </table> -->
              </div>
            </div>
            <!-- /.box-body -->
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
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>
  $(document).ready( function () {
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
      },
      startDate: moment(),
          endDate  : moment(),
          // startDate: moment().subtract(29, 'days'),
          // endDate  : moment(),

      locale: {
        format: 'YYYY-MM-DD'
      }
    },

    function (start, end) {
      $('#range-date-full span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
    });

    // $('#datatable').DataTable();

    function refresh_table(){
      $('#table-request').DataTable().ajax.reload();
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

      var vendor = document.getElementById("vendor").value;
      var kategori = document.getElementById("kategori").value;
      var status = document.getElementById("status").value;
      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
              url:'<?php echo base_url()?>admin/masuk/dasbor_list_count_request/',
              type: "post",
              data: {vendor: vendor, status: status, kategori:kategori, periodik: periodik, [csrfName]: csrfHash},
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
                document.getElementById("total-request").innerHTML=data.total;
                document.getElementById("total-sudah").innerHTML=data.sudah;
                document.getElementById("total-proses").innerHTML=data.proses;
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

  $('#kategori').on('change', function(){
    refresh_table();
  });

  $('#vendor').on('change', function(){
    refresh_table();
  });

  $('#status').on('change', function(){
    refresh_table();
  });

  $('#range-date').on('change', function(){
    refresh_table();
  });

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

  var table = $('#table-request').DataTable({
        "iDisplayLength":50,
        'processing': true,
        'serverSide': true,
        'ajax': {
            'url': '<?php echo base_url()?>admin/masuk/get_data_request',
            'data': function(d){
              d.vendor = $('#vendor').val();
              d.kategori = $('#kategori').val();
              d.status = $('#status').val();
              d.periodik = $('#range-date').val();
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
            { data: "no_request"},
            { data: "nama_vendor"},
            { data: "action"},
            // { data: "hapus"},
        ],
        columnDefs: [
          // { className: 'text-left', 
          //   targets: [2, 3, 4] 
          // },
          { className: 'text-center', 
            targets: [0, 1, 2, 3, 4] 
          }
        ]
        // "fnServerParams": function ( aoData ) {
        //   aoData.push( { "name": "vendor", "value": $('#vendor').val()} );
        //   aoData.push( { "name": "kategori", "value": $('#kategori').val()} );
        //   aoData.push( { "name": "status", "value": $('#status').val()} );
        //   aoData.push( { "name": "periodik", "value": $('#range-date').val()} );
        //   dasbor_list_count();
        // }
    });

    // Add event listener for opening and closing details
    $('#table-request').on('click', 'td.details-control', function () {
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
