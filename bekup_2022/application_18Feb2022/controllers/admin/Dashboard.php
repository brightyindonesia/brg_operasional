<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array('Dashboard_model', 'Produk_model', 'Bahan_kemas_model', 'Toko_model', 'Vendor_model', 'Keluar_model'));
		$this->load->helper('auth');

		is_login();

		$this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    	$this->data['skins_template']     			= $this->Template_model->skins();
	}

	// Table Data Count
	// Datatable Server Side
	// Penjualan
	function get_data_modal_data_penjualan()
    {
        $list = $this->Dashboard_model->get_datatables_count_penjualan();
        $dataJSON = array();
        foreach ($list as $data) {
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
            "recordsTotal" => $this->Dashboard_model->count_all_count_penjualan(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_count_penjualan(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // Resi
    function get_data_modal_data_resi()
    {
        $list = $this->Dashboard_model->get_datatables_count_resi();
        $dataJSON = array();
        foreach ($list as $data) {
			if ($data->status ==  0) {
				$status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-times' style='margin-right:5px;'></i>Belum diproses</a>";
			}elseif ($data->status ==  1) {
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
			}elseif ($data->status ==  2){
	            $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
	        }else{
	          	$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-minus-circle' style='margin-right:5px;'></i>Retur</a>";
	        }

			$get_penjualan = $this->Keluar_model->get_all_by_id($data->nomor_pesanan);
			$get_detail_penjualan = $this->Keluar_model->get_all_detail_by_id($data->nomor_pesanan);
			if ($get_penjualan->id_status_transaksi == 1) {
				$status_transaksi = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i>".$get_penjualan->nama_status_transaksi."</a>";
			}else if ($get_penjualan->id_status_transaksi == 2) {
				$status_transaksi = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-money' style='margin-right:5px;'></i>".$get_penjualan->nama_status_transaksi."</a>";
			}else if ($get_penjualan->id_status_transaksi == 3) {
			$status_transaksi = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>".$get_penjualan->nama_status_transaksi."</a>";
			}else if ($get_penjualan->id_status_transaksi == 4) {
				$status_transaksi = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-exchange' style='margin-right:5px;'></i>".$get_penjualan->nama_status_transaksi."</a>";
			}
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			        '<tr>'.
			            '<td width="15%">Tanggal Pesanan</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$get_penjualan->tgl_penjualan.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td width="15%">Update Resi </td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$data->tgl_resi.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>Toko</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$get_penjualan->nama_toko.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>Status Transaksi</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$status_transaksi.'</td>'.
			        '</tr>'.
			    '</table>'.
			    '<hr width="100%">'.
			    '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			        '<tr>'.
			            '<td width="20%">Nama Penerima</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$get_penjualan->nama_penerima.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>Nomor Handphone</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$get_penjualan->hp_penerima.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>Provinsi</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$get_penjualan->provinsi.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>Kota / Kabupaten</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$get_penjualan->kabupaten.'</td>'.
			        '</tr>'.
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

            $row = array();
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y H:i:s', strtotime($data->created_resi));
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['status'] = $status;
            $row['detail'] = $detail;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_count_resi(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_count_resi(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // Retur
	function get_data_modal_data_retur()
    {
    	$i = 1;
        $list = $this->Dashboard_model->get_datatables_count_retur();
        $dataJSON = array();
        foreach ($list as $data) {
   			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan).' Produk</span>';
			if ($data->status_retur == 0) {
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i> Sedang diproses</a>";
			}else if($data->status_retur == 1) {
				$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i> Sudah diproses</a>";
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

            $row = array();
            if ($data->status_follow_up == 1){
				$row['status_fu'] = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i> Sudah follow up</a>";
			}else{
				$row['status_fu'] = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-times' style='margin-right:5px;'></i> Belum follow up</a>";
			}
            $row['no'] = $i;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_retur));
            $row['nomor_retur'] = $data->nomor_retur;
            $row['nomor_pesanan'] = $produk;
            $row['nama_toko'] = $data->nama_toko;
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['total_harga'] = $data->total_harga;
            $row['keterangan'] = $data->keterangan_retur;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['status'] = $status;
            $row['detail'] = $detail;
            $row['created'] = $data->created;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_count_retur(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_count_retur(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // Repeat
	function get_data_modal_data_repeat()
    {
        $list = $this->Dashboard_model->get_datatables_count_repeat();
        $dataJSON = array();
        foreach ($list as $data) {
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
            "recordsTotal" => $this->Dashboard_model->count_all_count_repeat(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_count_repeat(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function index()
	{
		$this->data['page_title'] = 'Dashboard';

		$this->data['get_total_menu']     		= $this->Menu_model->total_rows();
		$this->data['get_total_submenu']     	= $this->Submenu_model->total_rows();				
		$this->data['get_total_user']     		= $this->Auth_model->total_rows();
		$this->data['get_total_usertype']     	= $this->Usertype_model->total_rows();
		$this->data['get_total_produk']     	= $this->Produk_model->total_rows();
		$this->data['get_total_bahan']     		= $this->Bahan_kemas_model->total_rows();
		$this->data['get_total_toko']     		= $this->Toko_model->total_rows();
		$this->data['get_total_vendor']     	= $this->Vendor_model->total_rows();

		$this->load->view('back/dashboard/body', $this->data);
	}

	// Non Retur

	public function export_penjualan()
	{	
		$status  = $this->uri->segment(4); 
		$tanggal = $this->uri->segment(5);
		$periodik = $this->uri->segment(6);

		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		if ($tanggal == 'impor') {
			$data['title']	= "Export Data Penjualan Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
		}else{
			$data['title']	= "Export Data Penjualan Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
		}
		
		$data['penjualan']	= $this->Dashboard_model->get_penjualan($status, $tanggal,$start, $end);

		// echo print_r($repeat);
		$this->load->view('back/dashboard/penjualan_export', $data);

		// $pesan = "Data Repeat Order berhasil diexport!";	
  //   	$msg = array(	'sukses'	=> $pesan
  //   			);
  //   	echo json_encode($msg);
	}

	public function export_resi()
	{	
		$status  = $this->uri->segment(4); 
		$tanggal = $this->uri->segment(5);
		$periodik = $this->uri->segment(6);

		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		if ($tanggal == 'impor') {
			$data['title']	= "Export Data Resi Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
		}else{
			$data['title']	= "Export Data Resi Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
		}
		
		$data['penjualan']	= $this->Dashboard_model->get_resi($status, $tanggal,$start, $end);

		// echo print_r($repeat);
		$this->load->view('back/dashboard/resi_export', $data);

		// $pesan = "Data Repeat Order berhasil diexport!";	
  //   	$msg = array(	'sukses'	=> $pesan
  //   			);
  //   	echo json_encode($msg);
	}

	public function export_retur()
	{	
		$status  = $this->uri->segment(4); 
		$tanggal = $this->uri->segment(5);
		$follup = $this->uri->segment(6);
		$periodik = $this->uri->segment(7);

		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		if ($tanggal == 'impor') {
			$data['title']	= "Export Data Retur Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
		}else{
			$data['title']	= "Export Data Retur Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
		}
		
		$data['penjualan']	= $this->Dashboard_model->get_retur($status, $tanggal, $follup,$start, $end);

		// echo print_r($repeat);
		$this->load->view('back/dashboard/retur_export', $data);

		// $pesan = "Data Repeat Order berhasil diexport!";	
  //   	$msg = array(	'sukses'	=> $pesan
  //   			);
  //   	echo json_encode($msg);
	}

	public function export_repeat_modal_penjualan()
	{	
		$hp  = $this->uri->segment(4); 
		$first = $this->uri->segment(5);
		$last = $this->uri->segment(6);
		$trigger = $this->uri->segment(7);

		if ($trigger == 'impor') {
			$data['title']	= "Export Data Penjualan Repeat Order Per Tanggal Impor ".$first." - ".$last."_".date("H_i_s");	
		}else{
			$data['title']	= "Export Data Penjualan Repeat Order Per Tanggal Penjualan ".$first." - ".$last."_".date("H_i_s");
		}
		
		$data['penjualan']	= $this->Dashboard_model->get_repeat($hp, $first, $last, $trigger);

		// echo print_r($repeat);
		$this->load->view('back/dashboard/penjualan_export', $data);

		// $pesan = "Data Repeat Order berhasil diexport!";	
  //   	$msg = array(	'sukses'	=> $pesan
  //   			);
  //   	echo json_encode($msg);
	}

	public function export_repeat($periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Repeat Order Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");
		$data['repeat']	= $this->Dashboard_model->get_customer_repeat($start, $end);

		// echo print_r($repeat);
		$this->load->view('back/dashboard/repeat_export', $data);

		// $pesan = "Data Repeat Order berhasil diexport!";	
  //   	$msg = array(	'sukses'	=> $pesan
  //   			);
  //   	echo json_encode($msg);
	}

	public function export_repeat_penjualan($periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Repeat Order Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
		$data['repeat']	= $this->Dashboard_model->get_customer_repeat_penjualan($start, $end);

		// echo print_r($repeat);
		$this->load->view('back/dashboard/repeat_export', $data);

		// $pesan = "Data Repeat Order berhasil diexport!";	
  //   	$msg = array(	'sukses'	=> $pesan
  //   			);
  //   	echo json_encode($msg);
	}

	// DASBOR COUNT IMPOR
	function dasbor_list_count_penjualan(){
		$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_penjualan($start, $end);
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

    function dasbor_list_count_resi(){
    	$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_resi($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
        					'belum'		=> $data->belum,
			        		'sedang'	=> $data->sedang,
			        		'sudah'		=> $data->sudah,
			        		'retur'		=> $data->retur,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_retur(){
    	$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_retur($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'			=> $data->total,
        					'diproses'		=> $data->diproses,
			        		'sudah'			=> $data->sudah,
			        		'belum_fu'		=> $data->belum_fu,
			        		'sudah_fu'		=> $data->sudah_fu,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    // DASBOR COUNT REAL
	function dasbor_list_count_penjualan_real(){
		$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_penjualan_real($start, $end);
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

    function dasbor_list_count_resi_real(){
    	$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_resi_real($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
        					'belum'		=> $data->belum,
			        		'sedang'	=> $data->sedang,
			        		'sudah'		=> $data->sudah,
			        		'retur'		=> $data->retur,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_retur_real(){
    	$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_retur_real($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'			=> $data->total,
        					'diproses'		=> $data->diproses,
			        		'sudah'			=> $data->sudah,
			        		'belum_fu'		=> $data->belum_fu,
			        		'sudah_fu'		=> $data->sudah_fu,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

	public function ajax_dasbor_prosku()
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

	public function ajax_dasbor_prosku_penjualan()
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

	public function ajax_dasbor_jenis_toko()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_jenis_toko = $this->Dashboard_model->get_jenis_toko($start, $end);
		$jenis_data 	= array();
		$toko_data 		= array();
		$i = 0;
		foreach ($get_jenis_toko as $val_jenis) {
			$int_jumlah_jenis = intval($val_jenis->jumlah_jenis);
			
			$jenis_data[] = array( 'name' 		=> $val_jenis->nama_jenis_toko,
								  'y' 	 		=> $int_jumlah_jenis,
								  'drilldown'   => $val_jenis->nama_jenis_toko,
			);

			$toko_data[$i] = array( 'name' 		=> $val_jenis->nama_jenis_toko,
								   	  'id'	 	 	=> $val_jenis->nama_jenis_toko,
			);

			$get_toko = $this->Dashboard_model->get_toko_by_jenis($start, $end, $val_jenis->jenis_toko_id);
			foreach ($get_toko as $val_tok) {
				$int_jumlah_tok = intval($val_tok->jumlah_toko);

				$toko_data[$i]['data'][] = array( 0	=> $val_tok->nama_toko,
									 		      1	=> $int_jumlah_tok
				);
			}

			$i++;
		}

		$result = array(	'jenis'		=> $jenis_data,
							'toko'		=> $toko_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_dasbor_jenis_toko_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_jenis_toko = $this->Dashboard_model->get_jenis_toko_penjualan($start, $end);
		$jenis_data 	= array();
		$toko_data 		= array();
		$i = 0;
		foreach ($get_jenis_toko as $val_jenis) {
			$int_jumlah_jenis = intval($val_jenis->jumlah_jenis);
			
			$jenis_data[] = array( 'name' 		=> $val_jenis->nama_jenis_toko,
								  'y' 	 		=> $int_jumlah_jenis,
								  'drilldown'   => $val_jenis->nama_jenis_toko,
			);

			$toko_data[$i] = array( 'name' 		=> $val_jenis->nama_jenis_toko,
								   	  'id'	 	 	=> $val_jenis->nama_jenis_toko,
			);

			$get_toko = $this->Dashboard_model->get_toko_by_jenis_penjualan($start, $end, $val_jenis->jenis_toko_id);
			foreach ($get_toko as $val_tok) {
				$int_jumlah_tok = intval($val_tok->jumlah_toko);

				$toko_data[$i]['data'][] = array( 0	=> $val_tok->nama_toko,
									 		      1	=> $int_jumlah_tok
				);
			}

			$i++;
		}

		$result = array(	'jenis'		=> $jenis_data,
							'toko'		=> $toko_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}
	
	public function ajax_dasbor_total()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 23);
		// Hitung jarak dan membandingkan dihari sebelumnya
		$jarak		= abs(strtotime($end) - strtotime($start));
		if (round($jarak / (60 * 60 * 24)) == 0) {
			$fix_jarak = round($jarak / (60 * 60 * 24)) + 1;
		}else{
			$fix_jarak = round($jarak / (60 * 60 * 24)) + 1;
		}
		$start_past = date('Y-m-d', strtotime("$start -$fix_jarak days"));
		$end_past	= date('Y-m-d', strtotime("$end  -$fix_jarak days"));
		// End hitung jarak dan membandingkan dihari sebelumnya

		// Get Income
		$get_income	= $this->Dashboard_model->get_pendapat_dasbor($start, $end);
		$get_income_past = $this->Dashboard_model->get_pendapat_dasbor($start_past, $end_past);

		// Get Pending Payment
		$get_pending = $this->Dashboard_model->get_pending_payment($start, $end);
		$get_pending_past = $this->Dashboard_model->get_pending_payment($start_past, $end_past);

		// Get Pesanan
		$get_pesan = $this->Dashboard_model->get_total_pesanan_by_periodik($start, $end);
		$get_pesan_past = $this->Dashboard_model->get_total_pesanan_by_periodik($start_past, $end_past);

		// Mencari nilai MAX dari 2 variabel
		$max_diterima = max(array($get_income->diterima, $get_income_past->diterima));
		$max_pending  = max(array($get_pending->total_pending, $get_pending_past->total_pending));
		$max_income   = max(array($get_income->fix, $get_income_past->fix));
		$max_laba     = max(array($get_income->total, $get_income_past->total));
		$max_ongkir   = max(array($get_income->tot_ongkir, $get_income_past->tot_ongkir));
		$max_pesan	  = max(array($get_pesan->jumlah_tanggal, $get_pesan_past->jumlah_tanggal));

		if ($max_diterima == NULL && $max_income == NULL && $max_laba == NULL && $max_ongkir == NULL && $max_pesan == 0 && $max_pending == NULL) {
			$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PESANAN</span>';

			$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL DITERIMA</span>';

            $html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';

            $html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL REVENUE</span>';
            
            $html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
               	 			 
            $html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL ONGKIR</span>';   	 			 
		}else{
			// Mencari total persen dari range angka
			// PESANAN
			if ($max_pesan == 0) {
				$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL PESANAN</span>';
			}else{
				$persen_pesan	= (($get_pesan->jumlah_tanggal - $get_pesan_past->jumlah_tanggal) / $max_pesan) * 100;
				$sisa_pesanan	= $get_pesan->jumlah_tanggal - $get_pesan_past->jumlah_tanggal;

				if ($persen_pesan == 0 && $sisa_pesanan == 0) {
					$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.$get_pesan->jumlah_tanggal.'</h5>'.
		               	 			 '<span class="description-text">TOTAL PESANAN</span>';
				}else if ($persen_pesan < 0 && $sisa_pesanan < 0) {
					$html_pesan = '<span class="description-percentage text-red"> -'.($sisa_pesanan * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_pesan * -1).' %)</span>'.
									 '<h5 class="description-header">'.($get_pesan->jumlah_tanggal).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PESANAN</span>';
				}else if ($persen_pesan > 0 && $sisa_pesanan > 0) {
					$html_pesan = '<span class="description-percentage text-green">+'.$sisa_pesanan.' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_pesan).'%)</span>'.
									 '<h5 class="description-header">'.$get_pesan->jumlah_tanggal.'</h5>'.
		               	 			 '<span class="description-text">TOTAL PESANAN</span>';
				}
			}

			// DITERIMA
			if ($max_diterima == 0) {
				$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL DITERIMA</span>';
			}else{
				$persen_diterima	= (($get_income->diterima - $get_income_past->diterima) / $max_diterima) * 100;
				$sisa_diterima		= $get_income->diterima - $get_income_past->diterima;

				if ($persen_diterima == 0 && $sisa_diterima == 0) {
					$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_income->diterima).'</h5>'.
		               	 			 '<span class="description-text">TOTAL DITERIMA</span>';
				}else if ($persen_diterima < 0 && $sisa_diterima < 0) {
					$html_diterima = '<span class="description-percentage text-red"> -'.rupiah($sisa_diterima * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_diterima * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->diterima).'</h5>'.
		               	 			 '<span class="description-text">TOTAL DITERIMA</span>';
				}else if ($persen_diterima > 0 && $sisa_diterima > 0) {
					$html_diterima = '<span class="description-percentage text-green">+'.rupiah($sisa_diterima).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_diterima).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->diterima).'</h5>'.
		               	 			 '<span class="description-text">TOTAL DITERIMA</span>';
				}
			}

			// PENDING PAYMENT
			if ($max_pending == 0) {
				$html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
			}else{
				$persen_pending	= (($get_pending->total_pending - $get_pending_past->total_pending) / $max_pending) * 100;
				$sisa_pending	= $get_pending->total_pending - $get_pending_past->total_pending;

				if ($persen_pending == 0 && $sisa_pending == 0) {
					$html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}else if ($persen_pending < 0 && $sisa_pending < 0) {
					$html_pending = '<span class="description-percentage text-green"> -'.rupiah($sisa_pending * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_pending * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}else if ($persen_pending > 0 && $sisa_pending > 0) {
					$html_pending = '<span class="description-percentage text-red">+'.rupiah($sisa_pending).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_pending).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}
			}

			// INCOME
			if ($max_income == 0) {
				$html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL GROSS REVENUE</span>';
			}else{
				$persen_income		= (($get_income->fix - $get_income_past->fix) / $max_income) * 100;
				$sisa_income		= $get_income->fix - $get_income_past->fix;

				if ($persen_income == 0 && $sisa_income == 0) {
					$html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}else if ($persen_income < 0 && $sisa_income < 0) {
					$html_income = '<span class="description-percentage text-red"> -'.rupiah($sisa_income * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_income * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}else if ($persen_income > 0 && $sisa_income > 0) {
					$html_income = '<span class="description-percentage text-green"> +'.rupiah($sisa_income).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_income).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}
			}

			// LABA
			if ($max_laba == 0) {
				$html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL REVENUE</span>';
			}else{
				$persen_laba	= (($get_income->total - $get_income_past->total) / $max_laba) * 100;
				$sisa_laba		= $get_income->total - $get_income_past->total;

				if ($persen_laba == 0 && $sisa_laba == 0) {
					$html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}else if ($persen_laba < 0 && $sisa_laba < 0) {
					$html_laba = '<span class="description-percentage text-red"> -'.rupiah($sisa_laba * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_laba * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}else if ($persen_laba > 0 && $sisa_laba > 0) {
					$html_laba = '<span class="description-percentage text-green"> +'.rupiah($sisa_laba).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_laba).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}
			}
			
			// ONGKIR
			if ($max_ongkir == 0) {
				$html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL ONGKIR</span>';   
			}else{
				$persen_ongkir	= (($get_income->tot_ongkir - $get_income_past->tot_ongkir) / $max_ongkir) * 100;
				$sisa_ongkir	= $get_income->tot_ongkir - $get_income_past->tot_ongkir;

				if ($persen_ongkir == 0 && $sisa_ongkir == 0) {
					$html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}else if ($persen_ongkir < 0 && $sisa_ongkir < 0) {
					$html_ongkir = '<span class="description-percentage text-green"> -'.rupiah($sisa_ongkir * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_ongkir * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}else if ($persen_ongkir > 0 && $sisa_ongkir > 0) {
					$html_ongkir = '<span class="description-percentage text-red"> +'.rupiah($sisa_ongkir).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_ongkir).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}
			}
			
			// // End Mencari total persen dari range angka
		}

		$result = array( 'income'  => $html_income,
						 'diterima'=> $html_diterima,
						 'pending'=> $html_pending,
						 // 'judul'   => 'Statistik Data Keuangan Tanggal: '.$start.' - '.$end.' dengan Tanggal: '.$start_past.' - '.$end_past.' ('.$fix_jarak.' Hari)', 
						 // 'hpp' 	   => $hpp,
						 'laba'    => $html_laba,
						 'ongkir'  => $html_ongkir,
						 'pesan'  => $html_pesan
		);

		echo json_encode($result);
	}

	public function ajax_dasbor_total_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		// Hitung jarak dan membandingkan dihari sebelumnya
		$jarak		= abs(strtotime($end) - strtotime($start));
		if (round($jarak / (60 * 60 * 24)) == 0) {
			$fix_jarak = round($jarak / (60 * 60 * 24)) + 1;
		}else{
			$fix_jarak = round($jarak / (60 * 60 * 24)) + 1;
		}
		$start_past = date('Y-m-d', strtotime("$start -$fix_jarak days"));
		$end_past	= date('Y-m-d', strtotime("$end  -$fix_jarak days"));
		// End hitung jarak dan membandingkan dihari sebelumnya

		// Get Income
		$get_income	= $this->Dashboard_model->get_pendapat_dasbor_penjualan($start, $end);
		$get_income_past = $this->Dashboard_model->get_pendapat_dasbor_penjualan($start_past, $end_past);

		// Get Pending Payment
		$get_pending = $this->Dashboard_model->get_pending_payment_penjualan($start, $end);
		$get_pending_past = $this->Dashboard_model->get_pending_payment_penjualan($start_past, $end_past);

		// Get Pesanan
		$get_pesan = $this->Dashboard_model->get_total_pesanan_by_periodik_penjualan($start, $end);
		$get_pesan_past = $this->Dashboard_model->get_total_pesanan_by_periodik_penjualan($start_past, $end_past);

		// Mencari nilai MAX dari 2 variabel
		$max_diterima = max(array($get_income->diterima, $get_income_past->diterima));
		$max_pending  = max(array($get_pending->total_pending, $get_pending_past->total_pending));
		$max_income   = max(array($get_income->fix, $get_income_past->fix));
		$max_laba     = max(array($get_income->total, $get_income_past->total));
		$max_ongkir   = max(array($get_income->tot_ongkir, $get_income_past->tot_ongkir));
		$max_pesan	  = max(array($get_pesan->jumlah_tanggal, $get_pesan_past->jumlah_tanggal));

		if ($max_diterima == NULL && $max_income == NULL && $max_laba == NULL && $max_ongkir == NULL && $max_pesan == 0 && $max_pending == NULL) {
			$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PESANAN</span>';

			$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL DITERIMA</span>';

            $html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';

            $html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
            
            $html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL REVENUE</span>';
               	 			 
            $html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL ONGKIR</span>';   	 			 
		}else{
			// Mencari total persen dari range angka
			// PESANAN
			if ($max_pesan == 0) {
				$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL PESANAN</span>';
			}else{
				$persen_pesan	= (($get_pesan->jumlah_tanggal - $get_pesan_past->jumlah_tanggal) / $max_pesan) * 100;
				$sisa_pesanan	= $get_pesan->jumlah_tanggal - $get_pesan_past->jumlah_tanggal;

				if ($persen_pesan == 0 && $sisa_pesanan == 0) {
					$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.$get_pesan->jumlah_tanggal.'</h5>'.
		               	 			 '<span class="description-text">TOTAL PESANAN</span>';
				}else if ($persen_pesan < 0 && $sisa_pesanan < 0) {
					$html_pesan = '<span class="description-percentage text-red"> -'.($sisa_pesanan * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_pesan * -1).' %)</span>'.
									 '<h5 class="description-header">'.($get_pesan->jumlah_tanggal).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PESANAN</span>';
				}else if ($persen_pesan > 0 && $sisa_pesanan > 0) {
					$html_pesan = '<span class="description-percentage text-green">+'.$sisa_pesanan.' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_pesan).'%)</span>'.
									 '<h5 class="description-header">'.$get_pesan->jumlah_tanggal.'</h5>'.
		               	 			 '<span class="description-text">TOTAL PESANAN</span>';
				}
			}

			// DITERIMA
			if ($max_diterima == 0) {
				$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL DITERIMA</span>';
			}else{
				$persen_diterima	= (($get_income->diterima - $get_income_past->diterima) / $max_diterima) * 100;
				$sisa_diterima		= $get_income->diterima - $get_income_past->diterima;

				if ($persen_diterima == 0 && $sisa_diterima == 0) {
					$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_income->diterima).'</h5>'.
		               	 			 '<span class="description-text">TOTAL DITERIMA</span>';
				}else if ($persen_diterima < 0 && $sisa_diterima < 0) {
					$html_diterima = '<span class="description-percentage text-red"> -'.rupiah($sisa_diterima * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_diterima * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->diterima).'</h5>'.
		               	 			 '<span class="description-text">TOTAL DITERIMA</span>';
				}else if ($persen_diterima > 0 && $sisa_diterima > 0) {
					$html_diterima = '<span class="description-percentage text-green">+'.rupiah($sisa_diterima).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_diterima).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->diterima).'</h5>'.
		               	 			 '<span class="description-text">TOTAL DITERIMA</span>';
				}
			}

			// PENDING PAYMENT
			if ($max_pending == 0) {
				$html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
			}else{
				$persen_pending	= (($get_pending->total_pending - $get_pending_past->total_pending) / $max_pending) * 100;
				$sisa_pending	= $get_pending->total_pending - $get_pending_past->total_pending;

				if ($persen_pending == 0 && $sisa_pending == 0) {
					$html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}else if ($persen_pending < 0 && $sisa_pending < 0) {
					$html_pending = '<span class="description-percentage text-green"> -'.rupiah($sisa_pending * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_pending * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}else if ($persen_pending > 0 && $sisa_pending > 0) {
					$html_pending = '<span class="description-percentage text-red">+'.rupiah($sisa_pending).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_pending).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}
			}

			// INCOME
			if ($max_income == 0) {
				$html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL MARGIN</span>';
			}else{
				$persen_income		= (($get_income->fix - $get_income_past->fix) / $max_income) * 100;
				$sisa_income		= $get_income->fix - $get_income_past->fix;

				if ($persen_income == 0 && $sisa_income == 0) {
					$html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}else if ($persen_income < 0 && $sisa_income < 0) {
					$html_income = '<span class="description-percentage text-red"> -'.rupiah($sisa_income * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_income * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}else if ($persen_income > 0 && $sisa_income > 0) {
					$html_income = '<span class="description-percentage text-green"> +'.rupiah($sisa_income).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_income).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}
			}

			// LABA
			if ($max_laba == 0) {
				$html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL REVENUE</span>';
			}else{
				$persen_laba	= (($get_income->total - $get_income_past->total) / $max_laba) * 100;
				$sisa_laba		= $get_income->total - $get_income_past->total;

				if ($persen_laba == 0 && $sisa_laba == 0) {
					$html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}else if ($persen_laba < 0 && $sisa_laba < 0) {
					$html_laba = '<span class="description-percentage text-red"> -'.rupiah($sisa_laba * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_laba * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}else if ($persen_laba > 0 && $sisa_laba > 0) {
					$html_laba = '<span class="description-percentage text-green"> +'.rupiah($sisa_laba).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_laba).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}
			}
			
			// ONGKIR
			if ($max_ongkir == 0) {
				$html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL ONGKIR</span>';   
			}else{
				$persen_ongkir	= (($get_income->tot_ongkir - $get_income_past->tot_ongkir) / $max_ongkir) * 100;
				$sisa_ongkir	= $get_income->tot_ongkir - $get_income_past->tot_ongkir;

				if ($persen_ongkir == 0 && $sisa_ongkir == 0) {
					$html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}else if ($persen_ongkir < 0 && $sisa_ongkir < 0) {
					$html_ongkir = '<span class="description-percentage text-green"> -'.rupiah($sisa_ongkir * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_ongkir * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}else if ($persen_ongkir > 0 && $sisa_ongkir > 0) {
					$html_ongkir = '<span class="description-percentage text-red"> +'.rupiah($sisa_ongkir).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_ongkir).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}
			}
			
			// // End Mencari total persen dari range angka
		}

		$result = array( 'income'  => $html_income,
						 'diterima'=> $html_diterima,
						 'pending'=> $html_pending,
						 // 'judul'   => 'Statistik Data Keuangan Tanggal: '.$start.' - '.$end.' dengan Tanggal: '.$start_past.' - '.$end_past.' ('.$fix_jarak.' Hari)', 
						 // 'hpp' 	   => $hpp,
						 'laba'    => $html_laba,
						 'ongkir'  => $html_ongkir,
						 'pesan'  => $html_pesan
		);

		echo json_encode($result);
	}

	public function ajax_line_income()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_income	= $this->Dashboard_model->get_pendapat_periodik($start, $end);
		$income 	= array();
		// $hpp 		= array();
		$diterima	= array();
		$laba 		= array();
		$ongkir 	= array();
		foreach ($get_income as $val_come) {
			$int_jumlah_income = intval($val_come->fix);
			// $int_jumlah_hpp	   = intval($val_come->tot_hpp);
			$int_jumlah_diterima   = intval($val_come->diterima);
			$int_jumlah_laba   = intval($val_come->total);
			$int_jumlah_ongkir = intval($val_come->tot_ongkir);
			
			$income[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_income		
			);

			// $hpp[] = array( 0 => $val_come->tanggal,
			// 				   1 => $int_jumlah_hpp		
			// );

			$diterima[] = array( 0 => $val_come->tanggal,
							   	 1 => $int_jumlah_diterima		
			);

			$laba[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_laba		
			);

			$ongkir[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_ongkir		
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'income'  => $income,
						 'diterima'  => $diterima,
						 // 'hpp' 	   => $hpp,
						 'laba'    => $laba,
						 'ongkir'  => $ongkir
		);

		echo json_encode($result);
	}

	public function ajax_line_income_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_income	= $this->Dashboard_model->get_pendapat_periodik_penjualan($start, $end);
		$income 	= array();
		// $hpp 		= array();
		$diterima	= array();
		$laba 		= array();
		$ongkir 	= array();
		foreach ($get_income as $val_come) {
			$int_jumlah_income = intval($val_come->fix);
			// $int_jumlah_hpp	   = intval($val_come->tot_hpp);
			$int_jumlah_diterima   = intval($val_come->diterima);
			$int_jumlah_laba   = intval($val_come->total);
			$int_jumlah_ongkir = intval($val_come->tot_ongkir);
			
			$income[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_income		
			);

			// $hpp[] = array( 0 => $val_come->tanggal,
			// 				   1 => $int_jumlah_hpp		
			// );

			$diterima[] = array( 0 => $val_come->tanggal,
							   	 1 => $int_jumlah_diterima		
			);

			$laba[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_laba		
			);

			$ongkir[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_ongkir		
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'income'  => $income,
						 'diterima'  => $diterima,
						 // 'hpp' 	   => $hpp,
						 'laba'    => $laba,
						 'ongkir'  => $ongkir
		);

		echo json_encode($result);
	}

	public function get_data_produk_status()
	{
		$list = $this->Dashboard_model->get_datatables_produk_status();
        $dataJSON = array();
        foreach ($list as $data) {
        	if ($data->qty_produk <= 0) {
        		$status = '<span class="label label-danger">Habis</span>';
        	}else if($data->qty_produk <= 10){
        		$status = '<span class="label label-warning">Sedikit</span>';
        	}else if($data->qty_produk > 10){
        		$status = '<span class="label label-success">Ada</span>';
        	}

			$rows[] = array( 'sku' 			=> $data->nama_sku, 
							 'sub_sku' 		=> $data->sub_sku,
							 'nama_produk'	=> $data->nama_produk,
							 'status' 		=> $status,
							 'stok' 		=> number_format($data->qty_produk,null,",",".")
			);

			$dataJSON = $rows;
		}

		$output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_produk_status(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_produk_status(),
            "data" => $dataJSON,
        );

		echo json_encode($output);
	}

	public function get_data_bahan_status()
	{
		$list = $this->Dashboard_model->get_datatables_bahan_status();
        $dataJSON = array();
        foreach ($list as $data) {
        	if ($data->qty_bahan_kemas <= 0) {
        		$status = '<span class="label label-danger">Habis</span>';
        	}else if($data->qty_bahan_kemas <= 10){
        		$status = '<span class="label label-warning">Terbatas</span>';
        	}else if($data->qty_bahan_kemas > 10){
        		$status = '<span class="label label-success">Ada</span>';
        	}

			$rows[] = array( 'sku' 				=> $data->kode_sku_bahan_kemas, 
							 'nama_bahan'		=> $data->nama_bahan_kemas,
							 'status' 			=> $status,
							 'stok' 			=> number_format($data->qty_bahan_kemas,null,",",".")
			);

			$dataJSON = $rows;
		}

		$output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_bahan_status(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_bahan_status(),
            "data" => $dataJSON,
        );

		echo json_encode($output);
	}
	
	public function get_data_repeat()
	{
		$i = 1;
		$list = $this->Dashboard_model->get_datatables();
		$first = substr($_GET['periodik'], 0, 10);
	    $last = substr($_GET['periodik'], 13, 24);
        $dataJSON = array();
        foreach ($list as $data) {

			$jumlah = '<a href="javascript:void(0)" onclick="tabelDataRepeat(\''.$data->hp_penerima.'\',\''.$first.'\',\''.$last.'\', \'impor\')" class="btn btn-success btn-sm"><i class="fa fa-shopping-cart" style="margin-right:5px;"></i>'.$data->jumlah_penerima.' Pesanan</a>';
			$rows[] = array( 'no'				=> $i,
							 'nama_penerima' 	=> $data->nama_penerima, 
							 'provinsi' 		=> $data->provinsi,
							 'kabupaten'	    => $data->kabupaten,
							 'hp_penerima' 		=> $data->hp_penerima,
							 'alamat' 			=> $data->alamat_penerima,
							 'repeat' 			=> $jumlah
			);

			$i++;

			$dataJSON = $rows;
		}

		$output = array(
            "recordsTotal" => $this->Dashboard_model->count_all(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered(),
            "data" => $dataJSON,
        );

		echo json_encode($output);
	}

	public function get_data_repeat_penjualan()
	{
		$i = 1;
		$list = $this->Dashboard_model->get_datatables_penjualan();
		$first = substr($_GET['periodik'], 0, 10);
	    $last = substr($_GET['periodik'], 13, 24);
        $dataJSON = array();
        foreach ($list as $data) {
			$jumlah = '<a href="javascript:void(0)" onclick="tabelDataRepeat(\''.$data->hp_penerima.'\',\''.$first.'\',\''.$last.'\', \'penjualan\')" class="btn btn-success btn-sm"><i class="fa fa-shopping-cart" style="margin-right:5px;"></i>'.$data->jumlah_penerima.' Pesanan</a>';
			$rows[] = array( 'no'				=> $i,
							 'nama_penerima' 	=> $data->nama_penerima, 
							 'provinsi' 		=> $data->provinsi,
							 'kabupaten'	    => $data->kabupaten,
							 'hp_penerima' 		=> $data->hp_penerima,
							 'alamat' 			=> $data->alamat_penerima,
							 'repeat' 			=> $jumlah
			);

			$i++;

			$dataJSON = $rows;
		}

		$output = array(
            "recordsTotal" => $this->Dashboard_model->count_all(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_penjualan(),
            "data" => $dataJSON,
        );

		echo json_encode($output);
	}

	public function ajax_pie_provkab()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_prov 	= $this->Dashboard_model->get_customer_provinsi($start, $end);
		$prov_data 	= array();
		$kab_data 	= array();
		$i = 0;
		foreach ($get_prov as $val_prov) {
			$int_jumlah_prov = intval($val_prov->jumlah_provinsi);
			
			$prov_data[] = array( 'name' 		=> $val_prov->provinsi,
								  'y' 	 		=> $int_jumlah_prov,
								  'drilldown'   => $val_prov->provinsi,
			);

			$kab_data[$i] = array( 'name' 		=> $val_prov->provinsi,
								   'id' 	 	=> $val_prov->provinsi,
			);

			$get_kab 	= $this->Dashboard_model->get_customer_kabupaten($start, $end, $val_prov->provinsi);
			foreach ($get_kab as $val_kab) {
				$int_jumlah_kab = intval($val_kab->jumlah_kabupaten);

				$kab_data[$i]['data'][] = array( 0	=> $val_kab->kabupaten,
									 		     1	=> $int_jumlah_kab
				);
			}

			$i++;
		}

		$result = array(	'provinsi'	=> $prov_data,
							'kabupaten' => $kab_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_pie_provkab_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_prov 	= $this->Dashboard_model->get_customer_provinsi_penjualan($start, $end);
		$prov_data 	= array();
		$kab_data 	= array();
		$i = 0;
		foreach ($get_prov as $val_prov) {
			$int_jumlah_prov = intval($val_prov->jumlah_provinsi);
			
			$prov_data[] = array( 'name' 		=> $val_prov->provinsi,
								  'y' 	 		=> $int_jumlah_prov,
								  'drilldown'   => $val_prov->provinsi,
			);

			$kab_data[$i] = array( 'name' 		=> $val_prov->provinsi,
								   'id' 	 	=> $val_prov->provinsi,
			);

			$get_kab 	= $this->Dashboard_model->get_customer_kabupaten($start, $end, $val_prov->provinsi);
			foreach ($get_kab as $val_kab) {
				$int_jumlah_kab = intval($val_kab->jumlah_kabupaten);

				$kab_data[$i]['data'][] = array( 0	=> $val_kab->kabupaten,
									 		     1	=> $int_jumlah_kab
				);
			}

			$i++;
		}

		$result = array(	'provinsi'	=> $prov_data,
							'kabupaten' => $kab_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_bar_produk()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_produk	= $this->Dashboard_model->get_produk_toko_periodik($start, $end);
		$produk = array();
		foreach ($get_produk as $val_pro) {
			$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
			if (isset($cek_propak)) {
				// foreach ($cek_propak as $val_propak) {
				// 	$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
			
				// 	$produk[] = array( 0 => $val_propak->nama_produk,
				// 					   1 => $int_fix
				// 	);		
				// }
			}else{
				// $int_jumlah = intval($val_pro->jumlah_produk);
			
				// $produk[] = array( 0 => $val_pro->nama_produk,
				// 						  1 => $int_jumlah
				// );
			}

			$int_jumlah = intval($val_pro->jumlah_produk);
			
			$produk[] = array( 0 => $val_pro->nama_produk,
							   1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'produk'  => $produk
		);

		echo json_encode($result);
	}

	public function ajax_bar_produk_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_produk	= $this->Dashboard_model->get_produk_toko_periodik_penjualan($start, $end);
		$produk = array();
		foreach ($get_produk as $val_pro) {
			$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
			if (isset($cek_propak)) {
				// foreach ($cek_propak as $val_propak) {
				// 	$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
			
				// 	$produk[] = array( 0 => $val_propak->nama_produk,
				// 					   1 => $int_fix
				// 	);		
				// }
			}else{
				// $int_jumlah = intval($val_pro->jumlah_produk);
			
				// $produk[] = array( 0 => $val_pro->nama_produk,
				// 						  1 => $int_jumlah
				// );
			}

			$int_jumlah = intval($val_pro->jumlah_produk);
			
			$produk[] = array( 0 => $val_pro->nama_produk,
							   1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'produk'  => $produk
		);

		echo json_encode($result);
	}

	public function ajax_bar_kurir()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_kurir	= $this->Dashboard_model->get_kurir_by_periodik($start, $end);
		$kurir = array();
		foreach ($get_kurir as $val_kur) {
			$int_jumlah = intval($val_kur->jumlah_kurir);
			
			$kurir[] = array( 0 => $val_kur->nama_kurir,
									  1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'kurir'  => $kurir
		);

		echo json_encode($result);
	}

	public function ajax_bar_kurir_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_kurir	= $this->Dashboard_model->get_kurir_by_periodik_penjualan($start, $end);
		$kurir = array();
		foreach ($get_kurir as $val_kur) {
			$int_jumlah = intval($val_kur->jumlah_kurir);
			
			$kurir[] = array( 0 => $val_kur->nama_kurir,
									  1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'kurir'  => $kurir
		);

		echo json_encode($result);
	}

	public function ajax_bar_pesanan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_pesanan	= $this->Dashboard_model->get_pesanan_by_periodik($start, $end);
		$pesanan		= array();
		foreach ($get_pesanan as $val_pesanan) {
			$int_jumlah = intval($val_pesanan->jumlah_tanggal);
			
			$pesanan[] = array( 0 => $val_pesanan->tanggal,
							  1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'pesanan'  => $pesanan
		);

		echo json_encode($result);
	}

	public function ajax_bar_pesanan_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_pesanan	= $this->Dashboard_model->get_pesanan_by_periodik_penjualan($start, $end);
		$pesanan		= array();
		foreach ($get_pesanan as $val_pesanan) {
			$int_jumlah = intval($val_pesanan->jumlah_tanggal);
			
			$pesanan[] = array( 0 => $val_pesanan->tanggal,
							  1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'pesanan'  => $pesanan
		);

		echo json_encode($result);
	}

	// Retur
	public function ajax_dasbor_jenis_toko_retur()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_jenis_toko = $this->Dashboard_model->get_jenis_toko_retur($start, $end);
		$jenis_data 	= array();
		$toko_data 		= array();
		$i = 0;
		foreach ($get_jenis_toko as $val_jenis) {
			$int_jumlah_jenis = intval($val_jenis->jumlah_jenis);
			
			$jenis_data[] = array( 'name' 		=> $val_jenis->nama_jenis_toko,
								  'y' 	 		=> $int_jumlah_jenis,
								  'drilldown'   => $val_jenis->nama_jenis_toko,
			);

			$toko_data[$i] = array( 'name' 		=> $val_jenis->nama_jenis_toko,
								   	  'id'	 	 	=> $val_jenis->nama_jenis_toko,
			);

			$get_toko = $this->Dashboard_model->get_toko_by_jenis_retur($start, $end, $val_jenis->jenis_toko_id);
			foreach ($get_toko as $val_tok) {
				$int_jumlah_tok = intval($val_tok->jumlah_toko);

				$toko_data[$i]['data'][] = array( 0	=> $val_tok->nama_toko,
									 		      1	=> $int_jumlah_tok
				);
			}

			$i++;
		}

		$result = array(	'jenis'		=> $jenis_data,
							'toko'		=> $toko_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}
	
	public function ajax_line_income_retur()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_income	= $this->Dashboard_model->get_pendapat_periodik_retur($start, $end);
		$income 	= array();
		// $hpp 		= array();
		$diterima	= array();
		$laba 		= array();
		$ongkir 	= array();
		foreach ($get_income as $val_come) {
			$int_jumlah_income = intval($val_come->fix);
			// $int_jumlah_hpp	   = intval($val_come->tot_hpp);
			$int_jumlah_diterima   = intval($val_come->diterima);
			$int_jumlah_laba   = intval($val_come->total);
			$int_jumlah_ongkir = intval($val_come->tot_ongkir);
			
			$income[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_income		
			);

			// $hpp[] = array( 0 => $val_come->tanggal,
			// 				   1 => $int_jumlah_hpp		
			// );

			$diterima[] = array( 0 => $val_come->tanggal,
							   	 1 => $int_jumlah_diterima		
			);

			$laba[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_laba		
			);

			$ongkir[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_ongkir		
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'income'  => $income,
						 'diterima'  => $diterima,
						 // 'hpp' 	   => $hpp,
						 'laba'    => $laba,
						 'ongkir'  => $ongkir
		);

		echo json_encode($result);
	}

	public function ajax_pie_provkab_retur()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_prov 	= $this->Dashboard_model->get_customer_provinsi_retur($start, $end);
		$prov_data 	= array();
		$kab_data 	= array();
		$i = 0;
		foreach ($get_prov as $val_prov) {
			$int_jumlah_prov = intval($val_prov->jumlah_provinsi);
			
			$prov_data[] = array( 'name' 		=> $val_prov->provinsi,
								  'y' 	 		=> $int_jumlah_prov,
								  'drilldown'   => $val_prov->provinsi,
			);

			$kab_data[$i] = array( 'name' 		=> $val_prov->provinsi,
								   'id' 	 	=> $val_prov->provinsi,
			);

			$get_kab 	= $this->Dashboard_model->get_customer_kabupaten_retur($start, $end, $val_prov->provinsi);
			foreach ($get_kab as $val_kab) {
				$int_jumlah_kab = intval($val_kab->jumlah_kabupaten);

				$kab_data[$i]['data'][] = array( 0	=> $val_kab->kabupaten,
									 		     1	=> $int_jumlah_kab
				);
			}

			$i++;
		}

		$result = array(	'provinsi'	=> $prov_data,
							'kabupaten' => $kab_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_bar_produk_retur()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_produk	= $this->Dashboard_model->get_produk_toko_periodik_retur($start, $end);
		$produk = array();
		foreach ($get_produk as $val_pro) {
			$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
			if (isset($cek_propak)) {
				// foreach ($cek_propak as $val_propak) {
				// 	$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
			
				// 	$produk[] = array( 0 => $val_propak->nama_produk,
				// 					   1 => $int_fix
				// 	);		
				// }
			}else{
				// $int_jumlah = intval($val_pro->jumlah_produk);
			
				// $produk[] = array( 0 => $val_pro->nama_produk,
				// 						  1 => $int_jumlah
				// );
			}

			$int_jumlah = intval($val_pro->jumlah_produk);
			
			$produk[] = array( 0 => $val_pro->nama_produk,
							   1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'produk'  => $produk
		);

		echo json_encode($result);
	}

	public function ajax_bar_kurir_retur()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_kurir	= $this->Dashboard_model->get_kurir_by_periodik_retur($start, $end);
		$kurir = array();
		foreach ($get_kurir as $val_kur) {
			$int_jumlah = intval($val_kur->jumlah_kurir);
			
			$kurir[] = array( 0 => $val_kur->nama_kurir,
									  1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'kurir'  => $kurir
		);

		echo json_encode($result);
	}

	public function ajax_bar_pesanan_retur()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_pesanan	= $this->Dashboard_model->get_pesanan_by_periodik_retur($start, $end);
		$pesanan		= array();
		foreach ($get_pesanan as $val_pesanan) {
			$int_jumlah = intval($val_pesanan->jumlah_tanggal);
			
			$pesanan[] = array( 0 => $val_pesanan->tanggal,
							  1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'pesanan'  => $pesanan
		);

		echo json_encode($result);
	}

	// Retur Penjualan
	public function ajax_dasbor_jenis_toko_retur_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_jenis_toko = $this->Dashboard_model->get_jenis_toko_retur_penjualan($start, $end);
		$jenis_data 	= array();
		$toko_data 		= array();
		$i = 0;
		foreach ($get_jenis_toko as $val_jenis) {
			$int_jumlah_jenis = intval($val_jenis->jumlah_jenis);
			
			$jenis_data[] = array( 'name' 		=> $val_jenis->nama_jenis_toko,
								  'y' 	 		=> $int_jumlah_jenis,
								  'drilldown'   => $val_jenis->nama_jenis_toko,
			);

			$toko_data[$i] = array( 'name' 		=> $val_jenis->nama_jenis_toko,
								   	  'id'	 	 	=> $val_jenis->nama_jenis_toko,
			);

			$get_toko = $this->Dashboard_model->get_toko_by_jenis_retur_penjualan($start, $end, $val_jenis->jenis_toko_id);
			foreach ($get_toko as $val_tok) {
				$int_jumlah_tok = intval($val_tok->jumlah_toko);

				$toko_data[$i]['data'][] = array( 0	=> $val_tok->nama_toko,
									 		      1	=> $int_jumlah_tok
				);
			}

			$i++;
		}

		$result = array(	'jenis'		=> $jenis_data,
							'toko'		=> $toko_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}
	
	public function ajax_line_income_retur_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_income	= $this->Dashboard_model->get_pendapat_periodik_retur_penjualan($start, $end);
		$income 	= array();
		// $hpp 		= array();
		$diterima	= array();
		$laba 		= array();
		$ongkir 	= array();
		foreach ($get_income as $val_come) {
			$int_jumlah_income = intval($val_come->fix);
			// $int_jumlah_hpp	   = intval($val_come->tot_hpp);
			$int_jumlah_diterima   = intval($val_come->diterima);
			$int_jumlah_laba   = intval($val_come->total);
			$int_jumlah_ongkir = intval($val_come->tot_ongkir);
			
			$income[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_income		
			);

			// $hpp[] = array( 0 => $val_come->tanggal,
			// 				   1 => $int_jumlah_hpp		
			// );

			$diterima[] = array( 0 => $val_come->tanggal,
							   	 1 => $int_jumlah_diterima		
			);

			$laba[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_laba		
			);

			$ongkir[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_ongkir		
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'income'  => $income,
						 'diterima'  => $diterima,
						 // 'hpp' 	   => $hpp,
						 'laba'    => $laba,
						 'ongkir'  => $ongkir
		);

		echo json_encode($result);
	}

	public function ajax_pie_provkab_retur_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_prov 	= $this->Dashboard_model->get_customer_provinsi_retur_penjualan($start, $end);
		$prov_data 	= array();
		$kab_data 	= array();
		$i = 0;
		foreach ($get_prov as $val_prov) {
			$int_jumlah_prov = intval($val_prov->jumlah_provinsi);
			
			$prov_data[] = array( 'name' 		=> $val_prov->provinsi,
								  'y' 	 		=> $int_jumlah_prov,
								  'drilldown'   => $val_prov->provinsi,
			);

			$kab_data[$i] = array( 'name' 		=> $val_prov->provinsi,
								   'id' 	 	=> $val_prov->provinsi,
			);

			$get_kab 	= $this->Dashboard_model->get_customer_kabupaten_retur_penjualan($start, $end, $val_prov->provinsi);
			foreach ($get_kab as $val_kab) {
				$int_jumlah_kab = intval($val_kab->jumlah_kabupaten);

				$kab_data[$i]['data'][] = array( 0	=> $val_kab->kabupaten,
									 		     1	=> $int_jumlah_kab
				);
			}

			$i++;
		}

		$result = array(	'provinsi'	=> $prov_data,
							'kabupaten' => $kab_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_bar_produk_retur_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_produk	= $this->Dashboard_model->get_produk_toko_periodik_retur_penjualan($start, $end);
		$produk = array();
		foreach ($get_produk as $val_pro) {
			$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
			if (isset($cek_propak)) {
				// foreach ($cek_propak as $val_propak) {
				// 	$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
			
				// 	$produk[] = array( 0 => $val_propak->nama_produk,
				// 					   1 => $int_fix
				// 	);		
				// }
			}else{
				// $int_jumlah = intval($val_pro->jumlah_produk);
			
				// $produk[] = array( 0 => $val_pro->nama_produk,
				// 						  1 => $int_jumlah
				// );
			}

			$int_jumlah = intval($val_pro->jumlah_produk);
			
			$produk[] = array( 0 => $val_pro->nama_produk,
							   1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'produk'  => $produk
		);

		echo json_encode($result);
	}

	public function ajax_bar_kurir_retur_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_kurir	= $this->Dashboard_model->get_kurir_by_periodik_retur_penjualan($start, $end);
		$kurir = array();
		foreach ($get_kurir as $val_kur) {
			$int_jumlah = intval($val_kur->jumlah_kurir);
			
			$kurir[] = array( 0 => $val_kur->nama_kurir,
									  1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'kurir'  => $kurir
		);

		echo json_encode($result);
	}

	public function ajax_bar_pesanan_retur_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_pesanan	= $this->Dashboard_model->get_pesanan_by_periodik_retur_penjualan($start, $end);
		$pesanan		= array();
		foreach ($get_pesanan as $val_pesanan) {
			$int_jumlah = intval($val_pesanan->jumlah_tanggal);
			
			$pesanan[] = array( 0 => $val_pesanan->tanggal,
							  1 => $int_jumlah
			);
		}

		$result = array( 'tanggal' => $start." - ".$end,
						 'pesanan'  => $pesanan
		);

		echo json_encode($result);
	}
}
