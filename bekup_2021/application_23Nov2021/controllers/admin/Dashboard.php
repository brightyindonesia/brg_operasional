<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array('Dashboard_model'));
		$this->load->helper('auth');

		is_login();

		$this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    	$this->data['skins_template']     			= $this->Template_model->skins();
	}

	public function index()
	{
		$this->data['page_title'] = 'Dashboard';

		$this->data['get_total_menu']     		= $this->Menu_model->total_rows();
		$this->data['get_total_submenu']     	= $this->Submenu_model->total_rows();				
		$this->data['get_total_user']     		= $this->Auth_model->total_rows();
		$this->data['get_total_usertype']     	= $this->Usertype_model->total_rows();

		$this->load->view('back/dashboard/body', $this->data);
	}

	// Non Retur

	public function export_repeat($periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Repeat Order Per Tanggal ".$start." - ".$end."_".date("H_i_s");
		$data['repeat']	= $this->Dashboard_model->get_customer_repeat($start, $end);

		// echo print_r($repeat);
		$this->load->view('back/dashboard/repeat_export', $data);

		// $pesan = "Data Repeat Order berhasil diexport!";	
  //   	$msg = array(	'sukses'	=> $pesan
  //   			);
  //   	echo json_encode($msg);
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
	
	public function ajax_dasbor_total()
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
		$get_income	= $this->Dashboard_model->get_pendapat_dasbor($start, $end);
		$get_income_past = $this->Dashboard_model->get_pendapat_dasbor($start_past, $end_past);

		// Get Pesanan
		$get_pesan = $this->Dashboard_model->get_total_pesanan_by_periodik($start, $end);
		$get_pesan_past = $this->Dashboard_model->get_total_pesanan_by_periodik($start_past, $end_past);

		// Mencari nilai MAX dari 2 variabel
		$max_diterima = max(array($get_income->diterima, $get_income_past->diterima));
		$max_income   = max(array($get_income->fix, $get_income_past->fix));
		$max_laba     = max(array($get_income->total, $get_income_past->total));
		$max_ongkir   = max(array($get_income->tot_ongkir, $get_income_past->tot_ongkir));
		$max_pesan	  = max(array($get_pesan->jumlah_tanggal, $get_pesan_past->jumlah_tanggal));

		if ($max_diterima == NULL && $max_income == NULL && $max_laba == NULL && $max_ongkir == NULL && $max_pesan == 0) {
			$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PESANAN</span>';

			$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL DITERIMA</span>';

            $html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL OMSET</span>';
            
            $html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL MARGIN</span>';
               	 			 
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

			// INCOME
			if ($max_income == 0) {
				$html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL OMSET</span>';
			}else{
				$persen_income		= (($get_income->fix - $get_income_past->fix) / $max_income) * 100;
				$sisa_income		= $get_income->fix - $get_income_past->fix;

				if ($persen_income == 0 && $sisa_income == 0) {
					$html_income = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL OMSET</span>';
				}else if ($persen_income < 0 && $sisa_income < 0) {
					$html_income = '<span class="description-percentage text-red"> -'.rupiah($sisa_income * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_income * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL OMSET</span>';
				}else if ($persen_income > 0 && $sisa_income > 0) {
					$html_income = '<span class="description-percentage text-green"> +'.rupiah($sisa_income).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_income).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL OMSET</span>';
				}
			}

			// LABA
			if ($max_laba == 0) {
				$html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL MARGIN</span>';
			}else{
				$persen_laba	= (($get_income->total - $get_income_past->total) / $max_laba) * 100;
				$sisa_laba		= $get_income->total - $get_income_past->total;

				if ($persen_laba == 0 && $sisa_laba == 0) {
					$html_laba = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
				}else if ($persen_laba < 0 && $sisa_laba < 0) {
					$html_laba = '<span class="description-percentage text-red"> -'.rupiah($sisa_laba * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_laba * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
				}else if ($persen_laba > 0 && $sisa_laba > 0) {
					$html_laba = '<span class="description-percentage text-green"> +'.rupiah($sisa_laba).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_laba).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
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
					$html_ongkir = '<span class="description-percentage text-red"> -'.rupiah($sisa_ongkir * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_ongkir * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}else if ($persen_ongkir > 0 && $sisa_ongkir > 0) {
					$html_ongkir = '<span class="description-percentage text-green"> +'.rupiah($sisa_ongkir).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_ongkir).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}
			}
			
			// // End Mencari total persen dari range angka
		}

		$result = array( 'income'  => $html_income,
						 'diterima'=> $html_diterima,
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

	public function ajax_repeat()
	{
		$i = 1;
		$start = substr($this->input->get('periodik'), 0, 10);
		$end = substr($this->input->get('periodik'), 13, 24);
		$rows = array();
		$get_repeat = $this->Dashboard_model->get_customer_repeat($start, $end);
		foreach ($get_repeat as $data) {
			$jumlah = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-shopping-cart' style='margin-right:5px;'></i>".$data->jumlah_penerima." Pesanan</a>";
			$rows[] = array( 'no'				=> $i,
							 'nama_penerima' 	=> $data->nama_penerima, 
							 'provinsi' 		=> $data->provinsi,
							 'kabupaten'	    => $data->kabupaten,
							 'hp_penerima' 		=> $data->hp_penerima,
							 'alamat' 			=> $data->alamat_penerima,
							 'repeat' 			=> $jumlah
			);

			$i++;
		}
		echo json_encode($rows);
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
}
