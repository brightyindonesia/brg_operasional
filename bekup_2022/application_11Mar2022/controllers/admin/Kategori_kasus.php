<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Kategori_kasus extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Kategori Kasus';

	    $this->load->model(array('Kategori_kasus_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
	    $this->data['btn_import']    = 'Format Data Import';
	    $this->data['add_action'] = base_url('admin/kategori_kasus/tambah');
	    $this->data['export_action'] = base_url('admin/kategori_kasus/export');
	    $this->data['import_action'] = base_url('assets/template/excel/format_kategori_kasus.xlsx');

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
        $list = $this->Kategori_kasus_model->get_datatables();
        $dataJSON = array();
        $no = 1;
        foreach ($list as $data) {
   			// Detail Provinsi
   			$action = '<a href="'.base_url('admin/kategori_kasus/ubah/'.$data->id_kategori_kasus).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/kategori_kasus/hapus/'.$data->id_kategori_kasus).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->id_kategori_kasus.'">';
			
            $row = array();
            $row['no'] = $no;
            $row['nama'] = $data->nama_kategori_kasus;
            $row['action'] = $action;
            $row['select'] = $select;
 
            $dataJSON[] = $row;

            $no++;
        }
 
        $output = array(
            "recordsTotal" => $this->Kategori_kasus_model->count_all(),
            "recordsFiltered" => $this->Kategori_kasus_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

	public function index()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';

	    $this->data['get_all'] = $this->Kategori_kasus_model->get_all();

	    $this->load->view('back/kategori_kasus/kategori_kasus_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/kategori_kasus/tambah_proses';

	    $this->data['nama_kategori_kasus'] = [
	      'name'          => 'nama_kategori_kasus',
	      'id'            => 'nama-kategori-kasus',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_kategori_kasus'),
	    ];

	    $this->load->view('back/kategori_kasus/kategori_kasus_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_kategori_kasus', 'Nama Kategori Kasus', 'max_length[255]|trim|required',
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
	        'nama_kategori_kasus'     => $this->input->post('nama_kategori_kasus')
	      );

	      $this->Kategori_kasus_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/kategori_kasus');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['kategori_kasus']     = $this->Kategori_kasus_model->get_by_id($id);

	    if($this->data['kategori_kasus'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/kategori_kasus/ubah_proses';

	      $this->data['id_kategori_kasus'] = [
	        'name'          => 'id_kategori_kasus',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['nama_kategori_kasus'] = [
	      'name'          => 'nama_kategori_kasus',
	      'id'            => 'nama-kategori-kasus',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];


	      $this->load->view('back/kategori_kasus/kategori_kasus_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/kategori_kasus');
	    }
	}

	function ubah_proses()
	{
		$cek_kategori_kasus_model = $this->Kategori_kasus_model->get_by_id($this->input->post('id_kategori_kasus'));

		if ($cek_kategori_kasus_model->nama_kategori_kasus != $this->input->post('nama_kategori_kasus')) {
			$this->form_validation->set_rules('nama_kategori_kasus', 'Nama Kategori Kasus', 'trim|required',
				array(	'required' 		=> '%s harus diisi!')
			);
		}else{
			$this->form_validation->set_rules('nama_kategori_kasus', 'Nama Kategori Kasus', 'is_unique[kategori_kasus.nama_kategori_kasus]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'is_unique'		=> '<strong>'.$this->input->post('nama_kategori_kasus').'</strong> sudah ada. Buat %s baru',
				)
			);
		}

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_kategori_kasus'));
		}
		else
		{
		  $data = array(
		    'nama_kategori_kasus'     => $this->input->post('nama_kategori_kasus'),
		  );

		  $this->Kategori_kasus_model->update($this->input->post('id_kategori_kasus'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/kategori_kasus');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Kategori_kasus_model->get_by_id($id);

		if($delete)
		{
		  $this->Kategori_kasus_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/kategori_kasus');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/kategori_kasus');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$kasus = $this->input->post('ids');
		// echo $produk;

		$this->Kategori_kasus_model->delete_in($kasus);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	function export() {
		$data['title']	= "Export Data Kategori Kasus_".date("Y_m_d");
		$data['kategori_kasus']	= $this->Kategori_kasus_model->get_all();

		$this->load->view('back/kategori_kasus/kategori_kasus_export', $data);
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/kategori_kasus/proses_import';

	    $this->load->view('back/kategori_kasus/kategori_kasus_import', $this->data);
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
							$data 	= array(	'id_kategori_kasus'	=> $row->getCellAtIndex(0),
												'nama_kategori_kasus'	=> $row->getCellAtIndex(1),
							);

							$this->Kategori_kasus_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/kategori_kasus');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

}

/* End of file Kategori_kasus.php */
/* Location: ./application/controllers/admin/Kategori_kasus.php */