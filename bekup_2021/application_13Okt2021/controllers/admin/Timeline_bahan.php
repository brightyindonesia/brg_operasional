<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timeline_bahan extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Timeline Bahan Produksi';

	    $this->load->model(array('Timeline_bahan_model', 'Po_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
			$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
		// $this->data['export_action'] = base_url('admin/kurir/export');
	 //    $this->data['add_action'] = base_url('admin/kurir/tambah');
	 //    $this->data['btn_import']    = 'Format Data Import';
		// $this->data['import_action'] = base_url('assets/template/excel/format_kurir.xlsx');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	public function index()
	{
		is_read();    

	    $this->data['page_title'] = 'Purchase Order List';

	    $this->data['get_all'] = $this->Timeline_bahan_model->get_data_po();

	    $this->load->view('back/timeline_bahan/timeline_bahan_po', $this->data);
	}

	public function tambah($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_bahan_model->get_all_by_id_row(base64_decode($id));
		$this->data['get_all_sku'] 			= $this->Timeline_bahan_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_bahan_model->get_all_kategori(); 

		if($this->data['timeline'])
	    {
	    	// echo print_r($this->data['daftar_bahan_kemas']);
	    	$this->data['page_title'] = 'Create Data '.$this->data['module'];
	    	$this->data['action']     = 'admin/timeline_bahan/proses_tambah';
	    	$this->data['nomor_produksi'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nomor_request'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nama_vendor'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['qty'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      	 	$this->data['id'] = [
      	 		'name'          => 'nomor_produksi',	
			  	'id' 			=> 'nomor-produksi', 
		        'type'          => 'hidden',
	     	];	

	     	$this->data['id_po'] = [	
	     		'name'          => 'nomor_request',
			  	'id' 			=> 'nomor-request', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['id_sku'] = [	
	     		'name' 			=> 'sku', 	
			  	'id' 			=> 'sku', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['qty_produksi'] = [	
	     		'name' 			=> 'qty_produksi', 
			  	'id' 			=> 'qty-produksi', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['sku'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '', 
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['kategori'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '',  
		    	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];	

		    $this->load->view('back/timeline_bahan/timeline_bahan_add', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_bahan');
	    }
	}

	public function proses_tambah()
	{
		$data = array(	'no_timeline_bahan'		=> $this->input->post('nomor_produksi'),
						'no_po' 				=> $this->input->post('nomor_request'),
						'total_bahan' 			=> $this->input->post('qty_produksi'),
						'total_bahan_jadi'		=> 0,
						'status_timeline_bahan'	=> 0,
		);

		$this->Timeline_bahan_model->insert($data);

	    write_log();

	    $updatePO = array(	'status_po'	=> 1
		);

		$this->Po_model->update($this->input->post('nomor_request'),$updatePO);

	    write_log();

	    $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	    redirect('admin/timeline_bahan/timeline');
	}

	public function timeline()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';

	    // $this->data['get_all'] = $this->Timeline_bahan_model->get_all();
	    $this->data['get_all_vendor'] = $this->Timeline_bahan_model->get_all_vendor_list();
	    $this->data['get_all_kategori'] = $this->Timeline_bahan_model->get_all_kategori_po_list();
	    $this->data['get_all_status'] = array( 'semua'	=> '- Semua Data-',
	    									   '0' 		=> 'Sedang diproses',
	    									   '1' 		=> 'Sudah diproses',
	     								);

	    $this->data['vendor'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'vendor',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kategori'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kategori',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/timeline_bahan/timeline_bahan_list', $this->data);
	}

	function dasbor_list_count(){
		$vendor 	= $this->input->post('vendor');
		$kategori 	= $this->input->post('kategori');
		$status 	= $this->input->post('status');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      	= $this->Timeline_bahan_model->get_dasbor_list($vendor, $kategori, $status, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'	=> $data->total,
			        		'proses'=> $data->proses,
			        		'sudah'	=> $data->sudah,
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
		$vendor = $this->input->get('vendor');
		$kategori = $this->input->get('kategori');
		$status = $this->input->get('status');
		$start = substr($this->input->get('periodik'), 0, 10);
		$end = substr($this->input->get('periodik'), 13, 24);
		$rows = array();
		$get_all = $this->Timeline_bahan_model->get_datatable($vendor, $kategori, $status, $start, $end);
		foreach ($get_all as $data) {
			  if ($data->status_timeline_bahan ==  0) {
	            $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
	          }elseif ($data->status_timeline_bahan ==  1) {
	            $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
	          }

	          $get_detail = $this->Timeline_bahan_model->get_detail_ajax_datatable($data->no_timeline_bahan);
	          $detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			            '<tr>'.
			                '<td width="15%">Status</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$status.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td>Tanggal</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->tgl_timeline_bahan.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td>SKU Produk</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->nama_sku.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td>Kategori Purchase Order</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->nama_kategori_po.'</td>'.
			            '</tr>'.
			        '</table>'.
			        '<hr width="100%">'.
			        '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			            '<tr align="center">'.
			                '<td>Jenis Timeline</td>'.
			                '<td>Tanggal Awal</td>'.
			                '<td>Tanggal Akhir</td>'.
			                '<td>Durasi</td>'.
			            '</tr>';

			  foreach ($get_detail as $val_detail) {
			  	$diff = abs(strtotime($val_detail->end_date_detail_timeline_bahan) - strtotime($val_detail->start_date_detail_timeline_bahan));
                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                if ($val_detail->id_jenis_timeline == 1) {
                  $jenis = '<a href="#" class="btn btn-sm btn-info"><i class="fa fa-info" style="margin-right:5px;"></i>'.$val_detail->nama_jenis_timeline.'</a>';
                }elseif ($val_detail->id_jenis_timeline == 2) {
                  $jenis = '<a href="#" class="btn btn-sm btn-primary"><i class="fa fa-industry" style="margin-right:5px;"></i>'.$val_detail->nama_jenis_timeline.'</a>';
                }elseif ($val_detail->id_jenis_timeline == 3) {
                  $jenis = '<a href="#" class="btn btn-sm btn-success"><i class="fa fa-truck" style="margin-right:5px;"></i>'.$val_detail->nama_jenis_timeline.'</a>';
                }elseif ($val_detail->id_jenis_timeline == 4) {
                  $jenis = '<a href="#" class="btn btn-sm btn-warning"><i class="fa fa-exchange" style="margin-right:5px;"></i>'.$val_detail->nama_jenis_timeline.'</a>';
                }

			  	$detail .= '<tr align="center">'.
				                '<td>'.$jenis.'</td>'.
				                '<td>'.$val_detail->start_date_detail_timeline_bahan.'</td>'.
				                '<td>'.$val_detail->end_date_detail_timeline_bahan.'</td>'.
				                '<td>'.$days.' Hari</td>'.
				            '</tr>';
			  }

			  $detail .= '</table>';

			  if ($data->status_timeline_bahan == 0) {
                // action
                $action = '<a href="'.base_url('admin/timeline_bahan/history/'.base64_encode($data->no_timeline_bahan)).'" class="btn btn-sm btn-default"><i class="fa fa-list"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_bahan/info/'.base64_encode($data->no_timeline_bahan)).'" class="btn btn-sm btn-info"><i class="fa fa-info"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_bahan/industry/'.base64_encode($data->no_timeline_bahan)).'" class="btn btn-sm btn-primary"><i class="fa fa-industry"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_bahan/delivery/'.base64_encode($data->no_timeline_bahan)).'" class="btn btn-sm btn-success"><i class="fa fa-truck"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_bahan/retur/'.base64_encode($data->no_timeline_bahan)).'" class="btn btn-sm btn-warning"><i class="fa fa-exchange"></i></a> ';    
              }else{
                $action = '<a href="'.base_url('admin/timeline_bahan/history/'.base64_encode($data->no_timeline_bahan)).'" class="btn btn-sm btn-default"><i class="fa fa-list"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_bahan/hapus/'.base64_encode($data->no_timeline_bahan)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
              }

			  $rows[] = array( 'no'				=> $i,
							 'nomor_timeline' 	=> $data->no_timeline_bahan,
							 'nomor_po'		    => $data->no_po,
							 'nama_vendor' 		=> $data->nama_vendor,
							 'jumlah' 			=> $data->total_bahan." / ".$data->total_bahan_jadi,
							 'action' 			=> $action,
							 'detail' 			=> $detail
			);

			$i++;
		}
		echo json_encode($rows);
	}

	public function history($id)
	{
		is_read();

		$this->data['timeline'] 			= $this->Timeline_bahan_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['detail'] 				= $this->Timeline_bahan_model->get_detail_by_timeline(base64_decode($id));
		$this->data['get_all_sku'] 			= $this->Timeline_bahan_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_bahan_model->get_all_kategori(); 
		if($this->data['timeline'] || $this->data['detail'])
	    {
	    	$this->data['page_title'] = 'List Detail Data '.$this->data['module'];
	    	$this->data['action']     = 'admin/timeline_bahan/history_proses';
	    	$this->data['nomor_produksi'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nomor_request'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nama_vendor'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['qty'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      	 	$this->data['id'] = [
      	 		'name'          => 'nomor_produksi',	
			  	'id' 			=> 'nomor-produksi', 
		        'type'          => 'hidden',
	     	];	

	     	$this->data['id_po'] = [	
	     		'name'          => 'nomor_request',
			  	'id' 			=> 'nomor-request', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['id_sku'] = [	
	     		'name' 			=> 'sku', 	
			  	'id' 			=> 'sku', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['qty_produksi'] = [	
	     		'name' 			=> 'qty_produksi', 
			  	'id' 			=> 'qty-produksi', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['sku'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '', 
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['kategori'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '',  
		    	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];	

		    $this->load->view('back/timeline_bahan/timeline_bahan_history', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_bahan/timeline');
	    }
	}

	public function history_proses($id)
	{
		$no_po = str_replace("TML","PO",base64_decode($id));

		$this->data['timeline'] = $this->Timeline_bahan_model->get_all_by_timeline_row(base64_decode($id));
		if (isset($this->data['timeline'])) {
			if ($this->data['timeline']->status_timeline_bahan == 0) {
				$updateTimeline = array( 'status_timeline_bahan' => 1,
				);

				$this->Timeline_bahan_model->update($this->data['timeline']->no_timeline_bahan, $updateTimeline);

				write_log();

				$updatePO = array(	'status_po'	=> 2
				);

				$this->Timeline_bahan_model->updatePO($no_po, $updatePO);

				write_log();

				$detailPO = $this->Timeline_bahan_model->get_bahan_by_po($no_po);
				foreach ($detailPO as $val_detail) {
					$bahan = $this->Timeline_bahan_model->get_bahan_by_id($val_detail->id_bahan_kemas);
					$updateBahan = array( 'qty_bahan_kemas'	=> $bahan->qty_bahan_kemas + $val_detail->selisih_po_produksi);

					$this->Timeline_bahan_model->update_produk($bahan->id_bahan_kemas, $updateBahan);
				}

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
			    redirect('admin/timeline_bahan/timeline');	
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
		    	redirect('admin/timeline_bahan/timeline');
			}
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_bahan/timeline');
		}
	}

	public function info($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_bahan_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['bahan_kemas'] 			= $this->Timeline_bahan_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['get_all_sku'] 			= $this->Timeline_bahan_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_bahan_model->get_all_kategori(); 

		if($this->data['timeline'])
	    {
	    	// echo print_r($this->data['daftar_bahan_kemas']);
	    	$this->data['page_title'] = 'Create Detail Info: '.base64_decode($id);
	    	$this->data['action']     = 'admin/timeline_bahan/proses_info';
	    	$this->data['nomor_produksi'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nomor_request'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nama_vendor'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['qty'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      	 	$this->data['id'] = [
      	 		'name'          => 'nomor_produksi',	
			  	'id' 			=> 'nomor-produksi', 
		        'type'          => 'hidden',
	     	];	

	     	$this->data['id_po'] = [	
	     		'name'          => 'nomor_po',
			  	'id' 			=> 'nomor-po', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['id_sku'] = [	
	     		'name' 			=> 'sku', 	
			  	'id' 			=> 'sku', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['qty_produksi'] = [	
	     		'name' 			=> 'qty_produksi', 
			  	'id' 			=> 'qty-produksi', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['sku'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '', 
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['kategori'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '',  
		    	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];	

		    $this->data['keterangan'] = [
		      'name'          => 'keterangan',
		      'id'            => 'keterangan',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off'
		    ];

		    $this->load->view('back/timeline_bahan/timeline_bahan_info', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_bahan/timeline');
	    }
	}

	public function info_proses()
	{
		// Ambil Data
		$i = $this->input;

		$start_date 	= substr($i->post('date'), 0, 10);
		$end_date 		= substr($i->post('date'), 13, 24);
		$no_po 			= $i->post('nomor_po');
		$nomor_produksi = $i->post('nomor_produksi');
		$keterangan	 	= $i->post('keterangan');
		$len 			= $i->post('length');
		$dt_id 			= $i->post('dt_id');
		$dt_po 			= $i->post('dt_po');
		$decode_id 		= json_decode($dt_id, TRUE);
		$decode_po 		= json_decode($dt_po, TRUE);

		for ($n=0; $n < $len; $n++)
        {
          	$dataDetail[$n] 	= array(	'no_timeline_bahan'					=> $nomor_produksi,
          									'no_po'								=> $decode_po[$n],
											'id_jenis_timeline'					=> 1,
											'id_bahan_kemas'					=> $decode_id[$n],
											'start_date_detail_timeline_bahan'	=> $start_date,
											'end_date_detail_timeline_bahan'	=> $end_date,
											'qty_detail_timeline_bahan'			=> '',
											'harga_detail_timeline_bahan'		=> '',
											'ket_detail_timeline_bahan'			=> $keterangan,
									);

          	$this->Timeline_bahan_model->insert_detail($dataDetail[$n]);

          	write_log();

          	$this->Timeline_bahan_model->updated_time($nomor_produksi);

          	write_log();
        }

        $pesan = "Informasi Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function industry($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_bahan_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['bahan_kemas'] 			= $this->Timeline_bahan_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['get_all_sku'] 			= $this->Timeline_bahan_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_bahan_model->get_all_kategori(); 

		if($this->data['timeline'])
	    {
	    	// echo print_r($this->data['daftar_bahan_kemas']);
	    	$this->data['page_title'] = 'Create Detail Info: '.base64_decode($id);
	    	$this->data['action']     = 'admin/timeline_bahan/proses_info';
	    	$this->data['nomor_produksi'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nomor_request'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['nama_vendor'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      		$this->data['qty'] = [
		        'class'         => 'form-control',
				'readonly' 		=> '' 
      		];

      	 	$this->data['id'] = [
      	 		'name'          => 'nomor_produksi',	
			  	'id' 			=> 'nomor-produksi', 
		        'type'          => 'hidden',
	     	];	

	     	$this->data['id_po'] = [	
	     		'name'          => 'nomor_po',
			  	'id' 			=> 'nomor-po', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['id_sku'] = [	
	     		'name' 			=> 'sku', 	
			  	'id' 			=> 'sku', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['qty_produksi'] = [	
	     		'name' 			=> 'qty_produksi', 
			  	'id' 			=> 'qty-produksi', 
		        'type'          => 'hidden',
	     	];

	     	$this->data['sku'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '', 
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['kategori'] = [
		    	'class'         => 'form-control select2bs4',
		    	'disabled' 		=> '',  
		    	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];	

		    $this->data['keterangan'] = [
		      'name'          => 'keterangan',
		      'id'            => 'keterangan',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off'
		    ];

		    $this->load->view('back/timeline_bahan/timeline_bahan_industry', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_bahan/timeline');
	    }
	}

	public function industry_proses()
	{
		// Ambil Data
		$i = $this->input;

		$start_date 	= substr($i->post('date'), 0, 10);
		$end_date 		= substr($i->post('date'), 13, 24);
		$no_po 			= $i->post('nomor_po');
		$nomor_produksi = $i->post('nomor_produksi');
		$keterangan	 	= $i->post('keterangan');
		$len 			= $i->post('length');
		$dt_id 			= $i->post('dt_id');
		$dt_po 			= $i->post('dt_po');
		$dt_qty			= $i->post('dt_qty');
		$decode_id 		= json_decode($dt_id, TRUE);
		$decode_po 		= json_decode($dt_po, TRUE);
		$decode_qty		= json_decode($dt_qty, TRUE);

		for ($n=0; $n < $len; $n++)
        {
          	$dataDetail[$n] 	= array(	'no_timeline_bahan'					=> $nomor_produksi,
          									'no_po'								=> $decode_po[$n],
											'id_jenis_timeline'					=> 2,
											'id_bahan_kemas'					=> $decode_id[$n],
											'start_date_detail_timeline_bahan'	=> $start_date,
											'end_date_detail_timeline_bahan'	=> $end_date,
											'qty_detail_timeline_bahan'			=> $decode_qty[$n],
											'harga_detail_timeline_bahan'		=> '',
											'ket_detail_timeline_bahan'			=> $keterangan,
									);

          	$this->Timeline_bahan_model->insert_detail($dataDetail[$n]);

          	write_log();

          	$this->Timeline_bahan_model->updated_time($nomor_produksi);

          	write_log();
        }

        $pesan = "Produksi Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function delivery($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_bahan_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['bahan_kemas'] 			= $this->Timeline_bahan_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['get_all_sku'] 			= $this->Timeline_bahan_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_bahan_model->get_all_kategori(); 

		if($this->data['timeline'])
	    {
	    	if (!$this->data['timeline']->total_bahan_jadi >= $this->data['timeline']->total_bahan) {
		    	$this->data['page_title'] = 'Create Detail Delivery: '.base64_decode($id);
		    	$this->data['action']     = 'admin/timeline_bahan/proses_delivery';
		    	$this->data['nomor_produksi'] = [
			        'class'         => 'form-control',
					'readonly' 		=> '' 
	      		];

	      		$this->data['nomor_request'] = [
			        'class'         => 'form-control',
					'readonly' 		=> '' 
	      		];

	      		$this->data['nama_vendor'] = [
			        'class'         => 'form-control',
					'readonly' 		=> '' 
	      		];

	      		$this->data['qty'] = [
			        'class'         => 'form-control',
					'readonly' 		=> '' 
	      		];

	      	 	$this->data['id'] = [
	      	 		'name'          => 'nomor_produksi',	
				  	'id' 			=> 'nomor-produksi', 
			        'type'          => 'hidden',
		     	];	

		     	$this->data['id_po'] = [	
		     		'name'          => 'nomor_po',
				  	'id' 			=> 'nomor-po', 
			        'type'          => 'hidden',
		     	];

		     	$this->data['id_sku'] = [	
		     		'name' 			=> 'sku', 	
				  	'id' 			=> 'sku', 
			        'type'          => 'hidden',
		     	];

		     	$this->data['qty_produksi'] = [	
		     		'name' 			=> 'qty_produksi', 
				  	'id' 			=> 'qty-produksi', 
			        'type'          => 'hidden',
		     	];

		     	$this->data['sku'] = [
			    	'class'         => 'form-control select2bs4',
			    	'disabled' 		=> '', 
			      	'required'      => '',
			      	'style' 		=> 'width:100%'
			    ];

			    $this->data['kategori'] = [
			    	'class'         => 'form-control select2bs4',
			    	'disabled' 		=> '',  
			    	'required'      => '',
			      	'style' 		=> 'width:100%'
			    ];	

			    $this->data['keterangan'] = [
			      'name'          => 'keterangan',
			      'id'            => 'keterangan',
			      'class'         => 'form-control',
			      'autocomplete'  => 'off'
			    ];

			    $this->load->view('back/timeline_bahan/timeline_bahan_delivery', $this->data);	
	    	}else{
	    		$this->session->set_flashdata('message', '<div class="alert alert-danger">Timeline: '.$this->data['timeline']->no_timeline_bahan.'. Order has been sent</div>');
		    	redirect('admin/timeline_bahan/timeline');
	    	}
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_bahan/timeline');
	    }
	}

	public function delivery_proses()
	{
		$total_pesan = 0;

		// Ambil Data
		$i = $this->input;

		$start_date 	= substr($i->post('date'), 0, 10);
		$end_date 		= substr($i->post('date'), 13, 24);
		$no_po 			= $i->post('nomor_po');
		$nomor_produksi = $i->post('nomor_produksi');
		$keterangan	 	= $i->post('keterangan');
		$len 			= $i->post('length');
		$dt_id 			= $i->post('dt_id');
		$dt_po 			= $i->post('dt_po');
		$dt_qty 		= $i->post('dt_qty');
		$decode_id 		= json_decode($dt_id, TRUE);
		$decode_po 		= json_decode($dt_po, TRUE);
		$decode_qty 	= json_decode($dt_qty, TRUE);
		$detail 		= $this->Timeline_bahan_model->get_detail_all_by_timeline_bahan_row($nomor_produksi);

		for ($y=0; $y < $len; $y++)
        {
           $total_pesan		= $total_pesan + $decode_qty[$y];
        }

        // echo print_r($detail);

		for ($n=0; $n < $len; $n++)
        {
        	$updatePO = array(	'total_selisih_po_produksi'	=> $detail->total_selisih_po_produksi + $total_pesan
        	);

        	$this->Timeline_bahan_model->updatePO($decode_po[$n], $updatePO);

        	write_log();

        	$updateTimeline = array(	'total_bahan_jadi'	=> $detail->total_bahan_jadi + $total_pesan
        	);
        	$this->Timeline_bahan_model->updateTimeline($nomor_produksi, $updateTimeline);

        	write_log();

        	$cariDetailPO[$n] = $this->Timeline_bahan_model->get_detail_po_by_po_bahan_row($decode_po[$n], $decode_id[$n]);
        	$updateDetailPO[$n] = array( 'selisih_po_produksi'	=> $cariDetailPO[$n]->selisih_po_produksi + $decode_qty[$n]
        	);
        	
        	
        	$this->Timeline_bahan_model->update_detailPO_by_bahan($decode_po[$n], $decode_id[$n], $updateDetailPO[$n]);

        	write_log();

          	$dataDetail[$n] 	= array(	'no_timeline_bahan'					=> $nomor_produksi,
          									'no_po'								=> $decode_po[$n],
											'id_jenis_timeline'					=> 3,
											'id_bahan_kemas'					=> $decode_id[$n],
											'start_date_detail_timeline_bahan'	=> $start_date,
											'end_date_detail_timeline_bahan'	=> $end_date,
											'qty_detail_timeline_bahan'			=> $decode_qty[$n],
											'harga_detail_timeline_bahan'		=> '',
											'ket_detail_timeline_bahan'			=> $keterangan,
									);

          	$this->Timeline_bahan_model->insert_detail($dataDetail[$n]);

          	write_log();

          	$this->Timeline_bahan_model->updated_time($nomor_produksi);

          	write_log();
        }

        $pesan = "Pengiriman Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function retur($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_bahan_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['bahan_kemas'] 			= $this->Timeline_bahan_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['get_all_sku'] 			= $this->Timeline_bahan_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_bahan_model->get_all_kategori(); 

		if($this->data['timeline'])
	    {
	    	if (!$this->data['timeline']->total_bahan_jadi == 0) {
		    	$this->data['page_title'] = 'Create Detail Retur: '.base64_decode($id);
		    	$this->data['action']     = 'admin/timeline_bahan/proses_retur';
		    	$this->data['nomor_produksi'] = [
			        'class'         => 'form-control',
					'readonly' 		=> '' 
	      		];

	      		$this->data['nomor_request'] = [
			        'class'         => 'form-control',
					'readonly' 		=> '' 
	      		];

	      		$this->data['nama_vendor'] = [
			        'class'         => 'form-control',
					'readonly' 		=> '' 
	      		];

	      		$this->data['qty'] = [
			        'class'         => 'form-control',
					'readonly' 		=> '' 
	      		];

	      	 	$this->data['id'] = [
	      	 		'name'          => 'nomor_produksi',	
				  	'id' 			=> 'nomor-produksi', 
			        'type'          => 'hidden',
		     	];	

		     	$this->data['id_po'] = [	
		     		'name'          => 'nomor_po',
				  	'id' 			=> 'nomor-po', 
			        'type'          => 'hidden',
		     	];

		     	$this->data['id_sku'] = [	
		     		'name' 			=> 'sku', 	
				  	'id' 			=> 'sku', 
			        'type'          => 'hidden',
		     	];

		     	$this->data['qty_produksi'] = [	
		     		'name' 			=> 'qty_produksi', 
				  	'id' 			=> 'qty-produksi', 
			        'type'          => 'hidden',
		     	];

		     	$this->data['sku'] = [
			    	'class'         => 'form-control select2bs4',
			    	'disabled' 		=> '', 
			      	'required'      => '',
			      	'style' 		=> 'width:100%'
			    ];

			    $this->data['kategori'] = [
			    	'class'         => 'form-control select2bs4',
			    	'disabled' 		=> '',  
			    	'required'      => '',
			      	'style' 		=> 'width:100%'
			    ];	

			    $this->data['keterangan'] = [
			      'name'          => 'keterangan',
			      'id'            => 'keterangan',
			      'class'         => 'form-control',
			      'autocomplete'  => 'off'
			    ];

			    $this->load->view('back/timeline_bahan/timeline_bahan_retur', $this->data);	
	    	}else{
	    		$this->session->set_flashdata('message', '<div class="alert alert-danger">Timeline: '.$this->data['timeline']->no_timeline_bahan.'. Order not sent</div>');
		    	redirect('admin/timeline_bahan/timeline');
	    	}
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_bahan/timeline');
	    }
	}

	public function retur_proses()
	{
		$total_pesan = 0;

		// Ambil Data
		$i = $this->input;

		$start_date 	= substr($i->post('date'), 0, 10);
		$end_date 		= substr($i->post('date'), 13, 24);
		$no_po 			= $i->post('nomor_po');
		$nomor_produksi = $i->post('nomor_produksi');
		$keterangan	 	= $i->post('keterangan');
		$len 			= $i->post('length');
		$dt_id 			= $i->post('dt_id');
		$dt_po 			= $i->post('dt_po');
		$dt_qty 		= $i->post('dt_qty');
		$decode_id 		= json_decode($dt_id, TRUE);
		$decode_po 		= json_decode($dt_po, TRUE);
		$decode_qty 	= json_decode($dt_qty, TRUE);
		$detail 		= $this->Timeline_bahan_model->get_detail_all_by_timeline_bahan_row($nomor_produksi);

		for ($y=0; $y < $len; $y++)
        {
           $total_pesan		= $total_pesan + $decode_qty[$y];
        }

        // echo print_r($detail);

		for ($n=0; $n < $len; $n++)
        {
        	$updatePO = array(	'total_selisih_po_produksi'	=> $detail->total_selisih_po_produksi - $total_pesan
        	);

        	$this->Timeline_bahan_model->updatePO($decode_po[$n], $updatePO);

        	write_log();

        	$updateTimeline = array(	'total_bahan_jadi'	=> $detail->total_bahan_jadi - $total_pesan
        	);
        	$this->Timeline_bahan_model->updateTimeline($nomor_produksi, $updateTimeline);

        	write_log();

        	$cariDetailPO[$n] = $this->Timeline_bahan_model->get_detail_po_by_po_bahan_row($decode_po[$n], $decode_id[$n]);
        	$updateDetailPO[$n] = array( 'selisih_po_produksi'	=> $cariDetailPO[$n]->selisih_po_produksi - $decode_qty[$n]
        	);
        	$harga_detail = $cariDetailPO[$n]->harga_po * $decode_qty[$n];
        	$fix_ket = $keterangan.'. Total Harga Retur: Rp.'.number_format($harga_detail);
        	
        	
        	$this->Timeline_bahan_model->update_detailPO_by_bahan($decode_po[$n], $decode_id[$n], $updateDetailPO[$n]);

        	write_log();

          	$dataDetail[$n] 	= array(	'no_timeline_bahan'					=> $nomor_produksi,
          									'no_po'								=> $decode_po[$n],
											'id_jenis_timeline'					=> 4,
											'id_bahan_kemas'					=> $decode_id[$n],
											'start_date_detail_timeline_bahan'	=> $start_date,
											'end_date_detail_timeline_bahan'	=> $end_date,
											'qty_detail_timeline_bahan'			=> $decode_qty[$n],
											'harga_detail_timeline_bahan'		=> $harga_detail,
											'ket_detail_timeline_bahan'			=> $fix_ket,
									);

          	$this->Timeline_bahan_model->insert_detail($dataDetail[$n]);

          	write_log();

          	$this->Timeline_bahan_model->updated_time($nomor_produksi);

          	write_log();
        }

        $pesan = "Retur Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function hapus_detail($id)
	{
		is_delete();

		$this->data['detail'] 	= $this->Timeline_bahan_model->get_detail_all_by_timeline_row(base64_decode($id));
		// echo print_r($this->data['detail'])."<br>";
		if(isset($this->data['detail']))
		{
			if ($this->data['detail']->id_jenis_timeline == 4) {
				$updatePO = array(	'total_selisih_po_produksi'	=> $this->data['detail']->total_selisih_po_produksi + $this->data['detail']->qty_detail_timeline_bahan
	        	);
	        	$this->Timeline_bahan_model->updatePO($this->data['detail']->no_po, $updatePO);

	        	write_log();

	        	$updateTimeline = array(	'total_bahan_jadi'	=> $this->data['detail']->total_bahan_jadi + $this->data['detail']->qty_detail_timeline_bahan
	        	);
	        	$this->Timeline_bahan_model->updateTimeline($this->data['detail']->no_timeline_bahan, $updateTimeline);

	        	write_log();

	        	$updateDetailPO= array( 'selisih_po_produksi'	=> $this->data['detail']->selisih_po_produksi + $this->data['detail']->qty_detail_timeline_bahan
	        	);
	        	
	        	$this->Timeline_bahan_model->update_detailPO_by_bahan($this->data['detail']->no_po, $this->data['detail']->bahan_id, $updateDetailPO);

	        	write_log();
				// print_r($this->data['detail']);
				$this->Timeline_bahan_model->delete_detail(base64_decode($id));
				write_log();

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
				$this->history(base64_encode($this->data['detail']->no_timeline_bahan));
			}else if($this->data['detail']->id_jenis_timeline == 3){
				$updatePO = array(	'total_selisih_po_produksi'	=> $this->data['detail']->total_selisih_po_produksi - $this->data['detail']->qty_detail_timeline_bahan
	        	);

	        	$this->Timeline_bahan_model->updatePO($this->data['detail']->no_po, $updatePO);

	        	write_log();

	        	$updateTimeline = array(	'total_bahan_jadi'	=> $this->data['detail']->total_bahan_jadi - $this->data['detail']->qty_detail_timeline_bahan
	        	);

	        	$this->Timeline_bahan_model->updateTimeline($this->data['detail']->no_timeline_bahan, $updateTimeline);

	        	write_log();

	        	$updateDetailPO= array( 'selisih_po_produksi'	=> $this->data['detail']->selisih_po_produksi - $this->data['detail']->qty_detail_timeline_bahan
	        	);
	        	
	        	// echo print_r($updatePO)."<br>";
	        	// echo print_r($updateTimeline)."<br>";
	        	// echo print_r($updateDetailPO)."<br>";
	        	// echo $this->data['detail']->no_po."<br>";
	        	// echo $this->data['detail']->bahan_id."<br>";

	        	$this->Timeline_bahan_model->update_detailPO_by_bahan($this->data['detail']->no_po, $this->data['detail']->bahan_id, $updateDetailPO);

	        	write_log();

				$this->Timeline_bahan_model->delete_detail(base64_decode($id));
				write_log();

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
				$this->history(base64_encode($this->data['detail']->no_timeline_bahan));
			}else{
				$this->Timeline_bahan_model->delete_detail(base64_decode($id));
				write_log();

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
				$this->history(base64_encode($this->data['detail']->no_timeline_bahan));
			}
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  	redirect('admin/timeline_bahan/timeline');
		}
	}

	public function hapus($id)
	{
		is_delete();

		$this->data['timeline'] 	= $this->Timeline_bahan_model->get_all_by_timeline_row(base64_decode($id));
		// echo print_r($this->data['timeline']);
		if (isset($this->data['timeline'])) {
			if ($this->data['timeline']->status_timeline_bahan == 0) {
				$no_po = str_replace("TML","PO",$this->data['timeline']->no_timeline_bahan);

				$DataUpdatePO =  array(	'status_po' => 0
				);

				$this->Timeline_bahan_model->updatePO($no_po, $DataUpdatePO);

				write_log();

				$this->Timeline_bahan_model->delete_detail($this->data['timeline']->no_timeline_bahan);

				write_log();

				$this->Timeline_bahan_model->delete($this->data['timeline']->no_timeline_bahan);

				write_log();

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
				redirect('admin/timeline_bahan/timeline');	
			}else if ($this->data['timeline']->status_timeline_bahan == 1) {
				$no_po = str_replace("TML","PO",base64_decode($id));

				$updateTimeline = array( 'status_timeline_bahan' => 0,
				);

				$this->Timeline_bahan_model->update($this->data['timeline']->no_timeline_bahan, $updateTimeline);

				write_log();

				$updatePO = array(	'status_po'	=> 1
				);

				$this->Timeline_bahan_model->updatePO($no_po, $updatePO);

				write_log();

				$detailPO = $this->Timeline_bahan_model->get_bahan_by_po($no_po);
				foreach ($detailPO as $val_detail) {
					$bahan = $this->Timeline_bahan_model->get_bahan_by_id($val_detail->id_bahan_kemas);
					$updateBahan = array( 'qty_bahan_kemas'	=> $bahan->qty_bahan_kemas - $val_detail->selisih_po_produksi);

					$this->Timeline_bahan_model->update_produk($bahan->id_bahan_kemas, $updateBahan);
				}

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
			    redirect('admin/timeline_bahan/timeline');
			}
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  	redirect('admin/timeline_bahan/timeline');
		}
	}

}

/* End of file Timeline_bahan.php */
/* Location: ./application/controllers/admin/Timeline_bahan.php */