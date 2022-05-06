<?php $this->load->view('back/template_shopee_api/meta'); ?>
<div class="wrapper">

  <?php $this->load->view('back/template_shopee_api/navbar'); ?>
  <?php $this->load->view('back/template_shopee_api/sidebar'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="background-color: #feece6;">
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

      <div class="row">
        <div class="col-sm-3">
          <div class="box box-primary no-border">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo $shop_profile->shop_logo; ?>" alt="User profile picture">
              <h3 class="profile-username text-center"><?php echo $shop_profile->shop_name; ?></h3>
              <p class="text-muted text-center"><?php echo $shop_profile->description; ?></p>
            </div>
          </div>
        </div>
      </div>

      <?php 
        echo print_r($channel_list)."<br><br>";
        foreach ($channel_list as $val_channel_list) {
          if ($val_channel_list->enabled == 1) {
            echo $val_channel_list->logistics_channel_name."<br>";  
          }
          
        }
      ?>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template_shopee_api/footer'); ?>
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
  </script>

</div>
<!-- ./wrapper -->

</body>
</html>
