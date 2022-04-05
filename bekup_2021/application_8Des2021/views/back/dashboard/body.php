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
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?php echo $page_title ?></li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="box box-primary">
        <div class="box-header">
          <div class="form-group"><label>Pilih Tanggal</label>
            <input type="text" name="periodik" class="form-control float-right" id="range-date-full">
          </div>
        </div>
      </div>
      <?php $this->load->view('back/dashboard/header'); ?>

      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php 
        if ($this->session->userdata('usertype') == 1 || $this->session->userdata('usertype') == 2) {
      ?>
      <?php $this->load->view('back/dashboard/record'); ?>
      <?php 
        }
      ?>

      <div class="row">
        <div class="col-sm-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#impor" data-toggle="tab">Tanggal Diimpor</a></li>
              <li><a href="#penjualan" data-toggle="tab">Tanggal Penjualan</a></li>
            </ul>
            <div class="tab-content">
              <div class="active impor tab-pane" id="impor">
                <?php include('tab_content/content_dashboard_impor.php'); ?>                
              </div>

              <div class="tab-pane" id="penjualan">  
                <?php include('tab_content/content_dashboard_penjualan.php'); ?>  
              </div>       
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- Highcharts -->
  <!-- <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>highcharts.js.map"></script> -->
  <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>highcharts.js"></script>
  <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>exporting.js"></script>
  <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>export-data.js"></script>
  <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>accessibility.js"></script>
  <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>data.js"></script>
  <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>drilldown.js"></script>
  <!-- date-range-picker -->
  <script src="<?php echo base_url('assets/plugins/') ?>moment/min/moment.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
  <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
  <!-- Plugin untuk nampilin export Datatable -->
  <!-- <script src="<?php echo base_url('assets/plugins/') ?>datatables.net/button/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables.net/button/dataTables.buttons.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables.net/button/buttons.html5.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables.net/button/buttons.print.min.js"></script>  
  <script src="<?php echo base_url('assets/plugins/') ?>datatables.net/button/jszip.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables.net/button/pdfmake.min.js"></script>
  <script src="<?php echo base_url('assets/plugins/') ?>datatables.net/button/vfs_fonts.js"></script> -->
  <script type="text/javascript">
    window.onload = function() {
      refresh_table();
      refresh_chart();
      referesh_matriks();
    }

    function refresh_table() {
      $('#table-repeat-order').DataTable().ajax.reload();
      $('#table-repeat-order-penjualan').DataTable().ajax.reload();
      $('#table-produk-status').DataTable().ajax.reload();
      $('#table-bahan-status').DataTable().ajax.reload();
    }

    function referesh_matriks() {
      dasbor_count_status_penjualan();
      dasbor_count_status_resi();
    }

    function refresh_chart() {
      dasbor_total();
      dasbor_total_penjualan();
      pie_prokur();
      pie_prokur_penjualan();
      line_income();
      line_income_penjualan();
      bar_produk();
      bar_produk_penjualan();
      bar_kurir();
      bar_kurir_penjualan();
      dasbor_jenis_toko();
      dasbor_jenis_toko_penjualan();
      bar_pesanan();
      bar_pesanan_penjualan();
      bar_sku();
      bar_sku_penjualan();
    }

    /* Fungsi formatRupiah */
    function formatRupiah(bilangan) {
      var number_string = bilangan.toString(),
      split = number_string.split(','),
      sisa  = split[0].length % 3,
      rupiah  = split[0].substr(0, sisa),
      ribuan  = split[0].substr(sisa).match(/\d{1,3}/gi);
          
      if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }
      rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;

      return 'Rp. '+rupiah;
    }

    $('#btn-eksport-repeat').click(function(){

      var periodik = document.getElementById("range-date-full").value;

      window.open("<?php echo base_url() ?>admin/dashboard/export_repeat/"+periodik,+"_self");
    });

    $('#btn-eksport-repeat-penjualan').click(function(){

      var periodik = document.getElementById("range-date-full").value;

      window.open("<?php echo base_url() ?>admin/dashboard/export_repeat_penjualan/"+periodik,+"_self");
    });

    //Date range as a button
    $('#range-date-full').daterangepicker(
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

    $('#range-date-full').on('change', function(){
      refresh_table();
      refresh_chart();
      referesh_matriks();
    });
    // ======== Dasbor ==========
    function dasbor_count_status_penjualan()
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
       
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
                url:'<?php echo base_url()?>admin/dashboard/dasbor_list_count_penjualan/',
                type: "post",
                data: {[csrfName]: csrfHash},
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
                  document.getElementById("jumlah-pending-penjualan").innerHTML=data.pending;
                  document.getElementById("jumlah-transfer-penjualan").innerHTML=data.transfer;
                  document.getElementById("jumlah-diterima-penjualan").innerHTML=data.diterima;
                  document.getElementById("jumlah-retur-penjualan").innerHTML=data.retur;
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

    function dasbor_count_status_resi()
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
       
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
                url:'<?php echo base_url()?>admin/dashboard/dasbor_list_count_resi/',
                type: "post",
                data: {[csrfName]: csrfHash},
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
                  document.getElementById("jumlah-belum-resi").innerHTML=data.belum;
                  document.getElementById("jumlah-sedang-resi").innerHTML=data.sedang;
                  document.getElementById("jumlah-sudah-resi").innerHTML=data.sudah;
                  document.getElementById("jumlah-retur-resi").innerHTML=data.retur;
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
    // ======== End Dasbor ======

    // ======== Tabel ============
    $('#table-produk-status').DataTable({
        "iDisplayLength":5,
        "paging":   false,
        "ordering": false,
        "info":     false,
        "searching": false,
        "processing": false,
        "serverSide": true,
        "autoWidth": false,
        'ajax': {
            'url': '<?php echo base_url()?>admin/dashboard/get_data_produk_status'
        },
        'columns': [
            { data: "sku"},
            { data: "sub_sku"},
            { data: "nama_produk"},
            { data: "status"},
            { data: "stok"},
            // { data: "hapus"},
        ],
        columnDefs: [
          { className: 'text-center', 
            targets: [3, 4] 
          },
          { className: 'text-left', 
            targets: [0, 1, 2] 
          },
        ],
    });

    $('#table-bahan-status').DataTable({
        "iDisplayLength":5,
        "paging":   false,
        "ordering": false,
        "info":     false,
        "searching": false,
        "processing": false,
        "serverSide": true,
        "autoWidth": false,
        'ajax': {
            'url': '<?php echo base_url()?>admin/dashboard/get_data_bahan_status'
        },
        'columns': [
            { data: "sku"},
            { data: "nama_bahan"},
            { data: "status"},
            { data: "stok"},
            // { data: "hapus"},
        ],
        columnDefs: [
          { className: 'text-center', 
            targets: [0, 2, 3] 
          },
          { className: 'text-left', 
            targets: [1] 
          }
        ],
    });

    $('#table-repeat-order').DataTable({
          "iDisplayLength":10,
          "processing": false,
          "serverSide": true,
          "autoWidth": false,
          'ajax': {
              'url': '<?php echo base_url()?>admin/dashboard/get_data_repeat',
              'data': function(d){
                d.periodik = $('#range-date-full').val();
              }
          },
          'columns': [
              { data: "no"},
              { data: "nama_penerima"},
              { data: "provinsi"},
              { data: "kabupaten"},
              { data: "hp_penerima"},
              { data: "alamat"},
              { data: "repeat"},
              // { data: "hapus"},
          ],
          columnDefs: [
            { className: 'text-center', 
              targets: [0, 1, 2, 3, 4, 5, 6] 
            }
          ],
      });

      $('#table-repeat-order-penjualan').DataTable({
          "iDisplayLength":10,
          "processing": false,
          "serverSide": true,
          "autoWidth": false,
          'ajax': {
              'url': '<?php echo base_url()?>admin/dashboard/get_data_repeat_penjualan',
              'data': function(d){
                d.periodik = $('#range-date-full').val();
              }
          },
          'columns': [
              { data: "no"},
              { data: "nama_penerima"},
              { data: "provinsi"},
              { data: "kabupaten"},
              { data: "hp_penerima"},
              { data: "alamat"},
              { data: "repeat"},
              // { data: "hapus"},
          ],
          columnDefs: [
            { className: 'text-center', 
              targets: [0, 1, 2, 3, 4, 5, 6] 
            }
          ],
      });
      // ======== End Tabel ============

      // ======== Pie Chart ============
      function pie_prokur() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_pie_provkab",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(provinsi.kabupaten);
                // Create the chart
                Highcharts.chart('container-pie', {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: 'Daftar Provinsi dan Kota Customer per Tanggal'
                    },
                    subtitle: {
                        text: data.tanggal
                    },

                    accessibility: {
                        announceNewData: {
                            enabled: true
                        },
                        point: {
                            valueSuffix: 'Customer'
                        }
                    },

                    plotOptions: {
                        series: {
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.y}'
                                // format: '{point.name}: {point.y:.1f}%'
                            }
                        }
                    },
                    credits: {
                          enabled: false
                      },
                    series: [
                        {
                            name: "Provinsi",
                            colorByPoint: true,
                            data: data.provinsi
                        }
                    ],
                    drilldown: {
                        series: data.kabupaten
                    }
                });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }

      function pie_prokur_penjualan() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_pie_provkab_penjualan",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(provinsi.kabupaten);
                // Create the chart
                Highcharts.chart('container-pie-penjualan', {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: 'Daftar Provinsi dan Kota Customer per Tanggal'
                    },
                    subtitle: {
                        text: data.tanggal
                    },

                    accessibility: {
                        announceNewData: {
                            enabled: true
                        },
                        point: {
                            valueSuffix: 'Customer'
                        }
                    },

                    plotOptions: {
                        series: {
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.y}'
                                // format: '{point.name}: {point.y:.1f}%'
                            }
                        }
                    },
                    credits: {
                          enabled: false
                      },
                    series: [
                        {
                            name: "Provinsi",
                            colorByPoint: true,
                            data: data.provinsi
                        }
                    ],
                    drilldown: {
                        series: data.kabupaten
                    }
                });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }
      // ======== End Pie Chart ============

      // ======== Dashboard Pendapatan =========
      function dasbor_total() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_dasbor_total",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // document.getElementById("judul-dasbor-total").innerHTML= data.judul;
                document.getElementById("dasbor-total-pesanan").innerHTML= data.pesan;
                document.getElementById("dasbor-total-diterima").innerHTML= data.diterima;
                document.getElementById("dasbor-total-omset").innerHTML= data.laba;
                document.getElementById("dasbor-total-margin").innerHTML=data.income;
                document.getElementById("dasbor-total-ongkir").innerHTML=data.ongkir;
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }

      function dasbor_total_penjualan() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_dasbor_total_penjualan",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // document.getElementById("judul-dasbor-total").innerHTML= data.judul;
                document.getElementById("dasbor-total-pesanan-penjualan").innerHTML= data.pesan;
                document.getElementById("dasbor-total-diterima-penjualan").innerHTML= data.diterima;
                document.getElementById("dasbor-total-omset-penjualan").innerHTML= data.laba;
                document.getElementById("dasbor-total-margin-penjualan").innerHTML=data.income;
                document.getElementById("dasbor-total-ongkir-penjualan").innerHTML=data.ongkir;
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }
      // ======== End Dashboard Pendapatan =========

      // ======== Line Chart Pendapatan =========
      function line_income() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_line_income",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data.income);
                Highcharts.chart('container-income', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: 'Grafik Keuangan per Tanggal'
                    },
                    subtitle: {
                        text: data.tanggal
                    },
                    xAxis: {
                      reversed: false,
                       type: 'category',
                       labels: {
                         formatter() {
                           if (typeof(this.value) === 'string') {
                             return this.value
                           }
                         }
                       }
                      },
                    yAxis: {
                        title: {
                            text: 'Jumlah (Rp.)'
                        }
                    },
                    credits: {
                          enabled: false
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: false
                        }
                    },
                    series: [{
                        name: 'Omset',
                        data: data.laba
                    },
                    {
                        name: 'Margin',
                        data: data.income
                    },
                    {
                        name: 'Diterima',
                        data: data.diterima
                    },
                    {
                        name: 'Ongkir',
                        data: data.ongkir
                    }]
                });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }

      function line_income_penjualan() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_line_income_penjualan",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data.income);
                Highcharts.chart('container-income-penjualan', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: 'Grafik Keuangan per Tanggal'
                    },
                    subtitle: {
                        text: data.tanggal
                    },
                    xAxis: {
                      reversed: false,
                       type: 'category',
                       labels: {
                         formatter() {
                           if (typeof(this.value) === 'string') {
                             return this.value
                           }
                         }
                       }
                      },
                    yAxis: {
                        title: {
                            text: 'Jumlah (Rp.)'
                        }
                    },
                    credits: {
                          enabled: false
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: false
                        }
                    },
                    series: [{
                        name: 'Omset',
                        data: data.laba
                    },
                    {
                        name: 'Margin',
                        data: data.income
                    },
                    {
                        name: 'Diterima',
                        data: data.diterima
                    },
                    {
                        name: 'Ongkir',
                        data: data.ongkir
                    }]
                });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }
      // ======== End Line Chart Pendapatan =========

      // ======== Bar SKU dan Produk ==============
      function bar_sku() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_dasbor_prosku",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data);
                Highcharts.chart('container-sku', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Jumlah SKU dan Produk pada Toko per Tanggal'
                    },
                    subtitle: {
                        text: data.tanggal
                    },
                    accessibility: {
                        announceNewData: {
                            enabled: true
                        }
                    },
                    xAxis: {
                        type: 'category',
                    },
                    yAxis: {
                        title: {
                            text: 'Jumlah'
                        },
                        stackLabels: {
                          enabled: true,
                          style: {
                            color: 'black'
                          },
                          defer: false,
                          crop: false,
                        }

                    },
                    legend: {
                        enabled: true
                    },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: false,
                                format: '{point.y}'
                            },
                            stacking: 'normal'
                        },
                        column: {
                          dataLabels: {
                            enabled: true,
                          },
                          stacking: 'normal'
                        }
                    },

                    // tooltip: {
                    //     headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    //     pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                    // },

                    series: [
                        {
                            name: "SKU",
                            colorByPoint: true,
                            data: data.sku,
                            dataLabels: {
                                enabled: false
                            }
                        }
                    ],
                    drilldown: {
                        series: data.produk
                    },
                    credits: {
                        enabled: false
                    },
                });
                
              }
              ,
              error: function(data){
                console.log(data.responseText);
              }  
          });
      }

      function bar_sku_penjualan() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_dasbor_prosku_penjualan",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data);
                Highcharts.chart('container-sku-penjualan', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Jumlah SKU dan Produk pada Toko per Tanggal'
                    },
                    subtitle: {
                        text: data.tanggal
                    },
                    accessibility: {
                        announceNewData: {
                            enabled: true
                        }
                    },
                    xAxis: {
                        type: 'category',
                    },
                    yAxis: {
                        title: {
                            text: 'Jumlah'
                        },
                        stackLabels: {
                          enabled: true,
                          style: {
                            color: 'black'
                          },
                          defer: false,
                          crop: false,
                        }

                    },
                    legend: {
                        enabled: true
                    },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: false,
                                format: '{point.y}'
                            },
                            stacking: 'normal'
                        },
                        column: {
                          dataLabels: {
                            enabled: true,
                          },
                          stacking: 'normal'
                        }
                    },

                    // tooltip: {
                    //     headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    //     pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                    // },

                    series: [
                        {
                            name: "SKU",
                            colorByPoint: true,
                            data: data.sku,
                            dataLabels: {
                                enabled: false
                            }
                        }
                    ],
                    drilldown: {
                        series: data.produk
                    },
                    credits: {
                        enabled: false
                    },
                });
                
              }
              ,
              error: function(data){
                console.log(data.responseText);
              }  
          });
      }
      // ======== Bar Produk Terbanyak ============
      function bar_produk() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_bar_produk",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data.produk);
                Highcharts.chart('container-produk', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Grafik Produk per Tanggal'
                    },
                    subtitle: {
                        text: data.tanggal
                    },
                    xAxis: {
                        type: 'category',
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Jumlah'
                        },
                        stackLabels: {
                          enabled: true,
                          style: {
                            color: 'black'
                          },
                          defer: false,
                          crop: true,
                        }
                    },
                    credits: {
                          enabled: false
                    },
                    plotOptions: {
                      column: {
                        dataLabels: {
                          enabled: true,
                        },
                        stacking: 'normal'
                      }
                    },
                    series: [{
                        name: 'Produk',
                        data: data.produk,
                        dataLabels: {
                            enabled: false,
                            rotation: 0,
                            // color: '#FFFFFF',
                            align: 'center',
                            // format: '{point.y:.1f}', // one decimal
                            y: 0, // 10 pixels down from the top
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    }]
                });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }

      function bar_produk_penjualan() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_bar_produk_penjualan",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data.produk);
                Highcharts.chart('container-produk-penjualan', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Grafik Produk per Tanggal'
                    },
                    subtitle: {
                        text: data.tanggal
                    },
                    xAxis: {
                        type: 'category',
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Jumlah'
                        },
                        stackLabels: {
                          enabled: true,
                          style: {
                            color: 'black'
                          },
                          defer: false,
                          crop: true,
                        }
                    },
                    credits: {
                          enabled: false
                    },
                    plotOptions: {
                      column: {
                        dataLabels: {
                          enabled: true,
                        },
                        stacking: 'normal'
                      }
                    },
                    series: [{
                        name: 'Produk',
                        data: data.produk,
                        dataLabels: {
                            enabled: false,
                            rotation: 0,
                            // color: '#FFFFFF',
                            align: 'center',
                            // format: '{point.y:.1f}', // one decimal
                            y: 0, // 10 pixels down from the top
                            style: {
                                fontSize: '13px',
                                fontFamily: 'Verdana, sans-serif'
                            }
                        }
                    }]
                });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }
      // ======== End Bar Produk Terbanyak ============

      // ======== Bar Kurir Terbanyak ============
      function bar_kurir() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_bar_kurir",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data.income);
                Highcharts.chart('container-kurir', {
                  chart: {
                      type: 'column'
                  },
                  title: {
                      text: 'Grafik Kurir per Tanggal'
                  },
                  subtitle: {
                      text: data.tanggal
                  },
                  xAxis: {
                      type: 'category',
                      // labels: {
                      //     rotation: -45,
                      //     style: {
                      //         fontSize: '13px',
                      //         fontFamily: 'Verdana, sans-serif'
                      //     }
                      // }
                  },
                  yAxis: {
                      min: 0,
                      title: {
                          text: 'Jumlah'
                      }
                  },
                  legend: {
                      enabled: false
                  },
                  credits: {
                        enabled: false
                  },
                  // tooltip: {
                  //     pointFormat: 'Population in 2017: <b>{point.y:.1f} millions</b>'
                  // },
                  series: [{
                      name: 'Kurir',
                      data: data.kurir,
                      dataLabels: {
                          enabled: true,
                          rotation: 0,
                          // color: '#FFFFFF',
                          align: 'center',
                          // format: '{point.y:.1f}', // one decimal
                          y: 0, // 10 pixels down from the top
                          style: {
                              fontSize: '13px',
                              fontFamily: 'Verdana, sans-serif'
                          }
                      }
                  }]
              });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }

      function bar_kurir_penjualan() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_bar_kurir_penjualan",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data.income);
                Highcharts.chart('container-kurir-penjualan', {
                  chart: {
                      type: 'column'
                  },
                  title: {
                      text: 'Grafik Kurir per Tanggal'
                  },
                  subtitle: {
                      text: data.tanggal
                  },
                  xAxis: {
                      type: 'category',
                      // labels: {
                      //     rotation: -45,
                      //     style: {
                      //         fontSize: '13px',
                      //         fontFamily: 'Verdana, sans-serif'
                      //     }
                      // }
                  },
                  yAxis: {
                      min: 0,
                      title: {
                          text: 'Jumlah'
                      }
                  },
                  legend: {
                      enabled: false
                  },
                  credits: {
                        enabled: false
                  },
                  // tooltip: {
                  //     pointFormat: 'Population in 2017: <b>{point.y:.1f} millions</b>'
                  // },
                  series: [{
                      name: 'Kurir',
                      data: data.kurir,
                      dataLabels: {
                          enabled: true,
                          rotation: 0,
                          // color: '#FFFFFF',
                          align: 'center',
                          // format: '{point.y:.1f}', // one decimal
                          y: 0, // 10 pixels down from the top
                          style: {
                              fontSize: '13px',
                              fontFamily: 'Verdana, sans-serif'
                          }
                      }
                  }]
              });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }
      // ======== End Bar Kurir Terbanyak ============

      // ======== Bar Pesanan Terbanyak ============
      function bar_pesanan() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_bar_pesanan",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data.income);
                Highcharts.chart('container-pesanan', {
                  chart: {
                      type: 'column'
                  },
                  title: {
                      text: 'Grafik Pesanan per Tanggal'
                  },
                  subtitle: {
                      text: data.tanggal
                  },
                  xAxis: {
                      type: 'category',
                      // labels: {
                      //     rotation: -45,
                      //     style: {
                      //         fontSize: '13px',
                      //         fontFamily: 'Verdana, sans-serif'
                      //     }
                      // }
                  },
                  yAxis: {
                      min: 0,
                      title: {
                          text: 'Jumlah'
                      }
                  },
                  legend: {
                      enabled: false
                  },
                  credits: {
                        enabled: false
                  },
                  // tooltip: {
                  //     pointFormat: 'Population in 2017: <b>{point.y:.1f} millions</b>'
                  // },
                  series: [{
                      name: 'Pesanan',
                      data: data.pesanan,
                      dataLabels: {
                          enabled: true,
                          rotation: 0,
                          // color: '#FFFFFF',
                          align: 'center',
                          // format: '{point.y:.1f}', // one decimal
                          y: 0, // 10 pixels down from the top
                          style: {
                              fontSize: '13px',
                              fontFamily: 'Verdana, sans-serif'
                          }
                      }
                  }]
              });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }

      function bar_pesanan_penjualan() {
        var periodik = document.getElementById("range-date-full").value;
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
        $.ajax({
              url: "<?php echo base_url()?>admin/dashboard/ajax_bar_pesanan_penjualan",
              type: "post",
              data: {periodik: periodik, [csrfName]: csrfHash},
              dataType: 'JSON',
              success:function(data)  {  
                // console.log(data.income);
                Highcharts.chart('container-pesanan-penjualan', {
                  chart: {
                      type: 'column'
                  },
                  title: {
                      text: 'Grafik Pesanan per Tanggal'
                  },
                  subtitle: {
                      text: data.tanggal
                  },
                  xAxis: {
                      type: 'category',
                      // labels: {
                      //     rotation: -45,
                      //     style: {
                      //         fontSize: '13px',
                      //         fontFamily: 'Verdana, sans-serif'
                      //     }
                      // }
                  },
                  yAxis: {
                      min: 0,
                      title: {
                          text: 'Jumlah'
                      }
                  },
                  legend: {
                      enabled: false
                  },
                  credits: {
                        enabled: false
                  },
                  // tooltip: {
                  //     pointFormat: 'Population in 2017: <b>{point.y:.1f} millions</b>'
                  // },
                  series: [{
                      name: 'Pesanan',
                      data: data.pesanan,
                      dataLabels: {
                          enabled: true,
                          rotation: 0,
                          // color: '#FFFFFF',
                          align: 'center',
                          // format: '{point.y:.1f}', // one decimal
                          y: 0, // 10 pixels down from the top
                          style: {
                              fontSize: '13px',
                              fontFamily: 'Verdana, sans-serif'
                          }
                      }
                  }]
              });
              },
              error: function(data){
                console.log(data.responseText);
              }
        });      
      }
      // ======== End Bar Pesanan Terbanyak ============

      // ========== Chart Jenis Toko dan Toko =============
      function dasbor_jenis_toko() {
      var periodik = document.getElementById("range-date-full").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
            url: "<?php echo base_url()?>admin/dashboard/ajax_dasbor_jenis_toko",
            type: "post",
            data: {periodik: periodik, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  
              // console.log(data);
              Highcharts.chart('container-jenis-toko', {
              chart: {
                  type: 'column'
              },
              title: {
                  text: 'Jumlah Invoice dari Jenis Toko dan Toko per Tanggal'
              },
              subtitle: {
                  text: data.tanggal
              },
              accessibility: {
                  announceNewData: {
                      enabled: true
                  }
              },
              xAxis: {
                  type: 'category'
              },
              yAxis: {
                  title: {
                      text: 'Jumlah'
                  }

              },
              legend: {
                  enabled: false
              },
              plotOptions: {
                  series: {
                      borderWidth: 0,
                      dataLabels: {
                          enabled: true,
                          format: '{point.y}'
                      }
                  }
              },

              // tooltip: {
              //     headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
              //     pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
              // },

              series: [
                  {
                      name: "Jenis Toko",
                      colorByPoint: true,
                      data: data.jenis
                  }
              ],
              drilldown: {
                  series: data.toko
              },
              credits: {
                  enabled: false
              },
          });
              
            },
            error: function(data){
              console.log(data.responseText);
            }  
        });
    }

    function dasbor_jenis_toko_penjualan() {
      var periodik = document.getElementById("range-date-full").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
            url: "<?php echo base_url()?>admin/dashboard/ajax_dasbor_jenis_toko_penjualan",
            type: "post",
            data: {periodik: periodik, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  
              // console.log(data);
              Highcharts.chart('container-jenis-toko-penjualan', {
              chart: {
                  type: 'column'
              },
              title: {
                  text: 'Jumlah Jenis Toko dan Toko per Tanggal'
              },
              subtitle: {
                  text: data.tanggal
              },
              accessibility: {
                  announceNewData: {
                      enabled: true
                  }
              },
              xAxis: {
                  type: 'category'
              },
              yAxis: {
                  title: {
                      text: 'Jumlah'
                  }

              },
              legend: {
                  enabled: false
              },
              plotOptions: {
                  series: {
                      borderWidth: 0,
                      dataLabels: {
                          enabled: true,
                          format: '{point.y}'
                      }
                  }
              },

              // tooltip: {
              //     headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
              //     pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
              // },

              series: [
                  {
                      name: "Jenis Toko",
                      colorByPoint: true,
                      data: data.jenis
                  }
              ],
              drilldown: {
                  series: data.toko
              },
              credits: {
                  enabled: false
              },
          });
              
            },
            error: function(data){
              console.log(data.responseText);
            }  
        });
    }
    // ========== End Chart Jenis Toko dan Toko =============
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
