<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produksi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->data['module'] = 'Produksi';

	    $this->load->model(array('Request_model', 'Vendor_model', 'Sku_model', 'Kategori_po_model', 'Bahan_kemas_model', 'Po_model', 'Penerima_model', 'Produksi_model'));

	    $this->data['company_data']    				= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['add_action'] = base_url('admin/masuk/request_add');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	public function daftar()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';

	    $this->data['get_all_by_produksi'] = $this->Produksi_model->get_all_by_produksi();

	    $this->load->view('back/produksi/produksi_list', $this->data);
	}

	public function produksi_add($id)
	{
		is_create();

		$this->data['produksi'] 			= $this->Produksi_model->get_all_by_id_row(base64_decode($id));
		$this->data['daftar_bahan_kemas']	= $this->Produksi_model->get_detail_by_id(base64_decode($id));
		$this->data['get_all_sku'] 			= $this->Sku_model->get_all_combobox();
	    $this->data['get_all_kategori']		= $this->Produksi_model->get_all_kategori(); 

		if($this->data['produksi']->status_po == 0)
	    {
	    	// echo print_r($this->data['daftar_bahan_kemas']);
	    	$this->data['page_title'] = 'Create Data '.$this->data['module'];
	    	$this->data['nomor_produksi'] = [
		        'name'          => 'nomor_produksi',
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nomor_request'] = [
		        'name'          => 'nomor_request',
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      	 	$this->data['id'] = [	
			  	'id' 			=> 'nomor-produksi', 
		        'type'          => 'hidden',
	     	];	

	     	$this->data['id_po'] = [	
			  	'id' 			=> 'nomor-request', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['id_sku'] = [	
			  	'id' 			=> 'sku', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['qty_produksi'] = [	
			  	'id' 			=> 'qty-produksi', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['sku'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '', 
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['kategori'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id' 			=> 'kategori', 
		    	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];	

		    $this->data['list_po'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'list-po',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['bahan_kemas'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'bahan-kemas',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->load->view('back/produksi/produksi_add', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/produksi/daftar');
	    }
	}

	public function proses_produksi_add()
	{
		// Ambil Data
		$i = $this->input;

		$len = $i->post('length');
		$vendor = $i->post('vendor');
		$penerima = $i->post('penerima');
		$kategori = $i->post('kategori');
		$sku = $i->post('sku');
		$no_request = $i->post('nomor_request');
		$no_request = $i->post('nomor_request');
		$dt_po = $i->post('dt_po');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');
		$dt_harga = $i->post('dt_harga');
		$dt_jumlah = $i->post('dt_jml');
		$no_po	= str_replace("RFQ","PO",$no_request);

		$decode_po = json_decode($dt_po, TRUE);
		$decode_id = json_decode($dt_id, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);
		$decode_harga = json_decode($dt_harga, TRUE);
		$decode_jumlah = json_decode($dt_jumlah, TRUE);
		
		for ($y=0; $y < $len; $y++)
        {
           $diskon 	   		= ($decode_diskon[$y] * 0.01) * $decode_jumlah[$y];
           $pajak 	   		= ($decode_pajak[$y] * 0.01) * $decode_jumlah[$y];
           $total_harga 	= $total_harga + $decode_jumlah[$y];
           $total_qty 		= $total_qty + $decode_qty[$y];
           $total_diskon	= $total_diskon + $diskon;
           $total_pajak		= $total_pajak + $pajak;
        }
	}

	public function get_id_kategori()
	{
		$kategori = $this->input->post('kategori');
		$select_box[] = "<option value=''>- Pilih Nomor Purchase Order -</option>";
		$po = $this->Produksi_model->get_id_kategori($kategori);
		// echo print_r($po);
		if (count($po) > 0) {
			foreach ($po as $row) {
				$select_box[] = '<option value="'.$row->no_po.'">'.$row->no_po.' | '.$row->nama_kategori_po;
			}
			// header("Content-Type:application/json");
			echo json_encode($select_box);
		}else{
			$select_box = '<option value="">Tidak Ada</option>';
			echo json_encode($select_box);
		}
	}

	public function get_id_po()
	{
		$po = $this->input->post('po');
		$select_box[] = "<option value=''>- Pilih Nama Bahan Kemas -</option>";
		$bahan_kemas = $this->Produksi_model->get_id_po($po);
		// echo print_r($po);
		if (count($bahan_kemas) > 0) {
			foreach ($bahan_kemas as $row) {
				$stok = $row->kuantitas_po - $row->selisih_po_produksi;
				$select_box[] = '<option value="'.$row->id_bahan_kemas.'">'.$row->kode_sku_bahan_kemas.' | '.$row->nama_bahan_kemas.' ('.$stok.') | '.$row->keterangan;
			}
			// header("Content-Type:application/json");
			echo json_encode($select_box);
		}else{
			$select_box = '<option value="">Tidak Ada</option>';
			echo json_encode($select_box);
		}
	}

	public function get_id_bahan_kemas()
	{
		$bahan = $this->input->post('bahan');
		// $id_barang = "RPL2003200001";
		$cari_bahan =	$this->Produksi_model->get_id_bahan_kemas($bahan);
		echo json_encode($cari_bahan);
	}

}

/* End of file Produksi.php */
/* Location: ./application/controllers/admin/Produksi.php */