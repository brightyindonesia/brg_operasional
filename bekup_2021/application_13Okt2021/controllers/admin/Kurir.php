<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Kurir extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Kurir';

	    $this->load->model(array('Kurir_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/kurir/export');
	    $this->data['add_action'] = base_url('admin/kurir/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_kurir.xlsx');

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

	    $this->data['get_all'] = $this->Kurir_model->get_all();

	    $this->load->view('back/kurir/kurir_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/kurir/tambah_proses';

	    $this->data['kurir_nama'] = [
	      'name'          => 'nama_kurir',
	      'id'            => 'nama-kurir',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_kurir'),
	    ];

	    $this->load->view('back/kurir/kurir_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_kurir', 'Nama Kurir', 'is_unique[kurir.nama_kurir]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('nama_kurir').'</strong> sudah ada. Buat %s baru',
			)
		);

	    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

	    if($this->form_validation->run() === FALSE)
	    {
	      $this->tambah();
	    }
	    else
	    {
	      $data = array(
	        'nama_kurir'     => $this->input->post('nama_kurir'),
	      );

	      $this->Kurir_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/kurir');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['kurir']     = $this->Kurir_model->get_by_id($id);

	    if($this->data['kurir'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/kurir/ubah_proses';

	      $this->data['id_kurir'] = [
	        'name'          => 'id_kurir',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['nama_kurir'] = [
		      'name'          => 'nama_kurir',
		      'id'            => 'nama-kurir',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

	      $this->load->view('back/kurir/kurir_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/kurir');
	    }
	}

	function ubah_proses()
	{
		$cek_kurir = $this->Kurir_model->get_by_id($this->input->post('id_kurir'));

		if ($cek_kurir->nama_kurir == $this->input->post('nama_kurir')) {
			$this->form_validation->set_rules('nama_kurir', 'Nama Kurir', 'trim|required',
				array(	'required' 		=> '%s harus diisi!')
			);
		}else{
			$this->form_validation->set_rules('nama_kurir', 'Nama Kurir', 'is_unique[kurir.nama_kurir]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'is_unique'		=> '<strong>'.$this->input->post('nama_kurir').'</strong> sudah ada. Buat %s baru',
				)
			);
		}

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_kurir'));
		}
		else
		{
		  $data = array(
		    'nama_kurir'     => $this->input->post('nama_kurir'),
		  );

		  $this->Kurir_model->update($this->input->post('id_kurir'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/kurir');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Kurir_model->get_by_id($id);

		if($delete)
		{
		  $this->Kurir_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/kurir');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/kurir');
		}
	}

	function export() {
		$data['title']	= "Export Data Kurir_".date("Y_m_d");
		$data['kurir']	= $this->Kurir_model->get_all();

		$this->load->view('back/kurir/kurir_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/kurir/proses_import';

	    $this->load->view('back/kurir/kurir_import', $this->data);
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
							$data 	= array(	'id_kurir'			=> $row->getCellAtIndex(0),
												'nama_kurir'		=> $row->getCellAtIndex(1)
							);

							$this->Kurir_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/kurir');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}
}

/* End of file Kurir.php */
/* Location: ./application/controllers/admin/Kurir.php */