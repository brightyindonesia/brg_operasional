<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Percobaan extends CI_Controller{

  public function __construct()
  {
    parent::__construct();

    $this->data['module'] = 'Percobaan';    

    $this->load->model(array('Percobaan_model'));

    $this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    $this->data['skins_template']     			= $this->Template_model->skins();

    $this->data['btn_submit'] = 'Save';
    $this->data['btn_reset']  = 'Reset';
    $this->data['btn_add']    = 'Add New Data';
    $this->data['add_action'] = base_url('admin/percobaan/create');

    is_login();

    if($this->uri->segment(2) != NULL){
      menuaccess_check();
    }
    elseif($this->uri->segment(3) != NULL){
      submenuaccess_check();
    }
  }

  function index()
  {
    is_read();    

    $this->data['page_title'] = $this->data['module'].' List';

    $this->data['get_all'] = $this->Percobaan_model->get_all();

    $this->load->view('back/percobaan/percobaan_list', $this->data);
  }

  function create()
  {
    is_create();    

    $this->data['page_title'] = 'Create New '.$this->data['module'];
    $this->data['action']     = 'admin/percobaan/create_action';

    $this->data['percobaan_name'] = [
      'name'          => 'percobaan_name',
      'id'            => 'percobaan_name',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('percobaan_name'),
    ];

    $this->load->view('back/percobaan/percobaan_add', $this->data);

  }

  function create_action()
  {
    $this->form_validation->set_rules('percobaan_name', 'Percobaan Name', 'trim|required');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->create();
    }
    else
    {
      $data = array(
        'percobaan_name'     => $this->input->post('percobaan_name'),
      );

      $this->Percobaan_model->insert($data);

      write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
      redirect('admin/percobaan/index');
    }
  }

  function update($id = '')
  {
    is_update();

    $this->data['percobaan']     = $this->Percobaan_model->get_by_id($id);

    if($this->data['percobaan'])
    {
      $this->data['page_title'] = 'Update Data '.$this->data['module'];
      $this->data['action']     = 'admin/percobaan/update_action';

      $this->data['id_percobaan'] = [
        'name'          => 'id_percobaan',
        'type'          => 'hidden',
      ];
			$this->data['percobaan_name'] = [
	      'name'          => 'percobaan_name',
	      'id'            => 'percobaan_name',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

      $this->load->view('back/percobaan/percobaan_edit', $this->data);
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
      redirect('admin/percobaan/index');
    }

  }

  function update_action()
  {
    $this->form_validation->set_rules('percobaan_name', 'Percobaan Name', 'trim|required');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->update($this->input->post('id_percobaan'));
    }
    else
    {
      $data = array(
        'percobaan_name'     => $this->input->post('percobaan_name'),
      );

      $this->Percobaan_model->update($this->input->post('id_percobaan'),$data);

			write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
      redirect('admin/percobaan/index');
    }
  }

  function delete($id = '')
  {
    is_delete();

    $delete = $this->Percobaan_model->get_by_id($id);

    if($delete)
    {
      $this->Percobaan_model->delete($id);

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
      redirect('admin/percobaan/index');
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
      redirect('admin/percobaan/index');
    }
  }

}
