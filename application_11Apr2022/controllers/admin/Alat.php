<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// PDF to Image
use Spatie\PdfToImage\Pdf;
use Org_Heigl\Ghostscript\Ghostscript;

// OCR Image to Text
use thiagoalessio\TesseractOCR\TesseractOCR;

// Include librari PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Alat extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->data['module'] = 'Alat';

	    $this->load->model(array());

	    $this->data['company_data']    		= $this->Company_model->company_profile();
			$this->data['layout_template']  = $this->Template_model->layout();
	    $this->data['skins_template']     	= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_restore'] = 'Restore Database';
	    $this->data['btn_export']    = 'Export Data';
		$this->data['btn_backup']    = 'Backup Database';
		$this->data['backup_db_action'] = base_url('admin/alat/backup_db');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	// DATABASE

	public function database()
	{
		is_create();

		$this->data['page_title'] = $this->data['module'].' Database';

	    $this->load->view('back/alat/database', $this->data);
	}

	public function backup_db()
	{
		date_default_timezone_set('Asia/Jakarta');
		// Load the DB utility class
		$this->load->dbutil();

		$format = "Backup_DB_".date("d_m_Y_H_i_s").".sql";

		$prefs = array(
		        // 'tables'     => array('table1', 'table2'),
		        // Array table yang akan dibackup
		        'ignore'     => array('changelog_query', 'changelog_app'),
		        // Daftar table yang tidak akan dibackup
		        'format'     => 'txt',
		        // gzip, zip, txt format filenya
		        'filename'   => $format,
		        // Nama file
		        'add_drop'   => TRUE, 
		        // Untuk menambahkan drop table di backup
		        'add_insert' => TRUE,
		        // Untuk menambahkan data insert di file backup
		        'newline'    => "\n"
		        // Baris baru yang digunakan dalam file backup
		);

		// $this->dbutil->backup($prefs);

		// Backup database dan dijadikan variable
		$backup = $this->dbutil->backup($prefs);

		// Load file helper dan menulis ke server untuk keperluan restore
		// $this->load->helper('file');
		// write_file('/backup/database/mybackup.gz', $backup);

		// Load the download helper dan melalukan download ke komputer
		$this->load->helper('download');
		force_download($format, $backup);
	}

	public function restore_db()
	{
		date_default_timezone_set('Asia/Jakarta');
		$config['upload_path'] 		= './uploads/impor_db/';
		$config['allowed_types'] 	= 'sql';
		$config['file_name']		= "Restore_DB_".date("d_m_Y_H_i_s").".sql";;	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('restore_db')) {
			$file = $this->upload->data();
			// echo print_r($file);
			$sql_contents = file_get_contents($file['file_path'].$file['file_name']);
		    $string_query=rtrim($sql_contents, "\n;" );
			$array_query=explode(";", $string_query);

			// echo print_r($array_query);
			foreach($array_query as $query){
				$this->db->query($query);
			}

		    unlink($file['file_path'].$file['file_name']);
			// $this->session->set_flashdata('message', '<div class="alert alert-success">Restored database successfully</div>')
			// redirect('admin/keluar/penjualan_produk');

			$msg = array(	'sukses'	=> 'Berhasil Restore Database!',
	    			);
	    	echo json_encode($msg);
			
		}else{
			// $error = array('error' => $this->upload->display_errors());
			// $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>')
			
			$msg = array(	'validasi'	=> 'Terjadi Kesalahan!',
	    			);
	    	echo json_encode($msg);
			// redirect('admin/keluar/penjualan_produk');
			// echo $this->upload->display_errors();
		}
	}

	// OCR

	public function ocr()
	{
		is_create();

		$this->data['page_title'] = $this->data['module'].' OCR';

	    $this->load->view('back/alat/ocr', $this->data);
	}

	public function proses_ocr()
	{
		// [0] => Array
  //       (
  //           [file_name] => Gambar_OCR_2022_03_23_1648009766.jpg
  //           [file_type] => image/jpeg
  //           [file_path] => C:/xampp74/htdocs/hadi/uploads/gambar_kasus/
  //           [full_path] => C:/xampp74/htdocs/hadi/uploads/gambar_kasus/Gambar_OCR_2022_03_23_1648009766.jpg
  //           [raw_name] => Gambar_OCR_2022_03_23_1648009766
  //           [orig_name] => Gambar_OCR_2022_03_23_1648009766.jpg
  //           [client_name] => BR TIKTOK 7_page-0010.jpg
  //           [file_ext] => .jpg
  //           [file_size] => 203.08
  //           [is_image] => 1
  //           [image_width] => 619
  //           [image_height] => 875
  //           [image_type] => jpeg
  //           [image_size_str] => width="619" height="875"
  //       )

		$this->load->library('upload');
	    $dataInfo = array();
	    $dataOCR = array();
	    $pecahanOCR = array();
	    $validasiOCR = array();
	    $store_nama_hp = array();
	    $fixOCR = array();
	    $config = array();
	    $files = $_FILES;
	    $nama = '';
	    $hp = '';
	    $resi = '';
	    $indeks = 0;
	    if(!empty($_FILES)){
	    	if (count($_FILES['photo']['name']) <= 50) {
	    		foreach($_FILES['photo']['name'] as $value){
	    	        $_FILES['photo']['name']= $files['photo']['name'][$indeks];
	    	        $_FILES['photo']['type']= $files['photo']['type'][$indeks];
	    	        $_FILES['photo']['tmp_name']= $files['photo']['tmp_name'][$indeks];
	    	        $_FILES['photo']['error']= $files['photo']['error'][$indeks];
	    	        $_FILES['photo']['size']= $files['photo']['size'][$indeks]; 
	    	        
	        	    $config['upload_path']          = './uploads/ocr/';
	        		$config['allowed_types']        = 'jpg|png|jpeg';
	        		$config['file_name']			= 'Gambar_OCR_'.date('Y_m_d_').time();
	        		$config['max_size']             = 2000;
	        		
	    	        $this->upload->initialize($config);
	                $this->upload->do_upload('photo');
	                $dataInfo[] = $this->upload->data();
	                
	                $indeks++;
	    	    }
	    	    
	    	    foreach($dataInfo as $val_dataInfo){
	    	        // Mendeteksi OS yang digunakan
	                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	        		    $ocr = new TesseractOCR(base_url('uploads/ocr/'.$val_dataInfo['file_name']));
	        	        $ocr->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
	        	        $ocr->lang('eng');
	        			$content = $ocr->run();
	        			$dataOCR[] = $content;
	        		} else {
	        		    $ocr = new TesseractOCR('/home/brighty/public_html/uploads/ocr/'.$val_dataInfo['file_name']);
	        	        $ocr->lang('eng');
	        			$content = $ocr->run();
	        			$dataOCR[] = $content;
	        		}
	        
	        		// Hapus Gambar Apabila sudah di CONVERT ke TEXT
	        		$dir        = "./uploads/ocr/".$val_dataInfo['file_name'];
	        
	                if(is_file($dir))
	                {
	                  unlink($dir);
	                }
	    	    }
	    		
	    		$i = 0;
	    	    foreach ($dataOCR as $valOCR) {

	    	    	$pecahanOCR[] = preg_split('/\r\n|\r|\n/', $valOCR);

	            	foreach ($pecahanOCR as $key => $valPecahan) {
	            		foreach ($valPecahan as $keyVal => $val) {
	            			if (substr($valPecahan[$keyVal], 0, 8) === "Penerima") {
	            				$validasiOCR[$i]['penerima'] = $val;
	            			}

	            			if (substr($valPecahan[$keyVal], 0, 2) === "JX" || substr($valPecahan[$keyVal], 0, 2) === "Jx" || substr($valPecahan[$keyVal], 0, 2) === "jX" || substr($valPecahan[$keyVal], 0, 2) === "jx") {
	            				$validasiOCR[$i]['resi'] = $val;
	            			}
	            		}
	            	}

	            	$i++;
	            }
	            
	            // echo print_r($validasiOCR)."<br>";
	        // ===================== CARA AGAR BER ARRAY ===================
	        //     for ($main = 0; $main < count($pecahanOCR) ; $main++) {
	        //     	for ($child = 0; $child < count($pecahanOCR[$main]); $child++) {
	        //     		if (substr($pecahanOCR[$main][$child], 0, 8) === "Penerima") {
	        //     			// MENGAMBIL NILAI PENERIMA DAN NOMOR HP
	    				// 	// echo str_replace(array("Penerima : "), "", $pecahanOCR[$main][$child])."<br>";
	    				// 	$store_nama_hp[$main] = explode(",", str_replace(array("Penerima : "), "", $pecahanOCR[$main][$child]));
	    
	    				// 	$fixOCR[$main] = array( 'nama' => $store_nama_hp[$main][0],
	    				// 							'hp'	=> str_replace(" ", "", $store_nama_hp[$main][1])
	    				// 	);
	    				// }	
	    
	    				// if (substr($pecahanOCR[$main][$child], 0, 2) === "JX" || substr($pecahanOCR[$main][$child], 0, 2) === "Jx" || substr($pecahanOCR[$main][$child], 0, 2) === "jX" || substr($pecahanOCR[$main][$child], 0, 2) === "jx") {
	    				// 	// echo strtoupper(str_replace(array(" ", ",", ","), "", $pecahanOCR[$main][$child]))."<br>";
	    				// 	$fixOCR[$main]['resi'] = strtoupper(str_replace(array(" ", ",", ","), "", $pecahanOCR[$main][$child]));
	    				// }
	        //     	}
	        //     }
	    
	    
	        // ==================== CARA AGAR TIDAK BER ARRAY ===================
	        //     for ($main = 0; $main < count($pecahanOCR) ; $main++) {
	        //     	for ($child = 0; $child < count($pecahanOCR[$main]); $child++) {
	        //     		if (substr($pecahanOCR[$main][$child], 0, 8) === "Penerima") {
	        //     			// MENGAMBIL NILAI PENERIMA DAN NOMOR HP
	    				// 	// echo str_replace(array("Penerima : "), "", $pecahanOCR[$main][$child])."<br>";
	    				// 	$store_nama_hp[$main] = explode(",", str_replace(array("Penerima : ", "Penerima :"), "", $pecahanOCR[$main][$child]));
	    					
	    				// 	if (count($store_nama_hp[$main]) > 0) {
		    			// 		if ($main == count($pecahanOCR) - 1) {
		    			// 			$nama .= $store_nama_hp[$main][0];
		    			// 			$hp .= str_replace(array("(", "+", ")"), "", $store_nama_hp[$main][1]);
		    			// 		}else{
		    			// 			$nama .= $store_nama_hp[$main][0].",";
		    			// 			$hp .= str_replace(array("(", "+", ")"), "", $store_nama_hp[$main][1]).",";
		    			// 		}
		    			// 		// $fixOCR[$main] = array( 'nama' => $store_nama_hp[$main][0],
		    			// 		// 						'hp'	=> str_replace(" ", "", $store_nama_hp[$main][1])
		    			// 		// );

		    			// 		if (substr($pecahanOCR[$main][$child], 0, 2) === "JX" || substr($pecahanOCR[$main][$child], 0, 2) === "Jx" || substr($pecahanOCR[$main][$child], 0, 2) === "jX" || substr($pecahanOCR[$main][$child], 0, 2) === "jx") {
			    		// 			// echo strtoupper(str_replace(array(" ", ",", ","), "", $pecahanOCR[$main][$child]))."<br>";
			    		// 			// $fixOCR[$main]['resi'] = strtoupper(str_replace(array(" ", ",", ","), "", $pecahanOCR[$main][$child]));
			    		// 			if ($main == count($pecahanOCR) - 1) {
			    		// 				$resi .= strtoupper(str_replace(array(" ", ",", "."), "", $pecahanOCR[$main][$child]));
			    		// 			}else{
			    		// 				$resi .= strtoupper(str_replace(array(" ", ",", "."), "", $pecahanOCR[$main][$child])).",";
			    		// 			}
			    		// 		}	
	    				// 	}
	    				// }
	        //     	}
	        //     }

	        // ==================== CARA BARU AGAR TIDAK BER ARRAY ===================
	            for ($i = 0; $i < count($validasiOCR); $i++) {
	            	$store_nama_hp[$i] = explode(",", str_replace(array("Penerima : ", "Penerima :"), "", $validasiOCR[$i]['penerima']));
	            	if (count($store_nama_hp[$i]) > 0  && count($store_nama_hp[$i]) == 2) {
						if ($i == count($validasiOCR) - 1) {
							if ($store_nama_hp[$i][0] != NULL && $store_nama_hp[$i][1] != NULL  && $validasiOCR[$i]['resi'] != NULL) {
								$nama .= str_replace(array("'"), " ", $store_nama_hp[$i][0]);
								$hp .= str_replace(array("(", "+", ")"), "", $store_nama_hp[$i][1]);
								$resi .= strtoupper(str_replace(array(" ", ",", "."), "", $validasiOCR[$i]['resi']));	
							}
						}else{
							if ($store_nama_hp[$i][0] != NULL && $store_nama_hp[$i][1] != NULL  && $validasiOCR[$i]['resi'] != NULL) {
								$nama .= str_replace(array("'"), " ", $store_nama_hp[$i][0]).",";
								$hp .= str_replace(array("(", "+", ")", "#"), "", $store_nama_hp[$i][1]).",";
								$resi .= strtoupper(str_replace(array(" ", ",", "."), "", $validasiOCR[$i]['resi'])).",";	
							}
						}	
					}
	            }

	            // echo print_r($store_nama_hp);

	    	   // echo $nama."<br>";
	    	   // echo $hp."<br>";
	    	   // echo $resi."<br>";
	    
	    	    $msg  = array( 	'sukses'	=> 'Gambar berhasil di Convert ke Teks!',
	    	    				'nama'		=> $nama,
	    	    				'hp'		=> $hp,
	    	    				'resi'		=> $resi,
	    	    				'filter'	=> $this->input->post('filter')
	    		);
	    
	        	echo json_encode($msg);	
	    	}else{
	    		$msg  = array( 	'validasi'	=> 'Batas Gambar yang bisa diconvert hanya 50!'
	    		);
	    
	        	echo json_encode($msg);	
	    	}   
	    }
	}

	public function export_data_ocr()
	{
		$storeExport = array();
		// $nama = explode(",", str_replace("%20", " ", $nama));
		// $hp = explode(",", str_replace("%20", " ", $hp));
		// $resi = explode(",", $resi);
		$filter = $this->uri->segment(4);
		$nama = explode(",", str_replace("%20", " ", $this->uri->segment(5)));
		$hp = explode(",", str_replace("%20", " ", $this->uri->segment(6)));
		$resi = explode(",", $this->uri->segment(7));
		
		// echo print_r($nama)."<br>";
		// echo print_r($hp)."<br>";
		// echo print_r($resi)."<br>";

		if ($filter == 'no') {
			if (count($nama) == count($hp) && count($hp) == count($resi) && count($resi) == count($nama)) {
				for ($main = 0; $main < count($nama); $main++) {
					$storeExport[$main] = array(	'nama_penerima'	=> $nama[$main],
													'hp_penerima'	=> $hp[$main],
													'nomor_resi'	=> $resi[$main],
					);
				}

				$data['title']	= "Export Data Without Sync Database OCR_".date("H_i_s");			

				// PHPOffice

				$spreadsheet = new Spreadsheet();
				$sheet = $spreadsheet->getActiveSheet();

				$sheet->setCellValue('A1', 'nama_penerima');
				$sheet->setCellValue('B1', 'nomor_hp');
				$sheet->setCellValue('C1', 'no_resi');

				// set Row
		        $rowCount = 2;
				foreach ($storeExport as $val_store) {
					$sheet->SetCellValue('A' . $rowCount, $val_store['nama_penerima']);

					// Nomor HP
					if (is_numeric($val_store['hp_penerima'])) {
			          if (strlen($val_store['hp_penerima']) < 15) {
			            $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
			            $sheet->SetCellValue('B' . $rowCount, $val_store['hp_penerima']);
			          }else{
			            $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            $sheet->setCellValueExplicit('B' . $rowCount, $val_store['hp_penerima'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			          }
			        }else{
			          $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			          $sheet->SetCellValue('B' . $rowCount, $val_store['hp_penerima']);
			        }

					// Nomor Resi
		            if (is_numeric($val_store['nomor_resi'])) {
		              if (strlen($val_store->nomor_resi) < 15) {
			            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
			            $sheet->SetCellValue('C' . $rowCount, $val_store['nomor_resi']);
			          }else{
			            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('C' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('C' . $rowCount, $val_store['nomor_resi'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			          }
			        }else{
			          $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			          $sheet->SetCellValue('C' . $rowCount, $val_store['nomor_resi']);
			        }

			        $rowCount++;
				}

				// $writer = new Xlsx($spreadsheet);
				$extension = 'csv';
				if($extension == 'csv'){          
			      $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
			      $fileName = $data['title'].'.csv';
			    } elseif($extension == 'xlsx') {
			      $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			      $fileName = $data['title'].'.xlsx';
			    } else {
			      $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
			      $fileName = $data['title'].'.xls';
			    }
					
				header('Content-Type: application/vnd.ms-excel');
				header("Content-Transfer-Encoding: Binary"); 
				header('Content-Disposition: attachment;filename="'. $fileName .'"');
				header("Pragma: no-cache");
				header("Expires: 0");

				$writer->save('php://output');

				die();
			}
		}else if ($filter == 'yes') {
			if (count($nama) == count($hp) && count($hp) == count($resi) && count($resi) == count($nama)) {
				for ($main = 0; $main < count($nama); $main++) {
					$storeExport[$main] = array(	'nama_penerima'	=> $nama[$main],
													'hp_penerima'	=> $hp[$main],
													'nomor_resi'	=> $resi[$main],
					);
				}

				$data['title']	= "Export Data Sync Database OCR_".date("H_i_s");			

				// PHPOffice

				$spreadsheet = new Spreadsheet();
				$sheet = $spreadsheet->getActiveSheet();

				$sheet->setCellValue('A1', 'no_pesanan');
				$sheet->setCellValue('B1', 'tanggal');
				$sheet->setCellValue('C1', 'no_resi');
				$sheet->setCellValue('D1', 'kurir');
				$sheet->setCellValue('E1', 'ongkir');
				$sheet->setCellValue('F1', 'nama_penerima');
				$sheet->setCellValue('G1', 'alamat');
				$sheet->setCellValue('H1', 'provinsi');
				$sheet->setCellValue('I1', 'kota');
				$sheet->setCellValue('J1', 'hp_penerima');
				$sheet->setCellValue('K1', 'nama_produk');
				$sheet->setCellValue('L1', 'jumlah');
				$sheet->setCellValue('M1', 'total_harga');
				$sheet->setCellValue('N1', 'metode_pembayaran');
				$sheet->setCellValue('O1', 'status');
				$sheet->setCellValue('P1', 'total_jual');
				$sheet->setCellValue('Q1', 'sku');

		        // set Row
		        $rowCount = 2;
		        foreach ($storeExport as $val_store) {
		        	if ($val_store['nomor_resi'] != '' || $val_store['nomor_resi'] != NULL) {
		        		$data['export'] = $this->Keluar_model->get_all_detail_by_resi($val_store['nomor_resi']);	
		        	}
			       	
					foreach ($data['export'] as $list) {
						// Ubah Data Pesanan apabila Pesanan ditemukan berdasarkan Nomor Resi
						if ($val_store['nama_penerima'] != '' && $val_store['hp_penerima'] != '') {
							$ubahData = array(	'nama_penerima' => $val_store['nama_penerima'],
											'hp_penerima'	=> $val_store['hp_penerima'],
							);

							$this->Keluar_model->update($list->nomor_pesanan, $ubahData);

							write_log();	
						}

			        	// Nomor Pesanan
				        if (is_numeric($list->nomor_pesanan)) {

				          // See http://excelunplugged.com/2014/05/19/15-digit-limit-in-excel/
				          if (strlen($list->nomor_pesanan) < 15) {
				            $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
				            $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
				          }else{
				            $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
				            // The old way to force string. NumberFormat::FORMAT_TEXT is not
				            // enough.
				            // $formatted_value .= ' ';
				            // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
				            $sheet->setCellValueExplicit('A' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				          }
				        }else{
				          $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
				          $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
				        }
			            $sheet->SetCellValue('B' . $rowCount, date('d/m/Y', strtotime($list->tgl_penjualan)));

			            // Nomor Resi
			            if (is_numeric($val_store['nomor_resi'])) {

				          // See http://excelunplugged.com/2014/05/19/15-digit-limit-in-excel/
				          if (strlen($val_store->nomor_resi) < 15) {
				            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
				            $sheet->SetCellValue('C' . $rowCount, $val_store['nomor_resi']);
				          }else{
				            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
				            // The old way to force string. NumberFormat::FORMAT_TEXT is not
				            // enough.
				            // $formatted_value .= ' ';
				            // $sheet->SetCellValue('C' . $rowCount, "'".$formatted_value);
				            $sheet->setCellValueExplicit('C' . $rowCount, $val_store['nomor_resi'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				          }
				        }else{
				          $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
				          $sheet->SetCellValue('C' . $rowCount, $val_store['nomor_resi']);
				        }
			            // $sheet->SetCellValue('C' . $rowCount, $list->nomor_resi);

			            $sheet->SetCellValue('D' . $rowCount, $list->nama_kurir);
			            $sheet->SetCellValue('E' . $rowCount, $list->ongkir);
			            $sheet->SetCellValue('F' . $rowCount, $val_store['nama_penerima']);
			            $sheet->SetCellValue('G' . $rowCount, $list->alamat_penerima);
			            $sheet->SetCellValue('H' . $rowCount, $list->provinsi);
			            $sheet->SetCellValue('I' . $rowCount, $list->kabupaten);
			            // Nomor HP
			            if (is_numeric($val_store['hp_penerima'])) {

				          // See http://excelunplugged.com/2014/05/19/15-digit-limit-in-excel/
				          if (strlen($val_store['hp_penerima']) < 15) {
				            $sheet->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
				            $sheet->SetCellValue('J' . $rowCount, $val_store['hp_penerima']);
				          }else{
				            $sheet->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
				            // The old way to force string. NumberFormat::FORMAT_TEXT is not
				            // enough.
				            // $formatted_value .= ' ';
				            // $sheet->SetCellValue('I' . $rowCount, "'".$formatted_value);
				            $sheet->setCellValueExplicit('J' . $rowCount, $val_store['hp_penerima'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				          }
				        }else{
				          $sheet->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
				          $sheet->SetCellValue('J' . $rowCount, $val_store['hp_penerima']);
				        }

			            $sheet->SetCellValue('K' . $rowCount, $list->nama_produk);
			            $sheet->SetCellValue('L' . $rowCount, $list->qty);
			            $sheet->SetCellValue('M' . $rowCount, $list->harga);
			            $sheet->SetCellValue('N' . $rowCount, 'Transfer');
			            $sheet->SetCellValue('O' . $rowCount, 'Terkirim');
			            $sheet->SetCellValue('P' . $rowCount, $list->total_harga);
			            $sheet->SetCellValue('Q' . $rowCount, $list->sub_sku);
			            $rowCount++;
			        }
		        }

		        // $writer = new Xlsx($spreadsheet);
				$extension = 'xlsx';
				if($extension == 'csv'){          
			      $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
			      $fileName = $data['title'].'.csv';
			    } elseif($extension == 'xlsx') {
			      $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			      $fileName = $data['title'].'.xlsx';
			    } else {
			      $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
			      $fileName = $data['title'].'.xls';
			    }
					
				header('Content-Type: application/vnd.ms-excel');
				header("Content-Transfer-Encoding: Binary"); 
				header('Content-Disposition: attachment;filename="'. $fileName .'"');
				header("Pragma: no-cache");
				header("Expires: 0");

				$writer->save('php://output');

				die();
			}	
		}
	}

	private function set_upload_options()
	{   
	    //upload an image options
	    $config = array();
	    $config['upload_path']          = './uploads/ocr/';
		$config['allowed_types']        = 'jpg|png|jpeg';
		$config['file_name']			= 'Gambar_OCR_'.date('Y_m_d_').time();
		$config['max_size']             = 2000;

	    return $config;
	}

	// Convert PDF to Image

	public function convertpdf()
	{
		is_create();

		$this->data['page_title'] = $this->data['module'].' Convert PDF to Image';

	    $this->load->view('back/alat/convertpdf', $this->data);
	}

	public function proses_convertpdf()
	{
		// Ambil Data
		$i = $this->input;

		$config['upload_path']          = './uploads/pdf/';
		$config['allowed_types']        = 'pdf';
		$config['file_name']			= 'Convert_PDF_To_Image_'.date('Y_m_d_').time();
		$config['max_size']             = 10000;
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('pdf'))
		{
				$pesan = strip_tags($this->upload->display_errors());
				$msg = array(	'validasi'	=> $pesan
		    			);
		    	echo json_encode($msg);
		}else{
			// Upload Gambar
			date_default_timezone_set("Asia/Jakarta");
			$now = date('Y-m-d H:i:s');
			$pdf_data = $this->upload->data();

			$pdf = new Spatie\PdfToImage\Pdf($pdf_data['full_path']);
			$jumlah_page = $pdf->getNumberOfPages();
			$pdf->setOutputFormat('jpg')
			->setResolution(720)
			->saveAllPagesAsImages("./uploads/hasil_convertpdf/", "Hasil_".$pdf_data['raw_name']."_");

			// Simpan ke Array nama file PDF dan JPG
			$val_jpg = '';

			for ($i = 1; $i <= $jumlah_page; $i++) {
				if ($i == $jumlah_page) {
					$val_jpg .= "Hasil_".$pdf_data['raw_name']."_".$i.".jpg";
				}else{
					$val_jpg .= "Hasil_".$pdf_data['raw_name']."_".$i.".jpg,";
				}
			}

			// Hapus Gambar Apabila sudah di CONVERT ke TEXT
			$dir        = "./uploads/pdf/".$pdf_data['orig_name'];

	        if(is_file($dir))
	        {
	          unlink($dir);
	        }

			$msg  = array( 	'sukses'	=> 'PDF berhasil di Convert ke JPG!',
							'jpg'		=> $val_jpg,
							'pdf'		=> $pdf_data['raw_name']
    		);
    
        	echo json_encode($msg);  

			// echo print_r($store_convert);

			// HASILNYA
			// Array
			// (
			//     [file_name] => Convert_PDF_To_Image_2022_04_06_1649207760.pdf
			//     [file_type] => application/pdf
			//     [file_path] => C:/xampp74/htdocs/hadi/uploads/pdf/
			//     [full_path] => C:/xampp74/htdocs/hadi/uploads/pdf/Convert_PDF_To_Image_2022_04_06_1649207760.pdf
			//     [raw_name] => Convert_PDF_To_Image_2022_04_06_1649207760
			//     [orig_name] => Convert_PDF_To_Image_2022_04_06_1649207760.pdf
			//     [client_name] => BR TIKTOK 1.pdf
			//     [file_ext] => .pdf
			//     [file_size] => 6453.58
			//     [is_image] => 
			//     [image_width] => 
			//     [image_height] => 
			//     [image_type] => 
			//     [image_size_str] => 
			// )
		}
	}

	public function compress_convertpdf($pdf, $jpg)
	{
		$ex_jpg = explode(",", $jpg);

		$zip = new ZipArchive();
			$zip_name = "Kompres_".$pdf.".zip"; // Zip name
			$zip->open($zip_name,  ZipArchive::CREATE);
			foreach ($ex_jpg as $val_jpg) {
				$path = "./uploads/hasil_convertpdf/".$val_jpg;
				if(file_exists($path)){
					$zip->addFromString(basename($path), file_get_contents($path));
					unlink($path);
				}
				else{
					echo"File does not exist";
				}	
			}
			$zip->close();
	        header('Content-disposition: attachment; filename="'.$zip_name.'"');
	        header('Content-type: application/zip');
	        readfile($zip_name);
	}
}

/* End of file Alat.php */
/* Location: ./application/controllers/admin/Alat.php */