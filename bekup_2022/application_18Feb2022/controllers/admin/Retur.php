<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Retur';

	    $this->load->model(array('Retur_model', 'Keluar_model', 'Produk_model', 'Resi_model'));

	    $this->data['company_data']    				= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    // $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['add_action'] = base_url('admin/retur/retur_produk');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	function dasbor_retur()
	{
		$this->data['page_title'] = 'Dashboard '.$this->data['module'];

		$this->load->view('back/retur/retur_dashboard', $this->data);
	}

	function dasbor_list_count(){
		$kurir 		= $this->input->post('kurir');
		$toko 	= $this->input->post('toko');
		$status = $this->input->post('status');
		$followup = $this->input->post('followup');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Retur_model->get_dasbor_list($status, $followup, $kurir, $toko, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
        					'diproses'	=> $data->diproses,
			        		'sudah'		=> $data->sudah,
			        		'belum_fu'	=> $data->belum_fu,
			        		'sudah_fu'	=> $data->sudah_fu,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_produk(){
		$kurir 		= $this->input->post('kurir');
		$toko 	= $this->input->post('toko');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Retur_model->get_dasbor_list_produk($kurir, $toko, $start, $end);
    	if (isset($data)) {	
    		if ($data->total_produk == '' || $data->total_produk == 0) {
    			$total_produk = 0;
    		}else{
    			$total_produk = $data->total_produk;
    		}
        	$msg = array(	'total'		=> $data->total,
        					'produk'	=> $total_produk,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_riwayat(){
		$kurir 		= $this->input->post('kurir');
		$toko 	= $this->input->post('toko');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Retur_model->get_dasbor_list_riwayat($kurir, $toko, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
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
		$status = $this->input->get('status');
		$start = substr($this->input->get('periodik'), 0, 10);
		$end = substr($this->input->get('periodik'), 13, 24);
		$rows = array();
		$get_all = $this->Retur_model->get_datatable($status, $kurir, $toko, $start, $end);
		foreach ($get_all as $data) {
			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan).' Produk</span>';
			if ($data->status_retur == 0) {
				$action = ' <a href="'.base_url('admin/retur/proses/'.$data->nomor_retur).'" class="btn btn-sm btn-primary"><i class="fa fa-paper-plane"></i></a> ';
				$action .= '<a href="" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-whatsapp"></i></a>';
				$action .= ' <a href="'.base_url('admin/retur/ubah/'.$data->nomor_retur).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
	          	$action .= ' <a href="'.base_url('admin/retur/hapus/'.$data->nomor_retur).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i> Sedang diproses</a>";
			}else if($data->status_retur == 1) {
				$action = '<a href="https://api.whatsapp.com/send?phone='.$data->hp_penerima.'&text=Halo%20kak%20'.$data->nama_penerima.'%0APerkenalkan%20aku%20Hani%20dari%20Brighty%20Indonesia%20%F0%9F%A5%B0%0A%0AAku%20mau%20infoin%20ke%20kakak%20bahwa%20paket%20kakak%20return%20ke%20kita%20lagi%20karena%20kendala%20dari%20pihak%20ekspedisi%20nya%20kak.%0A%0ANah%2C%20aku%20mau%20kasih%20kakak%20opsi%20pengiriman%20sesuai%20yang%20kakak%20mau%20agar%20paket%20bisa%20sampai%20ke%20lokasi%20kakak%20dengan%20aman%20ya%20%F0%9F%A5%B0%0A%0AJika%20berminat%20kakak%20bisa%20balas%20chat%20aku%20ya%20kak%20%F0%9F%A5%B0%0A%0ATerimakasih%0ASalam%20Sayang%0ABrighty%20Indonesia%20%E2%9D%A4" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-whatsapp"></i></a>';
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

			$rows[] = array( 'no'				=> $i,
							 'tanggal' 			=> date('d-m-Y', strtotime($data->tgl_retur)), 
							 'nomor_retur' 		=> $data->nomor_retur, 
							 'nomor_pesanan'    => $produk,
							 'nama_toko' 		=> $data->nama_toko,
							 'nama_kurir' 		=> $data->nama_kurir,
							 'nomor_resi' 		=> $data->nomor_resi,
							 'total_harga' 		=> $data->total_harga,
							 'keterangan' 		=> $data->keterangan_retur,
							 'action' 			=> $action,
							 'status' 			=> $status,
							 'detail' 			=> $detail,
							 'created' 			=> $data->created 
			);

			$i++;
		}
		echo json_encode($rows);
	}

	// Datatable Server Side
	// Retur
	function get_data_retur()
    {
    	$i = 1;
        $list = $this->Retur_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
   			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan).' Produk</span>';
			if ($data->status_retur == 0) {
				$action = ' <a href="'.base_url('admin/retur/proses/'.$data->nomor_retur).'" class="btn btn-sm btn-primary"><i class="fa fa-paper-plane"></i></a> ';
				$action .= '<a href="'.base_url('admin/retur/wa_proses/'.base64_encode($data->nomor_retur)).'" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-whatsapp"></i></a>';
				// $action .= '<a href="https://api.whatsapp.com/send?phone='.$data->hp_penerima.'&text=Halo%20kak%20'.$data->nama_penerima.'%0APerkenalkan%20aku%20Hani%20dari%20Brighty%20Indonesia%20%F0%9F%A5%B0%0A%0AAku%20mau%20infoin%20ke%20kakak%20bahwa%20paket%20kakak%20return%20ke%20kita%20lagi%20karena%20kendala%20dari%20pihak%20ekspedisi%20nya%20kak.%0A%0ANah%2C%20aku%20mau%20kasih%20kakak%20opsi%20pengiriman%20sesuai%20yang%20kakak%20mau%20agar%20paket%20bisa%20sampai%20ke%20lokasi%20kakak%20dengan%20aman%20ya%20%F0%9F%A5%B0%0A%0AJika%20berminat%20kakak%20bisa%20balas%20chat%20aku%20ya%20kak%20%F0%9F%A5%B0%0A%0ATerimakasih%0ASalam%20Sayang%0ABrighty%20Indonesia%20%E2%9D%A4" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-whatsapp"></i></a>';
				$action .= ' <a href="'.base_url('admin/retur/ubah/'.$data->nomor_retur).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
	          	$action .= ' <a href="'.base_url('admin/retur/hapus/'.$data->nomor_retur).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i> Sedang diproses</a>";
			}else if($data->status_retur == 1) {
				$action = '<a href="'.base_url('admin/retur/wa_proses/'.base64_encode($data->nomor_retur)).'" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-whatsapp"></i></a>';
				// $action = '<a href="https://api.whatsapp.com/send?phone='.$data->hp_penerima.'&text=Halo%20kak%20'.$data->nama_penerima.'%0APerkenalkan%20aku%20Hani%20dari%20Brighty%20Indonesia%20%F0%9F%A5%B0%0A%0AAku%20mau%20infoin%20ke%20kakak%20bahwa%20paket%20kakak%20return%20ke%20kita%20lagi%20karena%20kendala%20dari%20pihak%20ekspedisi%20nya%20kak.%0A%0ANah%2C%20aku%20mau%20kasih%20kakak%20opsi%20pengiriman%20sesuai%20yang%20kakak%20mau%20agar%20paket%20bisa%20sampai%20ke%20lokasi%20kakak%20dengan%20aman%20ya%20%F0%9F%A5%B0%0A%0AJika%20berminat%20kakak%20bisa%20balas%20chat%20aku%20ya%20kak%20%F0%9F%A5%B0%0A%0ATerimakasih%0ASalam%20Sayang%0ABrighty%20Indonesia%20%E2%9D%A4" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-whatsapp"></i></a>';
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
            $row['action'] = $action;
            $row['status'] = $status;
            $row['detail'] = $detail;
            $row['created'] = $data->created;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Retur_model->count_all(),
            "recordsFiltered" => $this->Retur_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // Produk
    function get_data_produk()
    {
    	$i = 1;
        $list = $this->Retur_model->get_datatables_produk();
        $dataJSON = array();
        foreach ($list as $data) {
			$action = ' <a href="'.base_url('admin/retur/stok_aktif/'.$data->id_produk_retur).'" class="btn btn-sm btn-success"><i class="fa fa-cubes"></i></a> ';

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
            $row['no'] = $i;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_retur));
            $row['nomor_retur'] = $data->nomor_retur;
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['nama_produk'] = $data->nama_produk;
            $row['nama_toko'] = $data->nama_toko;
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['total_harga'] = $data->total_harga;
            $row['qty_retur'] = $data->qty_retur;
            $row['keterangan_produk'] = $data->keterangan_produk_retur;
            $row['keterangan'] = $data->keterangan_retur;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['action'] = $action;
            $row['detail']	= $detail;
            $row['created'] = $data->created;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Retur_model->count_all_produk(),
            "recordsFiltered" => $this->Retur_model->count_filtered_produk(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // Riwayat
    function get_data_riwayat()
    {
    	$i = 1;
        $list = $this->Retur_model->get_datatables_riwayat();
        $dataJSON = array();
        foreach ($list as $data) {
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
            $row['no'] = $i;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_riwayat_retur));
            $row['nomor_retur'] = $data->nomor_retur;
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['nama_produk'] = $data->nama_produk;
            $row['nama_toko'] = $data->nama_toko;
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['total_harga'] = $data->total_harga;
            $row['qty_retur'] = $data->qty_retur;
            $row['keterangan_produk'] = $data->keterangan_produk_retur;
            $row['keterangan'] = $data->keterangan_retur;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['detail']	= $detail;
            $row['created'] = $data->created;
            $row['updated_produk'] = $data->updated_qty_produk;
            $row['updated_retur'] = $data->updated_qty_retur;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Retur_model->count_all_riwayat(),
            "recordsFiltered" => $this->Retur_model->count_filtered_riwayat(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function data_retur()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';

	    $this->data['get_all_kurir'] = $this->Keluar_model->get_all_kurir_list();
	    $this->data['get_all_toko'] = $this->Keluar_model->get_all_toko_list();
	    $this->data['get_all_status'] = array( 'semua'	=> '- Semua Data-',
	    									   '0' 		=> 'Sedang diproses',
	    									   '1' 		=> 'Sudah diproses',
	     								);
	    $this->data['get_all_followup'] = array( 'semua'	=> '- Semua Data-',
		    									 '0' 		=> 'Belum difollow up',
		    									 '1' 		=> 'Sudah difollow up',
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

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['followup'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'followup',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/retur/retur_list', $this->data);
	}

	public function produk_retur()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' Product List';

	    $this->data['get_all_kurir'] = $this->Keluar_model->get_all_kurir_list();
	    $this->data['get_all_toko'] = $this->Keluar_model->get_all_toko_list();

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

	    $this->load->view('back/retur/retur_produk_list', $this->data);
	}

	public function riwayat_retur()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' Product History List';

	    $this->data['get_all_kurir'] = $this->Keluar_model->get_all_kurir_list();
	    $this->data['get_all_toko'] = $this->Keluar_model->get_all_toko_list();

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

	    $this->load->view('back/retur/retur_riwayat', $this->data);
	}

	public function retur_produk()
	{
		is_create();    

	    $this->data['page_title'] = 'Scan Resi: '.$this->data['module'];
	    // $this->data['get_cek'] = $this->Resi_model->get_data_cek();
	    // $this->data['action']     = 'admin/resi/tambah_proses';

	    $this->data['no_resi'] = [
	      'name'          => 'no_resi',
	      'id'            => 'no-resi',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'onchange'	  => 'cekResi()',
	    ];

	    $this->load->view('back/retur/retur_scan', $this->data);
	}

	public function retur_produk_proses()
	{
		// Ambil Data
		date_default_timezone_set("Asia/Jakarta");
		$i = $this->input;

		$nomor_pesanan = $i->post('nomor_pesanan');
		$resi = $i->post('resi');
		$status_resi = $i->post('status_resi');
		$keterangan = $i->post('keterangan');
		$status = $i->post('status');
		
		$date= date("Y-m-d");
		$tahun = substr($date, 2, 2);
		$bulan = substr($date, 5, 2);
		$tanggal = substr($date, 8, 2);
		$teks = "RTR".$tahun.$bulan.$tanggal;
		$ambil_nomor = $this->Retur_model->cari_nomor($teks);
		// echo print_r(json_encode($ambil_nomor));
		// $hitung = count($ambil_nomor);
		// echo $ambil_nomor->nomor_pesanan;
		if (isset($ambil_nomor)) {
			// TANGGAL DARI ID NILAI
			$ambil_tahun = substr($ambil_nomor->nomor_retur, 3, 2);
			$ambil_bulan = substr($ambil_nomor->nomor_retur, 5, 2);
			$ambil_tanggal = substr($ambil_nomor->nomor_retur, 7, 2);
			$ambil_no = (int) substr($ambil_nomor->nomor_retur, 9, 4);

			if ($tahun == $ambil_tahun && $bulan == $ambil_bulan && $tanggal == $ambil_tanggal) {
				$ambil_no++;	
				$no_retur = "RTR".$tahun.$bulan.$tanggal.sprintf("%04s", $ambil_no);
			}else{
				$no_retur = "RTR".$tahun.$bulan.$tanggal."0001";
			}
		}else{
			$no_retur = "RTR".$tahun.$bulan.$tanggal."0001";
		}

		$now = date('Y-m-d H:i:s');
		$dataRetur = array(	'nomor_retur'		=> $no_retur,
							'nomor_pesanan' 	=> $nomor_pesanan,
							'tgl_retur' 		=> $now,
							'keterangan_retur' 	=> str_replace(';', ',', $keterangan),
							'status_retur' 		=> 0,
							'status_transaksi' 	=> $status,
							'status_resi_retur' => $status_resi, 
		);

		$this->Retur_model->insert($dataRetur);

		write_log();

		$updatePesanan = array( 'id_status_transaksi' => 4
		);

		$this->Keluar_model->update($nomor_pesanan, $updatePesanan);

		write_log();

		$dataResi = array(	'id_users' 		=> $this->session->userdata('id_users'), 
							'status' 		=> 3,
							'tgl_resi' 		=> $now 
		);
		$this->Resi_model->update_by_resi($resi, $dataResi);

		write_log();

		$pesan = "Berhasil disimpan!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function scan_proses()
	{
		$resi 		= $this->input->post('resi');
		$cek_resi	= $this->Retur_model->get_cek_resi_all_by_resi($resi);
		if (isset($cek_resi)) {
			if ($cek_resi->id_status_transaksi == 4) {
				$pesan = "No Resi sudah diretur!";	
	        	$msg = array(	'validasi'	=> $pesan,
	        			);
	        	echo json_encode($msg); 
			}else{
				$i = 1;
				$rows = array();
				$produk_pesanan = $this->Keluar_model->get_detail_by_id($cek_resi->nomor_pesanan);
				foreach ($produk_pesanan as $data) {
					$rows[] = array( 'no'				=> $i,
									 'nama_produk' 		=> $data->nama_produk, 
									 'qty' 				=> $data->qty
					);

					$i++;
				}

				$pesan = "No Resi ditemukan!";	
	        	$msg = array(	'sukses'			=> $pesan,
	        					'nomor_pesanan' 	=> $cek_resi->nomor_pesanan,
	        					'nomor_resi' 		=> $cek_resi->nomor_resi,
	        					'nama_kurir' 		=> $cek_resi->nama_kurir,
	        					'nama_toko' 		=> $cek_resi->nama_toko,
	        					'nama_penerima' 	=> $cek_resi->nama_penerima,
	        					'alamat_penerima' 	=> $cek_resi->alamat_penerima,
	        					'kabupaten'		 	=> $cek_resi->kabupaten,
	        					'provinsi'		 	=> $cek_resi->provinsi,
	        					'hp_penerima'	 	=> $cek_resi->hp_penerima,
	        					'status'	 		=> $cek_resi->id_status_transaksi,
	        					'status_resi'	 	=> $cek_resi->status,
	        					'table' 			=> $rows
	        			);
	        	echo json_encode($msg); 
			}	
		}else{
			$pesan = "No Resi tidak ditemukan!";	
        	$msg = array(	'validasi'	=> $pesan
        			);
        	echo json_encode($msg); 
		}
	}

	public function ubah($id)
	{
		is_update();

	    $this->data['retur']     = $this->Retur_model->get_all_by_retur($id);

	    if($this->data['retur'])
	    {
	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
	      $this->data['action']     = 'admin/retur/ubah_proses';
	      $this->data['produk'] = $this->Keluar_model->get_detail_by_id($this->data['retur']->nomor_pesanan);

	      $this->data['nomor_retur'] = [
	        'name'          => 'nomor_retur',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['keterangan'] = [
		      'name'          => 'keterangan',
		      'id'            => 'keterangan',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

	      $this->load->view('back/retur/retur_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/retur/data_retur');
	    }
	}

	public function ubah_proses()
	{
		$this->form_validation->set_rules('keterangan', 'Keterangan Retur', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  $this->ubah($this->input->post('nomor_retur'));
		}
		else
		{
		  $updateRetur = array(	'keterangan_retur' 	=> str_replace(';', ',', $this->input->post('keterangan'))
		  );

		  $this->Retur_model->update($this->input->post('nomor_retur'), $updateRetur);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		  redirect('admin/retur/data_retur');
		}
	}

	public function stok_aktif($id)
	{
		is_update();

		$this->data['produk_retur']     = $this->Retur_model->get_produk_retur_by_id($id);

		if($this->data['produk_retur'])
	    {
	    	$this->data['page_title'] = 'Process Active Stock '.$this->data['module'].': '.$this->data['produk_retur']->nomor_retur;
		    $this->data['action']     = 'admin/retur/proses_stok_aktif';

			$this->data['id_produk_retur'] = [
				'name'          => 'id_produk_retur',
				'type'          => 'hidden',
			];

			$this->load->view('back/retur/retur_stok', $this->data);    
	    }
	}

	public function proses_stok_aktif()
	{
		date_default_timezone_set("Asia/Jakarta");
	    $now = date('Y-m-d H:i:s');
		$id = $this->input->post('id_produk_retur');
		$produk = $this->input->post('produk');
		$qty_aktif = $this->input->post('qty');

		$row_produk = $this->Produk_model->get_by_id($produk);
  		$tambahStok = array( 	'qty_produk' 		=> $row_produk->qty_produk + $qty_aktif
						);

		$this->Produk_model->update($produk, $tambahStok);	

		write_log();

		$row_stok = $this->Retur_model->get_produk_retur_by_id($id);
  		$kurangiStok = array( 	'qty_retur' 		=> $row_stok->qty_retur - $qty_aktif
						);

		$this->Retur_model->update_stok_aktif($id, $kurangiStok);	

		write_log();

		$riwayatRetur = array( 'id_produk_retur'		=> $id,
							   'updated_qty_produk'		=> $tambahStok['qty_produk'],
							   'updated_qty_retur' 		=> $kurangiStok['qty_retur'],
							   'tgl_riwayat_retur' 		=> $now

		);

		$this->Retur_model->insert_riwayat_retur($riwayatRetur);	

		write_log();

		$this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	    redirect('admin/retur/produk_retur');
	}

	public function proses($id)
	{
		is_update();

	    $this->data['retur']     = $this->Retur_model->get_all_by_retur($id);

	    if($this->data['retur'])
	    {
	      $this->data['page_title'] = 'Process '.$this->data['module'].': '.$id;
	      $this->data['action']     = 'admin/retur/proses_retur';
	      $this->data['produk'] = $this->Keluar_model->get_detail_by_id($this->data['retur']->nomor_pesanan);
	      // $this->data['fix_produk']	= array();

	      foreach ($this->data['produk'] as $cek) {
	      	$cekPaket = $this->Produk_model->get_produk_paket_by_id($cek->id_produk);
	      	if (!count($cekPaket) > 0) {
	      		// echo $cek->id_produk;
	      		$this->data['array_produk'][] = array( 'id_produk'	=> $cek->id_produk,
		      										 'nama_produk' 	=> $cek->nama_produk,
		      										 'qty' 			=> $cek->qty

		      	);
	      		
	      	}else{
	      		foreach ($cekPaket as $isiPaket) {
	      			$total = $cek->qty * $isiPaket->qty_pakduk;
	      			$this->data['array_produk'][] = array( 'id_produk'	=> $isiPaket->produk_id,
			      										 'nama_produk' 	=> $isiPaket->nama_produk,
			      										 'qty' 			=> $total

			      	);
	      		} 
	      	}
	      }

	      // Create an array with unique types
		    $ids = array();
		    foreach ($this->data['array_produk'] as $ids_key) {
		       if(!in_array($ids_key['id_produk'], $ids))
		           $ids[] = $ids_key['id_produk'];
		    }
		    // Loop through that array and sum amount of all equal types
		    #$x = [];
		    $i = 0;
		    foreach ($ids as $id_val) {
		        $x[$i]['qty'] = 0;
		        foreach ($this->data['array_produk'] as $val_arr) {
		            if($id_val == $val_arr['id_produk']){ // we have a matching type (x or y)
		                $x[$i]['id_produk'] = $val_arr['id_produk'];
		                $x[$i]['nama_produk'] = $val_arr['nama_produk'];
		                $x[$i]['qty'] += $val_arr['qty'];
		            }
		        }
		        $i += 1;
		    }

	      $convertedObj = ToObject($x);
	      // echo print_r($this->data['array_produk'])."</br>";
	      // $sum = array_sum(array_column($convertedObj, 'qty'));
	      // echo print_r($convertedObj);
	      $this->data['fix'] = $convertedObj;

	      $this->data['nomor_retur'] = [
	        'name'          => 'nomor_retur',
	        'type'          => 'hidden',
	      ];
		  
		  $this->data['keterangan'] = [
		      'name'          => 'keterangan',
		      'id'            => 'keterangan',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		  ];

	      $this->load->view('back/retur/retur_proses', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/retur/data_retur');
	    }
	}

	public function proses_retur()
	{
		date_default_timezone_set("Asia/Jakarta");
		$this->form_validation->set_rules('keterangan', 'Keterangan Retur', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if($this->form_validation->run() === FALSE)
		{
		  	$this->proses($this->input->post('nomor_retur'));
		}
		else
		{
			$keterangan = $this->input->post('keterangan_produk');
			$nomor_retur = $this->input->post('nomor_retur');
			$produk = $this->input->post('produk');
			$qty = $this->input->post('qty');
			$lenght = count($this->input->post('qty'));

			for ($i = 0; $i < $lenght; $i++) {
				$produkData = array( 'nomor_retur'				=> $nomor_retur,
									 'id_produk' 				=> $produk[$i],
									 'qty_retur' 				=> $qty[$i],
									 'keterangan_produk_retur'  => str_replace(';', ',', $keterangan[$i])
				);

				$this->Retur_model->insert_produk_retur($produkData);

				write_log();
			}

			$updateRetur = array( 'status_retur'	=> 1 
			);

			$this->Retur_model->update($nomor_retur, $updateRetur);

			write_log();

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
		    redirect('admin/retur/data_retur');
		}

		
	}

	function wa_proses($id)
	{
		$data = $this->Retur_model->get_by_id(base64_decode($id));
		$updateRetur = array( 'status_follow_up'	=> 1 
		);

		$this->Retur_model->update($data->nomor_retur, $updateRetur);

		write_log();

		redirect('https://api.whatsapp.com/send?phone='.$data->hp_penerima.'&text=Halo%20kak%20'.$data->nama_penerima.'%0APerkenalkan%20aku%20Hani%20dari%20Brighty%20Indonesia%20%F0%9F%A5%B0%0A%0AAku%20mau%20infoin%20ke%20kakak%20bahwa%20paket%20kakak%20return%20ke%20kita%20lagi%20karena%20kendala%20dari%20pihak%20ekspedisi%20nya%20kak.%0A%0ANah%2C%20aku%20mau%20kasih%20kakak%20opsi%20pengiriman%20sesuai%20yang%20kakak%20mau%20agar%20paket%20bisa%20sampai%20ke%20lokasi%20kakak%20dengan%20aman%20ya%20%F0%9F%A5%B0%0A%0AJika%20berminat%20kakak%20bisa%20balas%20chat%20aku%20ya%20kak%20%F0%9F%A5%B0%0A%0ATerimakasih%0ASalam%20Sayang%0ABrighty%20Indonesia%20%E2%9D%A4','refresh');
	}

	function hapus($id = '')
	{
		is_delete();

		$delete = $this->Retur_model->get_by_id($id);

		if($delete)
		{
		  $updatePesanan = array( 'id_status_transaksi' => $delete->status_transaksi
		  );

		  $this->Keluar_model->update($delete->nomor_pesanan, $updatePesanan);

		  write_log();

		  $dataResi = array(	'id_users' 		=> $this->session->userdata('id_users'), 
								'status' 		=> $delete->status_resi_retur,
								'tgl_resi' 		=> $now 
			);
			$this->Resi_model->update_by_resi($delete->nomor_resi, $dataResi);

		  $this->Retur_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/retur/data_retur');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/retur/data_retur');
		}
	}

}

/* End of file Retur.php */
/* Location: ./application/controllers/admin/Retur.php */