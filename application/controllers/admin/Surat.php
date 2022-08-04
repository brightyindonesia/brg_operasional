<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

// Include librari PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Surat extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->data['module'] = 'Travel Document';
		$this->data['module_pl'] = 'Packing Document';

		$this->load->model(array('Surat_model', 'Penerima_model', 'Auth_model', 'Usertype_model', 'Sku_model', 'Po_model'));

		$this->data['company_data']    				= $this->Company_model->company_profile();
		$this->data['layout_template']    			= $this->Template_model->layout();
		$this->data['skins_template']     			= $this->Template_model->skins();

		$this->data['btn_submit'] 		= 'Save';
		$this->data['btn_reset'] 		= 'Reset';
		$this->data['btn_add']    		= 'Add New Data';
		$this->data['add_action'] 	 	= base_url('admin/surat/surat_jalan_tambah');
		$this->data['btn_add_pl'] 		= 'Add New Data';
		$this->data['add_action_pl']	= base_url('admin/surat/surat_packing_tambah');
		$this->data['btn_add_tb']    		= 'Add New Data';
		$this->data['add_action_tb'] 	 	= base_url('admin/surat/surat_terima_barang_tambah');
		$this->data['import_sj_action'] = base_url('assets/template/excel/format_detail_surat_jalan.xlsx');
		$this->data['import_pl_action'] = base_url('assets/template/excel/format_detail_surat_packing.xlsx');
		$this->data['btn_import']    	= 'Format Data Import';


		is_login();

		if ($this->uri->segment(1) != NULL) {
			menuaccess_check();
		} elseif ($this->uri->segment(2) != NULL) {
			submenuaccess_check();
		}
	}

	// Surat Jalan
	public function get_data_surat_jalan()
	{
		$i = 1;
		$list = $this->Surat_model->get_datatables_surat_jalan();
		$dataJSON = array();
		foreach ($list as $data) {
			$action = '<a href="' . base_url('admin/surat/surat_jalan_print/' . base64_encode($data->no_surat_jalan)) . '" class="btn btn-sm btn-success"><i class="fa fa-print"></i></a>';
			$action .= ' <a href="' . base_url('admin/surat/surat_jalan_ubah/' . base64_encode($data->no_surat_jalan)) . '" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
			$action .= ' <a href="' . base_url('admin/surat/surat_jalan_hapus/' . base64_encode($data->no_surat_jalan)) . '" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';

			$row = array();
			$row['no'] = $i;
			$row['tanggal'] = date('d F Y', strtotime($data->tgl_surat_jalan));
			$row['nomor_jalan'] = $data->no_surat_jalan;
			$row['kepada'] = $data->kepada_surat_jalan;
			$row['keterangan'] = $data->keterangan_surat_jalan;
			$row['nama_penerima'] = $data->nama_penerima;
			$row['nama_surat_jalan'] = $data->nama_surat_jalan;
			$row['alamat_penerima'] = $data->alamat_penerima;
			$row['created'] = $data->created_surat_jalan;
			$row['action'] = $action;

			$dataJSON[] = $row;

			$i++;
		}

		$output = array(
			"recordsTotal" => $this->Surat_model->count_all_surat_jalan(),
			"recordsFiltered" => $this->Surat_model->count_filtered_surat_jalan(),
			"data" => $dataJSON,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	function dasbor_list_count()
	{
		$penerima	= $this->input->post('penerima');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Surat_model->get_dasbor_list($penerima, $start, $end);
		if (isset($data)) {
			$msg = array(
				'total'		=> $data->total
			);
			echo json_encode($msg);
		} else {
			$msg = array(
				'validasi'	=> validation_errors()
			);
			echo json_encode($msg);
		}
	}

	function surat_jalan()
	{
		is_read();

		$this->data['page_title'] = $this->data['module'] . ' List';

		$this->data['get_all_penerima'] = $this->Penerima_model->get_all_penerima_list();

		// $this->data['get_all'] = $this->Keluar_model->get_all();
		$this->data['penerima'] = [
			'class'         => 'form-control select2bs4',
			'id'            => 'penerima',
			'required'      => '',
			'style' 		=> 'width:100%'
		];

		$this->load->view('back/surat/surat_jalan_list', $this->data);
	}

	function surat_jalan_tambah()
	{
		is_create();

		// generate nomor surat jalan

		date_default_timezone_set("Asia/Jakarta");
		$date = date("Y-m-d");
		$tahun = substr($date, 2, 2);
		$tahun_full = substr($date, 0, 4);
		$bulan = substr($date, 5, 2);
		$tanggal = substr($date, 8, 2);
		// $teks = "BR/SJ/".$tanggal.$bulan.$tahun."/";
		$teks = "BR/SJ/" . $tahun_full . "/";
		$ambil_nomor = $this->Surat_model->cari_nomor_sj($teks);
		// echo print_r(json_encode($ambil_nomor));
		// $hitung = count($ambil_nomor);
		// echo $ambil_nomor->nomor_pesanan;
		if (isset($ambil_nomor)) {
			// TANGGAL DARI ID NILAI
			$ambil_tanggal = substr($ambil_nomor->no_surat_jalan, 11, 2);
			$ambil_bulan = substr($ambil_nomor->no_surat_jalan, 13, 2);
			$ambil_tahun = substr($ambil_nomor->no_surat_jalan, 15, 2);
			$ambil_tahun_full = substr($ambil_nomor->no_surat_jalan, 6, 4);
			$ambil_no = (int) substr($ambil_nomor->no_surat_jalan, 18, 4);

			// PERHARI
			// if ($tahun == $ambil_tahun && $bulan == $ambil_bulan && $tanggal == $ambil_tanggal) {
			// 	$ambil_no++;	
			// 	$no_surat = "BR/SJ/".$tanggal.$bulan.$tahun."/".sprintf("%04s", $ambil_no);
			// }else{
			// 	$no_surat = "BR/SJ/".$tanggal.$bulan.$tahun."/"."0001";
			// }

			// PERTAHUN
			if ($tahun_full == $ambil_tahun_full) {
				$ambil_no++;
				$no_surat = "BR/SJ/" . $tahun_full . "/" . $tanggal . $bulan . $tahun . "/" . sprintf("%04s", $ambil_no);
			} else {
				$no_surat = "BR/SJ/" . $tahun_full . "/" . $tanggal . $bulan . $tahun . "/" . "0001";
			}
		} else {
			$no_surat = "BR/SJ/" . $tahun_full . "/" . $tanggal . $bulan . $tahun . "/" . "0001";
		}


		$this->data['get_all_penerima'] = $this->Penerima_model->get_all_combobox();

		// echo print_r($this->data['daftar_bahan_kemas']);
		$this->data['page_title'] = 'Create Data ' . $this->data['module'];
		$this->data['action']     = 'admin/surat/proses_surat_jalan_tambah';
		$this->data['nomor_surat_jalan'] = [
			'name' 			=> 'nomor_surat_jalan',
			'id'            => 'nomor-surat-jalan',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'value' 		=> $no_surat,
			'required'      => '',
			'readonly' 		=> ''
		];

		$this->data['nama_surat_jalan'] = [
			'name' 			=> 'nama_surat_jalan',
			'id'            => 'nama-surat-jalan',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'required'      => ''
		];

		$this->data['kepada_surat_jalan'] = [
			'name' 			=> 'kepada_surat_jalan',
			'id'            => 'kepada-surat-jalan',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'required'      => ''
		];

		$this->data['keterangan'] = [
			'name'          => 'keterangan',
			'id'            => 'keterangan',
			'class'         => 'form-control',
			'autocomplete'  => 'off'
		];

		$this->data['penerima'] = [
			'class'         => 'form-control select2bs4',
			'id'            => 'penerima',
			'required'      => '',
			'style' 		=> 'width:100%'
		];

		$this->load->view('back/surat/surat_jalan_add', $this->data);
	}

	function proses_surat_jalan_tambah()
	{
		$this->form_validation->set_rules(
			'nama_surat_jalan',
			'Nama Surat Jalan',
			'required|trim|max_length[255]',
			array(
				'required' 		=> '%s harus diisi!',
				'max_length'	=> '%s maksimal 255 karakter'
			)
		);

		$this->form_validation->set_rules(
			'kepada_surat_jalan',
			'Kepada Penerima',
			'required|trim|max_length[255]',
			array(
				'required' 		=> '%s harus diisi!',
				'max_length'	=> '%s maksimal 255 karakter'
			)
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if ($this->form_validation->run() === FALSE) {
			$this->surat_jalan_tambah();
		} else {
			date_default_timezone_set("Asia/Jakarta");
			$now = date('Y-m-d H:i:s');

			$nomor_surat 		= $this->input->post('nomor_surat_jalan');
			$nama_surat  		= $this->input->post('nama_surat_jalan');
			$tgl_surat   		= $this->input->post('periodik');
			$kepada_surat   	= $this->input->post('kepada_surat_jalan');
			$keterangan_surat   = $this->input->post('keterangan');
			$id_penerima   		= $this->input->post('penerima');

			$dataSurat = array(
				'no_surat_jalan' 			=> $nomor_surat,
				'nama_surat_jalan' 			=> $nama_surat,
				'tgl_surat_jalan' 			=> $tgl_surat,
				'kepada_surat_jalan' 		=> $kepada_surat,
				'keterangan_surat_jalan' 	=> $keterangan_surat,
				'id_penerima' 				=> $id_penerima,
				'created_surat_jalan'		=> $now,
			);

			$this->Surat_model->insert($dataSurat);

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data saved successfully</div>');
			redirect('admin/surat/surat_jalan_ubah/' . base64_encode($nomor_surat));
		}
	}

	function surat_jalan_ubah($id)
	{
		$this->data['cek_surat'] = $this->Surat_model->get_surat_jalan_by_id_row(base64_decode($id));
		$this->data['barang'] = $this->Surat_model->get_detail_surat_jalan_by_nomor($this->data['cek_surat']->no_surat_jalan);

		if ($this->data['cek_surat']) {
			$this->data['get_all_penerima'] = $this->Penerima_model->get_all_combobox();

			$this->data['page_title'] = 'Edit Data ' . $this->data['module'];
			$this->data['action']     = 'admin/surat/proses_surat_jalan_ubah';
			$this->data['nomor_surat_jalan'] = [
				'name' 			=> 'nomor_surat_jalan',
				'id'            => 'nomor-surat-jalan',
				'class'         => 'form-control',
				'autocomplete'  => 'off',
				'value' 		=> $this->data['cek_surat']->no_surat_jalan,
				'required'      => '',
				'readonly' 		=> ''
			];

			$this->data['nama_surat_jalan'] = [
				'name' 			=> 'nama_surat_jalan',
				'id'            => 'nama-surat-jalan',
				'class'         => 'form-control',
				'autocomplete'  => 'off',
				'required'      => ''
			];

			$this->data['kepada_surat_jalan'] = [
				'name' 			=> 'kepada_surat_jalan',
				'id'            => 'kepada-surat-jalan',
				'class'         => 'form-control',
				'autocomplete'  => 'off',
				'required'      => ''
			];

			$this->data['keterangan'] = [
				'name'          => 'keterangan',
				'id'            => 'keterangan',
				'class'         => 'form-control',
				'autocomplete'  => 'off'
			];

			$this->data['penerima'] = [
				'class'         => 'form-control select2bs4',
				'id'            => 'penerima',
				'required'      => '',
				'style' 		=> 'width:100%'
			];

			$this->load->view('back/surat/surat_jalan_edit', $this->data);
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
			redirect('admin/surat_jalan');
		}
	}

	function proses_surat_jalan_ubah()
	{
		$i = $this->input;
		$len = $i->post('length');
		$nomor_surat = $i->post('nomor_surat');
		$nama_surat = $i->post('nama_surat');
		$tgl_surat = $i->post('date');
		$kepada_surat = $i->post('kepada_surat');
		$keterangan = $i->post('keterangan');
		$penerima = intval($i->post('penerima'));
		$dt_kode = $i->post('dt_kode');
		$dt_nama = $i->post('dt_nama');
		$dt_satuan = $i->post('dt_satuan');
		$dt_qty = $i->post('dt_qty');
		$dt_keterangan = $i->post('dt_keterangan');
		$dt_satuan = $i->post('dt_satuan');

		$decode_kode       = json_decode($dt_kode, TRUE);
		$decode_nama       = json_decode($dt_nama, TRUE);
		$decode_qty        = json_decode($dt_qty, TRUE);
		$decode_satuan 	   = json_decode($dt_satuan, TRUE);
		$decode_keterangan = json_decode($dt_keterangan, TRUE);

		$cek_detail = $this->Surat_model->get_detail_surat_jalan_by_nomor($nomor_surat);

		if ($cek_detail) {
			$this->Surat_model->delete_detail_by_nomor($nomor_surat);
			$UpdateSurat = array(
				'no_surat_jalan' 			=> $nomor_surat,
				'nama_surat_jalan' 		=> $nama_surat,
				'tgl_surat_jalan' 		=> $tgl_surat,
				'kepada_surat_jalan' 		=> $kepada_surat,
				'keterangan_surat_jalan' 	=> $keterangan,
				'id_penerima' 			=> $penerima,
			);

			$this->Surat_model->update($nomor_surat, $UpdateSurat);

			for ($n = 0; $n < $len; $n++) {
				$InsertDetail = array(
					'no_surat_jalan' 				=> $nomor_surat,
					'kode_barang_surat_jalan' 		=> $decode_kode[$n],
					'nama_barang_surat_jalan' 		=> $decode_nama[$n],
					'jumlah_barang_surat_jalan' 		=> $decode_qty[$n],
					'satuan_barang_surat_jalan' 		=> $decode_satuan[$n],
					'keterangan_barang_surat_jalan' 	=> $decode_keterangan[$n],
				);

				$this->Surat_model->insert_detail($InsertDetail);
			}

			$pesan = "Berhasil diubah!";
			$msg = array(
				'sukses'	=> $pesan,
				'nomor'		=> base64_encode($nomor_surat),
			);
			echo json_encode($msg);
		} else {
			$UpdateSurat = array(
				'no_surat_jalan' 			=> $nomor_surat,
				'nama_surat_jalan' 		=> $nama_surat,
				'tgl_surat_jalan' 		=> $tgl_surat,
				'kepada_surat_jalan' 		=> $kepada_surat,
				'keterangan_surat_jalan' 	=> $keterangan,
				'id_penerima' 			=> $penerima,
			);

			$this->Surat_model->update($nomor_surat, $UpdateSurat);

			for ($n = 0; $n < $len; $n++) {
				$InsertDetail = array(
					'no_surat_jalan' 				=> $nomor_surat,
					'kode_barang_surat_jalan' 		=> $decode_kode[$n],
					'nama_barang_surat_jalan' 		=> $decode_nama[$n],
					'jumlah_barang_surat_jalan' 		=> $decode_qty[$n],
					'satuan_barang_surat_jalan' 		=> $decode_satuan[$n],
					'keterangan_barang_surat_jalan' 	=> $decode_keterangan[$n],
				);

				$this->Surat_model->insert_detail($InsertDetail);
			}

			$pesan = "Berhasil diubah!";
			$msg = array(
				'sukses'	=> $pesan,
				'nomor'		=> base64_encode($nomor_surat),
			);
			echo json_encode($msg);
		}
	}

	function surat_jalan_print($id)
	{
		$this->data['surat_jalan']   		= $this->Surat_model->get_surat_jalan_by_id_row(base64_decode($id));
		$this->data['penerima']				= $this->Penerima_model->get_by_id($this->data['surat_jalan']->id_penerima);
		$this->data['detail_surat_jalan']	= $this->Surat_model->get_detail_surat_jalan_by_nomor(base64_decode($id));

		// echo print_r($this->data['request'])
		$html = $this->load->view('back/report/template_surat_jalan', $this->data, TRUE);
		$filename = 'CETAK_' . $this->data['surat_jalan']->nama_surat_jalan . '_' . date('d_M_y');
		$this->pdfgenerator->generate($html, $filename, true, 'A4', 'portrait');
	}

	function surat_jalan_hapus($id)
	{
		$this->data['surat_jalan']   = $this->Surat_model->get_surat_jalan_by_id_row(base64_decode($id));
		if ($this->data['surat_jalan']) {
			$this->Surat_model->delete_detail_by_nomor($this->data['surat_jalan']->no_surat_jalan);
			$this->Surat_model->delete($this->data['surat_jalan']->no_surat_jalan);

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
			redirect('admin/surat/surat_jalan');
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
			redirect('admin/surat_jalan');
		}
	}

	function surat_jalan_detail_hapus_all($id)
	{
		$this->Surat_model->delete_detail_by_nomor(base64_decode($id));

		$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		redirect('admin/surat/surat_jalan_ubah/' . $id);
	}

	function detail_surat_jalan_ubah()
	{
		$i = $this->input;

		$id = $i->post('id');
		$pilihan = $i->post('pilihan');
		$kode = $i->post('kode');
		$nama = $i->post('nama');
		$jumlah = $i->post('jumlah');
		$satuan = $i->post('satuan');
		$keterangan = $i->post('keterangan');

		if ($pilihan == 'simpan') {
			$cek_detail = $this->Surat_model->get_detail_surat_jalan_by_id_row($id);
			if ($cek_detail) {
				$updateData = array(
					'kode_barang_surat_jalan'		 => $kode,
					'nama_barang_surat_jalan'		 => $nama,
					'jumlah_barang_surat_jalan'	 => $jumlah,
					'satuan_barang_surat_jalan'	 => $satuan,
					'keterangan_barang_surat_jalan' => $keterangan,
					'nama_barang_surat_jalan' => $nama,

				);

				// echo print_r($updateData);
				$this->Surat_model->update_detail($id, $cek_detail->no_surat_jalan, $updateData);
				$pesan = "Berhasil diubah!";
				$msg = array(
					'sukses'	=> $pesan,
					'nomor'		=> base64_encode($cek_detail->no_surat_jalan),
				);
				echo json_encode($msg);
			}
		} elseif ($pilihan == 'hapus') {
			$cek_detail = $this->Surat_model->get_detail_surat_jalan_by_id_row($id);
			if ($cek_detail) {
				// echo print_r($updateData);
				$this->Surat_model->delete_detail_by_id($id);
				$pesan = "Berhasil dihapus!";
				$msg = array(
					'sukses'	=> $pesan,
					'nomor'		=> base64_encode($cek_detail->no_surat_jalan),
				);
				echo json_encode($msg);
			}
		}
	}

	// Surat Terima Barang
	public function get_data_surat_terima_barang()
	{
		$i = 1;
		$list = $this->Surat_model->get_datatables_surat_terima_barang();
		$dataJSON = array();
		foreach ($list as $data) {
			$action = '<a href="' . base_url('admin/surat/surat_terima_barang_print/' . base64_encode($data->nomor_surat_terima_barang)) . '" class="btn btn-sm btn-success"><i class="fa fa-print"></i></a>';
			// $action .= ' <a href="' . base_url('admin/surat/surat_jalan_ubah/' . base64_encode($data->id_surat_terima_barang)) . '" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
			$action .= ' <a href="' . base_url('admin/surat/surat_terima_barang_hapus/' . base64_encode($data->nomor_surat_terima_barang)) . '" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';

			$row = array();
			$row['no'] = $i;
			$row['tanggal'] = date('d F Y', strtotime($data->tgl_terima_surat_terima_barang));
			$row['nomor_surat_terima_barang'] = $data->nomor_surat_terima_barang;
			$row['nama_surat_terima_barang'] = $data->nama_surat_terima_barang;
			$row['action'] = $action;

			$dataJSON[] = $row;

			$i++;
		}

		$output = array(
			"recordsTotal" => $this->Surat_model->count_all_surat_terima_barang(),
			"recordsFiltered" => $this->Surat_model->count_filtered_surat_terima_barang(),
			"data" => $dataJSON,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	function surat_terima_barang()
	{
		is_read();

		$this->data['page_title'] = $this->data['module'] . ' List';

		$this->data['get_all_warehouse'] = $this->Auth_model->get_warehouse_all_combobox();

		// $this->data['get_all'] = $this->Keluar_model->get_all();
		$this->data['warehouse'] = [
			'class'         => 'form-control select2bs4',
			'id'            => 'warehouse',
			'required'      => '',
			'style' 		=> 'width:100%'
		];

		$this->load->view('back/surat/surat_terima_barang_list', $this->data);
	}

	function dasbor_list_count_terima_barang()
	{

		$data      = $this->Surat_model->count_filtered_surat_terima_barang();
		if (isset($data)) {
			$msg = array(
				'total'		=> $data
			);
			echo json_encode($msg);
		} else {
			$msg = array(
				'validasi'	=> validation_errors()
			);
			echo json_encode($msg);
		}
	}


	function surat_terima_barang_tambah()
	{
		is_create();
		$this->load->helper('date_helper');
		// generate nomor surat terima barang

		date_default_timezone_set("Asia/Jakarta");
		$date = date("Y-m-d");
		$tahun = substr($date, 2, 2);
		$tahun_full = substr($date, 0, 4);
		$bulan = numberToRomanRepresentation(substr($date, 5, 2));
		$tanggal = substr($date, 8, 2);
		// $teks = "BR/SJ/".$tanggal.$bulan.$tahun."/";
		$ambil_nomor = $this->Surat_model->count_total_surat_terima_barang();
		// echo print_r(json_encode($ambil_nomor));
		// $hitung = count($ambil_nomor);
		// echo $ambil_nomor->nomor_pesanan;
		if (empty($ambil_nomor)) $ambil_nomor = 0;
		$no_surat = '/' . sprintf("%02d", ($ambil_nomor + 1)) . "/" . $tanggal . '-' . $bulan . '-' .  $tahun_full;


		$this->data['get_all_warehouse'] = $this->Auth_model->get_warehouse_all_combobox();
		$this->data['get_satuan_produk'] = $this->Sku_model->get_all_combobox_unique();
		$this->data['get_po_list'] = $this->Po_model->get_all_po_list();
		$this->data['get_penerima_list'] = $this->Surat_model->get_all_penerima_surat_terima_list();


		// echo print_r($this->data['daftar_bahan_kemas']);
		$this->data['page_title'] = 'Create Data ' . $this->data['module'];
		$this->data['action']     = 'admin/surat/proses_surat_jalan_terima_barang';
		$this->data['nomor_surat_template'] = $no_surat;
		$this->data['nomor_surat_terima_barang'] = [
			'name' 			=> 'nomor_surat_terima_barang',
			'id'            => 'in_nomor_surat_terima_barang',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'required'      => '',
			'readonly' 		=> ''
		];

		$this->data['nama_surat_terima_barang'] = [
			'name' 			=> 'nama_surat_terima_barang',
			'id'            => 'in_nama_surat_terima_barang',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'required'      => ''
		];
		$this->data['nomor_surat_jalan'] = [
			'name' 			=> 'nomor_surat_jalan',
			'id'            => 'in_nomor_surat_jalan',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'required'      => ''
		];


		$this->data['nama_pengirim'] = [
			'name'          => 'nama_pengirim',
			'id'            => 'in_nama_pengirim',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
		];


		$this->data['nama_penerima'] = [
			'class'         => 'form-control select2bs45',
			'id'            => 'in_nama_penerima',
			'required'      => '',
			'style' 		=> 'width:100%'
		];

		$this->data['warehouse'] = [
			'class'         => 'form-control select2bs4',
			'id'            => 'in_warehouse',
			'required'      => '',
			'style' 		=> 'width:100%'
		];

		$this->data['pic_qc'] = [
			'class'         => 'form-control select2bs42',
			'id'            => 'in_pic_qc',
			'required'      => '',
			'style' 		=> 'width:100%'
		];

		$this->data['kode_po'] = [
			'class'         => 'form-control select2bs44',
			'id'            => 'in_kode_po',
			'required'      => '',
			'style' 		=> 'width:100%'
		];


		$this->data['nama_barang'] = [
			'class'         => 'form-control select2bs43',
			'id'            => 'in_nama_barang',
			'style' 		=> 'width:100%'
		];

		$this->load->view('back/surat/surat_terima_barang_add', $this->data);
	}

	function proses_surat_terima_barang()
	{
		date_default_timezone_set("Asia/Jakarta");
		$now = date('Y-m-d H:i:s');

		$nomor_surat_terima 		= $this->input->post('nomor_surat_terima');
		$nomor_surat_jalan  		= $this->input->post('nomor_surat_jalan');
		$kode_po   		= $this->input->post('kode_po');
		$nama_surat   	= $this->input->post('nama_surat');
		$warehouse   = $this->input->post('warehouse');
		$periodik_kirim   		= $this->input->post('periodik_kirim');
		$nama_pengirim   		= $this->input->post('nama_pengirim');
		$periodik_terima   		= $this->input->post('periodik_terima');
		$nama_penerima   		= $this->input->post('nama_penerima');
		$dt_pic_qc   		= $this->input->post('dt_pic_qc');
		$dt_nama_barang   		= $this->input->post('dt_nama_barang');
		$dt_kode_barang   		= $this->input->post('dt_kode_barang');
		$dt_qty   		= $this->input->post('dt_qty');
		$dt_no_batch   		= $this->input->post('dt_no_batch');
		$dt_jumlah_barang_qc   		= $this->input->post('dt_jumlah_barang_qc');
		$dt_tgl_selesai_qc   		= $this->input->post('dt_tgl_selesai_qc');
		$dt_keterangan_qc   		= $this->input->post('dt_keterangan_qc');
		$dt_exp_date  		= $this->input->post('dt_tgl_exp');
		$len   		= $this->input->post('length');

		$decode_pic_qc = json_decode($dt_pic_qc, TRUE);
		$decode_nama_barang = json_decode($dt_nama_barang, TRUE);
		$decode_kode_barang = json_decode($dt_kode_barang, TRUE);
		$decode_qty = json_decode($dt_qty, TRUE);
		$decode_no_batch = json_decode($dt_no_batch, TRUE);
		$decode_jumlah_barang_qc = json_decode($dt_jumlah_barang_qc, TRUE);
		$decode_tgl_selesai_qc = json_decode($dt_tgl_selesai_qc, TRUE);
		$decode_keterangan_qc = json_decode($dt_keterangan_qc, TRUE);
		$decode_exp_date = json_decode($dt_exp_date, TRUE);

		$dataSurat = array(
			'nomor_surat_terima_barang ' 			=> $nomor_surat_terima,
			'no_surat_jalan' 			=> $nomor_surat_jalan,
			'no_po' 			=> $kode_po,
			'nama_surat_terima_barang' 		=> $nama_surat,
			'id_warehouse_surat_terima_barang' 	=> $warehouse,
			'tgl_kirim_surat_terima_barang' 				=> $periodik_kirim,
			'nama_pengirim_surat_terima_barang' 				=> $nama_pengirim,
			'tgl_terima_surat_terima_barang' 				=> $periodik_terima,
			'nama_penerima_surat_terima_barang' 				=> $nama_penerima,
			'created_surat_terima_barang'		=> $now,
		);

		$this->Surat_model->insert_terima($dataSurat);
		write_log();

		for ($n = 0; $n < $len; $n++) {

			if (preg_match('/BR/i', $decode_kode_barang[$n])) {
				$kategori = "BRIGHTY";
			} else if (preg_match('/CR/i', $decode_kode_barang[$n])) {
				$kategori = "CIARA";
			} else if (preg_match('/AHA/i', $decode_kode_barang[$n])) {
				$kategori = "AHA";
			} else {
				$kategori = "Code Not Found";
			}

			$dataDetail[$n] = array(
				'no_surat_terima_barang '	=> $nomor_surat_terima,
				'id_pic_surat_terima_barang' 	=> $decode_pic_qc[$n],
				'no_batch_surat_terima_barang' 	=> $decode_no_batch[$n],
				'nama_barang_surat_jalan'			=> $decode_nama_barang[$n],
				'kode_barang_surat_terima_barang' 		=> $decode_kode_barang[$n],
				'kategori_barang_surat_terima_barang' => $kategori,
				'qty_barang_surat_terima_barang'	 		=> $decode_qty[$n],
				'jumlah_qc_surat_terima_barang'	 		=> $decode_jumlah_barang_qc[$n],
				'tgl_selesai_qc_surat_terima_barang'	 		=> $decode_tgl_selesai_qc[$n],
				'keterangan_barang_surat_terima_barang'	 		=> $decode_keterangan_qc[$n],
				'exp_date_surat_terima_barang'	 		=> $decode_exp_date[$n],
			);


			$this->Surat_model->insert_detail_terima($dataDetail[$n]);

			write_log();
		}

		$pesan = "Berhasil disimpan!";
		$msg = array(
			'sukses'	=> $pesan
		);
		echo json_encode($msg);
	}


	function surat_terima_barang_print($id)
	{
		$this->data['surat_terima_barang']   		= $this->Surat_model->get_surat_terima_barang_by_nomor(base64_decode($id));
		$this->data['detail_surat_terima_barang']	= $this->Surat_model->get_detail_surat_terima_barang_by_nomor(base64_decode($id));

		// die(print_r($this->Usertype_model->get_user_by_id($this->data['surat_terima_barang']->id_warehouse_surat_terima_barang)));
		$this->data['warehouse']				= $this->Usertype_model->get_user_by_id($this->data['surat_terima_barang']->id_warehouse_surat_terima_barang)->name;
		$this->data['pic_qc']				= $this->Usertype_model->get_user_by_id($this->data['detail_surat_terima_barang'][0]->id_pic_surat_terima_barang)->name;

		$this->load->view('back/report/template_surat_terima', $this->data);
		$html = $this->load->view('back/report/template_surat_terima', $this->data, TRUE);
		$filename = 'CETAK_' . $this->data['surat_terima_barang']->nama_surat_terima_barang . '_' . date('d_M_y');
		$this->pdfgenerator->generate($html, $filename, true, 'A4', 'portrait');
	}

	function surat_terima_barang_hapus($id)
	{
		$this->data['surat_terima_barang']   = $this->Surat_model->get_surat_terima_barang_by_nomor(base64_decode($id));

		if ($this->data['surat_terima_barang']) {
			$this->Surat_model->delete_surat_terima_detail_by_nomor(base64_decode($id));
			$this->Surat_model->delete_surat_terima(base64_decode($id));

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
			redirect('admin/surat/surat_terima_barang');
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
			redirect('admin/surat_terima_barang');
		}
	}



	public function proses_impor_sj()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc' . time();
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';

		$this->load->library('upload', $config);
		if ($this->upload->do_upload('import_data')) {
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/' . $file['file_name']);
			$nomor 	   	= $this->input->post('nomor');
			$numSheet 	= 0;
			$jumlah 	= 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 0) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow == 1) {
							if ($row->getCellAtIndex(0) != 'kode_barang_surat_jalan' || $row->getCellAtIndex(1) != 'nama_barang_surat_jalan' || $row->getCellAtIndex(2) != 'jumlah_barang_surat_jalan' || $row->getCellAtIndex(3) != 'satuan_barang_surat_jalan' || $row->getCellAtIndex(4) != 'keterangan_barang_surat_jalan') {
								$reader->close();
								unlink('uploads/' . $file['file_name']);

								$msg = array(
									'validasi'		=> 'Data import tidak sesuai!',
								);
								echo json_encode($msg);
							}
						}

						if ($numRow > 1) {
							$cells 	   = $row->getCells();

							$dataDetailSJ 	= array(
								'no_surat_jalan'				=> $nomor,
								'kode_barang_surat_jalan' 		=> $row->getCellAtIndex(0),
								'nama_barang_surat_jalan' 		=> $row->getCellAtIndex(1),
								'jumlah_barang_surat_jalan' 	=> $row->getCellAtIndex(2),
								'satuan_barang_surat_jalan' 	=> $row->getCellAtIndex(3),
								'keterangan_barang_surat_jalan' => $row->getCellAtIndex(4),
							);

							$this->Surat_model->insert_detail($dataDetailSJ);

							write_log();
						}
						$numRow++;
						$jumlah++;
					}
					$reader->close();
					unlink('uploads/' . $file['file_name']);

					$msg = array(
						'sukses'	=> $jumlah . ' Data imported successfully',
						'nomor'		=> base64_encode($nomor)
					);
					echo json_encode($msg);
					// $this->session->set_flashdata('message', '<div class="alert alert-success">'.$jumlah.' Data imported successfully</div>');
					// redirect('admin/surat/surat_packing_ubah/'.base64_encode($nomor));
				}
				$numSheet++;
			}
		} else {
			// $error = array('error' => $this->upload->display_errors());
			// $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>')

			$msg = array(
				'validasi'	=> 'Terjadi Kesalahan!',
			);
			echo json_encode($msg);
			// $error = array('error' => $this->upload->display_errors());
			// $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
			// redirect('admin/surat/surat_packing_ubah/'.base64_encode($nomor));
			// return $error;
		}
	}

	public function surat_jalan_ekspor($nomor)
	{
		$surat_jalan = $this->Surat_model->get_surat_jalan_by_id_row(base64_decode($nomor));
		$detail_surat_jalan = $this->Surat_model->get_detail_surat_jalan_by_nomor(base64_decode($nomor));
		$title = "Export Data Surat Jalan_" . date("H_i_s");

		// PHPOffice

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Data Surat Jalan
		$sheet->setCellValue('A1', 'Tanggal Surat Jalan');
		$sheet->setCellValue('B1', 'Nomor Surat Jalan');
		$sheet->mergeCells('C1:H1');
		$sheet->setCellValue('C1', 'Nama Surat Jalan');
		$sheet->setCellValue('I1', 'Kepada');
		$sheet->setCellValue('J1', 'Nama Penerima');
		$sheet->setCellValue('K1', 'Alamat Penerima');

		$sheet->setCellValue('A2', $surat_jalan->tgl_surat_jalan);
		$sheet->setCellValue('B2', $surat_jalan->no_surat_jalan);
		$sheet->mergeCells('C2:H2');
		$sheet->setCellValue('C2', $surat_jalan->nama_surat_jalan);
		$sheet->setCellValue('I2', $surat_jalan->kepada_surat_jalan);
		$sheet->setCellValue('J2', $surat_jalan->nama_penerima);
		$sheet->setCellValue('K2', $surat_jalan->alamat_penerima);


		// Data Detail Surat Jalan
		$sheet->setCellValue('A4', 'Kode Barang');
		$sheet->setCellValue('B4', 'Nama Barang');
		$sheet->setCellValue('C4', 'Jumlah');
		$sheet->setCellValue('D4', 'Satuan');
		$sheet->mergeCells('E4:K4');
		$sheet->setCellValue('E4', 'Keterangan');
		// set Row
		$rowCount = 5;
		foreach ($detail_surat_jalan as $list) {
			$sheet->SetCellValue('A' . $rowCount, $list->kode_barang_surat_jalan);
			$sheet->SetCellValue('B' . $rowCount, $list->nama_barang_surat_jalan);
			$sheet->SetCellValue('C' . $rowCount, $list->jumlah_barang_surat_jalan);
			$sheet->SetCellValue('D' . $rowCount, $list->satuan_barang_surat_jalan);
			$sheet->mergeCells('E' . $rowCount . ':K' . $rowCount);
			$sheet->SetCellValue('E' . $rowCount, $list->keterangan_barang_surat_jalan);
			$rowCount++;
		}

		$writer = new Xlsx($spreadsheet);

		header('Content-Type: application/vnd.ms-excel');
		header("Content-Transfer-Encoding: Binary");
		header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
		header("Pragma: no-cache");
		header("Expires: 0");

		$writer->save('php://output');

		die();
	}

	// Surat Packing
	public function get_data_surat_packing()
	{
		$i = 1;
		$list = $this->Surat_model->get_datatables_surat_packing();
		$dataJSON = array();
		foreach ($list as $data) {
			$action = '<a href="' . base_url('admin/surat/surat_packing_print/' . base64_encode($data->no_surat_packing)) . '" class="btn btn-sm btn-success"><i class="fa fa-print"></i></a> ';
			$action .= ' <a href="' . base_url('admin/surat/surat_packing_print_alamat/' . base64_encode($data->no_surat_packing)) . '" class="btn btn-sm btn-info"><i class="fa fa-id-card-o"></i></a>';
			$action .= ' <a href="' . base_url('admin/surat/surat_packing_ubah/' . base64_encode($data->no_surat_packing)) . '" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>';
			$action .= ' <a href="' . base_url('admin/surat/surat_packing_hapus/' . base64_encode($data->no_surat_packing)) . '" onClick="return confirm(\'Are you sure?\');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>';

			$row = array();
			$row['no'] = $i;
			$row['tanggal'] = date('d F Y', strtotime($data->tgl_surat_packing));
			$row['nomor_jalan'] = $data->no_surat_packing;
			$row['kepada'] = $data->kepada_surat_packing;
			$row['keterangan'] = $data->keterangan_surat_packing;
			$row['nama_penerima'] = $data->nama_penerima;
			$row['nama_surat_jalan'] = $data->nama_surat_packing;
			$row['alamat_penerima'] = $data->alamat_penerima;
			$row['created'] = $data->created_surat_packing;
			$row['action'] = $action;

			$dataJSON[] = $row;

			$i++;
		}

		$output = array(
			"recordsTotal" => $this->Surat_model->count_all_surat_packing(),
			"recordsFiltered" => $this->Surat_model->count_filtered_surat_packing(),
			"data" => $dataJSON,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	function dasbor_list_count_packing()
	{
		$penerima	= $this->input->post('penerima');
		$start 		= substr($this->input->post('periodik'), 0, 10);
		$end 		= substr($this->input->post('periodik'), 13, 24);
		$data      = $this->Surat_model->get_dasbor_list_packing($penerima, $start, $end);
		if (isset($data)) {
			$msg = array(
				'total'		=> $data->total
			);
			echo json_encode($msg);
		} else {
			$msg = array(
				'validasi'	=> validation_errors()
			);
			echo json_encode($msg);
		}
	}

	function surat_packing()
	{
		is_read();

		$this->data['page_title'] = $this->data['module_pl'] . ' List';

		$this->data['get_all_penerima'] = $this->Penerima_model->get_all_penerima_list();

		// $this->data['get_all'] = $this->Keluar_model->get_all();
		$this->data['penerima'] = [
			'class'         => 'form-control select2bs4',
			'id'            => 'penerima',
			'required'      => '',
			'style' 		=> 'width:100%'
		];

		$this->load->view('back/surat/surat_packing_list', $this->data);
	}

	function surat_packing_tambah()
	{
		is_create();

		// generate nomor surat jalan

		date_default_timezone_set("Asia/Jakarta");
		$date = date("Y-m-d");
		$tahun = substr($date, 2, 2);
		$tahun_full = substr($date, 0, 4);
		$bulan = substr($date, 5, 2);
		$tanggal = substr($date, 8, 2);
		// $teks = "BR/PL/".$tanggal.$bulan.$tahun."/";
		$teks = "BR/PL/" . $tahun_full . "/";
		$ambil_nomor = $this->Surat_model->cari_nomor_sp($teks);
		// echo print_r(json_encode($ambil_nomor));
		// $hitung = count($ambil_nomor);
		// echo $ambil_nomor->nomor_pesanan;
		if (isset($ambil_nomor)) {
			// TANGGAL DARI ID NILAI
			$ambil_tanggal = substr($ambil_nomor->no_surat_packing, 11, 2);
			$ambil_bulan = substr($ambil_nomor->no_surat_packing, 13, 2);
			$ambil_tahun = substr($ambil_nomor->no_surat_packing, 15, 2);
			$ambil_tahun_full = substr($ambil_nomor->no_surat_packing, 6, 4);
			$ambil_no = (int) substr($ambil_nomor->no_surat_packing, 18, 4);

			// PERHARI
			// if ($tahun == $ambil_tahun && $bulan == $ambil_bulan && $tanggal == $ambil_tanggal) {
			// 	$ambil_no++;	
			// 	$no_surat = "BR/SJ/".$tanggal.$bulan.$tahun."/".sprintf("%04s", $ambil_no);
			// }else{
			// 	$no_surat = "BR/SJ/".$tanggal.$bulan.$tahun."/"."0001";
			// }

			// PERTAHUN
			if ($tahun_full == $ambil_tahun_full) {
				$ambil_no++;
				$no_surat = "BR/PL/" . $tahun_full . "/" . $tanggal . $bulan . $tahun . "/" . sprintf("%04s", $ambil_no);
			} else {
				$no_surat = "BR/PL/" . $tahun_full . "/" . $tanggal . $bulan . $tahun . "/" . "0001";
			}
		} else {
			$no_surat = "BR/PL/" . $tahun_full . "/" . $tanggal . $bulan . $tahun . "/" . "0001";
		}


		$this->data['get_all_penerima'] = $this->Penerima_model->get_all_combobox();

		// echo print_r($this->data['daftar_bahan_kemas']);
		$this->data['page_title'] = 'Create Data ' . $this->data['module_pl'];
		$this->data['action']     = 'admin/surat/proses_surat_packing_tambah';
		$this->data['nomor_surat_packing'] = [
			'name' 			=> 'nomor_surat_packing',
			'id'            => 'nomor-surat-packing',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'value' 		=> $no_surat,
			'required'      => '',
			'readonly' 		=> ''
		];

		$this->data['nama_surat_packing'] = [
			'name' 			=> 'nama_surat_packing',
			'id'            => 'nama-surat-packing',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'required'      => ''
		];

		$this->data['kepada_surat_packing'] = [
			'name' 			=> 'kepada_surat_packing',
			'id'            => 'kepada-surat-packing',
			'class'         => 'form-control',
			'autocomplete'  => 'off',
			'required'      => ''
		];

		$this->data['keterangan'] = [
			'name'          => 'keterangan',
			'id'            => 'keterangan',
			'class'         => 'form-control',
			'autocomplete'  => 'off'
		];

		$this->data['penerima'] = [
			'class'         => 'form-control select2bs4',
			'id'            => 'penerima',
			'required'      => '',
			'style' 		=> 'width:100%'
		];

		$this->load->view('back/surat/surat_packing_add', $this->data);
	}

	function proses_surat_packing_tambah()
	{
		$this->form_validation->set_rules(
			'nama_surat_packing',
			'Nama Surat Packing',
			'required|trim|max_length[255]',
			array(
				'required' 		=> '%s harus diisi!',
				'max_length'	=> '%s maksimal 255 karakter'
			)
		);

		$this->form_validation->set_rules(
			'kepada_surat_packing',
			'Kepada Penerima',
			'required|trim|max_length[255]',
			array(
				'required' 		=> '%s harus diisi!',
				'max_length'	=> '%s maksimal 255 karakter'
			)
		);

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		if ($this->form_validation->run() === FALSE) {
			$this->surat_packing_tambah();
		} else {
			date_default_timezone_set("Asia/Jakarta");
			$now = date('Y-m-d H:i:s');

			$nomor_surat 		= $this->input->post('nomor_surat_packing');
			$nama_surat  		= $this->input->post('nama_surat_packing');
			$tgl_surat   		= $this->input->post('periodik');
			$kepada_surat   	= $this->input->post('kepada_surat_packing');
			$keterangan_surat   = $this->input->post('keterangan');
			$id_penerima   		= $this->input->post('penerima');

			$dataSurat = array(
				'no_surat_packing' 			=> $nomor_surat,
				'nama_surat_packing' 		=> $nama_surat,
				'tgl_surat_packing' 		=> $tgl_surat,
				'kepada_surat_packing' 		=> $kepada_surat,
				'keterangan_surat_packing' 	=> $keterangan_surat,
				'id_penerima' 				=> $id_penerima,
				'created_surat_packing'		=> $now,
			);

			$this->Surat_model->insert_packing($dataSurat);

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data saved successfully</div>');
			redirect('admin/surat/surat_packing_ubah/' . base64_encode($nomor_surat));
		}
	}

	function surat_packing_ubah($id)
	{
		$this->data['cek_surat'] = $this->Surat_model->get_surat_packing_by_id_row_packing(base64_decode($id));
		$this->data['barang'] = $this->Surat_model->get_detail_surat_packing_by_nomor_packing($this->data['cek_surat']->no_surat_packing);

		if ($this->data['cek_surat']) {
			$this->data['get_all_penerima'] = $this->Penerima_model->get_all_combobox();

			$this->data['page_title'] = 'Edit Data ' . $this->data['module_pl'];
			$this->data['action']     = 'admin/surat/proses_surat_packing_ubah';
			$this->data['nomor_surat_packing'] = [
				'name' 			=> 'nomor_surat_packing',
				'id'            => 'nomor-surat-packing',
				'class'         => 'form-control',
				'autocomplete'  => 'off',
				'value' 		=> $this->data['cek_surat']->no_surat_packing,
				'required'      => '',
				'readonly' 		=> ''
			];

			$this->data['nama_surat_packing'] = [
				'name' 			=> 'nama_surat_packing',
				'id'            => 'nama-surat-packing',
				'class'         => 'form-control',
				'autocomplete'  => 'off',
				'required'      => ''
			];

			$this->data['kepada_surat_packing'] = [
				'name' 			=> 'kepada_surat_packing',
				'id'            => 'kepada-surat-packing',
				'class'         => 'form-control',
				'autocomplete'  => 'off',
				'required'      => ''
			];

			$this->data['keterangan'] = [
				'name'          => 'keterangan',
				'id'            => 'keterangan',
				'class'         => 'form-control',
				'autocomplete'  => 'off'
			];

			$this->data['penerima'] = [
				'class'         => 'form-control select2bs4',
				'id'            => 'penerima',
				'required'      => '',
				'style' 		=> 'width:100%'
			];

			$this->load->view('back/surat/surat_packing_edit', $this->data);
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
			redirect('admin/surat_packing');
		}
	}

	function proses_surat_packing_ubah()
	{
		$i = $this->input;
		$len = $i->post('length');
		$nomor_surat = $i->post('nomor_surat');
		$nama_surat = $i->post('nama_surat');
		$tgl_surat = $i->post('date');
		$kepada_surat = $i->post('kepada_surat');
		$keterangan = $i->post('keterangan');
		$penerima = intval($i->post('penerima'));
		$dt_kode = $i->post('dt_kode');
		$dt_nama = $i->post('dt_nama');
		$dt_satuan = $i->post('dt_satuan');
		$dt_qty = $i->post('dt_qty');
		$dt_keterangan = $i->post('dt_keterangan');
		$dt_satuan = $i->post('dt_satuan');

		$decode_kode       = json_decode($dt_kode, TRUE);
		$decode_nama       = json_decode($dt_nama, TRUE);
		$decode_qty        = json_decode($dt_qty, TRUE);
		$decode_satuan 	   = json_decode($dt_satuan, TRUE);
		$decode_keterangan = json_decode($dt_keterangan, TRUE);

		$cek_detail = $this->Surat_model->get_detail_surat_packing_by_nomor_packing($nomor_surat);

		if ($cek_detail) {
			$this->Surat_model->delete_detail_by_nomor_packing($nomor_surat);
			$UpdateSurat = array(
				'no_surat_packing' 		=> $nomor_surat,
				'nama_surat_packing' 		=> $nama_surat,
				'tgl_surat_packing' 		=> $tgl_surat,
				'kepada_surat_packing'	=> $kepada_surat,
				'keterangan_surat_packing' => $keterangan,
				'id_penerima' 			=> $penerima,
			);

			$this->Surat_model->update_packing($nomor_surat, $UpdateSurat);

			for ($n = 0; $n < $len; $n++) {
				$InsertDetail = array(
					'no_surat_packing' 				=> $nomor_surat,
					'kode_barang_surat_packing' 		=> $decode_kode[$n],
					'nama_barang_surat_packing' 		=> $decode_nama[$n],
					'jumlah_barang_surat_packing' 	=> $decode_qty[$n],
					'satuan_barang_surat_packing' 	=> $decode_satuan[$n],
					'keterangan_barang_surat_packing' => $decode_keterangan[$n],
				);

				$this->Surat_model->insert_detail_packing($InsertDetail);
			}

			$pesan = "Berhasil diubah!";
			$msg = array(
				'sukses'	=> $pesan,
				'nomor'		=> base64_encode($nomor_surat),
			);
			echo json_encode($msg);
		} else {
			$UpdateSurat = array(
				'no_surat_packing' 		=> $nomor_surat,
				'nama_surat_packing' 		=> $nama_surat,
				'tgl_surat_packing' 		=> $tgl_surat,
				'kepada_surat_packing' 	=> $kepada_surat,
				'keterangan_surat_packing' => $keterangan,
				'id_penerima' 			=> $penerima,
			);

			$this->Surat_model->update_packing($nomor_surat, $UpdateSurat);

			for ($n = 0; $n < $len; $n++) {
				$InsertDetail = array(
					'no_surat_packing' 				=> $nomor_surat,
					'kode_barang_surat_packing' 		=> $decode_kode[$n],
					'nama_barang_surat_packing' 		=> $decode_nama[$n],
					'jumlah_barang_surat_packing' 	=> $decode_qty[$n],
					'satuan_barang_surat_packing' 	=> $decode_satuan[$n],
					'keterangan_barang_surat_packing' => $decode_keterangan[$n],
				);

				$this->Surat_model->insert_detail_packing($InsertDetail);
			}

			$pesan = "Berhasil diubah!";
			$msg = array(
				'sukses'	=> $pesan,
				'nomor'		=> base64_encode($nomor_surat),
			);
			echo json_encode($msg);
		}
	}

	function surat_packing_print($id)
	{
		$this->data['surat_packing']   		= $this->Surat_model->get_surat_packing_by_id_row_packing(base64_decode($id));
		$this->data['penerima']				= $this->Penerima_model->get_by_id($this->data['surat_packing']->id_penerima);
		$this->data['detail_surat_packing']	= $this->Surat_model->get_detail_surat_packing_by_nomor_packing(base64_decode($id));
		$this->data['total']				= count($this->data['detail_surat_packing']);

		// echo print_r($this->data['request'])
		$html = $this->load->view('back/report/template_surat_packing', $this->data, TRUE);
		$filename = 'CETAK_' . $this->data['surat_packing']->nama_surat_packing . '_' . date('d_M_y');
		$this->pdfgenerator->generate($html, $filename, true, 'A4', 'portrait');
	}

	function surat_packing_print_alamat($id)
	{
		$this->data['surat_packing']   		= $this->Surat_model->get_surat_packing_by_id_row_packing(base64_decode($id));
		$this->data['penerima']				= $this->Penerima_model->get_by_id($this->data['surat_packing']->id_penerima);
		$this->data['detail_surat_packing']	= $this->Surat_model->get_detail_surat_packing_by_nomor_packing(base64_decode($id));
		$this->data['total']				= count($this->data['detail_surat_packing']);

		// echo print_r($this->data['request'])
		$html = $this->load->view('back/report/template_surat_packing_alamat', $this->data, TRUE);
		$filename = 'CETAK_ALAMAT_' . $this->data['surat_packing']->nama_surat_packing . '_' . date('d_M_y');
		$this->pdfgenerator->generate($html, $filename, true, 'A4', 'portrait');
	}

	function surat_packing_hapus($id)
	{
		$this->data['surat_packing']   = $this->Surat_model->get_surat_packing_by_id_row_packing(base64_decode($id));
		if ($this->data['surat_packing']) {
			$this->Surat_model->delete_detail_by_nomor_packing($this->data['surat_packing']->no_surat_packing);
			$this->Surat_model->delete_packing($this->data['surat_packing']->no_surat_packing);

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
			redirect('admin/surat/surat_packing');
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Data not found</div>');
			redirect('admin/surat_packing');
		}
	}

	function surat_packing_detail_hapus_all($id)
	{
		$this->Surat_model->delete_detail_by_nomor_packing(base64_decode($id));

		$this->session->set_flashdata('message', '<div class="alert alert-success">Data deleted successfully</div>');
		redirect('admin/surat/surat_packing_ubah/' . $id);
	}

	function get_by_id_jalan($id)
	{
		$data['data'] = 0;
		$cek_detail = $this->Surat_model->get_detail_surat_jalan_by_id_row($id);
		if ($cek_detail) {
			$data['data'] = 1;
			$data['id'] = $cek_detail->id_detail_surat_jalan;
			$data['kode'] = $cek_detail->kode_barang_surat_jalan;
			$data['nama'] = $cek_detail->nama_barang_surat_jalan;
			$data['jumlah'] = $cek_detail->jumlah_barang_surat_jalan;
			$data['satuan'] = $cek_detail->satuan_barang_surat_jalan;
			$data['keterangan'] = $cek_detail->keterangan_barang_surat_jalan;
		}
		echo json_encode($data);
	}

	function get_by_id_packing($id)
	{
		$data['data'] = 0;
		$cek_detail = $this->Surat_model->get_detail_surat_packing_by_id_row($id);
		if ($cek_detail) {
			$data['data'] = 1;
			$data['id'] = $cek_detail->id_detail_surat_packing;
			$data['kode'] = $cek_detail->kode_barang_surat_packing;
			$data['nama'] = $cek_detail->nama_barang_surat_packing;
			$data['jumlah'] = $cek_detail->jumlah_barang_surat_packing;
			$data['satuan'] = $cek_detail->satuan_barang_surat_packing;
			$data['keterangan'] = $cek_detail->keterangan_barang_surat_packing;
		}
		echo json_encode($data);
	}

	function detail_surat_packing_ubah()
	{
		$i = $this->input;

		$id = $i->post('id');
		$pilihan = $i->post('pilihan');
		$kode = $i->post('kode');
		$nama = $i->post('nama');
		$jumlah = $i->post('jumlah');
		$satuan = $i->post('satuan');
		$keterangan = $i->post('keterangan');

		if ($pilihan == 'simpan') {
			$cek_detail = $this->Surat_model->get_detail_surat_packing_by_id_row($id);
			if ($cek_detail) {
				$updateData = array(
					'kode_barang_surat_packing'		=> $kode,
					'nama_barang_surat_packing'		=> $nama,
					'jumlah_barang_surat_packing'	 	=> $jumlah,
					'satuan_barang_surat_packing'	 	=> $satuan,
					'keterangan_barang_surat_packing' 	=> $keterangan,
					'nama_barang_surat_packing'		=> $nama,

				);

				// echo print_r($updateData);
				$this->Surat_model->update_detail_packing($id, $cek_detail->no_surat_packing, $updateData);
				$pesan = "Berhasil diubah!";
				$msg = array(
					'sukses'	=> $pesan,
					'nomor'		=> base64_encode($cek_detail->no_surat_packing),
				);
				echo json_encode($msg);
			}
		} elseif ($pilihan == 'hapus') {
			$cek_detail = $this->Surat_model->get_detail_surat_packing_by_id_row($id);
			if ($cek_detail) {
				// echo print_r($updateData);
				$this->Surat_model->delete_detail_by_id_packing($id);
				$pesan = "Berhasil dihapus!";
				$msg = array(
					'sukses'	=> $pesan,
					'nomor'		=> base64_encode($cek_detail->no_surat_packing),
				);
				echo json_encode($msg);
			}
		}
	}

	public function proses_impor_pl()
	{
		$config['upload_path'] 		= './uploads/';
		$config['allowed_types'] 	= 'xlsx|xls';
		$config['file_name']			= 'doc' . time();
		// $config['max_size']  = '100';
		// $config['max_width']  = '1024';
		// $config['max_height']  = '768';

		$this->load->library('upload', $config);
		if ($this->upload->do_upload('import_data')) {
			$file 		= $this->upload->data();
			$reader 	= ReaderEntityFactory::createXLSXReader();

			$reader->open('uploads/' . $file['file_name']);
			$nomor 	   	= $this->input->post('nomor');
			$numSheet 	= 0;
			$jumlah 	= 0;
			foreach ($reader->getSheetIterator() as $sheet) {
				$numRow = 1;
				if ($numSheet == 0) {
					foreach ($sheet->getRowIterator() as $row) {
						if ($numRow == 1) {
							if ($row->getCellAtIndex(0) != 'kode_barang_surat_packing' || $row->getCellAtIndex(1) != 'nama_barang_surat_packing' || $row->getCellAtIndex(2) != 'jumlah_barang_surat_packing' || $row->getCellAtIndex(3) != 'satuan_barang_surat_packing' || $row->getCellAtIndex(4) != 'keterangan_barang_surat_packing') {
								$reader->close();
								unlink('uploads/' . $file['file_name']);

								$msg = array(
									'validasi'		=> 'Data import tidak sesuai!',
								);
								echo json_encode($msg);
							}
						}

						if ($numRow > 1) {
							$cells 	   = $row->getCells();

							$dataDetailPL 	= array(
								'no_surat_packing'					=> $nomor,
								'kode_barang_surat_packing' 		=> $row->getCellAtIndex(0),
								'nama_barang_surat_packing' 		=> $row->getCellAtIndex(1),
								'jumlah_barang_surat_packing' 		=> $row->getCellAtIndex(2),
								'satuan_barang_surat_packing' 		=> $row->getCellAtIndex(3),
								'keterangan_barang_surat_packing' 	=> $row->getCellAtIndex(4),
							);

							$this->Surat_model->insert_detail_packing($dataDetailPL);

							write_log();
						}
						$numRow++;
						$jumlah++;
					}
					$reader->close();
					unlink('uploads/' . $file['file_name']);

					$msg = array(
						'sukses'	=> $jumlah . ' Data imported successfully',
						'nomor'		=> base64_encode($nomor)
					);
					echo json_encode($msg);
					// $this->session->set_flashdata('message', '<div class="alert alert-success">'.$jumlah.' Data imported successfully</div>');
					// redirect('admin/surat/surat_packing_ubah/'.base64_encode($nomor));
				}
				$numSheet++;
			}
		} else {
			// $error = array('error' => $this->upload->display_errors());
			// $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>')

			$msg = array(
				'validasi'	=> 'Terjadi Kesalahan!',
			);
			echo json_encode($msg);
			// $error = array('error' => $this->upload->display_errors());
			// $this->session->set_flashdata('message', '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
			// redirect('admin/surat/surat_packing_ubah/'.base64_encode($nomor));
			// return $error;
		}
	}

	public function surat_packing_ekspor($nomor)
	{
		$surat_packing = $this->Surat_model->get_surat_packing_by_id_row_packing(base64_decode($nomor));
		$detail_surat_packing = $this->Surat_model->get_detail_surat_packing_by_nomor_packing(base64_decode($nomor));
		$title = "Export Data Surat Packing List_" . date("H_i_s");

		// PHPOffice

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Data Surat Packing
		$sheet->setCellValue('A1', 'Tanggal Surat Packing');
		$sheet->setCellValue('B1', 'Nomor Surat Packing');
		$sheet->mergeCells('C1:H1');
		$sheet->setCellValue('C1', 'Nama Surat Packing');
		$sheet->setCellValue('I1', 'Kepada');
		$sheet->setCellValue('J1', 'Nama Penerima');
		$sheet->setCellValue('K1', 'Alamat Penerima');

		$sheet->setCellValue('A2', $surat_packing->tgl_surat_packing);
		$sheet->setCellValue('B2', $surat_packing->no_surat_packing);
		$sheet->mergeCells('C2:H2');
		$sheet->setCellValue('C2', $surat_packing->nama_surat_packing);
		$sheet->setCellValue('I2', $surat_packing->kepada_surat_packing);
		$sheet->setCellValue('J2', $surat_packing->nama_penerima);
		$sheet->setCellValue('K2', $surat_packing->alamat_penerima);


		// Data Detail Surat Packing
		$sheet->setCellValue('A4', 'Kode Barang');
		$sheet->setCellValue('B4', 'Nama Barang');
		$sheet->setCellValue('C4', 'Jumlah');
		$sheet->setCellValue('D4', 'Satuan');
		$sheet->mergeCells('E4:K4');
		$sheet->setCellValue('E4', 'Keterangan');
		// set Row
		$rowCount = 5;
		foreach ($detail_surat_packing as $list) {
			$sheet->SetCellValue('A' . $rowCount, $list->kode_barang_surat_packing);
			$sheet->SetCellValue('B' . $rowCount, $list->nama_barang_surat_packing);
			$sheet->SetCellValue('C' . $rowCount, $list->jumlah_barang_surat_packing);
			$sheet->SetCellValue('D' . $rowCount, $list->satuan_barang_surat_packing);
			$sheet->mergeCells('E' . $rowCount . ':K' . $rowCount);
			$sheet->SetCellValue('E' . $rowCount, $list->keterangan_barang_surat_packing);
			$rowCount++;
		}

		$writer = new Xlsx($spreadsheet);

		header('Content-Type: application/vnd.ms-excel');
		header("Content-Transfer-Encoding: Binary");
		header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
		header("Pragma: no-cache");
		header("Expires: 0");

		$writer->save('php://output');

		die();
	}
}

/* End of file Surat.php */
/* Location: ./application/controllers/admin/Surat.php */