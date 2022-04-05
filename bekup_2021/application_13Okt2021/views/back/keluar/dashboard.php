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
        <div class="box box-header">
          <div class="form-group"><label>Pilih Tanggal</label>
            <input type="text" name="periodik" class="form-control float-right" id="range-date">
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <!-- Dasbor Protok 2 -->
          <figure class="highcharts-figure">
              <div id="container-protok2"></div>
          </figure>
        </div>

        <div class="col-sm-6">
          <!-- Dasbor Prokur 2 -->
          <figure class="highcharts-figure">
              <div id="container-prokur2"></div>
          </figure>
        </div>
      </div>

      <!-- Dasbor Produk Toko -->
      <!-- <figure class="highcharts-figure">
          <div id="container-protok"></div>
          <p class="highcharts-description"></p>
      </figure> -->

      <!-- Dasbor Produk Kurir -->
      <!-- <figure class="highcharts-figure">
          <div id="container-prokur"></div>
          <p class="highcharts-description"></p>
      </figure> -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>
  <!-- Highcharts -->
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
      refresh_dasbor();
    }

    function refresh_dasbor() {
      dasbor_protok_2();
      dasbor_prokur_2();
      // dasbor_prokur();
      // dasbor_protok();
    }

    // ============= Protok ==============
    function dasbor_protok() {
      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
            url: "<?php echo base_url()?>admin/keluar/ajax_dasbor_protok",
            type: "post",
            data: {periodik: periodik, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  
              // console.log(data);
              Highcharts.chart('container-protok', {
                  chart: {
                      type: 'bar'
                  },
                  title: {
                      text: 'Jumlah Produk Terjual pada Toko'
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
                          text: 'Jumlah',
                          align: 'high'
                      },
                      labels: {
                          overflow: 'justify'
                      }
                  },
                  tooltip: {
                      valueSuffix: ' Pcs'
                  },
                  plotOptions: {
                      bar: {
                          dataLabels: {
                              enabled: true
                          }
                      }
                  },
                  legend: {
                      layout: 'vertical',
                      align: 'right',
                      verticalAlign: 'top',
                      x: -40,
                      y: 80,
                      floating: true,
                      borderWidth: 1,
                      backgroundColor:
                          Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                      shadow: true
                  },
                  credits: {
                      enabled: false
                  },
                  series: data.protok
              });
              
            },
            error: function(data){
              console.log(data.responseText);
            }  
        });
    }

    function dasbor_protok_2() {
      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
            url: "<?php echo base_url()?>admin/keluar/ajax_dasbor_protok_2",
            type: "post",
            data: {periodik: periodik, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  
              // console.log(data);
              Highcharts.chart('container-protok2', {
              chart: {
                  type: 'column'
              },
              title: {
                  text: 'Jumlah Toko dan Produk pada Toko per Tanggal'
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
                      name: "Toko",
                      colorByPoint: true,
                      data: data.toko,
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
              
            },
            error: function(data){
              console.log(data.responseText);
            }  
        });
    }
    // ============= End Protok ==============

    // ============= Prokur ==============
    function dasbor_prokur() {
      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
            url: "<?php echo base_url()?>admin/keluar/ajax_dasbor_prokur",
            type: "post",
            data: {periodik: periodik, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  
              // console.log(data);
              Highcharts.chart('container-prokur', {
              chart: {
                  type: 'bar'
              },
              title: {
                  text: 'Jumlah Produk Terjual pada Kurir Ekspedisi'
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
                      text: 'Jumlah',
                      align: 'high'
                  },
                  labels: {
                      overflow: 'justify'
                  }
              },
              tooltip: {
                  valueSuffix: ' Pcs'
              },
              plotOptions: {
                  bar: {
                      dataLabels: {
                          enabled: true
                      }
                  }
              },
              legend: {
                  layout: 'vertical',
                  align: 'right',
                  verticalAlign: 'top',
                  x: -40,
                  y: 80,
                  floating: true,
                  borderWidth: 1,
                  backgroundColor:
                      Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                  shadow: true
              },
              credits: {
                  enabled: false
              },
              series: data.prokur
          });
              
            },
            error: function(data){
              console.log(data.responseText);
            }  
        });
    }

    function dasbor_prokur_2() {
      var periodik = document.getElementById("range-date").value;
      var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
          csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
      $.ajax({
            url: "<?php echo base_url()?>admin/keluar/ajax_dasbor_prokur_2",
            type: "post",
            data: {periodik: periodik, [csrfName]: csrfHash},
            dataType: 'JSON',
            success:function(data)  {  
              console.log(data.kurir);
              Highcharts.chart('container-prokur2', {
              chart: {
                  type: 'column'
              },
              title: {
                  text: 'Jumlah Toko dan Kurir pada Toko per Tanggal'
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
                      name: "Toko",
                      colorByPoint: true,
                      data: data.toko
                  }
              ],
              drilldown: {
                  series: data.kurir
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
    // ============= End Prokur ==============

    $(document).ready( function () {
      $('#range-date').on('change', function(){
        refresh_dasbor();
      });
    });

    // $('#range-date').daterangepicker({
    //     locale: {
    //       format: 'DD/MM/YYYY'
    //     }
    // });
    //Date range as a button
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
        // startDate: moment(),
        // endDate  : moment()
        startDate: moment().subtract(29, 'days'),
        endDate  : moment(),
        locale: {
          format: 'YYYY-MM-DD'
        }
      },
      function (start, end) {
        $('#range-date-full span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'))
      }
    )
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
