<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// batas awal panggil library PHPMailer
// buang / komen saja bagian PHPMailer dibawah ini sampai ke batas akhir kalau tidak mau dipakai
use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// load composer autoloader
require 'vendor/autoload.php';
// batas akhir pemanggilan PHPMailer

class Auth extends CI_Controller{

  public function __construct()
  {
    parent::__construct();    
    
    $this->data['module'] = 'User';

    $this->data['company_data']    					= $this->Company_model->company_profile();

		$this->data['layout_template']    			= $this->Template_model->layout();
    $this->data['skins_template']     			= $this->Template_model->skins();

    $this->data['btn_submit'] = 'Save';
    $this->data['btn_reset']  = 'Reset';
    $this->data['btn_add']    = 'Add New Data';
    $this->data['add_action'] = base_url('admin/auth/create');

    $this->load->library('encryption');
  }

  function index()
  {
    is_login();
    is_read();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $this->data['page_title'] = $this->data['module'].' List';

    $this->data['get_all'] = $this->Auth_model->get_all();

    $this->load->view('back/auth/user_list', $this->data);
  }

  function create()
  {
    is_login();
    is_create();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $this->data['page_title'] = 'Create New '.$this->data['module'];
    $this->data['action']     = 'admin/auth/create_action';

    $this->data['get_all_combobox_usertype']     = $this->Usertype_model->get_all_combobox();
    $this->data['get_all_combobox_data_access']  = $this->Dataaccess_model->get_all_combobox();

    $this->data['name'] = [
      'name'          => 'name',
      'id'            => 'name',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('name'),
    ];
    $this->data['birthdate'] = [
      'name'          => 'birthdate',
      'id'            => 'birthdate',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'value'         => $this->form_validation->set_value('birthdate'),
    ];
    $this->data['birthplace'] = [
      'name'          => 'birthplace',
      'id'            => 'birthplace',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'value'         => $this->form_validation->set_value('birthplace'),
    ];
    $this->data['gender'] = [
      'name'          => 'gender',
      'id'            => 'gender',
      'class'         => 'form-control',
    ];
    $this->data['gender_value'] = [
      '1'             => 'Male',
      '2'             => 'Female',
    ];
    $this->data['address'] = [
      'name'          => 'address',
      'id'            => 'address',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'rows'           => '3',
      'value'         => $this->form_validation->set_value('address'),
    ];
    $this->data['phone'] = [
      'name'          => 'phone',
      'id'            => 'phone',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'value'         => $this->form_validation->set_value('phone'),
    ];
    $this->data['email'] = [
      'name'          => 'email',
      'id'            => 'email',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('email'),
    ];
    $this->data['username'] = [
      'name'          => 'username',
      'id'            => 'username',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('username'),
    ];
    $this->data['password'] = [
      'name'          => 'password',
      'id'            => 'password',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('password'),
    ];
    $this->data['password_confirm'] = [
      'name'          => 'password_confirm',
      'id'            => 'password_confirm',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('password_confirm'),
    ];
    $this->data['usertype'] = [
      'name'          => 'usertype',
      'id'            => 'usertype',
      'class'         => 'form-control',
      'required'      => '',
    ];
    $this->data['data_access_id'] = [
      'name'          => 'data_access_id[]',
      'id'            => 'data_access_id',
      'class'         => 'form-control select2',
      'required'      => '',
      'multiple'      => '',
    ];

    $this->load->view('back/auth/user_add', $this->data);
  }

  function create_action()
  {
    $this->form_validation->set_rules('name', 'Full Name', 'trim|required');
    $this->form_validation->set_rules('phone', 'Phone No', 'trim|is_numeric');
    $this->form_validation->set_rules('username', 'Username', 'trim|is_unique[users.username]|required');
    $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[users.email]|required');
    $this->form_validation->set_rules('usertype', 'Usertype', 'required');
    $this->form_validation->set_rules('data_access_id[]', 'Data Access', 'required');
    $this->form_validation->set_rules('password', 'Password', 'trim|min_length[8]|required');
    $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'trim|matches[password]|required');

    // $this->form_validation->set_message('required', '{field} wajib diisi');
    // $this->form_validation->set_message('valid_email', '{field} format email tidak benar');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->create();
    }
    else
    {
      $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);

      if($_FILES['photo']['error'] <> 4)
      {
        $nmfile = strtolower(url_title($this->input->post('username'))).date('YmdHis');

        $config['upload_path']      = './assets/images/user/';
        $config['allowed_types']    = 'jpg|jpeg|png';
        $config['max_size']         = 2048; // 2Mb
        $config['file_name']        = $nmfile;

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('photo'))
        {
          $error = array('error' => $this->upload->display_errors());
          $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$error['error'].'</div>');

          $this->create();
        }
        else
        {
          $photo = $this->upload->data();

          $config['image_library']    = 'gd2';
          $config['source_image']     = './assets/images/user/'.$photo['file_name'].'';
          $config['create_thumb']     = TRUE;
          $config['maintain_ratio']   = TRUE;
          $config['width']            = 250;
          $config['height']           = 250;

          $this->load->library('image_lib', $config);
          $this->image_lib->resize();

          $data = array(
            'name'              => $this->input->post('name'),
            'birthdate'         => $this->input->post('birthdate'),
            'birthplace'        => $this->input->post('birthplace'),
            'gender'            => $this->input->post('gender'),
            'address'           => $this->input->post('address'),
            'phone'             => $this->input->post('phone'),
            'email'             => $this->input->post('email'),
            'username'          => $this->input->post('username'),
            'password'          => $password,
            'usertype'          => $this->input->post('usertype'),
            'created_by'        => $this->session->username,
            'ip_add_reg'        => $this->input->ip_address(),
            'photo'             => $this->upload->data('file_name'),
            'photo_thumb'       => $nmfile.'_thumb'.$this->upload->data('file_ext'),
          );

          $this->Auth_model->insert($data);

          $this->db->select_max('id_users');
          $result = $this->db->get('users')->row_array();

          $data_access_id = count($this->input->post('data_access_id'));

          for($i_data_access_id = 0; $i_data_access_id < $data_access_id; $i_data_access_id++)
          {
            $datas_data_access_id[$i_data_access_id] = array(
              'user_id'           => $result['id_users'],
              'data_access_id'    => $this->input->post('data_access_id['.$i_data_access_id.']'),
            );

            $this->db->insert('users_data_access', $datas_data_access_id[$i_data_access_id]);
          }

          $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
          redirect('admin/auth');
        }
      }
      else
      {
        $data = array(
          'name'              => $this->input->post('name'),
          'birthdate'         => $this->input->post('birthdate'),
          'birthplace'        => $this->input->post('birthplace'),
          'gender'            => $this->input->post('gender'),
          'address'           => $this->input->post('address'),
          'phone'             => $this->input->post('phone'),
          'email'             => $this->input->post('email'),
          'username'          => $this->input->post('username'),
          'password'          => $password,
          'usertype'          => $this->input->post('usertype'),
          'created_by'        => $this->session->username,
          'ip_add_reg'        => $this->input->ip_address()
        );

        $this->Auth_model->insert($data);

        $this->db->select_max('id_users');
        $result = $this->db->get('users')->row_array();

        $data_access_id = count($this->input->post('data_access_id'));

        for($i_data_access_id = 0; $i_data_access_id < $data_access_id; $i_data_access_id++)
        {
          $datas_data_access_id[$i_data_access_id] = array(
            'user_id'           => $result['id_users'],
            'data_access_id'    => $this->input->post('data_access_id['.$i_data_access_id.']'),
          );

          $this->db->insert('users_data_access', $datas_data_access_id[$i_data_access_id]);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
        redirect('admin/auth');
      }
    }
  }

  function update($id)
  {
    is_login();
    is_update();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $this->data['user']     = $this->Auth_model->get_by_id($id);

    if($this->data['user'])
    {
      $this->data['page_title'] = 'Update Data '.$this->data['module'];
      $this->data['action']     = 'admin/auth/update_action';

      $this->data['get_all_combobox_usertype']      = $this->Usertype_model->get_all_combobox();
      $this->data['get_all_combobox_data_access']   = $this->Dataaccess_model->get_all_combobox();
      $this->data['get_all_data_access_old']        = $this->Dataaccess_model->get_all_data_access_old($id);

      $this->data['id_users'] = [
        'name'          => 'id_users',
        'type'          => 'hidden',
      ];
      $this->data['name'] = [
        'name'          => 'name',
        'id'            => 'name',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
      ];
      $this->data['birthdate'] = [
        'name'          => 'birthdate',
        'id'            => 'birthdate',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['birthplace'] = [
        'name'          => 'birthplace',
        'id'            => 'birthplace',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['gender'] = [
        'name'          => 'gender',
        'id'            => 'gender',
        'class'         => 'form-control',
      ];
      $this->data['gender_value'] = [
        '1'             => 'Male',
        '2'             => 'Female',
      ];
      $this->data['address'] = [
        'name'          => 'address',
        'id'            => 'address',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'rows'           => '3',
      ];
      $this->data['phone'] = [
        'name'          => 'phone',
        'id'            => 'phone',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['email'] = [
        'name'          => 'email',
        'id'            => 'email',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
      ];
      $this->data['username'] = [
        'name'          => 'username',
        'id'            => 'username',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
      ];
      $this->data['usertype'] = [
        'name'          => 'usertype',
        'id'            => 'usertype',
        'class'         => 'form-control',
        'required'      => '',
      ];
      $this->data['data_access_id'] = [
        'name'          => 'data_access_id[]',
        'id'            => 'data_access_id',
        'class'         => 'form-control select2',
        'multiple'      => '',
      ];

      $this->load->view('back/auth/user_edit', $this->data);
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">User not found</div>');
      redirect('admin/auth');
    }
  }

  function update_action()
  {
    $this->form_validation->set_rules('name', 'Full Name', 'trim|required');
    $this->form_validation->set_rules('phone', 'Phone No', 'trim|is_numeric');
    $this->form_validation->set_rules('username', 'Username', 'trim|required');
    $this->form_validation->set_rules('email', 'Email', 'valid_email|required');
    $this->form_validation->set_rules('usertype', 'Usertype', 'required');
    $this->form_validation->set_rules('data_access_id[]', 'Data Access');

    // $this->form_validation->set_message('required', '{field} wajib diisi');
    // $this->form_validation->set_message('valid_email', '{field} format email tidak benar');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->update($this->input->post('id_users'));
    }
    else
    {

      if($_FILES['photo']['error'] <> 4)
      {
        $nmfile = strtolower(url_title($this->input->post('username'))).date('YmdHis');

        $config['upload_path']      = './assets/images/user/';
        $config['allowed_types']    = 'jpg|jpeg|png';
        $config['max_size']         = 2048; // 2Mb
        $config['file_name']        = $nmfile;

        $this->load->library('upload', $config);

        $delete = $this->Auth_model->get_by_id($this->input->post('id_users'));

        $dir        = "./assets/images/user/".$delete->photo;
        $dir_thumb  = "./assets/images/user/".$delete->photo_thumb;

        if(is_file($dir))
        {
          unlink($dir);
          unlink($dir_thumb);
        }

        if(!$this->upload->do_upload('photo'))
        {
          $error = array('error' => $this->upload->display_errors());
          $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$error['error'].'</div>');

          $this->update($this->input->post('id_users'));
        }
        else
        {
          $photo = $this->upload->data();

          $config['image_library']    = 'gd2';
          $config['source_image']     = './assets/images/user/'.$photo['file_name'].'';
          $config['create_thumb']     = TRUE;
          $config['maintain_ratio']   = TRUE;
          $config['width']            = 250;
          $config['height']           = 250;

          $this->load->library('image_lib', $config);
          $this->image_lib->resize();

          $data = array(
            'name'              => $this->input->post('name'),
            'birthdate'         => $this->input->post('birthdate'),
            'birthplace'        => $this->input->post('birthplace'),
            'gender'            => $this->input->post('gender'),
            'address'           => $this->input->post('address'),
            'phone'             => $this->input->post('phone'),
            'email'             => $this->input->post('email'),
            'username'          => $this->input->post('username'),
            'usertype'          => $this->input->post('usertype'),
            'modified_by'       => $this->session->username,
            'photo'             => $this->upload->data('file_name'),
            'photo_thumb'       => $nmfile.'_thumb'.$this->upload->data('file_ext'),
          );

          $this->Auth_model->update($this->input->post('id_users'),$data);

          write_log();

          if(!empty($this->input->post('data_access_id')))
          {
            $this->db->where('user_id', $this->input->post('id_users'));
            $this->db->delete('users_data_access');

            $data_access_id = count($this->input->post('data_access_id'));

            for($i_data_access_id = 0; $i_data_access_id < $data_access_id; $i_data_access_id++)
            {
              $datas_data_access_id[$i_data_access_id] = array(
                'user_id'           => $this->input->post('id_users'),
                'data_access_id'    => $this->input->post('data_access_id['.$i_data_access_id.']'),
              );

              $this->db->insert('users_data_access', $datas_data_access_id[$i_data_access_id]);

              write_log();
            }
          }

          $this->session->set_flashdata('message', '<div class="alert alert-success">Data update succesfully</div>');
          redirect('admin/auth');
        }
      }
      else
      {
        $data = array(
          'name'              => $this->input->post('name'),
          'birthdate'         => $this->input->post('birthdate'),
          'birthplace'        => $this->input->post('birthplace'),
          'gender'            => $this->input->post('gender'),
          'address'           => $this->input->post('address'),
          'phone'             => $this->input->post('phone'),
          'email'             => $this->input->post('email'),
          'username'          => $this->input->post('username'),
          'usertype'          => $this->input->post('usertype'),
          'modified_by'       => $this->session->username,
        );

        $this->Auth_model->update($this->input->post('id_users'),$data);

        write_log();

        if(!empty($this->input->post('data_access_id')))
        {
          $this->db->where('user_id', $this->input->post('id_users'));
          $this->db->delete('users_data_access');

          $data_access_id = count($this->input->post('data_access_id'));

          for($i_data_access_id = 0; $i_data_access_id < $data_access_id; $i_data_access_id++)
          {
            $datas_data_access_id[$i_data_access_id] = array(
              'user_id'           => $this->input->post('id_users'),
              'data_access_id'    => $this->input->post('data_access_id['.$i_data_access_id.']'),
            );

            $this->db->insert('users_data_access', $datas_data_access_id[$i_data_access_id]);

            write_log();
          }
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success">Data update succesfully</div>');
        redirect('admin/auth');
      }
    }
  }

  function delete($id)
  {
    is_login();
    is_delete();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $delete = $this->Auth_model->get_by_id($id);

    if($delete)
    {
      $data = array(
        'is_delete'   => '1',
        'deleted_by'  => $this->session->username,
        'deleted_at'  => date('Y-m-d H:i:a'),
      );

      $this->Auth_model->soft_delete($id, $data);

      write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
      redirect('admin/auth');
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
      redirect('admin/auth');
    }
  }

  function delete_permanent($id)
  {
    is_login();
    is_delete();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $delete = $this->Auth_model->get_by_id($id);

    if($delete)
    {
      $dir        = "./assets/images/user/".$delete->photo;
      $dir_thumb  = "./assets/images/user/".$delete->photo_thumb;

      if(is_file($dir))
      {
        unlink($dir);
        unlink($dir_thumb);
      }

      $this->Auth_model->delete($id);

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted permanently</div>');
      redirect('admin/auth/deleted_list');
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
      redirect('admin/auth');
    }
  }

  function deleted_list()
  {
    is_login();
    is_restore();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $this->data['page_title'] = 'Deleted '.$this->data['module'].' List';

    $this->data['get_all_deleted'] = $this->Auth_model->get_all_deleted();

    $this->load->view('back/auth/user_deleted_list', $this->data);
  }

  function restore($id)
  {
    is_login();
    is_restore();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $row = $this->Auth_model->get_by_id($id);

    if($row)
    {
      $data = array(
        'is_delete'   => '0',
        'deleted_by'  => NULL,
        'deleted_at'  => NULL,
      );

      $this->Auth_model->update($id, $data);

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data restored successfully</div>');
      redirect('admin/auth/deleted_list');
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
      redirect('admin/auth');
    }
  }

  function activate($id)
  {
    is_login();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $this->Auth_model->update($id, array('is_active'=>'1'));

    $this->session->set_flashdata('message', '<div class="alert alert-success">Data activate successfully</div>');
    redirect('admin/auth');
  }

  function deactivate($id)
  {
    is_login();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }

    $this->Auth_model->update($id, array('is_active'=>'0'));

    $this->session->set_flashdata('message', '<div class="alert alert-success">Data deactivate successfully</div>');
    redirect('admin/auth');
  }

  function update_profile($id)
  {
    is_login();

    $this->data['user']     = $this->Auth_model->get_by_id($id);

    if($id != $this->session->id_users)
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t change other user profile</div>');
      redirect('admin/dashboard');
    }

    if($this->data['user'])
    {
      $this->data['page_title'] = 'Update Profile';
      $this->data['action']     = 'admin/auth/update_profile_action';

      $this->data['get_all_data_access_old']        = $this->Dataaccess_model->get_all_data_access_old($id);

      $this->data['id_users'] = [
        'name'          => 'id_users',
        'type'          => 'hidden',
      ];
      $this->data['name'] = [
        'name'          => 'name',
        'id'            => 'name',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
      ];
      $this->data['birthdate'] = [
        'name'          => 'birthdate',
        'id'            => 'birthdate',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['birthplace'] = [
        'name'          => 'birthplace',
        'id'            => 'birthplace',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['gender'] = [
        'name'          => 'gender',
        'id'            => 'gender',
        'class'         => 'form-control',
      ];
      $this->data['gender_value'] = [
        '1'             => 'Male',
        '2'             => 'Female',
      ];
      $this->data['address'] = [
        'name'          => 'address',
        'id'            => 'address',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'rows'           => '3',
      ];
      $this->data['phone'] = [
        'name'          => 'phone',
        'id'            => 'phone',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['email'] = [
        'name'          => 'email',
        'id'            => 'email',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
      ];
      $this->data['username'] = [
        'name'          => 'username',
        'id'            => 'username',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
      ];
      $this->data['usertype'] = [
        'name'          => 'usertype',
        'id'            => 'usertype',
        'class'         => 'form-control',
        'required'      => '',
      ];

      $this->load->view('back/auth/update_profile', $this->data);
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">User not found</div>');
      redirect('admin/dashboard');
    }
  }

  function update_profile_action()
  {
    $this->form_validation->set_rules('name', 'Full Name', 'trim|required');
    $this->form_validation->set_rules('phone', 'Phone No', 'trim|is_numeric');
    $this->form_validation->set_rules('username', 'Username', 'trim|required');
    $this->form_validation->set_rules('email', 'Email', 'valid_email|required');

    // $this->form_validation->set_message('required', '{field} wajib diisi');
    // $this->form_validation->set_message('valid_email', '{field} format email tidak benar');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->update($this->input->post('id_users'));
    }
    else
    {
      if($_FILES['photo']['error'] <> 4)
      {
        $nmfile = strtolower(url_title($this->input->post('username'))).date('YmdHis');

        $config['upload_path']      = './assets/images/user/';
        $config['allowed_types']    = 'jpg|jpeg|png';
        $config['max_size']         = 2048; // 2Mb
        $config['file_name']        = $nmfile;

        $this->load->library('upload', $config);

        $delete = $this->Auth_model->get_by_id($this->input->post('id_users'));

        $dir        = "./assets/images/user/".$delete->photo;
        $dir_thumb  = "./assets/images/user/".$delete->photo_thumb;

        if(is_file($dir))
        {
          unlink($dir);
          unlink($dir_thumb);
        }

        if(!$this->upload->do_upload('photo'))
        {
          $error = array('error' => $this->upload->display_errors());
          $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$error['error'].'</div>');

          $this->update($this->input->post('id_users'));
        }
        else
        {
          $photo = $this->upload->data();

          $config['image_library']    = 'gd2';
          $config['source_image']     = './assets/images/user/'.$photo['file_name'].'';
          $config['create_thumb']     = TRUE;
          $config['maintain_ratio']   = TRUE;
          $config['width']            = 250;
          $config['height']           = 250;

          $this->load->library('image_lib', $config);
          $this->image_lib->resize();

          $data = array(
            'name'              => $this->input->post('name'),
            'birthdate'         => $this->input->post('birthdate'),
            'birthplace'        => $this->input->post('birthplace'),
            'gender'            => $this->input->post('gender'),
            'address'           => $this->input->post('address'),
            'phone'             => $this->input->post('phone'),
            'email'             => $this->input->post('email'),
            'username'          => $this->input->post('username'),
            'modified_by'       => $this->session->username,
            'photo'             => $this->upload->data('file_name'),
            'photo_thumb'       => $nmfile.'_thumb'.$this->upload->data('file_ext'),
          );

          $this->Auth_model->update($this->input->post('id_users'),$data);

          write_log();

          $this->session->set_flashdata('message', '<div class="alert alert-success">Data update succesfully</div>');
          redirect('admin/auth/update_profile/'.$this->session->id_users);
        }
      }
      else
      {
        $data = array(
          'name'              => $this->input->post('name'),
          'birthdate'         => $this->input->post('birthdate'),
          'birthplace'        => $this->input->post('birthplace'),
          'gender'            => $this->input->post('gender'),
          'address'           => $this->input->post('address'),
          'phone'             => $this->input->post('phone'),
          'email'             => $this->input->post('email'),
          'username'          => $this->input->post('username'),
          'modified_by'       => $this->session->username,
        );

        $this->Auth_model->update($this->input->post('id_users'),$data);

        write_log();

        $this->session->set_flashdata('message', '<div class="alert alert-success">Data update succesfully</div>');
        redirect('admin/auth/update_profile/'.$this->session->id_users);
      }
    }
  }

  function change_password()
  {
    is_login();

    $this->data['page_title'] = 'Change Password';
    $this->data['action']     = 'admin/auth/change_password_action';

    $this->data['get_all_users']     = $this->Auth_model->get_all_combobox();

    $this->data['user_id'] = [
      'name'          => 'user_id',
      'type'          => 'hidden',
      'class'          => 'form-control',
    ];
    $this->data['new_password'] = [
      'name'          => 'new_password',
      'id'            => 'new_password',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
    ];
    $this->data['confirm_new_password'] = [
      'name'          => 'confirm_new_password',
      'id'            => 'confirm_new_password',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
    ];

    $this->load->view('back/auth/change_password', $this->data);
  }

  function change_password_action()
  {
    $this->form_validation->set_rules('new_password', 'Password', 'min_length[8]|required');
    $this->form_validation->set_rules('confirm_new_password', 'Password Confirmation', 'matches[new_password]|required');

    // $this->form_validation->set_message('required', '{field} wajib diisi');
    // $this->form_validation->set_message('valid_email', '{field} format email tidak benar');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() == FALSE)
    {
      $this->change_password();
    }
    else
    {
      $password = password_hash($this->input->post('new_password'), PASSWORD_BCRYPT);

      if(is_superadmin()){$id_user = $this->input->post('user_id');}
      else{$id_user = $this->session->id_users;}

      $data = array(
        'password' => $password
      );

      $this->Auth_model->update($id_user, $data);

      write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Password change successfully</div>');
      redirect('admin/auth/change_password');
    }
  }

  function login()
  {
    $this->data['page_title'] = 'Login';
    $this->data['action']     = 'admin/auth/login';

    $this->form_validation->set_rules('username', 'Username', 'trim|required');
    $this->form_validation->set_rules('password', 'Password', 'required');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    // $this->form_validation->set_message('required', '{field} wajib diisi');

    if($this->form_validation->run() === TRUE)
    {
      $row = $this->Auth_model->get_by_username($this->input->post('username'));

      if(!$row->username)
      {
        $this->session->set_flashdata('message', '<div class="alert alert-danger">Username not found</div>');
        redirect('admin/auth/login');
      }
      elseif($row->is_active == 0)
      {
        $this->session->set_flashdata('message', '<div class="alert alert-danger">Your account is INACTIVE</div>');
        redirect('admin/auth/login');
      }
      elseif(!password_verify($this->input->post('password'), $row->password))
      {
        $log = $this->Auth_model->get_total_login_attempts_per_user($this->input->post('username'));

        // kunci akun kalau gagal input password 3x
        if($log > 2)
        {
          $this->Auth_model->lock_account($this->input->post('username'), array('is_active' => '0'));

          $this->Auth_model->clear_login_attempt($this->input->post('username'));

          $this->session->set_flashdata('message', '<div class="alert alert-danger">Too many login attemps, we set your account to INACTIVE. Please contact superadmin to open it again.</div>');
          redirect('admin/auth/login');
        }
        else
        {
          $this->Auth_model->insert_login_attempt(array('ip_address'=>$this->input->ip_address(), 'username'=>$this->input->post('username')));

          $this->session->set_flashdata('message', '<div class="alert alert-danger">Wrong Password</div>');
          redirect('admin/auth/login');
        }

      }
      else
      {
        $this->Auth_model->clear_login_attempt($this->input->post('username'));

        $session = array(
          'id_users'    => $row->id_users,
          'name'        => $row->name,
          'username'    => $row->username,
          'email'       => $row->email,
          'usertype'    => $row->usertype,
          'photo'       => $row->photo,
          'photo_thumb' => $row->photo_thumb,
        );

        $this->session->set_userdata($session);

        $this->Auth_model->update($this->session->id_users, array('last_login'=>date('Y-m-d H:i:s')));

        redirect('admin/dashboard');
      }
    }
    else
    {
      $this->data['username'] = [
        'name'              => 'username',
        'id'                => 'username',
        'class'             => 'form-control',
        'placeholder'       => 'Please insert your username',
        'value'             => $this->form_validation->set_value('username'),
      ];

      $this->data['password'] = [
        'name'              => 'password',
        'id'                => 'password',
        'class'             => 'form-control',
        'placeholder'       => 'Please insert your password',
        'value'             => $this->form_validation->set_value('password'),
      ];

      $this->load->view('back/auth/login', $this->data);
    }
  }

  function logout()
  {
    $this->session->sess_destroy();

    redirect('admin/auth/login');
  }

  function forgot_password()
  {
    $this->data['page_title'] = "Forgot Password";
    $this->data['action']     = 'admin/auth/forgot_password_process';

    $this->data['email'] = [
      'name'          => 'email',
      'id'            => 'email',
      'class'         => 'form-control',
      'required'      => '',
    ];

    $this->load->view('back/auth/forgot_password', $this->data);
  }

  function forgot_password_process()
  {
    $this->load->helper(array('random_str'));

    // siapkan variabel nama akun email webmail/ gmail
    $webmail_name = $this->data['company_data']->company_webmail_name;
    $webmail_pass = $this->encryption->decrypt($this->data['company_data']->company_webmail_pass);

    $gmail_name   = $this->data['company_data']->company_gmail_name;
    $gmail_pass   = $this->encryption->decrypt($this->data['company_data']->company_gmail_pass);

    if(isset($_POST['submit']))
    {
      $this->form_validation->set_rules('email', 'Email', 'valid_email|required');
      $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

      if($this->form_validation->run() == FALSE)
      {
        $this->forgot_password();
      }
      else
      {
        $email_check = $this->Auth_model->get_by_email($this->input->post('email'));

        if(!$email_check)
        {
          $this->session->set_flashdata('message', '<div class="alert alert-danger">Sorry, we can\'t find your email.</div>');
          redirect('admin/auth/forgot_password');
        }
        else
        {
          // buat random hash / kata acak
          $hash   = random_str(50);
          // ambil nilai email yg diinput user
          $email  = $this->input->post('email');

          // // BATAS AWAL CONFIG EMAIL DENGAN CI SECARA DEFAULT (tidak jalan kalau project masih localhost / offline)
          // // buang bagian dibawah $config ini kalau tidak mau pakai bawaan ci
          // $config = [
          // 'protocol' => 'smtp',
          // 'smtp_host' => 'ssl://operasional.brightyofficial.com;159.223.48.169',
          // 'smtp_port' => 465,
          // 'smtp_user' => 'no-reply@operasional.brightyofficial.com',
          // 'smtp_pass' => 'Bismillah123!',
          // 'mailtype'  => 'html', 
          // 'charset'   => 'iso-8859-1'
          // //   'protocol' 	=> 'sendmail',
          // //   'mailtype' 	=> 'html',
          // //   'wordwrap' 	=> TRUE,
          // //   'priority' 	=> 1,
          // ];

          // // panggil library email
          // $this->load->library('email', $config);
          // // set asal pengirim email dan nama pengirim
          // $this->email->from('no-reply@'.$_SERVER['SERVER_NAME'], $this->data['company_data']->company_name);
          // // set tujuan / penerima
          // $this->email->to($email);
          // // set judul email
          // $this->email->subject('Forgot Password Attempt dgn Native CI');
          // $mailContent = '
          //   <h3>Hi, '.$email.'</h3>
          //   In a few moment ago, we received your request to reset your account password <br>
          //   To reset your password, please click the following link below:
          //   <p><a href="'.base_url('auth/reset_password/'.$hash).'">'.$hash.'</a></p>
          //   <p>If you did not initiate this request, you may safely ignore this message. The request will automatically expired.</p>
          //   <hr>
          //   <b>THIS EMAIL IS CREATED AUTOMATICALLY. PLEASE DO NOT SEND A REPLY TO THIS EMAIL.</b>
          //   <hr>
          //   <b>'.$this->data['company_data']->company_name.'</b>
          //   <br>Address: '.$this->data['company_data']->company_address.'
          //   <br>Phone: '.$this->data['company_data']->company_phone.'
          //   <br>Fax: '.$this->data['company_data']->company_fax.'
          //   <br>Email: '.$webmail_name.' / '.$gmail_name.'
          // ';

          // // siapkan ke dalam fungsi bawaan ci
          // $this->email->message($mailContent);

          // // mulai proses pengiriman email
          // if($this->email->send())
          // {
          //   $this->Auth_model->update_by_email($email, array('code_forgotten'=>$hash));

          //   $this->session->set_flashdata('message', '<div class="alert alert-success">Your password has been successfully reset. Please check your email.</div>');
          //   redirect('admin/auth/forgot_password');
          // }
          // else
          // {
          //   // hilangkan komen baris dibawah ini kalau mau debugging
          //   // echo "Message could not be sent. Mailer Error: ".$mail->ErrorInfo."";

          //   $this->session->set_flashdata('message', '<div class="alert alert-danger">Sorry, forgot password failed. Please try again later.</div>');
          //   redirect('admin/auth/forgot_password');
          // }
          // // BATAS AKHIR CONFIG EMAIL DENGAN CI SECARA DEFAULT

          // =================================================================================================================================

          // BATAS AWAL CONFIG EMAIL DENGAN PHPMAILER (SMTP hosting)
          // buang bagian dibawah $config ini kalau tidak mau pakai PHPMailer (SMTP hosting)
          
          // panggil library phpmailer
          $this->load->library('phpmailer_lib');
          
          // set PHPMailer object
          $mail = $this->phpmailer_lib->load();
          
          // SMTP configuration
          // Set email format to HTML
          // $mail->SMTPDebug = 3;        // hilangkan komen baris ini kalau mau debugging (1 = client, 2 = client and server, 3 = client, server, connection, 4 = low level information)
          $mail->isSMTP();
          $mail->ClearAddresses();
           $mail->ClearCCs();
           $mail->ClearBCCs();
          $mail->isHTML(true);
          // $mail->Host        = $_SERVER['SERVER_NAME'];          // nama domain web Anda
          $mail->Host        = 'ssl://brightyofficial.com;159.223.48.169';          // nama domain web Anda
          $mail->SMTPAuth    = true;
          $mail->SMTPSecure  = 'ssl';
          $mail->SMTPOptions  = array( 'ssl'  => array( 'verify_peer' => false,
                                                        'verify_peer_name'  => false,
                                                        'allow_self_signed' => false
                                                      )
                                      );
          $mail->Port        = 465;
          $mail->Username    = $webmail_name;
          $mail->Password    = $webmail_pass;
          
          $mail->setFrom($webmail_name, $this->data['company_data']->company_name);
          $mail->addReplyTo($webmail_name);
          
          // penerima email
          $mail->addAddress($email);
          
          // judul email
          $mail->Subject = 'Forgot Password Attempt dgn SMTP Hosting';
          
          // isi pesan / email
          $mailContent = '
            <h3>Hi, '.$email.'</h3>
            In a few moment ago, we received your request to reset your account password <br>
            To reset your password, please click the following link below:
            <p><a href="'.base_url('auth/reset_password/'.$hash).'">'.$hash.'</a></p>
            <p>If you did not initiate this request, you may safely ignore this message. The request will automatically expired.</p>
            <hr>
            <b>THIS EMAIL IS CREATED AUTOMATICALLY. PLEASE DO NOT SEND A REPLY TO THIS EMAIL.</b>
            <hr>
            <b>'.$this->data['company_data']->company_name.'</b>
            <br>Address: '.$this->data['company_data']->company_address.'
            <br>Phone: '.$this->data['company_data']->company_phone.'
            <br>Fax: '.$this->data['company_data']->company_fax.'
            <br>Email: '.$webmail_name.' / '.$gmail_name.'
          ';
          
          // set email yg akan dikirim dalam variabel
          $mail->Body = $mailContent;
          
          // proses kirim email
          if($mail->send())
          {
            $this->Auth_model->update_by_email($email, array('code_forgotten'=>$hash));
          
            $this->session->set_flashdata('message', '<div class="alert alert-success">Your password has been successfully reset. Please check your email.</div>');
            redirect('admin/auth/forgot_password');
          }
          else
          {
            // hilangkan komen baris dibawah ini kalau mau debugging
            // echo "Message could not be sent. Mailer Error: ".$mail->ErrorInfo."";
          
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Sorry, forgot password failed. Please try again later.</div>');
            redirect('admin/auth/forgot_password');
          }
          // BATAS AKHIR CONFIG EMAIL DENGAN PHPMAILER (SMTP hosting)

          // =================================================================================================================================

          // // BATAS AWAL CONFIG EMAIL DENGAN PHPMAILER (SMTP gmail)
          // // buang bagian dibawah $config ini kalau tidak mau pakai PHPMailer (SMTP gmail)
          //
          // // panggil library phpmailer
          // $this->load->library('phpmailer_lib');
          //
          // // set PHPMailer object
          // $mail = $this->phpmailer_lib->load();
          //
          // // SMTP configuration
          // // Set email format to HTML
          // // $mail->SMTPDebug = 3;        // hilangkan komen baris ini kalau mau debugging (1 = client, 2 = client and server, 3 = client, server, connection, 4 = low level information)
          // $mail->isSMTP();
          // $mail->isHTML(true);
          // $mail->Host       = 'smtp.gmail.com';
          // $mail->SMTPAuth   = true;
          // $mail->SMTPSecure = 'tls';              // tls atau ssl
          // $mail->Port       = 587;                // 25 atau 26 atau 465 atau 587
          // $mail->Username   = $gmail_name;
          // $mail->Password   = $gmail_pass;
          //
          // $mail->setFrom($gmail_name, $this->data['company_data']->company_name);
          // $mail->addReplyTo($gmail_name);
          //
          // // penerima email
          // $mail->addAddress($email);
          //
          // // judul email
          // $mail->Subject = 'Forgot Password Attempt pake SMTP GMAIL';
          //
          // // isi pesan / email
          // $mailContent = '
          //   <h3>Hi, '.$email.'</h3>
          //   In a few moment ago, we received your request to reset your account password <br>
          //   To reset your password, please click the following link below:
          //   <p><a href="'.base_url('auth/reset_password/'.$hash).'">'.$hash.'</a></p>
          //   <p>If you did not initiate this request, you may safely ignore this message. The request will automatically expired.</p>
          //   <hr>
          //   <b>THIS EMAIL IS CREATED AUTOMATICALLY. PLEASE DO NOT SEND A REPLY TO THIS EMAIL.</b>
          //   <hr>
          //   <b>'.$this->data['company_data']->company_name.'</b>
          //   <br>Address: '.$this->data['company_data']->company_address.'
          //   <br>Phone: '.$this->data['company_data']->company_phone.'
          //   <br>Fax: '.$this->data['company_data']->company_fax.'
          //   <br>Email: '.$webmail_name.' / '.$gmail_name.'
          // ';
          //
          // // set email yg akan dikirim dalam variabel
          // $mail->Body = $mailContent;
          //
          // // proses kirim email
          // if($mail->send())
          // {
          //   $this->Auth_model->update_by_email($email, array('code_forgotten'=>$hash));
          //
          //   $this->session->set_flashdata('message', '<div class="alert alert-success">Your password has been successfully reset. Please check your email.</div>');
          //   redirect('admin/auth/forgot_password');
          // }
          // else
          // {
          //   // hilangkan komen baris dibawah ini kalau mau debugging
          //   // echo "Message could not be sent. Mailer Error: ".$mail->ErrorInfo."";
          //
          //   $this->session->set_flashdata('message', '<div class="alert alert-danger">Sorry, forgot password failed. Please try again later.</div>');
          //   redirect('admin/auth/forgot_password');
          // }
          // // BATAS AKHIR CONFIG EMAIL DENGAN PHPMAILER (SMTP gmail)

        }
      }
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You must enter the submit button!</div>');
      redirect('admin/auth/forgot_password');
    }
  }

  function reset_password($code = NULL)
  {
    $this->data['reset'] = $this->Auth_model->get_by_code_forgotten($code);

    if(!$this->data['reset'] OR $code == NULL)
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">Sorry, we can\'t found the forgotten code.</div>');
      redirect('admin/auth/forgot_password');
    }
    else
    {
      $this->data['page_title'] = 'Reset Password';
      $this->data['action'] = 'auth/reset_password_process/'.$code;

      $this->data['code_forgotten'] = [
        'name'      => 'code_forgotten',
        'type'      => 'hidden',
        'value'     => $this->uri->segment(3),
      ];
      $this->data['new_password'] = [
        'name'          => 'new_password',
        'class'         => 'form-control',
        'placeholder'   => 'Insert your New Password',
        'required'      => '',
      ];

      $this->load->view('back/auth/reset_password', $this->data);

    }
  }

  function reset_password_process()
  {
    $this->form_validation->set_rules('new_password', 'New Password', 'min_length[8]|required');
    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() == FALSE)
    {
      $this->reset_password($this->input->post('code_forgotten'));
    }
    else
    {
      $password = password_hash($this->input->post('new_password'), PASSWORD_BCRYPT);

      $data = array(
        'password'        => $password,
        'code_forgotten'  => '',
      );

      $this->Auth_model->update_by_code_forgotten($this->input->post('code_forgotten'), $data);
      $this->session->set_flashdata('message', '<div class="alert alert-success">New Password saved successfully.</div>');
      redirect('admin/auth/login');
    }
  }

}
