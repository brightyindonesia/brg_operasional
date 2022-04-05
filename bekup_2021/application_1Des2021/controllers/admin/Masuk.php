<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masuk extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->data['module'] = 'Request For Quotation';
		$this->data['module_po'] = 'Purchase Order';

	    $this->load->model(array('Request_model', 'Vendor_model', 'Sku_model', 'Kategori_po_model', 'Bahan_kemas_model', 'Po_model', 'Penerima_model', 'Timeline_bahan_model'));

	    $this->data['company_data']    				= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['add_action'] = base_url('admin/masuk/request_add');

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	public function request()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';

	    // $this->data['get_all'] = $this->Request_model->get_all();
	    $this->data['get_all_vendor'] = $this->Request_model->get_all_vendor_list();
	    $this->data['get_all_kategori'] = $this->Request_model->get_all_kategori_po_list();
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

	    $this->load->view('back/masuk/request_list', $this->data);
	}

	function dasbor_list_count_request(){
		$vendor 	= $this->input->post('vendor');
		$kategori 	= $this->input->post('kategori');
		$status 	= $this->input->post('status');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      	= $this->Request_model->get_dasbor_list($vendor, $kategori, $status, $start, $end);
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

    // Datatable Server Side
	function get_data_request()
    {
    	$i = 1;
        $list = $this->Request_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
   			  $print = '<a href="'.base_url('admin/masuk/request_print/'.base64_encode($data->no_request)).'" class="btn btn-sm btn-primary"><i class="fa fa-print"></i></a>';
              if ($data->status_request == 0) {
                $forward = '<a href="'.base_url('admin/masuk/request_forward/'.base64_encode($data->no_request)).'" class="btn btn-sm btn-success"><i class="fa fa-send"></i></a>';
                $edit = '<a href="'.base_url('admin/masuk/request_edit/'.base64_encode($data->no_request)).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
                $delete = '<a href="'.base_url('admin/masuk/request_delete/'.base64_encode($data->no_request)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
                $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
                $action = $print." ".$edit." ".$delete." ".$forward;
              }else{
              	$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
                $action = $print;
              }

	          $get_detail = $this->Request_model->get_detail_by_id($data->no_request);
	          $detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			            '<tr>'.
			                '<td width="15%">Status</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$status.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">Nama SKU</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->nama_sku.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">Nama Penerima</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->nama_penerima.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">Alamat Penerima</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->alamat_penerima.'</td>'.
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
			                '<td>Nama Bahan Kemas</td>'.
			                '<td>Jumlah Request</td>'.
			            '</tr>';

			  foreach ($get_detail as $val_detail) {

			  	$detail .= '<tr align="center">'.
				                '<td>'.$val_detail->nama_bahan_kemas.'</td>'.
				                '<td>'.$val_detail->kuantitas_request.'</td>'.
				            '</tr>';
			  }

			  $detail .= '</table>';

            $row = array();
            $row['no'] = $i;
            $row['tanggal'] = $data->tgl_request;
            $row['no_request'] = $data->no_request;
            $row['nama_vendor'] = $data->nama_vendor;
            $row['action'] = $action;
            $row['detail'] = $detail;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Request_model->count_all(),
            "recordsFiltered" => $this->Request_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function request_add()
	{
		is_create();    

	    $this->data['page_title']					= 'Create New '.$this->data['module'];
	    $this->data['get_all_vendor'] 				= $this->Vendor_model->get_all_combobox2();
	    $this->data['get_all_penerima'] 			= $this->Penerima_model->get_all_combobox();
	    $this->data['get_all_sku'] 					= $this->Sku_model->get_all_combobox();
	    $this->data['get_all_kategori']				= $this->Kategori_po_model->get_all_combobox();

	    $this->data['nomor_request'] = [
	      'name'          => 'nomor_request',
	      'id'            => 'nomor-request',
	      'class'         => 'form-control',
	      'onload' 		  => 'loadRequest()', 
	      'readonly' 	  => '',	
	      'required'      => ''
	    ];

	    $this->data['ongkir'] = [
	      'name'          => 'ongkir',
	      'id'            => 'ongkir',
	      'class'         => 'form-control',
	      'required'      => ''
	    ];

	    $this->data['vendor'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'vendor',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['penerima'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'penerima',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['bahan_kemas'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'bahan-kemas',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['sku'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'sku',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kategori'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'kategori',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['remarks'] = [
	      'name'          => 'remarks',
	      'id'            => 'remarks',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	    $this->load->view('back/masuk/request_add', $this->data);
	}

	public function request_proses()
	{
		// Penjumlahan
		$total_harga 	= 0;
		$total_diskon 	= 0;
		$total_pajak 	= 0;

		// Ambil Data
		$i = $this->input;

		$len = $i->post('length');
		$vendor = $i->post('vendor');
		$penerima = $i->post('penerima');
		$kategori = $i->post('kategori');
		$sku = $i->post('sku');
		$remarks = $i->post('remarks');
		$ongkir = intval($i->post('ongkir'));
		$no_request = $i->post('nomor_request');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');
		$dt_harga = $i->post('dt_harga');
		$dt_jumlah = $i->post('dt_jml');
		$dt_diskon = $i->post('dt_diskon');
		$dt_pajak = $i->post('dt_pajak');

		$decode_id = json_decode($dt_id, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);
		$decode_harga = json_decode($dt_harga, TRUE);
		$decode_jumlah = json_decode($dt_jumlah, TRUE);
		$decode_diskon = json_decode($dt_diskon, TRUE);
		$decode_pajak = json_decode($dt_pajak, TRUE);
		
		for ($y=0; $y < $len; $y++)
        {
           $diskon 	   		= ($decode_diskon[$y] * 0.01) * $decode_jumlah[$y];
           $pajak 	   		= ($decode_pajak[$y] * 0.01) * $decode_jumlah[$y];
           $total_harga 	= $total_harga + $decode_jumlah[$y];
           $total_diskon	= $total_diskon + $diskon;
           $total_pajak		= $total_pajak + $pajak;
        }

        $total_harga 	= $total_harga + $ongkir;

        $cek_no_request = $this->Request_model->get_by_id($no_request);
        if (isset($cek_no_request)) {
        	$pesan = "No. Request sudah ada";	
        	$msg = array(	'validasi'	=> $pesan
        			);
        	echo json_encode($msg); 
        }else{
        	$cari_kategori = $this->Kategori_po_model->get_by_id($kategori);
        	$cari_sku = $this->Sku_model->get_by_id($sku);
        	$new_no_req    = $no_request."/".$cari_kategori->kode_kategori_po."/".$cari_sku->kode_sku;
        	$data = array(	'no_request'		=> $new_no_req,
        					'id_kategori_po'	=> $kategori,
			        		'id_users' 			=> $this->session->userdata('id_users'),
        					'id_penerima'		=> $penerima,
        					'id_vendor'			=> $vendor,
							'id_sku'	 		=> $sku,
							'remarks'	 		=> $remarks,
							'ongkir'			=> $ongkir,
							'total_diskon'		=> $total_diskon,
							'total_pajak' 		=> $total_pajak,
							'total_harga'		=> $total_harga,
							'status_request' 	=> 0 
					);

			$this->Request_model->insert($data);

			write_log();

			for ($n=0; $n < $len; $n++)
	        {
	        	$diskon	   		= ($decode_diskon[$n] * 0.01) * $decode_jumlah[$n];
		        $pajak	   		= ($decode_pajak[$n] * 0.01) * $decode_jumlah[$n];
	          	$dataDetail[$n] 	= array(	'no_request'		=> $new_no_req,
												'id_bahan_kemas' 	=> $decode_id[$n],
												'kuantitas_request'	=> $decode_qty[$n],
												'harga_request' 	=> $decode_harga[$n],
												'diskon_request'	=> $decode_diskon[$n],
												'pajak_request'		=> $decode_pajak[$n],
										);

				$this->Request_model->insert_detail($dataDetail[$n]);

				write_log();
	        }

	        $pesan = "Berhasil disimpan!";	
        	$msg = array(	'sukses'	=> $pesan
        			);
        	echo json_encode($msg);
        }
	}

	public function request_edit($id = '')
	{
		is_update();

		$this->data['request']   			= $this->Request_model->get_all_by_id_row(base64_decode($id));
	    $this->data['daftar_bahan_kemas']	= $this->Request_model->get_detail_by_id(base64_decode($id));
	    $this->data['get_all_penerima'] 	= $this->Penerima_model->get_all_combobox();
	    $this->data['get_all_bahan_kemas']	= $this->Bahan_kemas_model->get_all_kemas_by_vendor($this->data['request']->id_vendor);
	    $this->data['get_all_vendor'] 		= $this->Request_model->get_all_vendor();
	    $this->data['get_all_sku'] 			= $this->Request_model->get_all_sku();	
	    $this->data['get_all_kategori'] 	= $this->Request_model->get_all_kategori();	

	    // echo print_r($this->data['get_all_bahan_kemas']);
	    if($this->data['request'])
	    {

	      $this->data['page_title'] = 'Update Data '.$this->data['module'];
		  
		  $this->data['nomor_request'] = [
	        'name'          => 'nomor_request',
	        'class'         => 'form-control',
			'readonly' 		=> '' 
      	  ];

      	  $this->data['id'] = [	
		  	'id' 			=> 'nomor-request', 
	        'type'          => 'hidden',
	      ];	

	      $this->data['ongkir'] = [
	      'name'          => 'ongkir',
	      'id'            => 'ongkir',
	      'class'         => 'form-control',
	      'required'      => ''
	    ];

	    $this->data['vendor'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'vendor',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['penerima'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'penerima',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['bahan_kemas'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'bahan-kemas',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['sku'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'sku',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['kategori'] = [
	    	'class'         => 'form-control select2bs4',
	    	'disabled' 		=> '', 
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['remarks'] = [
	      'name'          => 'remarks',
	      'id'            => 'remarks',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => ''
	    ];

	      $this->load->view('back/masuk/request_edit', $this->data);
	    }else{
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/masuk/request');
	    }
	}

	public function request_edit_proses()
	{
		// Penjumlahan
		$total_harga 	= 0;
		$total_diskon 	= 0;
		$total_pajak 	= 0;

		// Ambil Data
		$i = $this->input;

		$len = $i->post('length');
		$vendor = $i->post('vendor');
		$penerima = $i->post('penerima');
		$sku = $i->post('sku');
		$remarks = $i->post('remarks');
		$ongkir = intval($i->post('ongkir'));
		$no_request = $i->post('nomor_request');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');
		$dt_harga = $i->post('dt_harga');
		$dt_jumlah = $i->post('dt_jml');
		$dt_diskon = $i->post('dt_diskon');
		$dt_pajak = $i->post('dt_pajak');

		$decode_id = json_decode($dt_id, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);
		$decode_harga = json_decode($dt_harga, TRUE);
		$decode_jumlah = json_decode($dt_jumlah, TRUE);
		$decode_diskon = json_decode($dt_diskon, TRUE);
		$decode_pajak = json_decode($dt_pajak, TRUE);
		
		for ($y=0; $y < $len; $y++)
        {
           $diskon 	   		= ($decode_diskon[$y] * 0.01) * $decode_jumlah[$y];
           $pajak 	   		= ($decode_pajak[$y] * 0.01) * $decode_jumlah[$y];
           $total_harga 	= $total_harga + $decode_jumlah[$y];
           $total_diskon	= $total_diskon + $diskon;
           $total_pajak		= $total_pajak + $pajak;
        }

        $total_harga 	= $total_harga + $ongkir;

        // Hapus detail request
        $this->Request_model->delete_detail($no_request);

        $cek_no_request = $this->Request_model->get_by_id($no_request);
        if (isset($cek_no_request)){
        	$data = array(	'id_users' 			=> $this->session->userdata('id_users'),
        					'id_vendor'			=> $vendor,
        					'id_penerima'		=> $penerima,
							'id_sku'	 		=> $sku,
							'remarks'	 		=> $remarks,
							'ongkir'			=> $ongkir,
							'total_diskon'		=> $total_diskon,
							'total_pajak' 		=> $total_pajak,
							'total_harga'		=> $total_harga,
					);

			$this->Request_model->update($no_request, $data);

			write_log();

			for ($n=0; $n < $len; $n++)
	        {
	        	$diskon	   		= ($decode_diskon[$n] * 0.01) * $decode_jumlah[$n];
		        $pajak	   		= ($decode_pajak[$n] * 0.01) * $decode_jumlah[$n];
	          	$dataDetail[$n] 	= array(	'no_request'		=> $no_request,
												'id_bahan_kemas' 	=> $decode_id[$n],
												'kuantitas_request'	=> $decode_qty[$n],
												'harga_request' 	=> $decode_harga[$n],
												'diskon_request'	=> $decode_diskon[$n],
												'pajak_request'		=> $decode_pajak[$n],
										);

				$this->Request_model->insert_detail($dataDetail[$n]);

				write_log();
	        }

	        $pesan = "Berhasil diubah!";	
        	$msg = array(	'sukses'	=> $pesan
        			);
        	echo json_encode($msg);
        }
	}

	public function request_delete($id)
	{
		is_delete();

		$cariDetail = $this->Request_model->get_all_full_detail_by_id(base64_decode($id));
		if(isset($cariDetail))
		{
		  $blob = $this->Po_model->get_bukti_by_po(str_replace("RFQ","PO", base64_decode($id)));

		  if ($blob) {
		  	foreach ($blob as $val_blob) {
			  	unlink("./uploads/bukti_tf_po/".$val_blob->nama_berkas);	
			}

			$this->Po_model->delete_bukti_by_po(str_replace("RFQ","PO", base64_decode($id)));	
		  }
		  
		  $this->Request_model->delete_detail(base64_decode($id));

		  write_log();

		  $this->Request_model->delete(base64_decode($id));

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/masuk/request');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/masuk/request');
		}
	}

	public function request_print($id = '')
	{
		$this->data['request']   			= $this->Request_model->get_all_by_id_row(base64_decode($id));
		$this->data['penerima']				= $this->Penerima_model->get_by_id($this->data['request']->id_penerima);	
		$this->data['daftar_bahan_kemas']	= $this->Request_model->get_detail_by_id(base64_decode($id));

		// echo print_r($this->data['request'])
		$html = $this->load->view('back/report/template_rfq_bahan_kemas', $this->data, TRUE);
		$filename = 'CETAK_RFQ_BAHAN_KEMAS_'.date('d_M_y');
		$this->pdfgenerator->generate($html, $filename, true, 'A4', 'portrait');
	}

	public function request_forward($id = '')
	{
		is_create();

		$this->data['request']   			= $this->Request_model->get_all_by_id_row(base64_decode($id));
	    $this->data['daftar_bahan_kemas']	= $this->Request_model->get_detail_by_id(base64_decode($id));
	    $this->data['get_all_penerima'] 	= $this->Penerima_model->get_all_combobox();
	    $this->data['get_all_bahan_kemas']	= $this->Bahan_kemas_model->get_all_kemas_by_vendor($this->data['request']->id_vendor);
	    $this->data['get_all_vendor'] 		= $this->Request_model->get_all_vendor();
	    $this->data['get_all_sku'] 			= $this->Request_model->get_all_sku();	
	    $this->data['get_all_kategori'] 	= $this->Request_model->get_all_kategori();	

	    if ($this->data['request']->status_request == 0) {
			$this->data['page_title'] = 'Forward Data '.$this->data['module'].' to Purchase Order ';

			$this->data['nomor_request'] = [
			'name'          => 'nomor_request',
			'class'         => 'form-control',
			'readonly' 		=> '' 
			  ];

			$this->data['id'] = [	
				'id' 			=> 'nomor-request', 
				'type'          => 'hidden',
			];

			$this->data['id_kategori'] = [	
				'id' 			=> 'kategori', 
				'type'          => 'hidden',
			];

			$this->data['id_vendor'] = [	
				'id' 			=> 'vendor', 
				'type'          => 'hidden',
			];	

			$this->data['id_penerima'] = [	
				'id' 			=> 'penerima', 
				'type'          => 'hidden',
			];	

			$this->data['id_sku'] = [	
				'id' 			=> 'sku', 
				'type'          => 'hidden',
			];		

			$this->data['ongkir'] = [
			'name'          => 'ongkir',
			'id'            => 'ongkir',
			'class'         => 'form-control'
			];

			$this->data['vendor'] = [
			'class'         => 'form-control select2bs4',
			'required'      => '',
			'disabled' 		=> '', 
			'style' 		=> 'width:100%'
			];

			$this->data['penerima'] = [
			'class'         => 'form-control select2bs4',
			'required'      => '',
			'disabled' 		=> '', 
			'style' 		=> 'width:100%'
			];

			$this->data['bahan_kemas'] = [
			'class'         => 'form-control select2bs4',
			'id'            => 'bahan-kemas',
			'required'      => '',
			'style' 		=> 'width:100%'
			];

			$this->data['sku'] = [
			'class'         => 'form-control select2bs4',
			'required'      => '',
			'disabled' 		=> '', 
			'style' 		=> 'width:100%'
			];

			$this->data['kategori'] = [
			'class'         => 'form-control select2bs4',
			'disabled' 		=> '', 
			'required'      => '',
			'style' 		=> 'width:100%'
			];

			$this->data['remarks'] = [
			'name'          => 'remarks',
			'id'            => 'remarks',
			'class'         => 'form-control',
			'autocomplete'  => 'off'
			];

			$this->load->view('back/masuk/request_forward', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data already exists</div>');
	    	redirect('admin/masuk/request');
	    }
	}

	public function request_forward_proses()
	{
		// Ambil Data
		$i = $this->input;

		// Simpan Database
		// Penjumlahan
		$total_qty		= 0;
		$total_harga 	= 0;
		$total_diskon 	= 0;
		$total_pajak 	= 0;

		$len = $i->post('length');
		$vendor = $i->post('vendor');
		$penerima = $i->post('penerima');
		$kategori = $i->post('kategori');
		$sku = $i->post('sku');
		$remarks = $i->post('remarks');
		$ongkir = intval($i->post('ongkir'));
		$no_request = $i->post('nomor_request');
		$dt_id = $i->post('dt_id');
		$dt_qty = $i->post('dt_qty');
		$dt_harga = $i->post('dt_harga');
		$dt_jumlah = $i->post('dt_jml');
		$dt_diskon = $i->post('dt_diskon');
		$dt_pajak = $i->post('dt_pajak');
		$no_po	= str_replace("RFQ","PO",$no_request);

		$decode_id = json_decode($dt_id, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);
		$decode_harga = json_decode($dt_harga, TRUE);
		$decode_jumlah = json_decode($dt_jumlah, TRUE);
		$decode_diskon = json_decode($dt_diskon, TRUE);
		$decode_pajak = json_decode($dt_pajak, TRUE);

		for ($y=0; $y < $len; $y++)
        {
           $diskon 	   		= ($decode_diskon[$y] * 0.01) * $decode_jumlah[$y];
           $pajak 	   		= ($decode_pajak[$y] * 0.01) * $decode_jumlah[$y];
           $total_harga 	= $total_harga + $decode_jumlah[$y];
           $total_qty 		= $total_qty + $decode_qty[$y];
           $total_diskon	= $total_diskon + $diskon;
           $total_pajak		= $total_pajak + $pajak;
        }
    
        $total_harga 	= $total_harga + $ongkir;


        $data = array(	'no_po'							=> $no_po,
    					'id_kategori_po'				=> $kategori,
		        		'id_users' 						=> $this->session->userdata('id_users'),
    					'id_vendor'						=> $vendor,
    					'id_penerima'					=> $penerima,
						'id_sku'	 					=> $sku,
						'remarks_po' 					=> $remarks,
						'ongkir_po'						=> $ongkir,
						'total_diskon_po'				=> $total_diskon,
						'total_pajak_po'				=> $total_pajak,
						'total_harga_po'				=> $total_harga,
						'total_kuantitas_po' 			=> $total_qty,
						'total_selisih_po_produksi' 	=> 0,
						'status_po'						=> 0,
				);

        $this->Po_model->insert($data);

      	write_log();

      	$updateRequest = array(	'status_request' => 1,
    				);

      	$this->Request_model->update($no_request, $updateRequest);

      	write_log();

        for ($n=0; $n < $len; $n++)
        {
        	$diskon	   		= ($decode_diskon[$n] * 0.01) * $decode_jumlah[$n];
	        $pajak	   		= ($decode_pajak[$n] * 0.01) * $decode_jumlah[$n];
          	$dataDetail[$n] 	= array(	'no_po'		=> $no_po,
											'id_bahan_kemas' 		=> $decode_id[$n],
											'kuantitas_po'			=> $decode_qty[$n],
											'selisih_po_produksi'	=> 0,
											'harga_po' 				=> $decode_harga[$n],
											'diskon_po'				=> $decode_diskon[$n],
											'pajak_po'				=> $decode_pajak[$n],
									);

          	$this->Po_model->insert_detail($dataDetail[$n]);

          	write_log();

         //  	$cariBahanKemas[$n] = $this->Bahan_kemas_model->get_by_id($decode_id[$n]);
         //  	$kurangStokahanKemas[$n] = array(	'qty_bahan_kemas' 		=> $cariBahanKemas[$n]->qty_bahan_kemas + $decode_qty[$n]
						   //        	);

	        // $this->Bahan_kemas_model->update($decode_id[$n], $kurangStokahanKemas[$n]);

	        // write_log();
        }

        $pesan = "Purchase Order berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function purchase_unggah_bukti($id)
	{
		is_create();

		$this->data['po']   				= $this->Po_model->get_all_by_id_row(base64_decode($id));
	    $this->data['daftar_bahan_kemas']	= $this->Po_model->get_detail_by_id(base64_decode($id));
	    $this->data['daftar_unggah_po']		= $this->Po_model->get_bukti_by_po(base64_decode($id));
	    $this->data['get_all_penerima'] 	= $this->Penerima_model->get_all_combobox();
	    $this->data['get_all_bahan_kemas']	= $this->Bahan_kemas_model->get_all_kemas_by_vendor($this->data['po']->id_vendor);
	    $this->data['get_all_vendor'] 		= $this->Po_model->get_all_vendor();
	    $this->data['get_all_sku'] 			= $this->Po_model->get_all_sku();	
	    $this->data['get_all_kategori'] 	= $this->Po_model->get_all_kategori();	

	    if ($this->data['po']) {
			$this->data['page_title'] = 'Upload Evidence Transfer PO: '.$this->data['po']->no_po;

			$this->data['nomor_po'] = [
			'name'          => 'nomor_po',
			'class'         => 'form-control',
			'readonly' 		=> '' 
			  ];

			$this->data['id'] = [	
				'id' 			=> 'nomor-po', 
				'type'          => 'hidden',
			];

			$this->data['id_kategori'] = [	
				'id' 			=> 'kategori', 
				'type'          => 'hidden',
			];

			$this->data['id_vendor'] = [	
				'id' 			=> 'vendor', 
				'type'          => 'hidden',
			];	

			$this->data['id_penerima'] = [	
				'id' 			=> 'penerima', 
				'type'          => 'hidden',
			];	

			$this->data['id_sku'] = [	
				'id' 			=> 'sku', 
				'type'          => 'hidden',
			];		

			$this->data['ongkir'] = [
			'name'          => 'ongkir',
			'id'            => 'ongkir',
			'disabled' 		=> '', 
			'class'         => 'form-control'
			];

			$this->data['vendor'] = [
			'class'         => 'form-control select2bs4',
			'required'      => '',
			'disabled' 		=> '', 
			'style' 		=> 'width:100%'
			];

			$this->data['penerima'] = [
			'class'         => 'form-control select2bs4',
			'required'      => '',
			'disabled' 		=> '', 
			'style' 		=> 'width:100%'
			];

			$this->data['bahan_kemas'] = [
			'class'         => 'form-control select2bs4',
			'id'            => 'bahan-kemas',
			'required'      => '',
			'style' 		=> 'width:100%'
			];

			$this->data['sku'] = [
			'class'         => 'form-control select2bs4',
			'required'      => '',
			'disabled' 		=> '', 
			'style' 		=> 'width:100%'
			];

			$this->data['kategori'] = [
			'class'         => 'form-control select2bs4',
			'disabled' 		=> '', 
			'required'      => '',
			'style' 		=> 'width:100%'
			];

			$this->data['remarks'] = [
			'name'          => 'remarks',
			'id'            => 'remarks',
			'disabled' 		=> '', 
			'class'         => 'form-control',
			'autocomplete'  => 'off'
			];

			$this->data['keterangan_unggah'] = [
			'name'          => 'keterangan_unggah',
			'id'            => 'keterangan-unggah',
			'class'         => 'form-control',
			'autocomplete'  => 'off'
			];

			$this->load->view('back/masuk/po_unggah', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
	    	redirect('admin/masuk/purchase');
	    }
	}

	public function proses_purchase_unggah_bukti()
	{
		// Ambil Data
		$i = $this->input;

		$config['upload_path']          = './uploads/bukti_tf_po/';
		$config['allowed_types']        = 'gif|jpg|png|jpeg';
		$config['file_name']			= 'Bukti_TF_PO_'.date('Y_m_d_').time();
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
			$image_data = $this->upload->data();
			$imgdata = file_get_contents($image_data['full_path']);
			$file_encode=base64_encode($imgdata);
			// $data['tipe_berkas'] = $this->upload->data('file_type');
			// $data['bukti_berkas'] = $file_encode;
			$nama_berkas =  'Bukti_TF_PO_'.date('Y_m_d_').time().$image_data['file_ext'];

			// Simpan Database
			// Penjumlahan

			$no_po = $i->post('nomor_po');
			$keterangan = $i->post('keterangan');
			$tanggal = $i->post('tanggal');

	        $data = array(	'tgl_bukti_tf_po'				=> $tanggal,	
	        				'no_po'							=> $no_po,
							'bukti_tf_po' 					=> $file_encode,
							'keterangan_bukti_tf_po'		=> $keterangan,
							'nama_berkas' 					=> $nama_berkas,
							'tipe_berkas' 					=> $this->upload->data('file_type') 	
					);

	        $this->Po_model->insert_bukti($data);

	      	write_log();

	        $pesan = "Bukti TF PO berhasil diunggah!";	
	    	$msg = array(	'sukses'	=> $pesan,
	    					'no_po' 	=> base64_encode($no_po)
	    			);
	    	echo json_encode($msg);
		}
	}

	public function img_blob($id)
	{
		$blob = $this->Po_model->get_detail_bukti_by_po_row(base64_decode($id));

		echo "<img src='data:".$blob->tipe_berkas.";base64,".$blob->bukti_tf_po."'></td>";
	}

	function unggah_delete($id)
	{
		$cek_unggah = $this->Po_model->get_detail_bukti_by_id_row(base64_decode($id));
		if ($cek_unggah) {
			unlink("./uploads/bukti_tf_po/".$cek_unggah->nama_berkas);	
			$this->Po_model->delete_bukti($cek_unggah->id_bukti_tf_po);	

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		   	redirect('admin/masuk/purchase_unggah_bukti/'.base64_encode($cek_unggah->no_po));
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		   	redirect('admin/masuk/purchase');
		}
	}

	public function purchase()
	{	
		is_read();    

	    $this->data['page_title'] = $this->data['module_po'].' List';

	    // $this->data['get_all'] = $this->Po_model->get_all();
	    $this->data['get_all_vendor'] = $this->Po_model->get_all_vendor_list();
	    $this->data['get_all_kategori'] = $this->Po_model->get_all_kategori_po_list();
	    $this->data['get_all_status'] = array( 'semua'	=> '- Semua Data-',
	    									   '0' 		=> 'Belum diproses',
	    									   '1' 		=> 'Sedang diproses',
	    									   '2' 		=> 'Sudah diproses'
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

	    $this->load->view('back/masuk/po_list', $this->data);
	}

	function dasbor_list_count_po(){
		$vendor 	= $this->input->post('vendor');
		$kategori 	= $this->input->post('kategori');
		$status 	= $this->input->post('status');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      	= $this->Po_model->get_dasbor_list($vendor, $kategori, $status, $start, $end);
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

    // Datatable Server Side
	function get_data_po()
    {
    	$i = 1;
        $list = $this->Po_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
   			  if ($data->status_po ==  0) {
                // $status = "<a href='".base_url('admin/masuk/img_blob/'.base64_encode($data->no_po))."' class='btn btn-primary btn-sm'><i class='fa fa-file-image-o'></i></a> ";
                $status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-times' style='margin-right:5px;'></i>Belum diproses</a>";
              }elseif ($data->status_po ==  1) {
                $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
              }else{
                $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
              }
              // action
              $print = '<a href="'.base_url('admin/masuk/purchase_print/'.base64_encode($data->no_po)).'" class="btn btn-sm btn-primary"><i class="fa fa-print"></i></a>';
              $delete = '<a href="'.base_url('admin/masuk/purchase_delete/'.base64_encode($data->no_po)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
              $unggah = ' <a href="'.base_url('admin/masuk/purchase_unggah_bukti/'.base64_encode($data->no_po)).'" class="btn btn-sm btn-info"><i class="fa fa-file-picture-o"></i></a>';
              $action = $print." ".$unggah." ".$delete;

	          $get_detail = $this->Po_model->get_detail_by_id($data->no_po);
	          $get_unggah = $this->Po_model->get_bukti_by_po($data->no_po);
	          $detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			            '<tr>'.
			                '<td width="15%">Status</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$status.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">Nama SKU</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->nama_sku.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">Nama Penerima</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->nama_penerima.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">Alamat Penerima</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->alamat_penerima.'</td>'.
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
			                '<td>Nama Bahan Kemas</td>'.
			                '<td>Jumlah Request / Jumlah Diterima</td>'.
			                '<td>Harga</td>'.
			            '</tr>';

			  foreach ($get_detail as $val_detail) {

			  	$detail .= '<tr align="center">'.
				                '<td>'.$val_detail->nama_bahan_kemas.'</td>'.
				                '<td>'.$val_detail->kuantitas_po.' / '.$val_detail->selisih_po_produksi.'</td>'.
				                '<td>'.$val_detail->harga_po.'</td>'.
				            '</tr>';
			  }

			  $detail .= '</table>'.
					     '<hr width="100%">'.
					     '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
						     '<tr align="center">'.
				                '<td>Tanggal</td>'.
				                '<td>Keterangan Bukti TF</td>'.
				                '<td>Action</td>'.
				             '</tr>';

				         if ($get_unggah) {
				         	foreach ($get_unggah as $val_unggah) {
				         		$detail .=  '<tr align="center">'.
								                '<td>'.date('d-m-Y', strtotime($val_unggah->tgl_bukti_tf_po)).'</td>'.
								                '<td>'.$val_unggah->keterangan_bukti_tf_po.'</td>'.
								                '<td>'.
								                	'<a href="'.base_url('admin/masuk/img_blob/'.base64_encode($data->no_po)).'" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-search" style="margin-right: 5px;"></i> Lihat Bukti TF</a>'.
								                '</td>'.
								            '</tr>';	
				         	}
				         }else{
				         	$detail .=  '<tr align="center">'.
							                '<td colspan="3"> Daftar Unggah Bukti TF masih kosong </td>'.
							            '</tr>';
				         }

			  $detail .= '</table>';

            $row = array();
            $row['no'] = $i;
            $row['tanggal'] = $data->tgl_po;
            $row['no_po'] = $data->no_po;
            $row['nama_vendor'] = $data->nama_vendor;
            $row['action'] = $action;
            $row['detail'] = $detail;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Po_model->count_all(),
            "recordsFiltered" => $this->Po_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function purchase_print($id = '')
	{
		$this->data['purchase']   			= $this->Po_model->get_all_by_id_row(base64_decode($id));
		$this->data['penerima']				= $this->Penerima_model->get_by_id($this->data['purchase']->id_penerima);	
		$this->data['daftar_bahan_kemas']	= $this->Po_model->get_detail_by_id(base64_decode($id));

		// echo print_r($this->data['request'])
		$html = $this->load->view('back/report/template_po_bahan_kemas', $this->data, TRUE);
		$filename = 'CETAK_PO_BAHAN_KEMAS_'.date('d_M_y');
		$this->pdfgenerator->generate($html, $filename, true, 'A4', 'portrait');
	}

	public function purchase_delete($id)
	{
		is_delete();

		$cek_timeline = $this->Timeline_bahan_model->get_all_by_timeline_row(str_replace("PO","TML", base64_decode($id)));

		if (!isset($cek_timeline)) {
			$cariDetail = $this->Po_model->get_all_full_detail_by_id(base64_decode($id));
			$no_request	= str_replace("PO","RFQ",base64_decode($id));
			if(isset($cariDetail))
			{
			  $i = 0;
			 //  foreach ($cariDetail as $bahan_kemas) {

				// $kurangiStok[$i] = array( 	'qty_bahan_kemas' 		=> $bahan_kemas->qty_bahan_kemas - $bahan_kemas->kuantitas_po
				// 					);
				// $this->Bahan_kemas_model->update($bahan_kemas->id_bahan_kemas, $kurangiStok[$i]);

				// write_log();
			 //  }

			  $updateRequest = array(	'status_request' => 0,
	    				);

		      $this->Request_model->update($no_request, $updateRequest);

		      write_log();
			  
			  $this->Po_model->delete_detail(base64_decode($id));

			  write_log();

			  $this->Po_model->delete(base64_decode($id));

			  write_log();

			  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
			  redirect('admin/masuk/purchase');
			}
			else
			{
			  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
			  redirect('admin/masuk/purchase');
			}	
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Cannot be deleted, because the Purchase Order data is already in the Timeline Bahan Produksi</div>');
			  redirect('admin/masuk/purchase');
		}
	}

	public function generate_nomor()
	{
		date_default_timezone_set("Asia/Jakarta");
		$date= date("Y-m-d");
		$tahun = substr($date, 2, 2);
		$bulan = substr($date, 5, 2);
		$tanggal = substr($date, 8, 2);
		$teks = "RFQ/".$tahun.$bulan.$tanggal;
		$ambil_nomor = $this->Request_model->cari_nomor($teks);
		// echo print_r(json_encode($ambil_nomor));
		// $hitung = count($ambil_nomor);
		// echo $ambil_nomor->nomor_pesanan;
		if (isset($ambil_nomor)) {
			// TANGGAL DARI ID NILAI
			$ambil_tahun = substr($ambil_nomor->no_request, 4, 2);
			$ambil_bulan = substr($ambil_nomor->no_request, 6, 2);
			$ambil_tanggal = substr($ambil_nomor->no_request, 8, 2);
			$ambil_no = (int) substr($ambil_nomor->no_request, 11, 4);

			if ($tahun == $ambil_tahun && $bulan == $ambil_bulan && $tanggal == $ambil_tanggal) {
				$ambil_no++;	
				$no_masuk = "RFQ/".$tahun.$bulan.$tanggal."/".sprintf("%04s", $ambil_no);
			}else{
				$no_masuk = "RFQ/".$tahun.$bulan.$tanggal."/0001";
			}

			echo json_encode($no_masuk);
		}else{
			$no_masuk = "RFQ/".$tahun.$bulan.$tanggal."/0001";

			echo json_encode($no_masuk);
		}
	}

	public function get_id_vendor()
	{
		$vendor = $this->input->post('vendor');
		$select_box[] = "<option value=''>- Pilih Nama Bahan Kemas -</option>";
		$bahan_kemas = $this->Request_model->get_id_vendor($vendor);
		if (count($bahan_kemas) > 0) {
			foreach ($bahan_kemas as $row) {
				$select_box[] = '<option value="'.$row->id_bahan_kemas.'">'.$row->kode_sku_bahan_kemas.' | '.$row->nama_bahan_kemas.' (<small>'.$row->keterangan.'</small>)</option>';
			}
			// header("Content-Type:application/json");
			echo json_encode($select_box);
		}else{
			$select_box = '<option value="">Tidak Ada</option>';
			echo json_encode($select_box);
		}
	}

	public function get_id_bahan_kemas()
	{
		$bahan_kemas = $this->input->post('bahan_kemas');
		$cari_bahan_kemas =	$this->Request_model->get_id_bahan_kemas($bahan_kemas);
		echo json_encode($cari_bahan_kemas);
	}

}

/* End of file Purchasing.php */
/* Location: ./application/controllers/admin/Purchasing.php */