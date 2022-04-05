<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Jenis_timeline extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Jenis Timeline';

	    $this->load->model(array('Jenis_timeline_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/jenis_timeline/export');
	    $this->data['add_action'] = base_url('admin/jenis_timeline/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_jenis_timeline.xlsx');

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

	    $this->data['get_all'] = $this->Jenis_timeline_model->get_all();

	    $this->load->view('back/jenis_timeline/jenis_timeline_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/jenis_timeline/tambah_proses';

	    $this->data['kode_timeline'] = [
	      'name'          => 'kode_timeline',
	      'id'            => 'kode-timeline',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('kode_timeline'),
	    ];
	    
	    $this->data['jenis_timeline'] = [
	      'name'          => 'jenis_timeline',
	      'id'            => 'jenis-timeline',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('jenis_timeline'),
	    ];

	    $this->load->view('back/jenis_timeline/jenis_timeline_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('jenis_timeline', 'Nama Jenis Timeline', 'max_length[50]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 50 karakter'
			)
		);

		$this->form_validation->set_rules('kode_timeline', 'Kode Jenis Timeline', 'is_unique[jenis_timeline.kode_jenis_timeline]|max_length[3]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('kode_timeline').'</strong> sudah ada. Buat %s baru',
					'max_length'	=> '%s maksimal 3 karakter'
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
	        'kode_jenis_timeline'	=> $this->input->post('kode_timeline'),
	        'nama_jenis_timeline'	=> $this->input->post('jenis_timeline'),
	      );

	      $this->Jenis_timeline_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/jenis_timeline');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['timeline']     = $this->Jenis_timeline_model->get_by_id($id);

	    if($this->data['timeline'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/jenis_timeline/ubah_proses';

	      $this->data['id_jenis_timeline'] = [
	        'name'          => 'id_jenis_timeline',
	        'type'          => 'hidden',
	      ];
		  

	    $this->data['kode_timeline'] = [
	      'name'          => 'kode_timeline',
	      'id'            => 'kode-timeline',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];
	    
	    $this->data['jenis_timeline'] = [
	      'name'          => 'jenis_timeline',
	      'id'            => 'jenis-timeline',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	      $this->load->view('back/jenis_timeline/jenis_timeline_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/jenis_timeline');
	    }
	}

	function ubah_proses()
	{
		$cek_jenis_timeline = $this->Jenis_timeline_model->get_by_id($this->input->post('id_jenis_timeline'));

		if ($cek_jenis_timeline->kode_jenis_timeline == $this->input->post('kode_timeline')) {
			$this->form_validation->set_rules('kode_timeline', 'Kode Jenis Timeline', 'max_length[3]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'max_length'	=> '%s maksimal 3 karakter'
				)
			);
		}else{
			$this->form_validation->set_rules('kode_timeline', 'Kode Jenis Timeline', 'is_unique[jenis_timeline.kode_jenis_timeline]|max_length[3]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'is_unique'		=> '<strong>'.$this->input->post('kode_kategori').'</strong> sudah ada. Buat %s baru',
						'max_length'	=> '%s maksimal 3 karakter'
				)
			);
		}

		$this->form_validation->set_rules('jenis_timeline', 'Nama Jenis Timeline', 'max_length[50]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 50 karakter'
			)
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_jenis_timeline'));
		}
		else
		{
		  $data = array(
			'kode_jenis_timeline'	=> $this->input->post('kode_timeline'),
	        'nama_jenis_timeline'	=> $this->input->post('jenis_timeline'),
		  );

		  $this->Jenis_timeline_model->update($this->input->post('id_jenis_timeline'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/jenis_timeline');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Jenis_timeline_model->get_by_id($id);

		if($delete)
		{
		  $this->Jenis_timeline_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/jenis_timeline');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/jenis_timeline');
		}
	}

	function export() {
		$data['title']	= "Export Data Jenis Timeline_".date("Y_m_d");
		$data['jenis_timeline']	= $this->Jenis_timeline_model->get_all();

		$this->load->view('back/jenis_timeline/jenis_timeline_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/jenis_timeline/proses_import';

	    $this->load->view('back/jenis_timeline/jenis_timeline_import', $this->data);
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
							$data 	= array(	'id_jenis_timeline'			=> $row->getCellAtIndex(0),
												'kode_jenis_timeline'		=> $row->getCellAtIndex(1),
												'nama_jenis_timeline'		=> $row->getCellAtIndex(2)
							);

							$this->Jenis_timeline_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/jenis_timeline');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

}

/* End of file Jenis_timeline.php */
/* Location: ./application/controllers/admin/Jenis_timeline.php */