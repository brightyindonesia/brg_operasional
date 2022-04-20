<header class="main-header">
  <!-- Logo -->
  <a href="<?php echo base_url('admin/dashboard') ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>B</b>ID</span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><img src="<?php echo base_url('assets/images/company/'.$company_data->company_photo_thumb) ?>" alt="Company Logo"> <?php // echo $company_data->company_name ?></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-gear"></i>
          </a>
          <ul class="dropdown-menu">
            <li class="user-body">
              <div class="row">
                <div class="col-xs-6 text-center">
                  <a href="<?php echo base_url('admin/auth/update_profile/'.$this->session->id_users) ?>">Update Profile</a>
                </div>
                <div class="col-xs-6 text-center">
                  <a href="<?php echo base_url('admin/auth/change_password') ?>">Change Password</a>
                </div>
              </div>
              <!-- /.row -->
            </li>
            <li class="user-footer">
              <div class="pull-right">
                <a href="<?php echo base_url('admin/auth/logout') ?>" class="btn btn-default btn-flat">Logout</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>

<div class="modal fade" id="modal-proses" data-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <div style="text-align: center;">
          <img width="50" src="<?php echo base_url(); ?>assets/images/loading.gif" /> <br />Data Sedang diproses...              
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
