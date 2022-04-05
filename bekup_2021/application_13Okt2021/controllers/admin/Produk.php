<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Produk extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Produk';

	    $this->load->model(array('Produk_model', 'Toko_model', 'Tokproaccess_model', 'Paket_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/produk/export');
	    $this->data['add_action'] = base_url('admin/produk/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_produk.xlsx');

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

	    $this->data['get_all'] = $this->Produk_model->get_all();

	    $this->load->view('back/produk/produk_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/produk/tambah_proses';
	    $this->data['get_all_tokpro_data_access']  = $this->Toko_model->get_all_combobox();
	    $this->data['get_all_satuan'] = $this->Produk_model->get_all_satuan();
	    $this->data['get_all_sku'] = $this->Produk_model->get_all_sku();
	    $this->data['get_all_paket']  = $this->Paket_model->get_all_combobox();

	    $this->data['sku'] = [
	      'id'            => 'kode-sku',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('sku'),
	    ];

	    $this->data['sub_sku'] = [
	      'name'          => 'sub_sku',
	      'id'            => 'sub-sku',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('sub_sku'),
	    ];

	    $this->data['qty'] = [
	      'name'          => 'qty',
	      'id'            => 'qty',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('qty'),
	    ];

	    $this->data['hpp'] = [
	      'name'          => 'hpp',
	      'id'            => 'hpp',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('hpp'),
	    ];

	    $this->data['produk_nama'] = [
	      'name'          => 'nama_produk',
	      'id'            => 'nama-produk',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_produk'),
	    ];

	    $this->data['tokpro_access_id'] = [
			'name'          => 'tokpro_access_id[]',
			'id'            => 'tokpro-access-id',
			'class'         => 'form-control select2',
			'multiple'      => '',
		];

		$this->data['satuan'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'satuan',
	     	'autocomplete'  => 'off',
	      	'required'      => '',
	      	'value'         => $this->form_validation->set_value('satuan'),
	    ];

	    $this->data['paket'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'paket',
	     	'autocomplete'  => 'off',
	      	'value'         => $this->form_validation->set_value('paket'),
	    ];

	    $this->load->view('back/produk/produk_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_produk', 'Nama Produk', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);
		
		$this->form_validation->set_rules('sku', 'Kode SKU', 'required',
			array(	'required' 		=> '%s harus diisi!' )
		);

		$this->form_validation->set_rules('sub_sku', 'Sub SKU', 'required|trim|is_unique[produk.sub_sku]|max_length[50]',
			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('sub_sku').'</strong> sudah ada. Buat %s baru',
					'max_length'	=> '%s maksimal 50 karakter'
			)
		);

		$this->form_validation->set_rules('qty', 'Qty', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('hpp', 'HPP', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);
		
		$this->form_validation->set_rules('tokpro_access_id[]', 'Daftar Toko', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('satuan', 'Satuan Produk', 'required',
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
	        'id_satuan'    => $this->input->post('satuan'),
	        'id_sku'       => $this->input->post('sku'),
	        'sub_sku' 	   => $this->input->post('sub_sku'),
	        'nama_produk'  => $this->input->post('nama_produk'),
	        'qty_produk'   => $this->input->post('qty'),
	        'hpp_produk'   => $this->input->post('hpp')
	      );

	      $this->Produk_model->insert($data);

	      write_log();

	      $this->db->select_max('id_produk');
          $result = $this->db->get('produk')->row_array();
	      
	      if ($this->input->post('paket') != '') {
	      	$dataPaket	= array( 'id_produk'	=> $result['id_produk'],
	      						 'id_paket' 	=> $this->input->post('paket')
	      	);

	      	$this->db->insert('propak_data_access', $dataPaket);
	      	write_log();
	      }

          $tokpro_access_id = count($this->input->post('tokpro_access_id'));

          for($i_tokpro_access_id = 0; $i_tokpro_access_id < $tokpro_access_id; $i_tokpro_access_id++)
          {
            $datas_tokpro_access_id[$i_tokpro_access_id] = array(
              'id_produk'   => $result['id_produk'],
              'id_toko'  	=> $this->input->post('tokpro_access_id['.$i_tokpro_access_id.']'),
            );

            $this->db->insert('tokpro_data_access', $datas_tokpro_access_id[$i_tokpro_access_id]);

            write_log();
          }

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/produk');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['produk']     	= $this->Produk_model->get_by_id($id);
	    $this->data['akses_produk']	= $this->Produk_model->get_all_by_id($id);
	    $this->data['get_all_combobox_tokpro_data_access']   = $this->Tokproaccess_model->get_all_combobox();
	    $this->data['get_all_tokpro_data_access_old']        = $this->Tokproaccess_model->get_all_data_access_old($id);
	    $this->data['get_all_satuan'] = $this->Produk_model->get_all_satuan();
	    $this->data['get_all_sku'] = $this->Produk_model->get_all_sku();
	    $this->data['get_all_paket']  = $this->Paket_model->get_all_combobox();

	    if($this->data['produk'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/produk/ubah_proses';

	      $this->data['id_produk'] = [
	        'name'          => 'id_produk',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['kode_sku'] = [
		      'name'          => 'kode_sku',
		      'id'            => 'kode-sku',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['sub_sku'] = [
		      'name'          => 'sub_sku',
		      'id'            => 'sub-sku',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['paket'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'paket',
	     	'autocomplete'  => 'off'
	      ];

		    $this->data['qty'] = [
		      'name'          => 'qty',
		      'id'            => 'qty',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['produk_nama'] = [
		      'name'          => 'nama_produk',
		      'id'            => 'nama-produk',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['tokpro_access_id'] = [
	        'name'          => 'tokpro_access_id[]',
	        'id'            => 'tokpro-access-id',
	        'class'         => 'form-control select2',
	        'multiple'      => '',
	      ];

	      $this->data['satuan'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'satuan',
	     	'autocomplete'  => 'off',
	      	'required'      => '',
	      ];

	      $this->data['sku'] = [
			      'id'            => 'kode-sku',
			      'class'         => 'form-control',
			      'autocomplete'  => 'off',
			      'required'      => ''
		   ];
		  
		  $this->data['hpp'] = [
			      'name'          => 'hpp',
			      'id'            => 'hpp',
			      'class'         => 'form-control',
			      'autocomplete'  => 'off',
			      'required'      => ''
		  ];



	      $this->load->view('back/produk/produk_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/produk');
	    }
	}

	function ubah_proses()
	{
		$cek_produk = $this->Produk_model->get_by_id($this->input->post('id_produk'));
		$cek_paket = $this->Produk_model->get_all_by_id($this->input->post('id_produk'));

		if ($cek_produk->sub_sku == $this->input->post('sub_sku')) {
			$this->form_validation->set_rules('sub_sku', 'Sub SKU', 'trim|required|max_length[50]',
				array(	'required' 		=> '%s harus diisi!',
						'max_length'	=> '%s maksimal 50 karakter'
				)
			);
		}else{
			$this->form_validation->set_rules('sub_sku', 'Sub SKU', 'required|trim|is_unique[produk.sub_sku]|max_length[50]',
				array(	'required' 		=> '%s harus diisi!',
						'is_unique'		=> '<strong>'.$this->input->post('sub_sku').'</strong> sudah ada. Buat %s baru',
						'max_length'	=> '%s maksimal 50 karakter'
				)
			);
		}

		if (isset($cek_paket)) {
			if ($cek_paket->id_paket != $this->input->post('paket')) {
				if ($this->input->post('paket') == '') {
					$this->db->where('id_paket', $cek_paket->id_paket);
			        $this->db->delete('propak_data_access');

			        write_log();
				}else{
					$this->db->where('id_paket', $cek_paket->id_paket);
			        $this->db->delete('propak_data_access');

			        write_log();

			        $dataPaket	= array( 'id_produk'	=> $this->input->post('id_produk'),
			      						 'id_paket' 	=> $this->input->post('paket')
			      	);

			      	$this->db->insert('propak_data_access', $dataPaket);
			      	write_log();
				}	
			}
		}else{
			$dataPaket	= array( 'id_produk'	=> $this->input->post('id_produk'),
		      					 'id_paket' 	=> $this->input->post('paket')
		      	);

		      	$this->db->insert('propak_data_access', $dataPaket);
		      	write_log();
		}

		$this->form_validation->set_rules('nama_produk', 'Nama Produk', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('sku', 'Kode SKU', 'trim|required',
			array(	'required' 		=> '%s harus diisi!',
			)
		);
		
		$this->form_validation->set_rules('qty', 'Qty', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('hpp', 'HPP', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_produk'));
		}
		else
		{
		  $data = array(
		  	'id_satuan'    => $this->input->post('satuan'),
		  	'sub_sku' 	   => $this->input->post('sub_sku'), 
		    'id_sku'       => $this->input->post('sku'),
	        'nama_produk'  => $this->input->post('nama_produk'),
	        'qty_produk'   => $this->input->post('qty'),
	        'hpp_produk'   => $this->input->post('hpp')
		  );

		  $this->Produk_model->update($this->input->post('id_produk'),$data);

		  write_log();

		  if(!empty($this->input->post('tokpro_access_id')))
          {
            $this->db->where('id_produk', $this->input->post('id_produk'));
            $this->db->delete('tokpro_data_access');

            write_log();

            $tokpro_access_id = count($this->input->post('tokpro_access_id'));

            for($i_tokpro_access_id = 0; $i_tokpro_access_id < $tokpro_access_id; $i_tokpro_access_id++)
	        {
	            $datas_tokpro_access_id[$i_tokpro_access_id] = array(
	              'id_produk'   => $this->input->post('id_produk'),
	              'id_toko'  	=> $this->input->post('tokpro_access_id['.$i_tokpro_access_id.']'),
	            );

	            $this->db->insert('tokpro_data_access', $datas_tokpro_access_id[$i_tokpro_access_id]);

	            write_log();
	        }
          }

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/produk');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Produk_model->get_by_id($id);

		if($delete)
		{
		  $this->db->where('id_produk', $id);
          $this->db->delete('propak_data_access');

          write_log();

		  $this->db->where('id_produk', $id);
          $this->db->delete('tokpro_data_access');
          
          write_log();

		  $this->Produk_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/produk');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/produk');
		}
	}

	function export() {
		$data['title']	= "Export Data Produk_".date("Y_m_d");
		$data['produk']	= $this->Produk_model->get_all();

		$this->load->view('back/produk/produk_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/produk/proses_import';

	    $this->load->view('back/produk/produk_import', $this->data);
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
							$data 	= array(	'id_produk'			=> $row->getCellAtIndex(0),
												'id_satuan'			=> $row->getCellAtIndex(1),
												'id_sku'			=> $row->getCellAtIndex(2),
												'sub_sku'			=> $row->getCellAtIndex(3),
												'nama_produk'		=> $row->getCellAtIndex(4),
												'qty_produk'		=> $row->getCellAtIndex(5),
												'hpp_produk'		=> $row->getCellAtIndex(6),
							);

							$this->Produk_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/produk');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}
}

/* End of file Produk.php */
/* Location: ./application/controllers/admin/Produk.php */