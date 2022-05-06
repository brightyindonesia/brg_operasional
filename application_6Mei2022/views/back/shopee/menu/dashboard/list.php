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
          <div class="box box-widget widget-user">
            <div class="widget-user-header" style="height: 70px;">
              <h5 class="widget-user-username"><i class="fa fa-star"></i> Rating Shop</h5>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                  <h5 class="description-header"><?php echo $shop_performance->customer_satisfaction->overall_reviewing_rate->total_data->target; ?></h5>
                  <span class="description-text">Target</span>
                  </div>

                </div>

                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header"><?php echo $shop_performance->customer_satisfaction->overall_reviewing_rate->total_data->my_shop_performance; ?></h5>
                    <span class="description-text">Performance</span>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="description-block">
                  <h5 class="description-header"><?php if($shop_performance->customer_satisfaction->overall_reviewing_rate->total_data->penalty_points == 'null' ) { echo 0; }else{ echo $shop_performance->customer_service->response_rate->total_data->penalty_points; } ?></h5>
                  <span class="description-text">Penalty</span>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="box box-widget widget-user">
            <div class="widget-user-header" style="height: 70px;">
              <h5 class="widget-user-username"><i class="fa fa-comments-o"></i> Response Chat</h5>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                  <h5 class="description-header"><?php echo $shop_performance->customer_service->response_rate->total_data->target; ?></h5>
                  <span class="description-text">Target</span>
                  </div>

                </div>

                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header"><?php echo $shop_performance->customer_service->response_rate->total_data->my_shop_performance; ?></h5>
                    <span class="description-text">Performance</span>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="description-block">
                  <h5 class="description-header"><?php if($shop_performance->customer_service->response_rate->total_data->penalty_points == 'null' ) { echo 0; }else{ echo $shop_performance->customer_service->response_rate->total_data->penalty_points; } ?></h5>
                  <span class="description-text">Penalty</span>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="box box-widget widget-user">
            <div class="widget-user-header" style="height: 70px;">
              <h5 class="widget-user-username"><i class="fa fa-clock-o"></i> Response Time</h5>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                  <h5 class="description-header"><?php echo $shop_performance->customer_service->response_time->total_data->target; ?></h5>
                  <span class="description-text">Target</span>
                  </div>

                </div>

                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header"><?php echo $shop_performance->customer_service->response_time->total_data->my_shop_performance; ?></h5>
                    <span class="description-text">Performance</span>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="description-block">
                  <h5 class="description-header"><?php if($shop_performance->customer_service->response_time->total_data->penalty_points == 'null' ) { echo 0; }else{ echo $shop_performance->customer_service->response_rate->total_data->penalty_points; } ?></h5>
                  <span class="description-text">Penalty</span>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="box box-widget widget-user">
            <div class="widget-user-header" style="height: 70px;">
              <h5 class="widget-user-username"><i class="fa fa-clock-o"></i> Packing Time</h5>
            </div>

            <div class="box-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                  <h5 class="description-header"><?php echo $shop_performance->fulfillment->preparation_time->total_data->target; ?></h5>
                  <span class="description-text">Target</span>
                  </div>

                </div>

                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header"><?php echo $shop_performance->fulfillment->preparation_time->total_data->my_shop_performance; ?></h5>
                    <span class="description-text">Performance</span>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div class="description-block">
                  <h5 class="description-header"><?php if($shop_performance->fulfillment->preparation_time->total_data->penalty_points == 'null' ) { echo 0; }else{ echo $shop_performance->customer_service->response_rate->total_data->penalty_points; } ?></h5>
                  <span class="description-text">Penalty</span>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

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

        <div class="col-sm-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <?php 
                $i = 0;
                $list_logistik_desk = '';
                // echo print_r($channel_list)."<br><br>";
                foreach ($channel_list as $val_channel_list) {
                  if ($val_channel_list->enabled == 1) {
                    if($i==0){ 
                      $active = 'active '; 
                    }else{
                      $active = '';
                    }

                    if ($val_channel_list->logistics_description == '') {
                      $deskripsi = 'Belum ada Deskripsi Logistik';
                    }else{
                      $deskripsi = $val_channel_list->logistics_description;
                    }

                    // BAGIAN UNTUK TAB CONTENT
                    $list_logistik_desk .=  '<div class="'.$active.$val_channel_list->logistics_channel_id.' tab-pane" id="'.$val_channel_list->logistics_channel_id.'">'.$deskripsi.'</div>';
              ?>

              <!-- BAGIAN UNTUK NAV TABS -->
              <li class="<?php echo $active ?>"><a href="<?php echo '#'.$val_channel_list->logistics_channel_id; ?>" data-toggle="tab"><?php echo $val_channel_list->logistics_channel_name; ?></a></li>      

              <?php
                  } 
                  $i++;   
                }
              ?>
            </ul>
            <div class="tab-content">
              <?php 
                echo $list_logistik_desk;
              ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
    <?php 
      // echo print_r($shop_performance)."<br><br>";
    ?>
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
