<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Penerima extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Penerima';

	    $this->load->model(array('Penerima_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/penerima/export');
	    $this->data['add_action'] = base_url('admin/penerima/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_penerima.xlsx');
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

	    $this->data['get_all'] = $this->Penerima_model->get_all();

	    $this->load->view('back/penerima/penerima_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/penerima/tambah_proses';

	    $this->data['penerima_nama'] = [
	      'name'          => 'nama_penerima',
	      'id'            => 'nama-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_penerima'),
	    ];

	    $this->data['penerima_alamat'] = [
	      'name'          => 'alamat_penerima',
	      'id'            => 'alamat-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'value'         => $this->form_validation->set_value('alamat_penerima'),
	    ];

	    $this->data['penerima_hp'] = [
	      'name'          => 'hp_penerima',
	      'id'            => 'hp-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'value'         => $this->form_validation->set_value('hp_penerima'),
	    ];

	    $this->data['penerima_telpon'] = [
	      'name'          => 'telpon_penerima',
	      'id'            => 'telpon-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'value'         => $this->form_validation->set_value('telpon_penerima'),
	    ];

	    $this->load->view('back/penerima/penerima_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_penerima', 'Nama Penerima', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('hp_penerima', 'No. Handphone Penerima', 'trim|max_length[13]',
			array(	'max_length' 		=> '%s harus 13 karakter!')
		);

		$this->form_validation->set_rules('telpon_penerima', 'No. Telepon Penerima', 'trim|max_length[13]',
			array(	'max_length' 		=> '%s harus 13 karakter!')
		);

	    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

	    if($this->form_validation->run() === FALSE)
	    {
	      $this->tambah();
	    }
	    else
	    {
	      $data = array(
	        'nama_penerima'     => $this->input->post('nama_penerima'),
	        'alamat_penerima'     => $this->input->post('alamat_penerima'),
	        'no_hp_penerima'     => $this->input->post('hp_penerima'),
	        'no_telpon_penerima'     => $this->input->post('telpon_penerima'),
	      );

	      $this->Penerima_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/penerima');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['penerima']     = $this->Penerima_model->get_by_id($id);

	    if($this->data['penerima'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/penerima/ubah_proses';

	      $this->data['id_penerima'] = [
	        'name'          => 'id_penerima',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['penerima_nama'] = [
		      'name'          => 'nama_penerima',
		      'id'            => 'nama-penerima',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['penerima_alamat'] = [
		      'name'          => 'alamat_penerima',
		      'id'            => 'alamat-penerima',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];

		    $this->data['penerima_hp'] = [
		      'name'          => 'hp_penerima',
		      'id'            => 'hp-penerima',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];

		    $this->data['penerima_telpon'] = [
		      'name'          => 'telpon_penerima',
		      'id'            => 'telpon-penerima',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];

	      $this->load->view('back/penerima/penerima_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/penerima');
	    }
	}

	function ubah_proses()
	{
		$this->form_validation->set_rules('nama_penerima', 'Nama Penerima', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('hp_penerima', 'No. Handphone Penerima', 'trim|max_length[13]',
			array(	'max_length' 		=> '%s harus 13 karakter!')
		);

		$this->form_validation->set_rules('telpon_penerima', 'No. Telepon Penerima', 'trim|max_length[13]',
			array(	'max_length' 		=> '%s harus 13 karakter!')
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_penerima'));
		}
		else
		{
		  $data = array(
		    'nama_penerima'     => $this->input->post('nama_penerima'),
		    'alamat_penerima'     => $this->input->post('alamat_penerima'),
	        'no_hp_penerima'     => $this->input->post('hp_penerima'),
	        'no_telpon_penerima'     => $this->input->post('telpon_penerima'),
		  );

		  $this->Penerima_model->update($this->input->post('id_penerima'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/penerima');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Penerima_model->get_by_id($id);

		if($delete)
		{
		  $this->Penerima_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/penerima');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/penerima');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$produk = $this->input->post('ids');
		// echo $produk;

		$this->Penerima_model->delete_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	function export() {
		$data['title']	= "Export Data Penerima_".date("Y_m_d");
		$data['penerima']	= $this->Penerima_model->get_all();

		$this->load->view('back/penerima/penerima_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/penerima/proses_import';

	    $this->load->view('back/penerima/penerima_import', $this->data);
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
							$data 	= array(	'id_penerima'		=> $row->getCellAtIndex(0),
												'nama_penerima'		=> $row->getCellAtIndex(1),
												'alamat_penerima'	=> $row->getCellAtIndex(2),
												'no_hp_penerima'	=> $row->getCellAtIndex(3),
												'no_telpon_penerima'=> $row->getCellAtIndex(4),
							);

							$this->Penerima_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/penerima');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}
}

/* End of file Penerima.php */
/* Location: ./application/controllers/admin/Penerima.php */