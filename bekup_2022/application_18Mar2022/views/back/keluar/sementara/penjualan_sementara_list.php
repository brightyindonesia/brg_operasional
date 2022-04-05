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
          <div class="box box-primary no-border">
            <div class="box-header">
              <div class="row">
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
            <div class="box-header">
              <a id="btn-eksport-penjualan" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Data</a>
              <a id="btn-sinkron-total-harga" class="btn btn-warning"><i class="fa fa-refresh"></i> <?php echo $btn_sinkron_total_harga ?></a>
              <a id="btn-migrate-data" class="btn btn-primary"><i class="fa fa-rocket"></i> Migrate all data to Sales Data</a>
              <a href="<?php echo base_url('admin/keluar/hapusAll_sementara') ?>" class="btn btn-danger" onClick="return confirm('Are you sure delete all data ?');"><i class="fa fa-trash"></i> Delete all data</a>
            </div>
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
            $("#modal-proses").modal('show');
            var join_selected_values = allVals.join(","); 
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            $.ajax({
                url: "<?php echo base_url()?>admin/keluar/penjualan_hapus_dipilih_sementara",
                type: 'POST',
                dataType: 'JSON', 
                data: {ids: join_selected_values, [csrfName]: csrfHash},
                success: function (data) {
                  // console.log(data);
                  if (data.sukses) {
                    $("#modal-proses").modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: 'Sukses!',
                      text: data.sukses,
                    }).then(function(){
                      window.location.replace("<?php echo base_url()?>admin/keluar/data_sementara");
                    });
                  }
                  // console.log(data);
                  // $(".sub_chk:checked").each(function() {  
                  //     $(this).parents("tr").remove();
                  // });
                  // alert("Item Deleted successfully.");
                },  
                error: function (data) {
                    $("#modal-proses").modal('hide');
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

  $('#btn-eksport-penjualan').click(function(){
    var kurir = document.getElementById("kurir").value;
    var toko = document.getElementById("toko").value;
    var resi = document.getElementById("resi").value;
    var status = document.getElementById("status").value;
    var periodik = document.getElementById("range-date").value;

    window.open("<?php echo base_url() ?>admin/keluar/export_keluar_sementara_penjualan/"+kurir+"/"+toko+"/"+resi+"/"+status+"/"+periodik,+"_self");
  });

  $('#btn-sinkron-total-harga').on('click', function(e) {
    const Toast = Swal.mixin({
          toast: false,
          position: 'center',
          showConfirmButton: false,
          // confirmButtonColor: '#86ccca',
          // timer: 3000,
          timerProgressBar: false,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })
    
    var check = confirm("Are you sure you want to sync data with total price?");  
    if(check == true){  
      $("#modal-proses").modal('show');

      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
          url: "<?php echo base_url()?>admin/keluar/sinkron_total_harga_sementara",
          type: 'POST',
          dataType: 'JSON', 
          data: {periodik: periodik, [csrfName]: csrfHash},
          success: function (data) {
            // console.log(data);
            if (data.sukses) {
              $("#modal-proses").modal('hide'); 
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/keluar/data_sementara");
              });
            }
            // console.log(data);
            // $(".sub_chk:checked").each(function() {  
            //     $(this).parents("tr").remove();
            // });
            // alert("Item Deleted successfully.");
          },  
          error: function (data) {
              console.log(data.responseText);
          }
      });
    }
  });

  $('#btn-migrate-data').on('click', function(e) {
    const Toast = Swal.mixin({
          toast: false,
          position: 'center',
          showConfirmButton: false,
          // confirmButtonColor: '#86ccca',
          // timer: 3000,
          timerProgressBar: false,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        })
    
    var check = confirm("Are you sure you ? Migrated data cannot be returned!");  
    if(check == true){  
      $("#modal-proses").modal('show');

      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
          url: "<?php echo base_url()?>admin/keluar/migrate_data_sementara",
          type: 'POST',
          dataType: 'JSON', 
          data: {periodik: periodik, [csrfName]: csrfHash},
          success: function (data) {
            // console.log(data);
            if (data.sukses) {
              $("#modal-proses").modal('hide'); 
              Toast.fire({
                icon: 'success',
                title: 'Sukses!',
                text: data.sukses,
              }).then(function(){
                window.location.replace("<?php echo base_url()?>admin/keluar/data_penjualan");
              });
            }
            // console.log(data);
            // $(".sub_chk:checked").each(function() {  
            //     $(this).parents("tr").remove();
            // });
            // alert("Item Deleted successfully.");
          },  
          error: function (data) {
              console.log(data.responseText);
          }
      });
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

        var kurir = document.getElementById("kurir").value;
        var toko = document.getElementById("toko").value;
        var resi = document.getElementById("resi").value;
        var status = document.getElementById("status").value;
        var periodik = document.getElementById("range-date").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
                url:'<?php echo base_url()?>admin/keluar/dasbor_list_count_sementara/',
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

  $('#master').on('click', function(e) {
   if($(this).is(':checked',true))  
   {
      $(".sub_chk").prop('checked', true);  
   } else {  
      $(".sub_chk").prop('checked',false);  
   }  
  });

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
                '<td width="20%">Tanggal Diterima</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.tgl_diterima+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Status Transaksi</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.status+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Total Jual</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.total_jual+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Total Harga</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.total_harga+'</td>'+
            '</tr>'+

            '<tr>'+
                '<td width="20%">Total HPP</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.total_hpp+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Ongkir</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.ongkir+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Margin</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.margin+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Selisih Margin</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.selisih_margin+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Jumlah Diterima</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.jumlah_diterima+'</td>'+
            '</tr>'+
        '</table>'+
        '<hr width="100%">'+
        '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'+
            '<tr>'+
                '<td width="20%">Nama Penerima</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.nama_penerima+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Nomor Handphone</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.hp_penerima+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Provinsi</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.provinsi+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td width="20%">Kota / Kabupaten</td>'+
                '<td width="1%">:</td>'+
                '<td>'+d.kabupaten+'</td>'+
            '</tr>'+
        '</table>'+d.detail;


    }

    var table = $('#table-penjualan').DataTable({
        "iDisplayLength":50,
        "processing": false,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "bAutoWidth": false,
        'ajax': {
            'url': '<?php echo base_url()?>admin/keluar/get_data_sementara',
            'data': function(d){
              d.kurir = $('#kurir').val();
              d.toko = $('#toko').val();
              d.resi = $('#resi').val();
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
            { data: "nomor_pesanan"},
            { data: "nama_toko"},
            { data: "nama_kurir"},
            { data: "nomor_resi"},
            // { data: "total_harga"},
            { data: "action"},
            { data: "select"},
            // { data: "hapus"},
        ],
        columnDefs: [
          { className: 'text-center', 
            targets: [0, 1, 3, 4, 5, 6] 
          },
          { className: 'text-left', 
            targets: [2] 
          },
          {
            orderable: false, 
            targets: [6,7]
          }
        ]
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
