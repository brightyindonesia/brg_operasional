<?php $this->load->view('back/template/meta'); ?>
<div class="wrapper">

  <?php $this->load->view('back/template/navbar'); ?>
  <?php $this->load->view('back/template/sidebar'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><?php echo $page_title ?></h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><?php echo $module ?></li>
        <li class="active"><?php echo $page_title ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <a href="<?php echo base_url('admin/changelog/applog_create') ?>" class="btn bg-black"><i class="fa fa-plus"></i> Add New Log</a>
        </div>
      </div>
      <hr>
      <ul class="timeline">
        <?php
        $a="";
        foreach($get_all as $applog){
          if($applog->changelog_type == 'CREATE'){
            $changelogType = '<i class="fa fa-plus bg-green"></i>';
          }
          elseif($applog->changelog_type == 'UPDATE'){
            $changelogType = '<i class="fa fa-refresh bg-yellow"></i>';
          }
          elseif($applog->changelog_type == 'DELETE'){
            $changelogType = '<i class="fa fa-remove bg-red"></i>';
          }
        ?>
          <!-- timeline time label -->
          <?php if($applog->changelog_date!=$a){?>
          <li class="time-label"><span class="bg-blue"><i class="fa fa-calendar"></i> <?php echo date_only($applog->changelog_date) ?></span></li>
          <?php
            $a = $applog->changelog_date;
          } ?>
          <!-- /.timeline-label -->

          <!-- timeline item -->
          <li>
            <!-- timeline icon -->
            <?php echo $changelogType ?>
            <div class="timeline-item">
              <span class="time"><i class="fa fa-clock-o"></i> <?php echo time_only($applog->created_at) ?></span>
              <h3 class="timeline-header"><a href="#">#<?php echo $applog->id ?> | <?php echo strtoupper($applog->changelog_name) ?></a></h3>

              <div class="timeline-body"><?php echo $applog->changelog_description ?></div>

              <div class="timeline-footer">
                <a class="btn bg-purple btn-xs"><i class="fa fa-user"></i> <?php echo $applog->created_by ?></a>
              </div>
            </div>
          </li>
        <!-- END timeline item -->
        <?php } ?>
        <li><i class="fa fa-clock-o bg-gray"></i></li>
      </ul>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('back/template/footer'); ?>

</div>
<!-- ./wrapper -->

</body>
</html>
