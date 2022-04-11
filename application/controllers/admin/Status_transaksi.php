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

class Status_transaksi extends CI_Controller {

public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Status Transaksi';

	    $this->load->model(array('Status_transaksi_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/status_transaksi/export');
	    $this->data['add_action'] = base_url('admin/status_transaksi/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_status_transaksi.xlsx');

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

	    $this->data['get_all'] = $this->Status_transaksi_model->get_all();

	    $this->load->view('back/status_transaksi/status_transaksi_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/status_transaksi/tambah_proses';
	    
	    $this->data['nama_status'] = [
	      'name'          => 'nama_status',
	      'id'            => 'nama-status',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_status'),
	    ];

	    $this->load->view('back/status_transaksi/status_transaksi_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_status', 'Nama Status Transaksi', 'max_length[50]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 50 karakter'
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
	        'nama_status_transaksi'	=> $this->input->post('nama_status'),
	      );

	      $this->Status_transaksi_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/status_transaksi');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['status_transaksi']     = $this->Status_transaksi_model->get_by_id($id);

	    if($this->data['status_transaksi'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/status_transaksi/ubah_proses';

	      $this->data['id_status_transaksi'] = [
	        'name'          => 'id_status_transaksi',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['nama_status'] = [
	      'name'          => 'nama_status',
	      'id'            => 'nama-status',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	      $this->load->view('back/status_transaksi/status_transaksi_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/status_transaksi');
	    }
	}

	function ubah_proses()
	{
		$cek_status_transaksi = $this->Status_transaksi_model->get_by_id($this->input->post('id_status_transaksi'));

		$this->form_validation->set_rules('nama_status', 'Nama Status Transaksi', 'max_length[50]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 50 karakter'
			)
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_status_transaksi'));
		}
		else
		{
		  $data = array(
			'nama_status_transaksi'	=> $this->input->post('nama_status'),
		  );

		  $this->Status_transaksi_model->update($this->input->post('id_status_transaksi'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/status_transaksi');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Status_transaksi_model->get_by_id($id);

		if($delete)
		{
		  $this->Status_transaksi_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/status_transaksi');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/status_transaksi');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$produk = $this->input->post('ids');
		// echo $produk;

		$this->Status_transaksi_model->delete_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	// function export() {
	// 	$data['title']	= "Export Data Status Transaksi_".date("Y_m_d");
	// 	$data['status_transaksi']	= $this->Status_transaksi_model->get_all();

	// 	$this->load->view('back/status_transaksi/status_transaksi_export', $data);
	// }

	function export_status_transaksi()
	{
		$data['title']	= "Export Data Status Transaksi_".date("Y_m_d");
		$data['status_transaksi']	= $this->Status_transaksi_model->get_all();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_status_transaksi');
		$sheet->setCellValue('B1', 'nama_status_transaksi');

        // set Row
        $rowCount = 2;
        foreach ($data['status_transaksi'] as $list) {
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

            $sheet->SetCellValue('A' . $rowCount, $list->id_status_transaksi);
            $sheet->SetCellValue('B' . $rowCount, $list->nama_status_transaksi);

           
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
	    $this->data['action']     = 'admin/status_transaksi/proses_import';

	    $this->load->view('back/status_transaksi/status_transaksi_import', $this->data);
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
						if ($numRow == 1) {
							if ($row->getCellAtIndex(0) != 'ID Status Transaksi' || $row->getCellAtIndex(1) != 'Nama Status Transaksi') {
								$reader->close();
								unlink('uploads/'.$file['file_name']);
								$this->session->set_flashdata('message', '<div class="alert alert-danger">Import data does not match!</div>');
								redirect('admin/status_transaksi');
							}
						}

						if ($numRow > 1) {
							$data 	= array(	'id_status_transaksi'			=> $row->getCellAtIndex(0),
												'nama_status_transaksi'			=> $row->getCellAtIndex(1)
							);

							$this->Status_transaksi_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/status_transaksi');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

}

/* End of file Status_transaksi.php */
/* Location: ./application/controllers/admin/Status_transaksi.php */