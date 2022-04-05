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

class Sku extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'SKU';

	    $this->load->model(array('Sku_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
	    $this->data['btn_import']    = 'Format Data Import';
	    $this->data['add_action'] = base_url('admin/sku/tambah');
	    $this->data['export_action'] = base_url('admin/sku/export');
	    $this->data['import_action'] = base_url('assets/template/excel/format_sku.xlsx');

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

	    $this->data['get_all'] = $this->Sku_model->get_all();

	    $this->load->view('back/sku/sku_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/sku/tambah_proses';

	    $this->data['kode_sku'] = [
	      'name'          => 'kode_sku',
	      'id'            => 'kode-sku',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('kode_sku'),
	    ];

	    $this->data['sku_nama'] = [
	      'name'          => 'nama_sku',
	      'id'            => 'nama-sku',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_sku'),
	    ];

	    $this->load->view('back/sku/sku_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('kode_sku', 'Kode SKU', 'max_length[50]|is_unique[sku.kode_sku]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('kode_sku').'</strong> sudah ada. Buat %s baru',
					'max_length'	=> '%s maksimal 50 karakter'
			)
		);

		$this->form_validation->set_rules('nama_sku', 'Nama SKU', 'max_length[100]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 100 karakter'
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
	        'kode_sku'     => $this->input->post('kode_sku'),
	        'nama_sku'     => $this->input->post('nama_sku')
	      );

	      $this->Sku_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/sku');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['sku']     = $this->Sku_model->get_by_id($id);

	    if($this->data['sku'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/sku/ubah_proses';

	      $this->data['id_sku'] = [
	        'name'          => 'id_sku',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['sku_nama'] = [
		      'name'          => 'nama_sku',
		      'id'            => 'nama-sku',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['kode_sku'] = [
		      'name'          => 'kode_sku',
		      'id'            => 'kode-sku',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

	      $this->load->view('back/sku/sku_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/sku');
	    }
	}

	function ubah_proses()
	{
		$cek_sku = $this->Sku_model->get_by_id($this->input->post('id_sku'));

		if ($cek_sku->kode_sku != $this->input->post('kode_sku')) {
			$this->form_validation->set_rules('kode_sku', 'Kode SKU', 'trim|required',
				array(	'required' 		=> '%s harus diisi!')
			);
		}else{
			$this->form_validation->set_rules('kode_sku', 'Kode SKU', 'is_unique[sku.kode_sku]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'is_unique'		=> '<strong>'.$this->input->post('kode_sku').'</strong> sudah ada. Buat %s baru',
				)
			);
		}

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_sku'));
		}
		else
		{
		  $data = array(
		    'nama_sku'     => $this->input->post('nama_sku'),
		    'kode_sku'     => $this->input->post('kode_sku')
		  );

		  $this->Sku_model->update($this->input->post('id_sku'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/sku');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Sku_model->get_by_id($id);

		if($delete)
		{
		  $this->Sku_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/sku');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/sku');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$produk = $this->input->post('ids');
		// echo $produk;

		$this->Sku_model->delete_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	// function export() {
	// 	$data['title']	= "Export Data SKU_".date("Y_m_d");
	// 	$data['sku']	= $this->Sku_model->get_all();

	// 	$this->load->view('back/sku/sku_export', $data);
	// }

	function export_sku()
	{
		$data['title']	= "Export Data SKU_".date("Y_m_d");
		$data['sku']	= $this->Sku_model->get_all();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_sku');
		$sheet->setCellValue('B1', 'kode_sku');
		$sheet->setCellValue('C1', 'nama_sku');

        // set Row
        $rowCount = 2;
        foreach ($data['sku'] as $list) {
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

            $sheet->SetCellValue('A' . $rowCount, $list->id_sku);
            $sheet->SetCellValue('B' . $rowCount, $list->kode_sku);
            $sheet->SetCellValue('C' . $rowCount, $list->nama_sku);

           
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
	    $this->data['action']     = 'admin/sku/proses_import';

	    $this->load->view('back/sku/sku_import', $this->data);
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
							$data 	= array(	'id_sku'	=> $row->getCellAtIndex(0),
												'kode_sku'	=> $row->getCellAtIndex(1),
												'nama_sku'	=> $row->getCellAtIndex(2)
							);

							$this->Sku_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/sku');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

}

/* End of file Sku.php */
/* Location: ./application/controllers/admin/Sku.php */