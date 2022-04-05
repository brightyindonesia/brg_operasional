<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timeline_produksi extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Timeline Produksi';

	    $this->load->model(array('Timeline_produksi_model', 'Po_model', 'Produk_model', 'Toko_model'));

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

	    $this->data['get_all'] = $this->Timeline_produksi_model->get_data_po();

	    $this->load->view('back/timeline_produksi/timeline_produksi_po', $this->data);
	}

	public function tambah($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_id_row(base64_decode($id));
		$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 

		if($this->data['timeline']->id_kategori_po == 6)
	    {
	    	// echo print_r($this->data['daftar_bahan_kemas']);
	    	$this->data['page_title'] = 'Create Data '.$this->data['module'];
	    	$this->data['action']     = 'admin/timeline_produksi/proses_tambah';
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

		    $this->load->view('back/timeline_produksi/timeline_produksi_add', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi');
	    }
	}

	public function proses_tambah()
	{
		$data = array(	'no_timeline_produksi'		=> $this->input->post('nomor_produksi'),
						'no_po' 					=> $this->input->post('nomor_request'),
						'total_produksi' 			=> $this->input->post('qty_produksi'),
						'total_produksi_jadi'		=> 0,
						'status_timeline_produksi'	=> 0,
		);

		$this->Timeline_produksi_model->insert($data);

	    write_log();

	    $updatePO = array(	'status_po'	=> 1
		);

		$this->Po_model->update($this->input->post('nomor_request'),$updatePO);

	    write_log();

	    $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	    redirect('admin/timeline_produksi/timeline');
	}

	public function timeline()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' List';

	    // $this->data['get_all'] = $this->Timeline_bahan_model->get_all();
	    $this->data['get_all_vendor'] = $this->Timeline_produksi_model->get_all_vendor_list();
	    // $this->data['get_all_kategori'] = $this->Timeline_produksi_model->get_all_kategori_po_list();
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

	    // $this->data['kategori'] = [
	    // 	'class'         => 'form-control select2bs4',
	    // 	'id'            => 'kategori',
	    //   	'required'      => '',
	    //   	'style' 		=> 'width:100%'
	    // ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/timeline_produksi/timeline_produksi_list', $this->data);
	}

	public function data_hpp()
	{
		is_read();    

	    $this->data['page_title'] = 'Product HPP of '.$this->data['module'].' List';

	    // $this->data['get_all'] = $this->Timeline_bahan_model->get_all();
	    $this->data['get_all_vendor'] = $this->Timeline_produksi_model->get_all_vendor_list();
	    // $this->data['get_all_kategori'] = $this->Timeline_produksi_model->get_all_kategori_po_list();
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

	    // $this->data['kategori'] = [
	    // 	'class'         => 'form-control select2bs4',
	    // 	'id'            => 'kategori',
	    //   	'required'      => '',
	    //   	'style' 		=> 'width:100%'
	    // ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/timeline_produksi/timeline_produksi_hpp_list', $this->data);
	}

	public function tambah_hpp()
	{
		is_create();    

	    $this->data['page_title'] = 'Create Product HPP of '.$this->data['module'];

	    $this->data['get_all_vendor'] = $this->Timeline_produksi_model->get_all_vendor_list();

	    $this->data['vendor'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'vendor',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->data['status'] = [
	    	'class'         => 'form-control select2bs4',
	    	'id'            => 'status',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/timeline_produksi/tambah_hpp_list', $this->data);
	}

	public function tambah_produk($id)
	{
		is_create();

		$this->data['page_title'] 	= 'Create Product of Product HPP';
		$this->data['hpp']			= $this->Timeline_produksi_model->get_all_hpp_by_id(base64_decode($id));
		if ($this->data['hpp']) {
			$this->data['timeline'] = $this->Timeline_produksi_model->get_all_by_timeline_row($this->data['hpp']->no_timeline_produksi);
		    $this->data['action']     = 'admin/timeline_produksi/tambah_produk_proses';
		    $this->data['get_all_tokpro_data_access']  = $this->Toko_model->get_all_combobox();
		    $this->data['get_all_satuan'] = $this->Produk_model->get_all_satuan();
		    $this->data['get_all_sku'] = $this->Produk_model->get_all_sku();

		    $this->data['sku'] = [
		      'id'            => 'kode-sku',
		      'class'         => 'form-control',
		      'disabled' 	  => '', 
		      'autocomplete'  => 'off',
		      'required'      => '',
		    ];

		    $this->data['id_sku'] = [
		      'id'            => 'id-sku',
		      'name'          => 'sku',
		      'type' 		  => 'hidden',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'value'         => $this->data['timeline']->id_sku,
		    ];

		    $this->data['id_hpp'] = [
		      'id'            => 'id-hpp',
		      'name'          => 'id_hpp',
		      'type' 		  => 'hidden',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'value'         => $id,
		    ];

		    $this->data['sub_sku'] = [
		      'name'          => 'sub_sku',
		      'id'            => 'sub-sku',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		      'value'         => $this->form_validation->set_value('sub_sku'),
		    ];

		    $this->data['qty'] = [
		      'name'          => 'qty',
		      'id'            => 'qty',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'readonly' 	  => '',
		      'required'      => '',
		      'value'         => $this->data['timeline']->total_produksi_jadi
		    ];

		    $this->data['hpp'] = [
		      'name'          => 'hpp',
		      'id'            => 'hpp',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'readonly' 	  => '', 
		      'required'      => '',
		      'value'         => $this->data['hpp']->total_hpp_produk
		    ];

		    $this->data['produk_nama'] = [
		      'name'          => 'nama_produk',
		      'id'            => 'nama-produk',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		      'value'         => $this->form_validation->set_value('nama_produk'),
		    ];

		    $this->data['tokpro_access_id'] = [
				'name'          => 'tokpro_access_id[]',
				'id'            => 'tokpro-access-id',
				'class'         => 'form-control select2',
				'multiple'      => '',
			];

			$this->data['satuan'] = [
		    	'class'         => 'form-control',
		    	'id'            => 'satuan',
		     	'autocomplete'  => 'off',
		      	'required'      => '',
		      	'value'         => $this->form_validation->set_value('satuan'),
		    ];

		    $this->load->view('back/timeline_produksi/timeline_produksi_produk_add', $this->data);
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  	redirect('admin/timeline_produksi/data_hpp');
		}
	}

	function tambah_produk_proses()
	{
		$this->form_validation->set_rules('nama_produk', 'Nama Produk', 'trim|required',
			array(	'required' 		=> '%s harus diisi!')
		);
		
		$this->form_validation->set_rules('sub_sku', 'Sub SKU', 'required|trim|is_unique[produk.sub_sku]|max_length[50]',
			array(	'required' 		=> '%s harus diisi!',
					'is_unique'		=> '<strong>'.$this->input->post('sub_sku').'</strong> sudah ada. Buat %s baru',
					'max_length'	=> '%s maksimal 50 karakter'
			)
		);

		$this->form_validation->set_rules('qty', 'Qty', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('hpp', 'HPP', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);
		
		$this->form_validation->set_rules('tokpro_access_id[]', 'Daftar Toko', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

		$this->form_validation->set_rules('satuan', 'Satuan Produk', 'required',
			array(	'required' 		=> '%s harus diisi!')
		);

	    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

	    if($this->form_validation->run() === FALSE)
	    {
	      $this->tambah_produk($this->input->post('id_hpp'));
	    }
	    else
	    {
	      $data = array(
	        'id_satuan'    => $this->input->post('satuan'),
	        'id_sku'       => $this->input->post('sku'),
	        'sub_sku' 	   => $this->input->post('sub_sku'),
	        'nama_produk'  => $this->input->post('nama_produk'),
	        'qty_produk'   => $this->input->post('qty'),
	        'hpp_produk'   => $this->input->post('hpp')
	      );

	      $this->Produk_model->insert($data);

	      write_log();

	      $this->db->select_max('id_produk');
          $result = $this->db->get('produk')->row_array();
	      
          $tokpro_access_id = count($this->input->post('tokpro_access_id'));

          for($i_tokpro_access_id = 0; $i_tokpro_access_id < $tokpro_access_id; $i_tokpro_access_id++)
          {
            $datas_tokpro_access_id[$i_tokpro_access_id] = array(
              'id_produk'   => $result['id_produk'],
              'id_toko'  	=> $this->input->post('tokpro_access_id['.$i_tokpro_access_id.']'),
            );

            $this->db->insert('tokpro_data_access', $datas_tokpro_access_id[$i_tokpro_access_id]);

            write_log();
          }

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/timeline_produksi/data_hpp');
	    }
	}

	function dasbor_list_count(){
		$vendor 	= $this->input->post('vendor');
		$status 	= $this->input->post('status');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      	= $this->Timeline_produksi_model->get_dasbor_list($vendor, $status, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'hpp'	=> $data->hpp,
        					'total'	=> $data->total,
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

    function dasbor_list_count_hpp(){
		$vendor 	= $this->input->post('vendor');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      	= $this->Timeline_produksi_model->get_dasbor_list_hpp($vendor, $start, $end);
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

    function dasbor_list_count_hpp_fix(){
		$vendor 	= $this->input->post('vendor');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      	= $this->Timeline_produksi_model->get_dasbor_list_hpp_fix($vendor, $start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'	=> $data->total,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    // Datatable Server Side
	function get_data_timeline()
    {
    	$i = 1;
        $list = $this->Timeline_produksi_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
   			if ($data->status_timeline_produksi ==  0) {
	            $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
	          }elseif ($data->status_timeline_produksi ==  1) {
	            $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
	          }elseif ($data->status_timeline_produksi ==  2) {
	            $status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-legal' style='margin-right:5px;'></i>Sudah dibuat HPP Produk</a>";
	          }

	          $get_detail = $this->Timeline_produksi_model->get_detail_ajax_datatable($data->no_timeline_produksi);
	          $detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			            '<tr>'.
			                '<td width="15%">Status</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$status.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">No. PO</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->no_po.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td>Tanggal</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->tgl_timeline_produksi.'</td>'.
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
			  	$diff = abs(strtotime($val_detail->end_date_detail_timeline_produksi) - strtotime($val_detail->start_date_detail_timeline_produksi));
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
				                '<td>'.$val_detail->start_date_detail_timeline_produksi.'</td>'.
				                '<td>'.$val_detail->end_date_detail_timeline_produksi.'</td>'.
				                '<td>'.$days.' Hari</td>'.
				            '</tr>';
			  }

			  $detail .= '</table>';

			  if ($data->status_timeline_produksi == 0) {
                // action
                $action = '<a href="'.base_url('admin/timeline_produksi/history/'.base64_encode($data->no_timeline_produksi)).'" class="btn btn-sm btn-default"><i class="fa fa-list"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_produksi/info/'.base64_encode($data->no_timeline_produksi)).'" class="btn btn-sm btn-info"><i class="fa fa-info"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_produksi/industry/'.base64_encode($data->no_timeline_produksi)).'" class="btn btn-sm btn-primary"><i class="fa fa-industry"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_produksi/delivery/'.base64_encode($data->no_timeline_produksi)).'" class="btn btn-sm btn-success"><i class="fa fa-truck"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_produksi/retur/'.base64_encode($data->no_timeline_produksi)).'" class="btn btn-sm btn-warning"><i class="fa fa-exchange"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_produksi/hapus/'.base64_encode($data->no_timeline_produksi)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';    
              }else{
                $action = '<a href="'.base_url('admin/timeline_produksi/history/'.base64_encode($data->no_timeline_produksi)).'" class="btn btn-sm btn-default"><i class="fa fa-list"></i></a> ';
                $action .= '<a href="'.base_url('admin/timeline_produksi/hapus/'.base64_encode($data->no_timeline_produksi)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
              }

            $row = array();
            $row['no'] = $i;
            $row['nomor_timeline'] = $data->no_timeline_produksi;
            $row['nama_vendor'] = $data->nama_vendor;
            $row['jumlah'] = $data->total_produksi." / ".$data->total_produksi_jadi;
            $row['action'] = $action;
            $row['detail'] = $detail;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Timeline_produksi_model->count_all(),
            "recordsFiltered" => $this->Timeline_produksi_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_timeline_hpp()
    {
    	$i = 1;
        $list = $this->Timeline_produksi_model->get_datatables_hpp();
        $dataJSON = array();
        foreach ($list as $data) {
   			if ($data->status_timeline_produksi ==  0) {
	            $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
	          }elseif ($data->status_timeline_produksi ==  1) {
	            $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
	          }elseif ($data->status_timeline_produksi ==  2) {
	            $status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-legal' style='margin-right:5px;'></i>Sudah dibuat HPP Produk</a>";
	          }

	          $get_detail = $this->Timeline_produksi_model->get_detail_ajax_datatable($data->no_timeline_produksi);
	          $detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			            '<tr>'.
			                '<td width="15%">Status</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$status.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">No. PO</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->no_po.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td>Tanggal</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->tgl_timeline_produksi.'</td>'.
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
			  	$diff = abs(strtotime($val_detail->end_date_detail_timeline_produksi) - strtotime($val_detail->start_date_detail_timeline_produksi));
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
				                '<td>'.$val_detail->start_date_detail_timeline_produksi.'</td>'.
				                '<td>'.$val_detail->end_date_detail_timeline_produksi.'</td>'.
				                '<td>'.$days.' Hari</td>'.
				            '</tr>';
			  }

			  $detail .= '</table>';

            $action = '<a href="'.base_url('admin/timeline_produksi/cek_bahan_produksi/'.base64_encode($data->no_timeline_produksi)).'" class="btn btn-sm btn-success"><i class="fa fa-legal"></i></a> ';

            $row = array();
            $row['no'] = $i;
            $row['nomor_timeline'] = $data->no_timeline_produksi;
            $row['nama_vendor'] = $data->nama_vendor;
            $row['jumlah'] = $data->total_produksi." / ".$data->total_produksi_jadi;
            $row['action'] = $action;
            $row['detail'] = $detail;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Timeline_produksi_model->count_all(),
            "recordsFiltered" => $this->Timeline_produksi_model->count_filtered_hpp(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function get_data_timeline_hpp_fix()
    {
    	$i = 1;
        $list = $this->Timeline_produksi_model->get_datatables_hpp_fix();
        $dataJSON = array();
        foreach ($list as $data) {
   			if ($data->status_timeline_produksi ==  0) {
	            $status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-half' style='margin-right:5px;'></i>Sedang diproses</a>";
	          }elseif ($data->status_timeline_produksi ==  1) {
	            $status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>Sudah diproses</a>";
	          }elseif ($data->status_timeline_produksi ==  2) {
	            $status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-legal' style='margin-right:5px;'></i>Sudah dibuat HPP Produk</a>";
	          }

	          $get_detail = $this->Timeline_produksi_model->get_detail_ajax_datatable($data->no_timeline_produksi);
	          $get_hpp = $this->Timeline_produksi_model->get_detail_ajax_datatable_hpp($data->id_hpp_produk);
	          $detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
			            '<tr>'.
			                '<td width="15%">Status</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$status.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td width="15%">No. PO</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->no_po.'</td>'.
			            '</tr>'.
			            '<tr>'.
			                '<td>Tanggal</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->tgl_timeline_produksi.'</td>'.
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
			            '<tr>'.
			                '<td>Keterangan</td>'.
			                '<td width="1%">:</td>'.
			                '<td>'.$data->keterangan_hpp_produk.'</td>'.
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
			  	$diff = abs(strtotime($val_detail->end_date_detail_timeline_produksi) - strtotime($val_detail->start_date_detail_timeline_produksi));
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
				                '<td>'.$val_detail->start_date_detail_timeline_produksi.'</td>'.
				                '<td>'.$val_detail->end_date_detail_timeline_produksi.'</td>'.
				                '<td>'.$days.' Hari</td>'.
				            '</tr>';
			  }

			  $detail .= '</table>'.
			        '<hr width="100%">'.
			  			 '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
				            '<tr align="center">'.
				                '<td>Nomor PO</td>'.
				                '<td>Nama Bahan Produksi</td>'.
				                '<td>Harga</td>'.
				            '</tr>';

			  foreach ($get_hpp as $val_hpp) {
			  	$detail .= '<tr align="center">'.
				                '<td>'.$val_hpp->no_po.'</td>'.
				                '<td>'.$val_hpp->nama_bahan_kemas.'</td>'.
				                '<td>'.$val_hpp->harga_hpp_produk.'</td>'.
				            '</tr>';
			  }

			  $detail .= '</table>';

            $action = '<a href="'.base_url('admin/timeline_produksi/tambah_produk/'.base64_encode($data->id_hpp_produk)).'" class="btn btn-sm btn-success"><i class="fa fa-cubes"></i></a> ';
            $action .= '<a href="'.base_url('admin/timeline_produksi/hapus_hpp/'.base64_encode($data->id_hpp_produk)).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';

            $row = array();
            $row['no'] = $i;
            $row['nomor_timeline'] = $data->no_timeline_produksi;
            $row['nama_vendor'] = $data->nama_vendor;
            $row['jumlah'] = $data->total_produksi." / ".$data->total_produksi_jadi;
            $row['total']  = $data->total_hpp_produk;
            $row['action'] = $action;
            $row['detail'] = $detail;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Timeline_produksi_model->count_all_hpp_fix(),
            "recordsFiltered" => $this->Timeline_produksi_model->count_filtered_hpp_fix(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function cek_bahan_produksi($id)
	{
		is_read();

		$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['bahan_po'] 			= $this->Timeline_produksi_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['bahan_kemas']			= $this->Timeline_produksi_model->get_posi_po_bahan_jenis_timeline_by_id(2, base64_decode($id));
		$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 
		if($this->data['timeline']->status_timeline_produksi == 1)
	    {
	    	$this->data['page_title'] = 'List Detail Data '.$this->data['module'];
	    	$this->data['action']     = 'admin/timeline_produksi/proses_cek_bahan_produksi';
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

		    $this->load->view('back/timeline_produksi/cek_bahan_produksi', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
	    }
	}

	public function proses_cek_bahan_produksi()
	{
		$details  			= json_decode($this->input->post('details'));
		$nomor_produksi		= $this->input->post('nomor_produksi');
		$no_po 				= str_replace("TML","PO",$this->input->post('nomor_produksi'));
		$sku				= $this->input->post('sku');
		$total				= $this->input->post('total');
		$jenis				= $this->input->post('jenis');
		$keterangan			= $jenis.$this->input->post('keterangan');

		$updateTimeline = array( 'status_timeline_produksi' => 2,
		);

		$this->Timeline_produksi_model->update($nomor_produksi, $updateTimeline);

		write_log();

		$detailPO = $this->Timeline_produksi_model->get_bahan_by_po($no_po);
		foreach ($detailPO as $val_detail) {
			$bahan = $this->Timeline_produksi_model->get_bahan_by_id($val_detail->id_bahan_kemas);
			$updateBahan = array( 'qty_bahan_kemas'	=> $bahan->qty_bahan_kemas - $val_detail->selisih_po_produksi);

			
			$this->Timeline_produksi_model->update_produk($bahan->id_bahan_kemas, $updateBahan);
		}

		$dataHPP = array(	'no_timeline_produksi' 	=> $nomor_produksi,
							'keterangan_hpp_produk' => $keterangan,
							'total_hpp_produk'  	=> $total 
		);

		$this->Timeline_produksi_model->insert_hpp($dataHPP);

		$get_last = $this->Timeline_produksi_model->get_last_hpp_produk();

        foreach($details as $val_detail ){
            $po_bahan = $val_detail->po_bahan;
            $po_harga = $val_detail->po_harga;
            $bahan_id = $val_detail->bahan_id;

            $data_detailHPP = array(	'id_hpp_produk'		=> $get_last->id_hpp_produk,
	            						'no_po' 			=> $po_bahan,
	            						'id_bahan_kemas'	=> $bahan_id,
	            						'harga_hpp_produk' 	=> $po_harga,
            );

            $this->Timeline_produksi_model->insert_detail_hpp($data_detailHPP);
        }

        $pesan = "Berhasil disimpan!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function history($id)
	{
		is_read();

		$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['detail'] 				= $this->Timeline_produksi_model->get_detail_by_timeline(base64_decode($id));
		$this->data['bahan_kemas']			= $this->Timeline_produksi_model->get_stok_bahan_by_id(base64_decode($id));
		$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 
		if($this->data['timeline'] || $this->data['detail'])
	    {
	    	$this->data['page_title'] = 'List Detail Data '.$this->data['module'];
	    	$this->data['action']     = 'admin/timeline_produksi/history_proses';
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

		    $this->load->view('back/timeline_produksi/timeline_produksi_history', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
	    }
	}

	public function history_proses($id)
	{
		$no_po = str_replace("TML","PO",base64_decode($id));

		$this->data['timeline'] = $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		if (isset($this->data['timeline'])) {
			if ($this->data['timeline']->status_timeline_produksi == 0) {
				$updateTimeline = array( 'status_timeline_produksi' => 1,
				);

				$this->Timeline_produksi_model->update($this->data['timeline']->no_timeline_produksi, $updateTimeline);

				write_log();

				$updatePO = array(	'status_po'	=> 2
				);

				$this->Timeline_produksi_model->updatePO($no_po, $updatePO);

				write_log();

				$detailPO = $this->Timeline_produksi_model->get_bahan_by_po($no_po);
				foreach ($detailPO as $val_detail) {
					$bahan = $this->Timeline_produksi_model->get_bahan_by_id($val_detail->id_bahan_kemas);
					$updateBahan = array( 'qty_bahan_kemas'	=> $bahan->qty_bahan_kemas + $val_detail->selisih_po_produksi);

					
					$this->Timeline_produksi_model->update_produk($bahan->id_bahan_kemas, $updateBahan);
				}

				$get_posi = $this->Timeline_produksi_model->get_posi_bahan_jenis_timeline_by_id(2, base64_decode($id));
				foreach ($get_posi as $val_posi) {
					$get_detail2 = $this->Timeline_produksi_model->get_all_detail_po_by_po_bahan_row($val_posi->no_po, $val_posi->id_bahan_kemas);
		          	if ($get_detail2) {
			          	$kurangiJumlahPO = array( 'total_selisih_po_produksi' 	=> $get_detail2->total_selisih_po_produksi + $val_posi->qty_detail_timeline_produksi,
			          	);

			          	$this->Timeline_produksi_model->updatePO($get_detail2->no_po, $kurangiJumlahPO);

			          	write_log();

			          	$kurangiJumlahdetailPO = array( 'selisih_po_produksi' 		=> $get_detail2->selisih_po_produksi + $val_posi->qty_detail_timeline_produksi,
			          	);


			          	$this->Timeline_produksi_model->update_detailPO_by_bahan($get_detail2->no_po, $get_detail2->id_bahan_kemas, $kurangiJumlahdetailPO);

			          	write_log();	

			          	if ($val_posi->selisih_detail_timeline_produksi == 0) {
			          		$fix_posi = $val_posi->terpakai_detail_timeline_produksi;
			          	}else{
			          		$fix_posi = $val_posi->terpakai_detail_timeline_produksi + $val_posi->selisih_detail_timeline_produksi;
			          	}

		          		$bahan = $this->Timeline_produksi_model->get_bahan_by_id($get_detail2->id_bahan_kemas);
						$updateBahan = array( 'qty_bahan_kemas'	=> $bahan->qty_bahan_kemas - $fix_posi
											);
						
						$this->Timeline_produksi_model->update_produk($bahan->id_bahan_kemas, $updateBahan);	
		          	}	
				}

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
			    redirect('admin/timeline_produksi/timeline');	
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
		    	redirect('admin/timeline_produksi/timeline');
			}
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
		}
	}

	public function info($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['bahan_kemas'] 			= $this->Timeline_produksi_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 

		if($this->data['timeline'])
	    {
	    	// echo print_r($this->data['daftar_bahan_kemas']);
	    	$this->data['page_title'] = 'Create Detail Info: '.base64_decode($id);
	    	$this->data['action']     = 'admin/timeline_produksi/proses_info';
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

		    $this->load->view('back/timeline_produksi/timeline_produksi_info', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
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
          	$dataDetail[$n] 	= array(	'no_timeline_produksi'					=> $nomor_produksi,
          									'no_po'									=> $decode_po[$n],
											'id_jenis_timeline'						=> 1,
											'id_bahan_kemas'						=> $decode_id[$n],
											'start_date_detail_timeline_produksi'	=> $start_date,
											'end_date_detail_timeline_produksi'		=> $end_date,
											'qty_detail_timeline_produksi'			=> '',
											'harga_detail_timeline_produksi'		=> '',
											'ket_detail_timeline_produksi'			=> $keterangan,
									);

          	$this->Timeline_produksi_model->insert_detail($dataDetail[$n]);

          	write_log();

          	$this->Timeline_produksi_model->updated_time($nomor_produksi);

          	write_log();
        }

        $pesan = "Informasi Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function stok_pabrik($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 
	    $this->data['get_all_po']			= $this->Timeline_produksi_model->get_po_list(); 

		if($this->data['timeline'])
	    {
	    	// echo print_r($this->data['daftar_bahan_kemas']);
	    	$this->data['page_title'] = 'Stock Material to Industry: '.base64_decode($id);
	    	$this->data['action']     = 'admin/timeline_bahan/proses_stok';
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

		    $this->data['po'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'po',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->data['bahan'] = [
		    	'class'         => 'form-control select2bs4',
		    	'id'            => 'bahan',
		      	'required'      => '',
		      	'style' 		=> 'width:100%'
		    ];

		    $this->load->view('back/timeline_produksi/timeline_produksi_stok', $this->data);
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
	    }
	}

	public function stok_pabrik_proses()
	{
		// Ambil Data
		$i = $this->input;

		$date 			= $i->post('date');
		$no_po 			= $i->post('nomor_po');
		$nomor_produksi = $i->post('nomor_produksi');
		$keterangan	 	= $i->post('keterangan');
		$len 			= $i->post('length');
		$dt_id 			= $i->post('dt_id');
		$dt_po 			= $i->post('dt_po');
		$dt_qty			= $i->post('dt_qty');
		$dt_harga		= $i->post('dt_harga');
		$decode_id 		= json_decode($dt_id, TRUE);
		$decode_po 		= json_decode($dt_po, TRUE);
		$decode_qty		= json_decode($dt_qty, TRUE);
		$decode_harga	= json_decode($dt_harga, TRUE);

        for ($n2 = 0; $n2 < $len; $n2++) {
        	$row_stok_po = $this->Timeline_produksi_model->get_stok_by_po_bahan_produksi($decode_po[$n2], $decode_id[$n2], $nomor_produksi);
        	if ($row_stok_po) {
        		$tambah_qty = $row_stok_po->qty_stok_pabrik + $decode_qty[$n2];
        		$tambah_fix_sisa = $row_stok_po->fix_sisa_stok_pabrik + $decode_qty[$n2];

				$updateQtyStok = array( 'qty_stok_pabrik' 			=> $tambah_qty,
										'fix_sisa_stok_pabrik'	 	=> $tambah_fix_sisa
				);

				$this->Timeline_produksi_model->update_stok($row_stok_po->id_stok_pabrik, $updateQtyStok);

				$this->Timeline_produksi_model->updated_time($nomor_produksi);

	          	write_log();

	          	$get_detail2 = $this->Timeline_produksi_model->get_all_detail_po_by_po_bahan_row($decode_po[$n2], $decode_id[$n2]);
	          	if ($get_detail2) {
		          	$kurangiJumlahPO = array( 'total_selisih_po_produksi' 	=> $get_detail2->total_selisih_po_produksi - $decode_qty[$n2],
		          	);

		          	$this->Timeline_produksi_model->updatePO($get_detail2->no_po, $kurangiJumlahPO);

		          	write_log();

		          	$kurangiJumlahdetailPO = array( 'selisih_po_produksi' 		=> $get_detail2->selisih_po_produksi - $decode_qty[$n2],
		          	);

		          	$this->Timeline_produksi_model->update_detailPO_by_bahan($get_detail2->no_po, $get_detail2->id_bahan_kemas, $kurangiJumlahdetailPO);

		          	write_log();	
	          	}	
        	}else{
	        	$get_detail = $this->Timeline_produksi_model->get_all_detail_po_by_po_bahan_row($decode_po[$n2], $decode_id[$n2]);
				if ($get_detail) {
					$dataStok	= array(	'tgl_stok_pabrik'						=> $date,
											'no_timeline_produksi'					=> $nomor_produksi,
		  									'no_po'									=> $decode_po[$n2],
		  									'id_bahan_kemas'						=> $decode_id[$n2],
											'qty_stok_pabrik'						=> $decode_qty[$n2],
											'terpakai_stok_pabrik'					=> 0,
											'reject_stok_pabrik'					=> 0,
											'fix_sisa_stok_pabrik'					=> $decode_qty[$n2],
									);

		          	$this->Timeline_produksi_model->insert_stok($dataStok);

		          	write_log();

		          	$this->Timeline_produksi_model->updated_time($nomor_produksi);

		          	write_log();

		          	$get_detail2 = $this->Timeline_produksi_model->get_all_detail_po_by_po_bahan_row($decode_po[$n2], $decode_id[$n2]);
		          	if ($get_detail2) {
			          	$kurangiJumlahPO = array( 'total_selisih_po_produksi' 	=> $get_detail2->total_selisih_po_produksi - $decode_qty[$n2],
			          	);

			          	$this->Timeline_produksi_model->updatePO($get_detail2->no_po, $kurangiJumlahPO);

			          	write_log();

			          	$kurangiJumlahdetailPO = array( 'selisih_po_produksi' 		=> $get_detail2->selisih_po_produksi - $decode_qty[$n2],
			          	);

			          	$this->Timeline_produksi_model->update_detailPO_by_bahan($get_detail2->no_po, $get_detail2->id_bahan_kemas, $kurangiJumlahdetailPO);

			          	write_log();	
		          	}	
				}
        	}
        }

        $pesan = "Stok Bahan Kemas untuk Pabrik berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan,
    					'nopro' 	=> base64_encode($nomor_produksi)
    			);
    	echo json_encode($msg);
	}

	public function industry($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['cek_stok']				= $this->Timeline_produksi_model->get_stok_bahan_by_id(base64_decode($id));
		$this->data['bahan_kemas'] 			= $this->Timeline_produksi_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 
	    $this->data['get_all_po']			= $this->Timeline_produksi_model->get_po_stok_by_id_list(base64_decode($id)); 

	    if ($this->data['cek_stok']) {
		    if($this->data['timeline'])
		    {
		    	// echo print_r($this->data['daftar_bahan_kemas']);
		    	$this->data['page_title'] = 'Create Detail Industry: '.base64_decode($id);
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

			    $this->data['po'] = [
			    	'class'         => 'form-control select2bs4',
			    	'id'            => 'po',
			      	'required'      => '',
			      	'style' 		=> 'width:100%'
			    ];

			    $this->data['bahan'] = [
			    	'class'         => 'form-control select2bs4',
			    	'id'            => 'bahan',
			      	'required'      => '',
			      	'style' 		=> 'width:100%'
			    ];

			    $this->data['keterangan'] = [
			      'name'          => 'keterangan',
			      'id'            => 'keterangan',
			      'class'         => 'form-control',
			      'autocomplete'  => 'off'
			    ];

			    $this->load->view('back/timeline_produksi/timeline_produksi_industry', $this->data);
		    }else{
		    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
		    	redirect('admin/timeline_produksi/timeline');
		    }	
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Material of Production not available</div>');
	    	redirect('admin/timeline_produksi/timeline');
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
		$dt_stok		= $i->post('dt_stok');
		$dt_harga		= $i->post('dt_harga');
		$decode_id 		= json_decode($dt_id, TRUE);
		$decode_po 		= json_decode($dt_po, TRUE);
		$decode_qty		= json_decode($dt_qty, TRUE);
		$decode_stok	= json_decode($dt_stok, TRUE);
		$decode_harga	= json_decode($dt_harga, TRUE);
		// Bahan Kemas
		$len_bahan 			= $i->post('length_bahan');
		$dt_id_bahan 		= $i->post('dt_id_bahan');
		$dt_po_bahan 		= $i->post('dt_po_bahan');
		$dt_qty_bahan		= $i->post('dt_qty_bahan');
		$dt_harga_bahan		= $i->post('dt_harga_bahan');
		$decode_id_bahan 	= json_decode($dt_id_bahan, TRUE);
		$decode_po_bahan 	= json_decode($dt_po_bahan, TRUE);
		$decode_qty_bahan	= json_decode($dt_qty_bahan, TRUE);
		$decode_harga_bahan	= json_decode($dt_harga_bahan, TRUE);

		for ($n=0; $n < $len_bahan; $n++)
        {
			// $get_detail = $this->Timeline_produksi_model->get_all_detail_po_by_po_bahan_row($decode_po[$n], $decode_id[$n]);
			// if (!$get_detail) {
				$dataDetail	= array(	'no_timeline_produksi'					=> $nomor_produksi,
	  									'no_po'									=> $decode_po_bahan[$n],
										'id_jenis_timeline'						=> 2,
										'id_bahan_kemas'						=> $decode_id_bahan[$n],
										'start_date_detail_timeline_produksi'	=> $start_date,
										'end_date_detail_timeline_produksi'		=> $end_date,
										'qty_detail_timeline_produksi'			=> $dt_qty_bahan[$n],
										'harga_detail_timeline_produksi'		=> $dt_harga_bahan[$n],
										'ket_detail_timeline_produksi'			=> $keterangan,
								);

	          	$this->Timeline_produksi_model->insert_detail($dataDetail);

	          	write_log();

	          	$this->Timeline_produksi_model->updated_time($nomor_produksi);

	          	write_log();
			// }
        }

        $last_row = $this->Timeline_produksi_model->get_last_detail_timeline(2, $nomor_produksi);

        $dataPropo	= array(	'no_timeline_produksi'			=> $nomor_produksi,
        						'id_detail_timeline_produksi'	=> $last_row->id_detail_timeline_produksi
						);

      	$this->Timeline_produksi_model->insert_propo($dataPropo);

      	write_log();

      	$this->Timeline_produksi_model->updated_time($nomor_produksi);

      	write_log();

        for ($n2 = 0; $n2 < $len; $n2++) {
        	$get_detail = $this->Timeline_produksi_model->get_all_detail_po_by_po_bahan_row($decode_po[$n2], $decode_id[$n2]);
			if ($get_detail) {
				$dataPosi	= array(	'id_detail_timeline_produksi'			=> $last_row->id_detail_timeline_produksi,
										'no_timeline_produksi'					=> $nomor_produksi,
	  									'no_po'									=> $decode_po[$n2],
	  									'id_jenis_timeline'						=> 2,	
	  									'id_bahan_kemas'						=> $decode_id[$n2],
										'qty_detail_timeline_produksi'			=> $decode_stok[$n2],
										'qty_selisih_detail_timeline_produksi'	=> $decode_qty[$n2],
										'terpakai_detail_timeline_produksi'		=> '',
										'selisih_detail_timeline_produksi'		=> '',
										'sisa_detail_timeline_produksi'			=> '',
								);

	          	$this->Timeline_produksi_model->insert_posi($dataPosi);

	          	write_log();

	          	$this->Timeline_produksi_model->updated_time($nomor_produksi);

	          	write_log();

	          	$get_stok = $this->Timeline_produksi_model->get_stok_by_po_bahan_produksi($decode_po[$n2], $decode_id[$n2], $nomor_produksi);
	          	if ($get_stok) {
		          	$kurangiFixSisa = array( 'fix_sisa_stok_pabrik' 	=> $get_stok->fix_sisa_stok_pabrik - $decode_qty[$n2],
								          	 'terpakai_stok_pabrik' 	=> $get_stok->terpakai_stok_pabrik + $decode_qty[$n2],
		          	);

		          	$this->Timeline_produksi_model->update_stok($get_stok->id_stok_pabrik, $kurangiFixSisa);

		          	write_log();
	          	}	
			}
        }

        $pesan = "Produksi Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan,
    					'produksi'  => base64_encode($nomor_produksi) 
    			);
    	echo json_encode($msg);
	}

	public function edit_industry($id, $produksi)
	{
		$this->data['detail_timeline']	= $this->Timeline_produksi_model->get_all_timeline_produksi_by_id_detail(base64_decode($id));
		if ($this->data['detail_timeline']) {
			$cek_detail = $this->Timeline_produksi_model->check_detail_timeline_jenis_timeline(3);
			if (count($cek_detail) > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Production from Production Timeline must be deleted first</div>');
			  	redirect('admin/timeline_produksi/history/'.$produksi);
			}else{
				$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row($this->data['detail_timeline']->no_timeline_produksi);
				$this->data['bahan']				= $this->Timeline_produksi_model->get_bahan_posi_by_detail(base64_decode($id));
				$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
			    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 
				
				$this->data['page_title'] = 'Edit Detail Industry: '.$this->data['detail_timeline']->no_timeline_produksi;
		    	$this->data['action']     = 'admin/timeline_produksi/proses_edit_industry';
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

		     	$this->data['id_detail'] = [
	      	 		'name'          => 'id_detail',	
				  	'id' 			=> 'id-detail', 
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

			    $this->load->view('back/timeline_produksi/timeline_produksi_industry_edit', $this->data);
			}	
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
		}
	}

	function proses_edit_industry()
	{
		// Ambil Data
		$i = $this->input;

		$start_date 	= substr($i->post('date'), 0, 10);
		$end_date 		= substr($i->post('date'), 13, 24);
		$id_detail 		= $i->post('id_detail');
		$nomor_produksi = $i->post('nomor_produksi');
		$keterangan 	= $i->post('keterangan');

		// Get Data Reject
		$len_dr 			= $i->post('length_dr');
		$dr_id 				= $i->post('dr_id');
		$dr_id_posi 		= $i->post('dr_id_posi');
		$dr_id_detail		= $i->post('dr_id_detail');
		$dr_po 				= $i->post('dr_po');
		$dr_qty 			= $i->post('dr_qty');
		$dr_stok 			= $i->post('dr_stok');
		$dr_sisa 			= $i->post('dr_sisa');
		$dr_terpakai		= $i->post('dr_terpakai');
		$dr_selisih			= $i->post('dr_selisih');
		$decode_dr_id_detail= json_decode($dr_id_detail, TRUE);
		$decode_dr_id 		= json_decode($dr_id, TRUE);
		$decode_dr_id_posi	= json_decode($dr_id_posi, TRUE);
		$decode_dr_po 		= json_decode($dr_po, TRUE);
		$decode_dr_qty		= json_decode($dr_qty, TRUE);
		$decode_dr_stok		= json_decode($dr_stok, TRUE);
		$decode_dr_sisa		= json_decode($dr_sisa, TRUE);
		$decode_dr_terpakai	= json_decode($dr_terpakai, TRUE);
		$decode_dr_selisih 	= json_decode($dr_selisih, TRUE);

		// Update Detail Timeline
		$dataDetail	= array(	'start_date_detail_timeline_produksi'	=> $start_date,
								'end_date_detail_timeline_produksi'		=> $end_date,
								'ket_detail_timeline_produksi'			=> $keterangan,
						);

      	$this->Timeline_produksi_model->updateDetailTimeline($id_detail, $dataDetail);

      	write_log();

      	$this->Timeline_produksi_model->updated_time($nomor_produksi);

      	write_log();

      	$cek_posi = $this->Timeline_produksi_model->get_bahan_posi_by_detail($id_detail);

      	foreach ($cek_posi as $val_posi) {
      		$data_posi = $this->Timeline_produksi_model->get_posi_by_po_bahan_produksi($val_posi->no_po, $val_posi->id_bahan_kemas, $val_posi->no_timeline_produksi);
      		$data_stok = $this->Timeline_produksi_model->get_stok_by_po_bahan_produksi($val_posi->no_po, $val_posi->id_bahan_kemas, $val_posi->no_timeline_produksi);

      		$UpdateStok = array( 'fix_sisa_stok_pabrik'	=> $data_stok->fix_sisa_stok_pabrik + $data_posi->qty_selisih_detail_timeline_produksi,
      							 'terpakai_stok_pabrik'	=> $data_stok->terpakai_stok_pabrik - $data_posi->qty_selisih_detail_timeline_produksi,
      		);

      		$this->Timeline_produksi_model->update_stok($data_stok->id_stok_pabrik, $UpdateStok);

      		write_log();

	      	$this->Timeline_produksi_model->updated_time($nomor_produksi);

	      	write_log();
      	}

      	for ($r = 0; $r < $len_dr; $r++) {
      		$data_posi = $this->Timeline_produksi_model->get_posi_by_po_bahan_produksi($decode_dr_po[$r], $decode_dr_id[$r], $nomor_produksi);
      		$data_stok = $this->Timeline_produksi_model->get_stok_by_po_bahan_produksi($decode_dr_po[$r], $decode_dr_id[$r], $nomor_produksi);

      		$UpdateStok = array( 'fix_sisa_stok_pabrik'	=> $data_stok->fix_sisa_stok_pabrik - $decode_dr_qty[$r],
      							 'terpakai_stok_pabrik'	=> $data_stok->terpakai_stok_pabrik + $decode_dr_qty[$r]
      		);

      		$this->Timeline_produksi_model->update_stok($data_stok->id_stok_pabrik, $UpdateStok);

      		write_log();

      		$this->Timeline_produksi_model->updated_time($nomor_produksi);

	      	write_log();

      		$UpdatePosi = array( 'qty_detail_timeline_produksi'		=> $decode_dr_stok[$r],
					      		 'qty_selisih_detail_timeline_produksi'	=> $decode_dr_qty[$r],	      		
	      	);

	      	$this->Timeline_produksi_model->update_posi($decode_dr_id_posi[$r], $UpdatePosi);

      		write_log();

	      	$this->Timeline_produksi_model->updated_time($nomor_produksi);

	      	write_log();
      	}

        $pesan = "Produksi Timeline berhasil diubah!";	
    	$msg = array(	'sukses'	=> $pesan,
    					'produksi'  => base64_encode($nomor_produksi) 
    			);
    	echo json_encode($msg);
	}

	public function delivery($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['bahan_kemas'] 			= $this->Timeline_produksi_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['reject']				= $this->Timeline_produksi_model->get_bahan_posi_by_bahan(base64_decode($id));
		$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 
	    $this->data['cek_produksi']			= $this->Timeline_produksi_model->check_detail_timeline_jenis_timeline(2);
	    $this->data['cek_pengiriman']		= $this->Timeline_produksi_model->check_detail_timeline_jenis_timeline(3);

	    // echo count($this->data['reject'])."<br>";
	    // echo print_r($this->data['reject'])."<br>";

	    if (count($this->data['cek_produksi']) == count($this->data['cek_pengiriman'])) {
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Production Timeline data does not exist. Create new data!</div>');
	    	redirect('admin/timeline_produksi/timeline');
	    }else{
		    if($this->data['timeline'])
		    {
		    	// if ($this->data['timeline']->total_bahan_jadi <= $this->data['timeline']->total_bahan) {
			    	$this->data['page_title'] = 'Create Detail Delivery: '.base64_decode($id);
			    	$this->data['action']     = 'admin/timeline_produksi/proses_delivery';
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

				    $this->load->view('back/timeline_produksi/timeline_produksi_delivery', $this->data);	
		    	// }else{
		    	// 	$this->session->set_flashdata('message', '<div class="alert alert-danger">Timeline: '.$this->data['timeline']->no_timeline_bahan.'. Order has been sent</div>');
			    // 	redirect('admin/timeline_bahan/timeline');
		    	// }
		    }else{
		    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
		    	redirect('admin/timeline_produksi/timeline');
		    }
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
		$detail 		= $this->Timeline_produksi_model->get_detail_all_by_timeline_produksi_row($nomor_produksi);

		for ($y=0; $y < $len; $y++)
        {
           $total_pesan		= $total_pesan + $decode_qty[$y];
        }

        // echo print_r($detail);

		for ($n=0; $n < $len; $n++)
        {
        	$updatePO = array(	'total_selisih_po_produksi'	=> $detail->total_selisih_po_produksi + $total_pesan
        	);

        	$this->Timeline_produksi_model->updatePO($decode_po[$n], $updatePO);

        	write_log();

        	$updateTimeline = array(	'total_produksi_jadi'	=> $detail->total_produksi_jadi + $total_pesan
        	);
        	$this->Timeline_produksi_model->updateTimeline($nomor_produksi, $updateTimeline);

        	write_log();

        	$cariDetailPO[$n] = $this->Timeline_produksi_model->get_detail_po_by_po_bahan_row($decode_po[$n], $decode_id[$n]);
        	$updateDetailPO[$n] = array( 'selisih_po_produksi'	=> $cariDetailPO[$n]->selisih_po_produksi + $decode_qty[$n]
        	);
        	
        	
        	$this->Timeline_produksi_model->update_detailPO_by_bahan($decode_po[$n], $decode_id[$n], $updateDetailPO[$n]);

        	write_log();

          	$dataDetail[$n] 	= array(	'no_timeline_produksi'					=> $nomor_produksi,
          									'no_po'									=> $decode_po[$n],
											'id_jenis_timeline'						=> 3,
											'id_bahan_kemas'						=> $decode_id[$n],
											'start_date_detail_timeline_produksi'	=> $start_date,
											'end_date_detail_timeline_produksi'		=> $end_date,
											'qty_detail_timeline_produksi'			=> $decode_qty[$n],
											'harga_detail_timeline_produksi'		=> '',
											'ket_detail_timeline_produksi'			=> $keterangan,
									);

          	$this->Timeline_produksi_model->insert_detail($dataDetail[$n]);

          	write_log();

          	$this->Timeline_produksi_model->updated_time($nomor_produksi);

          	write_log();
        }

        $pesan = "Pengiriman Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan,
    					'produksi' 	=> base64_encode($nomor_produksi) 
    			);
    	echo json_encode($msg);
	}

	public function add_posi_delivery($id)
	{
		is_create();
		$this->data['detail_timeline']	= $this->Timeline_produksi_model->get_all_timeline_produksi_by_id_detail(base64_decode($id));
		if ($this->data['detail_timeline']) {
			
			$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row($this->data['detail_timeline']->no_timeline_produksi);
			$this->data['reject']				= $this->Timeline_produksi_model->get_stok_bahan_by_id($this->data['detail_timeline']->no_timeline_produksi);
			$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
		    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 

	    	// if ($this->data['timeline']->total_bahan_jadi <= $this->data['timeline']->total_bahan) {
		    	$this->data['page_title'] = 'Add Material to Detail Delivery: '.$this->data['detail_timeline']->no_timeline_produksi;
		    	$this->data['action']     = 'admin/timeline_produksi/proses_add_posi_delivery';
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

		     	$this->data['id_detail'] = [
	      	 		'name'          => 'id_detail',	
				  	'id' 			=> 'id-detail', 
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

			    $this->load->view('back/timeline_produksi/timeline_produksi_delivery_add_posi', $this->data);	
	    	// }else{
	    	// 	$this->session->set_flashdata('message', '<div class="alert alert-danger">Timeline: '.$this->data['timeline']->no_timeline_bahan.'. Order has been sent</div>');
		    // 	redirect('admin/timeline_bahan/timeline');
	    	// }
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
		}
	}

	function proses_add_posi_delivery()
	{
		// Ambil Data
		$i = $this->input;

		$start_date 	= substr($i->post('date'), 0, 10);
		$end_date 		= substr($i->post('date'), 13, 24);
		$id_detail 		= $i->post('id_detail');
		$nomor_produksi = $i->post('nomor_produksi');
		$keterangan 	= $i->post('keterangan');

		// Get Data Reject
		$len_dr 			= $i->post('length_dr');
		$dr_id 				= $i->post('dr_id');
		$dr_id_detail		= $i->post('dr_id_detail');
		$dr_po 				= $i->post('dr_po');
		$dr_qty 			= $i->post('dr_qty');
		$dr_sisa 			= $i->post('dr_sisa');
		$dr_sisa_fix 		= $i->post('dr_sisa_fix');
		$dr_terpakai		= $i->post('dr_terpakai');
		$dr_selisih			= $i->post('dr_selisih');
		$decode_dr_id_detail= json_decode($dr_id_detail, TRUE);
		$decode_dr_id 		= json_decode($dr_id, TRUE);
		$decode_dr_po 		= json_decode($dr_po, TRUE);
		$decode_dr_qty		= json_decode($dr_qty, TRUE);
		$decode_dr_sisa		= json_decode($dr_sisa, TRUE);
		$decode_dr_sisa_fix	= json_decode($dr_sisa_fix, TRUE);
		$decode_dr_terpakai	= json_decode($dr_terpakai, TRUE);
		$decode_dr_selisih 	= json_decode($dr_selisih, TRUE);

		// Update Detail Timeline
		$dataDetail	= array(	'start_date_detail_timeline_produksi'	=> $start_date,
								'end_date_detail_timeline_produksi'		=> $end_date,
								'ket_detail_timeline_produksi'			=> $keterangan,
						);

      	$this->Timeline_produksi_model->updateDetailTimeline($id_detail, $dataDetail);

      	write_log();

      	$this->Timeline_produksi_model->updated_time($nomor_produksi);

      	write_log();

		// Insert Propo
        $dataPropo	= array(	'no_timeline_produksi'			=> $nomor_produksi,
        						'id_detail_timeline_produksi'	=> $id_detail
						);

      	$this->Timeline_produksi_model->insert_propo($dataPropo);

      	write_log();

      	$this->Timeline_produksi_model->updated_time($nomor_produksi);

      	write_log();

        for ($r = 0; $r < $len_dr; $r++) {

        	// Insert Posi
        	$dataPosi	= array(	'id_detail_timeline_produksi'			=> $id_detail,
									'no_timeline_produksi'					=> $nomor_produksi,
  									'no_po'									=> $decode_dr_po[$r],
  									'id_jenis_timeline'						=> 3,	
  									'id_bahan_kemas'						=> $decode_dr_id[$r],
									'qty_detail_timeline_produksi'			=> $decode_dr_qty[$r],
									'qty_selisih_detail_timeline_produksi'	=> $decode_dr_sisa[$r],
									'terpakai_detail_timeline_produksi'		=> $decode_dr_terpakai[$r],
									'selisih_detail_timeline_produksi'		=> $decode_dr_selisih[$r],
									'sisa_detail_timeline_produksi'			=> $decode_dr_sisa_fix[$r],
							);

          	$this->Timeline_produksi_model->insert_posi($dataPosi);

          	write_log();

          	$this->Timeline_produksi_model->updated_time($nomor_produksi);

          	write_log();

          	$get_stok = $this->Timeline_produksi_model->get_stok_by_po_bahan_produksi($decode_dr_po[$r], $decode_dr_id[$r], $nomor_produksi);
          	if ($get_stok) {
	          	$kurangiFixSisa = array( 'fix_sisa_stok_pabrik' 	=> $get_stok->fix_sisa_stok_pabrik - $decode_dr_selisih[$r],
							          	 'reject_stok_pabrik' 		=> $get_stok->reject_stok_pabrik + $decode_dr_selisih[$r],
	          	);

	          	$this->Timeline_produksi_model->update_stok($get_stok->id_stok_pabrik, $kurangiFixSisa);

	          	write_log();
          	}	

        }

        $pesan = "Pengiriman Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan,
    					'produksi'  => base64_encode($nomor_produksi) 
    			);
    	echo json_encode($msg);
	}

	public function edit_posi_delivery($id)
	{
		is_update();
		$this->data['detail_timeline']	= $this->Timeline_produksi_model->get_all_timeline_produksi_by_id_detail(base64_decode($id));
		if ($this->data['detail_timeline']) {
			$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row($this->data['detail_timeline']->no_timeline_produksi);
			$this->data['bahan']				= $this->Timeline_produksi_model->get_bahan_posi_by_detail(base64_decode($id));
			$this->data['reject']				= $this->Timeline_produksi_model->get_stok_bahan_by_id($this->data['detail_timeline']->no_timeline_produksi);
			$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
		    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 
			
			$this->data['page_title'] = 'Edit Material to Detail Delivery: '.$this->data['detail_timeline']->no_timeline_produksi;
	    	$this->data['action']     = 'admin/timeline_produksi/proses_edit_posi_delivery';
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

	     	$this->data['id_detail'] = [
      	 		'name'          => 'id_detail',	
			  	'id' 			=> 'id-detail', 
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

		    $this->load->view('back/timeline_produksi/timeline_produksi_delivery_edit_posi', $this->data);	
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
		}
	}

	function proses_edit_posi_delivery()
	{
		// Ambil Data
		$i = $this->input;

		// $start_date 	= substr($i->post('date'), 0, 10);
		// $end_date 		= substr($i->post('date'), 13, 24);
		$id_detail 		= $i->post('id_detail');
		$nomor_produksi = $i->post('nomor_produksi');

		// Get Data Reject
		$len_dr 			= $i->post('length_dr');
		$dr_id 				= $i->post('dr_id');
		$dr_id_posi 		= $i->post('dr_id_posi');
		$dr_id_detail		= $i->post('dr_id_detail');
		$dr_po 				= $i->post('dr_po');
		$dr_qty 			= $i->post('dr_qty');
		$dr_sisa 			= $i->post('dr_sisa');
		$dr_terpakai		= $i->post('dr_terpakai');
		$dr_selisih			= $i->post('dr_selisih');
		$decode_dr_id_detail= json_decode($dr_id_detail, TRUE);
		$decode_dr_id 		= json_decode($dr_id, TRUE);
		$decode_dr_id_posi	= json_decode($dr_id_posi, TRUE);
		$decode_dr_po 		= json_decode($dr_po, TRUE);
		$decode_dr_qty		= json_decode($dr_qty, TRUE);
		$decode_dr_sisa		= json_decode($dr_sisa, TRUE);
		$decode_dr_terpakai	= json_decode($dr_terpakai, TRUE);
		$decode_dr_selisih 	= json_decode($dr_selisih, TRUE);

		$get_propo = $this->Timeline_produksi_model->get_popro_posi($id_detail);
		// echo print_r($get_propo)."<br>";
		if ($get_propo) {
			foreach ($get_propo as $val_propo) {
				$row_posi_po = $this->Timeline_produksi_model->get_posi_by_po_bahan_produksi($val_propo->no_po, $val_propo->id_bahan_kemas, $val_propo->no_timeline_produksi);

				if ($row_posi_po->qty_detail_timeline_produksi == 0) {
					if ($val_propo->sisa_detail_timeline_produksi == $val_propo->selisih_detail_timeline_produksi) {
		        		$tambah_qty = ($row_posi_po->qty_detail_timeline_produksi + $val_propo->terpakai_detail_timeline_produksi) + $val_propo->sisa_detail_timeline_produksi;
		        	}else if ($val_propo->sisa_detail_timeline_produksi != 0 && $val_propo->selisih_detail_timeline_produksi != 0) {
		        		$tambah_qty = $row_posi_po->qty_detail_timeline_produksi + ($val_propo->terpakai_detail_timeline_produksi + $val_propo->sisa_detail_timeline_produksi);
		        	}else{
		        		$tambah_qty = $row_posi_po->qty_detail_timeline_produksi + ($val_propo->terpakai_detail_timeline_produksi + $val_propo->sisa_detail_timeline_produksi);
		        	}	
				}else{
					if ($val_propo->sisa_detail_timeline_produksi == $val_propo->selisih_detail_timeline_produksi) {
		        		$tambah_qty = ($row_posi_po->qty_detail_timeline_produksi + $val_propo->terpakai_detail_timeline_produksi) + $val_propo->sisa_detail_timeline_produksi;
		        	}else if ($val_propo->sisa_detail_timeline_produksi != 0 && $val_propo->selisih_detail_timeline_produksi != 0) {
		        		$tambah_qty = $row_posi_po->qty_detail_timeline_produksi + ($val_propo->terpakai_detail_timeline_produksi + $val_propo->selisih_detail_timeline_produksi);
		        	}else{
		        		$tambah_qty = $row_posi_po->qty_detail_timeline_produksi + $val_propo->terpakai_detail_timeline_produksi;
		        	}	
				}

				// $tambah_qty = $row_posi_po->qty_detail_timeline_produksi + ($val_propo->terpakai_detail_timeline_produksi + ($val_propo->sisa_detail_timeline_produksi - $val_propo->selisih_detail_timeline_produksi));
				$kurang_terpakai = $row_posi_po->terpakai_detail_timeline_produksi - $val_propo->terpakai_detail_timeline_produksi;
				$tambah_sisa = $row_posi_po->sisa_detail_timeline_produksi + $val_propo->terpakai_detail_timeline_produksi;
				$kurang_selisih = $row_posi_po->selisih_detail_timeline_produksi - $val_propo->selisih_detail_timeline_produksi;

				$updateQtyPosi = array( 'qty_detail_timeline_produksi' 		=> $tambah_qty,
										'terpakai_detail_timeline_produksi' => $kurang_terpakai,
										'sisa_detail_timeline_produksi' 	=> $tambah_sisa,
										'selisih_detail_timeline_produksi' 	=> $kurang_selisih,
				);

				$this->Timeline_produksi_model->update_posi($row_posi_po->id_posi_data_access, $updateQtyPosi);
			}	
		}
		// Insert Propo
    //     $dataPropo	= array(	'no_timeline_produksi'			=> $nomor_produksi,
    //     						'id_detail_timeline_produksi'	=> $id_detail
				// 		);

    //   	$this->Timeline_produksi_model->insert_propo($dataPropo);

    //   	write_log();

        for ($r = 0; $r < $len_dr; $r++) {
        	// Insert Posi
        	$dataPosi	= array(	'qty_detail_timeline_produksi'			=> $decode_dr_qty[$r],
									'qty_selisih_detail_timeline_produksi'	=> $decode_dr_qty[$r],
									'terpakai_detail_timeline_produksi'		=> $decode_dr_terpakai[$r],
									'selisih_detail_timeline_produksi'		=> $decode_dr_selisih[$r],
									'sisa_detail_timeline_produksi'			=> $decode_dr_sisa[$r],
							);

			// echo print_r($dataPosi)."<br>";

          	$this->Timeline_produksi_model->update_posi($decode_dr_id_posi[$r] , $dataPosi);    	

          	write_log();

        	$get_detail_bahan = $this->Timeline_produksi_model->get_bahan_posi_by_bahan_id($nomor_produksi, $decode_dr_id[$r]);

        	if ($decode_dr_sisa[$r] == $decode_dr_selisih[$r]) {
        		$sisa_qty = ($get_detail_bahan->qty_detail_timeline_produksi - $decode_dr_terpakai[$r]) - $decode_dr_sisa[$r];
        	}else if ($decode_dr_sisa[$r] != 0 && $decode_dr_selisih[$r] != 0) {
        		$sisa_qty = (($get_detail_bahan->qty_detail_timeline_produksi - $decode_dr_terpakai[$r]) - $decode_dr_sisa[$r]) + ($decode_dr_sisa[$r] - $decode_dr_selisih[$r]);
        	}else{
        		$sisa_qty = (($get_detail_bahan->qty_detail_timeline_produksi - $decode_dr_terpakai[$r]) - ($decode_dr_sisa[$r] + $decode_dr_selisih[$r])) + ($decode_dr_sisa[$r] - $decode_dr_selisih[$r]);
        	}

        	if ($decode_dr_terpakai[$r] == 0) {
        		$terpakai_qty = $get_detail_bahan->terpakai_detail_timeline_produksi + 0;
        		$sisa_nambah_qty = $get_detail_bahan->sisa_detail_timeline_produksi + 0;		
        	}else{
        		$terpakai_qty = $get_detail_bahan->terpakai_detail_timeline_produksi + $decode_dr_terpakai[$r];
				$sisa_nambah_qty = $get_detail_bahan->sisa_detail_timeline_produksi - $decode_dr_terpakai[$r];
           	}



        	if ($decode_dr_selisih[$r] == 0) {
        		$selisih_qty = $get_detail_bahan->selisih_detail_timeline_produksi + 0;        		
        	}else{
        		$selisih_qty = $get_detail_bahan->selisih_detail_timeline_produksi + $decode_dr_selisih[$r];
        	}	

        	$updateDataPosi = array(	'qty_detail_timeline_produksi'		=> $sisa_qty,
        								'terpakai_detail_timeline_produksi'	=> $terpakai_qty,
        								'sisa_detail_timeline_produksi'		=> $sisa_nambah_qty,
        								'selisih_detail_timeline_produksi'	=> $selisih_qty,
        	);
	        
			// echo print_r($updateDataPosi)."<br>";

	        $this->Timeline_produksi_model->update_posi($get_detail_bahan->id_posi_data_access, $updateDataPosi);

	        write_log();
        }

        $pesan = "Pengiriman Timeline berhasil diubah!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function retur($id)
	{
		is_create();

		$this->data['timeline'] 			= $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['bahan_kemas'] 			= $this->Timeline_produksi_model->get_bahan_by_po($this->data['timeline']->no_po);
		$this->data['get_all_sku'] 			= $this->Timeline_produksi_model->get_all_sku();
	    $this->data['get_all_kategori']		= $this->Timeline_produksi_model->get_all_kategori(); 

		if($this->data['timeline'])
	    {
	    	if (!$this->data['timeline']->total_produksi_jadi == 0) {
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

			    $this->load->view('back/timeline_produksi/timeline_produksi_retur', $this->data);	
	    	}else{
	    		$this->session->set_flashdata('message', '<div class="alert alert-danger">Timeline: '.$this->data['timeline']->no_timeline_produksi.'. Order not sent</div>');
		    	redirect('admin/timeline_produksi/timeline');
	    	}
	    }else{
	    	$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	    	redirect('admin/timeline_produksi/timeline');
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
		$detail 		= $this->Timeline_produksi_model->get_detail_all_by_timeline_produksi_row($nomor_produksi);

		for ($y=0; $y < $len; $y++)
        {
           $total_pesan		= $total_pesan + $decode_qty[$y];
        }

        // echo print_r($detail);

		for ($n=0; $n < $len; $n++)
        {
        	$updatePO = array(	'total_selisih_po_produksi'	=> $detail->total_selisih_po_produksi - $total_pesan
        	);

        	$this->Timeline_produksi_model->updatePO($decode_po[$n], $updatePO);

        	write_log();

        	$updateTimeline = array(	'total_produksi_jadi'	=> $detail->total_produksi_jadi - $total_pesan
        	);
        	$this->Timeline_produksi_model->updateTimeline($nomor_produksi, $updateTimeline);

        	write_log();

        	$cariDetailPO[$n] = $this->Timeline_produksi_model->get_detail_po_by_po_bahan_row($decode_po[$n], $decode_id[$n]);
        	$updateDetailPO[$n] = array( 'selisih_po_produksi'	=> $cariDetailPO[$n]->selisih_po_produksi - $decode_qty[$n]
        	);
        	$harga_detail = $cariDetailPO[$n]->harga_po * $decode_qty[$n];
        	$fix_ket = $keterangan.'. Total Harga Retur: Rp.'.number_format($harga_detail);
        	
        	
        	$this->Timeline_produksi_model->update_detailPO_by_bahan($decode_po[$n], $decode_id[$n], $updateDetailPO[$n]);

        	write_log();

          	$dataDetail[$n] 	= array(	'no_timeline_produksi'					=> $nomor_produksi,
          									'no_po'									=> $decode_po[$n],
											'id_jenis_timeline'						=> 4,
											'id_bahan_kemas'						=> $decode_id[$n],
											'start_date_detail_timeline_produksi'	=> $start_date,
											'end_date_detail_timeline_produksi'		=> $end_date,
											'qty_detail_timeline_produksi'			=> $decode_qty[$n],
											'harga_detail_timeline_produksi'		=> $harga_detail,
											'ket_detail_timeline_produksi'			=> $fix_ket,
									);

          	$this->Timeline_produksi_model->insert_detail($dataDetail[$n]);

          	write_log();

          	$this->Timeline_produksi_model->updated_time($nomor_produksi);

          	write_log();
        }

        $pesan = "Retur Timeline berhasil dibuat!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}	

	public function hapus_hpp($id)
	{
		is_delete();

		$cek_hpp = $this->Timeline_produksi_model->get_all_hpp_by_id(base64_decode($id));
		if ($cek_hpp) {
			$nomor_produksi = $cek_hpp->no_timeline_produksi;
			$no_po 			= str_replace("TML","PO",$nomor_produksi);

			$updateTimeline = array( 'status_timeline_produksi' => 1,
			);

			$this->Timeline_produksi_model->update($nomor_produksi, $updateTimeline);

			write_log();

			$detailPO = $this->Timeline_produksi_model->get_bahan_by_po($no_po);
			foreach ($detailPO as $val_detail) {
				$bahan = $this->Timeline_produksi_model->get_bahan_by_id($val_detail->id_bahan_kemas);
				$updateBahan = array( 'qty_bahan_kemas'	=> $bahan->qty_bahan_kemas + $val_detail->selisih_po_produksi);

				
				$this->Timeline_produksi_model->update_produk($bahan->id_bahan_kemas, $updateBahan);
			}

			$this->Timeline_produksi_model->delete_detail_hpp($cek_hpp->id_hpp_produk);
			
			write_log();

			$this->Timeline_produksi_model->delete_hpp($cek_hpp->id_hpp_produk);
			
			write_log();

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
			redirect('admin/timeline_produksi/data_hpp');
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  	redirect('admin/timeline_produksi/data_hpp');
		}
	}

	public function hapus_detail($id)
	{
		is_delete();

		$this->data['detail'] 	= $this->Timeline_produksi_model->get_detail_all_by_timeline_row(base64_decode($id));
		// echo print_r($this->data['detail'])."<br>";
		if(isset($this->data['detail']))
		{
			if ($this->data['detail']->id_jenis_timeline == 4) {
				$no_produksi_hapus = $this->data['detail']->no_timeline_produksi;
				$updatePO = array(	'total_selisih_po_produksi'	=> $this->data['detail']->total_selisih_po_produksi + $this->data['detail']->qty_detail_timeline_produksi
	        	);
	        	$this->Timeline_produksi_model->updatePO($this->data['detail']->no_po, $updatePO);

	        	write_log();

	        	$updateTimeline = array(	'total_produksi_jadi'	=> $this->data['detail']->total_produksi_jadi + $this->data['detail']->qty_detail_timeline_produksi
	        	);
	        	$this->Timeline_produksi_model->updateTimeline($this->data['detail']->no_timeline_produksi, $updateTimeline);

	        	write_log();

	        	$updateDetailPO= array( 'selisih_po_produksi'	=> $this->data['detail']->selisih_po_produksi + $this->data['detail']->qty_detail_timeline_produksi
	        	);
	        	
	        	$this->Timeline_produksi_model->update_detailPO_by_bahan($this->data['detail']->no_po, $this->data['detail']->bahan_id, $updateDetailPO);

	        	write_log();
				// print_r($this->data['detail']);
				$this->Timeline_produksi_model->delete_detail(base64_decode($id));
				write_log();

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
				redirect('admin/timeline_produksi/history/'.base64_encode($no_produksi_hapus));
			}else if($this->data['detail']->id_jenis_timeline == 3){
				$no_produksi_hapus = $this->data['detail']->no_timeline_produksi;
				$updatePO = array(	'total_selisih_po_produksi'	=> $this->data['detail']->total_selisih_po_produksi - $this->data['detail']->qty_detail_timeline_produksi
	        	);

	        	$this->Timeline_produksi_model->updatePO($this->data['detail']->no_po, $updatePO);

	        	write_log();

	        	$updateTimeline = array(	'total_produksi_jadi'	=> $this->data['detail']->total_produksi_jadi - $this->data['detail']->qty_detail_timeline_produksi
	        	);

	        	$this->Timeline_produksi_model->updateTimeline($this->data['detail']->no_timeline_produksi, $updateTimeline);

	        	write_log();

	        	$updateDetailPO= array( 'selisih_po_produksi'	=> $this->data['detail']->selisih_po_produksi - $this->data['detail']->qty_detail_timeline_produksi
	        	);

	        	$this->Timeline_produksi_model->update_detailPO_by_bahan($this->data['detail']->no_po, $this->data['detail']->bahan_id, $updateDetailPO);

	        	write_log();

	        	$get_propo = $this->Timeline_produksi_model->get_popro_posi(base64_decode($id));
				// echo print_r($get_propo)."<br>";
				if ($get_propo) {
					foreach ($get_propo as $val_propo) {
						$get_stok = $this->Timeline_produksi_model->get_stok_by_po_bahan_produksi($val_propo->no_po, $val_propo->id_bahan_kemas, $val_propo->no_timeline_produksi);

						$row_posi_po = $this->Timeline_produksi_model->get_posi_by_po_bahan_produksi_detail($val_propo->no_po, $val_propo->id_bahan_kemas, $val_propo->no_timeline_produksi, $val_propo->id_posi_data_access);


			          	if ($get_stok || $row_posi_po) {
				          	$kurangiFixSisa = array( 'fix_sisa_stok_pabrik' 	=> $get_stok->fix_sisa_stok_pabrik + $row_posi_po->selisih_detail_timeline_produksi,
									          		 'reject_stok_pabrik' 		=> $get_stok->reject_stok_pabrik - $row_posi_po->selisih_detail_timeline_produksi,
				          	);

				          	$this->Timeline_produksi_model->update_stok($get_stok->id_stok_pabrik, $kurangiFixSisa);

				          	write_log();
			          	}

						// if ($row_posi_po->qty_detail_timeline_produksi == 0) {
						// 	if ($val_propo->sisa_detail_timeline_produksi == $val_propo->selisih_detail_timeline_produksi) {
				  //       		$tambah_qty = ($row_posi_po->qty_detail_timeline_produksi + $val_propo->terpakai_detail_timeline_produksi) + $val_propo->sisa_detail_timeline_produksi;
				  //       	}else if ($val_propo->sisa_detail_timeline_produksi != 0 && $val_propo->selisih_detail_timeline_produksi != 0) {
				  //       		$tambah_qty = $row_posi_po->qty_detail_timeline_produksi + ($val_propo->terpakai_detail_timeline_produksi + $val_propo->sisa_detail_timeline_produksi);
				  //       	}else{
				  //       		$tambah_qty = $row_posi_po->qty_detail_timeline_produksi + ($val_propo->terpakai_detail_timeline_produksi + $val_propo->sisa_detail_timeline_produksi);
				  //       	}	
						// }else{
						// 	if ($val_propo->sisa_detail_timeline_produksi == $val_propo->selisih_detail_timeline_produksi) {
				  //       		$tambah_qty = ($row_posi_po->qty_detail_timeline_produksi + $val_propo->terpakai_detail_timeline_produksi) + $val_propo->sisa_detail_timeline_produksi;
				  //       	}else if ($val_propo->sisa_detail_timeline_produksi != 0 && $val_propo->selisih_detail_timeline_produksi != 0) {
				  //       		$tambah_qty = $row_posi_po->qty_detail_timeline_produksi + ($val_propo->terpakai_detail_timeline_produksi + $val_propo->selisih_detail_timeline_produksi);
				  //       	}else{
				  //       		$tambah_qty = $row_posi_po->qty_detail_timeline_produksi + $val_propo->terpakai_detail_timeline_produksi;
				  //       	}	
						// }

						// // $tambah_qty = $row_posi_po->qty_detail_timeline_produksi + ($val_propo->terpakai_detail_timeline_produksi + ($val_propo->sisa_detail_timeline_produksi - $val_propo->selisih_detail_timeline_produksi));
						// $kurang_terpakai = $row_posi_po->terpakai_detail_timeline_produksi - $val_propo->terpakai_detail_timeline_produksi;
						// $tambah_sisa = $row_posi_po->sisa_detail_timeline_produksi + $val_propo->terpakai_detail_timeline_produksi;
						// $kurang_selisih = $row_posi_po->selisih_detail_timeline_produksi - $val_propo->selisih_detail_timeline_produksi;

						// $updateQtyPosi = array( 'qty_detail_timeline_produksi' 		=> $tambah_qty,
						// 						'terpakai_detail_timeline_produksi' => $kurang_terpakai,
						// 						'sisa_detail_timeline_produksi' 	=> $tambah_sisa,
						// 						'selisih_detail_timeline_produksi' 	=> $kurang_selisih,
						// );

						// $this->Timeline_produksi_model->update_posi($row_posi_po->id_posi_data_access, $updateQtyPosi);
					}	
				}

				$this->Timeline_produksi_model->delete_propo_by_detail_timeline(base64_decode($id));

				write_log();

				$this->Timeline_produksi_model->delete_posi_by_detail_timeline(base64_decode($id));

				write_log();

				$this->Timeline_produksi_model->delete_detail(base64_decode($id));
				write_log();

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
				redirect('admin/timeline_produksi/history/'.base64_encode($no_produksi_hapus));
				
			}else if ($this->data['detail']->id_jenis_timeline == 2) {
				$no_produksi_hapus = $this->data['detail']->no_timeline_produksi;
				$cek_detail = $this->Timeline_produksi_model->check_detail_timeline_jenis_timeline(3);
				if ($cek_detail) {
					$this->session->set_flashdata('message', '<div class="alert alert-danger">Shipments from the Production Timeline must be removed first</div>');
					redirect('admin/timeline_produksi/history/'.base64_encode($no_produksi_hapus));
				}else{
					$get_propo = $this->Timeline_produksi_model->get_popro_posi(base64_decode($id));
					foreach ($get_propo as $val_propo) {
						$get_stok = $this->Timeline_produksi_model->get_stok_by_po_bahan_produksi($val_propo->no_po, $val_propo->id_bahan_kemas, $val_propo->no_timeline_produksi);

						$get_posi = $this->Timeline_produksi_model->$this->Timeline_produksi_model->get_posi_by_po_bahan_produksi_detail($val_propo->no_po, $val_propo->id_bahan_kemas, $val_propo->no_timeline_produksi, $val_propo->id_posi_data_access);
			          	
			          	if ($get_stok || $get_posi) {
				          	$kurangiFixSisa = array( 'fix_sisa_stok_pabrik' 	=> $get_stok->fix_sisa_stok_pabrik + $get_posi->qty_selisih_detail_timeline_produksi,
				          	);

				          	$this->Timeline_produksi_model->update_stok($get_stok->id_stok_pabrik, $kurangiFixSisa);

				          	write_log();
			          	}	
					}

					$this->Timeline_produksi_model->delete_propo_by_detail_timeline(base64_decode($id));

					write_log();

					$this->Timeline_produksi_model->delete_posi_by_detail_timeline(base64_decode($id));

					write_log();

					$this->Timeline_produksi_model->delete_detail(base64_decode($id));
					
					write_log();

					$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
					
					redirect('admin/timeline_produksi/history/'.base64_encode($no_produksi_hapus));
				}
			}else{
				$no_produksi_hapus = $this->data['detail']->no_timeline_produksi;

				$this->Timeline_produksi_model->delete_detail(base64_decode($id));
				write_log();

				$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
				redirect('admin/timeline_produksi/history/'.base64_encode($no_produksi_hapus));
			}
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  	redirect('admin/timeline_produksi/timeline');
		}
	}

	public function hapus_stok($id, $produksi)
	{
		is_delete();

		$get_stok = $this->Timeline_produksi_model->get_stok_bahan_by_id_stok_row(base64_decode($id));
		$cek_posi = $this->Timeline_produksi_model->check_posi_jenis_timeline(2);

		if ($cek_posi) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Production from Production Timeline must be deleted first</div>');
		  	redirect('admin/timeline_produksi/history/'.$produksi);
		}else{
			if ($get_stok) {
				$get_po = $this->Timeline_produksi_model->get_all_po_by_po_bahan_row($get_stok->no_po, $get_stok->id_bahan_kemas);

				if ($get_po) {
					$TambahJumlahPO = array( 'total_selisih_po_produksi' 	=> $get_po->total_selisih_po_produksi + $get_stok->qty_stok_pabrik,
		          	);

		          	$this->Timeline_produksi_model->updatePO($get_po->no_po, $TambahJumlahPO);

		          	write_log();

		          	$TambahJumlahdetailPO = array( 'selisih_po_produksi' 		=> $get_po->selisih_po_produksi + $get_stok->qty_stok_pabrik,
		          	);

		          	$this->Timeline_produksi_model->update_detailPO_by_bahan($get_po->no_po, $get_po->id_bahan_kemas, $TambahJumlahdetailPO);

		          	write_log();

					$this->Timeline_produksi_model->delete_stok($get_stok->id_stok_pabrik);

					write_log();


		          	$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
					redirect('admin/timeline_produksi/history/'.base64_encode($get_stok->no_timeline_produksi));
				}	
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
			  	redirect('admin/timeline_produksi/timeline');
			}
		}
	}

	public function hapus($id)
	{
		is_delete();

		$this->data['timeline'] 	= $this->Timeline_produksi_model->get_all_by_timeline_row(base64_decode($id));
		$this->data['hpp'] 			= $this->Timeline_produksi_model->get_all_hpp_by_timeline(base64_decode($id));
		
		// echo print_r($this->data['timeline']);
		if (!$this->data['hpp']) {
			if (isset($this->data['timeline'])) {
				if ($this->data['timeline']->status_timeline_produksi == 0) {
					$no_po = str_replace("TML","PO",$this->data['timeline']->no_timeline_produksi);

					$get_posi = $this->Timeline_produksi_model->get_posi_jenis_timeline_by_id(2, $this->data['timeline']->no_timeline_produksi);

					// echo print_r($get_posi);
					foreach ($get_posi as $val_posi) {
						$get_po = $this->Timeline_produksi_model->get_all_po_by_po_bahan_row($val_posi->no_po, $val_posi->id_bahan_kemas);
						$DataUpdatePO =  array(	'total_selisih_po_produksi'		=> $get_po->total_selisih_po_produksi + $val_posi->qty_selisih_detail_timeline_produksi
						);

						$this->Timeline_produksi_model->updatePO($get_po->no_po, $DataUpdatePO);

						write_log();

						$DataUpdateDetailPO =  array(	'selisih_po_produksi'	=> $get_po->selisih_po_produksi + $val_posi->qty_selisih_detail_timeline_produksi
						);

				        $this->Timeline_produksi_model->update_detailPO_by_bahan($get_po->no_po, $get_po->id_bahan_kemas, $DataUpdateDetailPO);

						write_log();
						// echo $get_po->total_selisih_po_produksi." - ".$val_posi->qty_selisih_detail_timeline_produksi."<br>";
						// echo $get_po->selisih_po_produksi." - ".$val_posi->qty_selisih_detail_timeline_produksi."<br>";
						// echo print_r($DataUpdatePO)."<br>";
						// echo print_r($DataUpdateDetailPO)."<br>";
					}

					$this->Timeline_produksi_model->delete_posi_by_id($this->data['timeline']->no_timeline_produksi);

					write_log();

					$this->Timeline_produksi_model->delete_propo_by_id($this->data['timeline']->no_timeline_produksi);

					write_log();

					$DataUpdatePO =  array(	'status_po' 				=> 0,
											'total_selisih_po_produksi' => 0
					);

					$this->Timeline_produksi_model->updatePO($no_po, $DataUpdatePO);

					write_log();

					$DataUpdateDetailPO =  array(	'selisih_po_produksi' 		=> 0 
					);

					$this->Timeline_produksi_model->update_detailPO($no_po, $DataUpdateDetailPO);

					write_log();

					$this->Timeline_produksi_model->delete_detail_by_timeline($this->data['timeline']->no_timeline_produksi);

					write_log();

					$this->Timeline_produksi_model->delete($this->data['timeline']->no_timeline_produksi);

					write_log();

					$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
					redirect('admin/timeline_produksi/timeline');	
				}else if ($this->data['timeline']->status_timeline_produksi == 1) {
					$no_po = str_replace("TML","PO",base64_decode($id));

					$updateTimeline = array( 'status_timeline_produksi' => 0,
					);

					$this->Timeline_produksi_model->update($this->data['timeline']->no_timeline_produksi, $updateTimeline);

					write_log();

					$updatePO = array(	'status_po'	=> 1
					);

					$this->Timeline_produksi_model->updatePO($no_po, $updatePO);

					write_log();

					$detailPO = $this->Timeline_produksi_model->get_bahan_by_po($no_po);
					foreach ($detailPO as $val_detail) {
						$bahan = $this->Timeline_produksi_model->get_bahan_by_id($val_detail->id_bahan_kemas);
						$updateBahan = array( 'qty_bahan_kemas'	=> $bahan->qty_bahan_kemas - $val_detail->selisih_po_produksi);

						$this->Timeline_produksi_model->update_produk($bahan->id_bahan_kemas, $updateBahan);
					}

					$get_posi = $this->Timeline_produksi_model->get_posi_bahan_jenis_timeline_by_id(2, base64_decode($id));
					foreach ($get_posi as $val_posi) {
						$get_detail2 = $this->Timeline_produksi_model->get_all_detail_po_by_po_bahan_row($val_posi->no_po, $val_posi->id_bahan_kemas);
			          	if ($get_detail2) {
				          	$kurangiJumlahPO = array( 'total_selisih_po_produksi' 	=> $get_detail2->total_selisih_po_produksi - $val_posi->qty_detail_timeline_produksi,
				          	);

				          	$this->Timeline_produksi_model->updatePO($get_detail2->no_po, $kurangiJumlahPO);

				          	write_log();

				          	$kurangiJumlahdetailPO = array( 'selisih_po_produksi' 		=> $get_detail2->selisih_po_produksi - $val_posi->qty_detail_timeline_produksi,
				          	);

				          	echo print_r($kurangiJumlahPO)."<br>";
				          	echo print_r($kurangiJumlahdetailPO)."<br>";

				          	$this->Timeline_produksi_model->update_detailPO_by_bahan($get_detail2->no_po, $get_detail2->id_bahan_kemas, $kurangiJumlahdetailPO);

				          	write_log();	

				          	if ($val_posi->selisih_detail_timeline_produksi == 0) {
				          		$fix_posi = $val_posi->terpakai_detail_timeline_produksi;
				          	}else{
				          		$fix_posi = $val_posi->terpakai_detail_timeline_produksi + $val_posi->selisih_detail_timeline_produksi;
				          	}

			          		$bahan = $this->Timeline_produksi_model->get_bahan_by_id($get_detail2->id_bahan_kemas);
							$updateBahan = array( 'qty_bahan_kemas'	=> $bahan->qty_bahan_kemas + $fix_posi
												);
							
							$this->Timeline_produksi_model->update_produk($bahan->id_bahan_kemas, $updateBahan);	
			          	}	
					}

					$this->session->set_flashdata('message', '<div class="alert alert-danger">Data deleted succesfully</div>');
				    redirect('admin/timeline_produksi/timeline');
				}
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
			  	redirect('admin/timeline_produksi/timeline');
			}	
		}else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Cannot be deleted because the Timeline Produksi is in use</div>');
		  	redirect('admin/timeline_produksi/timeline');
		}
	}

	public function get_no_po_stok()
	{
		$po = $this->input->post('po');
		$select_box[] = "<option value=''>- Pilih Nama Bahan Produksi -</option>";
		$bahan = $this->Timeline_produksi_model->get_no_po($po);
		if (count($bahan) > 0) {
			foreach ($bahan as $row) {
				$select_box[] = '<option value="'.$row->id_bahan_kemas.'">'.$row->kode_sku.' | '.$row->nama_bahan_kemas.' | Stok: '.$row->selisih_po_produksi.'</option>';
			}

			$msg = array( 'select' 	 => $select_box,
						  'po_bahan' => $po 
			);
			// header("Content-Type:application/json");
			echo json_encode($msg);
		}else{
			$select_box = '<option value="">Tidak Ada</option>';
			echo json_encode($select_box);
		}
	}

	public function get_no_po()
	{
		$po = $this->input->post('po');
		$select_box[] = "<option value=''>- Pilih Nama Bahan Produksi -</option>";
		$bahan = $this->Timeline_produksi_model->get_no_po_stok($po);
		if (count($bahan) > 0) {
			foreach ($bahan as $row) {
				$select_box[] = '<option value="'.$row->id_bahan_kemas.'">'.$row->kode_sku.' | '.$row->nama_bahan_kemas.' | Stok: '.$row->fix_sisa_stok_pabrik.'</option>';
			}

			$msg = array( 'select' 	 => $select_box,
						  'po_bahan' => $po 
			);
			// header("Content-Type:application/json");
			echo json_encode($msg);
		}else{
			$select_box = '<option value="">Tidak Ada</option>';
			echo json_encode($select_box);
		}
	}

	public function get_id_bahan_stok()
	{
		$bahan = $this->input->post('bahan');
		$po = $this->input->post('po');
		// $id_barang = "RPL2003200001";
		$cari_bahan =	$this->Timeline_produksi_model->get_id_bahan($bahan, $po);
		echo json_encode($cari_bahan);
	}

	public function get_id_bahan()
	{
		$bahan = $this->input->post('bahan');
		$po = $this->input->post('po');
		// $id_barang = "RPL2003200001";
		$cari_bahan =	$this->Timeline_produksi_model->get_id_bahan_stok($bahan, $po);
		echo json_encode($cari_bahan);
	}
}

/* End of file Timeline_produksi.php */
/* Location: ./application/controllers/admin/Timeline_produksi.php */