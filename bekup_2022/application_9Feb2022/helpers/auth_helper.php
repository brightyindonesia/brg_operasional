<?php
defined('BASEPATH') OR exit('No direct script access allowed');

  function is_login()
  {
    $CI =& get_instance();

    $username = $CI->session->username;

    if($username == NULL)
    {
      $CI->session->set_flashdata('message', '<div class="alert alert-danger">You must login first</div>');

      redirect('admin/auth/login');
    }
  }

  function is_superadmin()
  {
    $CI =& get_instance();

    $usertype = $CI->session->usertype;

    if($usertype == '1')
    {
      return $usertype;
    }

    return null;
  }

  function is_admin()
  {
    $CI =& get_instance();

    $usertype = $CI->session->usertype;

    if($usertype == '2')
    {
      return $usertype;
    }

    return null;
  }

  function is_read()
  {
    $CI =& get_instance();

    if($CI->Auth_model->get_access_read() == NULL)
    {
      $CI->session->set_flashdata('message', '<div class="alert alert-danger">You dont have access to read data</div>');

      redirect('admin/dashboard');
    }
  }

  function is_create()
  {
    $CI =& get_instance();

    if($CI->Auth_model->get_access_create() == NULL)
    {
      $CI->session->set_flashdata('message', '<div class="alert alert-danger">You dont have access to create data</div>');

      redirect('admin/dashboard');
    }
  }

  function is_update()
  {
    $CI =& get_instance();

    if($CI->Auth_model->get_access_update() == NULL)
    {
      $CI->session->set_flashdata('message', '<div class="alert alert-danger">You dont have access to update data</div>');

      redirect('admin/dashboard');
    }
  }

  function is_delete()
  {
    $CI =& get_instance();

    if($CI->Auth_model->get_access_delete() == NULL)
    {
      $CI->session->set_flashdata('message', '<div class="alert alert-danger">You dont have access to delete data</div>');

      redirect('admin/dashboard');
    }
  }

  function is_restore()
  {
    $CI =& get_instance();

    if($CI->Auth_model->get_access_restore() == NULL)
    {
      $CI->session->set_flashdata('message', '<div class="alert alert-danger">You dont have access to restore data</div>');

      redirect('admin/dashboard');
    }
  }

?>
