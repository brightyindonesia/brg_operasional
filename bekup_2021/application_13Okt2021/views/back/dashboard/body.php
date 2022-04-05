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
      <?php if($this->session->flashdata('message')){echo $this->session->flashdata('message');} ?>
      <?php 
        if ($this->session->userdata('usertype') == 1) {
      ?>
      <?php $this->load->view('back/dashboard/record'); ?>
      <?php 
        }
      ?>
      <div class="box box-primary">
        <div class="box-header">
          <div class="form-group"><label>Pilih Tanggal</label>
            <input type="text" name="periodik" class="form-control float-right" id="range-date-full">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="box box-primary">
            <div class="box-header">
              <h4>Tabel Data Customer Repeat Order</h4>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="table-repeat-order" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="text-align: center">No.</th>
                      <th style="text-align: center">Nama Penerima</th>
                      <th style="text-align: center">Provinsi</th>
                      <th style="text-align: center">Kabupaten</th>
                      <th style="text-align: center">Nomor Handphone</th>
                      <th style="text-align: center">Alamat</th>
                      <th style="text-align: center">Repeat Order</th>
                    </tr>
                  </thead>
                </table>
              </div>    
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <figure class="highcharts-figure">
              <div id="container-pie"></div>
          </figure>
        </div>

        <div class="col-sm-6">
          <figure class="highcharts-figure">
              <div id="container-income"></div>
          </figure>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <figure class="highcharts-figure">
              <div id="container-produk"></div>
          </figure>
        </div>

        <div class="col-sm-6">
          <figure class="highcharts-figure">
              <div id="container-kurir"></div>
          </figure>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <figure class="highcharts-figure">
              <div id="container-jenis-toko"></div>
          </figure>
        </div>

        <div class="col-sm-6">
          <figure class="highcharts-figure">
              <div id="container-pesanan"></div>
          </figure>
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
  <script type="text/javascript">
    window.onload = function() {
      refresh_table();
      refresh_chart();
    }

    function refresh_table() {
      $('#table-repeat-order').DataTable().ajax.reload();
    }

    function refresh_chart() {
      pie_prokur();
      line_income();
      bar_produk();
      bar_kurir();
      dasbor_jenis_toko();
      bar_pesanan();
    }

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
    });

    // ======== Tabel ============
    $('#table-repeat-order').DataTable({
          "iDisplayLength":10,
          'ajax': {
              'url': '<?php echo base_url()?>admin/dashboard/ajax_repeat',
              'dataSrc': ''
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
          "fnServerParams": function ( aoData ) {
            aoData.push( { "name": "periodik", "value": $('#range-date-full').val()} );
            // dasbor_list_count();
          }
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
      // ======== End Pie Chart ============

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
      // ======== End Line Chart Pendapatan =========

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
                console.log(data.produk);
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
