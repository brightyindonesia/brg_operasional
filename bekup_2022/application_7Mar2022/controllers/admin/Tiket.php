<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tiket extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Tiket';

	    $this->load->model(array('Tiket_model'));

	    $this->data['company_data']    				= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    // $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['add_action'] = base_url('admin/retur/retur_produk');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Scan Resi atau Nomor Pesanan: '.$this->data['module'];
	    // $this->data['get_cek'] = $this->Resi_model->get_data_cek();
	    // $this->data['action']     = 'admin/resi/tambah_proses';

	    $this->data['nomor'] = [
	      'name'          => 'nomor',
	      'id'            => 'nomor',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'onchange'	  => 'cekNomor()',
	    ];

	    $this->load->view('back/tiket/tiket_scan', $this->data);
	}

	public function scan_proses()
	{
		$nomor 			= $this->input->post('nomor');
		$cek_nomor		= $this->Tiket_model->get_cek_resi_all_by_nomor_pesanan_resi($nomor);
		if (isset($cek_nomor)) {
			$pesan = "No. Resi atau No. Pesanan ditemukan!";	
        	$msg = array(	'sukses'			=> $pesan
        			);
        	echo json_encode($msg); 
		}else{
			$pesan = "No Resi atau No. Pesanan tidak ditemukan!";	
        	$msg = array(	'validasi'	=> $pesan
        			);
        	echo json_encode($msg); 
		}
	}

}

/* End of file Tiket.php */
/* Location: ./application/controllers/admin/Tiket.php */