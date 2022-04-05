<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testing extends CI_Controller{

  public function __construct()
  {
    parent::__construct();

    $this->data['module'] = 'Testing';

    $this->load->model(array('Testing_model'));

    $this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    $this->data['skins_template']     			= $this->Template_model->skins();

    $this->data['btn_submit'] = 'Save';
    $this->data['btn_reset']  = 'Reset';
    $this->data['btn_add']    = 'Add New Data';
    $this->data['add_action'] = base_url('admin/testing/create');

    is_login();

    if($this->uri->segment(1) != NULL){
      menuaccess_check();
    }
    elseif($this->uri->segment(2) != NULL){
      submenuaccess_check();
    }
  }

  function index()
  {
    is_read();

    $this->data['page_title'] = $this->data['module'].' List';

    $this->data['get_all'] = $this->Testing_model->get_all();

    $this->load->view('back/testing/testing_list', $this->data);
  }

  function create()
  {
    is_create();

    $this->data['page_title'] = 'Create New '.$this->data['module'];
    $this->data['action']     = 'admin/testing/create_action';

    $this->data['testing_name'] = [
      'name'          => 'testing_name',
      'id'            => 'testing_name',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('testing_name'),
    ];

    $this->load->view('back/testing/testing_add', $this->data);

  }

  function create_action()
  {
    $this->form_validation->set_rules('testing_name', 'Testing Name', 'trim|required');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->create();
    }
    else
    {
      $data = array(
        'testing_name'     => $this->input->post('testing_name'),
      );

      $this->Testing_model->insert($data);

			write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
      redirect('admin/testing/index');
    }
  }

  function update($id = '')
  {
    is_update();

    $this->data['testing']     = $this->Testing_model->get_by_id($id);

    if($this->data['testing'])
    {
      $this->data['page_title'] = 'Update Data '.$this->data['module'];
      $this->data['action']     = 'admin/testing/update_action';

      $this->data['id_testing'] = [
        'name'          => 'id_testing',
        'type'          => 'hidden',
      ];
			$this->data['testing_name'] = [
	      'name'          => 'testing_name',
	      'id'            => 'testing_name',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

      $this->load->view('back/testing/testing_edit', $this->data);
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
      redirect('admin/testing/index');
    }

  }

  function update_action()
  {
    $this->form_validation->set_rules('testing_name', 'Testing Name', 'trim|required');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->update($this->input->post('id_testing'));
    }
    else
    {
      $data = array(
        'testing_name'     => $this->input->post('testing_name'),
      );

      $this->Testing_model->update($this->input->post('id_testing'),$data);

			write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
      redirect('admin/testing/index');
    }
  }

  function delete($id = '')
  {
    is_delete();

    $delete = $this->Testing_model->get_by_id($id);

    if($delete)
    {
      $this->Testing_model->delete($id);

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
      redirect('admin/testing/index');
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
      redirect('admin/testing/index');
    }
  }

}
