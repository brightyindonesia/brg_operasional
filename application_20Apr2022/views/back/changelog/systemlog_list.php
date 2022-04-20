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
        <div class="col-sm-3">
          <div class="box box-primary no-border">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <div class="form-group"><label>Pilih Tanggal</label>
                      <input type="text" name="range-date" class="form-control float-right" id="range-date-data">
                    </div> 

                    <div class="form-group"><label>Pilih Users</label>
                      <?php echo form_dropdown('users', $get_all_users, '', $users) ?>
                    </div>                   
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-9">
          <div class="box box-primary">
            <div class="box-header">
              <a href="<?php echo base_url('admin/changelog/systemlog_delete') ?>" class="btn btn-danger" onClick="return confirm('Are you sure?');" ><i class="fa fa-trash-o" style="margin-right: 5px;"></i>Hapus Semua Data</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table id="table-systemlog" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">No</th>
                      <th style="text-align: center; width: 150px">Time</th>
                      <th style="text-align: center">Action</th>
                      <th style="text-align: center">Created By</th>
                      <th style="text-align: center">IP Address</th>
                      <th style="text-align: center">User Agent</th>
                    </tr>
                  </thead>
                </table>
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
  $('#range-date-data').daterangepicker(
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
        $('#table-systemlog').DataTable().ajax.reload();
    }

    $('#range-date-data').on('change', function(){
      refresh_table();
    });

    $('#users').on('change', function(){
      refresh_table();
    });

  // Detail Datatable Ajax

    var table = $('#table-systemlog').DataTable({
        "iDisplayLength":50,
        "processing": false,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "bAutoWidth": false,
        'ajax': {
            'url': '<?php echo base_url()?>admin/changelog/get_data_systemlog',
            'data': function(d){
              d.periodik = $('#range-date-data').val();
              d.users = $('#users').val();
            }
        },
        'columns': [
            { data: "no"},
            { data: "tanggal"},
            { data: "content"},
            { data: "created_by"},
            { data: "ip_address"},
            { data: "user_agent"},
            // { data: "hapus"},
        ],
        columnDefs: [
          { className: 'text-center', 
            targets: [0, 1, 2, 3, 4, 5] 
          },
          {
            orderable: false, 
            targets: [0]
          }
        ]
    });
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
