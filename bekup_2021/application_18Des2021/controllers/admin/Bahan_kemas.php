<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Bahan_kemas extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Bahan Produksi';

	    $this->load->model(array('Bahan_kemas_model', 'Vendor_model', 'Venmasaccess_model', 'Produk_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/bahan_kemas/export');
	    $this->data['add_action'] = base_url('admin/bahan_kemas/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_bahan_kemas.xlsx');

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

	    $this->data['get_all'] = $this->Bahan_kemas_model->get_all();

	    $this->load->view('back/bahan_kemas/bahan_kemas_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/bahan_kemas/tambah_proses';
	    $this->data['get_all_venmas_data_access']  = $this->Vendor_model->get_all_combobox();
	    $this->data['get_all_satuan'] = $this->Bahan_kemas_model->get_all_satuan();
	    $this->data['get_all_produk'] = $this->Produk_model->get_all_combobox();

	    $this->data['kode_sku'] = [
	      'name'          => 'kode_sku',
	      'id'            => 'kode-sku',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('kode_sku'),
	    ];

	    $this->data['qty'] = [
	      'name'          => 'qty',
	      'id'            => 'qty',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('qty'),
	    ];

	    $this->data['bahan_kemas_nama'] = [
	      'name'          => 'nama_bahan_kemas',
	      'id'            => 'nama-bahan-kemas',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_bahan_kemas'),
	    ];

	    $this->data['venmas_access_id'] = [
			'name'          => 'venmas_access_id[]',
			'id'            => 'venmas-access-id',
			'class'         => 'form-control select2',
			'multiple'      => '',
		];

		$this->data['satuan'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'satuan',
	     	'autocomplete'  => 'off',
	      	'required'      => '',
	      	'value'         => $this->form_validation->set_value('nama_jenis_toko'),
	    ];

	    $this->data['keterangan'] = [
	      'name'          => 'keterangan',
	      'id'            => 'keterangan',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'value'         => $this->form_validation->set_value('keterangan'),
	    ];

	    $this->load->view('back/bahan_kemas/bahan_kemas_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_bahan_kemas', 'Nama Bahan Kemas', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);
		
		$this->form_validation->set_rules('kode_sku', 'Kode SKU', 'is_unique[bahan_kemas.kode_sku_bahan_kemas]|max_length[50]|trim|required',

			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('kode_sku').'</strong> sudah ada. Buat %s baru',
					'max_length'	=> '%s maksimal 50 karakter' )
		);

		$this->form_validation->set_rules('qty', 'Qty', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);
		
		$this->form_validation->set_rules('venmas_access_id[]', 'Daftar Toko', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('satuan', 'Satuan Bahan Kemas', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

	    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

	    if($this->form_validation->run() === FALSE)
	    {
	      $this->tambah();
	    }
	    else
	    {
	      $data = array(
	        'id_satuan'    			=> $this->input->post('satuan'),
	        'keterangan'   			=> $this->input->post('keterangan'),
	        'kode_sku_bahan_kemas'  => $this->input->post('kode_sku'),
	        'nama_bahan_kemas'  	=> $this->input->post('nama_bahan_kemas'),
	        'qty_bahan_kemas'		=> $this->input->post('qty')
	      );

	      $this->Bahan_kemas_model->insert($data);

	      write_log();

	      $this->db->select_max('id_bahan_kemas');
          $result = $this->db->get('bahan_kemas')->row_array();

          $venmas_access_id = count($this->input->post('venmas_access_id'));

          for($i_venmas_access_id = 0; $i_venmas_access_id < $venmas_access_id; $i_venmas_access_id++)
          {
            $datas_venmas_access_id[$i_venmas_access_id] = array(
              'id_bahan_kemas'   => $result['id_bahan_kemas'],
              'id_vendor'  	=> $this->input->post('venmas_access_id['.$i_venmas_access_id.']'),
            );

            $this->db->insert('venmas_data_access', $datas_venmas_access_id[$i_venmas_access_id]);

            write_log();
          }

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/bahan_kemas');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['bahan_kemas']    						 = $this->Bahan_kemas_model->get_by_id($id);
	    $this->data['get_all_combobox_venmas_data_access']   = $this->Venmasaccess_model->get_all_combobox();
	    $this->data['get_all_venmas_data_access_old']        = $this->Venmasaccess_model->get_all_data_access_old($id);
	    $this->data['get_all_satuan'] 						 = $this->Bahan_kemas_model->get_all_satuan();
	    $this->data['get_all_produk']						 = $this->Produk_model->get_all_combobox();

	    if($this->data['bahan_kemas'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/bahan_kemas/ubah_proses';

	      $this->data['id_bahan_kemas'] = [
	        'name'          => 'id_bahan_kemas',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['kode_sku'] = [
		      'name'          => 'kode_sku',
		      'id'            => 'kode-sku',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		    $this->data['qty'] = [
		      'name'          => 'qty',
		      'id'            => 'qty',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['bahan_kemas_nama'] = [
		      'name'          => 'nama_bahan_kemas',
		      'id'            => 'nama-bahan-kemas',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['venmas_access_id'] = [
	        'name'          => 'venmas_access_id[]',
	        'id'            => 'venmas-access-id',
	        'class'         => 'form-control select2',
	        'multiple'      => '',
	        'width' 		=> '100%' 
	      ];

	      $this->data['satuan'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'satuan',
	     	'autocomplete'  => 'off',
	      	'required'      => '',
	    ];

	    $this->data['produk'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'produk',
	     	'autocomplete'  => 'off',
	      	'required'      => '',
	    ];

	    $this->data['keterangan'] = [
	      'name'          => 'keterangan',
	      'id'            => 'keterangan',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	    ];


	      $this->load->view('back/bahan_kemas/bahan_kemas_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/bahan_kemas');
	    }
	}

	function ubah_proses()
	{
		$cek_bahan_kemas = $this->Bahan_kemas_model->get_by_id($this->input->post('id_bahan_kemas'));

		$this->form_validation->set_rules('nama_bahan_kemas', 'Nama Bahan Kemas', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);
		
		if ($cek_bahan_kemas->kode_sku_bahan_kemas == $this->input->post('kode_sku')) {
			$this->form_validation->set_rules('kode_sku', 'Kode SKU', 'max_length[50]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'max_length' 	=> '%s harus 50 karakter!'
				)
			);
		}else{
			$this->form_validation->set_rules('kode_sku', 'Kode SKU', 'is_unique[bahan_kemas.kode_sku_bahan_kemas]|max_length[50]|trim|required',

			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('kode_sku').'</strong> sudah ada. Buat %s baru',
					'max_length'	=> '%s maksimal 50 karakter' )
			);
		}
		
		
		$this->form_validation->set_rules('qty', 'Qty', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_bahan_kemas'));
		}
		else
		{
		  $data = array(
		  	'id_satuan'    			=> $this->input->post('satuan'),
	        'keterangan'   			=> $this->input->post('keterangan'),
		    'kode_sku_bahan_kemas'  => $this->input->post('kode_sku'),
	        'nama_bahan_kemas'  	=> $this->input->post('nama_bahan_kemas'),
	        'qty_bahan_kemas'		=> $this->input->post('qty')
		  );

		  $this->Bahan_kemas_model->update($this->input->post('id_bahan_kemas'),$data);

		  write_log();

		  if(!empty($this->input->post('venmas_access_id')))
          {
            $this->db->where('id_bahan_kemas', $this->input->post('id_bahan_kemas'));
            $this->db->delete('venmas_data_access');

            $venmas_access_id = count($this->input->post('venmas_access_id'));

            for($i_venmas_access_id = 0; $i_venmas_access_id < $venmas_access_id; $i_venmas_access_id++)
	        {
	            $datas_venmas_access_id[$i_venmas_access_id] = array(
	              'id_bahan_kemas'   => $this->input->post('id_bahan_kemas'),
	              'id_vendor'  		=> $this->input->post('venmas_access_id['.$i_venmas_access_id.']'),
	            );

	            $this->db->insert('venmas_data_access', $datas_venmas_access_id[$i_venmas_access_id]);

	            write_log();
	        }
          }

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/bahan_kemas');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Bahan_kemas_model->get_by_id($id);

		if($delete)
		{
		  $this->db->where('id_bahan_kemas', $id);
          $this->db->delete('venmas_data_access');
		  
		  $this->Bahan_kemas_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/bahan_kemas');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/bahan_kemas');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$produk = $this->input->post('ids');
		// echo $produk;

		$this->Bahan_kemas_model->delete_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	function export() {
		$data['title']	= "Export Data Bahan Kemas_".date("Y_m_d");
		$data['bahan_kemas']	= $this->Bahan_kemas_model->get_all();

		$this->load->view('back/bahan_kemas/bahan_kemas_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/bahan_kemas/proses_import';

	    $this->load->view('back/bahan_kemas/bahan_kemas_import', $this->data);
	}

	public function proses_import()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc'.time();	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('import')) {
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/'.$file['file_name']);
			$numSheet 	= 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 0) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow > 1) {
							$data 	= array(	'id_bahan_kemas'		=> $row->getCellAtIndex(0),
												'id_satuan'				=> $row->getCellAtIndex(2),
												'kode_sku_bahan_kemas'	=> $row->getCellAtIndex(3),
												'nama_bahan_kemas'		=> $row->getCellAtIndex(4),
												'qty_bahan_kemas'		=> $row->getCellAtIndex(5),
												'keterangan'			=> $row->getCellAtIndex(6),
							);

							$this->Bahan_kemas_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/bahan_kemas');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}
}

/* End of file Bahan_kemas.php */
/* Location: ./application/controllers/admin/Bahan_kemas.php */