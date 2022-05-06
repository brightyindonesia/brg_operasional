<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Changelog extends CI_Controller {

  function __construct()
  {
    parent::__construct();

    $this->data['module'] = 'Changelog';

    $this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    $this->data['skins_template']     			= $this->Template_model->skins();

    $this->data['btn_submit'] = 'Save';
    $this->data['btn_reset']  = 'Reset';
    $this->data['btn_add']    = 'Add New Data';

    is_login();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }
  }

  function get_data_systemlog()
  {
      $list = $this->Changelog_model->get_datatables();
      $dataJSON = array();
      $i = 1;
      foreach ($list as $data) {
      // Master
          $row = array();
          $row['no'] = $i;
          $row['tanggal'] = datetime_indo($data->created_at);
          $row['content'] = $data->content;
          $row['created_by'] = $data->created_by;
          $row['ip_address'] = $data->ip_address;
          $row['user_agent'] = $data->user_agent;

          $dataJSON[] = $row;
          $i++;
      }

      $output = array(
          "recordsTotal" => $this->Changelog_model->count_all(),
          "recordsFiltered" => $this->Changelog_model->count_filtered(),
          "data" => $dataJSON,
      );
      //output dalam format JSON
      echo json_encode($output);
  }
  // End Datatable Server Side

  function systemlog()
  {
    $this->data['page_title'] = 'System Log';

    $this->data['get_all']            = $this->Changelog_model->get_all_log_query();
      $this->data['get_all_users']   = $this->Changelog_model->get_all_users_list();


    $this->data['users'] = [
        'class'         => 'form-control',
        'id'            => 'users',
        'required'      => '',
        'style'         => 'width:100%'
    ];

    $this->load->view('back/changelog/systemlog_list', $this->data);
  }

  function systemlog_delete()
  {
    $this->Changelog_model->delete_log_query();

    $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
    redirect('admin/changelog/systemlog');
  }

  function applog()
  {
    $this->data['page_title'] = 'App Log';

    $this->data['get_all']    = $this->Changelog_model->get_all_log_app();

    $this->load->view('back/changelog/applog_list', $this->data);
  }

  function applog_create()
  {
    is_create();

    $this->data['page_title'] = 'Create New '.$this->data['module'];
    $this->data['action']     = 'admin/changelog/applog_create_action';

    $this->data['changelog_date'] = [
      'name'          => 'changelog_date',
      'id'            => 'changelog_date',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('changelog_date'),
    ];
    $this->data['changelog_name'] = [
      'name'          => 'changelog_name',
      'id'            => 'changelog_name',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('changelog_name'),
    ];
    $this->data['changelog_description'] = [
      'name'          => 'changelog_description',
      'id'            => 'changelog_description',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'value'         => $this->form_validation->set_value('changelog_description'),
    ];
    $this->data['changelog_option'] = [
      'CREATE'        => 'CREATE',
      'UPDATE'        => 'UPDATE',
      'DELETE'        => 'DELETE',
    ];
    $this->data['changelog_type'] = [
      'name'          => 'changelog_type',
      'id'            => 'changelog_type',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('changelog_type'),
    ];

    $this->load->view('back/changelog/applog_create', $this->data);

  }

  function applog_create_action()
  {
    $this->form_validation->set_rules('changelog_date', 'Changelog Date', 'trim|required');
    $this->form_validation->set_rules('changelog_type', 'Changelog Type', 'trim|required');
    $this->form_validation->set_rules('changelog_name', 'Changelog Name', 'trim|required');
    $this->form_validation->set_rules('changelog_description', 'Changelog Description', 'trim|required');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->applog_create();
    }
    else
    {
      $data = array(
        'changelog_date'            => $this->input->post('changelog_date'),
        'changelog_name'            => $this->input->post('changelog_name'),
        'changelog_description'     => $this->input->post('changelog_description', FALSE),
        'changelog_type'            => $this->input->post('changelog_type'),
        'created_by'                => $this->session->username,
      );

      $this->Changelog_model->insert_applog($data);

			write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
      redirect('admin/changelog/applog');
    }
  }

}

/* End of file Changelog.php */
