<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resi extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Resi';

	    $this->load->model(array('Resi_model', 'Keluar_model'));

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
		$kurir 		= $this->input->post('kurir');
		$status 	= $this->input->post('status');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Resi_model->get_dasbor_list($kurir, $status, $start, $end);
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
							 'tanggal' 			=> date('d-m-Y', strtotime($data->tgl_resi)), 
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
	          	$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-minus-circle' style='margin-right:5px;'></i>Gagal diproses</a>";
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

            $row = array();
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_resi));
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
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
	          	$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-minus-circle' style='margin-right:5px;'></i>Gagal diproses</a>";
	          }

            $row = array();
            $row['no'] = $i;
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_resi));
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
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
	          	$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-minus-circle' style='margin-right:5px;'></i>Gagal diproses</a>";
	          }
	          
            $row = array();
            $row['no'] = $i;
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_resi));
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
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
	    $this->data['get_all_status'] = array( 'semua'	=> '- Semua Data-',
	    									   '0' 		=> 'Belum diproses',
	    									   '1' 		=> 'Sedang diproses',
	    									   '2' 		=> 'Sudah diproses',
	    									   '3' 		=> 'Gagal diproses'
	     								);

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

	    $this->data['page_title'] = 'Scan Admin: '.$this->data['module'];
	    // $this->data['action']     = 'admin/resi/tambah_proses';

	    $this->data['no_resi'] = [
	      'name'          => 'no_resi',
	      'id'            => 'no-resi',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'onchange'	      => 'cekResi()',
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
		$cari_resi  = $this->Resi_model->get_penjualan_by_resi($resi);
		// echo json_encode($cari_resi);
		if (isset($cari_resi)) {
			$cek_resi = $this->Resi_model->get_by_resi($cari_resi->nomor_resi);
			if ($cek_resi->status == 1) {
				$pesan = "No Resi sedang diproses!";	
	        	$msg = array(	'validasi'	=> $pesan
	        			);
	        	echo json_encode($msg); 
			}elseif ($cek_resi->status == 2) {
				$pesan = "No Resi sudah diproses!";	
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
				$pesan = "No Resi sudah diproses!";	
	        	$msg = array(	'validasi'	=> $pesan
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