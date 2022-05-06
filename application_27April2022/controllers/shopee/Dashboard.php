<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		$this->data['module'] = 'API Shopee';
		$this->load->model(array('Shopee_model'));

		is_login();

		$this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    	$this->data['skins_template']     			= $this->Template_model->skins();

    	if(is_admin_cs())
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
	      redirect('admin/resi');
	    }

		$this->lib_shopee->check_expire_in();
	}

	public function index()
	{
		$this->data['page_title'] = 'Dashboard '.$this->data['module'];
		$this->data['shop_info'] = $this->lib_shopee->getShopInfo();
		$this->data['shop_profile'] = $this->lib_shopee->getShopProfle()->response;
		$this->data['channel_list'] = $this->lib_shopee->getChannelList()->response->logistics_channel_list;

		$this->load->view('back/shopee/menu/dashboard/list', $this->data);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/shopee/Dashboard.php */