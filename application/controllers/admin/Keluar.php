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
use PhpOffice\PhpSpreadsheet\Writer\PDF\DomPDF;

class Keluar extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->data['module'] = 'Penjualan Produk';

	    $this->load->model(array('Bahan_kemas_model', 'Vendor_model', 'Venmasaccess_model', 'Produk_model', 'Toko_model', 'Tokproaccess_model', 'Kurir_model', 'Keluar_model', 'Keluar_sementara_model', 'Paket_model', 'Resi_model', 'Dashboard_model', 'Status_transaksi_model', 'Keyword_model', 'Resi_model', 'Retur_model', 'Membership_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_restore'] = 'Restore Database';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['add_action'] = base_url('admin/keluar/penjualan_produk');
	    $this->data['btn_import']    = 'Format Data Import';
	    $this->data['btn_backup']    = 'Backup Database';
		$this->data['import_action'] = base_url('assets/template/excel/format_penjualan.xlsx');
		$this->data['backup_db_action'] = base_url('admin/keluar/backup_db');
		$this->data['format_diterima'] = base_url('assets/template/excel/format_jumlah_diterima.xlsx');
		$this->data['btn_sinkron_total_harga']    = 'Data Sync with Total Price';
		$this->data['sinkron_total_harga_action'] = base_url('admin/keluar/sinkron_total_harga');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	public function dasbor_penjualan()
	{
		$this->data['page_title'] 	= 'Dashboard '.$this->data['module'];
		$this->data['get_all_toko_impor'] = $this->Keluar_model->get_all_toko_only();
		$this->data['get_all_gudang_impor'] = $this->Keluar_model->get_all_gudang_only();
		$this->data['get_all_toko_penjualan'] = $this->Keluar_model->get_all_toko_only();
		$this->data['get_all_gudang_penjualan'] = $this->Keluar_model->get_all_gudang_only();

		$this->data['toko_impor_id'] = [
			'name'          => 'toko_impor_id[]',
			'id'            => 'toko-impor-id',
			'class'         => 'form-control select2-multiple',
			'style'			=> 'width:100%',
			'multiple'      => '',
		];

		$this->data['gudang_impor_id'] = [
			'name'          => 'gudang_impor_id[]',
			'id'            => 'gudang-impor-id',
			'class'         => 'form-control select2-multiple',
			'style'			=> 'width:100%',
			'multiple'      => '',
		];

		$this->data['toko_penjualan_id'] = [
			'name'          => 'toko_penjualan_id[]',
			'id'            => 'toko-penjualan-id',
			'class'         => 'form-control select2-multiple',
			'style'			=> 'width:100%',
			'multiple'      => '',
		];

		$this->data['gudang_penjualan_id'] = [
			'name'          => 'gudang_penjualan_id[]',
			'id'            => 'gudang-penjualan-id',
			'class'         => 'form-control select2-multiple',
			'style'			=> 'width:100%',
			'multiple'      => '',
		];

		$this->load->view('back/keluar/dashboard', $this->data);
	}

	public function ajax_dasbor_protok()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_produk	= $this->Dashboard_model->get_produk_toko_periodik($start, $end);
		$protok_dasbor = array();
		foreach ($get_produk as $val_pro) {
			$int_jumlah = intval($val_pro->jumlah_produk);
			
			$protok_dasbor[] = array( 'name' => $val_pro->nama_produk,
									  'data' => array(array( 0 => $val_pro->nama_toko,
													   		 1 => $int_jumlah
									)
								)
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'protok'  => $protok_dasbor
		);

		echo json_encode($result);
	}

	public function ajax_dasbor_prosku_2()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_sku 		= $this->Dashboard_model->get_sku($start, $end);
		$sku_data 		= array();
		$produk_data 	= array();
		$i = 0;
		foreach ($get_sku as $val_sku) {
			$int_jumlah_sku = intval($val_sku->jumlah_sku);
			
			$sku_data[] = array( 'name' 		=> $val_sku->nama_sku,
								  'y' 	 		=> $int_jumlah_sku,
								  'drilldown'   => $val_sku->nama_sku,
			);

			$produk_data[$i] = array( 'name' 		=> $val_sku->nama_sku,
								   	  'id'	 	 	=> $val_sku->nama_sku,
								   	  'dataLabels'	=> array(	'enabled'	=> false
								   	  )
			);

			$get_produk = $this->Dashboard_model->get_produk_by_sku($start, $end, $val_sku->sku_id);
			foreach ($get_produk as $val_pro) {
				$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
				if (isset($cek_propak)) {
					foreach ($cek_propak as $val_propak) {
						$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
						
						$produk_data[$i]['data'][] = array( 0	=> $val_propak->nama_produk,
											 		     	1	=> $int_fix
						);
					}
				}else{
					// $int_jumlah = intval($val_pro->jumlah_produk);
				
					// $produk[] = array( 0 => $val_pro->nama_produk,
					// 						  1 => $int_jumlah
					// );
				}
				$int_jumlah_pro = intval($val_pro->jumlah_produk);

				$produk_data[$i]['data'][] = array( 0	=> $val_pro->nama_produk,
									 		     	1	=> $int_jumlah_pro
				);
			}

			$i++;
		}

		$result = array(	'sku'		=> $sku_data,
							'produk'	=> $produk_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_dasbor_prosku_2_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_sku 		= $this->Dashboard_model->get_sku_penjualan($start, $end);
		$sku_data 		= array();
		$produk_data 	= array();
		$i = 0;
		foreach ($get_sku as $val_sku) {
			$int_jumlah_sku = intval($val_sku->jumlah_sku);
			
			$sku_data[] = array( 'name' 		=> $val_sku->nama_sku,
								  'y' 	 		=> $int_jumlah_sku,
								  'drilldown'   => $val_sku->nama_sku,
			);

			$produk_data[$i] = array( 'name' 		=> $val_sku->nama_sku,
								   	  'id'	 	 	=> $val_sku->nama_sku,
								   	  'dataLabels'	=> array(	'enabled'	=> false
								   	  )
			);

			$get_produk = $this->Dashboard_model->get_produk_by_sku_penjualan($start, $end, $val_sku->sku_id);
			foreach ($get_produk as $val_pro) {
				$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
				if (isset($cek_propak)) {
					foreach ($cek_propak as $val_propak) {
						$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
						
						$produk_data[$i]['data'][] = array( 0	=> $val_propak->nama_produk,
											 		     	1	=> $int_fix
						);
					}
				}else{
					// $int_jumlah = intval($val_pro->jumlah_produk);
				
					// $produk[] = array( 0 => $val_pro->nama_produk,
					// 						  1 => $int_jumlah
					// );
				}
				$int_jumlah_pro = intval($val_pro->jumlah_produk);

				$produk_data[$i]['data'][] = array( 0	=> $val_pro->nama_produk,
									 		     	1	=> $int_jumlah_pro
				);
			}

			$i++;
		}

		$result = array(	'sku'		=> $sku_data,
							'produk'	=> $produk_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_dasbor_protok_2()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_toko 		= $this->Dashboard_model->get_toko($start, $end);
		$toko_data 		= array();
		$produk_data 	= array();
		$i = 0;
		foreach ($get_toko as $val_tok) {
			$int_jumlah_toko = intval($val_tok->jumlah_toko);
			
			$toko_data[] = array( 'name' 		=> $val_tok->nama_toko,
								  'y' 	 		=> $int_jumlah_toko,
								  'drilldown'   => $val_tok->nama_toko,
			);

			$produk_data[$i] = array( 'name' 		=> $val_tok->nama_toko,
								   	  'id'	 	 	=> $val_tok->nama_toko,
								   	  'dataLabels'	=> array(	'enabled'	=> FALSE
								   	  )
			);

			$get_produk = $this->Dashboard_model->get_produk_by_toko($start, $end, $val_tok->toko_id);
			foreach ($get_produk as $val_pro) {
				$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
				if (isset($cek_propak)) {
					// foreach ($cek_propak as $val_propak) {
					// 	$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
						
					// 	$produk_data[$i]['data'][] = array( 0	=> $val_propak->nama_produk,
					// 						 		     	1	=> $int_fix
					// 	);
					// }
				}else{
					// $int_jumlah = intval($val_pro->jumlah_produk);
				
					// $produk[] = array( 0 => $val_pro->nama_produk,
					// 						  1 => $int_jumlah
					// );
				}
				$int_jumlah_pro = intval($val_pro->jumlah_produk);

				$produk_data[$i]['data'][] = array( 0	=> $val_pro->nama_produk,
									 		     	1	=> $int_jumlah_pro
				);
			}

			$i++;
		}

		$result = array(	'toko'		=> $toko_data,
							'produk'	=> $produk_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_dasbor_protok_2_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_toko 		= $this->Dashboard_model->get_toko_penjualan($start, $end);
		$toko_data 		= array();
		$produk_data 	= array();
		$i = 0;
		foreach ($get_toko as $val_tok) {
			$int_jumlah_toko = intval($val_tok->jumlah_toko);
			
			$toko_data[] = array( 'name' 		=> $val_tok->nama_toko,
								  'y' 	 		=> $int_jumlah_toko,
								  'drilldown'   => $val_tok->nama_toko,
			);

			$produk_data[$i] = array( 'name' 		=> $val_tok->nama_toko,
								   	  'id'	 	 	=> $val_tok->nama_toko,
								   	  'dataLabels'	=> array(	'enabled'	=> FALSE
								   	  )
			);

			$get_produk = $this->Dashboard_model->get_produk_by_toko_penjualan($start, $end, $val_tok->toko_id);
			foreach ($get_produk as $val_pro) {
				$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
				if (isset($cek_propak)) {
					// foreach ($cek_propak as $val_propak) {
					// 	$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
						
					// 	$produk_data[$i]['data'][] = array( 0	=> $val_propak->nama_produk,
					// 						 		     	1	=> $int_fix
					// 	);
					// }
				}else{
					// $int_jumlah = intval($val_pro->jumlah_produk);
				
					// $produk[] = array( 0 => $val_pro->nama_produk,
					// 						  1 => $int_jumlah
					// );
				}
				$int_jumlah_pro = intval($val_pro->jumlah_produk);

				$produk_data[$i]['data'][] = array( 0	=> $val_pro->nama_produk,
									 		     	1	=> $int_jumlah_pro
				);
			}

			$i++;
		}

		$result = array(	'toko'		=> $toko_data,
							'produk'	=> $produk_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_dasbor_prokur()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_produk	= $this->Dashboard_model->get_produk_kurir_periodik($start, $end);
		$protok_dasbor = array();
		foreach ($get_produk as $val_pro) {
			$int_jumlah = intval($val_pro->jumlah_produk);
			
			$protok_dasbor[] = array( 'name' => $val_pro->nama_produk,
									  'data' => array(array( 0 => $val_pro->nama_kurir,
													   		 1 => $int_jumlah
									)
								)
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'prokur'  => $protok_dasbor
		);

		echo json_encode($result);
	}

	public function ajax_dasbor_prokur_2()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_toko 		= $this->Dashboard_model->get_toko($start, $end);
		$toko_data 		= array();
		$kurir_data 	= array();
		$i = 0;
		foreach ($get_toko as $val_tok) {
			$int_jumlah_toko = intval($val_tok->jumlah_toko);
			
			$toko_data[] = array( 'name' 		=> $val_tok->nama_toko,
								  'y' 	 		=> $int_jumlah_toko,
								  'drilldown'   => $val_tok->nama_toko,
			);

			$kurir_data[$i] = array( 'name' 		=> $val_tok->nama_toko,
								   	  'id'	 	 	=> $val_tok->nama_toko,
			);

			$get_kurir = $this->Dashboard_model->get_kurir_by_toko($start, $end, $val_tok->toko_id);
			foreach ($get_kurir as $val_kur) {
				$int_jumlah_kur = intval($val_kur->jumlah_kurir);

				$kurir_data[$i]['data'][] = array( 0	=> $val_kur->nama_kurir,
									 		       1	=> $int_jumlah_kur
				);
			}

			$i++;
		}

		$result = array(	'toko'		=> $toko_data,
							'kurir'		=> $kurir_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_dasbor_prokur_2_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_toko 		= $this->Dashboard_model->get_toko_penjualan($start, $end);
		$toko_data 		= array();
		$kurir_data 	= array();
		$i = 0;
		foreach ($get_toko as $val_tok) {
			$int_jumlah_toko = intval($val_tok->jumlah_toko);
			
			$toko_data[] = array( 'name' 		=> $val_tok->nama_toko,
								  'y' 	 		=> $int_jumlah_toko,
								  'drilldown'   => $val_tok->nama_toko,
			);

			$kurir_data[$i] = array( 'name' 		=> $val_tok->nama_toko,
								   	  'id'	 	 	=> $val_tok->nama_toko,
			);

			$get_kurir = $this->Dashboard_model->get_kurir_by_toko_penjualan($start, $end, $val_tok->toko_id);
			foreach ($get_kurir as $val_kur) {
				$int_jumlah_kur = intval($val_kur->jumlah_kurir);

				$kurir_data[$i]['data'][] = array( 0	=> $val_kur->nama_kurir,
									 		       1	=> $int_jumlah_kur
				);
			}

			$i++;
		}

		$result = array(	'toko'		=> $toko_data,
							'kurir'		=> $kurir_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	function dasbor_list_count(){
		$kurir 		= $this->input->post('kurir');
		$toko 	= $this->input->post('toko');
		$resi 	= $this->input->post('resi');
		$status = $this->input->post('status');
		$trigger = $this->input->post('trigger');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Keluar_model->get_dasbor_list($trigger, $status, $kurir, $toko, $resi, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
			        		'pending'	=> $data->pending,
			        		'transfer'	=> $data->transfer,
			        		'diterima'	=> $data->diterima,
			        		'retur'		=> $data->retur,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_sementara(){
		$kurir 		= $this->input->post('kurir');
		$toko 	= $this->input->post('toko');
		$resi 	= $this->input->post('resi');
		$status = $this->input->post('status');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Keluar_sementara_model->get_dasbor_list($status, $kurir, $toko, $resi, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
			        		'pending'	=> $data->pending,
			        		'transfer'	=> $data->transfer,
			        		'diterima'	=> $data->diterima,
			        		'retur'		=> $data->retur,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

	public function ajax_list()
	{
		$i = 1;
		$kurir = $this->input->get('kurir');
		$toko = $this->input->get('toko');
		$resi = $this->input->get('resi');
		$status = $this->input->get('status');
		$start = substr($this->input->get('periodik'), 0, 10);
		$end = substr($this->input->get('periodik'), 13, 24);
		$rows = array();
		$get_all = $this->Keluar_model->get_datatable($status, $kurir, $toko, $resi, $start, $end);
		$length = count($get_all);
		foreach ($get_all as $data) {
			$action = '<a href="'.base_url('admin/keluar/ubah/'.base64_encode($data->nomor_pesanan)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/keluar/hapus/'.base64_encode($data->nomor_pesanan)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan).' Produk</span>';
			if ($data->id_status_transaksi == 1) {
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 2) {
				$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-money' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 3) {
				$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 4) {
				$status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-exchange' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}

			// Detail Penjualan
			$get_detail_penjualan = $this->Keluar_model->get_all_detail_by_id($data->nomor_pesanan);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td width="1%">Qty</td>'.
			                '<td colspan="2">Nama Produk</td>'.
			            '</tr>';
			foreach ($get_detail_penjualan as $val_detail) {
				$detail .= '<tr align="center">'.
				                '<td>'.$val_detail->qty.'</td>'.
				                '<td colspan="2">'.$val_detail->nama_produk.'</td>'.
				            '</tr>';
			}

			$detail .= '</table>';

			$rows[] = array( 'no'				=> $i,
							 'tanggal' 			=> date('d-m-Y', strtotime($data->tgl_penjualan)), 
							 'nomor_pesanan'    => $produk,
							 'nama_toko' 		=> $data->nama_toko,
							 'nama_kurir' 		=> $data->nama_kurir,
							 'nomor_resi' 		=> $data->nomor_resi,
							 'total_harga' 		=> $data->total_harga,
							 'range' 			=> $this->input->get('periodik'),
							 'action' 			=> $action,
							 'status' 			=> $status,
							 'detail' 			=> $detail,
							 'created' 			=> $data->created,
							 'length' 			=> $length 
			);

			$i++;
		}
		echo json_encode($rows);
	}

	// Datatable Server Side
	function get_data_dasbor_sku_impor()
    {
    	$list = $this->Keluar_model->get_datatables_sku_impor();

        $dataJSON = array();
        foreach ($list as $data) {
        	$row = array();
        	
        	$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($data->id_produk);
        	if (count($cek_propak) > 0) {
				// echo print_r($cek_propak)."<br>";
				foreach ($cek_propak as $val_propak) {
					$row['nama_produk'] = $val_propak->nama_produk;
					$row['sku'] = $val_propak->sub_sku;
		        	$row['qty'] = $val_propak->qty_pakduk * $data->sum_qty;

		        	$dataJSON[] = $row;
				}
			}else{
				$row['nama_produk'] = $data->nama_produk;
				$row['sku'] = $data->sub_sku;
	        	$row['qty'] = $data->sum_qty;
				
				$dataJSON[] = $row;
			}
        }

		// echo print_r($dataJSON)."<br>";

		$results = array();
		foreach($dataJSON as $value) {
		    //check if color exists in the temp array
		    if(!array_key_exists($value['nama_produk'], $results)) {
		        //if it does not exist, create it with a value of 0
		        $results[$value['nama_produk']]['qty'] = 0;
			    $results[$value['nama_produk']]['sku'] = $value['sku'];
		    }
		    //Add up the values from each color
		    $results[$value['nama_produk']]['qty'] += $value['qty'];
		    $results[$value['nama_produk']]['sku'] = $value['sku'];
		}

		$dataFix = array();
		foreach($results as $key => $value)
		{
		  // echo $key." ".$value['sku']."<br>";
		  $dataFix[] = array('nama_produk' => $key, 'sku' => $value['sku'], 'qty' => $value['qty']);
		}


        $output = array(
            "recordsTotal" => count($dataJSON),
            "recordsFiltered" => count($dataFix),
            "data" => $dataFix,
        );

        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_dasbor_sku_penjualan()
    {
    	$list = $this->Keluar_model->get_datatables_sku_penjualan();

        $dataJSON = array();
        foreach ($list as $data) {
        	$row = array();
        	
        	$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($data->id_produk);
        	if ($cek_propak) {
				// echo print_r($cek_propak)."<br>";
				foreach ($cek_propak as $val_propak) {
					$row['nama_produk'] = $val_propak->nama_produk;
					$row['sku'] = $val_propak->sub_sku;
		        	$row['qty'] = $val_propak->qty_pakduk * $data->sum_qty;

		        	$dataJSON[] = $row;
				}
			}else{
				$row['nama_produk'] = $data->nama_produk;
				$row['sku'] = $data->sub_sku;
	        	$row['qty'] = $data->sum_qty;
				
				$dataJSON[] = $row;
			}
        }

		$results = array();
		foreach($dataJSON as $value) {
		    //check if color exists in the temp array
		    if(!array_key_exists($value['nama_produk'], $results)) {
		        //if it does not exist, create it with a value of 0
		        $results[$value['nama_produk']]['qty'] = 0;
			    $results[$value['nama_produk']]['sku'] = $value['sku'];
		    }
		    //Add up the values from each color
		    $results[$value['nama_produk']]['qty'] += $value['qty'];
		    $results[$value['nama_produk']]['sku'] = $value['sku'];
		}
		
		$dataFix = array();
		foreach($results as $key => $value)
		{
		  // echo $key." ".$value['sku']."<br>";
		  $dataFix[] = array('nama_produk' => $key, 'sku' => $value['sku'], 'qty' => $value['qty']);
		}


        $output = array(
            "recordsTotal" => $this->Keluar_model->count_all_sku(),
            "recordsFiltered" => count($dataFix),
            "data" => $dataFix,
        );

        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_dasbor_sku_gudang_impor()
    {
    	$list = $this->Keluar_model->get_datatables_sku_gudang_impor();

        $dataJSON = array();
        foreach ($list as $data) {
        	$row = array();
        	
        	$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($data->id_produk);
        	if (count($cek_propak) > 0) {
				// echo print_r($cek_propak)."<br>";
				foreach ($cek_propak as $val_propak) {
					$row['nama_produk'] = $val_propak->nama_produk;
					$row['sku'] = $val_propak->sub_sku;
		        	$row['qty'] = $val_propak->qty_pakduk * $data->sum_qty;

		        	$dataJSON[] = $row;
				}
			}else{
				$row['nama_produk'] = $data->nama_produk;
				$row['sku'] = $data->sub_sku;
	        	$row['qty'] = $data->sum_qty;
				
				$dataJSON[] = $row;
			}
        }

		// echo print_r($dataJSON)."<br>";

		$results = array();
		foreach($dataJSON as $value) {
		    //check if color exists in the temp array
		    if(!array_key_exists($value['nama_produk'], $results)) {
		        //if it does not exist, create it with a value of 0
		        $results[$value['nama_produk']]['qty'] = 0;
			    $results[$value['nama_produk']]['sku'] = $value['sku'];
		    }
		    //Add up the values from each color
		    $results[$value['nama_produk']]['qty'] += $value['qty'];
		    $results[$value['nama_produk']]['sku'] = $value['sku'];
		}

		$dataFix = array();
		foreach($results as $key => $value)
		{
		  // echo $key." ".$value['sku']."<br>";
		  $dataFix[] = array('nama_produk' => $key, 'sku' => $value['sku'], 'qty' => $value['qty']);
		}


        $output = array(
            "recordsTotal" => count($dataJSON),
            "recordsFiltered" => count($dataFix),
            "data" => $dataFix,
        );

        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_dasbor_sku_gudang_penjualan()
    {
    	$list = $this->Keluar_model->get_datatables_sku_gudang_penjualan();

        $dataJSON = array();
        foreach ($list as $data) {
        	$row = array();
        	
        	$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($data->id_produk);
        	if ($cek_propak) {
				// echo print_r($cek_propak)."<br>";
				foreach ($cek_propak as $val_propak) {
					$row['nama_produk'] = $val_propak->nama_produk;
					$row['sku'] = $val_propak->sub_sku;
		        	$row['qty'] = $val_propak->qty_pakduk * $data->sum_qty;

		        	$dataJSON[] = $row;
				}
			}else{
				$row['nama_produk'] = $data->nama_produk;
				$row['sku'] = $data->sub_sku;
	        	$row['qty'] = $data->sum_qty;
				
				$dataJSON[] = $row;
			}
        }

		// echo print_r($dataJSON)."<br>";

		$results = array();
		foreach($dataJSON as $value) {
		    //check if color exists in the temp array
		    if(!array_key_exists($value['nama_produk'], $results)) {
		        //if it does not exist, create it with a value of 0
		        $results[$value['nama_produk']]['qty'] = 0;
			    $results[$value['nama_produk']]['sku'] = $value['sku'];
		    }
		    //Add up the values from each color
		    $results[$value['nama_produk']]['qty'] += $value['qty'];
		    $results[$value['nama_produk']]['sku'] = $value['sku'];
		}
		
		$dataFix = array();
		foreach($results as $key => $value)
		{
		  // echo $key." ".$value['sku']."<br>";
		  $dataFix[] = array('nama_produk' => $key, 'sku' => $value['sku'], 'qty' => $value['qty']);
		}


        $output = array(
            "recordsTotal" => $this->Keluar_model->count_all_sku(),
            "recordsFiltered" => count($dataFix),
            "data" => $dataFix,
        );

        //output dalam format JSON
        echo json_encode($output);
    }

	function get_data_penjualan()
    {
        $list = $this->Keluar_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
   			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan).' Produk</span>';
			if ($data->id_status_transaksi == 1) {
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
				$action = '<a href="'.base_url('admin/keluar/ubah/'.base64_encode($data->nomor_pesanan)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
	          	$action .= ' <a href="'.base_url('admin/keluar/hapus/'.base64_encode($data->nomor_pesanan)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
			}else if ($data->id_status_transaksi == 2) {
				$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-money' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
				$action = '<a href="'.base_url('admin/keluar/ubah/'.base64_encode($data->nomor_pesanan)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
	          	$action .= ' <a href="'.base_url('admin/keluar/hapus/'.base64_encode($data->nomor_pesanan)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
			}else if ($data->id_status_transaksi == 3) {
				$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
				$action = '<a href="'.base_url('admin/keluar/ubah/'.base64_encode($data->nomor_pesanan)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
	          	$action .= ' <a href="'.base_url('admin/keluar/hapus/'.base64_encode($data->nomor_pesanan)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
			}else if ($data->id_status_transaksi == 4) {
				$status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-exchange' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
				$action = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-exchange' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}

			if ($_GET['trigger'] == 'impor') {
		        $select = '<input type="checkbox" class="sub_chk" data-id="'.$data->nomor_pesanan.'">';
		    }else if ($_GET['trigger'] == 'penjualan') {
		        $select = '<input type="checkbox" class="sub_chk_penjualan" data-id="'.$data->nomor_pesanan.'">';
		    }

			// Detail Penjualan
			$get_detail_penjualan = $this->Keluar_model->get_all_detail_by_id($data->nomor_pesanan);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td width="1%">Qty</td>'.
			                '<td colspan="2">Nama Produk</td>'.
			            '</tr>';
			foreach ($get_detail_penjualan as $val_detail) {
				$detail .= '<tr align="center">'.
				                '<td>'.$val_detail->qty.'</td>'.
				                '<td colspan="2">'.$val_detail->nama_produk.'</td>'.
				            '</tr>';
			}

            $row = array();
            $row['nomor_pesanan'] = $produk;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_penjualan));
            $row['nama_toko'] = $data->nama_toko;
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['action'] = $action;
            $row['status'] = $status;
            $row['detail'] = $detail;
            $row['select'] = $select;
            $row['created'] = $data->created;
			$row['total_harga'] = $data->total_harga;
            $row['total_jual'] = $data->total_jual;
            $row['total_hpp'] = $data->total_hpp;
            $row['margin'] = $data->margin;
            $row['selisih_margin'] = $data->selisih_margin;
            $row['ongkir'] = $data->ongkir;
            $row['jumlah_diterima'] = $data->jumlah_diterima;
            if ($data->tgl_diterima == NULL) {
            	$row['tgl_diterima'] = "-";
            }else{
            	$row['tgl_diterima'] = $data->tgl_diterima;
            }
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Keluar_model->count_all(),
            "recordsFiltered" => $this->Keluar_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_sementara()
    {
        $list = $this->Keluar_sementara_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
   			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan_sementara($data->nomor_pesanan).' Produk</span>';
			if ($data->id_status_transaksi == 1) {
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
				$action = '<a href="'.base_url('admin/keluar/ubah_sementara/'.base64_encode($data->nomor_pesanan)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
	          	$action .= ' <a href="'.base_url('admin/keluar/hapus_sementara/'.base64_encode($data->nomor_pesanan)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
			}else if ($data->id_status_transaksi == 2) {
				$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-money' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
				$action = '<a href="'.base_url('admin/keluar/ubah_sementara/'.base64_encode($data->nomor_pesanan)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
	          	$action .= ' <a href="'.base_url('admin/keluar/hapus_sementara/'.base64_encode($data->nomor_pesanan)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
			}else if ($data->id_status_transaksi == 3) {
				$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
				$action = '<a href="'.base_url('admin/keluar/ubah_sementara/'.base64_encode($data->nomor_pesanan)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
	          	$action .= ' <a href="'.base_url('admin/keluar/hapus_sementara/'.base64_encode($data->nomor_pesanan)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
			}else if ($data->id_status_transaksi == 4) {
				$status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-exchange' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
				$action = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-exchange' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}
			$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->nomor_pesanan.'">';

			// Detail Penjualan
			$get_detail_penjualan = $this->Keluar_sementara_model->get_all_detail_by_id($data->nomor_pesanan);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td width="1%">Qty</td>'.
			                '<td colspan="2">Nama Produk</td>'.
			            '</tr>';
			foreach ($get_detail_penjualan as $val_detail) {
				$detail .= '<tr align="center">'.
				                '<td>'.$val_detail->qty.'</td>'.
				                '<td colspan="2">'.$val_detail->nama_produk.'</td>'.
				            '</tr>';
			}

            $row = array();
            $row['nomor_pesanan'] = $produk;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_penjualan));
            $row['nama_toko'] = $data->nama_toko;
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['action'] = $action;
            $row['status'] = $status;
            $row['detail'] = $detail;
            $row['select'] = $select;
            $row['created'] = $data->created;
            $row['total_harga'] = $data->total_harga;
            $row['total_jual'] = $data->total_jual;
            $row['total_hpp'] = $data->total_hpp;
            $row['margin'] = $data->margin;
            $row['selisih_margin'] = $data->selisih_margin;
            $row['ongkir'] = $data->ongkir;
            $row['jumlah_diterima'] = $data->jumlah_diterima;
            if ($data->tgl_diterima == NULL) {
            	$row['tgl_diterima'] = "-";
            }else{
            	$row['tgl_diterima'] = $data->tgl_diterima;
            }
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Keluar_sementara_model->count_all(),
            "recordsFiltered" => $this->Keluar_sementara_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

    // ORIGINAL
	public function data_penjualan()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';
	    $this->data['action_impor']  = 'admin/keluar/proses_impor_diterima';

	    $this->data['get_all_kurir'] = $this->Keluar_model->get_all_kurir_list();
	    $this->data['get_all_toko'] = $this->Keluar_model->get_all_toko_list();
	    $this->data['get_all_status'] = $this->Keluar_model->get_all_status_list();
	    $this->data['get_all_resi'] = array( 'semua'	=> '- Semua Data-',
	    									   '' 		=> 'Tidak Ada Resi'
	     								);

	    // $this->data['get_all'] = $this->Keluar_model->get_all();
	    $this->data['kurir'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kurir',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['toko'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'toko',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['resi'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'resi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['diterima'] = [	
		  	'id' 			=> 'diterima', 
	        'type'          => 'hidden',
	      ];

	    $this->load->view('back/keluar/penjualan_produk_list', $this->data);
	}

	// SEMENTARA
	public function data_sementara()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' Sementara List';
	    $this->data['get_all_kurir'] = $this->Keluar_model->get_all_kurir_list();
	    $this->data['get_all_toko'] = $this->Keluar_model->get_all_toko_list();
	    $this->data['get_all_status'] = $this->Keluar_model->get_all_status_list();
	    $this->data['get_all_resi'] = array( 'semua'	=> '- Semua Data-',
	    									   '' 		=> 'Tidak Ada Resi'
	     								);

	    // $this->data['get_all'] = $this->Keluar_model->get_all();
	    $this->data['kurir'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kurir',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['toko'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'toko',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['resi'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'resi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/keluar/sementara/penjualan_sementara_list', $this->data);
	}

	public function migrate_data_sementara()
	{
		$baris = 0;
		$resi = 0;
		$ambil_data = $this->Keluar_sementara_model->get_all();
		if (count($ambil_data) > 0) {
			foreach ($ambil_data as $val_sementara) {
				$cek_nomor = $this->Keluar_model->get_all_detail_by_id_row($val_sementara->nomor_pesanan);
				if (isset($cek_nomor)) {
					$cek_detail = $this->Keluar_sementara_model->get_detail_by_id($val_sementara->nomor_pesanan);
					if (count($cek_detail) > 0) {
						foreach ($cek_detail as $val_detail) {
							$simpanDetail		= array(	'nomor_pesanan'		=> $val_detail->nomor_pesanan,
															'id_produk' 		=> $val_detail->id_produk,
															'qty' 				=> $val_detail->qty,
															'harga'		 		=> $val_detail->harga,
															'hpp' 				=> $val_detail->hpp
													);

							$this->Keluar_model->insert_detail($simpanDetail);

							write_log();

							// START - Kurangin jumlah total jika ada didalam paket
				          	$cariPaket = $this->Produk_model->get_all_by_id($val_detail->id_produk);
				          	if (isset($cariPaket)) {
				          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
				          		if (count($produkPaket) > 0) {
				          			// echo print_r($produkPaket);
				          			foreach ($produkPaket as $result) {
				          				$total = $result->qty_pakduk * $val_detail->qty;
					          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
										          	);

										$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	

										write_log();
				          			}
				          		}
				          	}
				          	// END

							$cariProduk 			= $this->Produk_model->get_by_id($val_detail->id_produk);
				          	$kurangStokProduk 		= array(	'qty_produk' 		=> $cariProduk->qty_produk - $val_detail->qty
									          			);

				          	$this->Produk_model->update($cariProduk->id_produk, $kurangStokProduk);

				          	write_log();

						}
					}
				}else{					
					$simpanPenjualan	= array(	'nomor_pesanan'			=> $val_sementara->nomor_pesanan,
													'tgl_penjualan' 		=> $val_sementara->tgl_penjualan,
													'id_users' 				=> $val_sementara->id_users,
													'id_status_transaksi' 	=> $val_sementara->id_status_transaksi,
													'id_toko' 				=> $val_sementara->id_toko,
													'id_kurir'	 			=> $val_sementara->id_kurir,
													'nomor_resi'			=> $val_sementara->nomor_resi,
													'nama_penerima' 		=> $val_sementara->nama_penerima,
													'hp_penerima' 			=> $val_sementara->hp_penerima,
													'alamat_penerima' 		=> $val_sementara->alamat_penerima,
													'kabupaten' 			=> $val_sementara->kabupaten,
													'provinsi' 				=> $val_sementara->provinsi,
													'ongkir'		 		=> $val_sementara->ongkir,
													'biaya_admin'	 		=> $val_sementara->biaya_admin,
													'harga_jual'	 		=> $val_sementara->harga_jual,
													'total_hpp'	 			=> $val_sementara->total_hpp,
													'total_jual' 			=> $val_sementara->total_jual,
													'total_harga' 			=> $val_sementara->total_harga,
													'selisih_margin' 		=> $val_sementara->selisih_margin,
													'margin' 				=> $val_sementara->margin,
													'jumlah_diterima' 		=> $val_sementara->jumlah_diterima,
													'tgl_diterima' 			=> $val_sementara->tgl_diterima,
													'created' 				=> $val_sementara->created 	
											);

					$this->Keluar_model->insert($simpanPenjualan);

					write_log();



					if ($val_sementara->nomor_resi != '') {
						if ($val_sementara->id_status_transaksi == 4) {
							$data = array(	'nomor_resi'	=> $val_sementara->nomor_resi,
										'id_users' 		=> $val_sementara->id_users, 
										'status' 		=> 3,
										'tgl_resi' 		=> $val_sementara->tgl_penjualan,
										'created_resi'	=> $val_sementara->created,  
							);

							$this->Resi_model->insert($data);

							write_log();	
							$resi++;
						}else{
							$data = array(	'nomor_resi'	=> $val_sementara->nomor_resi,
										'id_users' 		=> $val_sementara->id_users, 
										'status' 		=> 0,
										'tgl_resi' 		=> $val_sementara->tgl_penjualan,
										'created_resi'	=> $val_sementara->created,  
							);

							$this->Resi_model->insert($data);

							write_log();	
							$resi++;
						}
					}

					$cek_detail = $this->Keluar_sementara_model->get_detail_by_id($val_sementara->nomor_pesanan);
					if (count($cek_detail) > 0) {
						foreach ($cek_detail as $val_detail) {
							$simpanDetail		= array(	'nomor_pesanan'		=> $val_detail->nomor_pesanan,
															'id_produk' 		=> $val_detail->id_produk,
															'qty' 				=> $val_detail->qty,
															'harga'		 		=> $val_detail->harga,
															'hpp' 				=> $val_detail->hpp
													);

							$this->Keluar_model->insert_detail($simpanDetail);

							write_log();

							// START - Kurangin jumlah total jika ada didalam paket
				          	$cariPaket = $this->Produk_model->get_all_by_id($val_detail->id_produk);
				          	if (isset($cariPaket)) {
				          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
				          		if (count($produkPaket) > 0) {
				          			// echo print_r($produkPaket);
				          			foreach ($produkPaket as $result) {
				          				$total = $result->qty_pakduk * $val_detail->qty;
					          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
										          	);

										$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	

										write_log();
				          			}
				          		}
				          	}
				          	// END

							$cariProduk 			= $this->Produk_model->get_by_id($val_detail->id_produk);
				          	$kurangStokProduk 		= array(	'qty_produk' 		=> $cariProduk->qty_produk - $val_detail->qty
									          			);

				          	$this->Produk_model->update($cariProduk->id_produk, $kurangStokProduk);

				          	write_log();

						}
					}
				}

				$this->Keluar_sementara_model->delete_detail($val_sementara->nomor_pesanan);

				write_log();

				$this->Keluar_sementara_model->delete($val_sementara->nomor_pesanan);

				write_log();

				$baris++;
			}

			$msg = array(	'sukses'	=> $baris.' Data Berhasil Import Penjualan!. '.$resi.' Data Resi ditambah!',
    			);
	    	echo json_encode($msg);
		}else{
			$msg = array(	'validasi'		=> 'Tidak ada data!',
			);
	    	echo json_encode($msg);
		}
	}
    // ORIGINAL
	public function customerinsight()
	{
		is_read();    

	    $this->data['page_title'] = 'Customer Insight';

	    $this->data['get_all_provinsi'] = $this->Keyword_model->get_all_provinsi_combobox();
	    $this->data['get_all_toko'] = $this->Keluar_model->get_all_toko_list();
	    $this->data['get_all_status'] = $this->Keluar_model->get_all_status_list();
	    $this->data['get_all_resi'] = array( 'semua'	=> '- Semua Data-',
	    									   '' 		=> 'Tidak Ada Resi'
	     								);

	    // $this->data['get_all'] = $this->Keluar_model->get_all();
	    $this->data['provinsi'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'provinsi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kabupaten'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kabupaten',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['resi'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'resi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['diterima'] = [	
		  	'id' 			=> 'diterima', 
	        'type'          => 'hidden',
	      ];

	    $this->load->view('back/keluar/customer_insight', $this->data);
	}

	function get_data_customer_insight()
    {	$start = substr($this->input->get('periodik'), 0, 10);
		$end = substr($this->input->get('periodik'), 13, 24);
		$provinsi = $this->input->get('provinsi');
		$kabupaten = $this->input->get('kabupaten');
		$belanja_min = $this->input->get('belanja_min');
		$belanja_max = $this->input->get('belanja_max');
		$qty_min = $this->input->get('qty_min');
		$qty_max = $this->input->get('qty_max');
        $list = $this->Keluar_model->get_datatable_customer_insight($start, $end, $provinsi, $kabupaten, $belanja_min, $belanja_max, $qty_min, $qty_max);
        $dataJSON = array();
        foreach ($list as $data) {
			$get_detail_penjualan = $this->Keluar_model->get_detail_by_cust_data($data->nama_penerima, $data->hp_penerima, $start, $end);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td width="1%">Qty</td>'.
			                '<td colspan="2">Nama Produk</td>'.
			            '</tr>';

						foreach ($get_detail_penjualan as $val_detail) {
							$detail .= '<tr align="center">'.
											'<td>'.$val_detail->total_qty.'</td>'.
											'<td colspan="2">'.$val_detail->nama_produk.'</td>'.
										'</tr>';
						}

            $row = array();
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_penjualan));
            $row['nomor_resi'] = $data->nomor_resi;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['created'] = $data->created;
			$row['total_harga'] = $data->total_harga;
            $row['total_jual'] = $data->total_jual;
            $row['total_hpp'] = $data->total_hpp;
            $row['margin'] = $data->margin;
            $row['selisih_margin'] = $data->selisih_margin;
            $row['ongkir'] = $data->ongkir;
            $row['jumlah_diterima'] = $data->jumlah_diterima;
			$row['total_qty'] = $data->total_qty;
			$row['jumlah_pesanan'] = $data->jumlah_pesanan;
			$row['tier'] = $this->Membership_model->getTierPoinByTotalOrder($data->total_harga_jual) ? $this->Membership_model->getTierPoinByTotalOrder($data->total_harga_jual)->tier : '';
			// die(print_r($this->Membership_model->getTierPoinByTotalOrder($data->total_harga_jual)->x_poin));
			$row['poin'] = $this->Membership_model->getTierPoinByTotalOrder($data->total_harga_jual) ? $data->jumlah_pesanan * $this->Membership_model->getTierPoinByTotalOrder($data->total_harga_jual)->x_poin : '';
			$row['total_harga_jual'] = 'Rp. ' . number_format($data->total_harga_jual,0,",",".");
            if ($data->tgl_diterima == NULL) {
            	$row['tgl_diterima'] = "-";
            }else{
            	$row['tgl_diterima'] = $data->tgl_diterima;
            }
			$row['detail'] = $detail;
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => 10,
            "recordsFiltered" => 10,
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	public function ubah($id = '')
	{
		is_update();

	    $this->data['penjualan']   			= $this->Keluar_model->get_all_by_id(base64_decode($id));
	    $this->data['daftar_produk']		= $this->Keluar_model->get_detail_by_id(base64_decode($id));
	    $this->data['get_all_produk'] 		= $this->Produk_model->get_all_produk_by_toko($this->data['penjualan']->id_toko);
	    $this->data['get_all_toko'] 		= $this->Keluar_model->get_all_toko();
	    $this->data['get_all_kurir'] 		= $this->Keluar_model->get_all_kurir();
	    $this->data['get_all_status']				= $this->Keluar_model->get_all_status();
	    $this->data['get_all_provinsi'] 	= provinsi();
	    if ($this->data['penjualan']->provinsi != '') {
	    	$this->data['get_all_kabupaten']	= kabupaten_indeks($this->data['penjualan']->provinsi);
	    }
	    

	    // echo print_r($this->data['produk']);
	    if($this->data['penjualan'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];

	      $this->data['nomor_pesanan'] = [
	        'name'          => 'nomor_pesanan',
	        'class'         => 'form-control',
			'readonly' 		=> '' 
	      ];

		  $this->data['id'] = [	
		  	'id' 			=> 'nomor-pesanan', 
	        'type'          => 'hidden',
	      ];	

		  $this->data['nama_kurir'] = [
		      'name'          => 'nama_kurir',
		      'id'            => 'nama-kurir',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['ongkir'] = [
	      'name'          => 'ongkir',
	      'id'            => 'ongkir',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['biaya_admin'] = [
	      'name'          => 'biaya_admin',
	      'id'            => 'biaya-admin',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];


		$this->data['nama_penerima'] = [
	      'name'          => 'nama_penerima',
	      'id'            => 'nama-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['hp_penerima'] = [
	      'name'          => 'hp_penerima',
	      'id'            => 'hp-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    if ($this->data['penjualan']->nomor_resi != '') {
		    $this->data['nomor_resi'] = [
		      'name'          => 'nomor_resi',
		      'id'            => 'nomor-resi',
		      'readonly' 	  => '', 
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];	
	    }else{
	    	$this->data['nomor_resi'] = [
		      'name'          => 'nomor_resi',
		      'id'            => 'nomor-resi',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];
	    }

	    $this->data['toko'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'toko',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['provinsi'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'provinsi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kabupaten'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kabupaten',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['produk'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'produk',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status-transaksi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kurir'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kurir',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['id_kurir'] = [	
	        'type'          => 'hidden',
	    ];

	    $this->data['alamat'] = [
	      'name'          => 'alamat',
	      'id'            => 'alamat',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->data['ongkir'] = [
	      'name'          => 'ongkir',
	      'id'            => 'ongkir',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->data['biaya_admin'] = [
	      'name'          => 'biaya_admin',
	      'id'            => 'biaya-admin',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->data['diterima'] = [
	      'name'          => 'diterima',
	      'id'            => 'diterima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	      $this->load->view('back/keluar/penjualan_produk_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/ubah');
	    }
	}

	public function ubah_sementara($id = '')
	{
		is_update();

	    $this->data['penjualan']   			= $this->Keluar_sementara_model->get_all_by_id(base64_decode($id));
	    $this->data['daftar_produk']		= $this->Keluar_sementara_model->get_detail_by_id(base64_decode($id));
	    $this->data['get_all_produk'] 		= $this->Produk_model->get_all_produk_by_toko($this->data['penjualan']->id_toko);
	    $this->data['get_all_toko'] 		= $this->Keluar_sementara_model->get_all_toko();
	    $this->data['get_all_kurir'] 		= $this->Keluar_sementara_model->get_all_kurir();
	    $this->data['get_all_status']		= $this->Keluar_sementara_model->get_all_status();
	    $this->data['get_all_provinsi'] 	= provinsi();
	    if ($this->data['penjualan']->provinsi != '') {
	    	$this->data['get_all_kabupaten']	= kabupaten_indeks($this->data['penjualan']->provinsi);
	    }
	    

	    // echo print_r($this->data['produk']);
	    if($this->data['penjualan'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'].' Sementara';

	      $this->data['nomor_pesanan'] = [
	        'name'          => 'nomor_pesanan',
	        'class'         => 'form-control',
			'readonly' 		=> '' 
	      ];

		  $this->data['id'] = [	
		  	'id' 			=> 'nomor-pesanan', 
	        'type'          => 'hidden',
	      ];	

		  $this->data['nama_kurir'] = [
		      'name'          => 'nama_kurir',
		      'id'            => 'nama-kurir',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

		  $this->data['ongkir'] = [
	      'name'          => 'ongkir',
	      'id'            => 'ongkir',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['biaya_admin'] = [
	      'name'          => 'biaya_admin',
	      'id'            => 'biaya-admin',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];


		$this->data['nama_penerima'] = [
	      'name'          => 'nama_penerima',
	      'id'            => 'nama-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['hp_penerima'] = [
	      'name'          => 'hp_penerima',
	      'id'            => 'hp-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    if ($this->data['penjualan']->nomor_resi != '') {
		    $this->data['nomor_resi'] = [
		      'name'          => 'nomor_resi',
		      'id'            => 'nomor-resi',
		      'readonly' 	  => '', 
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];	
	    }else{
	    	$this->data['nomor_resi'] = [
		      'name'          => 'nomor_resi',
		      'id'            => 'nomor-resi',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		    ];
	    }

	    $this->data['toko'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'toko',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['provinsi'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'provinsi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kabupaten'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kabupaten',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['produk'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'produk',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status-transaksi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];
	    
	    $this->data['kurir'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kurir',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['id_kurir'] = [	
	        'type'          => 'hidden',
	    ];

	    $this->data['alamat'] = [
	      'name'          => 'alamat',
	      'id'            => 'alamat',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->data['ongkir'] = [
	      'name'          => 'ongkir',
	      'id'            => 'ongkir',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->data['biaya_admin'] = [
	      'name'          => 'biaya_admin',
	      'id'            => 'biaya-admin',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->data['diterima'] = [
	      'name'          => 'diterima',
	      'id'            => 'diterima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	      $this->load->view('back/keluar/sementara/penjualan_sementara_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/ubah');
	    }
	}

	public function ubah_proses()
	{
		date_default_timezone_set("Asia/Jakarta");
		$now = date('Y-m-d H:i:s');
		// Penjumlahan
		$total_jual = 0;
		$total_hpp 	 = 0;	

		// Ambil Data
		$i = $this->input;

		$len = $i->post('length');
		$tgl_penjualan = $i->post('tgl_penjualan');
		$tgl_import = $i->post('tgl_import');
		$toko = $i->post('toko');
		$kurir = $i->post('kurir');
		$diterima = $i->post('diterima');
		$status = $i->post('status');
		$admin = intval($i->post('admin'));
		$ongkir = intval($i->post('ongkir'));
		$resi = $i->post('resi');
		$no_pesanan = $i->post('no_pesanan');
		$nama_penerima = $i->post('nama_penerima');
		$hp_penerima = $i->post('hp_penerima');
		$provinsi = $i->post('provinsi');
		$kabupaten = $i->post('kabupaten');
		$alamat = $i->post('alamat');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');
		$dt_harga = $i->post('dt_harga');
		$dt_jumlah = $i->post('dt_jml');
		$dt_jumlah_hpp = $i->post('dt_jml_hpp');
		$dt_hpp = $i->post('dt_hpp');

		$decode_id = json_decode($dt_id, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);
		$decode_harga = json_decode($dt_harga, TRUE);
		$decode_jumlah = json_decode($dt_jumlah, TRUE);
		$decode_jumlah_hpp = json_decode($dt_jumlah_hpp, TRUE);
		$decode_hpp = json_decode($dt_hpp, TRUE);
		
		for ($y=0; $y < $len; $y++)
        {
           $total_jual = $total_jual + $decode_jumlah[$y];
           $total_hpp   = $total_hpp + $decode_jumlah_hpp[$y];
        }

        $total 		 	= $ongkir + $admin;
        $total_jual_2 	= $total_jual + $total;
        $total_harga	= $total_jual;

        if ($resi != '') {
        	$cekResi = $this->Resi_model->get_by_resi($resi);
			if (!isset($cekResi)) {
				$now = date('Y-m-d H:i:s');
	    		$data = array(	'nomor_resi'	=> $resi,
								'id_users' 		=> $this->session->userdata('id_users'), 
								'status' 		=> 0,
								'tgl_resi' 		=> $now 
					);
				$this->Resi_model->insert($data);

				write_log();
	    	}	
        }

        $cek_no_pesanan = $this->Keluar_model->get_by_id($no_pesanan);
        if (isset($cek_no_pesanan)) {
        	if ($cek_no_pesanan->id_status_transaksi != 3) {
        		if ($status == 3) {
					$tgl_diterima = $now;
					$fix_diterima = $diterima;

					$bruto = $total_jual_2;
					$harga = $total_harga;
					$hpp = $total_hpp;

					$hasil_harga_diterima = $harga - $fix_diterima;
					
					if ($hasil_harga_diterima <= 0) {
						$hasil_harga_diterima = $hasil_harga_diterima * -1;	
					
						if (($bruto - $fix_diterima) != $ongkir) {
							$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
						}else{
							$selisih_margin = 0;
						}

						if ($selisih_margin <= 0) {
							$new_bruto = $bruto + (int) $selisih_margin;	
							$margin = $fix_diterima  - $hpp;
						}else{
							$new_bruto = $bruto - (int) $selisih_margin;
							$margin = $fix_diterima  - $hpp;
						}
					}else{
						if (($bruto - $fix_diterima) != $ongkir) {
							$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
						}else{
							$selisih_margin = 0;
						}

						if ($selisih_margin <= 0) {
							$new_bruto = $bruto + (int) $selisih_margin;	
							$margin = $fix_diterima  - $hpp;
						}else{
							$new_bruto = $bruto - (int) $selisih_margin;
							$margin = $fix_diterima - $hpp;
						}
					}		

					$data = array(	'id_users' 				=> $this->session->userdata('id_users'),
									'tgl_penjualan'			=> date('Y-m-d H:i:s', strtotime($tgl_penjualan)),
									'created'				=> date('Y-m-d H:i:s', strtotime($tgl_import)),
		        					'id_toko'				=> $toko,
									'id_kurir'	 			=> $kurir,
									'id_status_transaksi'	=> $status,
									'nomor_resi' 			=> $resi,
									'nama_penerima'			=> $nama_penerima,
									'hp_penerima' 			=> $hp_penerima, 
									'provinsi'	 			=> $provinsi,
									'kabupaten' 			=> $kabupaten,
									'alamat_penerima'		=> $alamat,
									'ongkir'				=> $ongkir,
									'biaya_admin'			=> $admin,
									'harga_jual' 			=> $total_harga,
									'total_hpp'				=> $total_hpp,
									'total_jual'			=> $new_bruto,
									'total_harga' 			=> $total_harga,
									'margin' 				=> $margin,
									'selisih_margin' 		=> $selisih_margin,
									'jumlah_diterima' 		=> $fix_diterima,
									'tgl_diterima' 			=> $tgl_diterima
					);

		        	// echo print_r($data);
					$this->Keluar_model->update($no_pesanan, $data);

					write_log();
				}else{
					$tgl_diterima = NULL;
					$fix_diterima = 0;

					$data = array(	'id_users' 				=> $this->session->userdata('id_users'),
        					'id_toko'				=> $toko,
							'tgl_penjualan'			=> date('Y-m-d H:i:s', strtotime($tgl_penjualan)),
							'created'				=> date('Y-m-d H:i:s', strtotime($tgl_import)),
							'id_kurir'	 			=> $kurir,
							'id_status_transaksi'	=> $status,
							'nomor_resi' 			=> $resi,
							'nama_penerima'			=> $nama_penerima,
							'hp_penerima' 			=> $hp_penerima, 
							'provinsi'	 			=> $provinsi,
							'kabupaten' 			=> $kabupaten,
							'alamat_penerima'		=> $alamat,
							'ongkir'				=> $ongkir,
							'biaya_admin'			=> $admin,
							'harga_jual' 			=> $total_harga,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $total_jual_2,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $total_jual_2 - $total - $total_hpp,
							'selisih_margin' 		=> $total_harga - $total_jual_2,
							'jumlah_diterima' 		=> $fix_diterima,
							'tgl_diterima' 			=> $tgl_diterima
					);

		        	// echo print_r($data);
					$this->Keluar_model->update($no_pesanan, $data);

					write_log();
				}
        	}else{
        		if ($status == 3) {
					$fix_diterima = $diterima;

					$bruto = $total_jual_2;
					$harga = $total_harga;
					$hpp = $total_hpp;

					$hasil_harga_diterima = $harga - $fix_diterima;
					
					if ($hasil_harga_diterima <= 0) {
						$hasil_harga_diterima = $hasil_harga_diterima * -1;	
					
						if (($bruto - $fix_diterima) != $ongkir) {
							$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
						}else{
							$selisih_margin = 0;
						}

						if ($selisih_margin <= 0) {
							$new_bruto = $bruto + (int) $selisih_margin;	
							$margin = $fix_diterima  - $hpp;
						}else{
							$new_bruto = $bruto - (int) $selisih_margin;
							$margin = $fix_diterima  - $hpp;
						}
					}else{
						if (($bruto - $fix_diterima) != $ongkir) {
							$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
						}else{
							$selisih_margin = 0;
						}

						if ($selisih_margin <= 0) {
							$new_bruto = $bruto + (int) $selisih_margin;	
							$margin = $fix_diterima  - $hpp;
						}else{
							$new_bruto = $bruto - (int) $selisih_margin;
							$margin = $fix_diterima - $hpp;
						}
					}		

					$data = array(	'id_users' 			=> $this->session->userdata('id_users'),
        					'id_toko'				=> $toko,
							'tgl_penjualan'			=> date('Y-m-d H:i:s', strtotime($tgl_penjualan)),
							'created'				=> date('Y-m-d H:i:s', strtotime($tgl_import)),
							'id_kurir'	 			=> $kurir,
							'id_status_transaksi'	=> $status,
							'nomor_resi' 			=> $resi,
							'nama_penerima'			=> $nama_penerima,
							'hp_penerima' 			=> $hp_penerima, 
							'provinsi'	 			=> $provinsi,
							'kabupaten' 			=> $kabupaten,
							'alamat_penerima'		=> $alamat,
							'ongkir'				=> $ongkir,
							'biaya_admin'			=> $admin,
							'harga_jual' 			=> $total_harga,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $new_bruto,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $margin,
							'selisih_margin' 		=> $selisih_margin,
							'jumlah_diterima' 		=> $fix_diterima,
					);

		        	// echo print_r($data);
					$this->Keluar_model->update($no_pesanan, $data);

					write_log();
				}else{
					$tgl_diterima = NULL;
					$fix_diterima = 0;

					$data = array(	'id_users' 				=> $this->session->userdata('id_users'),
        					'id_toko'				=> $toko,
							'tgl_penjualan'			=> date('Y-m-d H:i:s', strtotime($tgl_penjualan)),
							'created'				=> date('Y-m-d H:i:s', strtotime($tgl_import)),
							'id_kurir'	 			=> $kurir,
							'id_status_transaksi'	=> $status,
							'nomor_resi' 			=> $resi,
							'nama_penerima'			=> $nama_penerima,
							'hp_penerima' 			=> $hp_penerima, 
							'provinsi'	 			=> $provinsi,
							'kabupaten' 			=> $kabupaten,
							'alamat_penerima'		=> $alamat,
							'ongkir'				=> $ongkir,
							'biaya_admin'			=> $admin,
							'harga_jual' 			=> $total_harga,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $total_jual_2,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $total_harga - $total - $total_hpp,
							'selisih_margin' 		=> $total_harga - $total_jual_2,
							'jumlah_diterima' 		=> $fix_diterima,
							'tgl_diterima' 			=> $tgl_diterima
					);

		        	// echo print_r($data);
					$this->Keluar_model->update($no_pesanan, $data);

					write_log();
				}
        	}

			// tambah barang 
			$cariDetail = $this->Keluar_model->get_detail_by_id($no_pesanan);	
			foreach ($cariDetail as $produk) {
				// START - Kurangin jumlah total jika ada didalam paket
	          	$cariPaket = $this->Produk_model->get_all_by_id($produk->id_produk);
	          	if (isset($cariPaket)) {
	          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          		if (count($produkPaket) > 0) {
	          			// echo "Isi 2";
	          			foreach ($produkPaket as $result) {
	          				$total = $result->qty_pakduk * $produk->qty;
		          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk + $total
							          	);
							$this->Produk_model->update($result->id_produk, $kurangStokPakduk);

							write_log();	
	          			}
	          		}
	          	}
	          	// END
				$nambahStok = array( 	'qty_produk' 		=> $produk->qty_produk + $produk->qty
									);
				$this->Produk_model->update($produk->id_produk, $nambahStok);

				write_log();
			}
			// hapus detail penjualan
			
			$this->Keluar_model->delete_detail($no_pesanan);

			for ($n=0; $n < $len; $n++)
	        {
	          	$dataDetail[$n] = array(	'nomor_pesanan'	=> $no_pesanan,
											'id_produk' 	=> $decode_id[$n],
											'qty'			=> $decode_qty[$n],
											'harga' 		=> $decode_harga[$n],
											'hpp'	 		=> $decode_hpp[$n],
								);

	          	$cariProduk[$n] = $this->Produk_model->get_by_id($decode_id[$n]);
	          	// START - Kurangin jumlah total jika ada didalam paket
	          	$cariPaket = $this->Produk_model->get_all_by_id($decode_id[$n]);
	          	// echo print_r($cariPaket);
	          	if (isset($cariPaket)) {
	          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          		if (count($produkPaket) > 0) {
	          			// echo "Isi 2";
	          			$resultPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          			foreach ($resultPaket as $result) {
	          				$total = $result->qty_pakduk * $decode_qty[$n];
		          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
							          	);
							$this->Produk_model->update($result->id_produk, $kurangStokPakduk);

							write_log();
	          			}
	          		}
	          	}
	          	// END
	          	$kurangStokProduk[$n] = array(	'qty_produk' 		=> $cariProduk[$n]->qty_produk - $decode_qty[$n]
						          	);

	          	$this->Produk_model->update($decode_id[$n], $kurangStokProduk[$n]);

	          	write_log();
				
				$this->Keluar_model->insert_detail($dataDetail[$n]);

				write_log();
	        }

	        $pesan = "Berhasil diubah!";	
        	$msg = array(	'sukses'	=> $pesan
        			);
        	echo json_encode($msg);
        }   
	}

	public function ubah_sementara_proses()
	{
		date_default_timezone_set("Asia/Jakarta");
		$now = date('Y-m-d H:i:s');
		// Penjumlahan
		$total_jual = 0;
		$total_hpp 	 = 0;	

		// Ambil Data
		$i = $this->input;

		$len = $i->post('length');
		$tgl_penjualan = $i->post('tgl_penjualan');
		$tgl_import = $i->post('tgl_import');
		$toko = $i->post('toko');
		$kurir = $i->post('kurir');
		$diterima = $i->post('diterima');
		$status = $i->post('status');
		$admin = intval($i->post('admin'));
		$ongkir = intval($i->post('ongkir'));
		$resi = $i->post('resi');
		$no_pesanan = $i->post('no_pesanan');
		$nama_penerima = $i->post('nama_penerima');
		$hp_penerima = $i->post('hp_penerima');
		$provinsi = $i->post('provinsi');
		$kabupaten = $i->post('kabupaten');
		$alamat = $i->post('alamat');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');
		$dt_harga = $i->post('dt_harga');
		$dt_jumlah = $i->post('dt_jml');
		$dt_jumlah_hpp = $i->post('dt_jml_hpp');
		$dt_hpp = $i->post('dt_hpp');

		$decode_id = json_decode($dt_id, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);
		$decode_harga = json_decode($dt_harga, TRUE);
		$decode_jumlah = json_decode($dt_jumlah, TRUE);
		$decode_jumlah_hpp = json_decode($dt_jumlah_hpp, TRUE);
		$decode_hpp = json_decode($dt_hpp, TRUE);
		
		if ($resi != '') {
        	$cekResi = $this->Resi_model->get_by_resi($resi);
			if (!isset($cekResi)) {
				$now = date('Y-m-d H:i:s');
	    		$data = array(	'nomor_resi'	=> $resi,
								'id_users' 		=> $this->session->userdata('id_users'), 
								'status' 		=> 0,
								'tgl_resi' 		=> $now 
					);
				$this->Resi_model->insert($data);

				write_log();
	    	}	
        }

        for ($y=0; $y < $len; $y++)
        {
           $total_jual = $total_jual + $decode_jumlah[$y];
           $total_hpp   = $total_hpp + $decode_jumlah_hpp[$y];
        }

        $total 		 	= $ongkir + $admin;
        $total_jual_2 	= $total_jual + $total;
        $total_harga	= $total_jual;

        $cek_no_pesanan = $this->Keluar_sementara_model->get_by_id($no_pesanan);
        if (isset($cek_no_pesanan)) {
        	if ($cek_no_pesanan->id_status_transaksi != 3) {
        		if ($status == 3) {
					$tgl_diterima = $now;
					$fix_diterima = $diterima;

					$bruto = $total_jual_2;
					$harga = $total_harga;
					$hpp = $total_hpp;

					$hasil_harga_diterima = $harga - $fix_diterima;
					
					if ($hasil_harga_diterima <= 0) {
						$hasil_harga_diterima = $hasil_harga_diterima * -1;	
					
						if (($bruto - $fix_diterima) != $ongkir) {
							$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
						}else{
							$selisih_margin = 0;
						}

						if ($selisih_margin <= 0) {
							$new_bruto = $bruto + (int) $selisih_margin;	
							$margin = $fix_diterima  - $hpp;
						}else{
							$new_bruto = $bruto - (int) $selisih_margin;
							$margin = $fix_diterima  - $hpp;
						}
					}else{
						if (($bruto - $fix_diterima) != $ongkir) {
							$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
						}else{
							$selisih_margin = 0;
						}

						if ($selisih_margin <= 0) {
							$new_bruto = $bruto + (int) $selisih_margin;	
							$margin = $fix_diterima  - $hpp;
						}else{
							$new_bruto = $bruto - (int) $selisih_margin;
							$margin = $fix_diterima - $hpp;
						}
					}		

					$data = array(	'id_users' 				=> $this->session->userdata('id_users'),
        					'id_toko'				=> $toko,
							'tgl_penjualan'			=> date('Y-m-d H:i:s', strtotime($tgl_penjualan)),
							'created'				=> date('Y-m-d H:i:s', strtotime($tgl_import)),
							'id_kurir'	 			=> $kurir,
							'id_status_transaksi'	=> $status,
							'nomor_resi' 			=> $resi,
							'nama_penerima'			=> $nama_penerima,
							'hp_penerima' 			=> $hp_penerima, 
							'provinsi'	 			=> $provinsi,
							'kabupaten' 			=> $kabupaten,
							'alamat_penerima'		=> $alamat,
							'ongkir'				=> $ongkir,
							'biaya_admin'			=> $admin,
							'harga_jual' 			=> $total_harga,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $new_bruto,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $margin,
							'selisih_margin' 		=> $selisih_margin,
							'jumlah_diterima' 		=> $fix_diterima,
							'tgl_diterima' 			=> $tgl_diterima
					);

		        	// echo print_r($data);
					$this->Keluar_sementara_model->update($no_pesanan, $data);

					write_log();
				}else{
					$tgl_diterima = NULL;
					$fix_diterima = 0;

					$data = array(	'id_users' 				=> $this->session->userdata('id_users'),
        					'id_toko'				=> $toko,
							'tgl_penjualan'			=> date('Y-m-d H:i:s', strtotime($tgl_penjualan)),
							'created'				=> date('Y-m-d H:i:s', strtotime($tgl_import)),
							'id_kurir'	 			=> $kurir,
							'id_status_transaksi'	=> $status,
							'nomor_resi' 			=> $resi,
							'nama_penerima'			=> $nama_penerima,
							'hp_penerima' 			=> $hp_penerima, 
							'provinsi'	 			=> $provinsi,
							'kabupaten' 			=> $kabupaten,
							'alamat_penerima'		=> $alamat,
							'ongkir'				=> $ongkir,
							'biaya_admin'			=> $admin,
							'harga_jual' 			=> $total_harga,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $total_jual_2,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $total_jual_2 - $total - $total_hpp,
							'selisih_margin' 		=> $total_harga - $total_jual_2,
							'jumlah_diterima' 		=> $fix_diterima,
							'tgl_diterima' 			=> $tgl_diterima
					);

		        	// echo print_r($data);
					$this->Keluar_sementara_model->update($no_pesanan, $data);

					write_log();
				}
        	}else{
        		if ($status == 3) {
					$fix_diterima = $diterima;

					$bruto = $total_jual_2;
					$harga = $total_harga;
					$hpp = $total_hpp;

					$hasil_harga_diterima = $harga - $fix_diterima;
					
					if ($hasil_harga_diterima <= 0) {
						$hasil_harga_diterima = $hasil_harga_diterima * -1;	
					
						if (($bruto - $fix_diterima) != $ongkir) {
							$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
						}else{
							$selisih_margin = 0;
						}

						if ($selisih_margin <= 0) {
							$new_bruto = $bruto + (int) $selisih_margin;	
							$margin = $fix_diterima  - $hpp;
						}else{
							$new_bruto = $bruto - (int) $selisih_margin;
							$margin = $fix_diterima  - $hpp;
						}
					}else{
						if (($bruto - $fix_diterima) != $ongkir) {
							$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
						}else{
							$selisih_margin = 0;
						}

						if ($selisih_margin <= 0) {
							$new_bruto = $bruto + (int) $selisih_margin;	
							$margin = $fix_diterima  - $hpp;
						}else{
							$new_bruto = $bruto - (int) $selisih_margin;
							$margin = $fix_diterima - $hpp;
						}
					}		

					$data = array(	'id_users' 			=> $this->session->userdata('id_users'),
        					'id_toko'				=> $toko,
							'tgl_penjualan'			=> date('Y-m-d H:i:s', strtotime($tgl_penjualan)),
							'created'				=> date('Y-m-d H:i:s', strtotime($tgl_import)),
							'id_kurir'	 			=> $kurir,
							'id_status_transaksi'	=> $status,
							'nomor_resi' 			=> $resi,
							'nama_penerima'			=> $nama_penerima,
							'hp_penerima' 			=> $hp_penerima, 
							'provinsi'	 			=> $provinsi,
							'kabupaten' 			=> $kabupaten,
							'alamat_penerima'		=> $alamat,
							'ongkir'				=> $ongkir,
							'biaya_admin'			=> $admin,
							'harga_jual' 			=> $total_harga,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $new_bruto,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $margin,
							'selisih_margin' 		=> $selisih_margin,
							'jumlah_diterima' 		=> $fix_diterima,
					);

		        	// echo print_r($data);
					$this->Keluar_sementara_model->update($no_pesanan, $data);

					write_log();
				}else{
					$tgl_diterima = NULL;
					$fix_diterima = 0;

					$data = array(	'id_users' 				=> $this->session->userdata('id_users'),
        					'id_toko'				=> $toko,
							'tgl_penjualan'			=> date('Y-m-d H:i:s', strtotime($tgl_penjualan)),
							'created'				=> date('Y-m-d H:i:s', strtotime($tgl_import)),
							'id_kurir'	 			=> $kurir,
							'id_status_transaksi'	=> $status,
							'nomor_resi' 			=> $resi,
							'nama_penerima'			=> $nama_penerima,
							'hp_penerima' 			=> $hp_penerima, 
							'provinsi'	 			=> $provinsi,
							'kabupaten' 			=> $kabupaten,
							'alamat_penerima'		=> $alamat,
							'ongkir'				=> $ongkir,
							'biaya_admin'			=> $admin,
							'harga_jual' 			=> $total_harga,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $total_jual_2,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $total_harga - $total - $total_hpp,
							'selisih_margin' 		=> $total_harga - $total_jual_2,
							'jumlah_diterima' 		=> $fix_diterima,
							'tgl_diterima' 			=> $tgl_diterima
					);

		        	// echo print_r($data);
					$this->Keluar_sementara_model->update($no_pesanan, $data);

					write_log();
				}
        	}

			// tambah barang 
			$cariDetail = $this->Keluar_sementara_model->get_detail_by_id($no_pesanan);	
			foreach ($cariDetail as $produk) {
				// START - Kurangin jumlah total jika ada didalam paket
	          	$cariPaket = $this->Produk_model->get_all_by_id($produk->id_produk);
	          	if (isset($cariPaket)) {
	          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          		if (count($produkPaket) > 0) {
	          			// echo "Isi 2";
	          			foreach ($produkPaket as $result) {
	          				$total = $result->qty_pakduk * $produk->qty;
		          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk + $total
							          	);
							$this->Produk_model->update($result->id_produk, $kurangStokPakduk);

							write_log();	
	          			}
	          		}
	          	}
	          	// END
				$nambahStok = array( 	'qty_produk' 		=> $produk->qty_produk + $produk->qty
									);
				$this->Produk_model->update($produk->id_produk, $nambahStok);

				write_log();
			}
			// hapus detail penjualan
			
			$this->Keluar_sementara_model->delete_detail($no_pesanan);

			for ($n=0; $n < $len; $n++)
	        {
	          	$dataDetail[$n] = array(	'nomor_pesanan'	=> $no_pesanan,
											'id_produk' 	=> $decode_id[$n],
											'qty'			=> $decode_qty[$n],
											'harga' 		=> $decode_harga[$n],
											'hpp'	 		=> $decode_hpp[$n],
								);

	          	$cariProduk[$n] = $this->Produk_model->get_by_id($decode_id[$n]);
	          	// START - Kurangin jumlah total jika ada didalam paket
	          	$cariPaket = $this->Produk_model->get_all_by_id($decode_id[$n]);
	          	// echo print_r($cariPaket);
	          	if (isset($cariPaket)) {
	          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          		if (count($produkPaket) > 0) {
	          			// echo "Isi 2";
	          			$resultPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          			foreach ($resultPaket as $result) {
	          				$total = $result->qty_pakduk * $decode_qty[$n];
		          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
							          	);
							$this->Produk_model->update($result->id_produk, $kurangStokPakduk);

							write_log();
	          			}
	          		}
	          	}
	          	// END
	          	$kurangStokProduk[$n] = array(	'qty_produk' 		=> $cariProduk[$n]->qty_produk - $decode_qty[$n]
						          	);

	          	$this->Produk_model->update($decode_id[$n], $kurangStokProduk[$n]);

	          	write_log();
				
				$this->Keluar_sementara_model->insert_detail($dataDetail[$n]);

				write_log();
	        }

	        $pesan = "Berhasil diubah!";	
        	$msg = array(	'sukses'	=> $pesan
        			);
        	echo json_encode($msg);
        } 
	}

	public function penjualan_produk()
	{
		is_create();    

		$this->data['action_impor']     = 'admin/keluar/proses_impor';
		$this->data['action_restore']  = 'admin/keluar/restore_db';
	    $this->data['page_title']					= 'Create & Import New '.$this->data['module'];
	    $this->data['get_all_toko'] 				= $this->Keluar_model->get_all_toko();
	    $this->data['get_all_kurir'] 				= $this->Keluar_model->get_all_kurir();
	    $this->data['get_all_status']				= $this->Keluar_model->get_all_status();
	    $this->data['get_all_provinsi'] 			= $this->Keyword_model->get_all_provinsi_combobox();

	    $this->data['nomor_pesanan'] = [
	      'name'          => 'nomor_pesanan',
	      'id'            => 'nomor-pesanan',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['nama_penerima'] = [
	      'name'          => 'nama_penerima',
	      'id'            => 'nama-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['hp_penerima'] = [
	      'name'          => 'hp_penerima',
	      'id'            => 'hp-penerima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['ongkir'] = [
	      'name'          => 'ongkir',
	      'id'            => 'ongkir',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['biaya_admin'] = [
	      'name'          => 'biaya_admin',
	      'id'            => 'biaya-admin',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	    ];

	    $this->data['nomor_resi'] = [
	      'name'          => 'nomor_resi',
	      'id'            => 'nomor-resi',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	    ];

	    $this->data['toko'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'toko',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['provinsi'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'provinsi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kabupaten'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kabupaten',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['produk'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'produk',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kurir'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kurir',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status-transaksi',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['generate_no'] = [
	        'class' 		=> 'minimal',
	        // 'checked'       => FALSE,	
	        'style'         => 'margin-right:50px'
	    ];

	    $this->data['alamat'] = [
	      'name'          => 'alamat',
	      'id'            => 'alamat',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->data['diterima'] = [
	      'name'          => 'diterima',
	      'id'            => 'diterima',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->load->view('back/keluar/penjualan_produk_add', $this->data);
	}

	public function penjualan_produk_proses()
	{
		date_default_timezone_set("Asia/Jakarta");
		$now = date('Y-m-d H:i:s');
		// Penjumlahan
		$total_jual = 0;
		$total_hpp 	 = 0;	

		// Ambil Data
		$i = $this->input;

		$len = $i->post('length');
		$toko = $i->post('toko');
		$kurir = $i->post('kurir');
		$status_transaksi = $i->post('status');
		$admin = intval($i->post('admin'));
		$ongkir = intval($i->post('ongkir'));
		$resi = $i->post('resi');
		$diterima = $i->post('diterima');
		$no_pesanan = $i->post('no_pesanan');
		$nama_penerima = $i->post('nama_penerima');
		$hp_penerima = $i->post('hp_penerima');
		$provinsi = $i->post('provinsi');
		$kabupaten = $i->post('kabupaten');
		$alamat = $i->post('alamat');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');
		$dt_harga = $i->post('dt_harga');
		$dt_jumlah = $i->post('dt_jml');
		$dt_jumlah_hpp = $i->post('dt_jml_hpp');
		$dt_hpp = $i->post('dt_hpp');

		if ($diterima != '' || $diterima != NULL) {
			$tgl_diterima = $now;
			$fix_diterima = $diterima;

			$decode_id = json_decode($dt_id, TRUE);
			$decode_qty = json_decode($dt_qty, TRUE);
			$decode_harga = json_decode($dt_harga, TRUE);
			$decode_jumlah = json_decode($dt_jumlah, TRUE);
			$decode_jumlah_hpp = json_decode($dt_jumlah_hpp, TRUE);
			$decode_hpp = json_decode($dt_hpp, TRUE);
			
			for ($y=0; $y < $len; $y++)
	        {
	           $total_jual = $total_jual + $decode_jumlah[$y];
	           $total_hpp   = $total_hpp + $decode_jumlah_hpp[$y];
	        }

	        $total 		 	= $ongkir + $admin;
	        $total_jual_2 	= $total_jual + $total;
	        $total_harga	= $total_jual;

	        $bruto = $total_jual;
			$harga = $total_harga;
			$hpp = $total_hpp;

			$hasil_harga_diterima = $harga - $fix_diterima;
			
			if ($hasil_harga_diterima <= 0) {
				$hasil_harga_diterima = $hasil_harga_diterima * -1;	
			
				if (($bruto - $fix_diterima) != $ongkir) {
					$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
				}else{
					$selisih_margin = 0;
				}

				if ($selisih_margin <= 0) {
					$new_bruto = $bruto + (int) $selisih_margin;	
					$margin = $fix_diterima  - $hpp;
				}else{
					$new_bruto = $bruto - (int) $selisih_margin;
					$margin = $fix_diterima  - $hpp;
				}
			}else{
				if (($bruto - $fix_diterima) != $ongkir) {
					$selisih_margin = ($bruto - $fix_diterima - $ongkir) * -1;
				}else{
					$selisih_margin = 0;
				}

				if ($selisih_margin <= 0) {
					$new_bruto = $bruto + (int) $selisih_margin;	
					$margin = $fix_diterima  - $hpp;
				}else{
					$new_bruto = $bruto - (int) $selisih_margin;
					$margin = $fix_diterima - $hpp;
				}
			}

	        $cek_no_pesanan = $this->Keluar_model->get_by_id($no_pesanan);
	        if (isset($cek_no_pesanan)) {
	        	$pesan = "No. Pesanan sudah ada";	
	        	$msg = array(	'validasi'	=> $pesan
	        			);
	        	echo json_encode($msg); 
	        }else{
	        	// echo $total." ".$total_jual." ".$total_harga." ".$total_jual_2;
	        	$data = array(	'nomor_pesanan'			=> $no_pesanan,
				        		'id_users' 				=> $this->session->userdata('id_users'),
	        					'id_toko'				=> $toko,
								'id_kurir'	 			=> $kurir,
								'id_status_transaksi' 	=> $status_transaksi,
								'nomor_resi' 			=> $resi,
								'nama_penerima'			=> $nama_penerima,
								'hp_penerima'			=> $hp_penerima,
								'provinsi'	 			=> $provinsi,
								'kabupaten' 			=> $kabupaten,
								'alamat_penerima'		=> $alamat,
								'ongkir'				=> $ongkir,
								'biaya_admin'			=> $admin,
								'harga_jual' 			=> $total_harga,
								'total_hpp'				=> $total_hpp,
								'total_jual'			=> $new_bruto,
								'total_harga' 			=> $total_harga,
								'margin' 				=> $margin,
								'selisih_margin' 		=> $selisih_margin,
								'jumlah_diterima' 		=> $fix_diterima,
								'tgl_diterima' 			=> $tgl_diterima,
								'created' 				=> $now	
						);

				$this->Keluar_model->insert($data);

				write_log();

				if ($resi != '') {
		        	$now = date('Y-m-d H:i:s');
		        	$data = array(	'nomor_resi'	=> $resi,
									'id_users' 		=> $this->session->userdata('id_users'), 
									'status' 		=> 0,
									'tgl_resi' 	  	=> $now 
						);
					$this->Resi_model->insert($data);

					write_log();
		        }

				for ($n=0; $n < $len; $n++)
		        {
		          	$dataDetail[$n] = array(	'nomor_pesanan'	=> $no_pesanan,
												'id_produk' 	=> $decode_id[$n],
												'qty'			=> $decode_qty[$n],
												'harga' 		=> $decode_harga[$n],
												'hpp'	 		=> $decode_hpp[$n],
									);

		          	$cariProduk[$n] = $this->Produk_model->get_by_id($decode_id[$n]);
		          	// START - Kurangin jumlah total jika ada didalam paket
		          	$cariPaket[$n] = $this->Produk_model->get_all_by_id($decode_id[$n]);
		          	if (isset($cariPaket[$n])) {
		          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket[$n]->id_paket)->result();
		          		if (count($produkPaket) > 1) {
		          			// echo print_r($produkPaket);
		          			foreach ($produkPaket as $result) {
		          				$total = $result->qty_pakduk * $decode_qty[$n];
			          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
								          	);
								$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	
		          			}
		          		}else{
		          			$rowPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket[$n]->id_paket)->row();
		          			$total = $rowPaket->qty_pakduk * $decode_qty[$n];
		          			$kurangStokPakduk = array(	'qty_produk' 		=> $rowPaket->qty_produk - $total
							          	);
							$this->Produk_model->update($rowPaket->id_produk, $kurangStokPakduk);
		          		}
		          	}
		          	// END
		          	$kurangStokProduk[$n] = array(	'qty_produk' 		=> $cariProduk[$n]->qty_produk - $decode_qty[$n]
							          	);

		          	$this->Produk_model->update($decode_id[$n], $kurangStokProduk[$n]);

		          	write_log();

					$this->Keluar_model->insert_detail($dataDetail[$n]);

					write_log();
		        }

		        $pesan = "Berhasil disimpan!";	
	        	$msg = array(	'sukses'	=> $pesan
	        			);
	        	echo json_encode($msg);
	        } 
		}else{
			$tgl_diterima = NULL;
			$fix_diterima = 0;

			$decode_id = json_decode($dt_id, TRUE);
			$decode_qty = json_decode($dt_qty, TRUE);
			$decode_harga = json_decode($dt_harga, TRUE);
			$decode_jumlah = json_decode($dt_jumlah, TRUE);
			$decode_jumlah_hpp = json_decode($dt_jumlah_hpp, TRUE);
			$decode_hpp = json_decode($dt_hpp, TRUE);
			
			for ($y=0; $y < $len; $y++)
	        {
	           $total_jual = $total_jual + $decode_jumlah[$y];
	           $total_hpp   = $total_hpp + $decode_jumlah_hpp[$y];
	        }

	        $total 		 	= $ongkir + $admin;
	        $total_jual_2 	= $total_jual + $total;
	        $total_harga	= $total_jual;

	        $cek_no_pesanan = $this->Keluar_model->get_by_id($no_pesanan);
	        if (isset($cek_no_pesanan)) {
	        	$pesan = "No. Pesanan sudah ada";	
	        	$msg = array(	'validasi'	=> $pesan
	        			);
	        	echo json_encode($msg); 
	        }else{
	        	// echo $total." ".$total_jual." ".$total_harga." ".$total_jual_2;
	        	$data = array(	'nomor_pesanan'			=> $no_pesanan,
				        		'id_users' 				=> $this->session->userdata('id_users'),
	        					'id_toko'				=> $toko,
								'id_kurir'	 			=> $kurir,
								'id_status_transaksi' 	=> $status_transaksi,
								'nomor_resi' 			=> $resi,
								'nama_penerima'			=> $nama_penerima,
								'hp_penerima'			=> $hp_penerima,
								'provinsi'	 			=> $provinsi,
								'kabupaten' 			=> $kabupaten,
								'alamat_penerima'		=> $alamat,
								'ongkir'				=> $ongkir,
								'biaya_admin'			=> $admin,
								'harga_jual' 			=> $total_harga,
								'total_hpp'				=> $total_hpp,
								'total_jual'			=> $total_jual_2,
								'total_harga' 			=> $total_harga,
								'margin' 				=> $total_jual_2 - $total - $total_hpp,
								'selisih_margin' 		=> $total_harga - $total_jual_2,
								'jumlah_diterima' 		=> $fix_diterima,
								'tgl_diterima' 			=> $tgl_diterima,
								'created' 				=> $now	
						);

				$this->Keluar_model->insert($data);

				write_log();

				if ($resi != '') {
		        	$now = date('Y-m-d H:i:s');
		        	$data = array(	'nomor_resi'	=> $resi,
									'id_users' 		=> $this->session->userdata('id_users'), 
									'status' 		=> 0,
									'tgl_resi' 	  	=> $now 
						);
					$this->Resi_model->insert($data);

					write_log();
		        }

				for ($n=0; $n < $len; $n++)
		        {
		          	$dataDetail[$n] = array(	'nomor_pesanan'	=> $no_pesanan,
												'id_produk' 	=> $decode_id[$n],
												'qty'			=> $decode_qty[$n],
												'harga' 		=> $decode_harga[$n],
												'hpp'	 		=> $decode_hpp[$n],
									);

		          	$cariProduk[$n] = $this->Produk_model->get_by_id($decode_id[$n]);
		          	// START - Kurangin jumlah total jika ada didalam paket
		          	$cariPaket[$n] = $this->Produk_model->get_all_by_id($decode_id[$n]);
		          	if (isset($cariPaket[$n])) {
		          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket[$n]->id_paket)->result();
		          		if (count($produkPaket) > 1) {
		          			// echo print_r($produkPaket);
		          			foreach ($produkPaket as $result) {
		          				$total = $result->qty_pakduk * $decode_qty[$n];
			          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
								          	);
								$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	
		          			}
		          		}else{
		          			$rowPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket[$n]->id_paket)->row();
		          			$total = $rowPaket->qty_pakduk * $decode_qty[$n];
		          			$kurangStokPakduk = array(	'qty_produk' 		=> $rowPaket->qty_produk - $total
							          	);
							$this->Produk_model->update($rowPaket->id_produk, $kurangStokPakduk);
		          		}
		          	}
		          	// END
		          	$kurangStokProduk[$n] = array(	'qty_produk' 		=> $cariProduk[$n]->qty_produk - $decode_qty[$n]
							          	);

		          	$this->Produk_model->update($decode_id[$n], $kurangStokProduk[$n]);

		          	write_log();

					$this->Keluar_model->insert_detail($dataDetail[$n]);

					write_log();
		        }

		        $pesan = "Berhasil disimpan!";	
	        	$msg = array(	'sukses'	=> $pesan
	        			);
	        	echo json_encode($msg);
	        } 
		}
	}

	public function penjualan_hapus_dipilih()
	{
		is_delete();

		$nomor_pesanan = explode(",", $this->input->post('ids'));

		$i = 0;
		$cekDetail = $this->Keluar_model->get_detail_by_id_in($nomor_pesanan);
		if(isset($cekDetail))
		{
		  foreach ($cekDetail as $detail) {
				// Hapus Data Resi sesuai Resi (FIX)
			  	$cekResi = $this->Resi_model->get_by_resi($detail->nomor_resi);
			  	if (isset($cekResi)) {
			  		$this->Resi_model->delete_by_resi($detail->nomor_resi);
					write_log();
			  	}
				
				$row_produk = $this->Produk_model->get_by_id($detail->id_produk);
		  		$tambahStok = array( 	'qty_produk' 		=> $row_produk->qty_produk + $detail->qty
								);

				$this->Produk_model->update($row_produk->id_produk, $tambahStok);	

				write_log();

				// START - Kurangin jumlah total jika ada didalam paket
	          	$cariPaket = $this->Produk_model->get_all_by_id($detail->id_produk);
	          	if (isset($cariPaket)) {
	          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          		if (count($produkPaket) > 0) {
	          			// echo "Isi 2";
	          			foreach ($produkPaket as $result) {
	          				$total = $result->qty_pakduk * $detail->qty;
		          			$kurangStokPakduk[$i] = array(	'qty_produk' 		=> $result->qty_produk + $total
							          	);
							$this->Produk_model->update($result->id_produk, $kurangStokPakduk[$i]);	

							write_log();
	          			}
	          		}
	          	}
	          	// END		
	          	$i++;

				// $n++;
			}

		  $this->Keluar_model->delete_detail_in($nomor_pesanan);

		  write_log();

		  $this->Keluar_model->delete_in($nomor_pesanan);

		  write_log();

		  $pesan = "Berhasil dihapus!";	
	      $msg = array(	'sukses'	=> $pesan
	    			);
	      echo json_encode($msg);
		
		}
	}

	public function penjualan_hapus_dipilih_sementara()
	{
		is_delete();

		$nomor_pesanan = explode(",", $this->input->post('ids'));

		$cariDetail = $this->Keluar_sementara_model->get_all_detail_by_id_in($nomor_pesanan);
		if(isset($cariDetail))
		{
		  $this->Keluar_sementara_model->delete_detail_in($nomor_pesanan);

		  write_log();

		  $this->Keluar_sementara_model->delete_in($nomor_pesanan);

		  write_log();

		  $pesan = "Berhasil dihapus!";	
	      $msg = array(	'sukses'	=> $pesan
	    			);
	      echo json_encode($msg);
		
		}
	}

	public function hapus($id = '')
	{
		is_delete();

		// $n = 0;
		$i = 0;
		$cekDetail = $this->Keluar_model->get_detail_by_id(base64_decode($id));
		if(isset($cekDetail))
		{
			foreach ($cekDetail as $detail) {
				// Hapus Data Resi sesuai Resi (FIX)
			  	$cekResi = $this->Resi_model->get_by_resi($detail->nomor_resi);
			  	if (isset($cekResi)) {
			  		$this->Resi_model->delete_by_resi($detail->nomor_resi);
					write_log();
			  	}
				
				$row_produk = $this->Produk_model->get_by_id($detail->id_produk);
		  		$tambahStok = array( 	'qty_produk' 		=> $row_produk->qty_produk + $detail->qty
								);

				$this->Produk_model->update($row_produk->id_produk, $tambahStok);	

				write_log();

				// START - Kurangin jumlah total jika ada didalam paket
	          	$cariPaket = $this->Produk_model->get_all_by_id($detail->id_produk);
	          	if (isset($cariPaket)) {
	          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          		if (count($produkPaket) > 0) {
	          			// echo "Isi 2";
	          			foreach ($produkPaket as $result) {
	          				$total = $result->qty_pakduk * $detail->qty;
		          			$kurangStokPakduk[$i] = array(	'qty_produk' 		=> $result->qty_produk + $total
							          	);
							$this->Produk_model->update($result->id_produk, $kurangStokPakduk[$i]);	

							write_log();
	          			}
	          		}
	          	}
	          	// END		
	          	$i++;
			}	

		  $this->Keluar_model->delete_detail(base64_decode($id));

		  write_log();

		  $this->Keluar_model->delete(base64_decode($id));

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/keluar/data_penjualan');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/keluar/data_penjualan');
		}
	}

	public function hapus_by_date()
	{
		is_create();
		$row = 0;
		$i = 0;
		$start 		 = substr($this->input->post('periodik'), 0, 10);
		$end 		 = substr($this->input->post('periodik'), 13, 24);
		$trigger  	 = $this->input->post('trigger');
		$cek_pesanan = $this->Keluar_model->get_all_by_periodik_sinkron($trigger, $start, $end);

		if (count($cek_pesanan) > 0) {
			foreach ($cek_pesanan as $val_pesanan) {
				$cekDetail = $this->Keluar_model->get_detail_by_id($val_pesanan->nomor_pesanan);
				foreach ($cekDetail as $detail) {
					// Hapus Data Resi sesuai Resi (FIX)
				  	$cekResi = $this->Resi_model->get_by_resi($detail->nomor_resi);
				  	if (isset($cekResi)) {
				  		$this->Resi_model->delete_by_resi($detail->nomor_resi);
						write_log();
				  	}
					
					$row_produk = $this->Produk_model->get_by_id($detail->id_produk);
			  		$tambahStok = array( 	'qty_produk' 		=> $row_produk->qty_produk + $detail->qty
									);

					$this->Produk_model->update($row_produk->id_produk, $tambahStok);	

					write_log();

					// START - Kurangin jumlah total jika ada didalam paket
		          	$cariPaket = $this->Produk_model->get_all_by_id($detail->id_produk);
		          	if (isset($cariPaket)) {
		          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
		          		if (count($produkPaket) > 0) {
		          			// echo "Isi 2";
		          			foreach ($produkPaket as $result) {
		          				$total = $result->qty_pakduk * $detail->qty;
			          			$kurangStokPakduk[$i] = array(	'qty_produk' 		=> $result->qty_produk + $total
								          	);
								$this->Produk_model->update($result->id_produk, $kurangStokPakduk[$i]);	

								write_log();
		          			}
		          		}
		          	}
		          	// END		
		          	$i++;
				}

				$this->Keluar_model->delete_detail($val_pesanan->nomor_pesanan);

				write_log();

				$this->Keluar_model->delete($val_pesanan->nomor_pesanan);

				write_log();

				$row++;
			}

			if ($trigger == 'impor') {
				$pesan = $row.' Data by Import Date successfully deleted!';	
		    	$msg = array(	'sukses'	=> $pesan
		    			);
		    	echo json_encode($msg);		
			}else if($trigger == 'penjualan'){
				$pesan = $row.' Data by Date of Sale successfully deleted!';	
		    	$msg = array(	'sukses'	=> $pesan
		    			);
		    	echo json_encode($msg);		
			}
		}else{
			$pesan = 'Data not found!';	
	    	$msg = array(	'validasi'	=> $pesan
	    			);
	    	echo json_encode($msg);	
		}
	}

	public function hapus_sementara($id = '')
	{
		is_delete();

		$cariDetail = $this->Keluar_sementara_model->get_all_detail_by_id(base64_decode($id));
		if(isset($cariDetail))
		{
		  $this->Keluar_sementara_model->delete_detail(base64_decode($id));

		  write_log();

		  $this->Keluar_sementara_model->delete(base64_decode($id));

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/keluar/data_sementara');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/keluar/data_sementara');
		}
	}

	public function hapusAll_sementara()
	{
		is_delete();
		
		$this->Keluar_sementara_model->deleteAll();

		write_log();

		$this->session->set_flashdata('message', '<div class="alert alert-success">All data deleted successfully</div>');
		redirect('admin/keluar/data_sementara');
	}

	public function impor_penjualan()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action_impor']     = 'admin/keluar/proses_impor';
		$this->data['action_restore']  = 'admin/keluar/restore_db';

	    $this->load->view('back/keluar/impor_penjualan', $this->data);
	}

	public function proses_impor_new()
	{
		$keyword = $this->input->post('keyword');

		if ($keyword == "no") {
			$config['upload_path'] 		= './uploads/';
			$config['allowed_types'] 	= 'xlsx|xls';
			$config['file_name']		= 'doc'.time();	
			$storing = array();
			$storing_fix = array();
			$baris = 0;
			$barisExcel= 2;
			// $config['max_size']  = '100';
			// $config['max_width']  = '1024';
			// $config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('impor_penjualan')) {
				$file 		= $this->upload->data();
				$reader 	= ReaderEntityFactory::createXLSXReader();

				$reader->open('uploads/'.$file['file_name']);
				$numSheet 	= 0;
				$validasi = 0;

				foreach ($reader->getSheetIterator() as $sheetXLS) {
					$numRow = 1;
					if ($numSheet == 0) {
						foreach ($sheetXLS->getRowIterator() as $row) {
							if ($numRow == 1) {
								if ($row->getCellAtIndex(0) != 'no_pesanan' || $row->getCellAtIndex(1) != 'tgl_penjualan' || $row->getCellAtIndex(2) != 'id_toko' || $row->getCellAtIndex(3) != 'id_kurir' || $row->getCellAtIndex(4) != 'nomor_resi' || $row->getCellAtIndex(5) != 'nama_penerima' || $row->getCellAtIndex(6) != 'hp_penerima' || $row->getCellAtIndex(7) != 'alamat_penerima' || $row->getCellAtIndex(8) != 'kabupaten' || $row->getCellAtIndex(9) != 'provinsi' || $row->getCellAtIndex(10) != 'ongkir' || $row->getCellAtIndex(11) != 'id_produk' || $row->getCellAtIndex(12) != 'qty' || $row->getCellAtIndex(13) != 'harga' || $row->getCellAtIndex(14) != 'created' || $row->getCellAtIndex(15) != 'id_status_transaksi') {
									$reader->close();
									unlink('uploads/'.$file['file_name']);

									$msg = array(	'validasi'		=> 'Data import tidak sesuai!',
					    			);
							    	echo json_encode($msg);
								}
							}

							if ($numRow > 1) {
								// Melakukan Storing ke Array (BELUM FIX)
								$cells 	   = $row->getCells();
								$storing[] = array(	'nomor_pesanan'			=> $cells[0]->getValue(),
													'tgl_penjualan'			=> $cells[1]->getValue(),
													'id_toko'				=> $cells[2]->getValue(),
													'id_kurir'				=> $cells[3]->getValue(),
													'nomor_resi'			=> $cells[4]->getValue(),
													'nama_penerima'			=> $cells[5]->getValue(),
													'hp_penerima'			=> $cells[6]->getValue(),
													'alamat_penerima'		=> $cells[7]->getValue(),
													'kabupaten'				=> $cells[8]->getValue(),
													'provinsi'				=> $cells[9]->getValue(),
													'ongkir'				=> $cells[10]->getValue(),
													'id_produk'				=> $cells[11]->getValue(),
													'qty'					=> $cells[12]->getValue(),
													'harga'					=> $cells[13]->getValue(),
													'created'				=> $cells[14]->getValue(),
													'id_status_transaksi'	=> $cells[15]->getValue(),
								);
							}
							$numRow++;
						}
						$reader->close();
						unlink('uploads/'.$file['file_name']);
						// $this->session->set_flashdata('message', '<div class="alert alert-success">'.$baris.' Data imported successfully</div>');
						// redirect('admin/keluar/data_penjualan');
					}
					$numSheet++;
				}

				// Looping Array hasil Storing Array
				$count_nomor_pesanan = array_count_values(array_column($storing, 'nomor_pesanan'));
				$msg_err = '';
				foreach ($storing as $val_store) {
					// Cek Pesanan Apakah total nya sama
					$cek_pesanan = $this->Keluar_model->get_all_detail_by_id($val_store['nomor_pesanan']);
					$cek_pesanan_sementara = $this->Keluar_sementara_model->get_all_detail_by_id($val_store['nomor_pesanan']);
					if (count($cek_pesanan) >= $count_nomor_pesanan[$val_store['nomor_pesanan']]) {
						// Apabila memiliki jumlah pesanan yang sama dengan jumlah pesanan yang ada di Storing maka munculkan PESAN
						$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> sudah ada. Memiliki <b>'.count($cek_pesanan).' Pesanan</b> di <b>Data Penjualan</b> <br>';
						$validasi++;
					}elseif (count($cek_pesanan_sementara) >= $count_nomor_pesanan[$val_store['nomor_pesanan']]) {
						$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> sudah ada. Memiliki <b>'.count($cek_pesanan_sementara).' Pesanan</b> di <b>Data Sementara</b> <br>';
						$validasi++;

						// APABILA TERDAPAT DATA YANG DIDALAM SEMENTARA DAN PESANAN
					}elseif (count($cek_pesanan_sementara) > 0) {
						// $msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> di <b>Data Import</b> terdapat: <b>'.$count_nomor_pesanan[$val_store['nomor_pesanan']].'</b>. Sedangkan di <b>Data Sementara</b> hanya ada <b>'.count($cek_pesanan_sementara).'</b>.<br>';
						// $validasi++;
						if ($count_nomor_pesanan[$val_store['nomor_pesanan']] > count($cek_pesanan_sementara)) {
							// DATA SEMENTARA
							foreach ($cek_pesanan_sementara as $val_pesanan_sementara) {
								$row_produk = $this->Produk_model->get_by_id($val_pesanan_sementara->id_produk);

								$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> dan Produk <b>'.$row_produk->nama_produk.' ('.$row_produk->sub_sku.')</b> telah dihapus dari <b>Data Sementara</b> sebanyak <b>'.count($cek_pesanan_sementara).'</b>. </br>';

								$validasi++;

								$this->Keluar_sementara_model->delete_detail($val_pesanan_sementara->nomor_pesanan);

								write_log();

								$this->Keluar_sementara_model->delete($val_pesanan_sementara->nomor_pesanan);

								write_log();
							}

							// Validasi ID TOKO, KURIR, PRODUK dan STATUS TRANSAKSI
							$cek_kurir = $this->Kurir_model->get_by_id($val_store['id_kurir']);
							$cek_toko = $this->Toko_model->get_by_id($val_store['id_toko']);
							$cek_produk = $this->Produk_model->get_by_id($val_store['id_produk']);
							$cek_status_transaksi = $this->Status_transaksi_model->get_by_id($val_store['id_status_transaksi']);
							$cek_provinsi = $this->Keyword_model->get_nama_provinsi_by_provinsi($val_store['provinsi']);
							$cek_kotkab = $this->Keyword_model->get_nama_kotkab_by_kotkab($val_store['kabupaten']);

							if (!isset($cek_kurir) OR !isset($cek_toko) OR !isset($cek_produk) OR !isset($cek_status_transaksi) OR !isset($cek_provinsi) OR !isset($cek_kotkab)) {
								$teks = '';
								if (!isset($cek_kurir)) {
									$teks .= ' <b>ID Kurir</b> yang bernilai: <b>'.$val_store['id_kurir'].'</b>. </br>';
								}

								if (!isset($cek_toko)) {
									$teks .= ' <b>ID Toko</b> yang bernilai: <b>'.$val_store['id_toko'].'</b>. </br>';
								}

								if (!isset($cek_produk)) {
									$teks .= ' <b>ID Produk</b> yang bernilai: <b>'.$val_store['id_produk'].'</b>. </br>';
								}

								if (!isset($cek_status_transaksi)) {
									$teks .= ' <b>ID Status Transaksi</b> yang bernilai: <b>'.$val_store['id_status_transaksi'].'</b>. </br>';
								}

								if (!isset($cek_provinsi)) {
									$teks .= ' <b>Provinsi</b>: <b>'.$val_store['provinsi'].'</b>. </br>';
								}

								if (!isset($cek_kotkab)) {
									$teks .= ' <b>Kabupaten</b>: <b>'.$val_store['kabupaten'].'</b>. </br>';
								}

								$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> terdapat <b>ERROR</b> pada '.$teks.' Baris ke - '.$barisExcel.'</br>';
								$validasi++;
							}else{
								$cek_retur = $this->Retur_model->get_by_nomor_pesanan($val_store['nomor_pesanan']);
								if (count($cek_retur) > 0) {
									$status_transaksi = 4;

									$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> ada pada <b>Data Retur</b>. Maka status transaksinya menjadi <b>RETUR</b> </br>';
									$validasi++;
								}else{
									$status_transaksi = $val_store['id_status_transaksi'];
								}

								$storing_fix[] = array(	'nomor_pesanan'			=> $val_store['nomor_pesanan'],
														'tgl_penjualan'			=> $val_store['tgl_penjualan'],
														'id_toko'				=> $val_store['id_toko'],
														'id_kurir'				=> $val_store['id_kurir'],
														'nomor_resi'			=> $val_store['nomor_resi'],
														'nama_penerima'			=> $val_store['nama_penerima'],
														'hp_penerima'			=> $val_store['hp_penerima'],
														'alamat_penerima'		=> $val_store['alamat_penerima'],
														'kabupaten'				=> $val_store['kabupaten'],
														'provinsi'				=> $val_store['provinsi'],
														'ongkir'				=> $val_store['ongkir'],
														'id_produk'				=> $val_store['id_produk'],
														'qty'					=> $val_store['qty'],
														'harga'					=> $val_store['harga'],
														'created'				=> $val_store['created'],
														'id_status_transaksi'	=> $status_transaksi,
								);

								$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'(Pesanan)</b> diganti dengan <b>'.$count_nomor_pesanan[$val_store['nomor_pesanan']].' Detail Pesanan</b> di <b>Data Sementara</b>. </br>';
								$validasi++;	
							}
						}
					}elseif (count($cek_pesanan) > 0) {
						// $msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> di <b>Data Import</b> terdapat: <b>'.$count_nomor_pesanan[$val_store['nomor_pesanan']].'</b>. Sedangkan di <b>Data Pesanan</b> hanya ada <b>'.count($cek_pesanan).'</b>.<br>';
						// $validasi++;	
						if ($count_nomor_pesanan[$val_store['nomor_pesanan']] > count($cek_pesanan)) {
							// echo print_r($cek_pesanan);
							// Tambah Qty Produk
							foreach ($cek_pesanan as $val_pesanan) {
								// Hapus Data Resi sesuai Resi (FIX)
							  	$cekResi = $this->Resi_model->get_by_resi($val_pesanan->nomor_resi);
							  	if (isset($cekResi)) {
							  		$this->Resi_model->delete_by_resi($val_pesanan->nomor_resi);
									write_log();
							  	}

								$row_produk = $this->Produk_model->get_by_id($val_pesanan->id_produk);
						  		$tambahStok = array( 	'qty_produk' 		=> $row_produk->qty_produk + $val_pesanan->qty
												);

								$this->Produk_model->update($row_produk->id_produk, $tambahStok);	

								write_log();

								$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> dan Produk <b>'.$row_produk->nama_produk.' ('.$row_produk->sub_sku.')</b> telah dihapus dari <b>Data Pesanan</b> sebanyak <b>'.count($cek_pesanan).'</b>. <br>';

								$validasi++;

								// START - Kurangin jumlah total jika ada didalam paket
					          	$cariPaket = $this->Produk_model->get_all_by_id($val_pesanan->id_produk);
					          	if (isset($cariPaket)) {
					          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
					          		if (count($produkPaket) > 0) {
					          			// echo "Isi 2";
					          			foreach ($produkPaket as $result) {
					          				$total = $result->qty_pakduk * $val_pesanan->qty;
						          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk + $total
											          	);
											$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	

											write_log();
					          			}
					          		}
					          	}
					          	// END

								$this->Keluar_model->delete_detail($val_pesanan->nomor_pesanan);

								write_log();

								$this->Keluar_model->delete($val_pesanan->nomor_pesanan);

								write_log();
							}

							// Validasi ID TOKO, KURIR, PRODUK dan STATUS TRANSAKSI
							$cek_kurir = $this->Kurir_model->get_by_id($val_store['id_kurir']);
							$cek_toko = $this->Toko_model->get_by_id($val_store['id_toko']);
							$cek_produk = $this->Produk_model->get_by_id($val_store['id_produk']);
							$cek_status_transaksi = $this->Status_transaksi_model->get_by_id($val_store['id_status_transaksi']);
							$cek_provinsi = $this->Keyword_model->get_nama_provinsi_by_provinsi($val_store['provinsi']);
							$cek_kotkab = $this->Keyword_model->get_nama_kotkab_by_kotkab($val_store['kabupaten']);

							if (!isset($cek_kurir) OR !isset($cek_toko) OR !isset($cek_produk) OR !isset($cek_status_transaksi) OR !isset($cek_provinsi) OR !isset($cek_kotkab)) {
								$teks = '';
								if (!isset($cek_kurir)) {
									$teks .= ' <b>ID Kurir</b> yang bernilai: <b>'.$val_store['id_kurir'].'</b>. </br>';
								}

								if (!isset($cek_toko)) {
									$teks .= ' <b>ID Toko</b> yang bernilai: <b>'.$val_store['id_toko'].'</b>. </br>';
								}

								if (!isset($cek_produk)) {
									$teks .= ' <b>ID Produk</b> yang bernilai: <b>'.$val_store['id_produk'].'</b>. </br>';
								}

								if (!isset($cek_status_transaksi)) {
									$teks .= ' <b>ID Status Transaksi</b> yang bernilai: <b>'.$val_store['id_status_transaksi'].'</b>. </br>';
								}

								if (!isset($cek_provinsi)) {
									$teks .= ' <b>Provinsi</b>: <b>'.$val_store['provinsi'].'</b>. </br>';
								}

								if (!isset($cek_kotkab)) {
									$teks .= ' <b>Kabupaten</b>: <b>'.$val_store['kabupaten'].'</b>. </br>';
								}

								$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> terdapat <b>ERROR</b> pada '.$teks.' Baris ke - '.$barisExcel.'</br>';
								$validasi++;
							}else{
								$cek_retur = $this->Retur_model->get_by_nomor_pesanan($val_store['nomor_pesanan']);
								if (count($cek_retur) > 0) {
									$status_transaksi = 4;

									$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> ada pada <b>Data Retur</b>. Maka status transaksinya menjadi <b>RETUR</b> </br>';
									$validasi++;
								}else{
									$status_transaksi = $val_store['id_status_transaksi'];
								}

								$storing_fix[] = array(	'nomor_pesanan'			=> $val_store['nomor_pesanan'],
														'tgl_penjualan'			=> $val_store['tgl_penjualan'],
														'id_toko'				=> $val_store['id_toko'],
														'id_kurir'				=> $val_store['id_kurir'],
														'nomor_resi'			=> $val_store['nomor_resi'],
														'nama_penerima'			=> $val_store['nama_penerima'],
														'hp_penerima'			=> $val_store['hp_penerima'],
														'alamat_penerima'		=> $val_store['alamat_penerima'],
														'kabupaten'				=> $val_store['kabupaten'],
														'provinsi'				=> $val_store['provinsi'],
														'ongkir'				=> $val_store['ongkir'],
														'id_produk'				=> $val_store['id_produk'],
														'qty'					=> $val_store['qty'],
														'harga'					=> $val_store['harga'],
														'created'				=> $val_store['created'],
														'id_status_transaksi'	=> $status_transaksi,
								);

								$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'(Pesanan)</b> diganti dengan <b>'.$count_nomor_pesanan[$val_store['nomor_pesanan']].' Detail Pesanan</b> di <b>Data Sementara</b>. </br>';
								$validasi++;	
							}
						}
					}else{
						// Apabila memiliki jumlah pesanan yang tidak sama dengan jumlah pesanan yang ada di Storing maka masukan ke Storing FIX

						// Validasi ID TOKO, KURIR, PRODUK dan STATUS TRANSAKSI
						$cek_kurir = $this->Kurir_model->get_by_id($val_store['id_kurir']);
						$cek_toko = $this->Toko_model->get_by_id($val_store['id_toko']);
						$cek_produk = $this->Produk_model->get_by_id($val_store['id_produk']);
						$cek_status_transaksi = $this->Status_transaksi_model->get_by_id($val_store['id_status_transaksi']);
						$cek_provinsi = $this->Keyword_model->get_nama_provinsi_by_provinsi($val_store['provinsi']);
						$cek_kotkab = $this->Keyword_model->get_nama_kotkab_by_kotkab($val_store['kabupaten']);

						if (!isset($cek_kurir) OR !isset($cek_toko) OR !isset($cek_produk) OR !isset($cek_status_transaksi) OR !isset($cek_provinsi) OR !isset($cek_kotkab)) {
							$teks = '';
							if (!isset($cek_kurir)) {
								$teks .= ' <b>ID Kurir</b> yang bernilai: <b>'.$val_store['id_kurir'].'</b>. </br>';
							}

							if (!isset($cek_toko)) {
								$teks .= ' <b>ID Toko</b> yang bernilai: <b>'.$val_store['id_toko'].'</b>. </br>';
							}

							if (!isset($cek_produk)) {
								$teks .= ' <b>ID Produk</b> yang bernilai: <b>'.$val_store['id_produk'].'</b>. </br>';
							}

							if (!isset($cek_status_transaksi)) {
								$teks .= ' <b>ID Status Transaksi</b> yang bernilai: <b>'.$val_store['id_status_transaksi'].'</b>. </br>';
							}

							if (!isset($cek_provinsi)) {
								$teks .= ' <b>Provinsi</b>: <b>'.$val_store['provinsi'].'</b>. </br>';
							}

							if (!isset($cek_kotkab)) {
								$teks .= ' <b>Kabupaten</b>: <b>'.$val_store['kabupaten'].'</b>. </br>';
							}

							$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> terdapat <b>ERROR</b> pada '.$teks.' Baris ke - '.$barisExcel.'</br>';
							$validasi++;
						}else{
							$cek_retur = $this->Retur_model->get_by_nomor_pesanan($val_store['nomor_pesanan']);
							if (count($cek_retur) > 0) {
								$status_transaksi = 4;

								$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> ada pada <b>Data Retur</b>. Maka status transaksinya menjadi <b>RETUR</b> </br>';
								$validasi++;
							}else{
								$status_transaksi = $val_store['id_status_transaksi'];
							}

							$storing_fix[] = array(	'nomor_pesanan'			=> $val_store['nomor_pesanan'],
													'tgl_penjualan'			=> $val_store['tgl_penjualan'],
													'id_toko'				=> $val_store['id_toko'],
													'id_kurir'				=> $val_store['id_kurir'],
													'nomor_resi'			=> $val_store['nomor_resi'],
													'nama_penerima'			=> $val_store['nama_penerima'],
													'hp_penerima'			=> $val_store['hp_penerima'],
													'alamat_penerima'		=> $val_store['alamat_penerima'],
													'kabupaten'				=> $val_store['kabupaten'],
													'provinsi'				=> $val_store['provinsi'],
													'ongkir'				=> $val_store['ongkir'],
													'id_produk'				=> $val_store['id_produk'],
													'qty'					=> $val_store['qty'],
													'harga'					=> $val_store['harga'],
													'created'				=> $val_store['created'],
													'id_status_transaksi'	=> $status_transaksi,
							);
						}
					}	

					$barisExcel++;			
				}

				// Apabila terdapat Data yang siap untuk masuk ke Data Sementara
				if (count($storing_fix) > 0) {
					// Eksekusi ke Tabel Penjualan dan Detail Penjualan Sementara
					foreach ($storing_fix as $val_fix) {
						$produk    = $this->Produk_model->get_by_id($val_fix['id_produk']);
						// $produk    = $this->Produk_model->get_by_id($cells[11]->getValue());
						$ongkir    = (int)$val_fix['ongkir'];
						// $admin     = $cells[11]->getValue();
						$hpp 	   = (int)$produk->hpp_produk;
						$harga 	   = (int)$val_fix['harga'];
						$qty 	   = (int)$val_fix['qty'];

						$cek_nomor = $this->Keluar_sementara_model->get_all_detail_by_id_row($val_fix['nomor_pesanan']);

						if (isset($cek_nomor)) {

							// Mengecek apabila terdapat ID PRODUK SAMA didalam 1 NOMOR PESANAN
							$cek_pesanan_produk = $this->Keluar_sementara_model->get_all_detail_by_id_produk($val_fix['nomor_pesanan'], $val_fix['id_produk']);
							if (isset($cek_pesanan_produk)) {
								// PROSES KALKUKASI
								$harga_jual 		= ((int)$cek_pesanan_produk->harga_jual + ((int)$val_fix['harga'] * (int)$val_fix['qty']));
								$total_hpp			= ((int)$cek_pesanan_produk->total_hpp + ($hpp * (int)$val_fix['qty']));
								if ($cek_pesanan_produk->ongkir == 0 AND $ongkir == 0 ){
									$total_jual 		= $harga_jual;
									$total_harga 		= $harga_jual;
									$margin 			= $total_jual - $total_hpp;
									$selisih_margin 	= $total_harga - $total_jual;
									$ubahPenjualan 		= array(	'harga_jual' 		=> $harga_jual,
																	'total_hpp' 		=> $total_hpp,
																	'total_jual' 		=> $total_jual,
																	'total_harga' 		=> $total_harga,
																	'margin'	 		=> $margin,
																	'selisih_margin'	=> $selisih_margin,
																);

									$this->Keluar_sementara_model->update($cek_pesanan_produk->nomor_pesanan, $ubahPenjualan);

									write_log();
								}else{
									if ($cek_pesanan_produk->ongkir != 0) {
										$ongkir_fix	= $cek_pesanan_produk->ongkir;
									}elseif ($ongkir != 0) {
										$ongkir_fix	= $ongkir;
									}
									$total_jual 		= ((int)$ongkir_fix + $harga_jual);
									$total_harga 		= $harga_jual;
									$margin 			= $total_jual - (int)$ongkir_fix - $total_hpp;
									$selisih_margin 	= $total_harga - $total_jual;
									$ubahPenjualan 		= array(	'ongkir'			=> $ongkir_fix,
																	'harga_jual' 		=> $harga_jual,
																	'total_hpp' 		=> $total_hpp,
																	'total_jual' 		=> $total_jual,
																	'total_harga' 		=> $total_harga,
																	'margin'	 		=> $margin,
																	'selisih_margin'	=> $selisih_margin,
																);

									$this->Keluar_sementara_model->update($cek_pesanan_produk->nomor_pesanan, $ubahPenjualan);

									write_log();
								}

								$updateDetail		= array(	'qty' 				=> ((int)$cek_pesanan_produk->qty + (int)$val_fix['qty']),
														);

								$this->Keluar_sementara_model->update_detail($cek_pesanan_produk->nomor_pesanan, $cek_pesanan_produk->id_produk, $updateDetail);

								write_log();

								$msg_err .= 'Nomor Pesanan: <b>'.$cek_pesanan_produk->nomor_pesanan.'</b> terdapat Produk yang sama <b>'.$cek_pesanan_produk->nama_produk.' ('.$cek_pesanan_produk->sub_sku.')</b>. Jumlah Qty menjadi <b>'.$updateDetail['qty'].'</b>, dari <b>'.$cek_pesanan_produk->qty.' + '.$val_fix['qty'].' </b></br>';
								$validasi++;
							}else{
								// // TIDAK PROSES KALKUKASI
								// $harga_jual 		= ((int)$cek_nomor->harga_jual + ($harga * $qty));
								// $total_hpp			= ((int)$cek_nomor->total_hpp + ($hpp * $qty));
								// $total_jual 		= ((int)$cek_nomor->total_jual + $harga_jual);
								// $total_harga 		= ((int)$cek_nomor->total_harga + $harga_jual);
								// $selisih_margin 	= $total_jual - $total_hpp;
								// $margin 			= $total_jual - $total_harga;
								// $ubahPenjualan 	= array(	'harga_jual' 		=> $harga_jual,
								// 							'total_hpp' 		=> $total_hpp,
								// 							'total_jual' 		=> $total_jual,
								// 							'total_harga' 		=> $total_harga,
								// 							'margin'	 		=> $margin,
								// 							'selisih_margin'	=> $selisih_margin,
								// 						);

								// $this->Keluar_sementara_model->update($cek_nomor->nomor_pesanan, $ubahPenjualan);

								// write_log();

								// PROSES KALKUKASI
								$harga_jual 		= ((int)$cek_nomor->harga_jual + ((int)$val_fix['harga'] * (int)$val_fix['qty']));
								$total_hpp			= ((int)$cek_nomor->total_hpp + ($hpp * (int)$val_fix['qty']));
								if ($cek_nomor->ongkir == 0 AND $ongkir == 0 ){
									$total_jual 		= $harga_jual;
									$total_harga 		= $harga_jual;
									$margin 			= $total_jual - $total_hpp;
									$selisih_margin 	= $total_harga - $total_jual;
									$ubahPenjualan 		= array(	'harga_jual' 		=> $harga_jual,
																	'total_hpp' 		=> $total_hpp,
																	'total_jual' 		=> $total_jual,
																	'total_harga' 		=> $total_harga,
																	'margin'	 		=> $margin,
																	'selisih_margin'	=> $selisih_margin,
																);

									$this->Keluar_sementara_model->update($cek_nomor->nomor_pesanan, $ubahPenjualan);

									write_log();
								}else{
									if ($cek_nomor->ongkir != 0) {
										$ongkir_fix	= $cek_nomor->ongkir;
									}elseif ($ongkir != 0) {
										$ongkir_fix	= $ongkir;
									}
									$total_jual 		= ((int)$ongkir_fix + $harga_jual);
									$total_harga 		= $harga_jual;
									$margin 			= $total_jual - (int)$ongkir_fix - $total_hpp;
									$selisih_margin 	= $total_harga - $total_jual;
									$ubahPenjualan 		= array(	'ongkir'			=> $ongkir_fix,
																	'harga_jual' 		=> $harga_jual,
																	'total_hpp' 		=> $total_hpp,
																	'total_jual' 		=> $total_jual,
																	'total_harga' 		=> $total_harga,
																	'margin'	 		=> $margin,
																	'selisih_margin'	=> $selisih_margin,
																);

									$this->Keluar_sementara_model->update($cek_nomor->nomor_pesanan, $ubahPenjualan);

									write_log();
								}

								$simpanDetail		= array(	'nomor_pesanan'		=> $val_fix['nomor_pesanan'],
																'id_produk' 		=> $val_fix['id_produk'],
																'qty' 				=> $val_fix['qty'],
																'harga'		 		=> $val_fix['harga'],
																'hpp' 				=> $hpp
														);

								$this->Keluar_sementara_model->insert_detail($simpanDetail);

								write_log();
							}
						}else{
							if ($val_fix['created'] == '') {
								date_default_timezone_set("Asia/Jakarta");
								$now = date('Y-m-d H:i:s');
								$harga_jual 		= $harga * $qty;
								$hpp_jual 			= $hpp * $qty;
								// $total 				= $ongkir;
								$total_jual 		= $harga_jual +  $ongkir;
								$total_harga		= $harga_jual;
								$simpanPenjualan	= array(	'nomor_pesanan'			=> $val_fix['nomor_pesanan'],
																'tgl_penjualan' 		=> $val_fix['tgl_penjualan'],
																'id_users' 				=> $this->session->userdata('id_users'),
																'id_status_transaksi' 	=> $val_fix['id_status_transaksi'],
																'id_toko' 				=> $val_fix['id_toko'],
																'id_kurir'	 			=> $val_fix['id_kurir'],
																'nomor_resi'			=> $val_fix['nomor_resi'],
																'nama_penerima' 		=> str_replace(';', '', $val_fix['nama_penerima']),
																'hp_penerima' 			=> $val_fix['hp_penerima'],
																'alamat_penerima' 		=> str_replace(';', ',', $val_fix['alamat_penerima']),
																'kabupaten' 			=> $val_fix['kabupaten'],
																'provinsi' 				=> $val_fix['provinsi'],
																'ongkir'		 		=> $val_fix['ongkir'],
																// 'biaya_admin'	 	=> $row->getCellAtIndex(11),
																'harga_jual'	 		=> $harga_jual,
																'total_hpp'	 			=> $hpp_jual,
																'total_jual' 			=> $total_jual,
																'total_harga' 			=> $total_harga,
																'margin' 				=> $total_jual - $ongkir - $hpp_jual,
																'selisih_margin' 		=> $total_harga - $total_jual,
																'jumlah_diterima' 		=> 0,
																'tgl_diterima' 			=> NULL,
																'created' 				=> $now 	
														);

								$this->Keluar_sementara_model->insert($simpanPenjualan);

								write_log();


								$simpanDetail		= array(	'nomor_pesanan'		=> $val_fix['nomor_pesanan'],
																'id_produk' 		=> $val_fix['id_produk'],
																'qty' 				=> $val_fix['qty'],
																'harga'		 		=> $val_fix['harga'],
																'hpp' 				=> $hpp
														);

								$this->Keluar_sementara_model->insert_detail($simpanDetail);

								write_log();

								$baris++;
							}else{
								date_default_timezone_set("Asia/Jakarta");
								$harga_jual 		= $harga * $qty;
								$hpp_jual 			= $hpp * $qty;
								// $total 				= $ongkir;
								$total_jual 		= $harga_jual +  $ongkir;
								$total_harga		= $harga_jual;
								$simpanPenjualan	= array(	'nomor_pesanan'			=> $val_fix['nomor_pesanan'],
																'tgl_penjualan' 		=> $val_fix['tgl_penjualan'],
																'id_users' 				=> $this->session->userdata('id_users'),
																'id_status_transaksi' 	=> $val_fix['id_status_transaksi'],
																'id_toko' 				=> $val_fix['id_toko'],
																'id_kurir'	 			=> $val_fix['id_kurir'],
																'nomor_resi'			=> $val_fix['nomor_resi'],
																'nama_penerima' 		=> str_replace(';', '', $val_fix['nama_penerima']),
																'hp_penerima' 			=> $val_fix['hp_penerima'],
																'alamat_penerima' 		=> str_replace(';', ',', $val_fix['alamat_penerima']),
																'kabupaten' 			=> $val_fix['kabupaten'],
																'provinsi' 				=> $val_fix['provinsi'],
																'ongkir'		 		=> $val_fix['ongkir'],
																// 'biaya_admin'	 	=> $row->getCellAtIndex(11),
																'harga_jual'	 		=> $harga_jual,
																'total_hpp'	 			=> $hpp_jual,
																'total_jual' 			=> $total_jual,
																'total_harga' 			=> $total_harga,
																'margin' 				=> $total_jual - $ongkir - $hpp_jual,
																'selisih_margin' 		=> $total_harga - $total_jual,
																'jumlah_diterima' 		=> 0,
																'tgl_diterima' 			=> NULL,
																'created' 				=> $val_fix['created']	
														);

								$this->Keluar_sementara_model->insert($simpanPenjualan);

								write_log();


								$simpanDetail		= array(	'nomor_pesanan'		=> $val_fix['nomor_pesanan'],
																'id_produk' 		=> $val_fix['id_produk'],
																'qty' 				=> $val_fix['qty'],
																'harga'		 		=> $val_fix['harga'],
																'hpp' 				=> $hpp
														);

								$this->Keluar_sementara_model->insert_detail($simpanDetail);

								write_log();

								$baris++;
							}
						}
					}
					// Foreach	
					if ($validasi > 0) {
						$msg = array(	'sukses'	=> $baris.' Data Berhasil Import Penjualan ke Data Sementara!',
										'pesan_error'	=> $msg_err
			    			);
				    	echo json_encode($msg);	
					}else{
						$msg = array(	'sukses'	=> $baris.' Data Berhasil Import Penjualan ke Data Sementara!'
			    			);
				    	echo json_encode($msg);
					}
				}else{
					if ($validasi > 0) {
						$msg = array(	'validasi'		=> 'Tidak ada data yang diimport!',
										'pesan_error'	=> $msg_err
		    			);
				    	echo json_encode($msg);
					}else{
						$msg = array(	'validasi'		=> 'Tidak ada data yang diimport!'
		    			);
				    	echo json_encode($msg);	
					}
				}
			}else{
				$error = array('validasi' => $this->upload->display_errors());
				echo json_encode($error);
			}
		}elseif ($keyword == "yes") {
			// $msg = array(	'validasi'	=> 'Mohon maaf masih belum tersedia',
	  //   			);
	  //   	echo json_encode($msg);
			$config['upload_path'] 		= './uploads/';
			$config['allowed_types'] 	= 'xlsx|xls';
			$config['file_name']		= 'doc'.time();	
			$storing = array();
			$storing_fix = array();
			$baris = 0;
			$barisExcel= 2;
			// $config['max_size']  = '100';
			// $config['max_width']  = '1024';
			// $config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('impor_penjualan')) {
				$file 		= $this->upload->data();
				$reader 	= ReaderEntityFactory::createXLSXReader();

				$reader->open('uploads/'.$file['file_name']);
				$numSheet 	= 0;
				$validasi = 0;

				foreach ($reader->getSheetIterator() as $sheetXLS) {
					$numRow = 1;
					if ($numSheet == 0) {
						foreach ($sheetXLS->getRowIterator() as $row) {
							if ($numRow == 1) {
								if ($row->getCellAtIndex(0) != 'no_pesanan' || $row->getCellAtIndex(1) != 'tgl_penjualan' || $row->getCellAtIndex(2) != 'id_toko' || $row->getCellAtIndex(3) != 'id_kurir' || $row->getCellAtIndex(4) != 'nomor_resi' || $row->getCellAtIndex(5) != 'nama_penerima' || $row->getCellAtIndex(6) != 'hp_penerima' || $row->getCellAtIndex(7) != 'alamat_penerima' || $row->getCellAtIndex(8) != 'kabupaten' || $row->getCellAtIndex(9) != 'provinsi' || $row->getCellAtIndex(10) != 'ongkir' || $row->getCellAtIndex(11) != 'id_produk' || $row->getCellAtIndex(12) != 'qty' || $row->getCellAtIndex(13) != 'harga' || $row->getCellAtIndex(14) != 'created' || $row->getCellAtIndex(15) != 'id_status_transaksi') {
									$reader->close();
									unlink('uploads/'.$file['file_name']);

									$msg = array(	'validasi'		=> 'Data import tidak sesuai!',
					    			);
							    	echo json_encode($msg);
								}
							}

							if ($numRow > 1) {
								// Melakukan Storing ke Array (BELUM FIX)
								$cells 	   = $row->getCells();
								$storing[] = array(	'nomor_pesanan'			=> $cells[0]->getValue(),
													'tgl_penjualan'			=> $cells[1]->getValue(),
													'id_toko'				=> $cells[2]->getValue(),
													'id_kurir'				=> $cells[3]->getValue(),
													'nomor_resi'			=> $cells[4]->getValue(),
													'nama_penerima'			=> $cells[5]->getValue(),
													'hp_penerima'			=> $cells[6]->getValue(),
													'alamat_penerima'		=> $cells[7]->getValue(),
													'kabupaten'				=> $cells[8]->getValue(),
													'provinsi'				=> $cells[9]->getValue(),
													'ongkir'				=> $cells[10]->getValue(),
													'id_produk'				=> $cells[11]->getValue(),
													'qty'					=> $cells[12]->getValue(),
													'harga'					=> $cells[13]->getValue(),
													'created'				=> $cells[14]->getValue(),
													'id_status_transaksi'	=> $cells[15]->getValue(),
								);
							}
							$numRow++;
						}
						$reader->close();
						unlink('uploads/'.$file['file_name']);
						// $this->session->set_flashdata('message', '<div class="alert alert-success">'.$baris.' Data imported successfully</div>');
						// redirect('admin/keluar/data_penjualan');
					}
					$numSheet++;
				}

				// Looping Array hasil Storing Array
				$count_nomor_pesanan = array_count_values(array_column($storing, 'nomor_pesanan'));
				$msg_err = '';
				foreach ($storing as $val_store) {
					// Cek Pesanan Apakah total nya sama
					$cek_pesanan = $this->Keluar_model->get_all_detail_by_id($val_store['nomor_pesanan']);
					$cek_pesanan_sementara = $this->Keluar_sementara_model->get_all_detail_by_id($val_store['nomor_pesanan']);
					if (count($cek_pesanan) >= $count_nomor_pesanan[$val_store['nomor_pesanan']]) {
						// Apabila memiliki jumlah pesanan yang sama dengan jumlah pesanan yang ada di Storing maka munculkan PESAN
						$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> sudah ada. Memiliki <b>'.count($cek_pesanan).' Pesanan</b> di <b>Data Penjualan</b> <br>';
						$validasi++;
					}elseif (count($cek_pesanan_sementara) >= $count_nomor_pesanan[$val_store['nomor_pesanan']]) {
						$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> sudah ada. Memiliki <b>'.count($cek_pesanan_sementara).' Pesanan</b> di <b>Data Sementara</b> <br>';
						$validasi++;

						// APABILA TERDAPAT DATA YANG DIDALAM SEMENTARA DAN PESANAN
					}elseif (count($cek_pesanan_sementara) > 0) {
						// $msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> di <b>Data Import</b> terdapat: <b>'.$count_nomor_pesanan[$val_store['nomor_pesanan']].'</b>. Sedangkan di <b>Data Sementara</b> hanya ada <b>'.count($cek_pesanan_sementara).'</b>.<br>';
						// $validasi++;
						if ($count_nomor_pesanan[$val_store['nomor_pesanan']] > count($cek_pesanan_sementara)) {
							// DATA SEMENTARA
							foreach ($cek_pesanan_sementara as $val_pesanan_sementara) {
								$row_produk = $this->Produk_model->get_by_id($val_pesanan_sementara->id_produk);

								$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> dan Produk <b>'.$row_produk->nama_produk.' ('.$row_produk->sub_sku.')</b> telah dihapus dari <b>Data Sementara</b> sebanyak <b>'.count($cek_pesanan_sementara).'</b>. </br>';

								$validasi++;

								$this->Keluar_sementara_model->delete_detail($val_pesanan_sementara->nomor_pesanan);

								write_log();

								$this->Keluar_sementara_model->delete($val_pesanan_sementara->nomor_pesanan);

								write_log();
							}

							// Validasi ID TOKO, KURIR, PRODUK dan PROVINSI KOTKAB
							$cek_kurir = $this->Keyword_model->get_kurir_by_keys_kurir($val_store['id_kurir']);
							$cek_toko = $this->Keyword_model->get_toko_by_keys_toko($val_store['id_toko']);
							$cek_produk = $this->Keyword_model->get_produk_by_keys_produk($val_store['id_produk']);
							$cek_provinsi_kotkab = $this->Keyword_model->get_kotkab_provinsi_by_keys_kotkab($val_store['kabupaten']);
							$cek_status_transaksi = $this->Status_transaksi_model->get_by_id($val_store['id_status_transaksi']);

							if (!isset($cek_kurir) OR !isset($cek_toko) OR !isset($cek_produk) OR !isset($cek_provinsi_kotkab) OR !isset($cek_status_transaksi)) {
								$teks = '';
								if (!isset($cek_kurir)) {
									$teks .= ' <b>Nama Kurir</b>: <b>'.$val_store['id_kurir'].'</b> belum tersedia di Keyword. </br>';
								}

								if (!isset($cek_toko)) {
									$teks .= ' <b>Nama Toko</b>: <b>'.$val_store['id_toko'].'</b> belum tersedia di Keyword. </br>';
								}

								if (!isset($cek_produk)) {
									$teks .= ' <b>Nama Produk</b>: <b>'.$val_store['id_produk'].'</b> belum tersedia di Keyword.  </br>';
								}

								if (!isset($cek_provinsi_kotkab)) {
									$teks .= ' <b>Nama Kabupaten</b>: <b>'.$val_store['kabupaten'].'</b> belum tersedia di Keyword. Untuk Provinsi: <b>'.$val_store['provinsi'].'</b>. </br>';
								}

								if (!isset($cek_status_transaksi)) {
									$teks .= ' <b>ID Status Transaksi</b> yang bernilai: <b>'.$val_store['id_status_transaksi'].'</b>. </br>';
								}

								$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> terdapat <b>ERROR</b> pada: <br> '.$teks.' Baris ke - '.$barisExcel.'</br></br>';
								$validasi++;
							}else{
								$cek_retur = $this->Retur_model->get_by_nomor_pesanan($val_store['nomor_pesanan']);
								if (count($cek_retur) > 0) {
									$status_transaksi = 4;

									$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> ada pada <b>Data Retur</b>. Maka status transaksinya menjadi <b>RETUR</b> </br>';
									$validasi++;
								}else{
									$status_transaksi = $val_store['id_status_transaksi'];
								}

								$storing_fix[] = array(	'nomor_pesanan'			=> $val_store['nomor_pesanan'],
														'tgl_penjualan'			=> $val_store['tgl_penjualan'],
														'id_toko'				=> $cek_toko->id_toko,
														'id_kurir'				=> $cek_kurir->id_kurir,
														'nomor_resi'			=> $val_store['nomor_resi'],
														'nama_penerima'			=> $val_store['nama_penerima'],
														'hp_penerima'			=> $val_store['hp_penerima'],
														'alamat_penerima'		=> $val_store['alamat_penerima'],
														'kabupaten'				=> $cek_provinsi_kotkab->nama_kotkab,
														'provinsi'				=> $cek_provinsi_kotkab->nama_provinsi,
														'ongkir'				=> $val_store['ongkir'],
														'id_produk'				=> $cek_produk->id_produk,
														'qty'					=> $val_store['qty'],
														'harga'					=> $val_store['harga'],
														'created'				=> $val_store['created'],
														'id_status_transaksi'	=> $status_transaksi,
								);

								$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'(Pesanan)</b> diganti dengan <b>'.$count_nomor_pesanan[$val_store['nomor_pesanan']].' Detail Pesanan</b> di <b>Data Sementara</b>. </br>';
								$validasi++;	
							}
						}
					}elseif (count($cek_pesanan) > 0) {
						// $msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> di <b>Data Import</b> terdapat: <b>'.$count_nomor_pesanan[$val_store['nomor_pesanan']].'</b>. Sedangkan di <b>Data Pesanan</b> hanya ada <b>'.count($cek_pesanan).'</b>.<br>';
						// $validasi++;	
						if ($count_nomor_pesanan[$val_store['nomor_pesanan']] > count($cek_pesanan)) {
							// echo print_r($cek_pesanan);
							// Tambah Qty Produk
							foreach ($cek_pesanan as $val_pesanan) {
								// Hapus Data Resi sesuai Resi (FIX)
							  	$cekResi = $this->Resi_model->get_by_resi($val_pesanan->nomor_resi);
							  	if (isset($cekResi)) {
							  		$this->Resi_model->delete_by_resi($val_pesanan->nomor_resi);
									write_log();
							  	}

								$row_produk = $this->Produk_model->get_by_id($val_pesanan->id_produk);
						  		$tambahStok = array( 	'qty_produk' 		=> $row_produk->qty_produk + $val_pesanan->qty
												);

								$this->Produk_model->update($row_produk->id_produk, $tambahStok);	

								write_log();

								$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> dan Produk <b>'.$row_produk->nama_produk.' ('.$row_produk->sub_sku.')</b> telah dihapus dari <b>Data Pesanan</b> sebanyak <b>'.count($cek_pesanan).'</b>. <br>';

								$validasi++;

								// START - Kurangin jumlah total jika ada didalam paket
					          	$cariPaket = $this->Produk_model->get_all_by_id($val_pesanan->id_produk);
					          	if (isset($cariPaket)) {
					          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
					          		if (count($produkPaket) > 0) {
					          			// echo "Isi 2";
					          			foreach ($produkPaket as $result) {
					          				$total = $result->qty_pakduk * $val_pesanan->qty;
						          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk + $total
											          	);
											$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	

											write_log();
					          			}
					          		}
					          	}
					          	// END

								$this->Keluar_model->delete_detail($val_pesanan->nomor_pesanan);

								write_log();

								$this->Keluar_model->delete($val_pesanan->nomor_pesanan);

								write_log();
							}

							// Validasi ID TOKO, KURIR, PRODUK dan PROVINSI KOTKAB
							$cek_kurir = $this->Keyword_model->get_kurir_by_keys_kurir($val_store['id_kurir']);
							$cek_toko = $this->Keyword_model->get_toko_by_keys_toko($val_store['id_toko']);
							$cek_produk = $this->Keyword_model->get_produk_by_keys_produk($val_store['id_produk']);
							$cek_provinsi_kotkab = $this->Keyword_model->get_kotkab_provinsi_by_keys_kotkab($val_store['kabupaten']);
							$cek_status_transaksi = $this->Status_transaksi_model->get_by_id($val_store['id_status_transaksi']);

							if (!isset($cek_kurir) OR !isset($cek_toko) OR !isset($cek_produk) OR !isset($cek_provinsi_kotkab) OR !isset($cek_status_transaksi)) {
								$teks = '';
								if (!isset($cek_kurir)) {
									$teks .= ' <b>Nama Kurir</b>: <b>'.$val_store['id_kurir'].'</b> belum tersedia di Keyword. </br>';
								}

								if (!isset($cek_toko)) {
									$teks .= ' <b>Nama Toko</b>: <b>'.$val_store['id_toko'].'</b> belum tersedia di Keyword. </br>';
								}

								if (!isset($cek_produk)) {
									$teks .= ' <b>Nama Produk</b>: <b>'.$val_store['id_produk'].'</b> belum tersedia di Keyword.  </br>';
								}

								if (!isset($cek_provinsi_kotkab)) {
									$teks .= ' <b>Nama Kabupaten</b>: <b>'.$val_store['kabupaten'].'</b> belum tersedia di Keyword. Untuk Provinsi: <b>'.$val_store['provinsi'].'</b>. </br>';
								}

								if (!isset($cek_status_transaksi)) {
									$teks .= ' <b>ID Status Transaksi</b> yang bernilai: <b>'.$val_store['id_status_transaksi'].'</b>. </br>';
								}

								$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> terdapat <b>ERROR</b> pada: <br> '.$teks.' Baris ke - '.$barisExcel.'</br></br>';
								$validasi++;
							}else{
								$cek_retur = $this->Retur_model->get_by_nomor_pesanan($val_store['nomor_pesanan']);
								if (count($cek_retur) > 0) {
									$status_transaksi = 4;

									$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> ada pada <b>Data Retur</b>. Maka status transaksinya menjadi <b>RETUR</b> </br>';
									$validasi++;
								}else{
									$status_transaksi = $val_store['id_status_transaksi'];
								}

								$storing_fix[] = array(	'nomor_pesanan'			=> $val_store['nomor_pesanan'],
														'tgl_penjualan'			=> $val_store['tgl_penjualan'],
														'id_toko'				=> $cek_toko->id_toko,
														'id_kurir'				=> $cek_kurir->id_kurir,
														'nomor_resi'			=> $val_store['nomor_resi'],
														'nama_penerima'			=> $val_store['nama_penerima'],
														'hp_penerima'			=> $val_store['hp_penerima'],
														'alamat_penerima'		=> $val_store['alamat_penerima'],
														'kabupaten'				=> $cek_provinsi_kotkab->nama_kotkab,
														'provinsi'				=> $cek_provinsi_kotkab->nama_provinsi,
														'ongkir'				=> $val_store['ongkir'],
														'id_produk'				=> $cek_produk->id_produk,
														'qty'					=> $val_store['qty'],
														'harga'					=> $val_store['harga'],
														'created'				=> $val_store['created'],
														'id_status_transaksi'	=> $status_transaksi,
								);

								$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'(Pesanan)</b> diganti dengan <b>'.$count_nomor_pesanan[$val_store['nomor_pesanan']].' Detail Pesanan</b> di <b>Data Sementara</b>. </br>';
								$validasi++;	
							}
						}
					}else{
						// Apabila memiliki jumlah pesanan yang tidak sama dengan jumlah pesanan yang ada di Storing maka masukan ke Storing FIX

						// Validasi ID TOKO, KURIR, PRODUK dan PROVINSI KOTKAB
						$cek_kurir = $this->Keyword_model->get_kurir_by_keys_kurir($val_store['id_kurir']);
						$cek_toko = $this->Keyword_model->get_toko_by_keys_toko($val_store['id_toko']);
						$cek_produk = $this->Keyword_model->get_produk_by_keys_produk($val_store['id_produk']);
						$cek_provinsi_kotkab = $this->Keyword_model->get_kotkab_provinsi_by_keys_kotkab($val_store['kabupaten']);
						$cek_status_transaksi = $this->Status_transaksi_model->get_by_id($val_store['id_status_transaksi']);

						if (!isset($cek_kurir) OR !isset($cek_toko) OR !isset($cek_produk) OR !isset($cek_provinsi_kotkab) OR !isset($cek_status_transaksi)) {
							$teks = '';
							if (!isset($cek_kurir)) {
								$teks .= ' <b>Nama Kurir</b>: <b>'.$val_store['id_kurir'].'</b> belum tersedia di Keyword. </br>';
							}

							if (!isset($cek_toko)) {
								$teks .= ' <b>Nama Toko</b>: <b>'.$val_store['id_toko'].'</b> belum tersedia di Keyword. </br>';
							}

							if (!isset($cek_produk)) {
								$teks .= ' <b>Nama Produk</b>: <b>'.$val_store['id_produk'].'</b> belum tersedia di Keyword.  </br>';
							}

							if (!isset($cek_provinsi_kotkab)) {
								$teks .= ' <b>Nama Kabupaten</b>: <b>'.$val_store['kabupaten'].'</b> belum tersedia di Keyword. Untuk Provinsi: <b>'.$val_store['provinsi'].'</b>. </br>';
							}

							if (!isset($cek_status_transaksi)) {
								$teks .= ' <b>ID Status Transaksi</b> yang bernilai: <b>'.$val_store['id_status_transaksi'].'</b>. </br>';
							}

							$msg_err .= 'Nomor Pesanan: <b>'.$val_store['nomor_pesanan'].'</b> terdapat <b>ERROR</b> pada: <br> '.$teks.' Baris ke - '.$barisExcel.'</br></br>';
							$validasi++;
						}else{
							$cek_retur = $this->Retur_model->get_by_nomor_pesanan($val_store['nomor_pesanan']);
							if (count($cek_retur) > 0) {
								$status_transaksi = 4;

								$msg_err .= 'Nomor Pesanan <b>'.$val_store['nomor_pesanan'].'</b> ada pada <b>Data Retur</b>. Maka status transaksinya menjadi <b>RETUR</b> </br>';
								$validasi++;
							}else{
								$status_transaksi = $val_store['id_status_transaksi'];
							}

							$storing_fix[] = array(	'nomor_pesanan'			=> $val_store['nomor_pesanan'],
													'tgl_penjualan'			=> $val_store['tgl_penjualan'],
													'id_toko'				=> $cek_toko->id_toko,
													'id_kurir'				=> $cek_kurir->id_kurir,
													'nomor_resi'			=> $val_store['nomor_resi'],
													'nama_penerima'			=> $val_store['nama_penerima'],
													'hp_penerima'			=> $val_store['hp_penerima'],
													'alamat_penerima'		=> $val_store['alamat_penerima'],
													'kabupaten'				=> $cek_provinsi_kotkab->nama_kotkab,
													'provinsi'				=> $cek_provinsi_kotkab->nama_provinsi,
													'ongkir'				=> $val_store['ongkir'],
													'id_produk'				=> $cek_produk->id_produk,
													'qty'					=> $val_store['qty'],
													'harga'					=> $val_store['harga'],
													'created'				=> $val_store['created'],
													'id_status_transaksi'	=> $status_transaksi,
							);
						}
					}	

					$barisExcel++;			
				}

				// Apabila terdapat Data yang siap untuk masuk ke Data Sementara
				if (count($storing_fix) > 0) {
					// Eksekusi ke Tabel Penjualan dan Detail Penjualan Sementara
					foreach ($storing_fix as $val_fix) {
						$produk    = $this->Produk_model->get_by_id($val_fix['id_produk']);
						// $produk    = $this->Produk_model->get_by_id($cells[11]->getValue());
						$ongkir    = (int)$val_fix['ongkir'];
						// $admin     = $cells[11]->getValue();
						$hpp 	   = (int)$produk->hpp_produk;
						$harga 	   = (int)$val_fix['harga'];
						$qty 	   = (int)$val_fix['qty'];

						$cek_nomor = $this->Keluar_sementara_model->get_all_detail_by_id_row($val_fix['nomor_pesanan']);

						if (isset($cek_nomor)) {

							// Mengecek apabila terdapat ID PRODUK SAMA didalam 1 NOMOR PESANAN
							$cek_pesanan_produk = $this->Keluar_sementara_model->get_all_detail_by_id_produk($val_fix['nomor_pesanan'], $val_fix['id_produk']);
							if (isset($cek_pesanan_produk)) {
								// PROSES KALKUKASI
								$harga_jual 		= ((int)$cek_pesanan_produk->harga_jual + ((int)$val_fix['harga'] * (int)$val_fix['qty']));
								$total_hpp			= ((int)$cek_pesanan_produk->total_hpp + ($hpp * (int)$val_fix['qty']));
								if ($cek_pesanan_produk->ongkir == 0 AND $ongkir == 0 ){
									$total_jual 		= $harga_jual;
									$total_harga 		= $harga_jual;
									$margin 			= $total_jual - $total_hpp;
									$selisih_margin 	= $total_harga - $total_jual;
									$ubahPenjualan 		= array(	'harga_jual' 		=> $harga_jual,
																	'total_hpp' 		=> $total_hpp,
																	'total_jual' 		=> $total_jual,
																	'total_harga' 		=> $total_harga,
																	'margin'	 		=> $margin,
																	'selisih_margin'	=> $selisih_margin,
																);

									$this->Keluar_sementara_model->update($cek_pesanan_produk->nomor_pesanan, $ubahPenjualan);

									write_log();
								}else{
									if ($cek_pesanan_produk->ongkir != 0) {
										$ongkir_fix	= $cek_pesanan_produk->ongkir;
									}elseif ($ongkir != 0) {
										$ongkir_fix	= $ongkir;
									}
									$total_jual 		= ((int)$ongkir_fix + $harga_jual);
									$total_harga 		= $harga_jual;
									$margin 			= $total_jual - (int)$ongkir_fix - $total_hpp;
									$selisih_margin 	= $total_harga - $total_jual;
									$ubahPenjualan 		= array(	'ongkir'			=> $ongkir_fix,
																	'harga_jual' 		=> $harga_jual,
																	'total_hpp' 		=> $total_hpp,
																	'total_jual' 		=> $total_jual,
																	'total_harga' 		=> $total_harga,
																	'margin'	 		=> $margin,
																	'selisih_margin'	=> $selisih_margin,
																);

									$this->Keluar_sementara_model->update($cek_pesanan_produk->nomor_pesanan, $ubahPenjualan);

									write_log();
								}

								$updateDetail		= array(	'qty' 				=> ((int)$cek_pesanan_produk->qty + (int)$val_fix['qty']),
														);

								$this->Keluar_sementara_model->update_detail($cek_pesanan_produk->nomor_pesanan, $cek_pesanan_produk->id_produk, $updateDetail);

								write_log();
							}else{
								// TIDAK PROSES KALKUKASI
								// $harga_jual 		= ((int)$cek_nomor->harga_jual + ($harga * $qty));
								// $total_hpp			= ((int)$cek_nomor->total_hpp + ($hpp * $qty));
								// $total_jual 		= ((int)$cek_nomor->total_jual + $harga_jual);
								// $total_harga 		= ((int)$cek_nomor->total_harga + $harga_jual);
								// $selisih_margin 	= $total_jual - $total_hpp;
								// $margin 			= $total_jual - $total_harga;
								// $ubahPenjualan 	= array(	'harga_jual' 		=> $harga_jual,
								// 							'total_hpp' 		=> $total_hpp,
								// 							'total_jual' 		=> $total_jual,
								// 							'total_harga' 		=> $total_harga,
								// 							'margin'	 		=> $margin,
								// 							'selisih_margin'	=> $selisih_margin,
								// 						);

								// $this->Keluar_sementara_model->update($cek_nomor->nomor_pesanan, $ubahPenjualan);

								// write_log();

								// PROSES KALKUKASI
								$harga_jual 		= ((int)$cek_nomor->harga_jual + ((int)$val_fix['harga'] * (int)$val_fix['qty']));
								$total_hpp			= ((int)$cek_nomor->total_hpp + ($hpp * (int)$val_fix['qty']));
								if ($cek_nomor->ongkir == 0 AND $ongkir == 0 ){
									$total_jual 		= $harga_jual;
									$total_harga 		= $harga_jual;
									$margin 			= $total_jual - $total_hpp;
									$selisih_margin 	= $total_harga - $total_jual;
									$ubahPenjualan 		= array(	'harga_jual' 		=> $harga_jual,
																	'total_hpp' 		=> $total_hpp,
																	'total_jual' 		=> $total_jual,
																	'total_harga' 		=> $total_harga,
																	'margin'	 		=> $margin,
																	'selisih_margin'	=> $selisih_margin,
																);

									$this->Keluar_sementara_model->update($cek_nomor->nomor_pesanan, $ubahPenjualan);

									write_log();
								}else{
									if ($cek_nomor->ongkir != 0) {
										$ongkir_fix	= $cek_nomor->ongkir;
									}elseif ($ongkir != 0) {
										$ongkir_fix	= $ongkir;
									}
									$total_jual 		= ((int)$ongkir_fix + $harga_jual);
									$total_harga 		= $harga_jual;
									$margin 			= $total_jual - (int)$ongkir_fix - $total_hpp;
									$selisih_margin 	= $total_harga - $total_jual;
									$ubahPenjualan 		= array(	'ongkir'			=> $ongkir_fix,
																	'harga_jual' 		=> $harga_jual,
																	'total_hpp' 		=> $total_hpp,
																	'total_jual' 		=> $total_jual,
																	'total_harga' 		=> $total_harga,
																	'margin'	 		=> $margin,
																	'selisih_margin'	=> $selisih_margin,
																);

									$this->Keluar_sementara_model->update($cek_nomor->nomor_pesanan, $ubahPenjualan);

									write_log();
								}

								$simpanDetail		= array(	'nomor_pesanan'		=> $val_fix['nomor_pesanan'],
																'id_produk' 		=> $val_fix['id_produk'],
																'qty' 				=> $val_fix['qty'],
																'harga'		 		=> $val_fix['harga'],
																'hpp' 				=> $hpp
														);

								$this->Keluar_sementara_model->insert_detail($simpanDetail);

								write_log();
							}
						}else{
							if ($val_fix['created'] == '') {
								date_default_timezone_set("Asia/Jakarta");
								$now = date('Y-m-d H:i:s');
								$harga_jual 		= $harga * $qty;
								$hpp_jual 			= $hpp * $qty;
								// $total 				= $ongkir;
								$total_jual 		= $harga_jual +  $ongkir;
								$total_harga		= $harga_jual;
								$simpanPenjualan	= array(	'nomor_pesanan'			=> $val_fix['nomor_pesanan'],
																'tgl_penjualan' 		=> $val_fix['tgl_penjualan'],
																'id_users' 				=> $this->session->userdata('id_users'),
																'id_status_transaksi' 	=> $val_fix['id_status_transaksi'],
																'id_toko' 				=> $val_fix['id_toko'],
																'id_kurir'	 			=> $val_fix['id_kurir'],
																'nomor_resi'			=> $val_fix['nomor_resi'],
																'nama_penerima' 		=> str_replace(';', '', $val_fix['nama_penerima']),
																'hp_penerima' 			=> $val_fix['hp_penerima'],
																'alamat_penerima' 		=> str_replace(';', ',', $val_fix['alamat_penerima']),
																'kabupaten' 			=> $val_fix['kabupaten'],
																'provinsi' 				=> $val_fix['provinsi'],
																'ongkir'		 		=> $val_fix['ongkir'],
																// 'biaya_admin'	 	=> $row->getCellAtIndex(11),
																'harga_jual'	 		=> $harga_jual,
																'total_hpp'	 			=> $hpp_jual,
																'total_jual' 			=> $total_jual,
																'total_harga' 			=> $total_harga,
																'margin' 				=> $total_jual - $ongkir - $hpp_jual,
																'selisih_margin' 		=> $total_harga - $total_jual,
																'jumlah_diterima' 		=> 0,
																'tgl_diterima' 			=> NULL,
																'created' 				=> $now 	
														);

								$this->Keluar_sementara_model->insert($simpanPenjualan);

								write_log();


								$simpanDetail		= array(	'nomor_pesanan'		=> $val_fix['nomor_pesanan'],
																'id_produk' 		=> $val_fix['id_produk'],
																'qty' 				=> $val_fix['qty'],
																'harga'		 		=> $val_fix['harga'],
																'hpp' 				=> $hpp
														);

								$this->Keluar_sementara_model->insert_detail($simpanDetail);

								write_log();

								$baris++;
							}else{
								date_default_timezone_set("Asia/Jakarta");
								$harga_jual 		= $harga * $qty;
								$hpp_jual 			= $hpp * $qty;
								// $total 				= $ongkir;
								$total_jual 		= $harga_jual +  $ongkir;
								$total_harga		= $harga_jual;
								$simpanPenjualan	= array(	'nomor_pesanan'			=> $val_fix['nomor_pesanan'],
																'tgl_penjualan' 		=> $val_fix['tgl_penjualan'],
																'id_users' 				=> $this->session->userdata('id_users'),
																'id_status_transaksi' 	=> $val_fix['id_status_transaksi'],
																'id_toko' 				=> $val_fix['id_toko'],
																'id_kurir'	 			=> $val_fix['id_kurir'],
																'nomor_resi'			=> $val_fix['nomor_resi'],
																'nama_penerima' 		=> str_replace(';', '', $val_fix['nama_penerima']),
																'hp_penerima' 			=> $val_fix['hp_penerima'],
																'alamat_penerima' 		=> str_replace(';', ',', $val_fix['alamat_penerima']),
																'kabupaten' 			=> $val_fix['kabupaten'],
																'provinsi' 				=> $val_fix['provinsi'],
																'ongkir'		 		=> $val_fix['ongkir'],
																// 'biaya_admin'	 	=> $row->getCellAtIndex(11),
																'harga_jual'	 		=> $harga_jual,
																'total_hpp'	 			=> $hpp_jual,
																'total_jual' 			=> $total_jual,
																'total_harga' 			=> $total_harga,
																'margin' 				=> $total_jual - $ongkir - $hpp_jual,
																'selisih_margin' 		=> $total_harga - $total_jual,
																'jumlah_diterima' 		=> 0,
																'tgl_diterima' 			=> NULL,
																'created' 				=> $val_fix['created']	
														);

								$this->Keluar_sementara_model->insert($simpanPenjualan);

								write_log();


								$simpanDetail		= array(	'nomor_pesanan'		=> $val_fix['nomor_pesanan'],
																'id_produk' 		=> $val_fix['id_produk'],
																'qty' 				=> $val_fix['qty'],
																'harga'		 		=> $val_fix['harga'],
																'hpp' 				=> $hpp
														);

								$this->Keluar_sementara_model->insert_detail($simpanDetail);

								write_log();

								$baris++;
							}
						}
					}
					// Foreach	
					if ($validasi > 0) {
						$msg = array(	'sukses'	=> $baris.' Data Berhasil Import Penjualan ke Data Sementara!',
										'pesan_error'	=> $msg_err
			    			);
				    	echo json_encode($msg);	
					}else{
						$msg = array(	'sukses'	=> $baris.' Data Berhasil Import Penjualan ke Data Sementara!'
			    			);
				    	echo json_encode($msg);
					}
				}else{
					if ($validasi > 0) {
						$msg = array(	'validasi'		=> 'Tidak ada data yang diimport!',
										'pesan_error'	=> $msg_err
		    			);
				    	echo json_encode($msg);
					}else{
						$msg = array(	'validasi'		=> 'Tidak ada data yang diimport!'
		    			);
				    	echo json_encode($msg);	
					}
				}
			}else{
				$error = array('validasi' => $this->upload->display_errors());
				echo json_encode($error);
			}
		}
	}	

	public function proses_impor()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc'.time();	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('impor_penjualan')) {
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/'.$file['file_name']);
			$numSheet 	= 0;
			$jumlah = 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 0) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow > 1) {
							$cells 	   = $row->getCells();
							$produk    = $this->Produk_model->get_by_id($row->getCellAtIndex(11));
							// $produk    = $this->Produk_model->get_by_id($cells[11]->getValue());
							$ongkir    = $cells[10]->getValue();
							// $admin     = $cells[11]->getValue();
							$hpp 	   = $produk->hpp_produk;
							$harga 	   = $cells[13]->getValue();
							$qty 	   = $cells[12]->getValue();
							// $cek_nomor = $this->Keluar_model->get_all_detail_by_id_row($row->getCellAtIndex(0));
							$cek_nomor = $this->Keluar_model->get_all_detail_by_id_row($row->getCellAtIndex(0));
							if (isset($cek_nomor)) {
								$harga_jual 		= $cek_nomor->harga_jual + $harga;
								$total_hpp			= $cek_nomor->total_hpp + ($hpp * $qty);
								$total_jual 		= $cek_nomor->total_jual + $harga_jual;
								$total_harga 		= $cek_nomor->total_harga + $harga_jual;
								$selisih_margin 	= $total_jual - $total_hpp;
								$margin 			= $total_jual - $total_harga;
								$ubahPenjualan 	= array(	'harga_jual' 		=> $harga_jual,
															'total_hpp' 		=> $total_hpp,
															'total_jual' 		=> $total_jual,
															'total_harga' 		=> $total_harga,
															'margin'	 		=> $margin,
															'selisih_margin'	=> $selisih_margin,
														);

								$this->Keluar_model->update($cek_nomor->nomor_pesanan, $ubahPenjualan);

								write_log();

								$simpanDetail		= array(	'nomor_pesanan'		=> $row->getCellAtIndex(0),
																'id_produk' 		=> $row->getCellAtIndex(11),
																'qty' 				=> $row->getCellAtIndex(12),
																'harga'		 		=> $row->getCellAtIndex(13),
																'hpp' 				=> $hpp
														);

								$this->Keluar_model->insert_detail($simpanDetail);

								write_log();

								// START - Kurangin jumlah total jika ada didalam paket
					          	$cariPaket = $this->Produk_model->get_all_by_id($row->getCellAtIndex(11));
					          	if (isset($cariPaket)) {
					          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
					          		if (count($produkPaket) > 1) {
					          			// echo print_r($produkPaket);
					          			foreach ($produkPaket as $result) {
					          				$total = $result->qty_pakduk * $qty;
						          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
											          	);
											$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	

											write_log();
					          			}
					          		}else{
					          			$rowPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->row();
					          			$total = $rowPaket->qty_pakduk * $qty;
					          			$kurangStokPakduk = array(	'qty_produk' 		=> $rowPaket->qty_produk - $total
										          	);
										$this->Produk_model->update($rowPaket->id_produk, $kurangStokPakduk);

										write_log();
					          		}
					          	}
					          	// END

								$cariProduk 			= $this->Produk_model->get_by_id($row->getCellAtIndex(11));
					          	$kurangStokProduk 		= array(	'qty_produk' 		=> $cariProduk->qty_produk - $qty
										          			);

					          	$this->Produk_model->update($row->getCellAtIndex(11), $kurangStokProduk);

					          	write_log();
							}else{
								if ($row->getCellAtIndex(14) == '') {
									date_default_timezone_set("Asia/Jakarta");
									$now = date('Y-m-d H:i:s');
									$harga_jual 		= $harga * $qty;
									$hpp_jual 			= $hpp * $qty;
									$total 				= $ongkir;
									$total_jual 		= $harga_jual +  $ongkir;
									$total_harga		= $harga_jual +  $total;
									$simpanPenjualan	= array(	'nomor_pesanan'			=> $row->getCellAtIndex(0),
																	'tgl_penjualan' 		=> $row->getCellAtIndex(1),
																	'id_users' 				=> $this->session->userdata('id_users'),
																	'id_status_transaksi' 	=> $row->getCellAtIndex(15),
																	'id_toko' 				=> $row->getCellAtIndex(2),
																	'id_kurir'	 			=> $row->getCellAtIndex(3),
																	'nomor_resi'			=> $row->getCellAtIndex(4),
																	'nama_penerima' 		=> $row->getCellAtIndex(5),
																	'hp_penerima' 			=> $row->getCellAtIndex(6),
																	'alamat_penerima' 		=> str_replace(';', ',', $row->getCellAtIndex(7)),
																	'kabupaten' 			=> $row->getCellAtIndex(8),
																	'provinsi' 				=> $row->getCellAtIndex(9),
																	'ongkir'		 		=> $row->getCellAtIndex(10),
																	// 'biaya_admin'	 	=> $row->getCellAtIndex(11),
																	'harga_jual'	 		=> $harga_jual,
																	'total_hpp'	 			=> $hpp_jual,
																	'total_jual' 			=> $total_jual,
																	'total_harga' 			=> $total_harga,
																	'selisih_margin' 		=> $total_jual - $total - $hpp_jual,
																	'margin' 				=> $total_jual - $total_harga,
																	'jumlah_diterima' 		=> 0,
																	'tgl_diterima' 			=> NULL,
																	'created' 				=> $now 	
															);

									$this->Keluar_model->insert($simpanPenjualan);

									write_log();

									if ($row->getCellAtIndex(4) != '') {
										$data = array(	'nomor_resi'	=> $row->getCellAtIndex(4),
														'id_users' 		=> $this->session->userdata('id_users'), 
														'status' 		=> 0,
														'tgl_resi' 		=> $now,
														'created_resi'	=> $now, 
										);
										$this->Resi_model->insert($data);

										write_log();	
									}

									$simpanDetail		= array(	'nomor_pesanan'		=> $row->getCellAtIndex(0),
																	'id_produk' 		=> $row->getCellAtIndex(11),
																	'qty' 				=> $row->getCellAtIndex(12),
																	'harga'		 		=> $row->getCellAtIndex(13),
																	'hpp' 				=> $hpp
															);

									$this->Keluar_model->insert_detail($simpanDetail);

									write_log();

									// START - Kurangin jumlah total jika ada didalam paket
						          	$cariPaket = $this->Produk_model->get_all_by_id($row->getCellAtIndex(11));
						          	if (isset($cariPaket)) {
						          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
						          		if (count($produkPaket) > 1) {
						          			// echo print_r($produkPaket);
						          			foreach ($produkPaket as $result) {
						          				$total = $result->qty_pakduk * $qty;
							          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
												          	);
												$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	

												write_log();
						          			}
						          		}else{
						          			$rowPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->row();
						          			$total = $rowPaket->qty_pakduk * $qty;
						          			$kurangStokPakduk = array(	'qty_produk' 		=> $rowPaket->qty_produk - $total
											          	);
											$this->Produk_model->update($rowPaket->id_produk, $kurangStokPakduk);

											write_log();
						          		}
						          	}
						          	// END
									
									$cariProduk 			= $this->Produk_model->get_by_id($row->getCellAtIndex(11));
						          	$kurangStokProduk 		= array(	'qty_produk' 		=> $cariProduk->qty_produk - $qty
											          			);

						          	$this->Produk_model->update($row->getCellAtIndex(11), $kurangStokProduk);

						          	write_log();	
								}else{
									date_default_timezone_set("Asia/Jakarta");
									$harga_jual 		= $harga * $qty;
									$hpp_jual 			= $hpp * $qty;
									$total 				= $ongkir;
									$total_jual 		= $harga_jual +  $ongkir;
									$total_harga		= $harga_jual +  $total;
									$simpanPenjualan	= array(	'nomor_pesanan'			=> $row->getCellAtIndex(0),
																	'tgl_penjualan' 		=> $row->getCellAtIndex(1),
																	'id_users' 				=> $this->session->userdata('id_users'),
																	'id_status_transaksi' 	=> $row->getCellAtIndex(15),
																	'id_toko' 				=> $row->getCellAtIndex(2),
																	'id_kurir'	 			=> $row->getCellAtIndex(3),
																	'nomor_resi'			=> $row->getCellAtIndex(4),
																	'nama_penerima' 		=> $row->getCellAtIndex(5),
																	'hp_penerima' 			=> $row->getCellAtIndex(6),
																	'alamat_penerima' 		=> str_replace(';', ',', $row->getCellAtIndex(7)),
																	'kabupaten' 			=> $row->getCellAtIndex(8),
																	'provinsi' 				=> $row->getCellAtIndex(9),
																	'ongkir'		 		=> $row->getCellAtIndex(10),
																	// 'biaya_admin'	 	=> $row->getCellAtIndex(11),
																	'harga_jual'	 		=> $harga_jual,
																	'total_hpp'	 			=> $hpp_jual,
																	'total_jual' 			=> $total_jual,
																	'total_harga' 			=> $total_harga,
																	'selisih_margin' 		=> $total_jual - $total - $hpp_jual,
																	'margin' 				=> $total_jual - $total_harga,
																	'jumlah_diterima' 		=> 0,
																	'tgl_diterima' 			=> NULL,
																	'created' 				=> $row->getCellAtIndex(14)	
															);

									$this->Keluar_model->insert($simpanPenjualan);

									write_log();

									if ($row->getCellAtIndex(4) != '') {
										$data = array(	'nomor_resi'	=> $row->getCellAtIndex(4),
													'id_users' 		=> $this->session->userdata('id_users'), 
													'status' 		=> 0,
													'tgl_resi' 		=> $row->getCellAtIndex(1),
													'created_resi'	=> $row->getCellAtIndex(14),  
										);
										$this->Resi_model->insert($data);

										write_log();	
									}

									$simpanDetail		= array(	'nomor_pesanan'		=> $row->getCellAtIndex(0),
																	'id_produk' 		=> $row->getCellAtIndex(11),
																	'qty' 				=> $row->getCellAtIndex(12),
																	'harga'		 		=> $row->getCellAtIndex(13),
																	'hpp' 				=> $hpp
															);

									$this->Keluar_model->insert_detail($simpanDetail);

									write_log();

									// START - Kurangin jumlah total jika ada didalam paket
						          	$cariPaket = $this->Produk_model->get_all_by_id($row->getCellAtIndex(11));
						          	if (isset($cariPaket)) {
						          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
						          		if (count($produkPaket) > 1) {
						          			// echo print_r($produkPaket);
						          			foreach ($produkPaket as $result) {
						          				$total = $result->qty_pakduk * $qty;
							          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
												          	);
												$this->Produk_model->update($result->id_produk, $kurangStokPakduk);	

												write_log();
						          			}
						          		}else{
						          			$rowPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->row();
						          			$total = $rowPaket->qty_pakduk * $qty;
						          			$kurangStokPakduk = array(	'qty_produk' 		=> $rowPaket->qty_produk - $total
											          	);
											$this->Produk_model->update($rowPaket->id_produk, $kurangStokPakduk);

											write_log();
						          		}
						          	}
						          	// END
									
									$cariProduk 			= $this->Produk_model->get_by_id($row->getCellAtIndex(11));
						          	$kurangStokProduk 		= array(	'qty_produk' 		=> $cariProduk->qty_produk - $qty
											          			);

						          	$this->Produk_model->update($row->getCellAtIndex(11), $kurangStokProduk);

						          	write_log();
								}
							}	
						}
						$numRow++;
						$jumlah++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">'.$jumlah.' Data imported successfully</div>');
					redirect('admin/keluar/data_penjualan');
				}
				$numSheet++;
			}
		}else{
			// $error = array('error' => $this->upload->display_errors());
			$this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
			redirect('admin/keluar/penjualan_produk');
			// return $error;
		}
	}

	public function proses_impor_diterima_new()
	{
		if ($this->input->post('jenis') == 'pending') {
			// echo "ini pending";
			$config['upload_path'] 		= './uploads/';
			$config['allowed_types'] 	= 'xlsx|xls';
			$config['file_name']		= 'doc'.time();	
			$msg_err = '';
			$validasi = 0;
			// $config['max_size']  = '100';
			// $config['max_width']  = '1024';
			// $config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('impor_diterima')) {
				$file 		= $this->upload->data();
				$reader 	= ReaderEntityFactory::createXLSXReader();

				$reader->setShouldFormatDates(true);
				$reader->open('uploads/'.$file['file_name']);

				$baris = 0;
				$numSheet 	= 0;
				foreach ($reader->getSheetIterator() as $sheet) {
					$numRow = 1;
					if ($numSheet == 0) {
						foreach ($sheet->getRowIterator() as $row) {
							if ($numRow == 1) {
								if ($row->getCellAtIndex(0) != 'Nomor Pesanan' || $row->getCellAtIndex(1) != 'Jumlah Diterima' || $row->getCellAtIndex(2) != 'Tanggal Diterima') {
									$reader->close();
									unlink('uploads/'.$file['file_name']);

									$msg = array(	'validasi'		=> 'Data import tidak sesuai!',
					    			);
							    	echo json_encode($msg);
								}
							}

							if ($numRow > 1) {
								$cells 	    = $row->getCells();
								$jumlah     = $cells[1]->getValue();
								$date 		= date('Y-m-d H:i:s', strtotime($row->getCellAtIndex(2)));
								
								$row_detail = $this->Keluar_model->get_all_by_id($row->getCellAtIndex(0));
								if (isset($row_detail)) {
									if ($row_detail->id_status_transaksi != 4) {
										if ($row_detail->jumlah_diterima == 0 || $row_detail->jumlah_diterima =='' || $row_detail->jumlah_diterima == NULL) {
											$bruto = (int)$row_detail->total_jual;
											$harga = (int)$row_detail->total_harga;
											$hpp = (int)$row_detail->total_hpp;
											$diterima = (int) $jumlah;
											$ongkir = (int)$row_detail->ongkir;

											$hasil_harga_diterima = $harga - $diterima;
											
											if ($hasil_harga_diterima <= 0) {
												$hasil_harga_diterima = $hasil_harga_diterima * -1;	
											
												if (($bruto - $diterima) != $ongkir) {
													$selisih_margin = ($bruto - $diterima - $ongkir) * -1;
												}else{
													$selisih_margin = 0;
												}

												if ($selisih_margin <= 0) {
													$new_bruto = $bruto + (int) $selisih_margin;	
													$margin = $diterima  - $hpp;
												}else{
													$new_bruto = $bruto - (int) $selisih_margin;
													$margin = $diterima  - $hpp;
												}
											}else{
												if (($bruto - $diterima) != $ongkir) {
													$selisih_margin = ($bruto - $diterima - $ongkir) * -1;
												}else{
													$selisih_margin = 0;
												}

												if ($selisih_margin <= 0) {
													$new_bruto = $bruto + (int) $selisih_margin;	
													$margin = $diterima  - $hpp;
												}else{
													$new_bruto = $bruto - (int) $selisih_margin;
													$margin = $diterima - $hpp;
												}
											}									

											$ubahPenjualan = array(	'total_jual'			=> $new_bruto,
																	'selisih_margin'		=> $selisih_margin,
																	'margin'				=> $margin,
																	'jumlah_diterima'		=> $jumlah,
																	'id_status_transaksi' 	=> 3,
																	'tgl_diterima' 			=> $date
											);

											$this->Keluar_model->update($row_detail->nomor_pesanan, $ubahPenjualan);
											write_log();
											
											date_default_timezone_set("Asia/Jakarta");
											$now = date('Y-m-d H:i:s');
											$updateStatusResi = array(	'id_users' 		=> $this->session->userdata('id_users'), 
																		'status' 		=> 2,
																		'tgl_resi' 		=> $now  
															);
											$this->Resi_model->update_by_resi($row_detail->nomor_resi,$updateStatusResi);
											write_log();
											
											$baris++;	
										}else{
											$msg_err .= 'Nomor Pesanan: <b>'.$row_detail->nomor_pesanan.'</b> sudah dibayar sebesar <b>'.$row_detail->jumlah_diterima.'</b>! <br>';
											$validasi++;
										}
									}	
								}else{
									$msg_err .= 'Nomor Pesanan: <b>'.$row->getCellAtIndex(0).'</b> tidak ditemukan! <br>';
									$validasi++;
								}
							}
						$numRow++;
						}

						$reader->close();
						unlink('uploads/'.$file['file_name']);
					}
				$numSheet++;
				}

				if ($baris > 0) {
					if ($validasi > 0) {
						$msg = array(	'sukses'	=> $baris.' Data payment successfully!',
										'pesan_error'	=> $msg_err
			    			);
				    	echo json_encode($msg);	
					}else{
						$msg = array(	'sukses'	=> $baris.' Data payment successfully!'
			    			);
				    	echo json_encode($msg);
					}	
				}else{
					if ($validasi > 0) {
						$msg = array(	'validasi'	=> 'No data payment successfully!',
										'pesan_error'	=> $msg_err
			    			);
				    	echo json_encode($msg);	
					}else{
						$msg = array(	'validasi'	=> 'No data payment successfully!'
			    			);
				    	echo json_encode($msg);
					}	
				}
			}else{
				$pesan = strip_tags($this->upload->display_errors());
				$msg = array(	'validasi'	=> $pesan
		    			);
		    	echo json_encode($msg);
			}
		}elseif ($this->input->post('jenis') == 'transfer') {
			// echo "ini transfer";
			$config['upload_path'] 		= './uploads/';
			$config['allowed_types'] 	= 'xlsx|xls';
			$config['file_name']		= 'doc'.time();	
			$msg_err = '';
			// $config['max_size']  = '100';
			// $config['max_width']  = '1024';
			// $config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('impor_diterima')) {
				$file 		= $this->upload->data();
				$reader 	= ReaderEntityFactory::createXLSXReader();

				$reader->setShouldFormatDates(true);
				$reader->open('uploads/'.$file['file_name']);

				$baris = 0;
				$numSheet 	= 0;
				foreach ($reader->getSheetIterator() as $sheet) {
					$numRow = 1;
					if ($numSheet == 0) {
						foreach ($sheet->getRowIterator() as $row) {
							if ($numRow == 1) {
								if ($row->getCellAtIndex(0) != 'Nomor Pesanan' || $row->getCellAtIndex(1) != 'Jumlah Diterima' || $row->getCellAtIndex(2) != 'Tanggal Diterima') {
									$reader->close();
									unlink('uploads/'.$file['file_name']);

									$msg = array(	'validasi'		=> 'Data import tidak sesuai!',
					    			);
							    	echo json_encode($msg);
								}
							}
							
							if ($numRow > 1) {
								$cells 	    = $row->getCells();
								$jumlah     = $cells[1]->getValue();
								$date 		= date('Y-m-d H:i:s', strtotime($row->getCellAtIndex(2)));
								
								$row_detail = $this->Keluar_model->get_all_by_id($row->getCellAtIndex(0));
								if (isset($row_detail)) {
									if ($row_detail->id_status_transaksi != 4 AND $row_detail->id_status_transaksi == 2) {
										if ($row_detail->jumlah_diterima == 0 || $row_detail->jumlah_diterima == '' || $row_detail->jumlah_diterima == NULL) {
											$bruto = (int)$row_detail->total_jual;
											$harga = (int)$row_detail->total_harga;
											$hpp = (int)$row_detail->total_hpp;
											$diterima = (int) $jumlah;
											$ongkir = (int)$row_detail->ongkir;

											$hasil_harga_diterima = $harga - $diterima;
											
											if ($hasil_harga_diterima <= 0) {
												$hasil_harga_diterima = $hasil_harga_diterima * -1;	
											
												if (($bruto - $diterima) != $ongkir) {
													$selisih_margin = ($bruto - $diterima - $ongkir) * -1;
												}else{
													$selisih_margin = 0;
												}

												if ($selisih_margin <= 0) {
													$new_bruto = $bruto + (int) $selisih_margin;	
													$margin = $diterima  - $hpp;
												}else{
													$new_bruto = $bruto - (int) $selisih_margin;
													$margin = $diterima  - $hpp;
												}
											}else{
												if (($bruto - $diterima) != $ongkir) {
													$selisih_margin = ($bruto - $diterima - $ongkir) * -1;
												}else{
													$selisih_margin = 0;
												}

												if ($selisih_margin <= 0) {
													$new_bruto = $bruto + (int) $selisih_margin;	
													$margin = $diterima  - $hpp;
												}else{
													$new_bruto = $bruto - (int) $selisih_margin;
													$margin = $diterima - $hpp;
												}
											}									

											$ubahPenjualan = array(	'total_jual'			=> $new_bruto,
																	'selisih_margin'		=> $selisih_margin,
																	'margin'				=> $margin,
																	'jumlah_diterima'		=> $jumlah,
																	'id_status_transaksi' 	=> 3,
																	'tgl_diterima' 			=> $date
											);

											$this->Keluar_model->update($row_detail->nomor_pesanan, $ubahPenjualan);
											write_log();

											date_default_timezone_set("Asia/Jakarta");
											$now = date('Y-m-d H:i:s');
											$updateStatusResi = array(	'id_users' 		=> $this->session->userdata('id_users'), 
																		'status' 		=> 2,
																		'tgl_resi' 		=> $now  
															);
											$this->Resi_model->update_by_resi($row_detail->nomor_resi,$data);
											write_log();

											$baris++;			
										}else{
											$msg_err .= 'Nomor Pesanan: <b>'.$row_detail->nomor_pesanan.'</b> sudah dibayar sebesar <b>'.$row_detail->jumlah_diterima.'</b>! <br>';
											$validasi++;
										}								
									}else{
										$msg_err .= 'Status Transaksi Nomor Pesanan: <b>'.$row->getCellAtIndex(0).'</b> tidak sesuai! <br>';
									$validasi++;
									}	
								}else{
									$msg_err .= 'Nomor Pesanan: <b>'.$row->getCellAtIndex(0).'</b> tidak ditemukan! <br>';
									$validasi++;
								}
							}
						$numRow++;
						}
						$reader->close();
						unlink('uploads/'.$file['file_name']);
					}
				$numSheet++;
				}

				if ($validasi > 0) {
					$msg = array(	'sukses'	=> $baris.' Data payment successfully!',
									'pesan_error'	=> $msg_err
		    			);
			    	echo json_encode($msg);	
				}else{
					$msg = array(	'sukses'	=> $baris.' Data payment successfully!'
		    			);
			    	echo json_encode($msg);
				}
			}else{
				$pesan = strip_tags($this->upload->display_errors());
				$msg = array(	'validasi'	=> $pesan
		    			);
		    	echo json_encode($msg);
			}
		}
	}

	public function proses_impor_diterima()
	{
		if ($this->input->post('status_diterima') == 'pending') {
			// echo "ini pending";
			$config['upload_path'] 		= './uploads/';
			$config['allowed_types'] 	= 'xlsx|xls';
			$config['file_name']		= 'doc'.time();	
			// $config['max_size']  = '100';
			// $config['max_width']  = '1024';
			// $config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('impor_diterima')) {
				$file 		= $this->upload->data();
				$reader 	= ReaderEntityFactory::createXLSXReader();

				$reader->setShouldFormatDates(true);
				$reader->open('uploads/'.$file['file_name']);

				$baris = 0;
				$numSheet 	= 0;
				foreach ($reader->getSheetIterator() as $sheet) {
					$numRow = 1;
					if ($numSheet == 0) {
						foreach ($sheet->getRowIterator() as $row) {
							if ($numRow > 1) {
								$cells 	    = $row->getCells();
								$jumlah     = $cells[1]->getValue();
								$date 		= date('Y-m-d H:i:s', strtotime($row->getCellAtIndex(2)));
								
								$row_detail = $this->Keluar_model->get_all_by_id($row->getCellAtIndex(0));
								if ($row_detail->id_status_transaksi != 4) {
									$bruto = (int)$row_detail->total_jual;
									$harga = (int)$row_detail->total_harga;
									$hpp = (int)$row_detail->total_hpp;
									$diterima = (int) $jumlah;
									$ongkir = (int)$row_detail->ongkir;

									$hasil_harga_diterima = $harga - $diterima;
									
									if ($hasil_harga_diterima <= 0) {
										$hasil_harga_diterima = $hasil_harga_diterima * -1;	
									
										if (($bruto - $diterima) != $ongkir) {
											$selisih_margin = ($bruto - $diterima - $ongkir);
										}else{
											$selisih_margin = 0;
										}

										if ($selisih_margin <= 0) {
											$new_bruto = $bruto + $selisih_margin;	
										}else{
											$new_bruto = $bruto - $selisih_margin;
										}

										$margin = $diterima  - $hpp - $hasil_harga_diterima;

									}else{
										if (($bruto - $diterima) != $ongkir) {
											$selisih_margin = ($bruto - $diterima - $ongkir);
										}else{
											$selisih_margin = 0;
										}

										if ($selisih_margin <= 0) {
											$new_bruto = $bruto + $selisih_margin;	
										}else{
											$new_bruto = $bruto - $selisih_margin;
										}

										$margin = $diterima  - $hpp;
									}									

									$ubahPenjualan = array(	'total_jual'			=> $new_bruto,
															'selisih_margin'		=> $selisih_margin,
															'margin'				=> $margin,
															'jumlah_diterima'		=> $jumlah,
															'id_status_transaksi' 	=> 3,
															'tgl_diterima' 			=> $date
									);

									$this->Keluar_model->update($row_detail->nomor_pesanan, $ubahPenjualan);
									write_log();
									
									date_default_timezone_set("Asia/Jakarta");
									$now = date('Y-m-d H:i:s');
									$updateStatusResi = array(	'id_users' 		=> $this->session->userdata('id_users'), 
																'status' 		=> 2,
																'tgl_resi' 		=> $now  
													);
									$this->Resi_model->update_by_resi($row_detail->nomor_resi,$updateStatusResi);
									write_log();
									
									$baris++;
								}
							}
						$numRow++;
						}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">'.$baris.' Data payment successfully</div>');
					redirect('admin/keluar/data_penjualan');
					}
				$numSheet++;
				}
			}else{
				$error = array('error' => $this->upload->display_errors());
				$this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
				redirect('admin/keluar/penjualan_produk');
				return $error;
			}
		}elseif ($this->input->post('status_diterima') == 'transfer') {
			// echo "ini transfer";
			$config['upload_path'] 		= './uploads/';
			$config['allowed_types'] 	= 'xlsx|xls';
			$config['file_name']		= 'doc'.time();	
			// $config['max_size']  = '100';
			// $config['max_width']  = '1024';
			// $config['max_height']  = '768';
			
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('impor_diterima')) {
				$file 		= $this->upload->data();
				$reader 	= ReaderEntityFactory::createXLSXReader();

				$reader->setShouldFormatDates(true);
				$reader->open('uploads/'.$file['file_name']);

				$baris = 0;
				$numSheet 	= 0;
				foreach ($reader->getSheetIterator() as $sheet) {
					$numRow = 1;
					if ($numSheet == 0) {
						foreach ($sheet->getRowIterator() as $row) {
							if ($numRow > 1) {
								$cells 	    = $row->getCells();
								$jumlah     = $cells[1]->getValue();
								$date 		= date('Y-m-d H:i:s', strtotime($row->getCellAtIndex(2)));
								
								$row_detail = $this->Keluar_model->get_all_by_id($row->getCellAtIndex(0));
								if ($row_detail->id_status_transaksi != 4 AND $row_detail->id_status_transaksi == 2) {
									$bruto = (int)$row_detail->total_jual;
									$harga = (int)$row_detail->total_harga;
									$hpp = (int)$row_detail->total_hpp;
									$diterima = (int) $jumlah;
									$ongkir = (int)$row_detail->ongkir;

									$hasil_harga_diterima = $harga - $diterima;
									
									if ($hasil_harga_diterima < 0) {
										$hasil_harga_diterima = $hasil_harga_diterima * -1;	
									
										if (($bruto - $diterima) != $ongkir) {
											$selisih_margin = ($bruto - $diterima - $ongkir);
										}else{
											$selisih_margin = 0;
										}

										if ($selisih_margin < 0) {
											$new_bruto = $bruto + $selisih_margin;	
										}else{
											$new_bruto = $bruto - $selisih_margin;
										}

										$margin = $diterima  - $hpp - $hasil_harga_diterima;

									}else{
										if (($bruto - $diterima) != $ongkir) {
											$selisih_margin = ($bruto - $diterima - $ongkir);
										}else{
											$selisih_margin = 0;
										}

										if ($selisih_margin < 0) {
											$new_bruto = $bruto + $selisih_margin;	
										}else{
											$new_bruto = $bruto - $selisih_margin;
										}

										$margin = $diterima  - $hpp;
									}

									$ubahPenjualan = array(	'total_jual'			=> $new_bruto,
															'selisih_margin'		=> $selisih_margin,
															'margin'				=> $margin,
															'jumlah_diterima'		=> $jumlah,
															'tgl_diterima' 			=> $date
									);

									$this->Keluar_model->update($row_detail->nomor_pesanan, $ubahPenjualan);
									write_log();

									date_default_timezone_set("Asia/Jakarta");
									$now = date('Y-m-d H:i:s');
									$updateStatusResi = array(	'id_users' 		=> $this->session->userdata('id_users'), 
																'status' 		=> 2,
																'tgl_resi' 		=> $now  
													);
									$this->Resi_model->update_by_resi($row_detail->nomor_resi,$data);
									write_log();
								}
							}
						$numRow++;
						$baris++;
						}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">'.$baris.' Data payment successfully</div>');
					redirect('admin/keluar/data_penjualan');
					}
				$numSheet++;
				}
			}else{
				$error = array('error' => $this->upload->display_errors());
				$this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
				redirect('admin/keluar/penjualan_produk');
				return $error;
			}
		}
	}

	public function sinkron_total_harga()
	{
		is_create();
		$row = 0;
		$start 		 = substr($this->input->post('periodik'), 0, 10);
		$end 		 = substr($this->input->post('periodik'), 13, 24);
		$trigger  	 = $this->input->post('trigger');
		$cek_pesanan = $this->Keluar_model->get_all_by_periodik_impor($trigger, $start, $end);

		foreach ($cek_pesanan as $val_pesanan) {
			$cek_detail = $this->Keluar_model->get_detail_by_id($val_pesanan->nomor_pesanan);

			if (count($cek_detail) > 0) {
				$total_harga = 0;
				$total_jual = $val_pesanan->ongkir;
				$harga_jual  = 0;
				$hpp_jual 	 = 0;
				$ongkir	  	 = $val_pesanan->ongkir;
				foreach ($cek_detail as $val_detail) {
					$produk = $this->Produk_model->get_by_id($val_detail->id_produk);

					$updateDetail = array( 'hpp'	=> $produk->hpp_produk
					);
					
					$this->Keluar_model->update_detail($val_detail->nomor_pesanan, $val_detail->id_produk, $updateDetail);

					$hpp_jual = $hpp_jual + ($produk->hpp_produk * $val_detail->qty);
					$total_harga = $total_harga + ($val_detail->harga * $val_detail->qty);
					$total_jual = $total_jual + ($val_detail->harga * $val_detail->qty);
					$harga_jual = $harga_jual + ($val_detail->harga * $val_detail->qty);

					$updateTotalHarga = array ( 'harga_jual'	=> $harga_jual,
												'total_hpp'		=> $hpp_jual,
												'total_jual'	=> $total_jual,
												'total_harga'	=> $total_harga,
												'margin'		=> $total_jual - $ongkir - $hpp_jual,
												'selisih_margin'=> $total_harga	- $total_jual
											);

					$this->Keluar_model->update($val_detail->nomor_pesanan, $updateTotalHarga);
				}
			}

			$row++;
		}

		// $this->session->set_flashdata('message', '<div class="alert alert-success">'.$row.' Sync Data with Total Price successfully</div>');
		// redirect('admin/keluar/data_penjualan');

		$pesan = $row.' Sync Data with Total Price successfully';	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function sinkron_total_harga_sementara()
	{
		is_create();
		$row = 0;
		$start 		 = substr($this->input->post('periodik'), 0, 10);
		$end 		 = substr($this->input->post('periodik'), 13, 23);
		$cek_pesanan = $this->Keluar_sementara_model->get_all_by_periodik_sinkron($start, $end);

		foreach ($cek_pesanan as $val_pesanan) {
			$cek_detail = $this->Keluar_sementara_model->get_detail_by_id($val_pesanan->nomor_pesanan);

			if (count($cek_detail) > 0) {
				$total_harga = 0;
				$total_jual  = $val_pesanan->ongkir;
				$harga_jual  = 0;
				$hpp_jual 	 = 0;
				$ongkir	  	 = $val_pesanan->ongkir;
				foreach ($cek_detail as $val_detail) {
					$produk = $this->Produk_model->get_by_id($val_detail->id_produk);

					$updateDetail = array( 'hpp'	=> $produk->hpp_produk
					);
					
					$this->Keluar_sementara_model->update_detail($val_detail->nomor_pesanan, $val_detail->id_produk, $updateDetail);

					$hpp_jual = $hpp_jual + ($produk->hpp_produk * $val_detail->qty);
					$total_harga = $total_harga + ($val_detail->harga * $val_detail->qty);
					$total_jual = $total_jual + ($val_detail->harga * $val_detail->qty);
					$harga_jual = $harga_jual + ($val_detail->harga * $val_detail->qty);

					$updateTotalHarga = array ( 'harga_jual'	=> $harga_jual,
												'total_hpp'		=> $hpp_jual,
												'total_jual'	=> $total_jual,
												'total_harga'	=> $total_harga,
												'margin'		=> $total_jual - $ongkir - $hpp_jual,
												'selisih_margin'=> $total_harga	- $total_jual
											);

					$this->Keluar_sementara_model->update($val_detail->nomor_pesanan, $updateTotalHarga);
				
				}
			}

			$row++;
		}

		// $this->session->set_flashdata('message', '<div class="alert alert-success">'.$row.' Sync Data with Total Price successfully</div>');
		// redirect('admin/keluar/data_penjualan');

		$pesan = $row.' Sync Data with Total Price successfully';	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function get_id_provinsi()
	{
		$provinsi = $this->input->post('provinsi');
		$select_box[] = "<option value=''>- Pilih Kabupaten -</option>";
		// $kabupaten = json_decode(json_encode(kabupaten($provinsi)));
		$kabupaten = $this->Keyword_model->get_kabupaten_by_provinsi($provinsi);
		if (count($kabupaten) > 0) {
			foreach ($kabupaten as $val_kab) {
				$select_box[] = '<option value="'.$val_kab->nama_kotkab.'">'.$val_kab->nama_kotkab.'</option>';
			}
			// for ($i = 0; $i < count($kabupaten); $i++) {
			// 	$select_box[] = '<option value="'.$kabupaten[$i].'">'.$kabupaten[$i].'</option>';
			// }
			
			echo json_encode($select_box);
		}else{
			$select_box = '<option value="">Tidak Ada</option>';
			echo json_encode($select_box);
		}
	}

	public function get_id_toko()
	{
		$toko = $this->input->post('toko');
		$select_box[] = "<option value=''>- Pilih Nama Produk -</option>";
		$produk = $this->Keluar_model->get_id_toko($toko);
		if (count($produk) > 0) {
			foreach ($produk as $row) {
				$select_box[] = '<option value="'.$row->id_produk.'">'.$row->kode_sku.' | '.$row->sub_sku.' | '.$row->nama_produk.' | 	 Stok: '.$row->qty_produk.'</option>';
			}
			// header("Content-Type:application/json");
			echo json_encode($select_box);
		}else{
			$select_box = '<option value="">Tidak Ada</option>';
			echo json_encode($select_box);
		}
	}

	public function get_id_produk()
	{
		$produk = $this->input->post('produk');
		// $id_barang = "RPL2003200001";
		$cari_produk =	$this->Keluar_model->get_id_produk($produk);
		echo json_encode($cari_produk);
	}

	public function generate_nomor()
	{
		date_default_timezone_set("Asia/Jakarta");
		$date= date("Y-m-d");
		$tahun = substr($date, 2, 2);
		$bulan = substr($date, 5, 2);
		$tanggal = substr($date, 8, 2);
		$teks = "INV".$tahun.$bulan.$tanggal;
		$ambil_nomor = $this->Keluar_model->cari_nomor($teks);
		// echo print_r(json_encode($ambil_nomor));
		// $hitung = count($ambil_nomor);
		// echo $ambil_nomor->nomor_pesanan;
		if (isset($ambil_nomor)) {
			// TANGGAL DARI ID NILAI
			$ambil_tahun = substr($ambil_nomor->nomor_pesanan, 3, 2);
			$ambil_bulan = substr($ambil_nomor->nomor_pesanan, 5, 2);
			$ambil_tanggal = substr($ambil_nomor->nomor_pesanan, 7, 2);
			$ambil_no = (int) substr($ambil_nomor->nomor_pesanan, 9, 4);

			if ($tahun == $ambil_tahun && $bulan == $ambil_bulan && $tanggal == $ambil_tanggal) {
				$ambil_no++;	
				$no_masuk = "INV".$tahun.$bulan.$tanggal.sprintf("%04s", $ambil_no);
			}else{
				$no_masuk = "INV".$tahun.$bulan.$tanggal."0001";
			}

			echo json_encode($no_masuk);
		}else{
			$no_masuk = "INV".$tahun.$bulan.$tanggal."0001";

			echo json_encode($no_masuk);
		}
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

	// public function export_keluar($kurir, $toko, $resi, $status, $periodik)
	// {
	// 	$start = substr($periodik, 0, 10);
	// 	$end = substr($periodik, 17, 24);
	// 	$data['title']	= "Export Data Penjualan Per Tanggal ".$start." - ".$end."_".date("H_i_s");
	// 	$data['penjualan'] = $this->Keluar_model->get_datatable_all($status, $kurir, $toko, $resi, $start, $end);

	// 	$this->load->view('back/keluar/penjualan_export', $data);
	// }

	function export_keluar_penjualan($trigger,$kurir, $toko, $resi, $status, $periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Penjualan Per Tanggal ".$start." - ".$end."_".date("H_i_s");
		$data['penjualan'] = $this->Keluar_model->get_datatable_all($trigger,$status, $kurir, $toko, $resi, $start, $end);

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'nomor_pesanan');
		$sheet->setCellValue('B1', 'nomor_resi');
		$sheet->setCellValue('C1', 'tgl_penjualan');
		$sheet->setCellValue('D1', 'nama_kurir');
		$sheet->setCellValue('E1', 'nama_toko');
		$sheet->setCellValue('F1', 'nama_penerima');
		$sheet->setCellValue('G1', 'hp_penerima');
		$sheet->setCellValue('H1', 'alamat_penerima');
		$sheet->setCellValue('I1', 'kabupaten');
		$sheet->setCellValue('J1', 'provinsi');
		$sheet->setCellValue('K1', 'tgl_diterima');
		$sheet->setCellValue('L1', 'jumlah_diterima');
		$sheet->setCellValue('M1', 'total_harga');
		$sheet->setCellValue('N1', 'id_produk');
		$sheet->setCellValue('O1', 'sub_sku');
		$sheet->setCellValue('P1', 'nama_produk');
		$sheet->setCellValue('Q1', 'qty');
		$sheet->setCellValue('R1', 'harga');
		$sheet->setCellValue('S1', 'created');

        // set Row
        $rowCount = 2;
        foreach ($data['penjualan'] as $list) {
        	// Nomor Pesanan
	        if (is_numeric($list->nomor_pesanan)) {
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

	        // Nomor Resi
	        if (is_numeric($list->nomor_resi)) {
	          if (strlen($list->nomor_resi) < 15) {
	            $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('B' . $rowCount, $list->nomor_resi);
	          }else{
	            $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('B' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('B' . $rowCount, $list->nomor_resi, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('B' . $rowCount, $list->nomor_resi);
	        }

            $sheet->SetCellValue('C' . $rowCount, $list->tgl_penjualan);
            $sheet->SetCellValue('D' . $rowCount, $list->nama_kurir);
            $sheet->SetCellValue('E' . $rowCount, $list->nama_toko);
            $sheet->SetCellValue('F' . $rowCount, $list->nama_penerima);

	        // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('G' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('G' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('G' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('G' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('G' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('G' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('G' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('G'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('G'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }

            $sheet->SetCellValue('H' . $rowCount, $list->alamat_penerima);
            $sheet->SetCellValue('I' . $rowCount, $list->kabupaten);
            $sheet->SetCellValue('J' . $rowCount, $list->provinsi);
            $sheet->SetCellValue('K' . $rowCount, $list->tgl_diterima);
            $sheet->SetCellValue('L' . $rowCount, $list->jumlah_diterima);
            $sheet->SetCellValue('M' . $rowCount, $list->total_harga);
            $sheet->SetCellValue('N' . $rowCount, $list->id_produk);
            $sheet->SetCellValue('O' . $rowCount, $list->sub_sku);
            $sheet->SetCellValue('P' . $rowCount, $list->nama_produk);
            $sheet->SetCellValue('Q' . $rowCount, $list->qty);
            $sheet->SetCellValue('R' . $rowCount, $list->harga);
            $sheet->SetCellValue('S' . $rowCount, $list->created);

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

	// public function export_keluar_sementara($kurir, $toko, $resi, $status, $periodik)
	// {
	// 	$start = substr($periodik, 0, 10);
	// 	$end = substr($periodik, 17, 24);
	// 	$data['title']	= "Export Data Penjualan Sementara Per Tanggal ".$start." - ".$end."_".date("H_i_s");
	// 	$data['penjualan'] = $this->Keluar_sementara_model->get_datatable_all($status, $kurir, $toko, $resi, $start, $end);

	// 	$this->load->view('back/keluar/penjualan_export', $data);
	// }

	function export_keluar_sementara_penjualan($kurir, $toko, $resi, $status, $periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Penjualan Sementara Per Tanggal ".$start." - ".$end."_".date("H_i_s");
		$data['penjualan'] = $this->Keluar_sementara_model->get_datatable_all($status, $kurir, $toko, $resi, $start, $end);

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'nomor_pesanan');
		$sheet->setCellValue('B1', 'nomor_resi');
		$sheet->setCellValue('C1', 'tgl_penjualan');
		$sheet->setCellValue('D1', 'nama_kurir');
		$sheet->setCellValue('E1', 'nama_toko');
		$sheet->setCellValue('F1', 'nama_penerima');
		$sheet->setCellValue('G1', 'hp_penerima');
		$sheet->setCellValue('H1', 'alamat_penerima');
		$sheet->setCellValue('I1', 'kabupaten');
		$sheet->setCellValue('J1', 'provinsi');
		$sheet->setCellValue('K1', 'tgl_diterima');
		$sheet->setCellValue('L1', 'jumlah_diterima');
		$sheet->setCellValue('M1', 'total_harga');
		$sheet->setCellValue('N1', 'id_produk');
		$sheet->setCellValue('O1', 'sub_sku');
		$sheet->setCellValue('P1', 'nama_produk');
		$sheet->setCellValue('Q1', 'qty');
		$sheet->setCellValue('R1', 'harga');
		$sheet->setCellValue('S1', 'created');

        // set Row
        $rowCount = 2;
        foreach ($data['penjualan'] as $list) {
        	// Nomor Pesanan
	        if (is_numeric($list->nomor_pesanan)) {
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

	        // Nomor Resi
	        if (is_numeric($list->nomor_resi)) {
	          if (strlen($list->nomor_resi) < 15) {
	            $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('B' . $rowCount, $list->nomor_resi);
	          }else{
	            $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('B' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('B' . $rowCount, $list->nomor_resi, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('B' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('B' . $rowCount, $list->nomor_resi);
	        }

            $sheet->SetCellValue('C' . $rowCount, $list->tgl_penjualan);
            $sheet->SetCellValue('D' . $rowCount, $list->nama_kurir);
            $sheet->SetCellValue('E' . $rowCount, $list->nama_toko);
            $sheet->SetCellValue('F' . $rowCount, $list->nama_penerima);

	        // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('G' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('G' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('G' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('G' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('G' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('G' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('G' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('G'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('G'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }

            $sheet->SetCellValue('H' . $rowCount, $list->alamat_penerima);
            $sheet->SetCellValue('I' . $rowCount, $list->kabupaten);
            $sheet->SetCellValue('J' . $rowCount, $list->provinsi);
            $sheet->SetCellValue('K' . $rowCount, $list->tgl_diterima);
            $sheet->SetCellValue('L' . $rowCount, $list->jumlah_diterima);
            $sheet->SetCellValue('M' . $rowCount, $list->total_harga);
            $sheet->SetCellValue('N' . $rowCount, $list->id_produk);
            $sheet->SetCellValue('O' . $rowCount, $list->sub_sku);
            $sheet->SetCellValue('P' . $rowCount, $list->nama_produk);
            $sheet->SetCellValue('Q' . $rowCount, $list->qty);
            $sheet->SetCellValue('R' . $rowCount, $list->harga);
            $sheet->SetCellValue('S' . $rowCount, $list->created);

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

	public function get_toko_sku_by_periodik()
	{
		$start 					= substr($this->input->post('periodik'), 0, 10);
		$end 					= substr($this->input->post('periodik'), 13, 24);
		$get_toko 				= $this->Keluar_model->get_toko_sku($start, $end);
		$get_toko_penjualan 	= $this->Keluar_model->get_toko_sku_penjualan($start, $end);

		$pesan = "Berhasil disimpan!";	
		$msg = array(	'sukses'			=> $pesan,
						'toko'				=> $get_toko,
						'toko_penjualan'	=> $get_toko_penjualan
				);
		echo json_encode($msg);
	}

	public function get_gudang_sku_by_periodik()
	{
		$start 					= substr($this->input->post('periodik'), 0, 10);
		$end 					= substr($this->input->post('periodik'), 13, 24);
		$get_gudang 			= $this->Keluar_model->get_gudang_sku($start, $end);
		$get_gudang_penjualan 	= $this->Keluar_model->get_gudang_sku_penjualan($start, $end);

		$pesan = "Berhasil disimpan!";	
		$msg = array(	'sukses'			=> $pesan,
						'gudang'			=> $get_gudang,
						'gudang_penjualan'	=> $get_gudang_penjualan
				);
		echo json_encode($msg);
	}

	// public function export_customer_insight($start, $end, $provinsi, $kabupaten, $belanja_min, $belanja_max, $qty_min, $qty_max)
	public function export_customer_insight()
	{
		$start = substr($this->input->get('periodik'), 0, 10);
		$end = substr($this->input->get('periodik'), 13, 24);
		$provinsi = $this->input->get('provinsi');
		$kabupaten = $this->input->get('kabupaten');
		$belanja_min = $this->input->get('belanja_min');
		$belanja_max = $this->input->get('belanja_max');
		$qty_min = $this->input->get('qty_min');
		$qty_max = $this->input->get('qty_max');
		$data['title']	= "Export Data Customer Insight Per Tanggal ".$start." - ".$end."_".date("H_i_s");
        $lists = $this->Keluar_model->get_datatable_customer_insight($start, $end, $provinsi, $kabupaten, $belanja_min, $belanja_max, $qty_min, $qty_max);
		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'nomor_pesanan');
		$sheet->setCellValue('B1', 'nama_penerima');
		$sheet->setCellValue('C1', 'hp_penerima');
		$sheet->setCellValue('D1', 'qty');
		$sheet->setCellValue('E1', 'frequency');
		$sheet->setCellValue('F1', 'total_belanja');

        // set Row
        $rowCount = 2;
        foreach ($lists as $list) {
        	// Nomor Pesanan
	        if (is_numeric($list->nomor_pesanan)) {
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
            $sheet->SetCellValue('B' . $rowCount, $list->nama_penerima);

	        // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('C' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('C' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('C' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('C' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('C' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('C' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('C' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('C'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('C'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }
			
			$sheet->SetCellValue('D' . $rowCount, $list->qty);
			$sheet->SetCellValue('E' . $rowCount, $list->jumlah_pesanan);
			$sheet->SetCellValue('F' . $rowCount, 'Rp. ' . number_format($list->total_harga_jual,0,",","."));

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

	public function dashboard_customer_insight() {
		$this->data['page_title'] = 'Dashboard Insight Customer';

		$start = (substr($this->input->get('periodik'), 0, 10));
		$end = (substr($this->input->get('periodik'), 13, 24));

		$this->data['jumlah_invoice'] = $this->input->get('periodik') ? $this->db->query("SELECT nomor_pesanan FROM penjualan WHERE date_format(tgl_penjualan, '%Y-%m-%d') >= ? AND date_format(tgl_penjualan, '%Y-%m-%d') <= ?", array($start, $end))->num_rows() : $this->db->query("SELECT * FROM penjualan WHERE date_format(tgl_penjualan, '%Y-%m') = ".date('Y-m'))->num_rows();
		$this->data['qty_harga'] = $this->input->get('periodik') ? $this->db->query("SELECT SUM(qty) AS qty, AVG(harga) AS total_harga FROM detail_penjualan INNER JOIN penjualan ON detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan WHERE date_format(tgl_penjualan, '%Y-%m-%d') >= ? AND date_format(tgl_penjualan, '%Y-%m-%d') <= ?", array($start, $end))->row() : $this->db->query("SELECT SUM(qty) AS qty, AVG(harga) AS total_harga FROM detail_penjualan INNER JOIN penjualan ON detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan WHERE date_format(tgl_penjualan, '%Y-%m') = ?", array(date('Y-m')))->row();
		$this->data['avg_order_number'] = $this->input->get('periodik') ? $this->db->query("SELECT AVG(qty) as avg_order_number FROM penjualan INNER JOIN detail_penjualan ON penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan WHERE date_format(tgl_penjualan, '%Y-%m-%d') >= ? AND date_format(tgl_penjualan, '%Y-%m-%d') <= ?", array($start, $end))->row() : $this->db->query("SELECT AVG(qty) as avg_order_number FROM penjualan INNER JOIN detail_penjualan ON penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan WHERE date_format(tgl_penjualan, '%Y-%m') = ?", array(date('Y-m')))->row();
		$this->data['jumlah_pembeli'] = $this->input->get('periodik') ? $this->db->query("SELECT DISTINCT hp_penerima FROM penjualan WHERE date_format(tgl_penjualan, '%Y-%m-%d') >= ? AND date_format(tgl_penjualan, '%Y-%m-%d') <= ?", array($start, $end))->num_rows() : $this->db->query("SELECT DISTINCT nama_penerima,hp_penerima FROM penjualan WHERE date_format(tgl_penjualan, '%Y-%m') = ?", array(date('Y-m')))->num_rows();
		$this->data['pembeli_repeat_order'] = $this->input->get('periodik') ? $this->db->query("SELECT nama_penerima,hp_penerima FROM penjualan WHERE date_format(tgl_penjualan, '%Y-%m-%d') >= ? AND date_format(tgl_penjualan, '%Y-%m-%d') <= ? GROUP BY hp_penerima HAVING COUNT(hp_penerima) > 1", array($start, $end))->num_rows() : $this->db->query("SELECT nama_penerima,hp_penerima FROM penjualan WHERE date_format(tgl_penjualan, '%Y-%m') = ? GROUP BY hp_penerima HAVING COUNT(hp_penerima) > 1", array(date('Y-m')))->num_rows();
		$this->data['pembeli_baru'] = $this->input->get('periodik') ? $this->db->query("SELECT COUNT(DISTINCT hp_penerima) as pembeli_baru FROM penjualan WHERE date_format(tgl_penjualan, '%Y-%m-%d') >= ? AND date_format(tgl_penjualan, '%Y-%m-%d') <= ? AND hp_penerima IN (SELECT hp_penerima FROM penjualan GROUP BY hp_penerima HAVING COUNT(*)=1)", array($start, $end))->row() : $this->db->query("SELECT COUNT(DISTINCT hp_penerima) as pembeli_baru FROM penjualan WHERE date_format(tgl_penjualan, '%Y-%m') = ? AND hp_penerima IN (SELECT hp_penerima FROM penjualan GROUP BY hp_penerima HAVING COUNT(*)=1)", array(date('Y-m')))->row();
		// die(print_r($this->data['pembeli_baru']));
		if($this->data['pembeli_repeat_order'] > 0 && $this->data['jumlah_pembeli'] > 0){
			$this->data['repeat_order'] = ($this->data['pembeli_repeat_order'] / $this->data['jumlah_pembeli']);
		} else {
			$this->data['repeat_order'] = 0;
		}

		// $this->data['repeat_order'] = @($this->data['pembeli_repeat_order'] / $this->data['jumlah_pembeli']) === false ? 0 : ($this->data['pembeli_repeat_order'] / $this->data['jumlah_pembeli']);

		$this->load->view('back/keluar/dashboard_insight', $this->data);
	}

}

/* End of file Keluar.php */
/* Location: ./application/controllers/admin/Keluar.php */