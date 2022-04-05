<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Include librari PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Laporan extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	    $this->data['module'] = 'Report';

	    $this->load->model(array('Laporan_model', 'Auth_model', 'Keluar_model', 'Usertype_model'));

	    $this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
	    $this->data['skins_template']     			= $this->Template_model->skins();

	    $this->data['btn_submit'] = 'Save';
	    $this->data['btn_reset']  = 'Reset';
	    $this->data['btn_add']    = 'Add New Data';
	    $this->data['btn_export']    = 'Export Data';
	    $this->data['btn_import']    = 'Format Data Import';

	    is_login();

	    if($this->uri->segment(1) != NULL){
	      menuaccess_check();
	    }
	    elseif($this->uri->segment(2) != NULL){
	      submenuaccess_check();
	    }
	}

	function dasbor_list_count(){
		$start 	= substr($this->input->post('periodik'), 0, 10);
		$end 	= substr($this->input->post('periodik'), 13, 24);
		$report = $this->Laporan_model->total_rows_dasbor($start, $end,$this->input->post('usertype'));
    	if (isset($report)) {	
        	$msg = array(	'report'			=> $report,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'report'			=> 0,
        			);
        	echo json_encode($msg); 
    		// $msg = array(	'validasi'	=> validation_errors()
      //   			);
      //   	echo json_encode($msg);
    	}
    }

    function get_data()
    {
        $list = $this->Laporan_model->get_datatables();
        $dataJSON = array();
        foreach ($list as $data) {
   			// Detail CRM
   			// $action = '<a href="'.base_url('admin/laporan/master_ubah/'.$data->id_report).'" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
          	$action = ' <a href="'.base_url('admin/laporan/master_hapus/'.$data->id_report).'" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';
          	$select = '<input type="checkbox" class="sub_chk" data-id="'.$data->id_report.'">';
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table table-bordered table-striped" border="0" style="padding-left:50px;">'.
					  '<tr align="center">'.
			                '<td><b>Tanggal Awal</b></td>'.
			                '<td><b>Tanggal Akhir</b></td>'.
			                '<td><b>Divisi</b></td>'.
			            '</tr>';

			$detail .= '<tr align="center">'.
			                '<td>'.$data->date_first.'</td>'.
			                '<td>'.$data->date_last.'</td>'.
			                '<td>'.$data->usertype_name.'</td>'.
	        			'</tr>';

            $row = array();
            $row['tanggal'] = date('d-m-Y H:i:s', strtotime($data->created));
            $row['report'] = $data->report_data;
            $row['nama'] = $data->name;
            $row['action'] = $action;
            $row['detail'] = $detail;
            $row['select'] = $select;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Laporan_model->count_all(),
            "recordsFiltered" => $this->Laporan_model->count_filtered(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

    // Master

    public function master()
	{
		is_read();    

	    $this->data['page_title'] 		= $this->data['module'].' CRM List';
	    $this->data['get_all_usertype'] 	= $this->Usertype_model->get_all_combobox();

	    $this->data['usertype'] = [
	    	'class'         => 'form-control',
	    	'id'            => 'usertype',
	      	'required'      => '',
	      	'style' 		=> 'width:100%'
	    ];

	    $this->load->view('back/laporan/master/master', $this->data);
	}

	function master_hapus($id = '')
	{
		is_delete();

		$delete = $this->Laporan_model->get_by_id($id);

		if($delete)
		{
		  $this->Laporan_model->delete($id);

		  write_log();

		  $this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		  redirect('admin/laporan/master');
		}
		else
		{
		  $this->session->set_flashdata('message', '<div class="alert alert-danger">No data found</div>');
		  redirect('admin/laporan/master');
		}
	}

	function master_hapus_dipilih()
	{
		is_delete();

		$report = $this->input->post('ids');
		// echo $produk;

		$this->Laporan_model->delete_in($report);

		$pesan = "Berhasil dihapus!";	
    	$msg = array(	'sukses'	=> $pesan
    			);
    	echo json_encode($msg);
	}

	// CRM
	
	public function crm()
	{
		is_read();    

	    $this->data['page_title'] = $this->data['module'].' CRM List';

	    $this->load->view('back/laporan/crm', $this->data);
	}

	public function export_gabungin($users, $periodik)
	{
		date_default_timezone_set("Asia/Jakarta");
		$now = date('Y-m-d H:i:s');
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 27);
		$isi = "Format Gabung.in";
		$users = $this->Auth_model->get_by_id($users);
		if ($users->usertype == 1) {
			$data['title']	= "Export Format Gabung.in Per Tanggal ".$start." - ".$end."_".date("H_i_s");
			$data['export'] = $this->Keluar_model->get_all_detail_by_periodik($start, $end);

			// PHPOffice

			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			$sheet->setCellValue('A1', 'no_pesanan');
			$sheet->setCellValue('B1', 'tanggal');
			$sheet->setCellValue('C1', 'no_resi');
			$sheet->setCellValue('D1', 'kurir');
			$sheet->setCellValue('E1', 'ongkir');
			$sheet->setCellValue('F1', 'nama_penerima');
			$sheet->setCellValue('G1', 'provinsi');
			$sheet->setCellValue('H1', 'kota');
			$sheet->setCellValue('I1', 'alamat');
			$sheet->setCellValue('J1', 'hp_penerima');
			$sheet->setCellValue('K1', 'nama_produk');
			$sheet->setCellValue('L1', 'jumlah');
			$sheet->setCellValue('M1', 'total_harga');
			$sheet->setCellValue('N1', 'metode_pembayaran');
			$sheet->setCellValue('O1', 'status');
			$sheet->setCellValue('P1', 'total_jual');
			$sheet->setCellValue('Q1', 'sku');

	        // set Row
	        $rowCount = 2;
	        foreach ($data['export'] as $list) {
	            $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
	            $sheet->SetCellValue('B' . $rowCount, date('d/m/Y', strtotime($list->tgl_penjualan)));
	            $sheet->SetCellValue('C' . $rowCount, $list->nomor_resi);
	            $sheet->SetCellValue('D' . $rowCount, $list->nama_kurir);
	            $sheet->SetCellValue('E' . $rowCount, $list->ongkir);
	            $sheet->SetCellValue('F' . $rowCount, $list->nama_penerima);
	            $sheet->SetCellValue('G' . $rowCount, $list->provinsi);
	            $sheet->SetCellValue('H' . $rowCount, $list->kabupaten);
	            $sheet->SetCellValue('I' . $rowCount, $list->alamat_penerima);
	            $sheet->SetCellValue('J' . $rowCount, $list->hp_penerima);
	            $sheet->SetCellValue('K' . $rowCount, $list->nama_produk);
	            $sheet->SetCellValue('L' . $rowCount, $list->qty);
	            $sheet->SetCellValue('M' . $rowCount, $list->harga);
	            $sheet->SetCellValue('N' . $rowCount, 'Transfer');
	            $sheet->SetCellValue('O' . $rowCount, 'Terkirim');
	            $sheet->SetCellValue('P' . $rowCount, $list->total_harga);
	            $sheet->SetCellValue('Q' . $rowCount, $list->sub_sku);
	            $rowCount++;
	        }

	        $dataReport = array(	'id_users'		=> $this->session->userdata('id_users'),
									'usertype'		=> $users->usertype,
									'date_first'	=> $start,
									'date_last'		=> $end,
									'report_data'	=> $isi,
									'created'		=> $now
								 );

			$this->Laporan_model->insert($dataReport);

			write_log();

	        $writer = new Xlsx($spreadsheet);
			
			header('Content-Type: application/vnd.ms-excel');
			header("Content-Transfer-Encoding: Binary"); 
			header('Content-Disposition: attachment;filename="'. $data['title'] .'.xlsx"');
			header("Pragma: no-cache");
			header("Expires: 0");
	
			$writer->save('php://output');

			die();

			// $this->load->view('back/laporan/export_gabungin', $data);

      		// redirect('admin/laporan/crm');

      		
		}elseif ($users->usertype != 1) {
			$data['title']	= "Export Format Gabung.in Per Tanggal ".$start." - ".$end."_".date("H_i_s");
			$cek_data = $this->Laporan_model->get_by_usertype_periodik_row($users->usertype, $isi, $start, $end);
			if (!isset($cek_data)) {
				$data['export'] = $this->Keluar_model->get_all_detail_by_periodik($start, $end);

				// PHPOffice

				$spreadsheet = new Spreadsheet();
				$sheet = $spreadsheet->getActiveSheet();

				$sheet->setCellValue('A1', 'no_pesanan');
				$sheet->setCellValue('B1', 'tanggal');
				$sheet->setCellValue('C1', 'no_resi');
				$sheet->setCellValue('D1', 'kurir');
				$sheet->setCellValue('E1', 'ongkir');
				$sheet->setCellValue('F1', 'nama_penerima');
				$sheet->setCellValue('G1', 'provinsi');
				$sheet->setCellValue('H1', 'kota');
				$sheet->setCellValue('I1', 'alamat');
				$sheet->setCellValue('J1', 'hp_penerima');
				$sheet->setCellValue('K1', 'nama_produk');
				$sheet->setCellValue('L1', 'jumlah');
				$sheet->setCellValue('M1', 'total_harga');
				$sheet->setCellValue('N1', 'metode_pembayaran');
				$sheet->setCellValue('O1', 'status');
				$sheet->setCellValue('P1', 'total_jual');
				$sheet->setCellValue('Q1', 'sku');

		        // set Row
		        $rowCount = 2;
		        foreach ($data['export'] as $list) {
		            $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
		            $sheet->SetCellValue('B' . $rowCount, date('d/m/Y', strtotime($list->tgl_penjualan)));
		            $sheet->SetCellValue('C' . $rowCount, $list->nomor_resi);
		            $sheet->SetCellValue('D' . $rowCount, $list->nama_kurir);
		            $sheet->SetCellValue('E' . $rowCount, $list->ongkir);
		            $sheet->SetCellValue('F' . $rowCount, $list->nama_penerima);
		            $sheet->SetCellValue('G' . $rowCount, $list->provinsi);
		            $sheet->SetCellValue('H' . $rowCount, $list->kabupaten);
		            $sheet->SetCellValue('I' . $rowCount, $list->alamat_penerima);
		            $sheet->SetCellValue('J' . $rowCount, $list->hp_penerima);
		            $sheet->SetCellValue('K' . $rowCount, $list->nama_produk);
		            $sheet->SetCellValue('L' . $rowCount, $list->qty);
		            $sheet->SetCellValue('M' . $rowCount, $list->harga);
		            $sheet->SetCellValue('N' . $rowCount, 'Transfer');
		            $sheet->SetCellValue('O' . $rowCount, 'Terkirim');
		            $sheet->SetCellValue('P' . $rowCount, $list->total_harga);
		            $sheet->SetCellValue('Q' . $rowCount, $list->sub_sku);
		            $rowCount++;
		        }

		        $dataReport = array(	'id_users'		=> $this->session->userdata('id_users'),
										'usertype'		=> $users->usertype,
										'date_first'	=> $start,
										'date_last'		=> $end,
										'report_data'	=> $isi,
										'created'		=> $now
									 );

				$this->Laporan_model->insert($dataReport);

				write_log();

		        $writer = new Xlsx($spreadsheet);
				
				header('Content-Type: application/vnd.ms-excel');
				header("Content-Transfer-Encoding: Binary"); 
				header('Content-Disposition: attachment;filename="'. $data['title'] .'.xlsx"');
				header("Pragma: no-cache");
				header("Expires: 0");
		
				$writer->save('php://output');

				die();

				// $this->load->view('back/laporan/export_gabungin', $data);
			}else{
				$this->session->set_flashdata('message', '<div class="alert alert-danger">Export <b>'.$cek_data->report_data.'</b> tanggal <b>'.$cek_data->date_first.'</b> - <b>'.$cek_data->date_last.'</b> sudah dilakukan oleh <b>'.$cek_data->name.'</b> dari Divisi <b>'.$cek_data->usertype_name.'</b> pada waktu <b>'.$cek_data->created.'</b> </div>');
	      		redirect('admin/laporan/crm');
			}	
		}
	}

}

/* End of file Laporan.php */
/* Location: ./application/controllers/admin/Laporan.php */