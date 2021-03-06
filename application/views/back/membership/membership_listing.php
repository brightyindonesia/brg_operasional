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
      <div id="message-validasi-diterima"></div>
      <div class="row">
        <!-- <div class="col-sm-3">


          <div class="box box-primary no-border">
            <div class="box-header">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group"><label>Provinsi</label>
                    <?php echo form_dropdown('provinsi', $get_all_provinsi, 'semua', $provinsi) ?>
                  </div>
                  <div class="form-group"><label>Kabupaten</label>
                    <?php echo form_dropdown('kabupaten', '', 'semua', $kabupaten) ?>
                  </div>

                  <div class="form-group"><label>Total Belanja</label>
                    <div class="row">
                      <div class="col-lg-6 col-md-12"><input type="number" name="belanja_min" id="belanja_min" class="form-control float-left" placeholder="Minimum Total Belanja"></div>
                      <div class="col-lg-6 col-md-12"><input type="number" name="belanja_max" id="belanja_max" class="form-control float-right" placeholder="Maximum Total Belanja"></div>
                    </div>
                  </div>
                  <div class="form-group"><label>Quantity</label>
                  <div class="row">
                    <div class="col-lg-6 col-md-12"><input type="number" name="qty_min" id="qty_min" class="form-control float-left" placeholder="Minimum Total Quantity"></div>
                      <div class="col-lg-6 col-md-12"><input type="number" name="qty_max" id="qty_max" class="form-control float-right" placeholder="Maximum Total Quantity"></div>
                  </div>
                  </div>
                  <div class="form-group"><label>Pilih Tanggal</label>
                    <input type="text" name="periodik" class="form-control float-right" id="range-date">
                  </div>
                </div>
              </div>
            </div>
          </div>




        </div> -->

        <div class="col-sm-12">
          <div class="nav-tabs-custom">
            <div class="active impor-table tab-pane" id="impor-table">
              <?php include('tab_content/table_membership_insight.php'); ?>
            </div>
          </div>

        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- JQUERY Slider -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- DataTables -->
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <script>
    // ============= END HAPUS DIPILIH DATA PENJUALAN TANGGAL IMPOR =====================



    // ============= END HAPUS DIPILIH DATA PENJUALAN TANGGAL PENJUALAN =====================


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
        startDate: moment(),
        endDate: moment(),
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
      $('#table-listing-membership').DataTable().ajax.reload();
      $('#table-penjualan-penjualan').DataTable().ajax.reload();
    }


    function export_penjualan(trigger) {
      var kurir = document.getElementById("kurir").value;
      var toko = document.getElementById("toko").value;
      var resi = document.getElementById("resi").value;
      var status = document.getElementById("status").value;
      var periodik = document.getElementById("range-date").value;

      if (resi == '') {
        resi = 'null';
      }

      window.open("<?php echo base_url() ?>admin/keluar/export_keluar_penjualan/" + trigger + "/" + kurir + "/" + toko + "/" + resi + "/" + status + "/" + periodik, +"_self");
    }

    function export_customer_insight(trigger) {
        provinsi = $('#provinsi').val() ? $('#provinsi').val() : '';
        kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : '';
        belanja_max = $('#belanja_max').val() ? $('#belanja_max').val() : '';
        belanja_min = $('#belanja_min').val() ? $('#belanja_min').val() : '';
        qty_min = $('#qty_min').val() ? $('#qty_min').val() : '';
        qty_max = $('#qty_max').val() ? $('#qty_max').val() : '';
        periodik = $('#range-date').val() ? $('#range-date').val() : '';

        window.open(`<?= base_url('admin/keluar/export_customer_insight') ?>?provinsi=${provinsi}&kabupaten=${kabupaten}&belanja_max=${belanja_max}&belanja_min=${belanja_min}&qty_min=${qty_min}&qty_max=${qty_max}&periodik=${periodik}`, '_self');
        
      }




    $(document).ready(function() {

      $('#tier').on('change', function() {
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

      // ================== START DATATABLE UNTUK TABEL PENJUALAN KHUSUS IMPOR ======================

      // Detail Datatable Ajax
      function format_impor(d) {
        // `d` is the original data object for the row
        return d.detail;


      }

      

      var table_customer_insight = $('#table-listing-membership').DataTable({
        "iDisplayLength": 50,
        "deferRender": true,
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "bAutoWidth": false,
        'ajax': {
          'url': '<?php echo base_url() ?>admin/Membership/get_data_membership_listing',
          'data': function(d) {

            d.tier = $('#tier').val();
            // dasbor_list_count();
            // dasbor_list_count_penjualan();
          }
        },
        'columns': [{
            data: 'no',
          },
          {
            data: "nama_penerima"
          },

          {
            data: "hp_penerima"
          },
          {
            data: "total_harga_jual"
          },
          {
            data: "poin"
          },
          // { data: "total_harga"},

          // { data: "hapus"},
        ],
        columnDefs: [{
            className: 'text-center',
            targets: [0]
          },
          {
            className: 'text-left',
            targets: [2]
          },
          {
            orderable: false,
            targets: [0]
          }
        ]
      });

      // Add event listener for opening and closing details
      $('#table-listing-membership').on('click', 'td.details-control-impor', function() {
        var tr = $(this).closest('tr');
        var row = table_customer_insight.row(tr);

        if (row.child.isShown()) {
          // This row is already open - close it
          row.child.hide();
          tr.removeClass('shown-impor');
        } else {
          // Open this row
          row.child(format_impor(row.data())).show();
          tr.addClass('shown-impor');
        }
      });

      // ================== END DATATABLE UNTUK TABEL PENJUALAN KHUSUS IMPOR ======================

      // ================== START DATATABLE UNTUK TABEL PENJUALAN KHUSUS IMPOR ======================

      // Detail Datatable Ajax
      function format_penjualan(d) {
        // `d` is the original data object for the row
        return '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">' +
          '<tr>' +
          '<td width="20%">Tanggal Impor</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.created + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Tanggal Diterima</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.tgl_diterima + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Status Transaksi</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.status + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Total Jual</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.total_jual + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Total Harga</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.total_harga + '</td>' +
          '</tr>' +

          '<tr>' +
          '<td width="20%">Total HPP</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.total_hpp + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Ongkir</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.ongkir + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Margin</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.margin + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Selisih Margin</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.selisih_margin + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Jumlah Diterima</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.jumlah_diterima + '</td>' +
          '</tr>' +
          '</table>' +
          '<hr width="100%">' +
          '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">' +
          '<tr>' +
          '<td width="20%">Nama Penerima</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.nama_penerima + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Nomor Handphone</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.hp_penerima + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Provinsi</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.provinsi + '</td>' +
          '</tr>' +
          '<tr>' +
          '<td width="20%">Kota / Kabupaten</td>' +
          '<td width="1%">:</td>' +
          '<td>' + d.kabupaten + '</td>' +
          '</tr>' +
          '</table>' + d.detail;


      }


      $('#provinsi').on('change', function() {
        var provinsi = $(this).val();
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        if (provinsi != '') {
          // console.log(provinsi);
          $.ajax({
            url: "<?php echo base_url() ?>admin/keluar/get_id_provinsi",
            type: "post",
            data: {
              'provinsi': provinsi,
              [csrfName]: csrfHash
            },
            dataType: 'JSON',
            success: function(data) {
              $('#kabupaten').html(data);
              // $("#provinsi option[value='']").remove();
              // $("#example3").find('tbody').empty(); //add this line
            },
            error: function() {
              alert('Error ....');
            }
          });
        } else {
          $('#kabupaten').html('<option value="">- Pilih Kabupaten -</option>');
        }
      });

      // ================== END DATATABLE UNTUK TABEL PENJUALAN KHUSUS IMPOR ======================

    });

    $(function() {
      $("#slider").slider();
    });
  </script>

</div>
<!-- ./wrapper -->

</body>

</html>