<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends CI_Controller{

  public function __construct()
  {
    parent::__construct();    

    $this->data['module'] = 'Company';

    $this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    $this->data['skins_template']     			= $this->Template_model->skins();

    $this->data['btn_submit'] = 'Save';
    $this->data['btn_reset']  = 'Reset';

    // load library encryption
    $this->load->library('encryption');

    is_login();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }
  }

  function update_bio($id = '')
  {
    $this->data['company']     = $this->Company_model->get_by_id($id);

    if($this->data['company'])
    {
      $this->data['page_title'] = 'Update Data '.$this->data['module'];
      $this->data['action']     = 'admin/company/update_bio_action';

      $this->data['id_company'] = [
        'name'          => 'id_company',
        'type'          => 'hidden',
      ];
      $this->data['company_name'] = [
        'name'          => 'company_name',
        'id'            => 'company_name',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        // 'required'      => '',
      ];
      $this->data['company_desc'] = [
        'name'          => 'company_desc',
        'id'            => 'company_desc',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'rows' => '5',
      ];
      $this->data['company_address'] = [
        'name'          => 'company_address',
        'id'            => 'company_address',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
        'rows'          => '5',
      ];
      $this->data['company_maps'] = [
        'name'          => 'company_maps',
        'id'            => 'company_maps',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'rows'          => '3',
      ];
      $this->data['company_phone'] = [
        'name'          => 'company_phone',
        'id'            => 'company_phone',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['company_phone2'] = [
        'name'          => 'company_phone2',
        'id'            => 'company_phone2',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['company_fax'] = [
        'name'          => 'company_fax',
        'id'            => 'company_fax',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
      ];

      $this->load->view('back/company/company_edit_bio', $this->data);
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
      redirect('admin/company');
    }

  }

  function update_bio_action()
  {
    // $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
    $this->form_validation->set_rules('company_name', 'Company Name', 'trim');
    $this->form_validation->set_rules('company_desc', 'Company Description', 'trim|required');
    $this->form_validation->set_rules('company_address', 'Company Address', 'trim|required');

    $this->form_validation->set_message('required', '{field} wajib diisi');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->update($this->input->post('id_company'));
    }
    else
    {
      $password = $this->encryption->encrypt($this->input->post('company_gmail_pass'));

      if($_FILES['photo']['error'] <> 4)
      {
        $nmfile = strtolower(url_title($this->input->post('company_name'))).date('YmdHis');

        $config['upload_path']      = './assets/images/company/';
        $config['allowed_types']    = 'jpg|jpeg|png';
        $config['max_size']         = 2048; // 2Mb
        $config['file_name']        = $nmfile;

        $this->load->library('upload', $config);

        $delete = $this->Company_model->get_by_id($this->input->post('id_company'));

        $dir        = "./assets/images/company/".$delete->company_photo;
        $dir_thumb  = "./assets/images/company/".$delete->company_photo_thumb;

        if(is_file($dir))
        {
          unlink($dir);
          unlink($dir_thumb);
        }

        if(!$this->upload->do_upload('photo'))
        {
          $error = array('error' => $this->upload->display_errors());
          $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$error['error'].'</div>');

          $this->update($this->input->post('id_company'));
        }
        else
        {
          $photo = $this->upload->data();

          $config['image_library']    = 'gd2';
          $config['source_image']     = './assets/images/company/'.$photo['file_name'].'';
          $config['create_thumb']     = TRUE;
          $config['maintain_ratio']   = TRUE;
          $config['width']            = 100;
          $config['height']           = 100;

          $this->load->library('image_lib', $config);
          $this->image_lib->resize();

          $data = array(
            'company_name'          => $this->input->post('company_name'),
            'company_desc'          => $this->input->post('company_desc'),
            'company_address'       => $this->input->post('company_address'),
            'company_maps'          => $this->input->post('company_maps', FALSE),
            'company_phone'         => $this->input->post('company_phone'),
            'company_phone2'        => $this->input->post('company_phone2'),
            'company_fax'           => $this->input->post('company_fax'),
            'company_photo'         => $this->upload->data('file_name'),
            'company_photo_thumb'   => $nmfile.'_thumb'.$this->upload->data('file_ext'),
            'modified_by'           => $this->session->username,
          );

          $this->Company_model->update($this->input->post('id_company'),$data);

          write_log();

          $this->session->set_flashdata('message', '<div class="alert alert-success">Data update succesfully</div>');
          redirect('admin/company/update/1');
        }
      }
      else
      {
        $data = array(
          'company_name'          => $this->input->post('company_name'),
          'company_desc'          => $this->input->post('company_desc'),
          'company_address'       => $this->input->post('company_address'),
          'company_maps'          => $this->input->post('company_maps', FALSE),
          'company_phone'         => $this->input->post('company_phone'),
          'company_phone2'        => $this->input->post('company_phone2'),
          'company_fax'           => $this->input->post('company_fax'),
          'modified_by'           => $this->session->company_name,
        );

        $this->Company_model->update($this->input->post('id_company'),$data);

        write_log();

        $this->session->set_flashdata('message', '<div class="alert alert-success">Data update succesfully</div>');
        redirect('admin/company/update_bio/1');
      }
    }
  }

  function update_webmail($id)
  {
    $this->data['company']     = $this->Company_model->get_by_id($id);

    if($this->data['company'])
    {
      $this->data['page_title'] = 'Update Data Webmail Account';
      $this->data['action']     = 'admin/company/update_webmail_action';

      $this->data['id_company'] = [
        'name'          => 'id_company',
        'type'          => 'hidden',
      ];
      $this->data['company_webmail_name'] = [
        'name'          => 'company_webmail_name',
        'id'            => 'company_webmail_name',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['company_webmail_pass'] = [
        'name'          => 'company_webmail_pass',
        'id'            => 'company_webmail_pass',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'placeholder'   => 'skip this form if you only change webmail_name',
      ];

      $this->load->view('back/company/company_edit_webmail', $this->data);
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">User not found</div>');
      redirect('admin/company');
    }
  }

  function update_webmail_action()
  {
    $this->form_validation->set_rules('company_webmail_name', 'Company Webmail', 'trim|valid_email');

    $this->form_validation->set_message('valid_email', '{field} format email tidak benar');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->update($this->input->post('id_company'));
    }
    else
    {
      $company_webmail_pass = $this->encryption->encrypt($this->input->post('company_webmail_pass'));

      if($this->input->post('company_webmail_pass') == NULL)
      {
        $data = array(
          'company_webmail_name'  => $this->input->post('company_webmail_name'),
          'modified_by'           => $this->session->username,
        );
      }
      else
      {
        $data = array(
          'company_webmail_name'  => $this->input->post('company_webmail_name'),
          'company_webmail_pass'  => $company_webmail_pass,
          'modified_by'           => $this->session->username,
        );
      }

      $this->Company_model->update($this->input->post('id_company'),$data);

      write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data update succesfully</div>');
      redirect('admin/company/update_webmail/1');
    }
  }

  function update_gmail($id)
  {    
    $this->data['company']     = $this->Company_model->get_by_id($id);

    if($this->data['company'])
    {
      $this->data['page_title'] = 'Update Data Gmail Account';
      $this->data['action']     = 'admin/company/update_gmail_action';

      $this->data['id_company'] = [
        'name'          => 'id_company',
        'type'          => 'hidden',
      ];
      $this->data['company_gmail_name'] = [
        'name'          => 'company_gmail_name',
        'id'            => 'company_gmail_name',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
      ];
      $this->data['company_gmail_pass'] = [
        'name'          => 'company_gmail_pass',
        'id'            => 'company_gmail_pass',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'placeholder'   => 'skip this form if you only change gmail_name',
      ];

      $this->load->view('back/company/company_edit_gmail', $this->data);
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">User not found</div>');
      redirect('admin/company/update_webmail/1');
    }
  }

  function update_gmail_action()
  {
    $this->form_validation->set_rules('company_gmail_name', 'Company Gmail', 'trim|required');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->update($this->input->post('id_company'));
    }
    else
    {
      $company_gmail_pass = $this->encryption->encrypt($this->input->post('company_gmail_pass'));

      if($this->input->post('company_gmail_pass') == NULL)
      {
        $data = array(
          'company_gmail_name'    => $this->input->post('company_gmail_name'),
          'modified_by'           => $this->session->username,
        );
      }
      else
      {
        $data = array(
          'company_gmail_name'  => $this->input->post('company_gmail_name'),
          'company_gmail_pass'  => $company_gmail_pass,
          'modified_by'         => $this->session->username,
        );
      }

      $this->Company_model->update($this->input->post('id_company'),$data);

      write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data update succesfully</div>');
      redirect('admin/company/update_gmail/1');
    }
  }

}
