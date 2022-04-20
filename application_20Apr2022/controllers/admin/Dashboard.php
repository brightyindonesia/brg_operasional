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

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array('Dashboard_model', 'Produk_model', 'Bahan_kemas_model', 'Toko_model', 'Vendor_model', 'Keluar_model'));
		$this->load->helper('auth');

		is_login();

		$this->data['company_data']    					= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
    	$this->data['skins_template']     			= $this->Template_model->skins();

    	if(is_admin_cs())
	    {
	      $this->session->set_flashdata('message', '<div class="alert alert-danger">You can\'t access last page</div>');
	      redirect('admin/resi');
	    }
	}

	// Table Data Count
	// Datatable Server Side
	// Penjualan
	function get_data_modal_data_penjualan()
    {
        $list = $this->Dashboard_model->get_datatables_count_penjualan();
        $dataJSON = array();
        foreach ($list as $data) {
   			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan).' Produk</span>';
			if ($data->id_status_transaksi == 1) {
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 2) {
				$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-money' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 3) {
				$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 4) {
				$status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-exchange' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}

			// Detail Penjualan
			$get_detail_penjualan = $this->Keluar_model->get_all_detail_by_id($data->nomor_pesanan);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
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

            $row = array();
            $row['nomor_pesanan'] = $produk;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_penjualan));
            $row['nama_toko'] = $data->nama_toko;
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['status'] = $status;
            $row['detail'] = $detail;
            $row['created'] = $data->created;
            $row['total_harga'] = $data->total_harga;
            $row['jumlah_diterima'] = $data->jumlah_diterima;
            if ($data->tgl_diterima == NULL) {
            	$row['tgl_diterima'] = "-";
            }else{
            	$row['tgl_diterima'] = $data->tgl_diterima;
            }
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_count_penjualan(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_count_penjualan(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // Resi
    function get_data_modal_data_resi()
    {
        $list = $this->Dashboard_model->get_datatables_count_resi();
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
            $row['nomor_resi'] = $data->nomor_resi;
            $row['status'] = $status;
            $row['detail'] = $detail;
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_count_resi(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_count_resi(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // Retur
	function get_data_modal_data_retur()
    {
    	$i = 1;
        $list = $this->Dashboard_model->get_datatables_count_retur();
        $dataJSON = array();
        foreach ($list as $data) {
   			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan).' Produk</span>';
			if ($data->status_retur == 0) {
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i> Sedang diproses</a>";
			}else if($data->status_retur == 1) {
				$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i> Sudah diproses</a>";
			}

			// Detail Penjualan
			$get_detail_penjualan = $this->Keluar_model->get_all_detail_by_id($data->nomor_pesanan);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
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
            if ($data->status_follow_up == 1){
				$row['status_fu'] = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i> Sudah follow up</a>";
			}else{
				$row['status_fu'] = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-times' style='margin-right:5px;'></i> Belum follow up</a>";
			}
            $row['no'] = $i;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_retur));
            $row['nomor_retur'] = $data->nomor_retur;
            $row['nomor_pesanan'] = $produk;
            $row['nama_toko'] = $data->nama_toko;
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['total_harga'] = $data->total_harga;
            $row['keterangan'] = $data->keterangan_retur;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['status'] = $status;
            $row['detail'] = $detail;
            $row['created'] = $data->created;
 
            $dataJSON[] = $row;

            $i++;
        }
 
        $output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_count_retur(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_count_retur(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // Repeat
	function get_data_modal_data_repeat()
    {
        $list = $this->Dashboard_model->get_datatables_count_repeat();
        $dataJSON = array();
        foreach ($list as $data) {
   			$produk = $data->nomor_pesanan.' <span class="badge bg-green"><i class="fa fa-cubes" style="margin-right: 3px;"></i>'. $this->lib_keluar->count_detail_penjualan($data->nomor_pesanan).' Produk</span>';
			if ($data->id_status_transaksi == 1) {
				$status = "<a href='#' class='btn btn-warning btn-sm'><i class='fa fa-hourglass-2' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 2) {
				$status = "<a href='#' class='btn btn-primary btn-sm'><i class='fa fa-money' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 3) {
				$status = "<a href='#' class='btn btn-success btn-sm'><i class='fa fa-check' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}else if ($data->id_status_transaksi == 4) {
				$status = "<a href='#' class='btn btn-danger btn-sm'><i class='fa fa-exchange' style='margin-right:5px;'></i>".$data->nama_status_transaksi."</a>";
			}

			// Detail Penjualan
			$get_detail_penjualan = $this->Keluar_model->get_all_detail_by_id($data->nomor_pesanan);
			$detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">'.
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

            $row = array();
            $row['nomor_pesanan'] = $produk;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_penjualan));
            $row['nama_toko'] = $data->nama_toko;
            $row['nama_kurir'] = $data->nama_kurir;
            $row['nomor_resi'] = $data->nomor_resi;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['status'] = $status;
            $row['detail'] = $detail;
            $row['created'] = $data->created;
            $row['total_harga'] = $data->total_harga;
            $row['jumlah_diterima'] = $data->jumlah_diterima;
            if ($data->tgl_diterima == NULL) {
            	$row['tgl_diterima'] = "-";
            }else{
            	$row['tgl_diterima'] = $data->tgl_diterima;
            }
 
            $dataJSON[] = $row;
        }
 
        $output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_count_repeat(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_count_repeat(),
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
	// End Datatable Server Side

	public function index()
	{
		$this->data['page_title'] = 'Dashboard';

		$this->data['get_total_menu']     		= $this->Menu_model->total_rows();
		$this->data['get_total_submenu']     	= $this->Submenu_model->total_rows();				
		$this->data['get_total_user']     		= $this->Auth_model->total_rows();
		$this->data['get_total_usertype']     	= $this->Usertype_model->total_rows();
		$this->data['get_total_produk']     	= $this->Produk_model->total_rows();
		$this->data['get_total_bahan']     		= $this->Bahan_kemas_model->total_rows();
		$this->data['get_total_toko']     		= $this->Toko_model->total_rows();
		$this->data['get_total_vendor']     	= $this->Vendor_model->total_rows();

		$this->load->view('back/dashboard/body', $this->data);
	}

	// Non Retur

	// public function export_penjualan()
	// {	
	// 	$status  = $this->uri->segment(4); 
	// 	$tanggal = $this->uri->segment(5);
	// 	$periodik = $this->uri->segment(6);

	// 	$start = substr($periodik, 0, 10);
	// 	$end = substr($periodik, 17, 24);
	// 	if ($tanggal == 'impor') {
	// 		$data['title']	= "Export Data Penjualan Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
	// 	}else{
	// 		$data['title']	= "Export Data Penjualan Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
	// 	}
		
	// 	$data['penjualan']	= $this->Dashboard_model->get_penjualan($status, $tanggal,$start, $end);

	// 	// echo print_r($repeat);
	// 	$this->load->view('back/dashboard/penjualan_export', $data);
	// }

	function export_penjualan()
	{
		$status  = $this->uri->segment(4); 
		$tanggal = $this->uri->segment(5);
		$periodik = $this->uri->segment(6);

		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		if ($tanggal == 'impor') {
			$data['title']	= "Export Data Penjualan Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
		}else{
			$data['title']	= "Export Data Penjualan Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
		}
		
		$data['penjualan']	= $this->Dashboard_model->get_penjualan($status, $tanggal,$start, $end);

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'tgl_penjualan');
		$sheet->setCellValue('B1', 'created');
		$sheet->setCellValue('C1', 'nomor_pesanan');
		$sheet->setCellValue('D1', 'nama_toko');
		$sheet->setCellValue('E1', 'nama_kurir');
		$sheet->setCellValue('F1', 'nomor_resi');
		$sheet->setCellValue('G1', 'nama_status_transaksi');
		$sheet->setCellValue('H1', 'total_harga');
		$sheet->setCellValue('I1', 'nama_penerima');
		$sheet->setCellValue('J1', 'provinsi');
		$sheet->setCellValue('K1', 'kabupaten');
		$sheet->setCellValue('L1', 'alamat_penerima');
		$sheet->setCellValue('M1', 'hp_penerima');
		$sheet->setCellValue('N1', 'sub_sku');
		$sheet->setCellValue('O1', 'nama_produk');
		$sheet->setCellValue('P1', 'qty');

        // set Row
        $rowCount = 2;
        foreach ($data['penjualan'] as $list) {
        	$sheet->SetCellValue('A' . $rowCount, $list->tgl_penjualan);
            $sheet->SetCellValue('B' . $rowCount, $list->created);

        	// Nomor Pesanan
	        if (is_numeric($list->nomor_pesanan)) {
	          if (strlen($list->nomor_pesanan) < 15) {
	            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('C' . $rowCount, $list->nomor_pesanan);
	          }else{
	            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('C' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('C' . $rowCount, $list->nomor_pesanan);
	        }

            $sheet->SetCellValue('D' . $rowCount, $list->nama_toko);
            $sheet->SetCellValue('E' . $rowCount, $list->nama_kurir);

	        // Nomor Resi
	        if (is_numeric($list->nomor_resi)) {
	          if (strlen($list->nomor_resi) < 15) {
	            $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('F' . $rowCount, $list->nomor_resi);
	          }else{
	            $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('B' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('F' . $rowCount, $list->nomor_resi, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('F' . $rowCount, $list->nomor_resi);
	        }

            $sheet->SetCellValue('G' . $rowCount, $list->nama_status_transaksi);
            $sheet->SetCellValue('H' . $rowCount, $list->total_harga);
            $sheet->SetCellValue('I' . $rowCount, $list->nama_penerima);
            $sheet->SetCellValue('J' . $rowCount, $list->provinsi);
            $sheet->SetCellValue('K' . $rowCount, $list->kabupaten);
            $sheet->SetCellValue('L' . $rowCount, $list->alamat_penerima);

	        // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('M' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('M' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('M' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('M' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('M' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('M' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('M' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('M'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('M'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }

            $sheet->SetCellValue('N' . $rowCount, $list->sub_sku);
            $sheet->SetCellValue('O' . $rowCount, $list->nama_produk);
            $sheet->SetCellValue('P' . $rowCount, $list->qty);

           
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

	// public function export_resi()
	// {	
	// 	$status  = $this->uri->segment(4); 
	// 	$tanggal = $this->uri->segment(5);
	// 	$periodik = $this->uri->segment(6);

	// 	$start = substr($periodik, 0, 10);
	// 	$end = substr($periodik, 17, 24);
	// 	if ($tanggal == 'impor') {
	// 		$data['title']	= "Export Data Resi Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
	// 	}else{
	// 		$data['title']	= "Export Data Resi Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
	// 	}
		
	// 	$data['penjualan']	= $this->Dashboard_model->get_resi($status, $tanggal,$start, $end);

	// 	// echo print_r($repeat);
	// 	$this->load->view('back/dashboard/resi_export', $data);
	// }

	function export_resi()
	{
		$status  = $this->uri->segment(4); 
		$tanggal = $this->uri->segment(5);
		$periodik = $this->uri->segment(6);

		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		if ($tanggal == 'impor') {
			$data['title']	= "Export Data Resi Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
		}else{
			$data['title']	= "Export Data Resi Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
		}
		
		$data['penjualan']	= $this->Dashboard_model->get_resi($status, $tanggal,$start, $end);

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'tgl_penjualan');
		$sheet->setCellValue('B1', 'created');
		$sheet->setCellValue('C1', 'tgl_resi');		
		$sheet->setCellValue('D1', 'nomor_pesanan');
		$sheet->setCellValue('E1', 'nama_toko');
		$sheet->setCellValue('F1', 'nama_kurir');
		$sheet->setCellValue('G1', 'nomor_resi');
		$sheet->setCellValue('H1', 'nama_status_transaksi');
		$sheet->setCellValue('I1', 'status_resi');
		$sheet->setCellValue('J1', 'total_harga');
		$sheet->setCellValue('K1', 'nama_penerima');
		$sheet->setCellValue('L1', 'provinsi');
		$sheet->setCellValue('M1', 'kabupaten');
		$sheet->setCellValue('N1', 'alamat_penerima');
		$sheet->setCellValue('O1', 'hp_penerima');
		$sheet->setCellValue('P1', 'sub_sku');
		$sheet->setCellValue('Q1', 'nama_produk');
		$sheet->setCellValue('R1', 'qty');

        // set Row
        $rowCount = 2;
        foreach ($data['penjualan'] as $list) {
        	if ($list->status == 0) {
				$status_resi = "Belum Diproses";
			}else if($list->status == 1){
				$status_resi = "Sedang Diproses";
			}else if($list->status == 2){
				$status_resi = "Sudah Diproses";
			}else if($list->status == 3){
				$status_resi = "Retur";
			}

        	$sheet->SetCellValue('A' . $rowCount, $list->tgl_penjualan);
            $sheet->SetCellValue('B' . $rowCount, $list->created);
            $sheet->SetCellValue('C' . $rowCount, $list->tgl_resi);

        	// Nomor Pesanan
	        if (is_numeric($list->nomor_pesanan)) {
	          if (strlen($list->nomor_pesanan) < 15) {
	            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('D' . $rowCount, $list->nomor_pesanan);
	          }else{
	            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('D' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('D' . $rowCount, $list->nomor_pesanan);
	        }

            $sheet->SetCellValue('E' . $rowCount, $list->nama_toko);
            $sheet->SetCellValue('F' . $rowCount, $list->nama_kurir);

	        // Nomor Resi
	        if (is_numeric($list->nomor_resi)) {
	          if (strlen($list->nomor_resi) < 15) {
	            $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('G' . $rowCount, $list->nomor_resi);
	          }else{
	            $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('B' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('G' . $rowCount, $list->nomor_resi, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('G' . $rowCount, $list->nomor_resi);
	        }

            $sheet->SetCellValue('H' . $rowCount, $list->nama_status_transaksi);
            $sheet->SetCellValue('I' . $rowCount, $status_resi);
            $sheet->SetCellValue('J' . $rowCount, $list->total_harga);
            $sheet->SetCellValue('K' . $rowCount, $list->nama_penerima);
            $sheet->SetCellValue('L' . $rowCount, $list->provinsi);
            $sheet->SetCellValue('M' . $rowCount, $list->kabupaten);
            $sheet->SetCellValue('N' . $rowCount, $list->alamat_penerima);

	        // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('O' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('O' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('O' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('O' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('O' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('O' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('O' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('O'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('O'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }

            $sheet->SetCellValue('P' . $rowCount, $list->sub_sku);
            $sheet->SetCellValue('Q' . $rowCount, $list->nama_produk);
            $sheet->SetCellValue('R' . $rowCount, $list->qty);

           
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

	// public function export_retur()
	// {	
	// 	$status  = $this->uri->segment(4); 
	// 	$tanggal = $this->uri->segment(5);
	// 	$follup = $this->uri->segment(6);
	// 	$periodik = $this->uri->segment(7);

	// 	$start = substr($periodik, 0, 10);
	// 	$end = substr($periodik, 17, 24);
	// 	if ($tanggal == 'impor') {
	// 		$data['title']	= "Export Data Retur Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
	// 	}else{
	// 		$data['title']	= "Export Data Retur Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
	// 	}
		
	// 	$data['penjualan']	= $this->Dashboard_model->get_retur($status, $tanggal, $follup,$start, $end);

	// 	// echo print_r($repeat);
	// 	$this->load->view('back/dashboard/retur_export', $data);
	// }

	function export_retur()
	{
		$status  = $this->uri->segment(4); 
		$tanggal = $this->uri->segment(5);
		$follup = $this->uri->segment(6);
		$periodik = $this->uri->segment(7);

		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		if ($tanggal == 'impor') {
			$data['title']	= "Export Data Retur Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");	
		}else{
			$data['title']	= "Export Data Retur Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
		}
		
		$data['penjualan']	= $this->Dashboard_model->get_retur($status, $tanggal, $follup,$start, $end);

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'tgl_penjualan');
		$sheet->setCellValue('B1', 'created');
		$sheet->setCellValue('C1', 'tgl_retur');		
		$sheet->setCellValue('D1', 'nomor_pesanan');
		$sheet->setCellValue('E1', 'nama_toko');
		$sheet->setCellValue('F1', 'nama_kurir');
		$sheet->setCellValue('G1', 'nomor_resi');
		$sheet->setCellValue('H1', 'nama_status_transaksi');
		$sheet->setCellValue('I1', 'status_retur');
		$sheet->setCellValue('J1', 'status_follow_up');
		$sheet->setCellValue('K1', 'total_harga');
		$sheet->setCellValue('L1', 'nama_penerima');
		$sheet->setCellValue('M1', 'provinsi');
		$sheet->setCellValue('N1', 'kabupaten');
		$sheet->setCellValue('O1', 'alamat_penerima');
		$sheet->setCellValue('P1', 'hp_penerima');
		$sheet->setCellValue('Q1', 'sub_sku');
		$sheet->setCellValue('R1', 'nama_produk');
		$sheet->setCellValue('S1', 'qty');

        // set Row
        $rowCount = 2;
        foreach ($data['penjualan'] as $list) {
        	if ($list->status_retur == 0) {
				$status_retur = "Sedang Diproses";
			}else if($list->status_retur == 1){
				$status_retur = "Sudah Diproses";
			}

			if ($list->status_follow_up == 0) {
				$status_follow_up = "Belum Difollow Up";
			}else if($list->status_follow_up == 1){
				$status_follow_up = "Sudah Difollow Up";
			}

        	$sheet->SetCellValue('A' . $rowCount, $list->tgl_penjualan);
            $sheet->SetCellValue('B' . $rowCount, $list->created);
            $sheet->SetCellValue('C' . $rowCount, $list->tgl_retur);

        	// Nomor Pesanan
	        if (is_numeric($list->nomor_pesanan)) {
	          if (strlen($list->nomor_pesanan) < 15) {
	            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('D' . $rowCount, $list->nomor_pesanan);
	          }else{
	            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('D' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('D' . $rowCount, $list->nomor_pesanan);
	        }

            $sheet->SetCellValue('E' . $rowCount, $list->nama_toko);
            $sheet->SetCellValue('F' . $rowCount, $list->nama_kurir);

	        // Nomor Resi
	        if (is_numeric($list->nomor_resi)) {
	          if (strlen($list->nomor_resi) < 15) {
	            $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('G' . $rowCount, $list->nomor_resi);
	          }else{
	            $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('B' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('G' . $rowCount, $list->nomor_resi, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('G' . $rowCount, $list->nomor_resi);
	        }

            $sheet->SetCellValue('H' . $rowCount, $list->nama_status_transaksi);
            $sheet->SetCellValue('I' . $rowCount, $status_retur);
            $sheet->SetCellValue('J' . $rowCount, $status_follow_up);
            $sheet->SetCellValue('K' . $rowCount, $list->total_harga);
            $sheet->SetCellValue('L' . $rowCount, $list->nama_penerima);
            $sheet->SetCellValue('M' . $rowCount, $list->provinsi);
            $sheet->SetCellValue('N' . $rowCount, $list->kabupaten);
            $sheet->SetCellValue('O' . $rowCount, $list->alamat_penerima);

	        // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('P' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('P' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('P' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('P' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('P' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('P' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('P' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('P'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('P'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }

            $sheet->SetCellValue('Q' . $rowCount, $list->sub_sku);
            $sheet->SetCellValue('R' . $rowCount, $list->nama_produk);
            $sheet->SetCellValue('S' . $rowCount, $list->qty);

           
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

	// public function export_repeat_modal_penjualan()
	// {	
	// 	$hp  = $this->uri->segment(4); 
	// 	$first = $this->uri->segment(5);
	// 	$last = $this->uri->segment(6);
	// 	$trigger = $this->uri->segment(7);

	// 	if ($trigger == 'impor') {
	// 		$data['title']	= "Export Data Penjualan Repeat Order Per Tanggal Impor ".$first." - ".$last."_".date("H_i_s");	
	// 	}else{
	// 		$data['title']	= "Export Data Penjualan Repeat Order Per Tanggal Penjualan ".$first." - ".$last."_".date("H_i_s");
	// 	}
		
	// 	$data['penjualan']	= $this->Dashboard_model->get_repeat($hp, $first, $last, $trigger);

	// 	// echo print_r($repeat);
	// 	$this->load->view('back/dashboard/penjualan_export', $data);
	// }

	function export_repeat_modal_penjualan()
	{
		$hp  = $this->uri->segment(4); 
		$first = $this->uri->segment(5);
		$last = $this->uri->segment(6);
		$trigger = $this->uri->segment(7);

		if ($trigger == 'impor') {
			$data['title']	= "Export Data Penjualan Repeat Order Per Tanggal Impor ".$first." - ".$last."_".date("H_i_s");	
		}else{
			$data['title']	= "Export Data Penjualan Repeat Order Per Tanggal Penjualan ".$first." - ".$last."_".date("H_i_s");
		}
		
		$data['penjualan']	= $this->Dashboard_model->get_repeat($hp, $first, $last, $trigger);

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'tgl_penjualan');
		$sheet->setCellValue('B1', 'created');
		$sheet->setCellValue('C1', 'nomor_pesanan');
		$sheet->setCellValue('D1', 'nama_toko');
		$sheet->setCellValue('E1', 'nama_kurir');
		$sheet->setCellValue('F1', 'nomor_resi');
		$sheet->setCellValue('G1', 'nama_status_transaksi');
		$sheet->setCellValue('H1', 'total_harga');
		$sheet->setCellValue('I1', 'nama_penerima');
		$sheet->setCellValue('J1', 'provinsi');
		$sheet->setCellValue('K1', 'kabupaten');
		$sheet->setCellValue('L1', 'alamat_penerima');
		$sheet->setCellValue('M1', 'hp_penerima');
		$sheet->setCellValue('N1', 'sub_sku');
		$sheet->setCellValue('O1', 'nama_produk');
		$sheet->setCellValue('P1', 'qty');

        // set Row
        $rowCount = 2;
        foreach ($data['penjualan'] as $list) {
        	$sheet->SetCellValue('A' . $rowCount, $list->tgl_penjualan);
            $sheet->SetCellValue('B' . $rowCount, $list->created);

        	// Nomor Pesanan
	        if (is_numeric($list->nomor_pesanan)) {
	          if (strlen($list->nomor_pesanan) < 15) {
	            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('C' . $rowCount, $list->nomor_pesanan);
	          }else{
	            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('C' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('C' . $rowCount, $list->nomor_pesanan);
	        }

            $sheet->SetCellValue('D' . $rowCount, $list->nama_toko);
            $sheet->SetCellValue('E' . $rowCount, $list->nama_kurir);

	        // Nomor Resi
	        if (is_numeric($list->nomor_resi)) {
	          if (strlen($list->nomor_resi) < 15) {
	            $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	            $sheet->SetCellValue('F' . $rowCount, $list->nomor_resi);
	          }else{
	            $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	            // The old way to force string. NumberFormat::FORMAT_TEXT is not
	            // enough.
	            // $formatted_value .= ' ';
	            // $sheet->SetCellValue('B' . $rowCount, "'".$formatted_value);
	            $sheet->setCellValueExplicit('F' . $rowCount, $list->nomor_resi, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          }
	        }else{
	          $sheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	          $sheet->SetCellValue('F' . $rowCount, $list->nomor_resi);
	        }

            $sheet->SetCellValue('G' . $rowCount, $list->nama_status_transaksi);
            $sheet->SetCellValue('H' . $rowCount, $list->total_harga);
            $sheet->SetCellValue('I' . $rowCount, $list->nama_penerima);
            $sheet->SetCellValue('J' . $rowCount, $list->provinsi);
            $sheet->SetCellValue('K' . $rowCount, $list->kabupaten);
            $sheet->SetCellValue('L' . $rowCount, $list->alamat_penerima);

	        // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('M' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('M' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('M' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('M' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('M' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('M' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('M' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('M'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('M'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }

            $sheet->SetCellValue('N' . $rowCount, $list->sub_sku);
            $sheet->SetCellValue('O' . $rowCount, $list->nama_produk);
            $sheet->SetCellValue('P' . $rowCount, $list->qty);

           
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

	// public function export_repeat($periodik)
	// {
	// 	$start = substr($periodik, 0, 10);
	// 	$end = substr($periodik, 17, 24);
	// 	$data['title']	= "Export Data Repeat Order Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");
	// 	$data['repeat']	= $this->Dashboard_model->get_customer_repeat($start, $end);

	// 	// echo print_r($repeat);
	// 	$this->load->view('back/dashboard/repeat_export', $data);
	// }

	function export_repeat($periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Repeat Order Per Tanggal Impor ".$start." - ".$end."_".date("H_i_s");
		$data['repeat']	= $this->Dashboard_model->get_customer_repeat($start, $end);

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'nama_penerima');
		$sheet->setCellValue('B1', 'provinsi');
		$sheet->setCellValue('C1', 'kabupaten');
		$sheet->setCellValue('D1', 'hp_penerima');
		$sheet->setCellValue('E1', 'alamat_penerima');
		$sheet->setCellValue('F1', 'jumlah_penerima');

        // set Row
        $rowCount = 2;
        foreach ($data['repeat'] as $list) {
        	// Nomor Pesanan
	        // if (is_numeric($list->nomor_pesanan)) {
	        //   if (strlen($list->nomor_pesanan) < 15) {
	        //     $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	        //     $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
	        //   }else{
	        //     $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	        //     // The old way to force string. NumberFormat::FORMAT_TEXT is not
	        //     // enough.
	        //     // $formatted_value .= ' ';
	        //     // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
	        //     $sheet->setCellValueExplicit('A' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        //   }
	        // }else{
	        //   $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	        //   $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
	        // }

            $sheet->SetCellValue('A' . $rowCount, $list->nama_penerima);
            $sheet->SetCellValue('B' . $rowCount, $list->provinsi);
            $sheet->SetCellValue('C' . $rowCount, $list->kabupaten);
            
            // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('D' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('D' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('D' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('D' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('D' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('D' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('D' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('D'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('D'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }

            $sheet->SetCellValue('E' . $rowCount, $list->alamat_penerima);
            $sheet->SetCellValue('F' . $rowCount, $list->jumlah_penerima);

           
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

	// public function export_repeat_penjualan($periodik)
	// {
	// 	$start = substr($periodik, 0, 10);
	// 	$end = substr($periodik, 17, 24);
	// 	$data['title']	= "Export Data Repeat Order Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
	// 	$data['repeat']	= $this->Dashboard_model->get_customer_repeat_penjualan($start, $end);

	// 	// echo print_r($repeat);
	// 	$this->load->view('back/dashboard/repeat_export', $data);
	// }

	function export_repeat_penjualan($periodik)
	{
		$start = substr($periodik, 0, 10);
		$end = substr($periodik, 17, 24);
		$data['title']	= "Export Data Repeat Order Per Tanggal Penjualan ".$start." - ".$end."_".date("H_i_s");
		$data['repeat']	= $this->Dashboard_model->get_customer_repeat_penjualan($start, $end);

		// PHPOffice
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'nama_penerima');
		$sheet->setCellValue('B1', 'provinsi');
		$sheet->setCellValue('C1', 'kabupaten');
		$sheet->setCellValue('D1', 'hp_penerima');
		$sheet->setCellValue('E1', 'alamat_penerima');
		$sheet->setCellValue('F1', 'jumlah_penerima');

        // set Row
        $rowCount = 2;
        foreach ($data['repeat'] as $list) {
        	// Nomor Pesanan
	        // if (is_numeric($list->nomor_pesanan)) {
	        //   if (strlen($list->nomor_pesanan) < 15) {
	        //     $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
	        //     $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
	        //   }else{
	        //     $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	        //     // The old way to force string. NumberFormat::FORMAT_TEXT is not
	        //     // enough.
	        //     // $formatted_value .= ' ';
	        //     // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
	        //     $sheet->setCellValueExplicit('A' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	        //   }
	        // }else{
	        //   $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
	        //   $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
	        // }

            $sheet->SetCellValue('A' . $rowCount, $list->nama_penerima);
            $sheet->SetCellValue('B' . $rowCount, $list->provinsi);
            $sheet->SetCellValue('C' . $rowCount, $list->kabupaten);
            
            // Nomor HP
	        if (is_numeric($list->hp_penerima)) {
	          if (strlen($list->hp_penerima) < 15) {
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {

	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->setCellValueExplicit('D' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {
	          		// $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
		           //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

		            $ceknoldi62 = substr($list->hp_penerima, 0, 3);
		          	   if ($ceknoldi62 == '620') {
		          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('D' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }else{
		          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			            // The old way to force string. NumberFormat::FORMAT_TEXT is not
			            // enough.
			            // $formatted_value .= ' ';
			            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
			            $sheet->setCellValueExplicit('D' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		          	   }			
	          	}
	          }else{
	          	$firstCharacter = substr($list->hp_penerima, 0, 1);
	          	if ($firstCharacter == '0') {
	          		$edit_no = substr_replace($list->hp_penerima,"62",0, 1);
	          		$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('D' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	}else if ($firstCharacter == '6') {

	          		$ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
	          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('D' . $rowCount, substr_replace($list->hp_penerima,"62",0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }else{
	          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            // The old way to force string. NumberFormat::FORMAT_TEXT is not
		            // enough.
		            // $formatted_value .= ' ';
		            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
		            $sheet->setCellValueExplicit('D' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	          	   }		          
	          	}
	          }
	        }else{
	          $firstCharacter = substr($list->hp_penerima, 0, 1);
	          if ($firstCharacter == '0') {
	          	  $edit_no = substr_replace($list->hp_penerima,"62",0, 1);	
	      		  $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		          $sheet->SetCellValue('D' . $rowCount, $edit_no);
	          }else if ($firstCharacter == '6') {
	          	   $ceknoldi62 = substr($list->hp_penerima, 0, 3);
	          	   if ($ceknoldi62 == '620') {
		            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
		            $sheet->SetCellValue('D'.$rowCount, substr_replace($list->hp_penerima,"62",0, 3));	
	          	   }else{
	          	   	$sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
			        $sheet->SetCellValue('D'.$rowCount, $list->hp_penerima);	
	          	   }		         		
	          }
	        }

            $sheet->SetCellValue('E' . $rowCount, $list->alamat_penerima);
            $sheet->SetCellValue('F' . $rowCount, $list->jumlah_penerima);

           
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

	// DASBOR COUNT IMPOR
	function dasbor_list_count_penjualan(){
		$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_penjualan($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
        					'pending'	=> $data->pending,
			        		'transfer'	=> $data->transfer,
			        		'diterima'	=> $data->diterima,
			        		'retur'		=> $data->retur,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_resi(){
    	$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_resi($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
        					'belum'		=> $data->belum,
			        		'sedang'	=> $data->sedang,
			        		'sudah'		=> $data->sudah,
			        		'retur'		=> $data->retur,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_retur(){
    	$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_retur($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'			=> $data->total,
        					'diproses'		=> $data->diproses,
			        		'sudah'			=> $data->sudah,
			        		'belum_fu'		=> $data->belum_fu,
			        		'sudah_fu'		=> $data->sudah_fu,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    // DASBOR COUNT REAL
	function dasbor_list_count_penjualan_real(){
		$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_penjualan_real($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
        					'pending'	=> $data->pending,
			        		'transfer'	=> $data->transfer,
			        		'diterima'	=> $data->diterima,
			        		'retur'		=> $data->retur,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_resi_real(){
    	$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_resi_real($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'		=> $data->total,
        					'belum'		=> $data->belum,
			        		'sedang'	=> $data->sedang,
			        		'sudah'		=> $data->sudah,
			        		'retur'		=> $data->retur,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

    function dasbor_list_count_retur_real(){
    	$start = substr($this->input->post('periodik'), 0, 10);
		$end = substr($this->input->post('periodik'), 13, 24);
		$data    = $this->Dashboard_model->get_dasbor_list_retur_real($start, $end);
    	if (isset($data)) {	
        	$msg = array(	'total'			=> $data->total,
        					'diproses'		=> $data->diproses,
			        		'sudah'			=> $data->sudah,
			        		'belum_fu'		=> $data->belum_fu,
			        		'sudah_fu'		=> $data->sudah_fu,
        			);
        	echo json_encode($msg); 
    	}else {
    		$msg = array(	'validasi'	=> validation_errors()
        			);
        	echo json_encode($msg);
    	}
    }

	public function ajax_dasbor_prosku()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_sku 		= $this->Dashboard_model->get_sku($start, $end);
		$sku_data 		= array();
		$produk_data 	= array();
		$i = 0;
		foreach ($get_sku as $val_sku) {
			$int_jumlah_sku = intval($val_sku->jumlah_sku);
			
			$sku_data[] = array( 'name' 		=> $val_sku->nama_sku,
								  'y' 	 		=> $int_jumlah_sku,
								  'drilldown'   => $val_sku->nama_sku,
			);

			$produk_data[$i] = array( 'name' 		=> $val_sku->nama_sku,
								   	  'id'	 	 	=> $val_sku->nama_sku,
								   	  'dataLabels'	=> array(	'enabled'	=> false
								   	  )
			);

			$get_produk = $this->Dashboard_model->get_produk_by_sku($start, $end, $val_sku->sku_id);
			foreach ($get_produk as $val_pro) {
				$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
				if (isset($cek_propak)) {
					foreach ($cek_propak as $val_propak) {
						$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
						
						$produk_data[$i]['data'][] = array( 0	=> $val_propak->nama_produk,
											 		     	1	=> $int_fix
						);
					}
				}else{
					// $int_jumlah = intval($val_pro->jumlah_produk);
				
					// $produk[] = array( 0 => $val_pro->nama_produk,
					// 						  1 => $int_jumlah
					// );
				}
				$int_jumlah_pro = intval($val_pro->jumlah_produk);

				$produk_data[$i]['data'][] = array( 0	=> $val_pro->nama_produk,
									 		     	1	=> $int_jumlah_pro
				);
			}

			$i++;
		}

		$result = array(	'sku'		=> $sku_data,
							'produk'	=> $produk_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
	}

	public function ajax_dasbor_prosku_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_sku 		= $this->Dashboard_model->get_sku_penjualan($start, $end);
		$sku_data 		= array();
		$produk_data 	= array();
		$i = 0;
		foreach ($get_sku as $val_sku) {
			$int_jumlah_sku = intval($val_sku->jumlah_sku);
			
			$sku_data[] = array( 'name' 		=> $val_sku->nama_sku,
								  'y' 	 		=> $int_jumlah_sku,
								  'drilldown'   => $val_sku->nama_sku,
			);

			$produk_data[$i] = array( 'name' 		=> $val_sku->nama_sku,
								   	  'id'	 	 	=> $val_sku->nama_sku,
								   	  'dataLabels'	=> array(	'enabled'	=> false
								   	  )
			);

			$get_produk = $this->Dashboard_model->get_produk_by_sku_penjualan($start, $end, $val_sku->sku_id);
			foreach ($get_produk as $val_pro) {
				$cek_propak = $this->Dashboard_model->get_pakduk_produk_by_produk($val_pro->produk_id);
				if (isset($cek_propak)) {
					foreach ($cek_propak as $val_propak) {
						$int_fix = intval($val_pro->jumlah_produk * $val_propak->qty_pakduk);
						
						$produk_data[$i]['data'][] = array( 0	=> $val_propak->nama_produk,
											 		     	1	=> $int_fix
						);
					}
				}else{
					// $int_jumlah = intval($val_pro->jumlah_produk);
				
					// $produk[] = array( 0 => $val_pro->nama_produk,
					// 						  1 => $int_jumlah
					// );
				}
				$int_jumlah_pro = intval($val_pro->jumlah_produk);

				$produk_data[$i]['data'][] = array( 0	=> $val_pro->nama_produk,
									 		     	1	=> $int_jumlah_pro
				);
			}

			$i++;
		}

		$result = array(	'sku'		=> $sku_data,
							'produk'	=> $produk_data,
							'tanggal'   => $start." - ".$end 
        			);
		// return $result;
    	echo json_encode($result);
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

	public function ajax_dasbor_jenis_toko_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_jenis_toko = $this->Dashboard_model->get_jenis_toko_penjualan($start, $end);
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

			$get_toko = $this->Dashboard_model->get_toko_by_jenis_penjualan($start, $end, $val_jenis->jenis_toko_id);
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
		$end 		= substr($this->input->post('periodik'), 13, 23);
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

		// Get Pending Payment
		$get_pending = $this->Dashboard_model->get_pending_payment($start, $end);
		$get_pending_past = $this->Dashboard_model->get_pending_payment($start_past, $end_past);

		// Get Pesanan
		$get_pesan = $this->Dashboard_model->get_total_pesanan_by_periodik($start, $end);
		$get_pesan_past = $this->Dashboard_model->get_total_pesanan_by_periodik($start_past, $end_past);

		// Mencari nilai MAX dari 2 variabel
		$max_margin = max(array($get_income->margin, $get_income_past->margin));
		$max_selisih_margin = max(array($get_income->selisih_margin, $get_income_past->selisih_margin));
		$max_hpp = max(array($get_income->hpp, $get_income_past->hpp));
		$max_diterima = max(array($get_income->diterima, $get_income_past->diterima));
		$max_pending  = max(array($get_pending->total_pending, $get_pending_past->total_pending));
		$max_gross   = max(array($get_income->fix, $get_income_past->fix));
		$max_bruto   = max(array($get_income->bruto, $get_income_past->bruto));
		$max_revenue = max(array($get_income->total, $get_income_past->total));
		$max_ongkir   = max(array($get_income->tot_ongkir, $get_income_past->tot_ongkir));
		$max_pesan	  = max(array($get_pesan->jumlah_tanggal, $get_pesan_past->jumlah_tanggal));

		if ($max_diterima == NULL && $max_gross == NULL && $max_hpp == NULL && $max_revenue == NULL && $max_ongkir == NULL && $max_pesan == 0 && $max_pending == NULL && $max_bruto == NULL && $max_margin == NULL && $max_selisih_margin == NULL) {
			$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PESANAN</span>';

			$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL DITERIMA</span>';

            $html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';

            $html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL REVENUE</span>';
            
            $html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
               	 			 
            $html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL ONGKIR</span>';   

            $html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL BRUTO</span>';   

            $html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL MARGIN</span>';   

            $html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
									'<h5 class="description-header">0</h5>'.
		               	 			'<span class="description-text">TOTAL SELISIH MARGIN</span>';   

 			$html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
									'<h5 class="description-header">0</h5>'.
						 			'<span class="description-text">TOTAL HPP</span>';  	 			 
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

			// PENDING PAYMENT
			if ($max_pending == 0) {
				$html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
			}else{
				$persen_pending	= (($get_pending->total_pending - $get_pending_past->total_pending) / $max_pending) * 100;
				$sisa_pending	= $get_pending->total_pending - $get_pending_past->total_pending;

				if ($persen_pending == 0 && $sisa_pending == 0) {
					$html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}else if ($persen_pending < 0 && $sisa_pending < 0) {
					$html_pending = '<span class="description-percentage text-green"> -'.rupiah($sisa_pending * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_pending * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}else if ($persen_pending > 0 && $sisa_pending > 0) {
					$html_pending = '<span class="description-percentage text-red">+'.rupiah($sisa_pending).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_pending).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}
			}

			// GROSS REVENUE
			if ($max_gross == 0) {
				$html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL GROSS REVENUE</span>';
			}else{
				$persen_gross		= (($get_income->fix - $get_income_past->fix) / $max_gross) * 100;
				$sisa_gross		= $get_income->fix - $get_income_past->fix;

				if ($persen_gross == 0 && $sisa_gross == 0) {
					$html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}else if ($persen_gross < 0 && $sisa_gross < 0) {
					$html_gross = '<span class="description-percentage text-red"> -'.rupiah($sisa_gross * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_gross * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}else if ($persen_gross > 0 && $sisa_gross > 0) {
					$html_gross = '<span class="description-percentage text-green"> +'.rupiah($sisa_gross).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_gross).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}
			}

			// BRUTO
			if ($max_bruto == 0) {
				$html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL BRUTO/span>';
			}else{
				$persen_bruto		= (($get_income->bruto - $get_income_past->bruto) / $max_bruto) * 100;
				$sisa_bruto		= $get_income->bruto - $get_income_past->bruto;

				if ($persen_bruto == 0 && $sisa_bruto == 0) {
					$html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->bruto).'</h5>'.
		               	 			 '<span class="description-text">TOTAL BRUTO</span>';
				}else if ($persen_bruto < 0 && $sisa_bruto < 0) {
					$html_bruto = '<span class="description-percentage text-red"> -'.rupiah($sisa_bruto * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_bruto * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->bruto).'</h5>'.
		               	 			 '<span class="description-text">TOTAL BRUTO</span>';
				}else if ($persen_bruto > 0 && $sisa_bruto > 0) {
					$html_bruto = '<span class="description-percentage text-green"> +'.rupiah($sisa_bruto).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_bruto).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->bruto).'</h5>'.
		               	 			 '<span class="description-text">TOTAL BRUTO</span>';
				}
			}

			// REVENUE
			if ($max_revenue == 0) {
				$html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL REVENUE</span>';
			}else{
				$persen_revenue	= (($get_income->total - $get_income_past->total) / $max_revenue) * 100;
				$sisa_revenue		= $get_income->total - $get_income_past->total;

				if ($persen_revenue == 0 && $sisa_revenue == 0) {
					$html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}else if ($persen_revenue < 0 && $sisa_revenue < 0) {
					$html_revenue = '<span class="description-percentage text-red"> -'.rupiah($sisa_revenue * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_revenue * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}else if ($persen_revenue > 0 && $sisa_revenue > 0) {
					$html_revenue = '<span class="description-percentage text-green"> +'.rupiah($sisa_revenue).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_revenue).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
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
					$html_ongkir = '<span class="description-percentage text-green"> -'.rupiah($sisa_ongkir * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_ongkir * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}else if ($persen_ongkir > 0 && $sisa_ongkir > 0) {
					$html_ongkir = '<span class="description-percentage text-red"> +'.rupiah($sisa_ongkir).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_ongkir).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}
			}

			// MARGIN
			if ($max_margin == 0) {
				$html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL MARGIN</span>';
			}else{
				$persen_margin	= (($get_income->margin - $get_income_past->margin) / $max_margin) * 100;
				$sisa_margin		= $get_income->margin - $get_income_past->margin;

				if ($persen_margin == 0 && $sisa_margin == 0) {
					$html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_income->margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
				}else if ($persen_margin < 0 && $sisa_margin < 0) {
					$html_margin = '<span class="description-percentage text-red"> -'.rupiah($sisa_margin * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_margin * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
				}else if ($persen_margin > 0 && $sisa_margin > 0) {
					$html_margin = '<span class="description-percentage text-green">+'.rupiah($sisa_margin).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_margin).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
				}
			}

			// SELISIH MARGIN
			if ($max_selisih_margin == 0) {
				$html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL SELISIH MARGIN</span>';
			}else{
				$persen_selisih_margin	= (($get_income->selisih_margin - $get_income_past->selisih_margin) / $max_selisih_margin) * 100;
				$sisa_selisih_margin		= $get_income->selisih_margin - $get_income_past->selisih_margin;

				if ($persen_selisih_margin == 0 && $sisa_selisih_margin == 0) {
					$html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_income->selisih_margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
				}else if ($persen_selisih_margin < 0 || $sisa_selisih_margin < 0) {
					$html_selisih_margin = '<span class="description-percentage text-red"> -'.rupiah($sisa_selisih_margin).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_selisih_margin).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->selisih_margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
				}else if ($persen_selisih_margin > 0 && $sisa_selisih_margin > 0) {
					$html_selisih_margin = '<span class="description-percentage text-green">+'.rupiah($sisa_selisih_margin).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_selisih_margin).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->selisih_margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
				}
			}

			// HPP
			if ($max_hpp == 0) {
				$html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL HPP</span>';
			}else{
				$persen_hpp	= (($get_income->hpp - $get_income_past->hpp) / $max_hpp) * 100;
				$sisa_hpp		= $get_income->hpp - $get_income_past->hpp;

				if ($persen_hpp == 0 && $sisa_hpp == 0) {
					$html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_income->hpp).'</h5>'.
		               	 			 '<span class="description-text">TOTAL HPP</span>';
				}else if ($persen_hpp < 0 && $sisa_hpp < 0) {
					$html_hpp = '<span class="description-percentage text-red"> -'.rupiah($sisa_hpp * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_hpp * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->hpp).'</h5>'.
		               	 			 '<span class="description-text">TOTAL HPP</span>';
				}else if ($persen_hpp > 0 && $sisa_hpp > 0) {
					$html_hpp = '<span class="description-percentage text-green">+'.rupiah($sisa_hpp).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_hpp).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->hpp).'</h5>'.
		               	 			 '<span class="description-text">TOTAL HPP</span>';
				}
			}
			
			// // End Mencari total persen dari range angka
		}

		$result = array( 'gross'  			=> $html_gross,
						 'diterima'			=> $html_diterima,
						 'pending'			=> $html_pending,
						 // 'judul'   => 'Statistik Data Keuangan Tanggal: '.$start.' - '.$end.' dengan Tanggal: '.$start_past.' - '.$end_past.' ('.$fix_jarak.' Hari)', 
						 // 'hpp' 	   => $hpp,
						 'revenue'  		=> $html_revenue,
						 'bruto'    		=> $html_bruto,
						 'ongkir'  			=> $html_ongkir,
						 'pesan'  			=> $html_pesan,
						 'margin'			=> $html_margin,
						 'selisih_margin'	=> $html_selisih_margin,
						 'hpp'				=> $html_hpp,
		);

		echo json_encode($result);
	}

	public function ajax_dasbor_total_penjualan()
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
		$get_income	= $this->Dashboard_model->get_pendapat_dasbor_penjualan($start, $end);
		$get_income_past = $this->Dashboard_model->get_pendapat_dasbor_penjualan($start_past, $end_past);

		// Get Pending Payment
		$get_pending = $this->Dashboard_model->get_pending_payment_penjualan($start, $end);
		$get_pending_past = $this->Dashboard_model->get_pending_payment_penjualan($start_past, $end_past);

		// Get Pesanan
		$get_pesan = $this->Dashboard_model->get_total_pesanan_by_periodik_penjualan($start, $end);
		$get_pesan_past = $this->Dashboard_model->get_total_pesanan_by_periodik_penjualan($start_past, $end_past);

		// Mencari nilai MAX dari 2 variabel
		$max_margin = max(array($get_income->margin, $get_income_past->margin));
		$max_selisih_margin = max(array($get_income->selisih_margin, $get_income_past->selisih_margin));
		$max_hpp = max(array($get_income->hpp, $get_income_past->hpp));
		$max_diterima = max(array($get_income->diterima, $get_income_past->diterima));
		$max_pending  = max(array($get_pending->total_pending, $get_pending_past->total_pending));
		$max_gross   = max(array($get_income->fix, $get_income_past->fix));
		$max_bruto   = max(array($get_income->bruto, $get_income_past->bruto));
		$max_revenue = max(array($get_income->total, $get_income_past->total));
		$max_ongkir   = max(array($get_income->tot_ongkir, $get_income_past->tot_ongkir));
		$max_pesan	  = max(array($get_pesan->jumlah_tanggal, $get_pesan_past->jumlah_tanggal));

		if ($max_diterima == NULL && $max_gross == NULL && $max_hpp == NULL && $max_revenue == NULL && $max_ongkir == NULL && $max_pesan == 0 && $max_pending == NULL && $max_bruto == NULL && $max_margin == NULL && $max_selisih_margin == NULL) {
			$html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PESANAN</span>';

			$html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL DITERIMA</span>';

            $html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';

            $html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL REVENUE</span>';
            
            $html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
               	 			 
            $html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL ONGKIR</span>';   

            $html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL BRUTO</span>';   

            $html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL MARGIN</span>';   

            $html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
									'<h5 class="description-header">0</h5>'.
		               	 			'<span class="description-text">TOTAL SELISIH MARGIN</span>';   

 			$html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
									'<h5 class="description-header">0</h5>'.
						 			'<span class="description-text">TOTAL HPP</span>';  	 			 
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

			// PENDING PAYMENT
			if ($max_pending == 0) {
				$html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
			}else{
				$persen_pending	= (($get_pending->total_pending - $get_pending_past->total_pending) / $max_pending) * 100;
				$sisa_pending	= $get_pending->total_pending - $get_pending_past->total_pending;

				if ($persen_pending == 0 && $sisa_pending == 0) {
					$html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}else if ($persen_pending < 0 && $sisa_pending < 0) {
					$html_pending = '<span class="description-percentage text-green"> -'.rupiah($sisa_pending * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_pending * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}else if ($persen_pending > 0 && $sisa_pending > 0) {
					$html_pending = '<span class="description-percentage text-red">+'.rupiah($sisa_pending).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_pending).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_pending->total_pending).'</h5>'.
		               	 			 '<span class="description-text">TOTAL PENDING PAYMENT</span>';
				}
			}

			// GROSS REVENUE
			if ($max_gross == 0) {
				$html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL GROSS REVENUE</span>';
			}else{
				$persen_gross		= (($get_income->fix - $get_income_past->fix) / $max_gross) * 100;
				$sisa_gross		= $get_income->fix - $get_income_past->fix;

				if ($persen_gross == 0 && $sisa_gross == 0) {
					$html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}else if ($persen_gross < 0 && $sisa_gross < 0) {
					$html_gross = '<span class="description-percentage text-red"> -'.rupiah($sisa_gross * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_gross * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}else if ($persen_gross > 0 && $sisa_gross > 0) {
					$html_gross = '<span class="description-percentage text-green"> +'.rupiah($sisa_gross).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_gross).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->fix).'</h5>'.
		               	 			 '<span class="description-text">TOTAL GROSS REVENUE</span>';
				}
			}

			// BRUTO
			if ($max_bruto == 0) {
				$html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							   '<h5 class="description-header">0</h5>'.
	               	 		   '<span class="description-text">TOTAL BRUTO/span>';
			}else{
				$persen_bruto		= (($get_income->bruto - $get_income_past->bruto) / $max_bruto) * 100;
				$sisa_bruto		= $get_income->bruto - $get_income_past->bruto;

				if ($persen_bruto == 0 && $sisa_bruto == 0) {
					$html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->bruto).'</h5>'.
		               	 			 '<span class="description-text">TOTAL BRUTO</span>';
				}else if ($persen_bruto < 0 && $sisa_bruto < 0) {
					$html_bruto = '<span class="description-percentage text-red"> -'.rupiah($sisa_bruto * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_bruto * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->bruto).'</h5>'.
		               	 			 '<span class="description-text">TOTAL BRUTO</span>';
				}else if ($persen_bruto > 0 && $sisa_bruto > 0) {
					$html_bruto = '<span class="description-percentage text-green"> +'.rupiah($sisa_bruto).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_bruto).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->bruto).'</h5>'.
		               	 			 '<span class="description-text">TOTAL BRUTO</span>';
				}
			}

			// REVENUE
			if ($max_revenue == 0) {
				$html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
							 '<h5 class="description-header">0</h5>'.
               	 			 '<span class="description-text">TOTAL REVENUE</span>';
			}else{
				$persen_revenue	= (($get_income->total - $get_income_past->total) / $max_revenue) * 100;
				$sisa_revenue		= $get_income->total - $get_income_past->total;

				if ($persen_revenue == 0 && $sisa_revenue == 0) {
					$html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}else if ($persen_revenue < 0 && $sisa_revenue < 0) {
					$html_revenue = '<span class="description-percentage text-red"> -'.rupiah($sisa_revenue * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_revenue * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
				}else if ($persen_revenue > 0 && $sisa_revenue > 0) {
					$html_revenue = '<span class="description-percentage text-green"> +'.rupiah($sisa_revenue).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_revenue).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->total).'</h5>'.
		               	 			 '<span class="description-text">TOTAL REVENUE</span>';
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
					$html_ongkir = '<span class="description-percentage text-green"> -'.rupiah($sisa_ongkir * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_ongkir * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}else if ($persen_ongkir > 0 && $sisa_ongkir > 0) {
					$html_ongkir = '<span class="description-percentage text-red"> +'.rupiah($sisa_ongkir).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_ongkir).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->tot_ongkir).'</h5>'.
		               	 			 '<span class="description-text">TOTAL ONGKIR</span>';
				}
			}

			// MARGIN
			if ($max_margin == 0) {
				$html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL MARGIN</span>';
			}else{
				$persen_margin	= (($get_income->margin - $get_income_past->margin) / $max_margin) * 100;
				$sisa_margin		= $get_income->margin - $get_income_past->margin;

				if ($persen_margin == 0 && $sisa_margin == 0) {
					$html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_income->margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
				}else if ($persen_margin < 0 && $sisa_margin < 0) {
					$html_margin = '<span class="description-percentage text-red"> -'.rupiah($sisa_margin * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_margin * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
				}else if ($persen_margin > 0 && $sisa_margin > 0) {
					$html_margin = '<span class="description-percentage text-green">+'.rupiah($sisa_margin).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_margin).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL MARGIN</span>';
				}
			}

			// SELISIH MARGIN
			if ($max_selisih_margin == 0) {
				$html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL SELISIH MARGIN</span>';
			}else{
				$persen_selisih_margin	= (($get_income->selisih_margin - $get_income_past->selisih_margin) / $max_selisih_margin) * 100;
				$sisa_selisih_margin		= $get_income->selisih_margin - $get_income_past->selisih_margin;

				if ($persen_selisih_margin == 0 && $sisa_selisih_margin == 0) {
					$html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_income->selisih_margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
				}else if ($persen_selisih_margin < 0 || $sisa_selisih_margin < 0) {
					$html_selisih_margin = '<span class="description-percentage text-red"> -'.rupiah($sisa_selisih_margin).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_selisih_margin).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->selisih_margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
				}else if ($persen_selisih_margin > 0 && $sisa_selisih_margin > 0) {
					$html_selisih_margin = '<span class="description-percentage text-green">+'.rupiah($sisa_selisih_margin).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_selisih_margin).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->selisih_margin).'</h5>'.
		               	 			 '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
				}
			}

			// HPP
			if ($max_hpp == 0) {
				$html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>'.
								 '<h5 class="description-header">0</h5>'.
	               	 			 '<span class="description-text">TOTAL HPP</span>';
			}else{
				$persen_hpp	= (($get_income->hpp - $get_income_past->hpp) / $max_hpp) * 100;
				$sisa_hpp		= $get_income->hpp - $get_income_past->hpp;

				if ($persen_hpp == 0 && $sisa_hpp == 0) {
					$html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>'.
									 '<h5 class="description-header">'.rupiah($get_income->hpp).'</h5>'.
		               	 			 '<span class="description-text">TOTAL HPP</span>';
				}else if ($persen_hpp < 0 && $sisa_hpp < 0) {
					$html_hpp = '<span class="description-percentage text-red"> -'.rupiah($sisa_hpp * -1).' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>'.round($persen_hpp * -1).' %)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->hpp).'</h5>'.
		               	 			 '<span class="description-text">TOTAL HPP</span>';
				}else if ($persen_hpp > 0 && $sisa_hpp > 0) {
					$html_hpp = '<span class="description-percentage text-green">+'.rupiah($sisa_hpp).' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>'.round($persen_hpp).'%)</span>'.
									 '<h5 class="description-header">'.rupiah($get_income->hpp).'</h5>'.
		               	 			 '<span class="description-text">TOTAL HPP</span>';
				}
			}
			
			// // End Mencari total persen dari range angka
		}

		$result = array( 'gross'  			=> $html_gross,
						 'diterima'			=> $html_diterima,
						 'pending'			=> $html_pending,
						 // 'judul'   => 'Statistik Data Keuangan Tanggal: '.$start.' - '.$end.' dengan Tanggal: '.$start_past.' - '.$end_past.' ('.$fix_jarak.' Hari)', 
						 // 'hpp' 	   => $hpp,
						 'revenue'  		=> $html_revenue,
						 'bruto'    		=> $html_bruto,
						 'ongkir'  			=> $html_ongkir,
						 'pesan'  			=> $html_pesan,
						 'margin'			=> $html_margin,
						 'selisih_margin'	=> $html_selisih_margin,
						 'hpp'				=> $html_hpp,
		);

		echo json_encode($result);
	}

	public function ajax_line_income()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_income		= $this->Dashboard_model->get_pendapat_periodik($start, $end);
		$margin 		= array();
		$selisih_margin = array();
		$hpp 			= array();
		$diterima 		= array();
		$gross 			= array();
		$bruto 			= array();
		$revenue 		= array();
		$ongkir 		= array();
		foreach ($get_income as $val_come) {
			$int_jumlah_margin = intval($val_come->margin);
			$int_jumlah_selisih_margin = intval($val_come->selisih_margin);
			$int_jumlah_hpp = intval($val_come->hpp);
			$int_jumlah_diterima = intval($val_come->diterima);
			$int_jumlah_gross = intval($val_come->fix);
			$int_jumlah_bruto = intval($val_come->bruto);
			$int_jumlah_revenue = intval($val_come->total);
			$int_jumlah_ongkir = intval($val_come->tot_ongkir);
			
			$margin[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_margin		
			);

			$selisih_margin[] = array( 0 => $val_come->tanggal,
							   		   1 => $int_jumlah_selisih_margin		
			);

			$hpp[] = array( 0 => $val_come->tanggal,
				   		    1 => $int_jumlah_hpp		
			);

			$diterima[] = array( 0 => $val_come->tanggal,
				   		    	 1 => $int_jumlah_diterima		
			);

			$gross[] = array( 0 => $val_come->tanggal,
				   		      1 => $int_jumlah_gross		
			);	

			$bruto[] = array( 0 => $val_come->tanggal,
				   		      1 => $int_jumlah_bruto		
			);

			$revenue[] = array( 0 => $val_come->tanggal,
				   		      	1 => $int_jumlah_revenue		
			);	

			$ongkir[] = array( 0 => $val_come->tanggal,
				   		       1 => $int_jumlah_ongkir		
			);			
		}

		$result = array( 'tanggal' 			=> $start." - ".$end,
						 'margin'  			=> $margin,
						 'selisih_margin'  	=> $selisih_margin,
						 'hpp'  			=> $hpp,
						 'diterima'			=> $diterima,
						 'gross'			=> $gross,
						 'bruto'			=> $bruto,
						 'revenue'  		=> $revenue,
						 'ongkir'  			=> $ongkir,
		);

		echo json_encode($result);
	}

	public function ajax_line_income_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_income	= $this->Dashboard_model->get_pendapat_periodik_penjualan($start, $end);
		$margin 		= array();
		$selisih_margin = array();
		$hpp 			= array();
		$diterima 		= array();
		$gross 			= array();
		$bruto 			= array();
		$revenue 		= array();
		$ongkir 		= array();
		foreach ($get_income as $val_come) {
			$int_jumlah_margin = intval($val_come->margin);
			$int_jumlah_selisih_margin = intval($val_come->selisih_margin);
			$int_jumlah_hpp = intval($val_come->hpp);
			$int_jumlah_diterima = intval($val_come->diterima);
			$int_jumlah_gross = intval($val_come->fix);
			$int_jumlah_bruto = intval($val_come->bruto);
			$int_jumlah_revenue = intval($val_come->total);
			$int_jumlah_ongkir = intval($val_come->tot_ongkir);
			
			$margin[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_margin		
			);

			$selisih_margin[] = array( 0 => $val_come->tanggal,
							   		   1 => $int_jumlah_selisih_margin		
			);

			$hpp[] = array( 0 => $val_come->tanggal,
				   		    1 => $int_jumlah_hpp		
			);

			$diterima[] = array( 0 => $val_come->tanggal,
				   		    	 1 => $int_jumlah_diterima		
			);

			$gross[] = array( 0 => $val_come->tanggal,
				   		      1 => $int_jumlah_gross		
			);	

			$bruto[] = array( 0 => $val_come->tanggal,
				   		      1 => $int_jumlah_bruto		
			);

			$revenue[] = array( 0 => $val_come->tanggal,
				   		      	1 => $int_jumlah_revenue		
			);	

			$ongkir[] = array( 0 => $val_come->tanggal,
				   		       1 => $int_jumlah_ongkir		
			);			
		}

		$result = array( 'tanggal' 			=> $start." - ".$end,
						 'margin'  			=> $margin,
						 'selisih_margin'  	=> $selisih_margin,
						 'hpp'  			=> $hpp,
						 'diterima'			=> $diterima,
						 'gross'			=> $gross,
						 'bruto'			=> $bruto,
						 'revenue'  		=> $revenue,
						 'ongkir'  			=> $ongkir,
		);

		echo json_encode($result);
	}

	public function get_data_produk_status()
	{
		$list = $this->Dashboard_model->get_datatables_produk_status();
        $dataJSON = array();
        foreach ($list as $data) {
        	if ($data->qty_produk <= 0) {
        		$status = '<span class="label label-danger">Habis</span>';
        	}else if($data->qty_produk <= 10){
        		$status = '<span class="label label-warning">Sedikit</span>';
        	}else if($data->qty_produk > 10){
        		$status = '<span class="label label-success">Ada</span>';
        	}

			$rows[] = array( 'sku' 			=> $data->nama_sku, 
							 'sub_sku' 		=> $data->sub_sku,
							 'nama_produk'	=> $data->nama_produk,
							 'status' 		=> $status,
							 'stok' 		=> number_format($data->qty_produk,null,",",".")
			);

			$dataJSON = $rows;
		}

		$output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_produk_status(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_produk_status(),
            "data" => $dataJSON,
        );

		echo json_encode($output);
	}

	public function get_data_bahan_status()
	{
		$list = $this->Dashboard_model->get_datatables_bahan_status();
        $dataJSON = array();
        foreach ($list as $data) {
        	if ($data->qty_bahan_kemas <= 0) {
        		$status = '<span class="label label-danger">Habis</span>';
        	}else if($data->qty_bahan_kemas <= 10){
        		$status = '<span class="label label-warning">Terbatas</span>';
        	}else if($data->qty_bahan_kemas > 10){
        		$status = '<span class="label label-success">Ada</span>';
        	}

			$rows[] = array( 'sku' 				=> $data->kode_sku_bahan_kemas, 
							 'nama_bahan'		=> $data->nama_bahan_kemas,
							 'status' 			=> $status,
							 'stok' 			=> number_format($data->qty_bahan_kemas,null,",",".")
			);

			$dataJSON = $rows;
		}

		$output = array(
            "recordsTotal" => $this->Dashboard_model->count_all_bahan_status(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_bahan_status(),
            "data" => $dataJSON,
        );

		echo json_encode($output);
	}
	
	public function get_data_repeat()
	{
		$i = 1;
		$list = $this->Dashboard_model->get_datatables();
		$first = substr($_GET['periodik'], 0, 10);
	    $last = substr($_GET['periodik'], 13, 24);
        $dataJSON = array();
        foreach ($list as $data) {

			$jumlah = '<a href="javascript:void(0)" onclick="tabelDataRepeat(\''.$data->hp_penerima.'\',\''.$first.'\',\''.$last.'\', \'impor\')" class="btn btn-success btn-sm"><i class="fa fa-shopping-cart" style="margin-right:5px;"></i>'.$data->jumlah_penerima.' Pesanan</a>';
			$rows[] = array( 'no'				=> $i,
							 'nama_penerima' 	=> $data->nama_penerima, 
							 'provinsi' 		=> $data->provinsi,
							 'kabupaten'	    => $data->kabupaten,
							 'hp_penerima' 		=> $data->hp_penerima,
							 'alamat' 			=> $data->alamat_penerima,
							 'repeat' 			=> $jumlah
			);

			$i++;

			$dataJSON = $rows;
		}

		$output = array(
            "recordsTotal" => $this->Dashboard_model->count_all(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered(),
            "data" => $dataJSON,
        );

		echo json_encode($output);
	}

	public function get_data_repeat_penjualan()
	{
		$i = 1;
		$list = $this->Dashboard_model->get_datatables_penjualan();
		$first = substr($_GET['periodik'], 0, 10);
	    $last = substr($_GET['periodik'], 13, 24);
        $dataJSON = array();
        foreach ($list as $data) {
			$jumlah = '<a href="javascript:void(0)" onclick="tabelDataRepeat(\''.$data->hp_penerima.'\',\''.$first.'\',\''.$last.'\', \'penjualan\')" class="btn btn-success btn-sm"><i class="fa fa-shopping-cart" style="margin-right:5px;"></i>'.$data->jumlah_penerima.' Pesanan</a>';
			$rows[] = array( 'no'				=> $i,
							 'nama_penerima' 	=> $data->nama_penerima, 
							 'provinsi' 		=> $data->provinsi,
							 'kabupaten'	    => $data->kabupaten,
							 'hp_penerima' 		=> $data->hp_penerima,
							 'alamat' 			=> $data->alamat_penerima,
							 'repeat' 			=> $jumlah
			);

			$i++;

			$dataJSON = $rows;
		}

		$output = array(
            "recordsTotal" => $this->Dashboard_model->count_all(),
            "recordsFiltered" => $this->Dashboard_model->count_filtered_penjualan(),
            "data" => $dataJSON,
        );

		echo json_encode($output);
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

	public function ajax_pie_provkab_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_prov 	= $this->Dashboard_model->get_customer_provinsi_penjualan($start, $end);
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

	public function ajax_bar_produk_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_produk	= $this->Dashboard_model->get_produk_toko_periodik_penjualan($start, $end);
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

	public function ajax_bar_kurir_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_kurir	= $this->Dashboard_model->get_kurir_by_periodik_penjualan($start, $end);
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

	public function ajax_bar_pesanan_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_pesanan	= $this->Dashboard_model->get_pesanan_by_periodik_penjualan($start, $end);
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
		$margin 		= array();
		$selisih_margin = array();
		$hpp 			= array();
		$diterima 		= array();
		$gross 			= array();
		$bruto 			= array();
		$revenue 		= array();
		$ongkir 		= array();
		foreach ($get_income as $val_come) {
			$int_jumlah_margin = intval($val_come->margin);
			$int_jumlah_selisih_margin = intval($val_come->selisih_margin);
			$int_jumlah_hpp = intval($val_come->hpp);
			$int_jumlah_diterima = intval($val_come->diterima);
			$int_jumlah_gross = intval($val_come->fix);
			$int_jumlah_bruto = intval($val_come->bruto);
			$int_jumlah_revenue = intval($val_come->total);
			$int_jumlah_ongkir = intval($val_come->tot_ongkir);
			
			$margin[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_margin		
			);

			$selisih_margin[] = array( 0 => $val_come->tanggal,
							   		   1 => $int_jumlah_selisih_margin		
			);

			$hpp[] = array( 0 => $val_come->tanggal,
				   		    1 => $int_jumlah_hpp		
			);

			$diterima[] = array( 0 => $val_come->tanggal,
				   		    	 1 => $int_jumlah_diterima		
			);

			$gross[] = array( 0 => $val_come->tanggal,
				   		      1 => $int_jumlah_gross		
			);	

			$bruto[] = array( 0 => $val_come->tanggal,
				   		      1 => $int_jumlah_bruto		
			);

			$revenue[] = array( 0 => $val_come->tanggal,
				   		      	1 => $int_jumlah_revenue		
			);	

			$ongkir[] = array( 0 => $val_come->tanggal,
				   		       1 => $int_jumlah_ongkir		
			);			
		}

		$result = array( 'tanggal' 			=> $start." - ".$end,
						 'margin'  			=> $margin,
						 'selisih_margin'  	=> $selisih_margin,
						 'hpp'  			=> $hpp,
						 'diterima'			=> $diterima,
						 'gross'			=> $gross,
						 'bruto'			=> $bruto,
						 'revenue'  		=> $revenue,
						 'ongkir'  			=> $ongkir,
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

	// Retur Penjualan
	public function ajax_dasbor_jenis_toko_retur_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_jenis_toko = $this->Dashboard_model->get_jenis_toko_retur_penjualan($start, $end);
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

			$get_toko = $this->Dashboard_model->get_toko_by_jenis_retur_penjualan($start, $end, $val_jenis->jenis_toko_id);
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
	
	public function ajax_line_income_retur_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_income	= $this->Dashboard_model->get_pendapat_periodik_retur_penjualan($start, $end);
		$margin 		= array();
		$selisih_margin = array();
		$hpp 			= array();
		$diterima 		= array();
		$gross 			= array();
		$bruto 			= array();
		$revenue 		= array();
		$ongkir 		= array();
		foreach ($get_income as $val_come) {
			$int_jumlah_margin = intval($val_come->margin);
			$int_jumlah_selisih_margin = intval($val_come->selisih_margin);
			$int_jumlah_hpp = intval($val_come->hpp);
			$int_jumlah_diterima = intval($val_come->diterima);
			$int_jumlah_gross = intval($val_come->fix);
			$int_jumlah_bruto = intval($val_come->bruto);
			$int_jumlah_revenue = intval($val_come->total);
			$int_jumlah_ongkir = intval($val_come->tot_ongkir);
			
			$margin[] = array( 0 => $val_come->tanggal,
							   1 => $int_jumlah_margin		
			);

			$selisih_margin[] = array( 0 => $val_come->tanggal,
							   		   1 => $int_jumlah_selisih_margin		
			);

			$hpp[] = array( 0 => $val_come->tanggal,
				   		    1 => $int_jumlah_hpp		
			);

			$diterima[] = array( 0 => $val_come->tanggal,
				   		    	 1 => $int_jumlah_diterima		
			);

			$gross[] = array( 0 => $val_come->tanggal,
				   		      1 => $int_jumlah_gross		
			);	

			$bruto[] = array( 0 => $val_come->tanggal,
				   		      1 => $int_jumlah_bruto		
			);

			$revenue[] = array( 0 => $val_come->tanggal,
				   		      	1 => $int_jumlah_revenue		
			);	

			$ongkir[] = array( 0 => $val_come->tanggal,
				   		       1 => $int_jumlah_ongkir		
			);			
		}

		$result = array( 'tanggal' 			=> $start." - ".$end,
						 'margin'  			=> $margin,
						 'selisih_margin'  	=> $selisih_margin,
						 'hpp'  			=> $hpp,
						 'diterima'			=> $diterima,
						 'gross'			=> $gross,
						 'bruto'			=> $bruto,
						 'revenue'  		=> $revenue,
						 'ongkir'  			=> $ongkir,
		);

		echo json_encode($result);
	}

	public function ajax_pie_provkab_retur_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_prov 	= $this->Dashboard_model->get_customer_provinsi_retur_penjualan($start, $end);
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

			$get_kab 	= $this->Dashboard_model->get_customer_kabupaten_retur_penjualan($start, $end, $val_prov->provinsi);
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

	public function ajax_bar_produk_retur_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_produk	= $this->Dashboard_model->get_produk_toko_periodik_retur_penjualan($start, $end);
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

	public function ajax_bar_kurir_retur_penjualan()
	{
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$get_kurir	= $this->Dashboard_model->get_kurir_by_periodik_retur_penjualan($start, $end);
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

	public function ajax_bar_pesanan_retur_penjualan()
	{
		$start 			= substr($this->input->post('periodik'), 0, 10);
		$end 			= substr($this->input->post('periodik'), 13, 24);
		$get_pesanan	= $this->Dashboard_model->get_pesanan_by_periodik_retur_penjualan($start, $end);
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
