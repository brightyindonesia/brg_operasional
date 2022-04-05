<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Keluar extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->data['module'] = 'Penjualan Produk';

	    $this->load->model(array('Bahan_kemas_model', 'Vendor_model', 'Venmasaccess_model', 'Produk_model', 'Toko_model', 'Tokproaccess_model', 'Kurir_model', 'Keluar_model', 'Paket_model', 'Resi_model', 'Dashboard_model'));

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
		$this->data['page_title'] = 'Dashboard '.$this->data['module'];

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
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Keluar_model->get_dasbor_list($status, $kurir, $toko, $resi, $start, $end);
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
            $row['created'] = $data->created;
            $row['total_harga'] = $data->total_harga;
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
	// End Datatable Server Side

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

	    $this->load->view('back/keluar/penjualan_produk_list', $this->data);
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
	    $this->data['get_all_kabupaten']	= kabupaten_indeks($this->data['penjualan']->provinsi);

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

	    // if ($this->data['penjualan']->nomor_resi != '') {
		   //  $this->data['kurir'] = [
		   //  	'class'         => 'form-control select2bs4',
		   //    	'disabled' 	    => '',
		   //    	'style' 		=> 'width:100%'
		   //  ];

		   //  $this->data['id_kurir'] = [	
			  // 	'id' 			=> 'kurir', 
		   //      'type'          => 'hidden',
		   //    ];
	    // }else{
	    // 	$this->data['kurir'] = [
		   //  	'class'         => 'form-control select2bs4',
		   //  	'id'            => 'kurir',
		   //    	'required'      => '',
		   //    	'style' 		=> 'width:100%'
		   //  ];

		   //  $this->data['id_kurir'] = [	
		   //      'type'          => 'hidden',
		   //  ];
	    // }

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
        $total_harga 	= $total_jual + $total;
        $total_jual_2	= $total_jual + $ongkir;

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

        $cek_no_pesanan = $this->Keluar_model->get_by_id($no_pesanan);
        if (isset($cek_no_pesanan)) {
        	if ($cek_no_pesanan->id_status_transaksi != 3) {
        		if ($status == 3) {
					$tgl_diterima = $now;
					$fix_diterima = $diterima;
				}else{
					$tgl_diterima = '';
					$fix_diterima = '';
				}

				$data = array(	'id_users' 				=> $this->session->userdata('id_users'),
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
							'harga_jual' 			=> $total_jual,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $total_jual_2,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $total_jual_2 - $total - $total_hpp,
							'selisih_margin' 		=> $total_jual_2 - $total_harga,
							'jumlah_diterima' 		=> $fix_diterima,
							'tgl_diterima' 			=> $tgl_diterima
					);

	        	// echo print_r($data);
				$this->Keluar_model->update($no_pesanan, $data);

				write_log();
        	}else{
        		if ($status == 3) {
					$fix_diterima = $diterima;

					$data = array(	'id_users' 			=> $this->session->userdata('id_users'),
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
							'harga_jual' 			=> $total_jual,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $total_jual_2,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $total_jual_2 - $total - $total_hpp,
							'selisih_margin' 		=> $total_jual_2 - $total_harga,
							'jumlah_diterima' 		=> $fix_diterima
					);

		        	// echo print_r($data);
					$this->Keluar_model->update($no_pesanan, $data);

					write_log();
				}else{
					$tgl_diterima = '';
					$fix_diterima = '';

					$data = array(	'id_users' 				=> $this->session->userdata('id_users'),
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
							'harga_jual' 			=> $total_jual,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $total_jual_2,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $total_jual_2 - $total - $total_hpp,
							'selisih_margin' 		=> $total_jual_2 - $total_harga,
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
			$i=0;
			foreach ($cariDetail as $produk) {
				// START - Kurangin jumlah total jika ada didalam paket
	          	$cariPaket = $this->Produk_model->get_all_by_id($produk->id_produk);
	          	if (isset($cariPaket)) {
	          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          		if (count($produkPaket) > 1) {
	          			// echo "Isi 2";
	          			foreach ($produkPaket as $result) {
	          				$total = $result->qty_pakduk * $produk->qty;
		          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk + $total
							          	);
							$this->Produk_model->update($result->id_produk, $kurangStokPakduk);

							write_log();	
	          			}
	          		}else{
	          			$rowPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->row();
	          			$total = $rowPaket->qty_pakduk * $produk->qty;
	          			$kurangStokPakduk = array(	'qty_produk' 		=> $rowPaket->qty_produk + $total
						          	);
						$this->Produk_model->update($rowPaket->id_produk, $kurangStokPakduk);

						write_log();
	          		}
	          	}
	          	// END
				$nambahStok[$i] = array( 	'qty_produk' 		=> $produk->qty_produk + $produk->qty
									);
				$this->Produk_model->update($produk->id_produk, $nambahStok[$i]);

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
	          		if (count($produkPaket) > 1) {
	          			// echo "Isi 2";
	          			$resultPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
	          			foreach ($resultPaket as $result) {
	          				$total = $result->qty_pakduk * $decode_qty[$n];
		          			$kurangStokPakduk = array(	'qty_produk' 		=> $result->qty_produk - $total
							          	);
							$this->Produk_model->update($result->id_produk, $kurangStokPakduk);

							write_log();
	          			}
	          		}else{
	          			$rowPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->row();
	          			$total = $rowPaket->qty_pakduk * $decode_qty[$n];
	          			$kurangStokPakduk = array(	'qty_produk' 		=> $rowPaket->qty_produk - $total
						          	);
						$this->Produk_model->update($rowPaket->id_produk, $kurangStokPakduk);

						write_log();
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

	public function penjualan_produk()
	{
		is_create();    

		$this->data['action_impor']     = 'admin/keluar/proses_impor';
		$this->data['action_restore']  = 'admin/keluar/restore_db';
	    $this->data['page_title']					= 'Create & Import New '.$this->data['module'];
	    $this->data['get_all_toko'] 				= $this->Keluar_model->get_all_toko();
	    $this->data['get_all_kurir'] 				= $this->Keluar_model->get_all_kurir();
	    $this->data['get_all_status']				= $this->Keluar_model->get_all_status();
	    $this->data['get_all_provinsi'] 			= provinsi();

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

		if ($status_transaksi == 3) {
			$tgl_diterima = $now;
			$fix_diterima = $diterima;
		}else{
			$tgl_diterima = '';
			$fix_diterima = '';
		}

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
        $total_harga 	= $total_jual + $total;
        $total_jual_2	= $total_jual + $ongkir;

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
							'harga_jual' 			=> $total_jual,
							'total_hpp'				=> $total_hpp,
							'total_jual'			=> $total_jual_2,
							'total_harga' 			=> $total_harga,
							'margin' 				=> $total_jual_2 - $total - $total_hpp,
							'selisih_margin' 		=> $total_jual_2 - $total_harga,
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

	public function hapus($id = '')
	{
		is_delete();

		// $n = 0;
		$i = 0;
		$cekDetail = $this->Keluar_model->get_detail_by_id(base64_decode($id));
		foreach ($cekDetail as $detail) {
			$row_produk = $this->Produk_model->get_by_id($detail->id_produk);
	  		$tambahStok = array( 	'qty_produk' 		=> $row_produk->qty_produk + $detail->qty
							);

			$this->Produk_model->update($row_produk->id_produk, $tambahStok);	

			write_log();

			// $n++;
		}

		$cariDetail = $this->Keluar_model->get_all_detail_by_id(base64_decode($id));
		if(isset($cariDetail))
		{
		  foreach ($cariDetail as $produk) {
		  	// Hapus Data Resi sesuai Resi (FIX)
		  	$cekResi = $this->Resi_model->get_by_resi($produk->nomor_resi);
		  	if (isset($cekResi)) {
		  		$this->Resi_model->delete_by_resi($produk->nomor_resi);
				write_log();
		  	}
			
		  	// START - Kurangin jumlah total jika ada didalam paket
          	$cariPaket = $this->Produk_model->get_all_by_id($produk->id_produk);
          	if (isset($cariPaket)) {
          		$produkPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->result();
          		if (count($produkPaket) > 1) {
          			// echo "Isi 2";
          			foreach ($produkPaket as $result) {
          				$total = $result->qty_pakduk * $produk->qty;
	          			$kurangStokPakduk[$i] = array(	'qty_produk' 		=> $result->qty_produk + $total
						          	);
						$this->Produk_model->update($result->id_produk, $kurangStokPakduk[$i]);	

						write_log();
          			}
          		}else{
          			$rowPaket = $this->Paket_model->get_all_produk_by_paket_ops($cariPaket->id_paket)->row();
          			$total = $rowPaket->qty_pakduk * $produk->qty;
          			$kurangStokPakduk[$i] = array(	'qty_produk' 		=> $rowPaket->qty_produk + $total
					          	);
					$this->Produk_model->update($rowPaket->id_produk, $kurangStokPakduk[$i]);

					write_log();
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

	public function impor_penjualan()
	{
		is_create();

		$this->data['page_title'] = 'Import Data '.$this->data['module'];
	    $this->data['action']     = 'admin/keluar/proses_impor';

	    $this->load->view('back/keluar/impor_penjualan', $this->data);
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
							$ongkir    = $cells[10]->getValue();
							// $admin     = $cells[11]->getValue();
							$hpp 	   = $produk->hpp_produk;
							$harga 	   = $cells[13]->getValue();
							$qty 	   = $cells[12]->getValue();
							$cek_nomor = $this->Keluar_model->get_all_detail_by_id_row($row->getCellAtIndex(0));
							if (isset($cek_nomor)) {
								$harga_jual 		= $cek_nomor->harga_jual + $harga;
								$total_hpp			= $cek_nomor->total_hpp + ($hpp* $qty);
								$total_jual 		= $cek_nomor->total_jual + $harga_jual;
								$total_harga 		= $cek_nomor->total_harga + $harga_jual;
								$margin 			= $total_jual - $total_hpp;
								$selisih_margin 	= $total_jual - $total_harga;
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
																	'id_status_transaksi' 	=> 1,
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
																	'margin' 				=> $total_jual - $total - $hpp_jual,
																	'selisih_margin' 		=> $total_jual - $total_harga,
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
																	'id_status_transaksi' 	=> 1,
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
																	'margin' 				=> $total_jual - $total - $hpp_jual,
																	'selisih_margin' 		=> $total_jual - $total_harga,
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
													'tgl_resi' 		=> $row->getCellAtIndex(14),
													'created_resi'	=> $row->getCellAtIndex(1),  
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

	public function proses_impor_diterima()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc'.time();	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('impor_diterima')) {
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->setShouldFormatDates(true);
			$reader->open('uploads/'.$file['file_name']);

			$jumlah = 0;
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
								$ubahPenjualan = array(	'jumlah_diterima'		=> $jumlah,
														'id_status_transaksi' 	=> 3,
														'tgl_diterima' 			=> $date
								);

								$this->Keluar_model->update($row_detail->nomor_pesanan, $ubahPenjualan);
								write_log();
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
			$error = array('error' => $this->upload->display_errors());
			$this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
			redirect('admin/keluar/penjualan_produk');
			return $error;
		}
	}


	public function get_id_provinsi()
	{
		$provinsi = $this->input->post('provinsi');
		$select_box[] = "<option value=''>- Pilih Kabupaten -</option>";
		$kabupaten = json_decode(json_encode(kabupaten($provinsi)));
		if (count($kabupaten) > 0) {
			for ($i = 0; $i < count($kabupaten); $i++) {
				$select_box[] = '<option value="'.$kabupaten[$i].'">'.$kabupaten[$i].'</option>';
			}
			// header("Content-Type:application/json");
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

	public function export_keluar($kurir, $toko, $resi, $status, $periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Penjualan Per Tanggal ".$start." - ".$end."_".date("H_i_s");
		$data['penjualan'] = $this->Keluar_model->get_datatable_all($status, $kurir, $toko, $resi, $start, $end);

		$this->load->view('back/keluar/penjualan_export', $data);
	}
}

/* End of file Keluar.php */
/* Location: ./application/controllers/admin/Keluar.php */