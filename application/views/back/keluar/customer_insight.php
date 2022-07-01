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
        <div class="col-sm-3">


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
                      <div class="col-lg-6 col-md-12"><input type="number" name="belanja_min" id="belanja_min" class="form-control float-left" placeholder="Minimum Belanja"></div>
                      <div class="col-lg-6 col-md-12"><input type="number" name="belanja_max" id="belanja_max" class="form-control float-right" placeholder="Maximum Belanja"></div>
                    </div>
                  </div>
                  <div class="form-group"><label>Quantity</label>
                    <div class="row">
                      <div class="col-lg-6 col-md-12"><input type="number" name="qty_min" id="qty_min" class="form-control float-left" placeholder="Minimum Quantity"></div>
                      <div class="col-lg-6 col-md-12"><input type="number" name="qty_max" id="qty_max" class="form-control float-right" placeholder="Maximum Quantity"></div>
                    </div>
                  </div>
                  <div class="form-group"><label>Frequency</label>
                    <div class="row">
                      <div class="col-lg-6 col-md-12"><input type="number" name="freq_min" id="freq_min" class="form-control float-left" placeholder="Minimum Frequency"></div>
                      <div class="col-lg-6 col-md-12"><input type="number" name="freq_max" id="freq_max" class="form-control float-right" placeholder="Maximum Frequency"></div>
                    </div>
                  </div>

                  <div class="form-group"><label>Pilih Tanggal</label>
                    <input type="text" name="periodik" class="form-control float-right" id="range-date">
                  </div>
                  <div class="form-group"><label>Pilih Tanggal Terahir Order</label>
                    <input type="text" name="terakhir_order" class="form-control float-right" id="range-date-order">
                  </div>
                </div>
              </div>
            </div>
          </div>




        </div>

        <div class="col-sm-9">
          <div class="nav-tabs-custom">
            <div class="active impor-table tab-pane" id="impor-table">
              <?php include('tab_content/table_customer_insight.php'); ?>
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

    $('#range-date-order').daterangepicker({
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
        autoUpdateInput: false,

        locale: {
          format: 'YYYY-MM-DD'
        }
      },
      

      // function(start, end) {
      //   // $('#range-date-full span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
      // }
    )

    function refresh_table() {
      $('#table-customer-insight').DataTable().ajax.reload();
      $('#table-penjualan-penjualan').DataTable().ajax.reload();
    }

    function export_customer_insight(trigger) {
      provinsi = $('#provinsi').val() ? $('#provinsi').val() : '';
      kabupaten = $('#kabupaten').val() ? $('#kabupaten').val() : '';
      belanja_max = $('#belanja_max').val() ? $('#belanja_max').val() : '';
      belanja_min = $('#belanja_min').val() ? $('#belanja_min').val() : '';
      qty_min = $('#qty_min').val() ? $('#qty_min').val() : '';
      qty_max = $('#qty_max').val() ? $('#qty_max').val() : '';
      freq_min = $('#freq_min').val() ? $('#freq_min').val() : '';
      freq_max = $('#freq_max').val() ? $('#freq_max').val() : '';
      periodik = $('#range-date').val() ? $('#range-date').val() : '';
      terakhir_order = $('#range-date-order').val() ? $('#range-date-order').val() : '';

      window.open(`<?= base_url('admin/keluar/export_customer_insight') ?>?provinsi=${provinsi}&kabupaten=${kabupaten}&belanja_max=${belanja_max}&belanja_min=${belanja_min}&qty_min=${qty_min}&qty_max=${qty_max}&freq_min=${freq_min}&freq_max=${freq_max}&periodik=${periodik}`, '_self');

    }





    $(document).ready(function() {

      $('#range-date').on('change', function() {
        refresh_table();
      });

      $('#range-date-order').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        refresh_table()
      });

      $('#provinsi').on('change', function() {
        refresh_table();
      });

      $('#kabupaten').on('change', function() {
        refresh_table();
      });

      $('#belanja_max').on('change', function() {
        refresh_table();
      });

      $('#belanja_min').on('change', function() {
        refresh_table();
      });


      $('#qty_min').on('change', function() {
        refresh_table();
      });

      $('#qty_max').on('change', function() {
        refresh_table();
      });


      $('#freq_min').on('change', function() {
        refresh_table();
      });

      $('#freq_max').on('change', function() {
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

      var table_customer_insight = $('#table-customer-insight').DataTable({
        "iDisplayLength": 50,
        "deferRender": true,
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false,
        "bAutoWidth": false,
        'ajax': {
          'url': '<?php echo base_url() ?>admin/keluar/get_data_customer_insight',
          'data': function(d) {

            d.periodik = $('#range-date').val();
            d.provinsi = $('#provinsi').val();
            d.kabupaten = $('#kabupaten').val();
            d.belanja_min = $('#belanja_min').val();
            d.belanja_max = $('#belanja_max').val();
            d.qty_min = $('#qty_min').val();
            d.qty_max = $('#qty_max').val();
            d.freq_min = $('#freq_min').val();
            d.freq_max = $('#freq_max').val();
            d.terakhir_order = $('#range-date-order').val();

            // dasbor_list_count();
            // dasbor_list_count_penjualan();
          }
        },
        'columns': [{
            "className": 'details-control-impor',
            "orderable": false,
            "data": null,
            "defaultContent": ''
          },
          {
            data: "nama_penerima"
          },

          {
            data: "hp_penerima"
          },
          {
            data: "total_qty"
          },
          {
            data: "jumlah_pesanan"
          },
          {
            data: "total_harga_jual"
          },
          {
            data: "tgl_terakhir_order"
          }
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
      $('#table-customer-insight').on('click', 'td.details-control-impor', function() {
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