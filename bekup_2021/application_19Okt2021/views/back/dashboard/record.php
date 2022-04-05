<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-orange">
      <div class="inner">
        <h3><?php echo $get_total_menu ?></h3>
        <p>Menu</p>
      </div>
      <div class="icon"><i class="fa fa-list"></i></div>
      <a href="<?php echo base_url('admin/menu') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-aqua">
      <div class="inner">
        <h3><?php echo $get_total_submenu ?></h3>
        <p>SubMenu</p>
      </div>
      <div class="icon"><i class="fa fa-list"></i></div>
      <a href="<?php echo base_url('admin/submenu') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-red">
      <div class="inner">
        <h3><?php echo $get_total_user ?></h3>
        <p>User</p>
      </div>
      <div class="icon"><i class="fa fa-user"></i></div>
      <a href="<?php echo base_url('admin/auth') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-green">
      <div class="inner">
        <h3><?php echo $get_total_usertype ?></h3>
        <p>Usertype</p>
      </div>
      <div class="icon"><i class="fa fa-legal"></i></div>
      <a href="<?php echo base_url('admin/usertype') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>
<!-- /.row -->
