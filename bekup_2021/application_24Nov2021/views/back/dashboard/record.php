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

<div class="box box-primary">
<!-- <div class="box-header">
  <label id="judul-dasbor-total"></label>
</div> -->
<div class="box-body">
  <div class="row">
    <div class="col-sm-12 col-xs-12">
      <div class="description-block border-right" id="dasbor-total-pesanan" style="font-weight: bold;">
        <!-- <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span> -->
        <!-- <h5 class="description-header"></h5>
        <span class="description-text">TOTAL PESANAN</span> -->
      </div>
      <!-- /.description-block -->
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3 col-xs-6">
      <div class="description-block border-right" id="dasbor-total-diterima" style="color: #7ac76a;font-weight: bold;">
        <!-- <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span> -->
        <!-- <h5 class="description-header"></h5>
        <span class="description-text">TOTAL DITERIMA</span> -->
      </div>
      <!-- /.description-block -->
    </div>
    <!-- /.col -->
    <div class="col-sm-3 col-xs-6">
      <div class="description-block border-right" id="dasbor-total-omset" style="color: #699bca;font-weight: bold;">
        <!-- <span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i> 0%</span> -->
        <!-- <h5 class="description-header"></h5>
        <span class="description-text">TOTAL OMSET</span> -->
      </div>
      <!-- /.description-block -->
    </div>
    <!-- /.col -->
    <div class="col-sm-3 col-xs-6">
      <div class="description-block border-right" id="dasbor-total-margin" style="color: #686868;font-weight: bold;">
        <!-- <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 20%</span> -->
        <!-- <h5 class="description-header"></h5>
        <span class="description-text">TOTAL MARGIN</span> -->
      </div>
      <!-- /.description-block -->
    </div>
    <!-- /.col -->
    <div class="col-sm-3 col-xs-6">
      <div class="description-block" id="dasbor-total-ongkir" style="color: #fb9038;font-weight: bold;">
        <!-- <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> 18%</span> -->
        <!-- <h5 class="description-header"></h5>
        <span class="description-text">TOTAL ONGKIR</span> -->
      </div>
      <!-- /.description-block -->
    </div>
  </div>
  <!-- /.row -->    
</div>
</div>
