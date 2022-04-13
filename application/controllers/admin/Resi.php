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

class Resi extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Resi';

	    $this->load->model(array('Resi_model', 'Keluar_model', 'Auth_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    // $this->data['btn_submit'] = 'Save';
	    // $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['add_action'] = base_url('admin/resi/tambah');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	function dasbor_list_count(){
		$pic 		= $this->input->post('pic');
		$kurir 		= $this->input->post('kurir');
		$status 	= $this->input->post('status');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Resi_model->get_dasbor_list($pic, $kurir, $status, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'	=> $data->total,
			        		'belum'	=> $data->belum,
			        		'proses'=> $data->diproses,
			        		'sudah'	=> $data->sudah,
			        		'gagal' => $data->gagal
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_admin(){
		$data      = $this->Resi_model->get_dasbor_list_admin();
    	if (isset($data)) {	
        	$msg = array(	'total'	=> $data->total,
			        		'belum'	=> $data->belum,
			        		'proses'=> $data->diproses,
			        		'sudah'	=> $data->sudah,
			        		'gagal' => $data->gagal
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_gudang(){
		$data      = $this->Resi_model->get_dasbor_list_gudang();
    	if (isset($data)) {	
        	$msg = array(	'total'	=> $data->total,
			        		'belum'	=> $data->belum,
			        		'proses'=> $data->diproses,
			        		'sudah'	=> $data->sudah,
			        		'gagal' => $data->gagal
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
		$status = $this->input->get('status');
		$start = substr($this->input->get('periodik'), 0, 10);
		$end = substr($this->input->get('periodik'), 13, 24);
		$rows = array();
		$get_all = $this->Resi_model->get_datatable($kurir, $status, $start, $end);
		foreach ($get_all as $data) {
			  if ($data->status ==  0) {
	            $status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-times' style='margin-right:5px;'></i>Belum diproses</a>";
	          }elseif ($data->status ==  1) {
	            $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
	          }else{
	            $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
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

	          $hapus = '<a href="'.base_url('admin/resi/hapus/'.$data->id_resi).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
			  $rows[] = array( 'no'				=> $i,
							 'tanggal' 			=> date('d-m-Y H:i:s', strtotime($data->created_resi)), 
							 'nomor_resi' 		=> $data->nomor_resi,
							 'nomor_pesanan'    => $data->nomor_pesanan,
							 'nama_kurir' 		=> $data->nama_kurir,
							 'status' 			=> $status,
							 'hapus' 			=> $hapus,
							 'detail' 	 		=> $detail 
			);

			$i++;
		}
		echo json_encode($rows);
	}

	// Datatable Server Side
	function get_data_resi()
    {
        $list = $this->Resi_model->get_datatables();
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

	        if ($data->status !=  0) {
            	$pic = $data->nama_hd;	
            }else{
            	$pic = '-';
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
			            '<td width="15%">PIC Resi</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$pic.'</td>'.
			        '</tr>'.
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
            $row['nomor_resi'] = $data->noresi;
            $row['status'] = $status;
            $row['detail'] = $detail;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Resi_model->count_all(),
            "recordsFiltered" => $this->Resi_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_admin()
    {
    	$i = 1;
        $list = $this->Resi_model->get_datatables_admin();
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

            $row = array();
            $row['no'] = $i;
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y H:i:s', strtotime($data->tgl_resi));
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->noresi;
            if ($data->status !=  0) {
            	$row['pic'] = $data->nama_hd;	
            }else{
            	$row['pic'] = '-';
            }
            $row['status'] = $status;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Resi_model->count_all_admin(),
            "recordsFiltered" => $this->Resi_model->count_filtered_admin(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_gudang()
    {
    	$i = 1;
        $list = $this->Resi_model->get_datatables_gudang();
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
	          
            $row = array();
            $row['no'] = $i;
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y H:i:s', strtotime($data->tgl_resi));
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->noresi;
            if ($data->status !=  0) {
            	$row['pic'] = $data->nama_hd;	
            }else{
            	$row['pic'] = '-';
            }
            $row['status'] = $status;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Resi_model->count_all_gudang(),
            "recordsFiltered" => $this->Resi_model->count_filtered_gudang(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function ajax_list_admin()
	{
		$i = 1;
		$rows = array();
		$get_all = $this->Resi_model->get_all_by_harian();
		foreach ($get_all as $data) {
			if ($data->status ==  0) {
	            $status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-times' style='margin-right:5px;'></i>Belum diproses</a>";
	          }elseif ($data->status ==  1) {
	            $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
	          }else{
	            $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
	          }
			$rows[] = array( 'no'				=> $i,
							 'tanggal' 			=> $data->tgl_resi, 
							 'nomor_resi' 		=> $data->nomor_resi,
							 'nomor_pesanan'    => $data->nomor_pesanan,
							 'nama_kurir' 		=> $data->nama_kurir,
							 'status' 			=> $status 
			);

			$i++;
		}
		echo json_encode($rows);
	}

	public function ajax_list_gudang()
	{
		$i = 1;
		$rows = array();
		$get_all = $this->Resi_model->get_all_by_harian_gudang();
		foreach ($get_all as $data) {
			if ($data->status ==  0) {
	            $status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-times' style='margin-right:5px;'></i>Belum diproses</a>";
	          }elseif ($data->status ==  1) {
	            $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
	          }else{
	            $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
	          }
			$rows[] = array( 'no'				=> $i,
							 'tanggal' 			=> $data->tgl_resi, 
							 'nomor_resi' 		=> $data->nomor_resi,
							 'nomor_pesanan'    => $data->nomor_pesanan,
							 'nama_kurir' 		=> $data->nama_kurir,
							 'status' 			=> $status 
			);

			$i++;
		}
		echo json_encode($rows);
	}

	public function index()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';
	    $this->data['get_all_kurir'] = $this->Resi_model->get_all_kurir();
	    $this->data['get_all_pic']	= $this->Auth_model->get_pic_all_combobox_list();
	    $this->data['get_all_status'] = array( 'semua'	=> '- Semua Data-',
	    									   '0' 		=> 'Belum diproses',
	    									   '1' 		=> 'Sedang diproses',
	    									   '2' 		=> 'Sudah diproses',
	    									   '3' 		=> 'Retur'
	     								);
	    $this->data['pic'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'pic',
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
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    // $this->data['get_all'] = $this->Resi_model->get_all();

	    $this->load->view('back/resi/resi_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] 	= 'Scan Admin: '.$this->data['module'];
	    // $this->data['action']     = 'admin/resi/tambah_proses';
	    $this->data['get_all_pic']	= $this->Auth_model->get_pic_all_combobox();

	    $this->data['no_resi'] = [
	      'name'          => 'no_resi',
	      'id'            => 'no-resi',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'onchange'	      => 'cekResi()',
	    ];

	    $this->data['pic'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'pic',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/resi/resi_add', $this->data);
	}

	public function scan()
	{
		is_create();    

	    $this->data['page_title'] = 'Scan Gudang: '.$this->data['module'];
	    $this->data['get_cek'] = $this->Resi_model->get_data_cek();
	    // $this->data['action']     = 'admin/resi/tambah_proses';

	    $this->data['no_resi'] = [
	      'name'          => 'no_resi',
	      'id'            => 'no-resi',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'onchange'	  => 'cekResi()',
	    ];

	    $this->load->view('back/resi/resi_scan', $this->data);
	}

	function tambah_proses()
	{
		$resi 		= $this->input->post('resi');
		$pic 		= $this->input->post('pic');
		$cari_resi  = $this->Resi_model->get_penjualan_by_resi($resi);
		// echo json_encode($cari_resi);
		if (isset($cari_resi)) {
			$cek_resi = $this->Resi_model->get_by_resi($cari_resi->nomor_resi);
			if ($cek_resi->status == 1) {
				$pesan = "No Resi sedang diproses!";	
	        	$msg = array(	'validasi_dobel'	=> $pesan
	        			);
	        	echo json_encode($msg); 
			}elseif ($cek_resi->status == 2) {
				$pesan = "No Resi sudah diproses!";	
	        	$msg = array(	'validasi'	=> $pesan
	        			);
	        	echo json_encode($msg); 
			}elseif ($cek_resi->status == 3) {
				$pesan = "No Resi diretur!";	
	        	$msg = array(	'validasi'	=> $pesan
	        			);
	        	echo json_encode($msg); 
			}else{
				date_default_timezone_set("Asia/Jakarta");
				$now = date('Y-m-d H:i:s');
				$data = array(	'id_users' 		=> $this->session->userdata('id_users'), 
								'status' 		=> 1,
								'tgl_resi' 		=> $now 
							);
				$this->Resi_model->update_by_resi($resi, $data);

				$data_resi_access = array(	'nomor_resi'		=> $cari_resi->nomor_resi,
											'tgl_resi_access' 	=> $now,
											'created_by'		=> $this->session->userdata('id_users'),
											'handled_by'		=> $pic
									);
				$this->Resi_model->insert_resi_access($data_resi_access);

				write_log();

				$pesan = "No. Resi berhasil ditambah!";	
	        	$msg = array(	'sukses'	=> $pesan
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

	function scan_proses()
	{
		$resi 		= $this->input->post('resi');
		$cari_resi  = $this->Resi_model->get_by_resi($resi);
		// echo json_encode($cari_resi);
		if (isset($cari_resi)) {
			if ($cari_resi->status == 1) {
				// ================== DIGUNAKAN APABILA UNTUK MEMVALIDASI BEDA SCAN ========================
				// date_default_timezone_set("Asia/Jakarta");
				// $now = date('Y-m-d H:i:s');

				// $cek_resi_access = $this->Resi_model->get_resi_access_by_nomor_resi($cari_resi->nomor_resi);

				// if ($cek_resi_access->handled_by == $this->session->userdata('id_users')) {
				// 	$data = array(	'id_users' 		=> $this->session->userdata('id_users'), 
				// 					'status' 		=> 2,
				// 					'tgl_resi' 		=> $now  
				// 		);
				// 	$this->Resi_model->update_by_resi($cari_resi->nomor_resi,$data);

				// 	write_log();

				// 	$pesan = "No. Resi berhasil dicek!";	
		  //       	$msg = array(	'sukses'	=> $pesan
		  //       			);
		  //       	echo json_encode($msg);	
				// }else{
				// 	$pesan = "PIC No. Resi ini: ".$cek_resi_access->name.". Anda tidak berhak melakukan Scan Resi!";	
		  //       	$msg = array(	'validasi_check_pic'	=> $pesan
		  //       			);
		  //       	echo json_encode($msg); 
				// }

				// ================== DIGUNAKAN APABILA UNTUK TIDAK MEMVALIDASI BEDA SCAN ========================
				date_default_timezone_set("Asia/Jakarta");
				$now = date('Y-m-d H:i:s');

				$data = array(	'id_users' 		=> $this->session->userdata('id_users'), 
								'status' 		=> 2,
								'tgl_resi' 		=> $now  
					);
				$this->Resi_model->update_by_resi($cari_resi->nomor_resi,$data);

				write_log();

				$pesan = "No. Resi berhasil dicek!";	
	        	$msg = array(	'sukses'	=> $pesan
	        			);
	        	echo json_encode($msg);	
				
			}elseif ($cari_resi->status == 0){
				$pesan = "No Resi belum diproses!";	
	        	$msg = array(	'validasi'	=> $pesan
	        			);
	        	echo json_encode($msg); 
			}else{
				// ================== DIGUNAKAN APABILA UNTUK MEMVALIDASI BEDA SCAN ========================
				// $cek_resi_access = $this->Resi_model->get_resi_access_by_nomor_resi($cari_resi->nomor_resi);

				// if ($cek_resi_access->handled_by == $this->session->userdata('id_users')) {
				// 	$pesan = "No Resi sudah diproses!";	
		  //       	$msg = array(	'validasi_dobel'	=> $pesan
		  //       			);
		  //       	echo json_encode($msg); 
				// }else{
				// 	$pesan = "PIC No. Resi ini: ".$cek_resi_access->name.". Anda tidak berhak melakukan Scan Resi!";	
		  //       	$msg = array(	'validasi_check_pic'	=> $pesan
		  //       			);
		  //       	echo json_encode($msg); 
				// }

				// ================== DIGUNAKAN APABILA UNTUK TIDAK MEMVALIDASI BEDA SCAN ========================
				$pesan = "No Resi sudah diproses!";	
	        	$msg = array(	'validasi_dobel'	=> $pesan
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

	public function export_resi($kurir, $pic, $status, $periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Resi Per Tanggal ".$start." - ".$end."_".date("H_i_s");
		$data['resi'] = $this->Resi_model->get_datatable_all($kurir, $pic, $status, $start, $end);

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
		$sheet->setCellValue('K1', 'created_by');
		$sheet->setCellValue('L1', 'handled_by');
		$sheet->setCellValue('M1', 'status_resi');

		// set Row
        $rowCount = 2;
        // echo print_r($data['resi']);
        // echo $kurir;
        foreach ($data['resi'] as $list) {
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
            $sheet->SetCellValue('K' . $rowCount, $list->ra_created);
            $sheet->SetCellValue('L' . $rowCount, $list->handled_by);
            $sheet->SetCellValue('M' . $rowCount, $list->status);

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

	// public function tambah_proses()
	// {
	// 	$this->form_validation->set_rules('nama_resi', 'Nama Resi', 'is_unique[resi.nama_resi]|trim|required',
	// 		array(	'required' 		=> '%s harus diisi!',
	// 				'is_unique'		=> '<strong>'.$this->input->post('nama_satuan').'</strong> sudah ada. Buat %s baru',
	// 		)
	// 	);

	//     $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

	//     if($this->form_validation->run() === FALSE)
	//     {
	//       $this->tambah();
	//     }
	//     else
	//     {
	//       $data = array(
	//         'nama_resi'     => $this->input->post('nama_resi'),
	//       );

	//       $this->Resi_model->insert($data);

	//       write_log();

	//       $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	//       redirect('admin/resi');
	//     }
	// }

	// function hapus($id = '')
	// {
	// 	is_delete();

	// 	$delete = $this->Resi_model->get_by_id($id);

	// 	if($delete)
	// 	{
	// 	  $this->Resi_model->delete($id);

	// 	  write_log();

	// 	  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
	// 	  redirect('admin/resi');
	// 	}
	// 	else
	// 	{
	// 	  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
	// 	  redirect('admin/resi');
	// 	}
	// }

}

/* End of file Resi.php */
/* Location: ./application/controllers/admin/Resi.php */