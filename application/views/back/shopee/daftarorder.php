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
    </script>

</div>
<!-- ./wrapper -->

</body>

</html>