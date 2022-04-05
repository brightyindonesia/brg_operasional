<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Jenis_toko extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Jenis Toko';

	    $this->load->model(array('Jenis_toko_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/jenis_toko/export');
	    $this->data['add_action'] = base_url('admin/jenis_toko/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_jenis_toko.xlsx');

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

	    $this->data['get_all'] = $this->Jenis_toko_model->get_all();

	    $this->load->view('back/jenis_toko/jenis_toko_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/jenis_toko/tambah_proses';

	    $this->data['jenis_toko_nama'] = [
	      'name'          => 'nama_jenis_toko',
	      'id'            => 'nama-jenis_toko',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_jenis_toko'),
	    ];

	    $this->load->view('back/jenis_toko/jenis_toko_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_jenis_toko', 'Nama Jenis Toko', 'trim|required',
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
	        'nama_jenis_toko'     => $this->input->post('nama_jenis_toko'),
	      );

	      $this->Jenis_toko_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/jenis_toko');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['jenis_toko']     = $this->Jenis_toko_model->get_by_id($id);

	    if($this->data['jenis_toko'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/jenis_toko/ubah_proses';

	      $this->data['id_jenis_toko'] = [
	        'name'          => 'id_jenis_toko',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['nama_jenis_toko'] = [
		      'name'          => 'nama_jenis_toko',
		      'id'            => 'nama-jenis-toko',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

	      $this->load->view('back/jenis_toko/jenis_toko_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/jenis_toko');
	    }
	}

	function ubah_proses()
	{
		$this->form_validation->set_rules('nama_jenis_toko', 'Nama Jenis Toko', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_jenis_toko'));
		}
		else
		{
		  $data = array(
		    'nama_jenis_toko'     => $this->input->post('nama_jenis_toko'),
		  );

		  $this->Jenis_toko_model->update($this->input->post('id_jenis_toko'),$data);

				write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/jenis_toko');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Jenis_toko_model->get_by_id($id);

		if($delete)
		{
		  $this->Jenis_toko_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/jenis_toko');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/jenis_toko');
		}
	}

	function export() {
		$data['title']	= "Export Data Jenis Toko_".date("Y_m_d");
		$data['jenis_toko']	= $this->Jenis_toko_model->get_all();

		$this->load->view('back/jenis_toko/jenis_toko_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/jenis_toko/proses_import';

	    $this->load->view('back/jenis_toko/jenis_toko_import', $this->data);
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
							$data 	= array(	'id_jenis_toko'		=> $row->getCellAtIndex(0),
												'nama_jenis_toko'	=> $row->getCellAtIndex(1)
							);

							$this->Jenis_toko_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/jenis_toko');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}
}

/* End of file Jenis_toko.php */
/* Location: ./application/controllers/admin/Jenis_toko.php */