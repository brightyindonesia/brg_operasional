<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shopee extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
    
	    $this->data['module'] = 'API Shopee';

		$this->load->model(array('Shopee_model'));
	    $this->data['company_data']    	= $this->Company_model->company_profile();
		$this->data['layout_template']  = $this->Template_model->layout();
	    $this->data['skins_template']   = $this->Template_model->skins();

	    is_login();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['update_action'] = base_url('admin/shopee/update_data_api');


	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	public function index()
	{
		is_update();

		$this->lib_shopee->check_expire_in();

		$this->data['page_title'] = $this->data['module'].' Data';
		$this->data['shopee'] = $this->Shopee_model->get_by_id(1);

		// DATA API SHOPEE
		$this->data['host'] = [
	      'name'          => 'host',
	      'id'            => 'host',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['redirect'] = [
	      'name'          => 'redirect',
	      'id'            => 'redirect',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['partner_id'] = [
	      'name'          => 'partner_id',
	      'id'            => 'partner-id',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['partner_key'] = [
	      'name'          => 'partner_key',
	      'id'            => 'partner-key',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    // DATA SHOP

	    $this->data['shop'] = [
	      'name'          => 'shop',
	      'readonly'	  => '',
	      'id'            => 'shop',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['merchant'] = [
	      'name'          => 'merchant',
	      'readonly'	  => '',
	      'id'            => 'merchant',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['code'] = [
	      'name'          => 'code',
	      'readonly'	  => '',
	      'id'            => 'code',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    // DATA TOKEN

	    $this->data['access_token'] = [
	      'name'          => 'access_token',
	      'readonly'	  => '',
	      'id'            => 'access-token',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['refresh_token'] = [
	      'name'          => 'refresh_token',
	      'readonly'	  => '',
	      'id'            => 'refresh-token',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['expire_in'] = [
	      'name'          => 'expire_in',
	      'readonly'	  => '',
	      'id'            => 'expire-in',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

		$this->load->view('back/shopee/list', $this->data);
	}

	public function daftarorder()
	{
		// $this->data['order'] = $this->lib_shopee->getOrderList();
		echo print_r($this->lib_shopee->getOrderList());
	}

	public function update_data_api()
	{
		$this->form_validation->set_rules('host', 'Host', 'trim|required');
		$this->form_validation->set_rules('redirect', 'Redirect Auth', 'trim|required');
		$this->form_validation->set_rules('partner_id', 'Partner ID', 'trim|required');
		$this->form_validation->set_rules('partner_key', 'Partner Key', 'trim|required');

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

	    if($this->form_validation->run() === FALSE)
	    {
	      $this->index();
	    }
	    else
	    {
	        $data = array(
	          'host'              => $this->input->post('host'),
	          'redirect_auth'     => $this->input->post('redirect'),
	          'partner_id'        => $this->input->post('partner_id'),
	          'partner_key'       => $this->input->post('partner_key'),
	        );

	        $this->Shopee_model->update(1, $data);

	        write_log();

	        $this->session->set_flashdata('message', '<div class="alert alert-success">Data updated succesfully</div>');
	        redirect('admin/shopee');
	    }
	}

	public function auth()
	{
		$redirect = $this->input->post('redirect');
		$this->lib_shopee->AuthPartner(base_url().$redirect);
	}

	public function openmenu()
	{
		$cek = $this->lib_shopee->check_expire_in();
		if ($cek === FALSE) {
			$this->session->set_flashdata('message', '<div class="alert alert-success">Welcome to Menu API Shopee!</div>');
	        redirect('shopee/dashboard');
		}
	}

	public function accesstoken()
	{
		$this->lib_shopee->getAccessToken();
	}

	public function updateCodeShopID()
	{
		$code 		= $_GET['code'];
		$shop_id 	= $_GET['shop_id'];

		$data = array(
          'code'        			=> $code,
          'shop_id'     			=> $shop_id,
          'timestamp_access_token'	=> NULL,
          'access_token'			=> NULL,
          'refresh_token'			=> NULL,
          'expire_in'				=> NULL,
        );

        $this->Shopee_model->update(1, $data);

	    write_log();
        
        $this->session->set_flashdata('message', '<div class="alert alert-success">Data updated succesfully</div>');
        redirect('admin/shopee');
	}
}

/* End of file Shopee.php */
/* Location: ./application/controllers/admin/Shopee.php */