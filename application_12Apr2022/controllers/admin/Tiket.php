<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tiket extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Tiket';

	    $this->load->model(array('Tiket_model', 'Keluar_model', 'Kategori_kasus_model', 'Level_kasus_model', 'Status_tiket_model'));

	    $this->data['company_data']    				= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    // $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['add_action'] = base_url('admin/tiket/tambah');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	// Datatable Server Side
	function get_data_tiket()
    {
        $list = $this->Tiket_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
        	$action = '<a href="'.base_url('admin/tiket/ubah/'.base64_encode($data->nomor_tiket)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/tiket/hapus/'.base64_encode($data->nomor_tiket)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->nomor_tiket.'">';


			if ($data->id_status_tiket ==  1) {
				$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Terbuka</a>";
			}elseif ($data->id_status_tiket ==  2) {
				$status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-times' style='margin-right:5px;'></i>Pending</a>";
			}elseif ($data->id_status_tiket ==  3){
	            $status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-minus-circle' style='margin-right:5px;'></i>Tertutup</a>";
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
					'<tr>'.
			            '<td>Judul Kasus</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$data->judul_kasus.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>Pesan Tiket</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$data->pesan_tiket.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>Gambar</td>'.
			            '<td width="1%">:</td>'.
			            '<td><a href="'.base_url('admin/tiket/img_blob/'.base64_encode($data->nomor_tiket)).'" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-search" style="margin-right: 5px;"></i> Lihat Gambar</a></td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>PIC</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$data->nama_hd.'</td>'.
			        '</tr>'.
			        '<tr>'.
			            '<td>Dibuat oleh</td>'.
			            '<td width="1%">:</td>'.
			            '<td>'.$data->nama_cr.'</td>'.
			        '</tr>';

			        if ($data->tanggal_tiket_selesai != NULL) {
						$detail .= '<tr>'.
							            '<td width="15%">Tanggal Tiket Selesai</td>'.
							            '<td width="1%">:</td>'.
							            '<td>'.$data->tanggal_tiket_selesai.'</td>'.
							        '</tr>';
			        }

			$detail .=	'</table>'.
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
            $row['nomor_tiket'] = $data->nomor_tiket;
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y H:i:s', strtotime($data->tanggal_tiket));
            $row['nama_kategori_kasus'] = $data->nama_kategori_kasus;
            $row['nama_level_kasus'] = $data->nama_level_kasus;
            $row['nama_status_tiket'] = $status;
            $row['detail'] = $detail;
            $row['action'] = $action;
            $row['select'] = $select;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Tiket_model->count_all(),
            "recordsFiltered" => $this->Tiket_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function dasbor_list_count(){
		$kasus 		= $this->input->post('kasus');
		$level 		= $this->input->post('level');
		$status 	= $this->input->post('status');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Tiket_model->get_dasbor_list($kasus, $level, $status, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
			        		'terbuka'	=> $data->terbuka,
			        		'pending'	=> $data->pending,
			        		'ditutup'	=> $data->tertutup
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

	public function index()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';
	    $this->data['get_all_kasus'] = $this->Tiket_model->get_kasus_all_combobox();
	    $this->data['get_all_level'] = $this->Tiket_model->get_level_all_combobox();
	    $this->data['get_all_status'] = $this->Tiket_model->get_status_all_combobox();

	    $this->data['kasus'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kasus',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['level'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'level',
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

	    $this->load->view('back/tiket/tiket_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Scan Resi atau Nomor Pesanan: '.$this->data['module'];
	    $this->data['get_all_kasus'] = $this->Kategori_kasus_model->get_all_combobox();
	    $this->data['get_all_level'] = $this->Level_kasus_model->get_all_combobox();
	    $this->data['get_all_users'] = $this->Tiket_model->get_users_all_combobox();
	    
	    $this->data['judul'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'judul',
	      	'required'      => '',
	      	'style' 		=> 'width:100%',
	      	'max'			=> 255
	    ];

	    $this->data['kasus'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kasus',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['level'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'level',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['users'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'users',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    // $this->data['action']     = 'admin/resi/tambah_proses';

	    $this->data['nomor'] = [
	      'name'          => 'nomor',
	      'id'            => 'nomor',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'onchange'	  => 'cekNomor()',
	    ];

	    $this->load->view('back/tiket/tiket_scan', $this->data);
	}

	public function scan_proses()
	{
		$nomor 			= $this->input->post('nomor');
		$cek_nomor		= $this->Tiket_model->get_cek_resi_all_by_nomor_pesanan_resi($nomor);
		if (isset($cek_nomor)) {
			$i = 1;
			$rows = array();
			$produk_pesanan = $this->Keluar_model->get_detail_by_id($nomor);
			foreach ($produk_pesanan as $data) {
				$rows[] = array( 'no'				=> $i,
								 'nama_produk' 		=> $data->nama_produk, 
								 'qty' 				=> $data->qty
				);

				$i++;
			}

			$pesan = "No. Resi atau No. Pesanan ditemukan!";	
        	$msg = array(	'sukses'			=> $pesan,
        					'nomor_pesanan' 	=> $cek_nomor->nomor_pesanan,
        					'nomor_resi' 		=> $cek_nomor->nomor_resi,
        					'nama_kurir' 		=> $cek_nomor->nama_kurir,
        					'nama_toko' 		=> $cek_nomor->nama_toko,
        					'nama_penerima' 	=> $cek_nomor->nama_penerima,
        					'alamat_penerima' 	=> $cek_nomor->alamat_penerima,
        					'kabupaten'		 	=> $cek_nomor->kabupaten,
        					'provinsi'		 	=> $cek_nomor->provinsi,
        					'hp_penerima'	 	=> $cek_nomor->hp_penerima,
        					'table' 			=> $rows
        			);
        	echo json_encode($msg); 
		}else{
			$pesan = "No Resi atau No. Pesanan tidak ditemukan!";	
        	$msg = array(	'validasi'	=> $pesan
        			);
        	echo json_encode($msg); 
		}
	}

	public function tiket_tambah_proses()
	{
		// Ambil Data
		$i = $this->input;

		$config['upload_path']          = './uploads/gambar_kasus/';
		$config['allowed_types']        = 'jpg|png|jpeg';
		$config['file_name']			= 'Gambar_Kasus_'.date('Y_m_d_').time();
		$config['max_size']             = 2000;
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('photo'))
		{
				$pesan = strip_tags($this->upload->display_errors());
				$msg = array(	'validasi'	=> $pesan
		    			);
		    	echo json_encode($msg);
		}else{
			// Upload Gambar
			date_default_timezone_set("Asia/Jakarta");
			$now = date('Y-m-d H:i:s');
			$image_data = $this->upload->data();
			$imgdata = file_get_contents($image_data['full_path']);
			$file_encode=base64_encode($imgdata);
			// $data['tipe_berkas'] = $this->upload->data('file_type');
			// $data['bukti_berkas'] = $file_encode;
			$nama_berkas =  'Gambar_Kasus_'.date('Y_m_d_').time().$image_data['file_ext'];

			// Nomor Tiket
			$date= date("Y-m-d");
			$tahun = substr($date, 2, 2);
			$tahun_full = substr($date, 0, 4);
			$bulan = substr($date, 5, 2);
			$tanggal = substr($date, 8, 2);
			$teks = "TIK/".$tahun_full."/";
			$ambil_nomor = $this->Tiket_model->cari_nomor($teks);
			if (isset($ambil_nomor)) {
				$ambil_tanggal = substr($ambil_nomor->nomor_tiket, 9, 2);
				$ambil_bulan = substr($ambil_nomor->nomor_tiket, 11, 2);
				$ambil_tahun = substr($ambil_nomor->nomor_tiket, 13, 2);
				$ambil_tahun_full = substr($ambil_nomor->nomor_tiket, 4, 4);
				$ambil_no = (int) substr($ambil_nomor->nomor_tiket, 16, 4);
				// PERTAHUN
				if ($tahun_full == $ambil_tahun_full) {
					$ambil_no++;	
					$nomor_tiket = "TIK/".$tahun_full."/".$tanggal.$bulan.$tahun."/".sprintf("%04s", $ambil_no);
				}else{
					$nomor_tiket = "TIK/".$tahun_full."/".$tanggal.$bulan.$tahun."/"."0001";
				}
			}else{
				$nomor_tiket = "TIK/".$tahun_full."/".$tanggal.$bulan.$tahun."/"."0001";
			}

			// Simpan Database
			$nomor_pesanan 	= $i->post('nomor_pesanan');
			$judul 			= $i->post('judul');
			$pesan 			= $i->post('pesan');
			$kasus 			= $i->post('kasus');
			$level 			= $i->post('level');
			$users 			= $i->post('users');
			$created		= $i->post('created');

	        $data = array(	'nomor_tiket'			=> $nomor_tiket,	
	        				'nomor_pesanan'			=> $nomor_pesanan,
							'id_kategori_kasus' 	=> $kasus,
							'id_level_kasus'		=> $level,
							'id_status_tiket'		=> 1,
							'tanggal_tiket' 		=> $now,
							'tanggal_tiket_selesai' => NULL, 	
							'judul_kasus' 			=> $judul, 	
							'pesan_tiket' 			=> $pesan, 	
							'gambar' 				=> $file_encode, 	
							'nama_gambar' 			=> $nama_berkas, 	
							'tipe_gambar' 			=> $this->upload->data('file_type'), 	
							'created_by' 			=> $created, 	
							'handled_by' 			=> $users, 	
					);

	        $this->Tiket_model->insert($data);

	      	write_log();

	        $pesan = "Tiket Kasus telah dibuat!";	
	    	$msg = array(	'sukses'	=> $pesan,
	    			);
	    	echo json_encode($msg);
		}
	}

	function ubah($id='')
	{
		is_update();

		$this->data['tiket']   		 	= $this->Tiket_model->get_by_id(base64_decode($id));
		$this->data['get_all_kasus'] 	= $this->Kategori_kasus_model->get_all_combobox();
	    $this->data['get_all_level'] 	= $this->Level_kasus_model->get_all_combobox();
	    $this->data['get_all_status'] 	= $this->Status_tiket_model->get_all_combobox();
	    $this->data['get_all_users'] 	= $this->Tiket_model->get_users_all_combobox();

		if($this->data['tiket'])
	    {
			$this->data['get_pesanan']			= $this->Tiket_model->get_cek_resi_all_by_nomor_pesanan_resi($this->data['tiket']->nomor_pesanan);
	    	$this->data['get_produk_pesanan'] 	= $this->Keluar_model->get_detail_by_id($this->data['tiket']->nomor_pesanan);
	    	$this->data['page_title'] 			= 'Update Data '.$this->data['module'].': '.$this->data['tiket']->nomor_tiket;
		  
	      	$this->data['id'] = [	
			  	'id' 			=> 'nomor-tiket', 
		        'type'          => 'hidden',
		    ];	

		    $this->data['judul'] = [
		    	'class'         => 'form-control',
		    	'id'            => 'judul',
		      	'required'      => '',
		      	'style' 		=> 'width:100%',
		      	'max'			=> 255
		    ];

		    $this->data['kasus'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'kasus',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['level'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'level',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['status'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'status',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['users'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'users',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

	      	$this->load->view('back/tiket/tiket_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/tiket');
	    }
	}

	function tiket_ubah_proses()
	{
		if ($this->input->post('photo') != NULL) {
			$i = $this->input;
			$nomor_tiket 	= $i->post('nomor_tiket');
			$judul 			= $i->post('judul');
			$pesan 			= $i->post('pesan');
			$kasus 			= $i->post('kasus');
			$status 		= $i->post('status');
			$level 			= $i->post('level');
			$users 			= $i->post('users');

			$cariTiket = $this->Tiket_model->get_by_id($nomor_tiket);
			if(isset($cariTiket))
			{
				date_default_timezone_set("Asia/Jakarta");
				$now = date('Y-m-d H:i:s');
				if ($status == 3) {
					$tgl_selesai = $now;
				}elseif ($status == 1 || $status == 2) {
					$tgl_selesai = NULL;
				}
				// Ubah Database
				$data = array(	'id_kategori_kasus' 	=> $kasus,
								'id_level_kasus'		=> $level,
								'id_status_tiket'		=> $status,
								'tanggal_tiket_selesai' => $tgl_selesai, 	
								'judul_kasus' 			=> $judul, 	
								'pesan_tiket' 			=> $pesan, 		
								'handled_by' 			=> $users, 	
						);

		        $this->Tiket_model->update($cariTiket->nomor_tiket, $data);

		      	write_log();

		        $pesan = "Tiket Kasus telah diubah!";	
		    	$msg = array(	'sukses'	=> $pesan,
		    			);
		    	echo json_encode($msg);
			}else{
				$pesan = "Tiket Kasus tidak ditemukan!";	
		    	$msg = array(	'none'	=> $pesan,
		    			);
		    	echo json_encode($msg);
			}
		}else{
			$i = $this->input;
			$nomor_tiket 	= $i->post('nomor_tiket');
			$judul 			= $i->post('judul');
			$pesan 			= $i->post('pesan');
			$kasus 			= $i->post('kasus');
			$status 		= $i->post('status');
			$level 			= $i->post('level');
			$users 			= $i->post('users');

			$cariTiket = $this->Tiket_model->get_by_id($nomor_tiket);
			if(isset($cariTiket))
			{
			  	// Ambil Data
				$i = $this->input;

				$config['upload_path']          = './uploads/gambar_kasus/';
				$config['allowed_types']        = 'jpg|png|jpeg';
				$config['file_name']			= 'Gambar_Kasus_'.date('Y_m_d_').time();
				$config['max_size']             = 2000;
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('photo'))
				{
						$pesan = strip_tags($this->upload->display_errors());
						$msg = array(	'validasi'	=> $pesan
				    			);
				    	echo json_encode($msg);
				}else{
					unlink("./uploads/gambar_kasus/".$cariTiket->nama_gambar);

					// Upload Gambar
					date_default_timezone_set("Asia/Jakarta");
					$now = date('Y-m-d H:i:s');
					$image_data = $this->upload->data();
					$imgdata = file_get_contents($image_data['full_path']);
					$file_encode=base64_encode($imgdata);
					// $data['tipe_berkas'] = $this->upload->data('file_type');
					// $data['bukti_berkas'] = $file_encode;
					$nama_berkas =  'Gambar_Kasus_'.date('Y_m_d_').time().$image_data['file_ext'];

					if ($status == 3) {
						$tgl_selesai = $now;
					}elseif ($status == 1 || $status == 2) {
						$tgl_selesai = NULL;
					}
					// Ubah Database
					$data = array(	'id_kategori_kasus' 	=> $kasus,
									'id_level_kasus'		=> $level,
									'id_status_tiket'		=> $status,
									'tanggal_tiket_selesai' => $tgl_selesai, 	
									'judul_kasus' 			=> $judul, 	
									'pesan_tiket' 			=> $pesan, 	
									'gambar' 				=> $file_encode, 	
									'nama_gambar' 			=> $nama_berkas, 	
									'tipe_gambar' 			=> $this->upload->data('file_type'), 		
									'handled_by' 			=> $users, 	
							);

			        $this->Tiket_model->update($cariTiket->nomor_tiket, $data);

			      	write_log();

			        $pesan = "Tiket Kasus telah diubah!";	
			    	$msg = array(	'sukses'	=> $pesan,
			    			);
			    	echo json_encode($msg);
				}
			}
			else
			{
			  	$pesan = "Tiket Kasus tidak ditemukan!";	
		    	$msg = array(	'none'	=> $pesan,
		    			);
		    	echo json_encode($msg);
			}
		}
	}

	public function img_blob($id)
	{
		$blob = $this->Tiket_model->get_by_id(base64_decode($id));

		echo "<img src='data:".$blob->tipe_gambar.";base64,".$blob->gambar."'></td>";
	}

	function hapus($id)
	{
		is_delete();

		$cariTiket = $this->Tiket_model->get_by_id(base64_decode($id));
		if(isset($cariTiket))
		{
		  unlink("./uploads/gambar_kasus/".$cariTiket->nama_gambar);	

		  $this->Tiket_model->delete($cariTiket->nomor_tiket);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/tiket');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/tiket');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$nomor_tiket = $this->input->post('ids');
		// echo $produk;

		$cek_nomor = $this->Tiket_model->get_all_by_id_in($nomor_tiket);

		if (count($cek_nomor) > 0) {
			foreach ($cek_nomor as $val_nomor) {
				unlink("./uploads/gambar_kasus/".$val_nomor->nama_gambar);	

				$this->Tiket_model->delete($val_nomor->nomor_tiket);

				write_log();
			}

			$pesan = "Berhasil dihapus!";	
	    	$msg = array(	'sukses'	=> $pesan
	    			);
	    	echo json_encode($msg);
		}else{
			$pesan = "Tiket Kasus tidak ditemukan!";	
	    	$msg = array(	'none'	=> $pesan,
	    			);
	    	echo json_encode($msg);
		}
	}

}

/* End of file Tiket.php */
/* Location: ./application/controllers/admin/Tiket.php */