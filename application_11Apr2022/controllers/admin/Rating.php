<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rating extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Rating Pesanan';

	    $this->load->model(array('Rating_model', 'Keluar_model', 'Kategori_rating_model'));

	    $this->data['company_data']    				= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    // $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['add_action'] = base_url('admin/rating/tambah');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	// Datatable Server Side
	function get_data_rating()
    {
        $list = $this->Rating_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
        	$jumlah_kategori = 0;
        	$jumlah_rating = 0;
        	$action = '<a href="'.base_url('admin/rating/ubah/'.$data->id_rating).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/rating/hapus/'.$data->id_rating).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->id_rating.'">';

			$get_penjualan = $this->Keluar_model->get_all_by_id($data->nomor_pesanan);
			$get_detail_penjualan = $this->Keluar_model->get_all_detail_by_id($data->nomor_pesanan);
			$get_detail_rating = $this->Rating_model->get_detail_by_id($data->id_rating);
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
			            '<td>Gambar</td>'.
			            '<td width="1%">:</td>'.
			            '<td><a href="'.base_url('admin/rating/img_blob/'.$data->id_rating).'" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-search" style="margin-right: 5px;"></i> Lihat Gambar</a></td>'.
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

			$detail .=  '<tr align="center">'.
				            '<td>Rating</td>'.
				            '<td colspan="2">Kategori Rating</td>'.
				        '</tr>';

			foreach ($get_detail_rating as $val_detail_rating) {
				$fix_rating = '';
				for ($i = 1; $i <= $val_detail_rating->rating; $i++) {
					$fix_rating .= "<i class='fa fa-star' style='margin-right:5px;color:#FFD700'></i>";
					$jumlah_rating++;
				}

				$detail .= '<tr align="center">'.
			                '<td>'.$fix_rating.'</td>'.
			                '<td colspan="2">'.$val_detail_rating->nama_kategori_rating.'</td>'.
			            '</tr>';

			    $jumlah_kategori++;
			}
			
			$detail .= '</table>';


			$detail	.= '<hr width="100%">'.
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

			$rating_dibagi = (int)$jumlah_rating / (int)$jumlah_kategori;
			$isi_rating = '';
			for ($i = 1; $i <= $rating_dibagi; $i++) {
				$isi_rating .= "<i class='fa fa-star' style='margin-right:5px;color:#FFD700'></i>";
			}

            $row = array();
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y H:i:s', strtotime($data->tanggal_rating));
            $row['rating'] = $isi_rating;
            $row['detail'] = $detail;
            $row['action'] = $action;
            $row['select'] = $select;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Rating_model->count_all(),
            "recordsFiltered" => $this->Rating_model->count_filtered(),
            "data" => $dataJSON
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_mean()
    {
        $list = $this->Rating_model->get_datatables_mean();
        $dataJSON = array();
        foreach ($list as $data) {
        	$row = array();
            $row['nama_kategori_rating'] = $data->nama_kategori_rating;
            $row['jumlah'] = $data->jumlah;
            $row['avg'] = number_format($data->avg,1) . " / 5 <i class='fa fa-star' style='margin-right:5px;color:#FFD700'></i>";
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Rating_model->count_all_mean(),
            "recordsFiltered" => $this->Rating_model->count_filtered_mean(),
            "data" => $dataJSON
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function dasbor_list_count(){
		$kategori 	= $this->input->post('kategori');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Rating_model->get_dasbor_list($kategori, $start, $end);
    	if (count($data) >= 0) {	
    		$rating = 0;
    		foreach ($data as $val_data) {
				$rating += (int)$val_data->rating;
			}

			$mean = (count($data)!=0) ? $rating / count($data) : 0; 
        	$msg = array(	'total'		=> count($data),
        					'mean'		=> $mean
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
	    $this->data['get_all_kategori'] = $this->Rating_model->get_kategori_all_combobox();

	    $this->data['kategori'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kategori',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/rating/rating_list', $this->data);
	}

	public function tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Scan Resi atau Nomor Pesanan: '.$this->data['module'];
	    $this->data['get_all_kategori'] = $this->Kategori_rating_model->get_all_combobox_without_pilih();
	    $this->data['get_all_users'] = $this->Rating_model->get_users_all_combobox();	    

	    // $this->data['kategori'] = [
	    // 	'class'         => 'form-control select2bs4',
	    // 	'id'            => 'kategori',
	    //   	'required'      => '',
	    //   	'style' 		=> 'width:100%'
	    // ];

	    $this->data['users'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'users',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kategori_rating_id'] = [
			'name'          => 'kategori_rating_id[]',
			'id'            => 'kategori-rating-id',
			'class'         => 'form-control select2',
			'style'		  	=> 'width:100%',
			'multiple'      => '',
		];

	    $this->data['nomor'] = [
	      'name'          => 'nomor',
	      'id'            => 'nomor',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'onchange'	  => 'cekNomor()',
	    ];

	    $this->load->view('back/rating/rating_scan', $this->data);
	}

	public function scan_proses()
	{
		$nomor 			= $this->input->post('nomor');
		$cek_nomor		= $this->Rating_model->get_cek_resi_all_by_nomor_pesanan_resi($nomor);
		if (isset($cek_nomor)) {
			$cek_rating = $this->Rating_model->get_by_nomor_pesanan($cek_nomor->nomor_pesanan);
			if (isset($cek_rating)) {
				$pesan = "No Resi atau No. Pesanan sudah ada di Rating Pesanan!";	
	        	$msg = array(	'exist'		=> $pesan,
	        					'id_rating'	=> $cek_rating->id_rating
	        			);
	        	echo json_encode($msg); 
			}else{
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
			}
		}else{
			$pesan = "No Resi atau No. Pesanan tidak ditemukan!";	
        	$msg = array(	'validasi'	=> $pesan
        			);
        	echo json_encode($msg); 
		}
	}

	function rating_tambah_proses()
	{
		// Ambil Data
		$i = $this->input;

		$config['upload_path']          = './uploads/gambar_rating/';
		$config['allowed_types']        = 'jpg|png|jpeg';
		$config['file_name']			= 'Gambar_Rating_'.date('Y_m_d_').time();
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
			$nama_berkas =  'Gambar_Rating_'.date('Y_m_d_').time().$image_data['file_ext'];

			// Simpan Database
			$nomor_pesanan 	= $i->post('nomor_pesanan');
			$kategori 		= explode(',', $i->post('kategori'));
			$rating 		= $i->post('rating');
			$users 			= $i->post('users');
			$created 		= $i->post('created');

			$data = array(	'nomor_pesanan'			=> $nomor_pesanan,
							'tanggal_rating' 		=> $now,
							'gambar' 				=> $file_encode, 	
							'nama_gambar' 			=> $nama_berkas, 	
							'tipe_gambar' 			=> $this->upload->data('file_type'), 	
							'created_by' 			=> $created, 	
							'handled_by' 			=> $users, 	
					);

	        $this->Rating_model->insert($data);

	      	write_log();

	      	$last_rating = $this->Rating_model->get_by_nomor_pesanan($nomor_pesanan);

	      	// Masukan ke Detail Rating
			foreach ($kategori as $val_kategori) {
				$data = array(	'id_rating'				=> $last_rating->id_rating,
								'id_kategori_rating' 	=> $val_kategori,
								'rating'				=> $rating,
				);

		        $this->Rating_model->insert_detail($data);

		      	write_log();
			}

	        $pesan = "Rating Pesanan telah dibuat!";	
	    	$msg = array(	'sukses'	=> $pesan,
	    			);
	    	echo json_encode($msg);
		}
	}

	function ubah($id='')
	{
		is_update();

		$this->data['rating']   		= $this->Rating_model->get_by_id($id);
	    $this->data['get_all_kategori'] = $this->Kategori_rating_model->get_all_combobox_without_pilih();
	    $this->data['get_all_users'] 	= $this->Rating_model->get_users_all_combobox();	

		if($this->data['rating'])
	    {
			$this->data['get_pesanan']			= $this->Rating_model->get_cek_resi_all_by_nomor_pesanan_resi($this->data['rating']->nomor_pesanan);
	    	$this->data['get_produk_pesanan'] 	= $this->Keluar_model->get_detail_by_id($this->data['rating']->nomor_pesanan);
	    	$this->data['page_title'] 			= 'Update Data '.$this->data['module'].': '.$this->data['rating']->nomor_pesanan;

	    	$get_kategori_rating	= $this->Rating_model->get_detail_by_id($this->data['rating']->id_rating);
	    	$tot_data_kategori	= 0;
	    	$rating = 0;
	    	foreach ($get_kategori_rating as $val_kategori_rating) {
	    		$this->data['get_id_kategori_rating'][] = $val_kategori_rating->id_kategori_rating;
	    		$rating += $val_kategori_rating->rating;
	    		$tot_data_kategori++;
	    	}

	    	$this->data['fix_rating'] = (int)$rating / (int)$tot_data_kategori;
		  
	      	$this->data['id'] = [	
			  	'id' 			=> 'id', 
		        'type'          => 'hidden',
		    ];	

		    $this->data['users'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'users',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['kategori_rating_id'] = [
				'name'          => 'kategori_rating_id[]',
				'id'            => 'kategori-rating-id',
				'class'         => 'form-control select2',
				'style'		  	=> 'width:100%',
				'multiple'      => '',
			];

	      	$this->load->view('back/rating/rating_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/tiket');
	    }
	}

	function rating_ubah_proses()
	{
		if ($this->input->post('photo') != NULL) {
			$i = $this->input;
			$id_rating 		= $i->post('id_rating');
			$rating 		= $i->post('rating');
			$users 			= $i->post('users');
			$kategori		= explode(',', $i->post('kategori'));

			$cariRating = $this->Rating_model->get_by_id($id_rating);
			if(isset($cariRating))
			{
				// Ubah Database
				$data = array(	'handled_by' 			=> $users, 	
						);

		        $this->Rating_model->update($cariRating->id_rating, $data);

		      	write_log();

				// Hapus Detail Rating berdasarkan ID Rating
				$this->Rating_model->delete_detail_by_id($cariRating->id_rating);

		      	// Masukan ke Detail Rating
				foreach ($kategori as $val_kategori) {
					$data = array(	'id_rating'				=> $cariRating->id_rating,
									'id_kategori_rating' 	=> $val_kategori,
									'rating'				=> $rating,
					);

			        $this->Rating_model->insert_detail($data);

			      	write_log();
				}

		        $pesan = "Rating Pesanan telah diubah!";	
		    	$msg = array(	'sukses'	=> $pesan,
		    			);
		    	echo json_encode($msg);
			}else{
				$pesan = "Rating Pesanan tidak ditemukan!";	
		    	$msg = array(	'none'	=> $pesan,
		    			);
		    	echo json_encode($msg);
			}
		}else{
			$i = $this->input;
			$id_rating 		= $i->post('id_rating');
			$rating 		= $i->post('rating');
			$users 			= $i->post('users');
			$kategori		= explode(',', $i->post('kategori'));

			$cariRating = $this->Rating_model->get_by_id($id_rating);
			if(isset($cariRating))
			{
			  	// Ambil Data
				$i = $this->input;

				$config['upload_path']          = './uploads/gambar_rating/';
				$config['allowed_types']        = 'jpg|png|jpeg';
				$config['file_name']			= 'Gambar_Rating_'.date('Y_m_d_').time();
				$config['max_size']             = 2000;
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('photo'))
				{
						$pesan = strip_tags($this->upload->display_errors());
						$msg = array(	'validasi'	=> $pesan
				    			);
				    	echo json_encode($msg);
				}else{
					unlink("./uploads/gambar_rating/".$cariRating->nama_gambar);

					// Upload Gambar
					date_default_timezone_set("Asia/Jakarta");
					$now = date('Y-m-d H:i:s');
					$image_data = $this->upload->data();
					$imgdata = file_get_contents($image_data['full_path']);
					$file_encode=base64_encode($imgdata);
					// $data['tipe_berkas'] = $this->upload->data('file_type');
					// $data['bukti_berkas'] = $file_encode;
					$nama_berkas =  'Gambar_Rating_'.date('Y_m_d_').time().$image_data['file_ext'];

					
					// Ubah Database
					$data = array(	'gambar' 				=> $file_encode, 	
									'nama_gambar' 			=> $nama_berkas, 	
									'tipe_gambar' 			=> $this->upload->data('file_type'), 
									'handled_by' 			=> $users, 	
							);

			        $this->Rating_model->update($cariRating->id_rating, $data);

			      	write_log();

					// Hapus Detail Rating berdasarkan ID Rating
					$this->Rating_model->delete_detail_by_id($cariRating->id_rating);

			      	// Masukan ke Detail Rating
					foreach ($kategori as $val_kategori) {
						$data = array(	'id_rating'				=> $cariRating->id_rating,
										'id_kategori_rating' 	=> $val_kategori,
										'rating'				=> $rating,
						);

				        $this->Rating_model->insert_detail($data);

				      	write_log();
					}

			        $pesan = "Rating Pesanan telah diubah!";	
			    	$msg = array(	'sukses'	=> $pesan,
			    			);
			    	echo json_encode($msg);
				}
			}
			else
			{
			  	$pesan = "Rating Pesanan tidak ditemukan!";	
		    	$msg = array(	'none'	=> $pesan,
		    			);
		    	echo json_encode($msg);
			}
		}
	}

	public function img_blob($id)
	{
		$blob = $this->Rating_model->get_by_id($id);

		echo "<img src='data:".$blob->tipe_gambar.";base64,".$blob->gambar."'></td>";
	}

	function hapus($id)
	{
		is_delete();

		$cariRating = $this->Rating_model->get_by_id($id);
		if(isset($cariRating))
		{
		  unlink("./uploads/gambar_rating/".$cariRating->nama_gambar);	

		  $this->Rating_model->delete_detail_by_id($cariRating->id_rating);

		  $this->Rating_model->delete($cariRating->id_rating);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/rating');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/rating');
		}
	}

	function hapus_dipilih()
	{
		is_delete();

		$id_rating = $this->input->post('ids');
		// echo $produk;

		$cek_rating = $this->Rating_model->get_all_by_id_in($id_rating);

		if (count($cek_rating) > 0) {
			foreach ($cek_rating as $val_rating) {
				unlink("./uploads/gambar_rating/".$val_rating->nama_gambar);	

		  		$this->Rating_model->delete_detail_by_id($val_rating->id_rating);

				$this->Rating_model->delete($val_rating->id_rating);

				write_log();
			}

			$pesan = "Berhasil dihapus!";	
	    	$msg = array(	'sukses'	=> $pesan
	    			);
	    	echo json_encode($msg);
		}else{
			$pesan = "Rating Pesanan tidak ditemukan!";	
	    	$msg = array(	'none'	=> $pesan,
	    			);
	    	echo json_encode($msg);
		}
	}

}

/* End of file Rating.php */
/* Location: ./application/controllers/admin/Rating.php */