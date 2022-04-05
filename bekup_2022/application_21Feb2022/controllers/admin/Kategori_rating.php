<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Kategori_rating extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Kategori Rating';

	    $this->load->model(array('Kategori_rating_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
	    $this->data['btn_import']    = 'Format Data Import';
	    $this->data['add_action'] = base_url('admin/kategori_rating/tambah');
	    $this->data['export_action'] = base_url('admin/kategori_rating/export');
	    $this->data['import_action'] = base_url('assets/template/excel/format_kategori_rating.xlsx');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	function get_data_datatables()
    {
        $list = $this->Kategori_rating_model->get_datatables();
        $dataJSON = array();
        $no = 1;
        foreach ($list as $data) {
   			// Detail Provinsi
   			$action = '<a href="'.base_url('admin/kategori_rating/ubah/'.$data->id_kategori_rating).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/kategori_rating/hapus/'.$data->id_kategori_rating).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->id_kategori_rating.'">';
			
            $row = array();
            $row['no'] = $no;
            $row['nama'] = $data->nama_kategori_rating;
            $row['action'] = $action;
            $row['select'] = $select;
 
            $dataJSON[] = $row;

            $no++;
        }
 
        $output = array(
            "recordsTotal" => $this->Kategori_rating_model->count_all(),
            "recordsFiltered" => $this->Kategori_rating_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

	public function index()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';

	    $this->data['get_all'] = $this->Kategori_rating_model->get_all();

	    $this->load->view('back/kategori_rating/kategori_rating_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/kategori_rating/tambah_proses';

	    $this->data['nama_kategori_rating'] = [
	      'name'          => 'nama_kategori_rating',
	      'id'            => 'nama-kategori-rating',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_kategori_rating'),
	    ];

	    $this->load->view('back/kategori_rating/kategori_rating_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_kategori_rating', 'Nama Kategori Rating', 'max_length[255]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 255 karakter'
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
	        'nama_kategori_rating'     => $this->input->post('nama_kategori_rating')
	      );

	      $this->Kategori_rating_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/kategori_rating');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['kategori_rating']     = $this->Kategori_rating_model->get_by_id($id);

	    if($this->data['kategori_rating'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/kategori_rating/ubah_proses';

	      $this->data['id_kategori_rating'] = [
	        'name'          => 'id_kategori_rating',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['nama_kategori_rating'] = [
	      'name'          => 'nama_kategori_rating',
	      'id'            => 'nama-kategori-rating',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];


	      $this->load->view('back/kategori_rating/kategori_rating_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/kategori_rating');
	    }
	}

	function ubah_proses()
	{
		$cek_kategori_rating_model = $this->Kategori_rating_model->get_by_id($this->input->post('id_kategori_rating'));

		if ($cek_kategori_rating_model->nama_kategori_rating != $this->input->post('nama_kategori_rating')) {
			$this->form_validation->set_rules('nama_kategori_rating', 'Nama Kategori Rating', 'trim|required',
				array(	'required' 		=> '%s harus diisi!')
			);
		}else{
			$this->form_validation->set_rules('nama_kategori_rating', 'Nama Kategori Rating', 'is_unique[kategori_rating.nama_kategori_rating]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'is_unique'		=> '<strong>'.$this->input->post('nama_kategori_rating').'</strong> sudah ada. Buat %s baru',
				)
			);
		}

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_kategori_rating'));
		}
		else
		{
		  $data = array(
		    'nama_kategori_rating'     => $this->input->post('nama_kategori_rating'),
		  );

		  $this->Kategori_rating_model->update($this->input->post('id_kategori_rating'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/kategori_rating');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Kategori_rating_model->get_by_id($id);

		if($delete)
		{
		  $this->Kategori_rating_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/kategori_rating');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/kategori_rating');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$rating = $this->input->post('ids');
		// echo $produk;

		$this->Kategori_rating_model->delete_in($rating);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	function export() {
		$data['title']	= "Export Data Kategori Rating_".date("Y_m_d");
		$data['kategori_rating']	= $this->Kategori_rating_model->get_all();

		$this->load->view('back/kategori_rating/kategori_rating_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/kategori_rating/proses_import';

	    $this->load->view('back/kategori_rating/kategori_rating_import', $this->data);
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
							$data 	= array(	'id_kategori_rating'	=> $row->getCellAtIndex(0),
												'nama_kategori_rating'	=> $row->getCellAtIndex(1),
							);

							$this->Kategori_rating_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/kategori_rating');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

}

/* End of file Kategori_rating.php */
/* Location: ./application/controllers/admin/Kategori_rating.php */