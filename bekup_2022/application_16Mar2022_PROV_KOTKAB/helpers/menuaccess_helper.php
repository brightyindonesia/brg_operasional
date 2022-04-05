<?php
defined('BASEPATH') OR exit('No direct script access allowed');

  function menuaccess_check()
  {
    $CI =& get_instance();

    $username = $CI->session->username;

    if($CI->Menuaccess_model->get_menuAccess_by_user() == NULL)
    {
      $CI->session->set_flashdata('message', '<div class="alert alert-danger">You dont have access to the last page</div>');
      redirect('admin/dashboard');
    }
  }

  function submenuaccess_check()
  {
    $CI =& get_instance();

    $username = $CI->session->username;

    if($CI->Menuaccess_model->get_subMenuAccess_by_user() == NULL)
    {
      $CI->session->set_flashdata('message', '<div class="alert alert-danger">You dont have access to the last page</div>');
      redirect('admin/dashboard');
    }
  }

?>
