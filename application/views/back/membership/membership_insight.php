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

            <div class="box box-primary">
                <div class="box-header">
                    <select name="tahun" id="tahun" class="form-control" required="">
                        <option selected disabled>-- Pilih Tahun --</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                    </select>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <figure class="highcharts-figure">
                                <div id="container-membership-pie"></div>
                            </figure>
                        </div>
                        <div class="col-md-8">
                            <figure class="highcharts-figure">
                                <div id="container-membership-area"></div>
                            </figure>

                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <?php $this->load->view('back/template/footer'); ?>
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables-bs/css/dataTables.bootstrap.min.css">
    <script src="<?php echo base_url('assets/plugins/') ?>datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url('assets/plugins/') ?>datatables-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>highcharts.js"></script>
    <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>exporting.js"></script>
    <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>export-data.js"></script>
    <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>accessibility.js"></script>
    <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>data.js"></script>
    <script src="<?php echo base_url('assets/plugins/highcharts/js/') ?>drilldown.js"></script>
    <script>
        Highcharts.setOptions({
            time: {
                timezone: 'Asia/Jakarta'
            }
            });

        window.onload = function() {
                $("#modal-proses").modal('show');
                $.ajax({
                type: "GET",
                url: "<?php echo base_url('admin/Membership/get_count_membership') ?>",
                dataType: "JSON",
                success: function(data) {
                    
                    Highcharts.chart('container-membership-pie', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Jumlah Member di tahun ' + tahun
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                            }
                        },
                        series: [{
                            name: 'Jumlah',
                            colorByPoint: true,
                            data: data
                        }]
                    });
                }
            })
            $.ajax({
                type: "GET",
                url: "<?php echo base_url('admin/Membership/get_insight_membership') ?>",
                dataType: 'json',
                success: function(data) {
                    $("#modal-proses").modal('hide');
                    Highcharts.chart('container-membership-area', {
                        chart: {
                            type: 'area'
                        },
                        title: {
                            text: 'Statistik Member di tahun ' + tahun
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'left',
                            verticalAlign: 'top',
                            x: 100,
                            y: 70,
                            floating: true,
                            borderWidth: 1,
                            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'
                        },
                        xAxis: {
                            type: 'datetime',
                            tickInterval: 1000 * 3600 * 24 * 30, // 1 month
                            labels: {
                                formatter: function() {
                                    return Highcharts.dateFormat('%b %Y', new Date(this.value));
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Y-Axis'
                            }
                        },
                        plotOptions: {
                            area: {
                                fillOpacity: 0.5
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        series: JSON.parse(JSON.stringify(data))
                    });
                }
            });
            }
        $(document).ready(function() {
            
            tahun = $('#tahun').val() ? $('#tahun').val() : '<?= date('Y') ?>';
            
            

            $('#tahun').change(function() {
                $("#modal-proses").modal('show');
                tahun = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?php echo base_url('admin/Membership/get_count_membership') ?>",
                    data: {
                        tahun: tahun
                    },
                    dataType: 'json',
                    success: function(data) {
                        const membership_pie = Highcharts.chart('container-membership-pie', {
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {
                                text: 'Jumlah Member di tahun ' + tahun
                            },
                            tooltip: {
                                // pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            accessibility: {
                                point: {
                                    valueSuffix: '%'
                                }
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        // format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                                    }
                                }
                            },
                            series: [{
                                name: 'Brands',
                                colorByPoint: true,
                                data: data
                            }]
                        });
                    }
                })
                $.ajax({
                    type: "GET",
                    url: "<?php echo base_url('admin/Membership/get_insight_membership') ?>",
                    dataType: 'json',
                    data: {
                        tahun: tahun
                    },
                    success: function(data) {
                        $("#modal-proses").modal('hide');
                        Highcharts.chart('container-membership-area', {
                            chart: {
                                type: 'area'
                            },
                            title: {
                                text: 'Statistik Member di tahun ' + tahun
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'left',
                                verticalAlign: 'top',
                                x: 100,
                                y: 70,
                                floating: true,
                                borderWidth: 1,
                                backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'
                            },
                            xAxis: {
                                type: 'datetime',
                                tickInterval: 1000 * 3600 * 24 * 30, // 1 month
                                labels: {
                                    format: '{value:%b %Y}'
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'Y-Axis'
                                }
                            },
                            plotOptions: {
                                area: {
                                    fillOpacity: 0.5
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: JSON.parse(JSON.stringify(data))
                        });
                    }
                });
            })

        })
    </script>

</div>
<!-- ./wrapper -->

</body>

</html>