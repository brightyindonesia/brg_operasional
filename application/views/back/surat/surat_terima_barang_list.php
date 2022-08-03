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
      <?php if ($this->session->flashdata('message')) {
        echo $this->session->flashdata('message');
      } ?>
      <div class="row">
        <div class="col-sm-3">
          <div class="box box-primary">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group"><label>Nama PIC QC</label>
                    <?php echo form_dropdown('warehouse', $get_all_warehouse, '', $warehouse) ?>
                  </div>

                  <div class="form-group"><label>Pilih Tanggal</label>
                    <input type="text" name="periodik" class="form-control float-right" id="range-date">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- small box
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3 id="total-retur"></h3>

              <p>Total Surat Jalan</p>
            </div>
            <div class="icon">
              <i class="fa fa-file-text"></i>
            </div>
          </div> -->
        </div>

        <div class="col-sm-9">
          <div class="box box-primary">
            <div class="box-header"><a href="<?php echo $add_action_tb ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $btn_add_tb ?></a> </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table id="table-sj" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">Tanggal Terima</th>
                      <th style="text-align: center">Nomor Surat</th>
                      <th style="text-align: center">Nama Surat</th>
                      <th width="20%" style="text-align: center">Action</th>
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
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>
    // $('#range-date').daterangepicker({
    //     locale: {
    //       format: 'DD/MM/YYYY'
    //     }
    // });
    $('#range-date').daterangepicker({
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
          'This Years': [moment().startOf('years'), moment().endOf('years')],
          'Last Years': [moment().subtract(1, 'years').startOf('years'), moment().subtract(1, 'years').endOf('years')],
        },
        startDate: moment().startOf('years'),
        endDate: moment().endOf('years'),
        // startDate: moment().subtract(29, 'days'),
        // endDate  : moment(),

        locale: {
          format: 'YYYY-MM-DD'
        }
      },

      function(start, end) {
        $('#range-date-full span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
      }
    )

    function refresh_table() {
      $('#table-sj').DataTable().ajax.reload();
    }

    function dasbor_list_count() {
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
        url: '<?php echo base_url() ?>admin/surat/dasbor_list_count_terima_barang',
        type: "post",
        data: {

          [csrfName]: csrfHash
        },
        dataType: 'JSON',
        success: function(data) {
          // console.log(data);
          if (data.validasi) {
            // Toast.fire({
            //   icon: 'error',
            //   title: 'Perhatian!',
            //   text: data.validasi
            // })
            toastr.error(data.validasi)
          } else {
            document.getElementById("total-retur").innerHTML = data.total;
          }

        },
        error: function(data) {
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

    $(document).ready(function() {
      $('#warehouse').on('change', function() {
        refresh_table();
      });

      $('#range-date').on('change', function() {
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



      var table = $('#table-sj').DataTable({
        "iDisplayLength": 50,
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "bAutoWidth": false,
        'ajax': {
          'url': '<?php echo base_url() ?>admin/surat/get_data_surat_terima_barang',
          'data': function(d) {
            d.periodik = $('#range-date').val();
            d.warehouse = $('#warehouse').val();
          }
        },
        'columns': [{
            data: "tanggal"
          },
          {
            data: "nomor_surat_terima_barang"
          },
          {
            data: "nama_surat_terima_barang"
          },

          // { data: "total_harga"},
          {
            data: "action"
          },
          // { data: "hapus"},
        ],
        columnDefs: [{
            className: 'text-center',
            targets: [0, 1, 2]
          },
          {
            className: 'text-left',
            targets: [3]
          }
        ]
        // "fnServerParams": function ( aoData ) {
        //   aoData.push( { "name": "kurir", "value": $('#kurir').val()} );
        //   aoData.push( { "name": "toko", "value": $('#toko').val()} );
        //   aoData.push( { "name": "periodik", "value": $('#range-date').val()} );
        //   aoData.push( { "name": "status", "value": $('#status').val()} );
        //   dasbor_list_count();
        // }
      });

      // Add event listener for opening and closing details
      $('#table-sj').on('click', 'td.details-control', function() {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
          // This row is already open - close it
          row.child.hide();
          tr.removeClass('shown');
        } else {
          // Open this row
          row.child(format(row.data())).show();
          tr.addClass('shown');
        }
      });
    });
  </script>

</div>
<!-- ./wrapper -->

</body>

</html>