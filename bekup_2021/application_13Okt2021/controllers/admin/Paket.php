<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paket extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Paket';

	    $this->load->model(array('Paket_model', 'Produk_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/paket/export');
	    $this->data['add_action'] = base_url('admin/paket/tambah');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	public function index()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';

	    $this->data['get_all'] = $this->Paket_model->get_all();

	    $this->load->view('back/paket/paket_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] 		= 'Create New '.$this->data['module'];
	    $this->data['get_all_produk']	= $this->Paket_model->get_all_combobox_produk();
	    // $this->data['action']     = 'admin/paket/tambah_proses';

	    $this->data['paket_nama'] = [
	      'name'          => 'nama_paket',
	      'id'            => 'nama-paket',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_paket'),
	    ];

	    $this->data['produk'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'produk',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/paket/paket_add', $this->data);
	}

	public function tambah_proses()
	{
		// Ambil Data
		$i = $this->input;

		$len = $i->post('length');
		$nama = $i->post('nama');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');

		$decode_id = json_decode($dt_id, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);

    	$data = array(	'nama_paket'		=> $nama,
		  			);

		$this->Paket_model->insert($data);

		write_log();

		$this->db->select_max('id_paket');
	    $result = $this->db->get('paket')->row();

	    // echo print_r($result);
		for ($n=0; $n < $len; $n++)
        {
          	$dataPakduk[$n] = array(	'id_produk' 	=> $decode_id[$n],
										'qty_pakduk'	=> $decode_qty[$n],
										'id_paket' 		=> $result->id_paket 
							);

          	// $cariProduk[$n] = $this->Produk_model->get_by_id($decode_id[$n]);
          	// $kurangStokProduk[$n] = array(	'qty_produk' 		=> $cariProduk[$n]->qty_produk - $decode_qty[$n]
					      //     	);

          	// $this->Produk_model->update($decode_id[$n], $kurangStokProduk[$n]);
			$this->db->insert('pakduk_data_access',$dataPakduk[$n]);

			write_log();
        }

        $pesan = "Berhasil disimpan!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['paket']   			= $this->Paket_model->get_by_id($id);
	    $this->data['get_all_produk']	= $this->Paket_model->get_all_combobox_produk();
	    $this->data['get_all_pakduk']	= $this->Paket_model->get_all_produk_by_paket($this->data['paket']->id_paket);

	    // echo print_r($this->data['produk']);
	    if($this->data['paket'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];

		  $this->data['id_paket'] = [	
		  	'id' 			=> 'id-paket', 
	        'type'          => 'hidden',
	      ];	      
		  
		$this->data['paket_nama'] = [
	      'name'          => 'nama_paket',
	      'id'            => 'nama-paket',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['produk'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'produk',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/paket/paket_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/paket');
	    }
	}

	function ubah_proses()
	{
		// Ambil Data
		$i = $this->input;

		$id = $i->post('id');
		$len = $i->post('length');
		$nama = $i->post('nama');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');

		$decode_id = json_decode($dt_id, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);

    	$data = array(	'nama_paket'		=> $nama,
		  			);

		$this->Paket_model->update($id,$data);

		write_log();

		$this->db->where('id_paket', $id);
        $this->db->delete('pakduk_data_access');

	    // echo print_r($result);
		for ($n=0; $n < $len; $n++)
        {
          	$dataPakduk[$n] = array(	'id_produk' 	=> $decode_id[$n],
										'qty_pakduk'	=> $decode_qty[$n],
										'id_paket' 		=> $id
							);

          	// $cariProduk[$n] = $this->Produk_model->get_by_id($decode_id[$n]);
          	// $kurangStokProduk[$n] = array(	'qty_produk' 		=> $cariProduk[$n]->qty_produk - $decode_qty[$n]
					      //     	);

          	// $this->Produk_model->update($decode_id[$n], $kurangStokProduk[$n]);
			$this->db->insert('pakduk_data_access',$dataPakduk[$n]);

			write_log();
        }

        $pesan = "Berhasil diubah!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Paket_model->get_by_id($id);

		if($delete)
		{
		  $this->db->where('id_paket', $id);
          $this->db->delete('pakduk_data_access');

          write_log();

		  $this->Paket_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/paket');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/paket');
		}
	}

	public function get_id_produk()
	{
		$produk = $this->input->post('produk');
		// $id_barang = "RPL2003200001";
		$cari_produk =	$this->Paket_model->get_id_produk($produk);
		echo json_encode($cari_produk);
	}

	function export() {
		$data['title']	= "Export Data Paket".date("Y_m_d");
		$data['paket']	= $this->Paket_model->get_all();

		$this->load->view('back/paket/paket_export', $data);
	}
}

/* End of file Paket.php */
/* Location: ./application/controllers/admin/Paket.php */