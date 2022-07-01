<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Keyword extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module_provinsi'] = 'Keyword Provinsi';
	    $this->data['module_produk'] = 'Keyword Produk';
	    $this->data['module_toko'] = 'Keyword Toko';
	    $this->data['module_kurir'] = 'Keyword Kurir';
	    $this->data['module_kotkab'] = 'Keyword Kota / Kabupaten';

	    $this->load->model(array('Keyword_model', 'Produk_model', 'Toko_model', 'Kurir_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_delete']    = 'Delete Data';
	    $this->data['btn_export']    = 'Export Data';
	    $this->data['btn_import']    = 'Format Data Import';

	    // Provinsi
	    $this->data['add_action_provinsi'] = base_url('admin/keyword/provinsi_tambah');
	    $this->data['export_action_provinsi'] = base_url('admin/keyword/provinsi_export');
	    $this->data['format_provinsi'] = base_url('assets/template/excel/format_keyword_provinsi.xlsx');

	    // Produk
	    $this->data['add_action_produk'] = base_url('admin/keyword/produk_tambah');
	    $this->data['export_action_produk'] = base_url('admin/keyword/produk_export');
	    $this->data['format_produk'] = base_url('assets/template/excel/format_keyword_produk.xlsx');

	    // Toko
	    $this->data['add_action_toko'] = base_url('admin/keyword/toko_tambah');
	    $this->data['export_action_toko'] = base_url('admin/keyword/toko_export');
	    $this->data['format_toko'] = base_url('assets/template/excel/format_keyword_toko.xlsx');

	    // Kurir
	    $this->data['add_action_kurir'] = base_url('admin/keyword/kurir_tambah');
	    $this->data['export_action_kurir'] = base_url('admin/keyword/kurir_export');
	    $this->data['format_kurir'] = base_url('assets/template/excel/format_keyword_kurir.xlsx');


	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	// Provinsi
	function dasbor_list_provinsi_count(){
		$provinsi = $this->Keyword_model->total_rows_provinsi();
		$detail_provinsi = $this->Keyword_model->total_rows_detail_provinsi();
		$detail_kotkab = $this->Keyword_model->total_rows_detail_kotkab();
    	if (isset($provinsi) || isset($detail_provinsi) || isset($detail_kotkab) ) {	
        	$msg = array(	'provinsi'			=> $provinsi,
			        		'detail_provinsi'	=> $detail_provinsi,
			        		'detail_kotkab'		=> $detail_kotkab,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'provinsi'			=> 0,
			        		'detail_provinsi'	=> 0,
			        		'detail_kotkab'		=> 0,
        			);
        	echo json_encode($msg); 
    		// $msg = array(	'validasi'	=> validation_errors()
      //   			);
      //   	echo json_encode($msg);
    	}
    }

    function get_data_provinsi()
    {
        $list = $this->Keyword_model->get_datatables_provinsi();
        $dataJSON = array();
        foreach ($list as $data) {
   			// Detail Provinsi
   			$action = '<a href="'.base_url('admin/keyword/provinsi_ubah/'.$data->id_keyword_provinsi).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/keyword/provinsi_hapus/'.$data->id_keyword_provinsi).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->id_keyword_provinsi.'">';
			$get_detail_provinsi = $this->Keyword_model->get_detail_provinsi_by_id_provinsi($data->id_keyword_provinsi);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table table-bordered table-striped" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td><b>Kota / Kabupaten</b></td>'.
			                '<td><b>Keyword Kota / Kabupaten</b></td>'.
			            '</tr>';

			if($get_detail_provinsi == NULL)
	        {
	          $detail .= '<tr align="center">'.
			                '<td colspan="2"><a href="#" class="btn btn-sm btn-danger">No Data</a></td>'.
			            '</tr>';
	        }
	        else
	        {
	          foreach ($get_detail_provinsi as $val_detail) {
					$detail .= '<tr align="center">'.
					                '<td>'.$val_detail->nama_kotkab.'</td>';

					$cek_detail_kotkab = $this->Keyword_model->get_keys_detail_kotkab_by_id_detail_provinsi($val_detail->id_detail_keyword_provinsi);
		
			        if($cek_detail_kotkab == NULL)
			        {
			          $detail .=  '<td><a href="#" class="btn btn-sm btn-danger">No Data</a></td>'.
					              '</tr>';
			        }
			        else
			        {
			          $detail .=  '<td>';
			          foreach($cek_detail_kotkab as $val_kotkab)
			          {
			            $string = chunk_split($val_kotkab->keys_kotkab, 255, "</a> ");
			            $detail .=  '<a href="#" class="btn btn-sm btn-primary">'.$string;
			          }
			           $detail .=  '</td>'.
					              '</tr>';
			        }
					               
				}
	        }

            $row = array();
            $row['provinsi'] = $data->nama_provinsi;
            $row['action'] = $action;
            $row['detail'] = $detail;
            $row['select'] = $select;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Keyword_model->count_all_provinsi(),
            "recordsFiltered" => $this->Keyword_model->count_filtered_provinsi(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function provinsi()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module_provinsi'].' List';
	    $this->data['action_impor']  = 'admin/keyword/proses_provinsi_kotkab_impor';

	    $this->load->view('back/keyword/provinsi_list', $this->data);
	}

	public function provinsi_tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module_provinsi'];
	    $this->data['action']     = 'admin/keyword/provinsi_tambah_proses';

	    $this->data['provinsi_nama'] = [
	      'name'          => 'nama_provinsi',
	      'id'            => 'nama-provinsi',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_provinsi'),
	    ];

	    $this->load->view('back/keyword/provinsi_add', $this->data);
	}

	public function provinsi_tambah_proses()
	{
		$this->form_validation->set_rules('nama_provinsi', 'Nama Provinsi', 'max_length[255]|trim|required',
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
	        'nama_provinsi' => $this->input->post('nama_provinsi')
	      );

	      $this->Keyword_model->insert_provinsi($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/keyword/provinsi');
	    }
	}

	public function provinsi_ubah($id = '')
	{
		is_update();

	    $this->data['provinsi']    		= $this->Keyword_model->get_provinsi_by_id($id);
	    $this->data['kotkab']    		= $this->Keyword_model->get_provinsi_by_id($id);
	    $this->data['detail_provinsi']  = $this->Keyword_model->get_detail_provinsi_by_id_provinsi($id);
	    
	    if($this->data['provinsi'])
	    {
	    	// if (count($this->data['detail_provinsi']) > 0) {
		    // 	$this->data['arr_keys'] = array();

		    // 	foreach ($this->data['detail_provinsi'] as $val_provinsi) {
		    // 		$this->data['arr_keys'][] = $val_provinsi->keys_kotkab;
		    // 	}
		    // }else{
		    // 	$this->data['arr_keys'] = '';
		    // }

	      $this->data['page_title'] = 'Update Data '.$this->data['module_provinsi'];
	      $this->data['action']     = 'admin/keyword/provinsi_ubah_proses';

	      $this->data['id_keyword_provinsi'] = [
	        'name'          => 'id_keyword_provinsi',
	        'id'			=> 'id-keyword-provinsi',
	        'type'          => 'hidden',
	      ];

	      $this->data['id_keyword_detail_provinsi'] = [
	        'name'          => 'id_keyword_detail_provinsi',
	        'id'			=> 'id-keyword-detail-provinsi',
	        'type'          => 'hidden',
	      ];

		  $this->data['provinsi_nama'] = [
		      'name'          => 'nama_provinsi',
		      'id'            => 'nama-provinsi',
		      'class'         => 'form-control',
		      'readonly'	  => '',
		      'autocomplete'  => 'off',
		      'required'      => '',
		    ];

		  $this->data['kotkab_nama'] = [
		      'name'          => 'kotkab_nama',
		      'id'            => 'nama-kotkab',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		    ];

		  
		  $this->data['keys_kotkab'] = [
		      'name'          => 'keys_kotkab',
		      'id'            => 'keys-kotkab',
		      'class'	  	  => 'form-control',
		      'style'		  => 'width:100%'
		    ];

	      $this->load->view('back/keyword/provinsi_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/keyword/provinsi');
	    }
	}

	public function provinsi_ubah_proses() {
		$id_keyword_provinsi = $this->input->post('id');
		$kotkab = $this->input->post('kotkab');
		$keys = $this->input->post('keys');
		$ex_keys = explode(",", $keys);
		
		$data_provinsi = array(	'id_keyword_provinsi'	=> $id_keyword_provinsi,
								'nama_kotkab'			=> $kotkab,
		);

		$this->Keyword_model->insert_detail_provinsi($data_provinsi);

		write_log();

		$get_last_detail_provinsi = $this->Keyword_model->get_detail_provinsi_last();
		foreach ($ex_keys as $val_keys) {
			$cek_keys_detail_kotkab = $this->Keyword_model->get_keys_detail_kotkab_by_keys_kotkab_not_in($get_last_detail_provinsi->id_detail_keyword_provinsi, $val_keys);

			if (count($cek_keys_detail_kotkab) == 0) {
				$data_kotkab = array(	'id_detail_keyword_provinsi'	=> $get_last_detail_provinsi->id_detail_keyword_provinsi,
										'keys_kotkab'					=> $val_keys
				);

				$this->Keyword_model->insert_detail_kotkab($data_kotkab);

				write_log();	
			}
		}

		$pesan = "Berhasil ditambah!";	
    	$msg = array(	'sukses'	=> $pesan,
    					'id'		=> $id_keyword_provinsi
    			);
    	echo json_encode($msg);
	}	

	public function get_detail_provinsi_by_id_detail_provinsi($id)
	{
		$data['data'] = 0;
		$cek_detail = $this->Keyword_model->get_detail_provinsi_by_id_detail_provinsi_row($id);
		if($cek_detail){
			$isi = array();
			$i = 0;
			$data['data'] = 1;
			$data['id'] = $cek_detail->id_detail_keyword_provinsi;
			$data['id_provinsi'] = $cek_detail->id_keyword_provinsi;
			$data['nama_kotkab'] = $cek_detail->nama_kotkab;
			
			$cek_keyword = $this->Keyword_model->get_detail_provinsi_kotkab_by_id_detail_provinsi($id);
			$count_keys = count($cek_keyword) - 1;
			foreach ($cek_keyword as $val_keyword) {
	            $isi[] = $val_keyword->keys_kotkab;

				$i++;
			}

			$data['keys_kotkab'] = implode(",", $isi);
		}
		echo json_encode($data);	
	}

	public function detail_provinsi_ubah()
	{
		$i = $this->input;
		$id = $i->post('id');
		$id_provinsi = $i->post('id_provinsi');
		$pilihan = $i->post('pilihan');
		$kotkab = $i->post('kotkab');
		$keys = $i->post('keys');
		$ex_keys = explode(",", $keys);

		if ($pilihan == 'simpan') {
			$cek_detail = $this->Keyword_model->get_detail_provinsi_by_id_detail_provinsi_row($id);
			if ($cek_detail) {
				$this->Keyword_model->delete_detail_kotkab_by_id_detail_provinsi($id);
				$updateData = array( 'nama_kotkab'		=> $kotkab,	
				);	

				$this->Keyword_model->update_detail_provinsi($cek_detail->id_detail_keyword_provinsi, $updateData);
				
				foreach ($ex_keys as $val_keys) {
					$cek_keys_detail_kotkab = $this->Keyword_model->get_keys_detail_kotkab_by_keys_kotkab_not_in($cek_detail->id_detail_keyword_provinsi, $val_keys);

					if (count($cek_keys_detail_kotkab) == 0) {
						$data_kotkab = array(	'id_detail_keyword_provinsi'	=> $cek_detail->id_detail_keyword_provinsi,
												'keys_kotkab'					=> $val_keys
						);

						$this->Keyword_model->insert_detail_kotkab($data_kotkab);

						write_log();	
					}
				}

				$pesan = "Berhasil diubah!";	
	        	$msg = array(	'sukses'	=> $pesan,
	        					'id'		=> $id_provinsi,
	        			);
	        	echo json_encode($msg);
			}	
		}elseif ($pilihan == 'hapus'){
			$cek_detail = $this->Keyword_model->get_detail_provinsi_by_id_detail_provinsi_row($id);
			if ($cek_detail) {
				// echo print_r($updateData);
				$this->Keyword_model->delete_detail_kotkab_by_id_detail_provinsi($id);

				$this->Keyword_model->delete_detail_provinsi_by_id_detail_provinsi($id);

				$pesan = "Berhasil dihapus!";	
	        	$msg = array(	'sukses'	=> $pesan,
	        					'id'		=> $id_provinsi,
	        			);
	        	echo json_encode($msg);
			}
		}
	}

	public function provinsi_hapus($id = '')
	{
		is_delete();

		$provinsi  			= $this->Keyword_model->get_provinsi_by_id($id);
	    
	    if($provinsi)
		{
		  $detail_provinsi 	= $this->Keyword_model->get_detail_provinsi_by_id_provinsi($id);
		  if (count($detail_provinsi) > 0) {
		  	foreach ($detail_provinsi as $val_detail_provinsi) {
		  		$this->Keyword_model->delete_detail_kotkab_by_id_detail_provinsi($val_detail_provinsi->id_detail_keyword_provinsi);	
		  	}

		  	$this->Keyword_model->delete_detail_provinsi_by_id_provinsi($id);	
		  }

		  $this->Keyword_model->delete_provinsi($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/keyword/provinsi');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/keyword/provinsi');
		}
	}

	function provinsi_hapus_dipilih()
	{
		is_delete();

		$provinsi = $this->input->post('ids');

		$data_detail_provinsi = $this->Keyword_model->get_detail_provinsi_by_id_provinsi_in($provinsi);
		foreach ($data_detail_provinsi as $val_detail_provinsi) {
	  		$this->Keyword_model->delete_detail_kotkab_by_id_detail_provinsi($val_detail_provinsi->id_detail_keyword_provinsi);	
	  	}

		$this->Keyword_model->delete_detail_provinsi_in_by_id_provinsi($provinsi);

		$this->Keyword_model->delete_provinsi_in($provinsi);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function proses_provinsi_kotkab_impor()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc'.time();	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('import')) {
			$this->Keyword_model->deleteAll_detail_kotkab();
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/'.$file['file_name']);
			$numSheet 	= 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 1) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow == 1) {
							if ($row->getCellAtIndex(0) != 'ID' || $row->getCellAtIndex(1) != 'ID PROVINSI' || $row->getCellAtIndex(2) != 'PROVINSI' || $row->getCellAtIndex(3) != 'KOTA / KABUPATEN' || $row->getCellAtIndex(4) != 'KEYWORD') {
								$reader->close();
								unlink('uploads/'.$file['file_name']);
								$this->session->set_flashdata('message', '<div class="alert alert-danger">Import data does not match!</div>');
								redirect('admin/keyword/provinsi');
							}
						}

						if ($numRow > 1) {
							$dataProvinsi 	= array(	'id_keyword_provinsi'	=> $row->getCellAtIndex(1),
														'nama_provinsi'			=> $row->getCellAtIndex(2),
							);

							$dataDetailProvinsi 	= array(	'id_detail_keyword_provinsi'	=> $row->getCellAtIndex(0),
																'id_keyword_provinsi'			=> $row->getCellAtIndex(1),
																'nama_kotkab'					=> $row->getCellAtIndex(3),
							);

							$this->Keyword_model->import_provinsi($dataProvinsi);

							$this->Keyword_model->import_detail_provinsi($dataDetailProvinsi);

							if ($row->getCellAtIndex(4) != "") {
								$keys = $row->getCellAtIndex(4);
								$ex_keys = explode(",", $keys);
								foreach ($ex_keys as $val_keys) {
									$dataKotkab = array(	'id_detail_keyword_provinsi'	=> $row->getCellAtIndex(0),
															'keys_kotkab'					=> trim($val_keys)
									);

									$this->Keyword_model->import_detail_kotkab($dataKotkab);	
								}
							}
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/keyword/provinsi');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}


	// function provinsi_export() {
	// 	$data['title']	= "Export Data Keyword Provinsi_".date("Y_m_d");
	// 	$data['provinsi']	= $this->Keyword_model->get_all_detail_provinsi_provinsi();

	// 	$this->load->view('back/keyword/provinsi_export', $data);
	// }

	function export_provinsi()
	{
		$data['title']	= "Export Data Keyword Provinsi_".date("Y_m_d");
		$data['provinsi']	= $this->Keyword_model->get_all_detail_provinsi_provinsi();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_detail_keyword_provinsi');
		$sheet->setCellValue('B1', 'id_keyword_provinsi');
		$sheet->setCellValue('C1', 'nama_provinsi');
		$sheet->setCellValue('D1', 'nama_kotkab');
		$sheet->setCellValue('E1', 'keys_kotkab');

        // set Row
        $rowCount = 2;
        foreach ($data['provinsi'] as $list) {
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

	        $cek_detail_kotkab = $this->Keyword_model->get_keys_detail_kotkab_by_id_detail_provinsi($list->id_detail_keyword_provinsi);
			$keys = '';
		    if($cek_detail_kotkab == NULL)
		    {
		      $keys = '';
		    }
		    else
		    {
		      $i = 0;
		      $total = count($cek_detail_kotkab) - 1;
		      foreach($cek_detail_kotkab as $val_detail)
		      {
		        if ($i == $total) {
		          $keys .= $val_detail->keys_kotkab;
		        }else{
		          $keys .= $val_detail->keys_kotkab.",";
		        }

		        $i++;
		      }
		    }

            $sheet->SetCellValue('A' . $rowCount, $list->id_detail_keyword_provinsi);
            $sheet->SetCellValue('B' . $rowCount, $list->id_keyword_provinsi);
            $sheet->SetCellValue('C' . $rowCount, $list->nama_provinsi);
            $sheet->SetCellValue('D' . $rowCount, $list->nama_kotkab);
            $sheet->SetCellValue('E' . $rowCount, $keys);

           
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

	// Produk

	function dasbor_list_produk_count(){
		$produk = $this->Keyword_model->total_rows_produk();
		$keyword = $this->Keyword_model->total_rows_detail_produk();
    	if (isset($produk)) {	
        	$msg = array(	'produk'	=> $produk,
        					'keyword'	=> $keyword
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'produk'	=> 0,
			    			'keyword'	=> 0,
        			);
        	echo json_encode($msg); 
    		// $msg = array(	'validasi'	=> validation_errors()
      //   			);
      //   	echo json_encode($msg);
    	}
    }


    function get_data_produk()
    {
        $list = $this->Keyword_model->get_datatables_produk();
        $dataJSON = array();
        foreach ($list as $data) {
   			// Detail Produk
   			$action = '<a href="'.base_url('admin/keyword/produk_ubah/'.$data->id_keyword_produk).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/keyword/produk_hapus/'.$data->id_keyword_produk).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->id_keyword_produk.'">';
			$get_detail_produk = $this->Keyword_model->get_detail_produk_by_id_produk($data->id_keyword_produk);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table table-bordered table-striped" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td><b>Keyword Produk</b></td>'.
			            '</tr>';

			if($get_detail_produk == NULL)
	        {
	          $detail .= '<tr align="center">'.
			                '<td><a href="#" class="btn btn-sm btn-danger">No Data</a></td>'.
			            '</tr>';
	        }
	        else
	        {
	        	$detail .= '<tr align="center">';
		        $detail .=  '<td>';
	          foreach ($get_detail_produk as $val_detail) {
		            $string = chunk_split($val_detail->keys_produk, 255, "</a> ");
		            $detail .=  '<a href="#" class="btn btn-sm btn-primary">'.$string;
				}
				 $detail .=  '</td>'.
				            '</tr>';
	        }

            $row = array();
            $row['produk'] = $data->nama_produk;
            $row['action'] = $action;
            $row['detail'] = $detail;
            $row['select'] = $select;
			$row['status'] = $data->status ? '<a href="'.base_url('admin/keyword/ubah_status/'.$data->id_keyword_produk).'" class="btn btn-success">Active</a>' : '<a href="'.base_url('admin/keyword/ubah_status/'.$data->id_keyword_produk).'" class="btn btn-danger">Inactive</a>';
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Keyword_model->count_all_produk(),
            "recordsFiltered" => $this->Keyword_model->count_filtered_produk(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function produk()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module_produk'].' List';
	    $this->data['action_impor']  = 'admin/keyword/proses_produk_impor';

	    $this->data['get_all'] = $this->Keyword_model->get_all_produk();

	    $this->load->view('back/keyword/produk_list', $this->data);
	}

	public function produk_tambah()
	{
		is_create();    

		$this->data['page_title'] = 'Create New '.$this->data['module_produk'];
	    $this->data['action']     = 'admin/keyword/produk_tambah_proses';
	    $data_exist_produk = $this->Keyword_model->get_all_produk();

	    if (count($data_exist_produk) > 0) {
	    	$exist_produk = array();
		    foreach ($data_exist_produk as $val_exist) {
		    	$exist_produk[] = $val_exist->id_produk;
		    	
		    }

		    $this->data['get_all_produk'] = $this->Produk_model->get_all_combobox_where_not_in($exist_produk);	
	    }else{
		    $this->data['get_all_produk'] = $this->Produk_model->get_all_combobox();
	    }

	    $this->data['produk_nama'] = [
	      'name'          => 'nama_produk',
	      'id'            => 'nama-produk',
	      'class'         => 'form-control select2bs4',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['keys_produk'] = [
	      'name'          => 'keys_produk',
	      'id'            => 'keys-produk',
	      'class'	  	  => 'form-control',
	      'style'		  => 'width:100%'
	    ];

	    $this->load->view('back/keyword/produk_add', $this->data);
	}

	public function produk_tambah_proses() {
		$produk = $this->input->post('produk');
		$keys = $this->input->post('keys');
		$ex_keys = explode(",", $keys);
		
		$data_produk = array(	'id_produk'	=> $produk,
		);

		$this->Keyword_model->insert_produk($data_produk);

		write_log();

		$get_last_produk = $this->Keyword_model->get_produk_last();
		foreach ($ex_keys as $val_keys) {
			$cek_keys_produk = $this->Keyword_model->get_keys_produk_by_keys_produk_not_in($get_last_produk->id_keyword_produk, $val_keys);

			if (count($cek_keys_produk) == 0) {
				$data_detail_produk = array(	'id_keyword_produk'	=> $get_last_produk->id_keyword_produk,
												'keys_produk'		=> $val_keys
				);

				$this->Keyword_model->insert_detail_produk($data_detail_produk);

				write_log();	
			}
		}

		$pesan = "Berhasil ditambah!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function produk_ubah($id = '')
	{
		is_update();

	    $this->data['produk']    = $this->Keyword_model->get_produk_by_id($id);
	    $data_exist_produk = $this->Keyword_model->get_all_produk();

	    if (count($data_exist_produk) > 0) {
	    	$exist_produk = array();
		    foreach ($data_exist_produk as $val_exist) {
		    	$exist_produk[] = $val_exist->id_produk;
		    }

		    if (($key = array_search($this->data['produk']->id_produk, $exist_produk)) !== false) {
				unset($exist_produk[$key]);
			}

			if (count($exist_produk) > 0) {
				$this->data['get_all_produk'] = $this->Produk_model->get_all_combobox_where_not_in($exist_produk);	
			}else{
				$this->data['get_all_produk'] = $this->Produk_model->get_all_combobox();
			}		    		    
	    }else{
		    $this->data['get_all_produk'] = $this->Produk_model->get_all_combobox();
	    }

	    $this->data['detail_produk']  = $this->Keyword_model->get_detail_produk_by_id_produk($id);
	    
	    if($this->data['produk'])
	    {
	      if (count($this->data['detail_produk']) > 0) {
		    $this->data['arr_keys'] = array();
		    	foreach ($this->data['detail_produk'] as $val_produk) {
		    		$this->data['arr_keys'][] = $val_produk->keys_produk;
		    	}
	      }else{
		    	$this->data['arr_keys'] = '';
	      }
	      $this->data['page_title'] = 'Update Data '.$this->data['module_produk'];
	      $this->data['action']     = 'admin/keyword/produk_ubah_proses';

	      $this->data['id_keyword_produk'] = [
	        'name'          => 'id_keyword_produk',
	        'id'			=> 'id-keyword-produk',
	        'type'          => 'hidden',
	      ];

	      $this->data['produk_nama'] = [
	      'name'          => 'nama_produk',
	      'id'            => 'nama-produk',
	      'class'         => 'form-control select2bs4',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['keys_produk'] = [
	      'name'          => 'keys_produk',
	      'id'            => 'keys-produk',
	      'class'	  	  => 'form-control',
	      'style'		  => 'width:100%'
	    ];

	      $this->load->view('back/keyword/produk_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/keyword/produk');
	    }
	}	

	public function produk_ubah_proses() {
		$produk = $this->input->post('produk');
		$id = $this->input->post('id');
		$keys = $this->input->post('keys');
		$ex_keys = explode(",", $keys);
		
		$data_produk = array(	'id_produk'	=> $produk,
		);

		$this->Keyword_model->update_produk($id, $data_produk);

		write_log();

		$this->Keyword_model->delete_detail_produk_by_id_produk($id);

		foreach ($ex_keys as $val_keys) {
			$cek_keys_produk = $this->Keyword_model->get_keys_produk_by_keys_produk_not_in($id, $val_keys);

			if (count($cek_keys_produk) == 0) {
				$data_detail_produk = array(	'id_keyword_produk'	=> $id,
												'keys_produk'		=> $val_keys
				);

				$this->Keyword_model->insert_detail_produk($data_detail_produk);

				write_log();	
			}
		}

		$pesan = "Berhasil diubah!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function produk_hapus($id = '')
	{
		is_delete();

		$produk  			= $this->Keyword_model->get_produk_by_id($id);
	    
	    if($produk)
		{
		  $detail_produk 	= $this->Keyword_model->get_detail_produk_by_id_produk($id);
		  if (count($detail_produk) > 0) {
		  	$this->Keyword_model->delete_detail_produk_by_id_produk($id);	
		  }

		  $this->Keyword_model->delete_produk($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/keyword/produk');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/keyword/produk');
		}
	}

	function produk_hapus_dipilih()
	{
		is_delete();

		$produk = $this->input->post('ids');

		$this->Keyword_model->delete_detail_produk_in_by_id_produk($produk);

		$this->Keyword_model->delete_produk_in($produk);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function proses_produk_impor()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc'.time();	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('import')) {
			$this->Keyword_model->deleteAll_detail_kotkab();
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/'.$file['file_name']);
			$numSheet 	= 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 1) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow == 1) {
							if ($row->getCellAtIndex(0) != 'ID' || $row->getCellAtIndex(1) != 'ID PRODUK' || $row->getCellAtIndex(2) != 'KEYWORD') {
								$reader->close();
								unlink('uploads/'.$file['file_name']);
								$this->session->set_flashdata('message', '<div class="alert alert-danger">Import data does not match!</div>');
								redirect('admin/keyword/produk');
							}
						}

						if ($numRow > 1) {
							$dataProduk 	= array(	'id_keyword_produk'	=> $row->getCellAtIndex(0),
														'id_produk'			=> $row->getCellAtIndex(1),
							);

							$this->Keyword_model->import_produk($dataProduk);

							if ($row->getCellAtIndex(2) != "") {
								$keys = $row->getCellAtIndex(2);
								$ex_keys = explode(",", $keys);
								foreach ($ex_keys as $val_keys) {
									$dataDetail = array(	'id_keyword_produk'	=> $row->getCellAtIndex(0),
															'keys_produk'		=> trim($val_keys)
									);

									$this->Keyword_model->import_detail_produk($dataDetail);	
								}
							}
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/keyword/produk');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

	// function produk_export() {
	// 	$data['title']	= "Export Data Keyword Produk_".date("Y_m_d");
	// 	$data['produk']	= $this->Keyword_model->get_all_detail_produk_produk();

	// 	$this->load->view('back/keyword/produk_export', $data);
	// }

	function export_produk()
	{
		$data['title']	= "Export Data Keyword Produk_".date("Y_m_d");
		$data['produk']	= $this->Keyword_model->get_all_detail_produk_produk();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_keyword_produk');
		$sheet->setCellValue('B1', 'id_produk');
		$sheet->setCellValue('C1', 'keys_produk');

        // set Row
        $rowCount = 2;
        foreach ($data['produk'] as $list) {
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

	        $cek_detail_produk = $this->Keyword_model->get_keys_produk_by_id_produk($list->id_keyword_produk);
    		$keys = '';
		    if($cek_detail_produk == NULL)
		    {
		      $keys = '';
		    }
		    else
		    {
		      $i = 0;
		      $total = count($cek_detail_produk) - 1;
		      foreach($cek_detail_produk as $val_detail)
		      {
		        if ($i == $total) {
		          $keys .= $val_detail->keys_produk;
		        }else{
		          $keys .= $val_detail->keys_produk.",";
		        }

		        $i++;
		      }
		    }

            $sheet->SetCellValue('A' . $rowCount, $list->id_keyword_produk);
            $sheet->SetCellValue('B' . $rowCount, $list->id_produk);
            $sheet->SetCellValue('C' . $rowCount, $keys);

           
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

	// Toko

	function dasbor_list_toko_count(){
		$toko = $this->Keyword_model->total_rows_toko();
		$keyword = $this->Keyword_model->total_rows_detail_toko();
    	if (isset($toko)) {	
        	$msg = array(	'toko'	=> $toko,
        					'keyword'	=> $keyword
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'toko'	=> 0,
			    			'keyword'	=> 0,
        			);
        	echo json_encode($msg); 
    		// $msg = array(	'validasi'	=> validation_errors()
      //   			);
      //   	echo json_encode($msg);
    	}
    }


    function get_data_toko()
    {
        $list = $this->Keyword_model->get_datatables_toko();
        $dataJSON = array();
        foreach ($list as $data) {
   			// Detail Toko
   			$action = '<a href="'.base_url('admin/keyword/toko_ubah/'.$data->id_keyword_toko).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/keyword/toko_hapus/'.$data->id_keyword_toko).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->id_keyword_toko.'">';
			$get_detail_toko = $this->Keyword_model->get_detail_toko_by_id_toko($data->id_keyword_toko);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table table-bordered table-striped" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td><b>Keyword Toko</b></td>'.
			            '</tr>';

			if($get_detail_toko == NULL)
	        {
	          $detail .= '<tr align="center">'.
			                '<td><a href="#" class="btn btn-sm btn-danger">No Data</a></td>'.
			            '</tr>';
	        }
	        else
	        {
	        	$detail .= '<tr align="center">';
		        $detail .=  '<td>';
	          foreach ($get_detail_toko as $val_detail) {
		            $string = chunk_split($val_detail->keys_toko, 255, "</a> ");
		            $detail .=  '<a href="#" class="btn btn-sm btn-primary">'.$string;
				}
				 $detail .=  '</td>'.
				            '</tr>';
	        }

            $row = array();
            $row['toko'] = $data->nama_toko;
            $row['action'] = $action;
            $row['detail'] = $detail;
            $row['select'] = $select;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Keyword_model->count_all_toko(),
            "recordsFiltered" => $this->Keyword_model->count_filtered_toko(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function toko()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module_toko'].' List';
	    $this->data['action_impor']  = 'admin/keyword/proses_toko_impor';

	    $this->data['get_all'] = $this->Keyword_model->get_all_toko();

	    $this->load->view('back/keyword/toko_list', $this->data);
	}

	public function toko_tambah()
	{
		is_create();    

		$this->data['page_title'] = 'Create New '.$this->data['module_toko'];
	    $this->data['action']     = 'admin/keyword/toko_tambah_proses';
	    $data_exist_toko = $this->Keyword_model->get_all_toko();

	    if (count($data_exist_toko) > 0) {
	    	$exist_toko = array();
		    foreach ($data_exist_toko as $val_exist) {
		    	$exist_toko[] = $val_exist->id_toko;
		    	
		    }

		    $this->data['get_all_toko'] = $this->Toko_model->get_all_combobox_where_not_in($exist_toko);	
	    }else{
		    $this->data['get_all_toko'] = $this->Toko_model->get_all_combobox();
	    }

	    $this->data['toko_nama'] = [
	      'name'          => 'nama_toko',
	      'id'            => 'nama-toko',
	      'class'         => 'form-control select2bs4',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['keys_toko'] = [
	      'name'          => 'keys_toko',
	      'id'            => 'keys-toko',
	      'class'	  	  => 'form-control',
	      'style'		  => 'width:100%'
	    ];

	    $this->load->view('back/keyword/toko_add', $this->data);
	}

	public function toko_tambah_proses() {
		$toko = $this->input->post('toko');
		$keys = $this->input->post('keys');
		$ex_keys = explode(",", $keys);
		
		$data_toko = array(	'id_toko'	=> $toko,
		);

		$this->Keyword_model->insert_toko($data_toko);

		write_log();

		$get_last_toko = $this->Keyword_model->get_toko_last();
		foreach ($ex_keys as $val_keys) {
			$cek_keys_toko = $this->Keyword_model->get_keys_toko_by_keys_toko_not_in($get_last_toko->id_keyword_toko, $val_keys);

			if (count($cek_keys_toko) == 0) {
				$data_detail_toko = array(	'id_keyword_toko'	=> $get_last_toko->id_keyword_toko,
												'keys_toko'		=> $val_keys
				);

				$this->Keyword_model->insert_detail_toko($data_detail_toko);

				write_log();	
			}
		}

		$pesan = "Berhasil ditambah!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function toko_ubah($id = '')
	{
		is_update();

	    $this->data['toko']    = $this->Keyword_model->get_toko_by_id($id);
	    $data_exist_toko = $this->Keyword_model->get_all_toko();

	    if (count($data_exist_toko) > 0) {
	    	$exist_toko = array();
		    foreach ($data_exist_toko as $val_exist) {
		    	$exist_toko[] = $val_exist->id_toko;
		    }


		    if (($key = array_search($this->data['toko']->id_toko, $exist_toko)) !== false) {
				unset($exist_toko[$key]);
			}

			if (count($exist_toko) > 0) {
				$this->data['get_all_toko'] = $this->Toko_model->get_all_combobox_where_not_in($exist_toko);
			}else{
				$this->data['get_all_toko'] = $this->Toko_model->get_all_combobox();
			}		    	
	    }else{
		    $this->data['get_all_toko'] = $this->Toko_model->get_all_combobox();
	    }

	    $this->data['detail_toko']  = $this->Keyword_model->get_detail_toko_by_id_toko($id);
	    
	    if($this->data['toko'])
	    {
	      if (count($this->data['detail_toko']) > 0) {
		    $this->data['arr_keys'] = array();
		    	foreach ($this->data['detail_toko'] as $val_toko) {
		    		$this->data['arr_keys'][] = $val_toko->keys_toko;
		    	}
	      }else{
		    	$this->data['arr_keys'] = '';
	      }
	      $this->data['page_title'] = 'Update Data '.$this->data['module_toko'];
	      $this->data['action']     = 'admin/keyword/toko_ubah_proses';

	      $this->data['id_keyword_toko'] = [
	        'name'          => 'id_keyword_toko',
	        'id'			=> 'id-keyword-toko',
	        'type'          => 'hidden',
	      ];

	      $this->data['toko_nama'] = [
	      'name'          => 'nama_toko',
	      'id'            => 'nama-toko',
	      'class'         => 'form-control select2bs4',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['keys_toko'] = [
	      'name'          => 'keys_toko',
	      'id'            => 'keys-toko',
	      'class'	  	  => 'form-control',
	      'style'		  => 'width:100%'
	    ];

	      $this->load->view('back/keyword/toko_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/keyword/toko');
	    }
	}	

	public function toko_ubah_proses() {
		$toko = $this->input->post('toko');
		$id = $this->input->post('id');
		$keys = $this->input->post('keys');
		$ex_keys = explode(",", $keys);
		
		$data_toko = array(	'id_toko'	=> $toko,
		);

		$this->Keyword_model->update_toko($id, $data_toko);

		write_log();

		$this->Keyword_model->delete_detail_toko_by_id_toko($id);

		foreach ($ex_keys as $val_keys) {
			$cek_keys_toko = $this->Keyword_model->get_keys_toko_by_keys_toko_not_in($id, $val_keys);

			if (count($cek_keys_toko) == 0) {
				$data_detail_toko = array(	'id_keyword_toko'	=> $id,
											'keys_toko'			=> $val_keys
				);

				$this->Keyword_model->insert_detail_toko($data_detail_toko);

				write_log();	
			}
		}

		$pesan = "Berhasil diubah!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function toko_hapus($id = '')
	{
		is_delete();

		$toko  			= $this->Keyword_model->get_toko_by_id($id);
	    
	    if($toko)
		{
		  $detail_toko 	= $this->Keyword_model->get_detail_toko_by_id_toko($id);
		  if (count($detail_toko) > 0) {
		  	$this->Keyword_model->delete_detail_toko_by_id_toko($id);	
		  }

		  $this->Keyword_model->delete_toko($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/keyword/toko');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/keyword/toko');
		}
	}

	function toko_hapus_dipilih()
	{
		is_delete();

		$toko = $this->input->post('ids');

		$this->Keyword_model->delete_detail_toko_in_by_id_toko($toko);

		$this->Keyword_model->delete_toko_in($toko);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function proses_toko_impor()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc'.time();	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('import')) {
			$this->Keyword_model->deleteAll_detail_kotkab();
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/'.$file['file_name']);
			$numSheet 	= 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 1) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow == 1) {
							if ($row->getCellAtIndex(0) != 'ID' || $row->getCellAtIndex(1) != 'ID TOKO' || $row->getCellAtIndex(2) != 'KEYWORD') {
								$reader->close();
								unlink('uploads/'.$file['file_name']);
								$this->session->set_flashdata('message', '<div class="alert alert-danger">Import data does not match!</div>');
								redirect('admin/keyword/toko');
							}
						}

						if ($numRow > 1) {
							$dataToko 	= array(	'id_keyword_toko'	=> $row->getCellAtIndex(0),
														'id_toko'			=> $row->getCellAtIndex(1),
							);

							$this->Keyword_model->import_toko($dataToko);

							if ($row->getCellAtIndex(2) != "") {
								$keys = $row->getCellAtIndex(2);
								$ex_keys = explode(",", $keys);
								foreach ($ex_keys as $val_keys) {
									$dataDetail = array(	'id_keyword_toko'	=> $row->getCellAtIndex(0),
															'keys_toko'		=> trim($val_keys)
									);

									$this->Keyword_model->import_detail_toko($dataDetail);	
								}
							}
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/keyword/toko');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

	// function toko_export() {
	// 	$data['title']	= "Export Data Keyword Toko_".date("Y_m_d");
	// 	$data['toko']	= $this->Keyword_model->get_all_detail_toko_toko();

	// 	$this->load->view('back/keyword/toko_export', $data);
	// }

	function export_toko()
	{
		$data['title']	= "Export Data Keyword Toko_".date("Y_m_d");
		$data['toko']	= $this->Keyword_model->get_all_detail_toko_toko();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_keyword_toko');
		$sheet->setCellValue('B1', 'id_toko');
		$sheet->setCellValue('C1', 'keys_toko');

        // set Row
        $rowCount = 2;
        foreach ($data['toko'] as $list) {
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

	        $cek_detail_toko = $this->Keyword_model->get_keys_toko_by_id_toko($list->id_keyword_toko);
    		$keys = '';
		    if($cek_detail_toko == NULL)
		    {
		      $keys = '';
		    }
		    else
		    {
		      $i = 0;
		      $total = count($cek_detail_toko) - 1;
		      foreach($cek_detail_toko as $val_detail)
		      {
		        if ($i == $total) {
		          $keys .= $val_detail->keys_toko;
		        }else{
		          $keys .= $val_detail->keys_toko.",";
		        }

		        $i++;
		      }
		    }

            $sheet->SetCellValue('A' . $rowCount, $list->id_keyword_toko);
            $sheet->SetCellValue('B' . $rowCount, $list->id_toko);
            $sheet->SetCellValue('C' . $rowCount, $keys);

           
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

	// Kurir

	function dasbor_list_kurir_count(){
		$kurir = $this->Keyword_model->total_rows_kurir();
		$keyword = $this->Keyword_model->total_rows_detail_kurir();
    	if (isset($kurir)) {	
        	$msg = array(	'kurir'	=> $kurir,
        					'keyword'	=> $keyword
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'kurir'	=> 0,
			    			'keyword'	=> 0,
        			);
        	echo json_encode($msg); 
    		// $msg = array(	'validasi'	=> validation_errors()
      //   			);
      //   	echo json_encode($msg);
    	}
    }


    function get_data_kurir()
    {
        $list = $this->Keyword_model->get_datatables_kurir();
        $dataJSON = array();
        foreach ($list as $data) {
   			// Detail Kurir
   			$action = '<a href="'.base_url('admin/keyword/kurir_ubah/'.$data->id_keyword_kurir).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/keyword/kurir_hapus/'.$data->id_keyword_kurir).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->id_keyword_kurir.'">';
			$get_detail_kurir = $this->Keyword_model->get_detail_kurir_by_id_kurir($data->id_keyword_kurir);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table table-bordered table-striped" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td><b>Keyword Kurir</b></td>'.
			            '</tr>';

			if($get_detail_kurir == NULL)
	        {
	          $detail .= '<tr align="center">'.
			                '<td><a href="#" class="btn btn-sm btn-danger">No Data</a></td>'.
			            '</tr>';
	        }
	        else
	        {
	        	$detail .= '<tr align="center">';
		        $detail .=  '<td>';
	          foreach ($get_detail_kurir as $val_detail) {
		            $string = chunk_split($val_detail->keys_kurir, 255, "</a> ");
		            $detail .=  '<a href="#" class="btn btn-sm btn-primary">'.$string;
				}
				 $detail .=  '</td>'.
				            '</tr>';
	        }

            $row = array();
            $row['kurir'] = $data->nama_kurir;
            $row['action'] = $action;
            $row['detail'] = $detail;
            $row['select'] = $select;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Keyword_model->count_all_kurir(),
            "recordsFiltered" => $this->Keyword_model->count_filtered_kurir(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function kurir()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module_kurir'].' List';
	    $this->data['action_impor']  = 'admin/keyword/proses_kurir_impor';

	    $this->data['get_all'] = $this->Keyword_model->get_all_kurir();

	    $this->load->view('back/keyword/kurir_list', $this->data);
	}

	public function kurir_tambah()
	{
		is_create();    

		$this->data['page_title'] = 'Create New '.$this->data['module_kurir'];
	    $this->data['action']     = 'admin/keyword/kurir_tambah_proses';
	    $data_exist_kurir = $this->Keyword_model->get_all_kurir();

	    if (count($data_exist_kurir) > 0) {
	    	$exist_kurir = array();
		    foreach ($data_exist_kurir as $val_exist) {
		    	$exist_kurir[] = $val_exist->id_kurir;
		    	
		    }

		    $this->data['get_all_kurir'] = $this->Kurir_model->get_all_combobox_where_not_in($exist_kurir);	
	    }else{
		    $this->data['get_all_kurir'] = $this->Kurir_model->get_all_combobox();
	    }

	    $this->data['kurir_nama'] = [
	      'name'          => 'nama_kurir',
	      'id'            => 'nama-kurir',
	      'class'         => 'form-control select2bs4',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['keys_kurir'] = [
	      'name'          => 'keys_kurir',
	      'id'            => 'keys-kurir',
	      'class'	  	  => 'form-control',
	      'style'		  => 'width:100%'
	    ];

	    $this->load->view('back/keyword/kurir_add', $this->data);
	}

	public function kurir_tambah_proses() {
		$kurir = $this->input->post('kurir');
		$keys = $this->input->post('keys');
		$ex_keys = explode(",", $keys);
		
		$data_kurir = array(	'id_kurir'	=> $kurir,
		);

		$this->Keyword_model->insert_kurir($data_kurir);

		write_log();

		$get_last_kurir = $this->Keyword_model->get_kurir_last();
		foreach ($ex_keys as $val_keys) {
			$cek_keys_kurir = $this->Keyword_model->get_keys_kurir_by_keys_kurir_not_in($get_last_kurir->id_keyword_kurir, $val_keys);

			if (count($cek_keys_kurir) == 0) {
				$data_detail_kurir = array(	'id_keyword_kurir'	=> $get_last_kurir->id_keyword_kurir,
												'keys_kurir'		=> $val_keys
				);

				$this->Keyword_model->insert_detail_kurir($data_detail_kurir);

				write_log();	
			}
		}

		$pesan = "Berhasil ditambah!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function kurir_ubah($id = '')
	{
		is_update();

	    $this->data['kurir']    = $this->Keyword_model->get_kurir_by_id($id);
	    $data_exist_kurir = $this->Keyword_model->get_all_kurir();

	    if (count($data_exist_kurir) > 0) {
	    	$exist_kurir = array();
		    foreach ($data_exist_kurir as $val_exist) {
		    	$exist_kurir[] = $val_exist->id_kurir;
		    }


		    if (($key = array_search($this->data['kurir']->id_kurir, $exist_kurir)) !== false) {
				unset($exist_kurir[$key]);
			}

			if (count($exist_kurir) > 0) {
				$this->data['get_all_kurir'] = $this->Kurir_model->get_all_combobox_where_not_in($exist_kurir);
			}else{
				$this->data['get_all_kurir'] = $this->Kurir_model->get_all_combobox();
			}		    	
	    }else{
		    $this->data['get_all_kurir'] = $this->Kurir_model->get_all_combobox();
	    }

	    $this->data['detail_kurir']  = $this->Keyword_model->get_detail_kurir_by_id_kurir($id);
	    
	    if($this->data['kurir'])
	    {
	      if (count($this->data['detail_kurir']) > 0) {
		    $this->data['arr_keys'] = array();
		    	foreach ($this->data['detail_kurir'] as $val_kurir) {
		    		$this->data['arr_keys'][] = $val_kurir->keys_kurir;
		    	}
	      }else{
		    	$this->data['arr_keys'] = '';
	      }
	      $this->data['page_title'] = 'Update Data '.$this->data['module_kurir'];
	      $this->data['action']     = 'admin/keyword/kurir_ubah_proses';

	      $this->data['id_keyword_kurir'] = [
	        'name'          => 'id_keyword_kurir',
	        'id'			=> 'id-keyword-kurir',
	        'type'          => 'hidden',
	      ];

	      $this->data['kurir_nama'] = [
	      'name'          => 'nama_kurir',
	      'id'            => 'nama-kurir',
	      'class'         => 'form-control select2bs4',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['keys_kurir'] = [
	      'name'          => 'keys_kurir',
	      'id'            => 'keys-kurir',
	      'class'	  	  => 'form-control',
	      'style'		  => 'width:100%'
	    ];

	      $this->load->view('back/keyword/kurir_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/keyword/kurir');
	    }
	}	

	public function kurir_ubah_proses() {
		$kurir = $this->input->post('kurir');
		$id = $this->input->post('id');
		$keys = $this->input->post('keys');
		$ex_keys = explode(",", $keys);
		
		$data_kurir = array(	'id_kurir'	=> $kurir,
		);

		$this->Keyword_model->update_kurir($id, $data_kurir);

		write_log();

		$this->Keyword_model->delete_detail_kurir_by_id_kurir($id);

		foreach ($ex_keys as $val_keys) {
			$cek_keys_kurir = $this->Keyword_model->get_keys_kurir_by_keys_kurir_not_in($id, $val_keys);

			if (count($cek_keys_kurir) == 0) {
				$data_detail_kurir = array(	'id_keyword_kurir'	=> $id,
											'keys_kurir'			=> $val_keys
				);

				$this->Keyword_model->insert_detail_kurir($data_detail_kurir);

				write_log();	
			}
		}

		$pesan = "Berhasil diubah!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function kurir_hapus($id = '')
	{
		is_delete();

		$kurir  			= $this->Keyword_model->get_kurir_by_id($id);
	    
	    if($kurir)
		{
		  $detail_kurir 	= $this->Keyword_model->get_detail_kurir_by_id_kurir($id);
		  if (count($detail_kurir) > 0) {
		  	$this->Keyword_model->delete_detail_kurir_by_id_kurir($id);	
		  }

		  $this->Keyword_model->delete_kurir($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/keyword/kurir');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/keyword/kurir');
		}
	}

	function kurir_hapus_dipilih()
	{
		is_delete();

		$kurir = $this->input->post('ids');

		$this->Keyword_model->delete_detail_kurir_in_by_id_kurir($kurir);

		$this->Keyword_model->delete_kurir_in($kurir);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function proses_kurir_impor()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc'.time();	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('import')) {
			$this->Keyword_model->deleteAll_detail_kotkab();
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/'.$file['file_name']);
			$numSheet 	= 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 1) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow == 1) {
							if ($row->getCellAtIndex(0) != 'ID' || $row->getCellAtIndex(1) != 'ID KURIR' || $row->getCellAtIndex(2) != 'KEYWORD') {
								$reader->close();
								unlink('uploads/'.$file['file_name']);
								$this->session->set_flashdata('message', '<div class="alert alert-danger">Import data does not match!</div>');
								redirect('admin/keyword/kurir');
							}
						}

						if ($numRow > 1) {
							$dataKurir 	= array(	'id_keyword_kurir'	=> $row->getCellAtIndex(0),
														'id_kurir'			=> $row->getCellAtIndex(1),
							);

							$this->Keyword_model->import_kurir($dataKurir);

							if ($row->getCellAtIndex(2) != "") {
								$keys = $row->getCellAtIndex(2);
								$ex_keys = explode(",", $keys);
								foreach ($ex_keys as $val_keys) {
									$dataDetail = array(	'id_keyword_kurir'	=> $row->getCellAtIndex(0),
															'keys_kurir'		=> trim($val_keys)
									);

									$this->Keyword_model->import_detail_kurir($dataDetail);	
								}
							}
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/keyword/kurir');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

	// function kurir_export() {
	// 	$data['title']	= "Export Data Keyword Kurir_".date("Y_m_d");
	// 	$data['kurir']	= $this->Keyword_model->get_all_detail_kurir_kurir();

	// 	$this->load->view('back/keyword/kurir_export', $data);
	// }

	function export_kurir()
	{
		$data['title']	= "Export Data Keyword Kurir_".date("Y_m_d");
		$data['kurir']	= $this->Keyword_model->get_all_detail_kurir_kurir();

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'id_keyword_kurir');
		$sheet->setCellValue('B1', 'id_kurir');
		$sheet->setCellValue('C1', 'keys_kurir');

        // set Row
        $rowCount = 2;
        foreach ($data['kurir'] as $list) {
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

	        $cek_detail_kurir = $this->Keyword_model->get_keys_kurir_by_id_kurir($list->id_keyword_kurir);
    		$keys = '';
		    if($cek_detail_kurir == NULL)
		    {
		      $keys = '';
		    }
		    else
		    {
		      $i = 0;
		      $total = count($cek_detail_kurir) - 1;
		      foreach($cek_detail_kurir as $val_detail)
		      {
		        if ($i == $total) {
		          $keys .= $val_detail->keys_kurir;
		        }else{
		          $keys .= $val_detail->keys_kurir.",";
		        }

		        $i++;
		      }
		    }

            $sheet->SetCellValue('A' . $rowCount, $list->id_keyword_kurir);
            $sheet->SetCellValue('B' . $rowCount, $list->id_kurir);
            $sheet->SetCellValue('C' . $rowCount, $keys);

           
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
	
	function ubah_status($id) {
		$keyword = $this->Keyword_model->get_produk_by_id($id);
		$data = array(
			'status' => $keyword->status == 1 ? 0 : 1
		);
		$this->Keyword_model->update_produk($id, $data);
		$this->session->set_flashdata('message', '<div class="alert alert-success">Status berhasil diubah</div>');
		redirect('admin/keyword/produk');
	}
}

/* End of file Keyword.php */
/* Location: ./application/controllers/admin/Keyword.php */