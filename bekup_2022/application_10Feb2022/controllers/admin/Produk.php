<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Produk extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Produk';

	    $this->load->model(array('Produk_model', 'Toko_model', 'Tokproaccess_model', 'Paket_model', 'Keluar_model'));

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
		$this->data['btn_sinkron']    = 'Data Sync with Sales';
		$this->data['sinkron_action'] = base_url('admin/produk/sinkron_produk');

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
	    $this->data['action_delete_pilih'] = 'admin/produk/hapus_dipilih';

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
	        'width' 		=> '100%' 
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

		// if ($this->input->post('paket') != '') {
		// 	if (isset($cek_paket)) {
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
		// 	}else{
		// 		$dataPaket	= array( 'id_produk'	=> $this->input->post('id_produk'),
		// 	      					 'id_paket' 	=> $this->input->post('paket')
		// 	      	);

		// 	      	$this->db->insert('propak_data_access', $dataPaket);
		// 	      	write_log();
		// 	}	
		// }

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

	function generatehpp($id = '')
	{
		$detail_produk = $this->Produk_model->get_by_id($id);
		$cek_propak = $this->Paket_model->get_pakduk_produk_by_produk($id);
    	if (count($cek_propak) > 0) {
    		$total = 0;
			// echo print_r($cek_propak)."<br>";
			foreach ($cek_propak as $val_propak) {
				$total =+ $total + ($val_propak->qty_pakduk * $val_propak->hpp_produk);
			}

			$data = array(
		        'hpp_produk'   => $total
			);

			$this->Produk_model->update($id,$data);

			write_log();

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully. Generate HPP '.$detail_produk->nama_produk.': <b>'.$total.'</b></div>');
			redirect('admin/produk');
		}

		
	}

	function generatehpp_dipilih()
	{
		is_delete();
		
		$id = $this->input->post('ids');
		
		$cek_propak = $this->Paket_model->get_pakduk_produk_by_produk_in($id);
    	if (count($cek_propak) > 0) {
    		// echo print_r($cek_propak);
    		
			// echo print_r($cek_propak)."<br>";
			foreach ($cek_propak as $val_propak) {
				$total = 0;
				$total =+ $total + ($val_propak->qty_pakduk * $val_propak->hpp_produk);
				
				$data = array(
			        'hpp_produk'   => $total
				);

				$this->Produk_model->update($val_propak->produk_utama,$data);

				write_log();
			}
		}

		$pesan = "Berhasil digenerate hpp!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
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

	function hapus_dipilih()
	{
		is_delete();
		
		$produk = $this->input->post('ids');
		// echo $produk;

		$this->Produk_model->delete_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
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

	public function sinkron_produk()
	{
		// Ubah Total Hpp (menjadi 0) dan Margin (0 dikurangi ongkir)
		$kosongHppMargin = array( 'total_hpp' => 0,
								  'margin' 	  => 0
			);

		$this->Keluar_model->update_kosong_hpp_margin($kosongHppMargin);

		$kosongHppDetail = array( 'hpp' => 0
		);

		$this->Keluar_model->update_kosong_hpp_detail($kosongHppDetail);

		$ambil_penjualan = $this->Keluar_model->get_all();
		foreach ($ambil_penjualan as $val_penjualan) {
			$get_penjualan = $this->Keluar_model->get_by_id($val_penjualan->nomor_pesanan);
			$kurangiMarginOngkir = array( 'margin'	=> $get_penjualan->margin - $get_penjualan->total_jual 
			);

			$this->Keluar_model->update($get_penjualan->nomor_pesanan, $kurangiMarginOngkir);			
		}

		// Loop detail penjualan untuk ambil id_produknya
		$ambil_detail = $this->Keluar_model->get_all_detail();
		foreach ($ambil_detail as $val_detail) {
			$get_produk = $this->Keluar_model->get_all_detail_by_id_produk($val_detail->nomor_pesanan, $val_detail->id_produk);
			if (isset($get_produk)) {
				$fix_hpp = $get_produk->hpp_produk * $get_produk->qty;
				if ($get_produk->margin < 0) {
					$fix_margin = $get_produk->margin * -1;
				}else{
					$fix_margin = $get_produk->margin;
				}

				$updateDetail = array( 'hpp'	=> $fix_hpp
				);
				
				$this->Keluar_model->update_detail($get_produk->nomor_pesanan, $get_produk->id_produk, $updateDetail);

				$updatePenjualan = array( 	'total_hpp'	=> $get_produk->total_hpp + $fix_hpp,
											'margin'	=> $fix_margin - $fix_hpp
				);

				$this->Keluar_model->update($get_produk->nomor_pesanan, $updatePenjualan);	
			}
		}
		
		$this->session->set_flashdata('message', '<div class="alert alert-success">Data synced successfully</div>');
	  	redirect('admin/produk');
	}

	function sinkron_dipilih()
	{
		$produk 	= $this->input->post('ids');
		$get_produk = $this->Keluar_model->get_all_detail_by_produk_in($produk);
		foreach ($get_produk as $val_produk) {
			// Ubah Total Hpp (menjadi 0) dan Margin (0 dikurangi ongkir)
			$kosongHppMargin = array( 'total_hpp' => 0,
									  'margin' 	  => 0
				);

			$this->Keluar_model->update_kosong_hpp_margin_by_id($val_produk->nomor_pesanan, $kosongHppMargin);

			$kosongHppDetail = array( 'hpp' => 0
			);

			$this->Keluar_model->update_kosong_hpp_detail_by_id($val_produk->nomor_pesanan, $kosongHppDetail);
			
			$get_penjualan = $this->Keluar_model->get_by_id($val_produk->nomor_pesanan);
			if (isset($get_penjualan)) {
				// Mengurangi Margin dengan Jumlah Ongkir dan Harga Jual
				$kurangiMarginOngkir = array( 'margin'	=> $get_penjualan->margin - $get_penjualan->total_jual 
				);

				$this->Keluar_model->update($get_penjualan->nomor_pesanan, $kurangiMarginOngkir);	
			}
		}


		$get_detail = $this->Keluar_model->get_all_detail_by_produk_in($produk);
		foreach ($get_detail as $val_detail) {
			$get_pesanan = $this->Keluar_model->get_all_detail_by_id_produk($val_detail->nomor_pesanan, $val_detail->id_produk);
			if (isset($get_pesanan)) {
				$fix_hpp = $get_pesanan->hpp_produk * $get_pesanan->qty;
				if ($get_pesanan->margin < 0) {
					$fix_margin = $get_pesanan->margin * -1;
				}else{
					$fix_margin = $get_pesanan->margin;
				}	

				// Mengupdate margin
				$updateDetail = array( 'hpp'	=> $fix_hpp
				);
				
				$this->Keluar_model->update_detail($get_pesanan->nomor_pesanan, $get_pesanan->id_produk, $updateDetail);

				$updatePenjualan = array( 	'total_hpp'	=> $get_pesanan->total_hpp + $fix_hpp,
											'margin'	=> $fix_margin - $fix_hpp
				);

				$this->Keluar_model->update($get_pesanan->nomor_pesanan, $updatePenjualan);
			}
		}

		// $this->Produk_model->delete_in($produk);

		$pesan = "Berhasil disinkronisasi!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}
}

/* End of file Produk.php */
/* Location: ./application/controllers/admin/Produk.php */