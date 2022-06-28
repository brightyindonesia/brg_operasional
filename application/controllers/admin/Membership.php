<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Membership extends CI_Controller{

  public function __construct()
  {
    parent::__construct();

    $this->data['module'] = 'Membership';

    $this->load->model(array('Membership_model'));

    $this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    $this->data['skins_template']     			= $this->Template_model->skins();

    $this->data['btn_submit'] = 'Save';
    $this->data['btn_reset']  = 'Reset';
    $this->data['btn_add']    = 'Add New Data';
    $this->data['add_action'] = base_url('admin/membership/create');

    is_login();

    if(!is_superadmin())
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
      redirect('admin/dashboard');
    }
  }

  function index()
  {
    is_read();

    $this->data['page_title'] = $this->data['module'].' List';

    $this->data['get_all'] = $this->Membership_model->get_all();

    $this->load->view('back/membership/membership_list', $this->data);
  }

  function create()
  {
    is_create();

    $this->data['page_title'] = 'Create New '.$this->data['module'];
    $this->data['action']     = 'admin/membership/create_action';

    $this->data['tier'] = [
      'name'          => 'tier',
      'id'            => 'tier',
      'class'         => 'form-control',
      'autocomplete'  => 'off',
      'required'      => '',
      'value'         => $this->form_validation->set_value('tier'),
    ];
    $this->data['x_poin'] = [
        'name'          => 'x_poin',
        'id'            => 'x_poin',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
        'type'          => 'number',
        'value'         => $this->form_validation->set_value('x_poin'),
      ];
      $this->data['min_belanja'] = [
        'name'          => 'min_belanja',
        'id'            => 'min_belanja',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
        'type'          => 'number',
        'value'         => $this->form_validation->set_value('min_belanja'),
      ];
      $this->data['max_belanja'] = [
        'name'          => 'max_belanja',
        'id'            => 'max_belanja',
        'class'         => 'form-control',
        'autocomplete'  => 'off',
        'required'      => '',
        'type'          => 'number',
        'value'         => $this->form_validation->set_value('max_belanja'),
      ];

    $this->load->view('back/membership/membership_add', $this->data);
  }

  function create_action()
  {
    $this->form_validation->set_rules('tier', 'Tier Name', 'trim|required');
    $this->form_validation->set_rules('x_poin', 'x Poin', 'trim|required');
    $this->form_validation->set_rules('min_belanja', 'Min Belanja', 'trim|required');
    $this->form_validation->set_rules('max_belanja', 'Max Belanja', 'trim|required');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->create();
    }
    else
    {
      $data = array(
        'tier'     => $this->input->post('tier'),
        'x_poin' => $this->input->post('x_poin'),
        'min_belanja' => $this->input->post('min_belanja'),
        'max_belanja' => $this->input->post('max_belanja'),
      );

      $this->Membership_model->insert($data);

			write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
      redirect('admin/membership');
    }
  }

  function update($id)
  {
    is_update();

    $this->data['membership']     = $this->Membership_model->get_by_id($id);

    if($this->data['membership'])
    {
      $this->data['page_title'] = 'Update Data '.$this->data['module'];
      $this->data['action']     = 'admin/membership/update_action';

      $this->data['id_membership'] = [
        'name'          => 'id_membership',
        'type'          => 'hidden',
      ];
			$this->data['tier'] = [
	      'name'          => 'tier',
	      'id'            => 'tier',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];
        $this->data['x_poin'] = [
	      'name'          => 'x_poin',
	      'id'            => 'x_poin',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];
        $this->data['min_belanja'] = [
	      'name'          => 'min_belanja',
	      'id'            => 'min_belanja',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];
        $this->data['max_belanja'] = [
	      'name'          => 'max_belanja',
	      'id'            => 'max_belanja',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

      $this->load->view('back/membership/membership_edit', $this->data);
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
      redirect('admin/membership');
    }

  }

  function update_action()
  {
    $this->form_validation->set_rules('tier', 'Tier Name', 'trim|required');
    $this->form_validation->set_rules('x_poin', 'x Poin', 'trim|required');
    $this->form_validation->set_rules('min_belanja', 'Min Belanja', 'trim|required');
    $this->form_validation->set_rules('max_belanja', 'Max Belanja', 'trim|required');

    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

    if($this->form_validation->run() === FALSE)
    {
      $this->update($this->input->post('id_membership'));
    }
    else
    {
			$data = array(
        'tier'     => $this->input->post('tier'),
        'x_poin' => $this->input->post('x_poin'),
        'min_belanja' => $this->input->post('min_belanja'),
        'max_belanja' => $this->input->post('max_belanja'),
      );

      $this->Membership_model->update($this->input->post('id_membership'),$data);

			write_log();

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
      redirect('admin/membership');
    }
  }

  function delete($id)
  {
    is_delete();

    $delete = $this->Membership_model->get_by_id($id);

    if($delete)
    {
      $this->Membership_model->delete($id);

      $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
      redirect('admin/membership');
    }
    else
    {
      $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
      redirect('admin/membership');
    }
  }

  public function listing_membership()
  {
    is_read();    

	    $this->data['page_title'] = 'Listing Membership';
      $this->data['get_all_tier'] = $this->Membership_model->get_all_combobox();
      $this->data['tier'] = [
        'name'          => 'tier',
        'id'            => 'tier',
        'class'         => 'form-control',
        'required'      => '',
      ];

	    $this->load->view('back/membership/membership_listing', $this->data);
	
  }

  public function get_data_membership_listing() {
    $tier = $this->input->get('tier');
    
    echo json_encode($this->Membership_model->get_datatable_membership_insight($tier));
  }

  public function get_count_membership()
  {
    $tahun = $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');
    $tiers = [];
    foreach ($this->data['get_all'] = $this->Membership_model->get_all() as $key => $tier) {
      $tiers[$key]['name'] = $tier->tier;
      $tiers[$key]['y'] = $this->Membership_model->get_count_membership_by_id($tahun, $tier->id_membership);
    }
    echo json_encode($tiers);
  }

  public function get_insight_membership()
  {
    $tahun = $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');
    $tiers = [];
    foreach ($this->data['get_all'] = $this->Membership_model->get_all() as $key => $tier) {
      $tiers[$key]['name'] = $tier->tier;
      $tiers[$key]['data'] = $this->Membership_model->get_data_every_month_by_year($tahun,$tier->id_membership);
    }
    echo json_encode($tiers);
  }

  public function insight_membership()
  { 
    $this->data['page_title'] = 'Membership Insight';
    $this->data['tahun'] = [
      'name'          => 'tahun',
      'id'            => 'tahun',
      'class'         => 'form-control',
      'required'      => '',
    ];
    $this->load->view('back/membership/membership_insight', $this->data);
  }

}
