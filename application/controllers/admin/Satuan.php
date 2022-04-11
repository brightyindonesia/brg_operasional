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

class Satuan extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Satuan';

	    $this->load->model(array('Satuan_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/satuan/export');
	    $this->data['add_action'] = base_url('admin/satuan/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_satuan.xlsx');

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

	    $this->data['get_all'] = $this->Satuan_model->get_all();

	    $this->load->view('back/satuan/satuan_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/satuan/tambah_proses';

	    $this->data['satuan_nama'] = [
	      'name'          => 'nama_satuan',
	      'id'            => 'nama-satuan',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_satuan'),
	    ];

	    $this->load->view('back/satuan/satuan_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('nama_satuan', 'Nama Satuan', 'is_unique[satuan.nama_satuan]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('nama_satuan').'</strong> sudah ada. Buat %s baru',
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
	        'nama_satuan'     => $this->input->post('nama_satuan'),
	      );

	      $this->Satuan_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/satuan');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['satuan']     = $this->Satuan_model->get_by_id($id);

	    if($this->data['satuan'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/satuan/ubah_proses';

	      $this->data['id_satuan'] = [
	        'name'          => 'id_satuan',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['nama_satuan'] = [
		      'name'          => 'nama_satuan',
		      'id'            => 'nama-satuan',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

	      $this->load->view('back/satuan/satuan_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/satuan');
	    }
	}

	function ubah_proses()
	{
		$cek_satuan = $this->Satuan_model->get_by_id($this->input->post('id_satuan'));

		if ($cek_satuan->nama_satuan == $this->input->post('satuan_satuan')) {
			$this->form_validation->set_rules('nama_satuan', 'Nama Satuan', 'trim|required',
				array(	'required' 		=> '%s harus diisi!')
			);
		}else{
			$this->form_validation->set_rules('nama_satuan', 'Nama Satuan', 'is_unique[satuan.nama_satuan]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('nama_satuan').'</strong> sudah ada. Buat %s baru',
			)
		);
		}

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_satuan'));
		}
		else
		{
		  $data = array(
		    'nama_satuan'     => $this->input->post('nama_satuan'),
		  );

		  $this->Satuan_model->update($this->input->post('id_satuan'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/satuan');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Satuan_model->get_by_id($id);

		if($delete)
		{
		  $this->Satuan_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/satuan');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/satuan');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$produk = $this->input->post('ids');
		// echo $produk;

		$this->Satuan_model->delete_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	// function export() {
	// 	$data['title']	= "Export Data Satuan_".date("Y_m_d");
	// 	$data['satuan']	= $this->Satuan_model->get_all();

	// 	$this->load->view('back/satuan/satuan_export', $data);
	// }

	function export_satuan()
	{
		$data['title']	= "Export Data Satuan_".date("Y_m_d");
		$data['satuan']	= $this->Satuan_model->get_all();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_satuan');
		$sheet->setCellValue('B1', 'nama_satuan');

        // set Row
        $rowCount = 2;
        foreach ($data['satuan'] as $list) {
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

            $sheet->SetCellValue('A' . $rowCount, $list->id_satuan);
            $sheet->SetCellValue('B' . $rowCount, $list->nama_satuan);

           
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
	    $this->data['action']     = 'admin/satuan/proses_import';

	    $this->load->view('back/satuan/satuan_import', $this->data);
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
							if ($row->getCellAtIndex(0) != 'ID Satuan' || $row->getCellAtIndex(1) != 'Nama Satuan') {
								$reader->close();
								unlink('uploads/'.$file['file_name']);
								$this->session->set_flashdata('message', '<div class="alert alert-danger">Import data does not match!</div>');
								redirect('admin/satuan');
							}
						}

						if ($numRow > 1) {
							$data 	= array(	'id_satuan'		=> $row->getCellAtIndex(0),
												'nama_satuan'	=> $row->getCellAtIndex(1)
							);

							$this->Satuan_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/satuan');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}
}

/* End of file Satuan.php */
/* Location: ./application/controllers/admin/Satuan.php */