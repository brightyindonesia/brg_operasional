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

class Kategori_po extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Kategori PO';

	    $this->load->model(array('Kategori_po_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['export_action'] = base_url('admin/kategori_po/export');
	    $this->data['add_action'] = base_url('admin/kategori_po/tambah');
	    $this->data['btn_import']    = 'Format Data Import';
		$this->data['import_action'] = base_url('assets/template/excel/format_kategori_po.xlsx');

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

	    $this->data['get_all'] = $this->Kategori_po_model->get_all();

	    $this->load->view('back/kategori_po/kategori_po_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module'];
	    $this->data['action']     = 'admin/kategori_po/tambah_proses';

	    $this->data['kode_kategori'] = [
	      'name'          => 'kode_kategori',
	      'id'            => 'kode-kategori',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('kode_kategori'),
	    ];
	    
	    $this->data['jenis_kategori'] = [
	      'name'          => 'jenis_kategori',
	      'id'            => 'jenis-kategori',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('jenis_kategori'),
	    ];

	    $this->load->view('back/kategori_po/kategori_po_add', $this->data);
	}

	public function tambah_proses()
	{
		$this->form_validation->set_rules('jenis_kategori', 'Nama Jenis Kategori', 'max_length[50]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 50 karakter'
			)
		);

		$this->form_validation->set_rules('kode_kategori', 'Kode Jenis Kategori', 'is_unique[kategori_po.kode_kategori_po]|max_length[3]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('kode_kategori').'</strong> sudah ada. Buat %s baru',
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
	        'kode_kategori_po'	=> $this->input->post('kode_kategori'),
	        'nama_kategori_po'	=> $this->input->post('jenis_kategori'),
	      );

	      $this->Kategori_po_model->insert($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/kategori_po');
	    }
	}

	public function ubah($id = '')
	{
		is_update();

	    $this->data['kategori_po']     = $this->Kategori_po_model->get_by_id($id);

	    if($this->data['kategori_po'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/kategori_po/ubah_proses';

	      $this->data['id_kategori_po'] = [
	        'name'          => 'id_kategori_po',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['kode_kategori'] = [
	      'name'          => 'kode_kategori',
	      'id'            => 'kode-kategori',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];
	    
	    $this->data['jenis_kategori'] = [
	      'name'          => 'jenis_kategori',
	      'id'            => 'jenis-kategori',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	      $this->load->view('back/kategori_po/kategori_po_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/kategori_po');
	    }
	}

	function ubah_proses()
	{
		$cek_kategori_po = $this->Kategori_po_model->get_by_id($this->input->post('id_kategori_po'));

		if ($cek_kategori_po->kode_kategori_po == $this->input->post('kode_kategori')) {
			$this->form_validation->set_rules('kode_kategori', 'Kode Jenis Kategori', 'max_length[3]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'max_length'	=> '%s maksimal 3 karakter'
				)
			);
		}else{
			$this->form_validation->set_rules('kode_kategori', 'Kode Jenis Kategori', 'is_unique[kategori_po.kode_kategori_po]|max_length[3]|trim|required',
				array(	'required' 		=> '%s harus diisi!',
						'is_unique'		=> '<strong>'.$this->input->post('kode_kategori').'</strong> sudah ada. Buat %s baru',
						'max_length'	=> '%s maksimal 3 karakter'
				)
			);
		}

		$this->form_validation->set_rules('jenis_kategori', 'Nama Jenis Kategori', 'max_length[50]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 50 karakter'
			)
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('id_kategori_po'));
		}
		else
		{
		  $data = array(
			'kode_kategori_po'	=> $this->input->post('kode_kategori'),
	        'nama_kategori_po'	=> $this->input->post('jenis_kategori'),
		  );

		  $this->Kategori_po_model->update($this->input->post('id_kategori_po'),$data);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/kategori_po');
		}
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Kategori_po_model->get_by_id($id);

		if($delete)
		{
		  $this->Kategori_po_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/kategori_po');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/kategori_po');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$produk = $this->input->post('ids');
		// echo $produk;

		$this->Kategori_po_model->delete_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	// function export() {
	// 	$data['title']	= "Export Data Jenis Kategori_".date("Y_m_d");
	// 	$data['kategori_po']	= $this->Kategori_po_model->get_all();

	// 	$this->load->view('back/kategori_po/kategori_po_export', $data);
	// }

	function export_kategori_po()
	{
		$data['title']	= "Export Data Jenis Kategori PO_".date("Y_m_d");
		$data['kategori_po']	= $this->Kategori_po_model->get_all();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_kategori_po');
		$sheet->setCellValue('B1', 'kode_kategori_po');
		$sheet->setCellValue('C1', 'nama_kategori_po');

        // set Row
        $rowCount = 2;
        foreach ($data['kategori_po'] as $list) {
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

            $sheet->SetCellValue('A' . $rowCount, $list->id_kategori_po);
            $sheet->SetCellValue('B' . $rowCount, $list->kode_kategori_po);
            $sheet->SetCellValue('B' . $rowCount, $list->nama_kategori_po);

           
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
	    $this->data['action']     = 'admin/kategori_po/proses_import';

	    $this->load->view('back/kategori_po/kategori_po_import', $this->data);
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
							if ($row->getCellAtIndex(0) != 'ID Kategori PO' || $row->getCellAtIndex(1) != 'Kode Kategori PO' || $row->getCellAtIndex(2) != 'Nama Kategori PO') {
								$reader->close();
								unlink('uploads/'.$file['file_name']);
								$this->session->set_flashdata('message', '<div class="alert alert-danger">Import data does not match!</div>');
								redirect('admin/kategori_po');
							}
						}

						if ($numRow > 1) {
							$data 	= array(	'id_kategori_po'			=> $row->getCellAtIndex(0),
												'kode_kategori_po'			=> $row->getCellAtIndex(1),
												'nama_kategori_po'			=> $row->getCellAtIndex(2)
							);

							$this->Kategori_po_model->import($data);
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/kategori_po');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}
}

/* End of file Kategori_po.php */
/* Location: ./application/controllers/admin/Kategori_po.php */