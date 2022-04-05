<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Keyword extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module_provinsi'] = 'Keyword Provinsi';
	    $this->data['module_kotkab'] = 'Keyword Kota / Kabupaten';

	    $this->load->model(array('Keyword_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_delete']    = 'Delete Data';
	    $this->data['btn_export']    = 'Export Data';
	    $this->data['btn_import']    = 'Format Data Import';
	    // Provinsi
	    $this->data['add_action_provinsi'] = base_url('admin/keyword/provinsi_tambah');
	    $this->data['export_action_provinsi'] = base_url('admin/keyword/provinsi_export');
	    $this->data['format_provinsi'] = base_url('assets/template/excel/format_provinsi.xlsx');


	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	// Provinsi

	function dasbor_list_provinsi_count(){
		$provinsi = $this->Keyword_model->total_rows_provinsi();
		$detail_provinsi = $this->Keyword_model->total_rows_detail_provinsi();
		$detail_kotkab = $this->Keyword_model->total_rows_detail_kotkab();
    	if (isset($provinsi) || isset($detail_provinsi) || isset($detail_kotkab) ) {	
        	$msg = array(	'provinsi'			=> $provinsi,
			        		'detail_provinsi'	=> $detail_provinsi,
			        		'detail_kotkab'		=> $detail_kotkab,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'provinsi'			=> 0,
			        		'detail_provinsi'	=> 0,
			        		'detail_kotkab'		=> 0,
        			);
        	echo json_encode($msg); 
    		// $msg = array(	'validasi'	=> validation_errors()
      //   			);
      //   	echo json_encode($msg);
    	}
    }

    function get_data_provinsi()
    {
        $list = $this->Keyword_model->get_datatables_provinsi();
        $dataJSON = array();
        foreach ($list as $data) {
   			// Detail Provinsi
   			$action = '<a href="'.base_url('admin/keyword/provinsi_ubah/'.$data->id_keyword_provinsi).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action .= ' <a href="'.base_url('admin/keyword/provinsi_hapus/'.$data->id_keyword_provinsi).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="<?php echo $data->id_keyword_provinsi ?>">';
			$get_detail_provinsi = $this->Keyword_model->get_detail_provinsi_by_id_provinsi($data->id_keyword_provinsi);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table table-bordered table-striped" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td><b>Kota / Kabupaten</b></td>'.
			                '<td><b>Keyword Kota / Kabupaten</b></td>'.
			            '</tr>';

			if($get_detail_provinsi == NULL)
	        {
	          $detail .= '<tr align="center">'.
			                '<td colspan="2"><a href="#" class="btn btn-sm btn-danger">No Data</a></td>'.
			            '</tr>';
	        }
	        else
	        {
	          foreach ($get_detail_provinsi as $val_detail) {
					$detail .= '<tr align="center">'.
					                '<td>'.$val_detail->nama_kotkab.'</td>';

					$cek_detail_kotkab = $this->Keyword_model->get_keys_detail_kotkab_by_id_detail_provinsi($val_detail->id_detail_keyword_provinsi);
		
			        if($cek_detail_kotkab == NULL)
			        {
			          $detail .=  '<td><a href="#" class="btn btn-sm btn-danger">No Data</a></td>'.
					              '</tr>';
			        }
			        else
			        {
			          $detail .=  '<td>';
			          foreach($cek_detail_kotkab as $val_kotkab)
			          {
			            $string = chunk_split($val_kotkab->keys_kotkab, 255, "</a> ");
			            $detail .=  '<a href="#" class="btn btn-sm btn-primary">'.$string;
			          }
			           $detail .=  '</td>'.
					              '</tr>';
			        }
					               
				}
	        }

            $row = array();
            $row['provinsi'] = $data->nama_provinsi;
            $row['action'] = $action;
            $row['detail'] = $detail;
            $row['select'] = $select;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Keyword_model->count_all_provinsi(),
            "recordsFiltered" => $this->Keyword_model->count_filtered_provinsi(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function provinsi()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module_provinsi'].' List';
	    $this->data['action_impor']  = 'admin/keyword/proses_provinsi_kotkab_impor';

	    $this->data['get_all'] = $this->Keyword_model->get_all_provinsi();

	    $this->load->view('back/keyword/provinsi_list', $this->data);
	}

	public function provinsi_tambah()
	{
		is_create();    

	    $this->data['page_title'] = 'Create New '.$this->data['module_provinsi'];
	    $this->data['action']     = 'admin/keyword/provinsi_tambah_proses';

	    $this->data['provinsi_nama'] = [
	      'name'          => 'nama_provinsi',
	      'id'            => 'nama-provinsi',
	      'class'         => 'form-control',
	      'autocomplete'  => 'off',
	      'required'      => '',
	      'value'         => $this->form_validation->set_value('nama_provinsi'),
	    ];

	    $this->load->view('back/keyword/provinsi_add', $this->data);
	}

	public function provinsi_tambah_proses()
	{
		$this->form_validation->set_rules('nama_provinsi', 'Nama Provinsi', 'max_length[255]|trim|required',
			array(	'required' 		=> '%s harus diisi!',
					'max_length'	=> '%s maksimal 255 karakter'
			)
		);

	    $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

	    if($this->form_validation->run() === FALSE)
	    {
	      $this->tambah();
	    }
	    else
	    {
	      $data = array(
	        'nama_provinsi' => $this->input->post('nama_provinsi')
	      );

	      $this->Keyword_model->insert_provinsi($data);

	      write_log();

	      $this->session->set_flashdata('message', '<div class="alert alert-success">Data saved succesfully</div>');
	      redirect('admin/keyword/provinsi');
	    }
	}

	public function provinsi_ubah($id = '')
	{
		is_update();

	    $this->data['provinsi']    		= $this->Keyword_model->get_provinsi_by_id($id);
	    $this->data['kotkab']    		= $this->Keyword_model->get_provinsi_by_id($id);
	    $this->data['detail_provinsi']  = $this->Keyword_model->get_detail_provinsi_by_id_provinsi($id);
	    
	    if($this->data['provinsi'])
	    {
	    	// if (count($this->data['detail_provinsi']) > 0) {
		    // 	$this->data['arr_keys'] = array();

		    // 	foreach ($this->data['detail_provinsi'] as $val_provinsi) {
		    // 		$this->data['arr_keys'][] = $val_provinsi->keys_kotkab;
		    // 	}
		    // }else{
		    // 	$this->data['arr_keys'] = '';
		    // }

	      $this->data['page_title'] = 'Update Data '.$this->data['module_provinsi'];
	      $this->data['action']     = 'admin/keyword/provinsi_ubah_proses';

	      $this->data['id_keyword_provinsi'] = [
	        'name'          => 'id_keyword_provinsi',
	        'id'			=> 'id-keyword-provinsi',
	        'type'          => 'hidden',
	      ];

	      $this->data['id_keyword_detail_provinsi'] = [
	        'name'          => 'id_keyword_detail_provinsi',
	        'id'			=> 'id-keyword-detail-provinsi',
	        'type'          => 'hidden',
	      ];

		  $this->data['provinsi_nama'] = [
		      'name'          => 'nama_provinsi',
		      'id'            => 'nama-provinsi',
		      'class'         => 'form-control',
		      'readonly'	  => '',
		      'autocomplete'  => 'off',
		      'required'      => '',
		    ];

		  $this->data['kotkab_nama'] = [
		      'name'          => 'kotkab_nama',
		      'id'            => 'nama-kotkab',
		      'class'         => 'form-control',
		      'autocomplete'  => 'off',
		      'required'      => '',
		    ];

		  
		  $this->data['keys_kotkab'] = [
		      'name'          => 'keys_kotkab',
		      'id'            => 'keys-kotkab',
		      'class'	  	  => 'form-control',
		      'style'		  => 'width:100%'
		    ];

	      $this->load->view('back/keyword/provinsi_edit', $this->data);
	    }
	    else
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
	      redirect('admin/keyword/provinsi');
	    }
	}

	public function provinsi_ubah_proses() {
		$id_keyword_provinsi = $this->input->post('id');
		$kotkab = $this->input->post('kotkab');
		$keys = $this->input->post('keys');
		$ex_keys = explode(",", $keys);
		
		$data_provinsi = array(	'id_keyword_provinsi'	=> $id_keyword_provinsi,
								'nama_kotkab'			=> $kotkab,
		);

		$this->Keyword_model->insert_detail_provinsi($data_provinsi);

		write_log();

		$get_last_detail_provinsi = $this->Keyword_model->get_detail_provinsi_last();
		foreach ($ex_keys as $val_keys) {
			$data_kotkab = array(	'id_detail_keyword_provinsi'	=> $get_last_detail_provinsi->id_detail_keyword_provinsi,
									'keys_kotkab'					=> $val_keys
			);

			$this->Keyword_model->insert_detail_kotkab($data_kotkab);

			write_log();
		}

		$pesan = "Berhasil ditambah!";	
    	$msg = array(	'sukses'	=> $pesan,
    					'id'		=> $id_keyword_provinsi
    			);
    	echo json_encode($msg);
	}	

	public function get_detail_provinsi_by_id_detail_provinsi($id)
	{
		$data['data'] = 0;
		$cek_detail = $this->Keyword_model->get_detail_provinsi_by_id_detail_provinsi_row($id);
		if($cek_detail){
			$isi = array();
			$i = 0;
			$data['data'] = 1;
			$data['id'] = $cek_detail->id_detail_keyword_provinsi;
			$data['id_provinsi'] = $cek_detail->id_keyword_provinsi;
			$data['nama_kotkab'] = $cek_detail->nama_kotkab;
			
			$cek_keyword = $this->Keyword_model->get_detail_provinsi_kotkab_by_id_detail_provinsi($id);
			$count_keys = count($cek_keyword) - 1;
			foreach ($cek_keyword as $val_keyword) {
	            $isi[] = $val_keyword->keys_kotkab;

				$i++;
			}

			$data['keys_kotkab'] = implode(",", $isi);
		}
		echo json_encode($data);	
	}

	public function detail_provinsi_ubah()
	{
		$i = $this->input;
		$id = $i->post('id');
		$id_provinsi = $i->post('id_provinsi');
		$pilihan = $i->post('pilihan');
		$kotkab = $i->post('kotkab');
		$keys = $i->post('keys');
		$ex_keys = explode(",", $keys);

		if ($pilihan == 'simpan') {
			$cek_detail = $this->Keyword_model->get_detail_provinsi_by_id_detail_provinsi_row($id);
			if ($cek_detail) {
				$this->Keyword_model->delete_detail_kotkab_by_id_detail_provinsi($id);
				$updateData = array( 'nama_kotkab'		=> $kotkab,	
				);	

				$this->Keyword_model->update_detail_provinsi($cek_detail->id_detail_keyword_provinsi, $updateData);
				
				foreach ($ex_keys as $val_keys) {
					$data_kotkab = array(	'id_detail_keyword_provinsi'	=> $cek_detail->id_detail_keyword_provinsi,
											'keys_kotkab'					=> $val_keys
					);

					$this->Keyword_model->insert_detail_kotkab($data_kotkab);

					write_log();
				}

				$pesan = "Berhasil diubah!";	
	        	$msg = array(	'sukses'	=> $pesan,
	        					'id'		=> $id_provinsi,
	        			);
	        	echo json_encode($msg);
			}	
		}elseif ($pilihan == 'hapus'){
			$cek_detail = $this->Keyword_model->get_detail_provinsi_by_id_detail_provinsi_row($id);
			if ($cek_detail) {
				// echo print_r($updateData);
				$this->Keyword_model->delete_detail_kotkab_by_id_detail_provinsi($id);

				$this->Keyword_model->delete_detail_provinsi_by_id_detail_provinsi($id);

				$pesan = "Berhasil dihapus!";	
	        	$msg = array(	'sukses'	=> $pesan,
	        					'id'		=> $id_provinsi,
	        			);
	        	echo json_encode($msg);
			}
		}
	}

	public function provinsi_hapus($id = '')
	{
		is_delete();

		$provinsi  			= $this->Keyword_model->get_provinsi_by_id($id);
	    
	    if($provinsi)
		{
		  $detail_provinsi 	= $this->Keyword_model->get_detail_provinsi_by_id_provinsi($id);
		  if (count($detail_provinsi) > 0) {
		  	foreach ($detail_provinsi as $val_detail_provinsi) {
		  		$this->Keyword_model->delete_detail_kotkab_by_id_detail_provinsi($val_detail_provinsi->id_detail_keyword_provinsi);	
		  	}

		  	$this->Keyword_model->delete_detail_provinsi_by_id_provinsi($id);	
		  }

		  $this->Keyword_model->delete_provinsi($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/keyword/provinsi');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/keyword/provinsi');
		}
	}

	function provinsi_hapus_dipilih()
	{
		is_delete();

		$provinsi = $this->input->post('ids');

		$data_detail_provinsi = $this->Keyword_model->get_detail_provinsi_by_id_provinsi_in($provinsi);
		foreach ($data_detail_provinsi as $val_detail_provinsi) {
	  		$this->Keyword_model->delete_detail_kotkab_by_id_detail_provinsi($val_detail_provinsi->id_detail_keyword_provinsi);	
	  	}

		$this->Keyword_model->delete_detail_provinsi_in_by_id_provinsi($provinsi);

		$this->Keyword_model->delete_provinsi_in($provinsi);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	public function proses_provinsi_kotkab_impor()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc'.time();	
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('import')) {
			$this->Keyword_model->deleteAll_detail_kotkab();
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/'.$file['file_name']);
			$numSheet 	= 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 1) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow > 1) {
							$dataProvinsi 	= array(	'id_keyword_provinsi'	=> $row->getCellAtIndex(1),
														'nama_provinsi'			=> $row->getCellAtIndex(2),
							);

							$dataDetailProvinsi 	= array(	'id_detail_keyword_provinsi'	=> $row->getCellAtIndex(0),
																'id_keyword_provinsi'			=> $row->getCellAtIndex(1),
																'nama_kotkab'					=> $row->getCellAtIndex(3),
							);

							$this->Keyword_model->import_provinsi($dataProvinsi);

							$this->Keyword_model->import_detail_provinsi($dataDetailProvinsi);

							if ($row->getCellAtIndex(4) != "") {
								$keys = $row->getCellAtIndex(4);
								$ex_keys = explode(",", $keys);
								foreach ($ex_keys as $val_keys) {
									$dataKotkab = array(	'id_detail_keyword_provinsi'	=> $row->getCellAtIndex(0),
															'keys_kotkab'					=> trim($val_keys)
									);

									$this->Keyword_model->import_detail_kotkab($dataKotkab);	
								}
							}
						}
						$numRow++;
					}
					$reader->close();
					unlink('uploads/'.$file['file_name']);
					$this->session->set_flashdata('message', '<div class="alert alert-success">Data imported successfully</div>');
					redirect('admin/keyword/provinsi');
				}
				$numSheet++;
			}
		}else{
			$error = array('error' => $this->upload->display_errors());
			return $error;
		}
	}

	function provinsi_export() {
		$data['title']	= "Export Data Provinsi_".date("Y_m_d");
		$data['provinsi']	= $this->Keyword_model->get_all_detail_provinsi_provinsi();

		$this->load->view('back/keyword/provinsi_export', $data);
	}
}

/* End of file Keyword.php */
/* Location: ./application/controllers/admin/Keyword.php */