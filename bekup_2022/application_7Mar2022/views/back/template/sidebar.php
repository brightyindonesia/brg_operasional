<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <?php if($this->session->photo_thumb == NULL){ ?>
          <img src="<?php echo base_url('assets/images/noimage.jpg') ?>" class="user-image img-circle elevation-2" alt="No Image Found">
        <?php } else{ ?>
          <img src="<?php echo base_url('assets/images/user/'.$this->session->photo_thumb) ?>" class="user-image img-circle elevation-2" alt="<?php echo $this->session->name ?>">
        <?php } ?>
      </div>
      <div class="pull-left info">
        <p><?php echo $this->session->name ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <div class="modal" id="modal-proses" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <div style="text-align: center;">
              <img width="50" src="<?php echo base_url(); ?>assets/images/loading.gif" /> <br />Data Sedang diproses...              
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN MENU</li>
      <li class="<?php if(current_url() == base_url('admin/dashboard')){echo "active";} ?> ">
        <a href="<?php echo base_url('admin/dashboard') ?>"><i class="fa fa-dashboard"></i> <span>Dashboard Utama</span></a>
      </li>

      <?php 
        if ($this->session->userdata('usertype') == 1 || $this->session->userdata('usertype') == 7 || $this->session->userdata('usertype') == 11) {
      ?>
        <li class="<?php if(current_url() == base_url('admin/dashboard_crm')){echo "active";} ?> ">
          <a href="<?php echo base_url('admin/dashboard_crm') ?>"><i class="fa fa-dashboard"></i> <span>Dashboard CRM</span></a>
        </li>
      <?php
        }
      ?>
      
      <?php
      date_default_timezone_set("Asia/Jakarta");
      $this->db->join('menu_access', 'menu.id_menu = menu_access.menu_id');
      $this->db->join('submenu', 'menu.id_menu = submenu.id_submenu', 'LEFT');
      $this->db->where('menu_access.usertype_id', $this->session->usertype);
      $this->db->where('menu.is_active', '1');
      $this->db->group_by('menu.id_menu');
      $this->db->order_by('menu.order_no');
      $menu = $this->db->get('menu')->result();
      ?>

      <?php foreach($menu as $m){ ?>
        <!-- jika menu tidak punya submenu -->
        <?php if($m->submenu_id == NULL){ ?>
          <?php if(current_url() == base_url('admin/').$m->menu_controller.'/'.$m->menu_function){$active = 'class="active"';}else{$active = '';} ?>
            <li <?php echo $active ?>> <a href="<?php echo base_url('admin/').$m->menu_controller.'/'.$m->menu_function ?>">
              <i class="fa <?php echo $m->menu_icon ?>"></i> <span><?php echo $m->menu_name ?></span> </a>
            </li>
          <?php }
          else
          {
            $this->db->join('menu', 'submenu.id_submenu = menu.id_menu', 'LEFT');
            $this->db->join('menu_access', 'submenu.id_submenu = menu_access.submenu_id');
            $this->db->where('submenu.menu_id', $m->id_menu);
            $this->db->where('menu_access.usertype_id', $this->session->usertype);
            $this->db->order_by('submenu.order_no');
            $submenu = $this->db->get('submenu')->result();

            if($this->uri->segment(2) == $m->menu_controller){$actives = 'class="active treeview menu-open"';}
            else{$actives = 'class="treeview"';}
          ?>
            <li <?php echo $actives ?>>
              <a href="#">
                <i class="fa <?php echo $m->menu_icon ?>"></i> <span><?php echo $m->menu_name ?></span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <?php foreach($submenu as $sm){ ?>
                  <?php if(current_url() == base_url('admin/').$m->menu_controller.'/'.$sm->submenu_function){$active = 'class="active"';}else{$active = '';} ?>
                  <li <?php echo $active ?>><a href="<?php echo base_url('admin/').$m->menu_controller.'/'.$sm->submenu_function ?>"><i class="fa fa-circle-o"></i> <?php echo $sm->submenu_name ?></a> </li>
                <?php } ?>
              </ul>
            </li>
        <?php
          }
        } ?>

      <li class="header">SETTINGS</li>
      <?php if(is_superadmin()){ ?>
        <li class="<?php if($this->uri->segment(2) == 'changelog'){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Change Log</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php if($this->uri->segment(2) == 'changelog' && $this->uri->segment(3) == 'systemlog'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/changelog/systemlog') ?>"><i class="fa fa-circle-o"></i> System Log</a></li>
            <li <?php if($this->uri->segment(2) == 'changelog' && $this->uri->segment(3) == 'applog'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/changelog/applog') ?>"><i class="fa fa-circle-o"></i> App Log</a></li>
          </ul>
        </li>
        <li class="<?php if($this->uri->segment(2) == 'company'){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-building"></i> <span>Company Profile</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php if($this->uri->segment(2) == 'company' && $this->uri->segment(3) == 'update_bio'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/company/update_bio/1') ?>"><i class="fa fa-circle-o"></i> Bio</a></li>
            <li <?php if($this->uri->segment(2) == 'company' && $this->uri->segment(3) == 'update_webmail'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/company/update_webmail/1') ?>"><i class="fa fa-circle-o"></i> Webmail Account</a></li>
            <li <?php if($this->uri->segment(2) == 'company' && $this->uri->segment(3) == 'update_gmail'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/company/update_gmail/1') ?>"><i class="fa fa-circle-o"></i> Gmail Account</a></li>
          </ul>
        </li>
        <li class="<?php if($this->uri->segment(2) == 'auth' && $this->uri->segment(3) != 'log_list'){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>User Management</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php if($this->uri->segment(2) == 'auth' && $this->uri->segment(3) == 'create'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/auth/create') ?>"><i class="fa fa-circle-o"></i> Add User</a></li>
            <li <?php if($this->uri->segment(2) == 'auth' && $this->uri->segment(3) == ''){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/auth') ?>"><i class="fa fa-circle-o"></i> User List</a></li>
            <li <?php if($this->uri->segment(2) == 'auth' && $this->uri->segment(3) == 'deleted_list'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/auth/deleted_list') ?>"><i class="fa fa-circle-o"></i> Recycle Bin</a></li>
          </ul>
        </li>
        <li class="<?php if($this->uri->segment(2) == 'usertype'){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-legal"></i> <span>Usertype Management</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php if($this->uri->segment(2) == 'usertype' && $this->uri->segment(3) == 'create'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/usertype/create') ?>"><i class="fa fa-circle-o"></i> Add Usertype</a></li>
            <li <?php if($this->uri->segment(2) == 'usertype' && $this->uri->segment(3) == ''){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/usertype') ?>"><i class="fa fa-circle-o"></i> Usertype List</a></li>
          </ul>
        </li>
        <li class="<?php if($this->uri->segment(2) == 'menu'){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-list"></i> <span>Menu Management</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php if($this->uri->segment(2) == 'menu' && $this->uri->segment(3) == 'create'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/menu/create') ?>"><i class="fa fa-circle-o"></i> Add Menu</a></li>
            <li <?php if($this->uri->segment(2) == 'menu' && $this->uri->segment(3) == ''){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/menu') ?>"><i class="fa fa-circle-o"></i> Menu List</a></li>
          </ul>
        </li>
        <li class="<?php if($this->uri->segment(2) == 'submenu'){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-list"></i> <span>SubMenu Management</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php if($this->uri->segment(2) == 'submenu' && $this->uri->segment(3) == 'create'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/submenu/create') ?>"><i class="fa fa-circle-o"></i> Add SubMenu</a></li>
            <li <?php if($this->uri->segment(2) == 'submenu' && $this->uri->segment(3) == ''){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/submenu') ?>"><i class="fa fa-circle-o"></i> SubMenu List</a></li>
          </ul>
        </li>
        <li class="<?php if($this->uri->segment(2) == 'menuaccess'){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Menu Access Management</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php if($this->uri->segment(2) == 'menuaccess' && $this->uri->segment(3) == 'create'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/menuaccess/create') ?>"><i class="fa fa-circle-o"></i> Add Menu Access</a></li>
            <li <?php if($this->uri->segment(2) == 'menuaccess' && $this->uri->segment(3) == ''){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/menuaccess') ?>"><i class="fa fa-circle-o"></i> Menu Access List</a></li>
          </ul>
        </li>
        <li class="<?php if($this->uri->segment(2) == 'template'){echo "active";} ?> treeview">
          <a href="#">
            <i class="fa fa-gears"></i> <span>Template Management</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li <?php if($this->uri->segment(2) == 'template' && $this->uri->segment(4) == '1'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/template/update/1') ?>"><i class="fa fa-circle-o"></i> Layout</a></li>
            <li <?php if($this->uri->segment(2) == 'template' && $this->uri->segment(4) == '2'){echo 'class="active"';} ?>><a href="<?php echo base_url('admin/template/update/2') ?>"><i class="fa fa-circle-o"></i> Skins</a></li>
          </ul>
        </li>
      <?php } ?>
      <li class="<?php if($this->uri->segment(3) == 'update_profile'){echo "active";} ?>" ><a href="<?php echo base_url('admin/auth/update_profile/'.$this->session->id_users) ?>"><i class="fa fa-pencil"></i> <span>Edit Profile</span></a></li>
      <li class="<?php if($this->uri->segment(3) == 'change_password'){echo "active";} ?>" ><a href="<?php echo base_url('admin/auth/change_password') ?>"><i class="fa fa-asterisk"></i> <span>Change Password</span></a></li>
      <li><a href="<?php echo base_url('admin/auth/logout') ?>"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
