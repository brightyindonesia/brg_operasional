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
                  <div class="form-group"><label>Kategori Rating</label>
                    <?php echo form_dropdown('kategori', $get_all_kategori, 'semua', $kategori) ?>
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
              <h3 id="total-rating"></h3>

              <p>Total Data Rating</p>
            </div>
            <div class="icon">
              <i class="fa fa-star"></i>
            </div>
          </div>

          <!-- small box -->
          <div id="mean-bg" class="small-box">
            <div class="inner">
              <h3 id="mean-rating"></h3>

              <p>Rata-Rata Rating</p>
            </div>
            <div class="icon">
              <i class="fa fa-star"></i>
            </div>
          </div>

          <div class="box box-primary no-border">
            <div class="box-body">
              <label>Rata-Rata Kategori Rating</label>
              <div class="table-responsive">
                <table id="table-mean" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">Kategori</th>
                      <th style="text-align: center">Jumlah</th>
                      <th style="text-align: center">Rata-Rata</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <!-- /.box-body -->
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
                  <table id="table-rating" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th style="text-align: center">#</th>
                        <th style="text-align: center">Tanggal</th>
                        <th style="text-align: center">Nomor Pesanan</th>
                        <th style="text-align: center">Rating</th>
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
                  url: "<?php echo base_url()?>admin/rating/hapus_dipilih",
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
                        window.location.replace("<?php echo base_url()?>admin/rating");
                      });
                    }

                    if (data.sukses) {
                      Toast.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: data.sukses,
                      }).then(function(){
                        window.location.replace("<?php echo base_url()?>admin/rating");
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
      $('#table-rating').DataTable().ajax.reload();
      $('#table-mean').DataTable().ajax.reload();
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

      var kategori = document.getElementById("kategori").value;
      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
              url:'<?php echo base_url()?>admin/rating/dasbor_list_count/',
              type: "post",
              data: {kategori: kategori, periodik: periodik, [csrfName]: csrfHash},
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
                document.getElementById("total-rating").innerHTML=data.total;
                if (data.mean < 3) {
                  document.getElementById("mean-bg").className += " bg-red";
                  document.getElementById("mean-rating").innerHTML=data.mean+ ' / 5';
                }else if (data.mean < 4) {
                  document.getElementById("mean-bg").className += " bg-yellow";
                  document.getElementById("mean-rating").innerHTML=data.mean+ ' / 5';
                }else if (data.mean >= 4 ) {
                  document.getElementById("mean-bg").className += " bg-green";
                  document.getElementById("mean-rating").innerHTML=data.mean+ ' / 5';
                }
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
    $('#kategori').on('change', function(){
      refresh_table();
    });

    $('#range-date').on('change', function(){
      refresh_table();
    });

    // TABEL MEAN KATEGORI RATING
    $('#table-mean').DataTable({
        "iDisplayLength":50,
        'processing': false,
        'serverSide': true,
        "responsive": true,
        "autoWidth": false,
        "bAutoWidth": false,
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        'ajax': {
            'url': '<?php echo base_url()?>admin/rating/get_data_mean',
            'data': function (d) {
              d.kategori = $('#kategori').val();
              d.periodik = $('#range-date').val();
              dasbor_list_count();
            }
        },
        'columns': [
            { data: "nama_kategori_rating"},
            { data: "jumlah"},
            { data: "avg"},
        ],
        columnDefs: [
          { className: 'text-center', 
            targets: [1, 2] 
          },
          { className: 'text-left', 
            targets: [0] 
          }
        ]
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

    var table = $('#table-rating').DataTable({
          "iDisplayLength":50,
          'processing': false,
          'serverSide': true,
          "responsive": true,
          "autoWidth": false,
          "bAutoWidth": false,
          'ajax': {
              'url': '<?php echo base_url()?>admin/rating/get_data_rating',
              'data': function (d) {
                d.kategori = $('#kategori').val();
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
              { data: "nomor_pesanan"},
              { data: "rating"},
              { data: "action"},
              { data: "select"},
          ],
          columnDefs: [
            { className: 'text-center', 
              targets: [0, 1, 2, 3, 4, 5] 
            },
            {
              orderable: false, 
              targets: [4, 5]
            }
          ]
      });

      // Add event listener for opening and closing details
      $('#table-rating').on('click', 'td.details-control', function () {
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
