<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

// Include librari PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Vendor extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Vendor';

	    $this->load->model(array('Vendor_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/vendor/export');
	    $this->data['add_action'] = base_url('admin/vendor/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_vendor.xlsx');

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

	    $this->data['get_all'] = $this->Vendor_model->get_all();

	    $this->load->view('back/vendor/vendor_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/vendor/tambah_proses';

	    $this->data['vendor_nama'] = [
	      'name'          => 'nama_vendor',
	      'id'            => 'nama-vendor',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_vendor'),
	    ];

	    $this->data['vendor_alamat'] = [
	      'name'          => 'alamat_vendor',
	      'id'            => 'alamat-vendor',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'value'         => $this->form_validation->set_value('alamat_vendor'),
	    ];

	    $this->data['vendor_hp'] = [
	      'name'          => 'hp_vendor',
	      'id'            => 'hp-vendor',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'value'         => $this->form_validation->set_value('hp_vendor'),
	    ];

	    $this->data['vendor_telpon'] = [
	      'name'          => 'telpon_vendor',
	      'id'            => 'telpon-vendor',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'value'         => $this->form_validation->set_value('telpon_vendor'),
	    ];

	    $this->load->view('back/vendor/vendor_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_vendor', 'Nama Vendor', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('hp_vendor', 'No. Handphone Vendor', 'trim|max_length[13]',
			array(	'max_length' 		=> '%s harus 13 karakter!')
		);

		$this->form_validation->set_rules('telpon_vendor', 'No. Telepon Vendor', 'trim|max_length[13]',
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
	        'nama_vendor'     => $this->input->post('nama_vendor'),
	        'alamat_vendor'     => $this->input->post('alamat_vendor'),
	        'no_hp_vendor'     => $this->input->post('hp_vendor'),
	        'no_telpon_vendor'     => $this->input->post('telpon_vendor'),
	      );

	      $this->Vendor_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/vendor');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['vendor']     = $this->Vendor_model->get_by_id($id);

	    if($this->data['vendor'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/vendor/ubah_proses';

	      $this->data['id_vendor'] = [
	        'name'          => 'id_vendor',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['vendor_nama'] = [
		      'name'          => 'nama_vendor',
		      'id'            => 'nama-vendor',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['vendor_alamat'] = [
		      'name'          => 'alamat_vendor',
		      'id'            => 'alamat-vendor',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];

		    $this->data['vendor_hp'] = [
		      'name'          => 'hp_vendor',
		      'id'            => 'hp-vendor',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];

		    $this->data['vendor_telpon'] = [
		      'name'          => 'telpon_vendor',
		      'id'            => 'telpon-vendor',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];

	      $this->load->view('back/vendor/vendor_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/vendor');
	    }
	}

	function ubah_proses()
	{
		$this->form_validation->set_rules('nama_vendor', 'Nama Vendor', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('hp_vendor', 'No. Handphone Vendor', 'trim|max_length[13]',
			array(	'max_length' 		=> '%s harus 13 karakter!')
		);

		$this->form_validation->set_rules('telpon_vendor', 'No. Telepon Vendor', 'trim|max_length[13]',
			array(	'max_length' 		=> '%s harus 13 karakter!')
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_vendor'));
		}
		else
		{
		  $data = array(
		    'nama_vendor'     => $this->input->post('nama_vendor'),
		    'alamat_vendor'     => $this->input->post('alamat_vendor'),
	        'no_hp_vendor'     => $this->input->post('hp_vendor'),
	        'no_telpon_vendor'     => $this->input->post('telpon_vendor'),
		  );

		  $this->Vendor_model->update($this->input->post('id_vendor'),$data);

				write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/vendor');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Vendor_model->get_by_id($id);

		if($delete)
		{
		  $this->Vendor_model->delete($id);

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/vendor');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/vendor');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$produk = $this->input->post('ids');
		// echo $produk;

		$this->Vendor_model->delete_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	// function export() {
	// 	$data['title']	= "Export Data Vendor_".date("Y_m_d");
	// 	$data['vendor']	= $this->Vendor_model->get_all();

	// 	$this->load->view('back/vendor/vendor_export', $data);
	// }

	function export_vendor()
	{
		$data['title']	= "Export Data Vendor_".date("Y_m_d");
		$data['vendor']	= $this->Vendor_model->get_all();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_vendor');
		$sheet->setCellValue('B1', 'nama_vendor');
		$sheet->setCellValue('C1', 'alamat_vendor');
		$sheet->setCellValue('D1', 'no_hp_vendor');
		$sheet->setCellValue('E1', 'no_telpon_vendor');

        // set Row
        $rowCount = 2;
        foreach ($data['vendor'] as $list) {
        	// Nomor Pesanan
	        // if (is_numeric($list->nomor_pesanan)) {
	        //   if (strlen($list->nomor_pesanan) < 15) {
	        //     $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	        //     $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
	        //   }else{
	        //     $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	        //     // The old way to force string. NumberFormat::FORMAT_TEXT is not
	        //     // enough.
	        //     // $formatted_value .= ' ';
	        //     // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
	        //     $sheet->setCellValueExplicit('A' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        //   }
	        // }else{
	        //   $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	        //   $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
	        // }

            $sheet->SetCellValue('A' . $rowCount, $list->id_vendor);
            $sheet->SetCellValue('B' . $rowCount, $list->nama_vendor);
            $sheet->SetCellValue('C' . $rowCount, $list->alamat_vendor);
            $sheet->SetCellValue('D' . $rowCount, $list->no_hp_vendor);
            $sheet->SetCellValue('E' . $rowCount, $list->no_telpon_vendor);

           
            $rowCount++;
        }

        $writer = new Xlsx($spreadsheet);
		
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Transfer-Encoding: Binary"); 
		header('Content-Disposition: attachment;filename="'. $data['title'] .'.xlsx"');
		header("Pragma: no-cache");
		header("Expires: 0");

		$writer->save('php://output');

		die();
	}

	public function import()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/vendor/proses_import';

	    $this->load->view('back/vendor/vendor_import', $this->data);
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
							$data 	= array(	'id_vendor'			=> $row->getCellAtIndex(0),
												'nama_vendor'		=> $row->getCellAtIndex(1),
												'alamat_vendor'		=> $row->getCellAtIndex(2),
												'no_hp_vendor'		=> $row->getCellAtIndex(3),
												'no_telpon_vendor'	=> $row->getCellAtIndex(4),
							);

							$this->Vendor_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/vendor');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}
}

/* End of file Vendor.php */
/* Location: ./application/controllers/admin/Vendor.php */