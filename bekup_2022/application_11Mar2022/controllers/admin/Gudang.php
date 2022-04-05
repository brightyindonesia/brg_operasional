<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Gudang extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Gudang';

	    $this->load->model(array('Gudang_model', 'Toko_model', 'Gutokaccess_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/gudang/export');
	    $this->data['add_action'] = base_url('admin/gudang/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_gudang.xlsx');

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

	    $this->data['get_all'] = $this->Gudang_model->get_all();

	    $this->load->view('back/gudang/gudang_list', $this->data);		
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/gudang/tambah_proses';
	    $this->data['get_all_gutok_data_access']  = $this->Toko_model->get_all_combobox_without_pilih();

	    $this->data['gudang_nama'] = [
	      'name'          => 'gudang_nama',
	      'id'            => 'nama-gudang',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('gudang_nama'),
	    ];

	    $this->data['gutok_access_id'] = [
			'name'          => 'gutok_access_id[]',
			'id'            => 'gutok-access-id',
			'class'         => 'form-control select2',
			'multiple'      => '',
		];

	    $this->data['alamat'] = [
	      'name'          => 'alamat',
	      'id'            => 'alamat',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'value'         => $this->form_validation->set_value('alamat'),
	    ];

	    $this->load->view('back/gudang/gudang_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('gudang_nama', 'Nama Gudang', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);
		
		$this->form_validation->set_rules('gutok_access_id[]', 'Daftar Toko', 'required',
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
	        'nama_gudang'    			=> $this->input->post('gudang_nama'),
	        'alamat_gudang'   			=> $this->input->post('alamat')
	      );

	      $this->Gudang_model->insert($data);

	      write_log();

	      $this->db->select_max('id_gudang');
          $result = $this->db->get('gudang')->row_array();

          $count_gutok_access_id = count($this->input->post('gutok_access_id'));

          for($gutok_access_id = 0; $gutok_access_id < $count_gutok_access_id; $gutok_access_id++)
          {
            $datas_gutok_access_id[$gutok_access_id] = array(
              'id_gudang'   => $result['id_gudang'],
              'id_toko'  	=> $this->input->post('gutok_access_id['.$gutok_access_id.']'),
            );

            $this->db->insert('gutok_data_access', $datas_gutok_access_id[$gutok_access_id]);

            write_log();
          }

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/gudang');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['gudang']    						 	 = $this->Gudang_model->get_by_id($id);
	    $this->data['get_all_combobox_gutok_data_access']   = $this->Gutokaccess_model->get_all_combobox();
	    $this->data['get_all_gutok_data_access_old']        = $this->Gutokaccess_model->get_all_data_access_old($id);

	    if($this->data['gudang'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/gudang/ubah_proses';

	      $this->data['id_gudang'] = [
	        'name'          => 'id_gudang',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['gudang_nama'] = [
		      'name'          => 'gudang_nama',
		      'id'            => 'nama-gudang',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		    ];

		    $this->data['gutok_access_id'] = [
				'name'          => 'gutok_access_id[]',
				'id'            => 'gutok-access-id',
				'class'         => 'form-control select2',
				'multiple'      => '',
			];

		    $this->data['alamat'] = [
		      'name'          => 'alamat',
		      'id'            => 'alamat',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];


	      $this->load->view('back/gudang/gudang_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/gudang');
	    }
	}

	function ubah_proses()
	{
		$cek_gudang = $this->Gudang_model->get_by_id($this->input->post('id_gudang'));

		
		if ($cek_gudang->nama_gudang == $this->input->post('gudang_nama')) {
			$this->form_validation->set_rules('gudang_nama', 'Nama Gudang', 'max_length[255]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'max_length' 	=> '%s harus 255 karakter!'
				)
			);
		}else{
			$this->form_validation->set_rules('gudang_nama', 'Nama Gudang', 'is_unique[gudang.nama_gudang]|max_length[255]|trim|required',

			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('gudang_nama').'</strong> sudah ada. Buat %s baru',
					'max_length'	=> '%s maksimal 255 karakter' )
			);
		}
		
		
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_gudang'));
		}
		else
		{
		  $data = array(
	        'nama_gudang'    			=> $this->input->post('gudang_nama'),
	        'alamat_gudang'   			=> $this->input->post('alamat')
	      );

		  $this->Gudang_model->update($this->input->post('id_gudang'),$data);

		  write_log();

		  if(!empty($this->input->post('gutok_access_id')))
          {
            $this->db->where('id_gudang', $this->input->post('id_gudang'));
            $this->db->delete('gutok_data_access');

            $gutok_access_id = count($this->input->post('gutok_access_id'));

            for($i_gutok_access_id = 0; $i_gutok_access_id < $gutok_access_id; $i_gutok_access_id++)
	        {
	            $datas_gutok_access_id[$i_gutok_access_id] = array(
	              'id_gudang'   => $this->input->post('id_gudang'),
	              'id_toko'  	=> $this->input->post('gutok_access_id['.$i_gutok_access_id.']'),
	            );

	            $this->db->insert('gutok_data_access', $datas_gutok_access_id[$i_gutok_access_id]);

	            write_log();
	        }
          }

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/gudang');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Gudang_model->get_by_id($id);

		if($delete)
		{
		  $this->db->where('id_gudang', $id);
          $this->db->delete('gutok_data_access');
		  
		  $this->Gudang_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/gudang');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/gudang');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$gudang = $this->input->post('ids');
		// echo $produk;

		$this->db->where_in('id_gudang', explode(",", $gudang));
        $this->db->delete('gutok_data_access');

		$this->Gudang_model->delete_in($gudang);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	function export() {
		$data['title']	= "Export Data Gudang_".date("Y_m_d");
		$data['gudang']	= $this->Gudang_model->get_all();

		$this->load->view('back/gudang/gudang_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/gudang/proses_import';

	    $this->load->view('back/gudang/gudang_import', $this->data);
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
							$data 	= array(	'id_gudang'		=> $row->getCellAtIndex(0),
												'nama_gudang'	=> $row->getCellAtIndex(1),
												'alamat_gudang'	=> $row->getCellAtIndex(2),
											);

							$this->Gudang_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/gudang');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

}

/* End of file Gudang.php */
/* Location: ./application/controllers/admin/Gudang.php */