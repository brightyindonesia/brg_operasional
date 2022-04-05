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
                <?php echo form_open_multipart($action_impor) ?>
                <div class="col-sm-12">
                  <div class="form-group"><label>Upload File Impor Jumlah Diterima (*)</label>
                    <input type="file" name="impor_diterima" id="impor_diterima" class="form-control" accept=".xlsx,.xls">
                  </div>

                  <div class="form-group">
                    <button type="submit" name="button" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $btn_submit ?></button>
                    <a href="<?php echo $format_diterima ?>" class="btn btn-info"><i class="fa fa-file-excel-o"></i> <?php echo $btn_import ?></a>
                  </div>
                </div>
                <?php echo form_close(); ?>

                <div class="col-sm-12">
                  <div class="form-group"><label>Toko</label>
                    <?php echo form_dropdown('toko', $get_all_toko, '', $toko) ?>
                  </div>

                  <div class="form-group"><label>Kurir</label>
                    <?php echo form_dropdown('kurir', $get_all_kurir, '', $kurir) ?>
                  </div>

                  <div class="form-group"><label>Resi</label>
                    <?php echo form_dropdown('resi', $get_all_resi, 'semua', $resi) ?>
                  </div>

                  <div class="form-group"><label>Status Transaksi</label>
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
              <h3 id="total-penjualan"></h3>

              <p>Total Penjualan</p>
            </div>
            <div class="icon">
              <i class="fa fa-cloud-upload"></i>
            </div>
          </div>
          
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3 id="total-pending"></h3>

              <p>Pending Payment</p>
            </div>
            <div class="icon">
              <i class="fa fa-hourglass-2"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-primary">
            <div class="inner">
              <h3 id="total-transfer"></h3>

              <p>Transfer</p>
            </div>
            <div class="icon">
              <i class="fa fa-money"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3 id="total-diterima"></h3>

              <p>Diterima</p>
            </div>
            <div class="icon">
              <i class="fa fa-check"></i>
            </div>
          </div>

          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3 id="total-retur"></h3>

              <p>Retur</p>
            </div>
            <div class="icon">
              <i class="fa fa-exchange"></i>
            </div>
          </div>
        </div>

        <div class="col-sm-9">
          <div class="box box-primary">
            <div class="box-header"><a href="<?php echo $add_action ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $btn_add ?></a> </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table id="table-penjualan" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">#</th>
                      <th style="text-align: center">Tanggal</th>
                      <th width="27%" style="text-align: center">No. Pesanan</th>
                      <th style="text-align: center">Toko</th>
                      <th style="text-align: center">Kurir</th>
                      <th style="text-align: center">No. Resi</th>
                      <th style="text-align: center">Action</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <!-- <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">No</th>
                      <th style="text-align: center">Tanggal</th>
                      <th style="text-align: center">No. Pesanan</th>
                      <th style="text-align: center">Toko</th>
                      <th style="text-align: center">Kurir</th>
                      <th style="text-align: center">No. Resi</th>
                      <th style="text-align: center">Total Harga</th>
                      <th style="text-align: center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; foreach($get_all as $data){
                      // action
                      $edit = '<a href="'.base_url('admin/keluar/ubah/'.$data->nomor_pesanan).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
                      $delete = '<a href="'.base_url('admin/keluar/hapus/'.$data->nomor_pesanan).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                    ?>
                      <tr>
                        <td style="text-align: center"><?php echo $no++ ?></td>
                        <td style="text-align: center"><?php echo date('d/m/Y', strtotime($data->tgl_penjualan)) ?></td>
                        <td style="text-align: center"><?php echo $data->nomor_pesanan ?> <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i><?php echo $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan); ?> Produk</span></td>
                        <td style="text-align: center"><?php echo $data->nama_toko ?></td>
                        <td style="text-align: center"><?php echo $data->nama_kurir ?></td>
                        <td style="text-align: center"><?php echo $data->nomor_resi ?></td>
                        <td style="text-align: center"><?php echo $data->total_harga  ?></td>
                        <td style="text-align: center"><?php echo $edit ?> <?php echo $delete ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th style="text-align: center">No</th>
                      <th style="text-align: center">Tanggal</th>
                      <th style="text-align: center">No. Pesanan</th>
                      <th style="text-align: center">Toko</th>
                      <th style="text-align: center">Kurir</th>
                      <th style="text-align: center">No. Resi</th>
                      <th style="text-align: center">Total Harga</th>
                      <th style="text-align: center">Action</th>
                    </tr>
                  </tfoot>
                </table>
              </div> -->
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
      }
    )

  function refresh_table(){
      $('#table-penjualan').DataTable().ajax.reload();
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

        var kurir = document.getElementById("kurir").value;
        var toko = document.getElementById("toko").value;
        var resi = document.getElementById("resi").value;
        var status = document.getElementById("status").value;
        var periodik = document.getElementById("range-date").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
                url:'<?php echo base_url()?>admin/keluar/dasbor_list_count/',
                type: "post",
                data: {status: status, kurir: kurir, toko: toko, resi: resi, periodik: periodik, [csrfName]: csrfHash},
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
                  document.getElementById("total-penjualan").innerHTML=data.total;
                  document.getElementById("total-pending").innerHTML=data.pending;
                  document.getElementById("total-transfer").innerHTML=data.transfer;
                  document.getElementById("total-diterima").innerHTML=data.diterima;
                  document.getElementById("total-retur").innerHTML=data.retur;
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
    $('#kurir').on('change', function(){
      refresh_table();
    });

    $('#toko').on('change', function(){
      refresh_table();
    });

    $('#resi').on('change', function(){
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
        return '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'+
            '<tr>'+
                '<td width="20%">Tanggal Impor</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.created+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Status Transaksi</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.status+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Total Harga</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.total_harga+'</td>'+
            '</tr>'+
        '</table>'+d.detail;


    }

    var table = $('#table-penjualan').DataTable({
          "iDisplayLength":50,
          "processing": true,
          // "serverSide": true,
          'ajax': {
              'url': '<?php echo base_url()?>admin/keluar/ajax_list',
              'dataSrc': ''
          },
          'columns': [
              {
                  "className"     : 'details-control',
                  "orderable"     :  false,
                  "data"          :  null,
                  "defaultContent":  ''
              },
              { data: "tanggal"},
              { data: "nomor_pesanan"},
              { data: "nama_toko"},
              { data: "nama_kurir"},
              { data: "nomor_resi"},
              // { data: "total_harga"},
              { data: "action"},
              // { data: "hapus"},
          ],
          columnDefs: [
            { className: 'text-center', 
              targets: [0, 1, 3, 4, 5, 6] 
            },
            { className: 'text-left', 
              targets: [2] 
            }
          ],
          "fnServerParams": function ( aoData ) {
            aoData.push( { "name": "kurir", "value": $('#kurir').val()} );
            aoData.push( { "name": "toko", "value": $('#toko').val()} );
            aoData.push( { "name": "resi", "value": $('#resi').val()} );
            aoData.push( { "name": "periodik", "value": $('#range-date').val()} );
            aoData.push( { "name": "status", "value": $('#status').val()} );
            dasbor_list_count();
          }
      });

    // Add event listener for opening and closing details
    $('#table-penjualan').on('click', 'td.details-control', function () {
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
