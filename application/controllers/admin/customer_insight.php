<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

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

class customer_insight extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['module'] = 'Customer Insight';

        $this->load->model(array('Bahan_kemas_model', 'Vendor_model', 'Venmasaccess_model', 'Produk_model', 'Toko_model', 'Tokproaccess_model', 'Kurir_model', 'Keluar_model', 'Keluar_sementara_model', 'Paket_model', 'Resi_model', 'Dashboard_model', 'Status_transaksi_model', 'Keyword_model', 'Resi_model', 'Retur_model'));

        $this->data['company_data']                        = $this->Company_model->company_profile();
        $this->data['layout_template']                = $this->Template_model->layout();
        $this->data['skins_template']                 = $this->Template_model->skins();

        $this->data['btn_submit'] = 'Save';
        $this->data['btn_restore'] = 'Restore Database';
        $this->data['btn_reset']  = 'Reset';
        $this->data['btn_add']    = 'Add New Data';
        $this->data['add_action'] = base_url('admin/keluar/penjualan_produk');
        $this->data['btn_import']    = 'Format Data Import';
        $this->data['btn_backup']    = 'Backup Database';
        $this->data['import_action'] = base_url('assets/template/excel/format_penjualan.xlsx');
        $this->data['backup_db_action'] = base_url('admin/keluar/backup_db');
        $this->data['format_diterima'] = base_url('assets/template/excel/format_jumlah_diterima.xlsx');
        $this->data['btn_sinkron_total_harga']    = 'Data Sync with Total Price';
        $this->data['sinkron_total_harga_action'] = base_url('admin/keluar/sinkron_total_harga');

        is_login();

        if ($this->uri->segment(1) != NULL) {
            menuaccess_check();
        } elseif ($this->uri->segment(2) != NULL) {
            submenuaccess_check();
        }
    }

    public function index()
    {
        $this->data['page_title']     = 'Dashboard ' . $this->data['module'];
        $this->data['get_all_toko_impor'] = $this->Keluar_model->get_all_toko_only();
        $this->data['get_all_gudang_impor'] = $this->Keluar_model->get_all_gudang_only();
        $this->data['get_all_toko_penjualan'] = $this->Keluar_model->get_all_toko_only();
        $this->data['get_all_gudang_penjualan'] = $this->Keluar_model->get_all_gudang_only();

        $this->data['toko_impor_id'] = [
            'name'          => 'toko_impor_id[]',
            'id'            => 'toko-impor-id',
            'class'         => 'form-control select2-multiple',
            'style'            => 'width:100%',
            'multiple'      => '',
        ];

        $this->data['gudang_impor_id'] = [
            'name'          => 'gudang_impor_id[]',
            'id'            => 'gudang-impor-id',
            'class'         => 'form-control select2-multiple',
            'style'            => 'width:100%',
            'multiple'      => '',
        ];

        $this->data['toko_penjualan_id'] = [
            'name'          => 'toko_penjualan_id[]',
            'id'            => 'toko-penjualan-id',
            'class'         => 'form-control select2-multiple',
            'style'            => 'width:100%',
            'multiple'      => '',
        ];

        $this->data['gudang_penjualan_id'] = [
            'name'          => 'gudang_penjualan_id[]',
            'id'            => 'gudang-penjualan-id',
            'class'         => 'form-control select2-multiple',
            'style'            => 'width:100%',
            'multiple'      => '',
        ];

        $this->load->view('back/keluar/dashboard', $this->data);
    }

    // ORIGINAL
    public function customerinsight()
    {
        is_read();

        $this->data['page_title'] = 'Customer Insight';

        $this->data['get_all_provinsi'] = $this->Keyword_model->get_all_provinsi_combobox();
        $this->data['get_all_toko'] = $this->Keluar_model->get_all_toko_list();
        $this->data['get_all_status'] = $this->Keluar_model->get_all_status_list();
        $this->data['get_all_resi'] = array(
            'semua'    => '- Semua Data-',
            ''         => 'Tidak Ada Resi'
        );

        // $this->data['get_all'] = $this->Keluar_model->get_all();
        $this->data['provinsi'] = [
            'class'         => 'form-control select2bs4',
            'id'            => 'provinsi',
            'required'      => '',
            'style'         => 'width:100%'
        ];

        $this->data['kabupaten'] = [
            'class'         => 'form-control select2bs4',
            'id'            => 'kabupaten',
            'required'      => '',
            'style'         => 'width:100%'
        ];

        $this->data['resi'] = [
            'class'         => 'form-control select2bs4',
            'id'            => 'resi',
            'required'      => '',
            'style'         => 'width:100%'
        ];

        $this->data['status'] = [
            'class'         => 'form-control select2bs4',
            'id'            => 'status',
            'required'      => '',
            'style'         => 'width:100%'
        ];

        $this->data['diterima'] = [
            'id'             => 'diterima',
            'type'          => 'hidden',
        ];

        $this->load->view('back/keluar/customer_insight', $this->data);
    }

    function get_data_customer_insight()
    {
        $start = substr($this->input->get('periodik'), 0, 10);
        $end = substr($this->input->get('periodik'), 13, 24);
        $provinsi = $this->input->get('provinsi');
        $kabupaten = $this->input->get('kabupaten');
        $belanja_min = $this->input->get('belanja_min');
        $belanja_max = $this->input->get('belanja_max');
        $qty_min = $this->input->get('qty_min');
        $qty_max = $this->input->get('qty_max');
        $list = $this->Keluar_model->get_datatable_customer_insight($start, $end, $provinsi, $kabupaten, $belanja_min, $belanja_max, $qty_min, $qty_max);
        $dataJSON = array();
        foreach ($list as $data) {
            $get_detail_penjualan = $this->Keluar_model->get_detail_by_cust_data($data->nama_penerima, $data->hp_penerima, $start, $end);
            $detail = '<table cellpadding="0" width="100%" cellspacing="0" class="table" border="0" style="padding-left:50px;">' .
                '<tr align="center">' .
                '<td width="1%">Qty</td>' .
                '<td colspan="2">Nama Produk</td>' .
                '</tr>';

            foreach ($get_detail_penjualan as $val_detail) {
                $detail .= '<tr align="center">' .
                    '<td>' . $val_detail->total_qty . '</td>' .
                    '<td colspan="2">' . $val_detail->nama_produk . '</td>' .
                    '</tr>';
            }

            $row = array();
            $row['nomor_pesanan'] = $data->nomor_pesanan;
            $row['tanggal'] = date('d-m-Y', strtotime($data->tgl_penjualan));
            $row['nomor_resi'] = $data->nomor_resi;
            $row['nama_penerima'] = $data->nama_penerima;
            $row['hp_penerima'] = $data->hp_penerima;
            $row['provinsi'] = $data->provinsi;
            $row['kabupaten'] = $data->kabupaten;
            $row['created'] = $data->created;
            $row['total_harga'] = $data->total_harga;
            $row['total_jual'] = $data->total_jual;
            $row['total_hpp'] = $data->total_hpp;
            $row['margin'] = $data->margin;
            $row['selisih_margin'] = $data->selisih_margin;
            $row['ongkir'] = $data->ongkir;
            $row['jumlah_diterima'] = $data->jumlah_diterima;
            $row['total_qty'] = $data->total_qty;
            $row['jumlah_pesanan'] = $data->jumlah_pesanan;
            $row['total_harga_jual'] = 'Rp. ' . number_format($data->total_harga_jual, 0, ",", ".");
            if ($data->tgl_diterima == NULL) {
                $row['tgl_diterima'] = "-";
            } else {
                $row['tgl_diterima'] = $data->tgl_diterima;
            }
            $row['detail'] = $detail;
            $dataJSON[] = $row;
        }

        $output = array(
            "recordsTotal" => 10,
            "recordsFiltered" => 10,
            "data" => $dataJSON,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function get_id_provinsi()
    {
        $provinsi = $this->input->post('provinsi');
        $select_box[] = "<option value=''>- Pilih Kabupaten -</option>";
        // $kabupaten = json_decode(json_encode(kabupaten($provinsi)));
        $kabupaten = $this->Keyword_model->get_kabupaten_by_provinsi($provinsi);
        if (count($kabupaten) > 0) {
            foreach ($kabupaten as $val_kab) {
                $select_box[] = '<option value="' . $val_kab->nama_kotkab . '">' . $val_kab->nama_kotkab . '</option>';
            }
            // for ($i = 0; $i < count($kabupaten); $i++) {
            // 	$select_box[] = '<option value="'.$kabupaten[$i].'">'.$kabupaten[$i].'</option>';
            // }

            echo json_encode($select_box);
        } else {
            $select_box = '<option value="">Tidak Ada</option>';
            echo json_encode($select_box);
        }
    }

    public function get_id_toko()
    {
        $toko = $this->input->post('toko');
        $select_box[] = "<option value=''>- Pilih Nama Produk -</option>";
        $produk = $this->Keluar_model->get_id_toko($toko);
        if (count($produk) > 0) {
            foreach ($produk as $row) {
                $select_box[] = '<option value="' . $row->id_produk . '">' . $row->kode_sku . ' | ' . $row->sub_sku . ' | ' . $row->nama_produk . ' | 	 Stok: ' . $row->qty_produk . '</option>';
            }
            // header("Content-Type:application/json");
            echo json_encode($select_box);
        } else {
            $select_box = '<option value="">Tidak Ada</option>';
            echo json_encode($select_box);
        }
    }

    public function get_id_produk()
    {
        $produk = $this->input->post('produk');
        // $id_barang = "RPL2003200001";
        $cari_produk =    $this->Keluar_model->get_id_produk($produk);
        echo json_encode($cari_produk);
    }

    // public function export_customer_insight($start, $end, $provinsi, $kabupaten, $belanja_min, $belanja_max, $qty_min, $qty_max)
    public function export_customer_insight()
    {
        $start = substr($this->input->get('periodik'), 0, 10);
        $end = substr($this->input->get('periodik'), 13, 24);
        $provinsi = $this->input->get('provinsi');
        $kabupaten = $this->input->get('kabupaten');
        $belanja_min = $this->input->get('belanja_min');
        $belanja_max = $this->input->get('belanja_max');
        $qty_min = $this->input->get('qty_min');
        $qty_max = $this->input->get('qty_max');
        $data['title']    = "Export Data Customer Insight Per Tanggal " . $start . " - " . $end . "_" . date("H_i_s");
        $lists = $this->Keluar_model->get_datatable_customer_insight($start, $end, $provinsi, $kabupaten, $belanja_min, $belanja_max, $qty_min, $qty_max);
        // die(print_r($lists));
        // PHPOffice
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'nomor_pesanan');
        $sheet->setCellValue('B1', 'nama_penerima');
        $sheet->setCellValue('C1', 'hp_penerima');
        $sheet->setCellValue('D1', 'qty');
        $sheet->setCellValue('E1', 'frequency');
        $sheet->setCellValue('F1', 'total_belanja');

        // set Row
        $rowCount = 2;
        foreach ($lists as $list) {
            // Nomor Pesanan
            if (is_numeric($list->nomor_pesanan)) {
                if (strlen($list->nomor_pesanan) < 15) {
                    $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                    $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
                } else {
                    $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    // The old way to force string. NumberFormat::FORMAT_TEXT is not
                    // enough.
                    // $formatted_value .= ' ';
                    // $sheet->SetCellValue('A' . $rowCount, "'".$formatted_value);
                    $sheet->setCellValueExplicit('A' . $rowCount, $list->nomor_pesanan, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
            } else {
                $sheet->getStyle('A' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $sheet->SetCellValue('A' . $rowCount, $list->nomor_pesanan);
            }
            $sheet->SetCellValue('B' . $rowCount, $list->nama_penerima);

            // Nomor HP
            if (is_numeric($list->hp_penerima)) {
                if (strlen($list->hp_penerima) < 15) {
                    $firstCharacter = substr($list->hp_penerima, 0, 1);
                    if ($firstCharacter == '0') {

                        $edit_no = substr_replace($list->hp_penerima, "62", 0, 1);
                        $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                        $sheet->setCellValueExplicit('C' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    } else if ($firstCharacter == '6') {
                        // $sheet->getStyle('AD' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                        //  $sheet->SetCellValue('AD' . $rowCount, '+'.$list->hp_penerima);			          	

                        $ceknoldi62 = substr($list->hp_penerima, 0, 3);
                        if ($ceknoldi62 == '620') {
                            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                            // The old way to force string. NumberFormat::FORMAT_TEXT is not
                            // enough.
                            // $formatted_value .= ' ';
                            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
                            $sheet->setCellValueExplicit('C' . $rowCount, substr_replace($list->hp_penerima, "62", 0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        } else {
                            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                            // The old way to force string. NumberFormat::FORMAT_TEXT is not
                            // enough.
                            // $formatted_value .= ' ';
                            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
                            $sheet->setCellValueExplicit('C' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        }
                    }
                } else {
                    $firstCharacter = substr($list->hp_penerima, 0, 1);
                    if ($firstCharacter == '0') {
                        $edit_no = substr_replace($list->hp_penerima, "62", 0, 1);
                        $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                        // The old way to force string. NumberFormat::FORMAT_TEXT is not
                        // enough.
                        // $formatted_value .= ' ';
                        // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
                        $sheet->setCellValueExplicit('C' . $rowCount, $edit_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    } else if ($firstCharacter == '6') {

                        $ceknoldi62 = substr($list->hp_penerima, 0, 3);
                        if ($ceknoldi62 == '620') {
                            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                            // The old way to force string. NumberFormat::FORMAT_TEXT is not
                            // enough.
                            // $formatted_value .= ' ';
                            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
                            $sheet->setCellValueExplicit('C' . $rowCount, substr_replace($list->hp_penerima, "62", 0, 3), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        } else {
                            $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                            // The old way to force string. NumberFormat::FORMAT_TEXT is not
                            // enough.
                            // $formatted_value .= ' ';
                            // $sheet->SetCellValue('AD' . $rowCount, "'".$formatted_value);
                            $sheet->setCellValueExplicit('C' . $rowCount, $list->hp_penerima, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        }
                    }
                }
            } else {
                $firstCharacter = substr($list->hp_penerima, 0, 1);
                if ($firstCharacter == '0') {
                    $edit_no = substr_replace($list->hp_penerima, "62", 0, 1);
                    $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->SetCellValue('C' . $rowCount, $edit_no);
                } else if ($firstCharacter == '6') {
                    $ceknoldi62 = substr($list->hp_penerima, 0, 3);
                    if ($ceknoldi62 == '620') {
                        $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                        $sheet->SetCellValue('C' . $rowCount, substr_replace($list->hp_penerima, "62", 0, 3));
                    } else {
                        $sheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                        $sheet->SetCellValue('C' . $rowCount, $list->hp_penerima);
                    }
                }
            }

            $sheet->SetCellValue('D' . $rowCount, $list->qty);
            $sheet->SetCellValue('E' . $rowCount, $list->jumlah_pesanan);
            $sheet->SetCellValue('F' . $rowCount, 'Rp. ' . number_format($list->total_harga_jual, 0, ",", "."));

            $rowCount++;
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Disposition: attachment;filename="' . $data['title'] . '.xlsx"');
        header("Pragma: no-cache");
        header("Expires: 0");

        $writer->save('php://output');

        die();
    }

    public function ajax_dasbor_total_penjualan()
    {
        $start         = substr($this->input->post('periodik'), 0, 10);
        $end         = substr($this->input->post('periodik'), 13, 24);
        // Hitung jarak dan membandingkan dihari sebelumnya
        $jarak        = abs(strtotime($end) - strtotime($start));
        if (round($jarak / (60 * 60 * 24)) == 0) {
            $fix_jarak = round($jarak / (60 * 60 * 24)) + 1;
        } else {
            $fix_jarak = round($jarak / (60 * 60 * 24)) + 1;
        }
        $start_past = date('Y-m-d', strtotime("$start -$fix_jarak days"));
        $end_past    = date('Y-m-d', strtotime("$end  -$fix_jarak days"));
        // End hitung jarak dan membandingkan dihari sebelumnya

        // Get Income
        $get_income    = $this->Dashboard_model->get_pendapat_dasbor_penjualan($start, $end);
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
        $max_pesan      = max(array($get_pesan->jumlah_tanggal, $get_pesan_past->jumlah_tanggal));

        if ($max_diterima == NULL && $max_gross == NULL && $max_hpp == NULL && $max_revenue == NULL && $max_ongkir == NULL && $max_pesan == 0 && $max_pending == NULL && $max_bruto == NULL && $max_margin == NULL && $max_selisih_margin == NULL) {
            $html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL PESANAN</span>';

            $html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL DITERIMA</span>';

            $html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL PENDING PAYMENT</span>';

            $html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL REVENUE</span>';

            $html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL GROSS REVENUE</span>';

            $html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL ONGKIR</span>';

            $html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL BRUTO</span>';

            $html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL MARGIN</span>';

            $html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL SELISIH MARGIN</span>';

            $html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                '<h5 class="description-header">0</h5>' .
                '<span class="description-text">TOTAL HPP</span>';
        } else {
            // Mencari total persen dari range angka

            // PESANAN
            if ($max_pesan == 0) {
                $html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL PESANAN</span>';
            } else {
                $persen_pesan    = (($get_pesan->jumlah_tanggal - $get_pesan_past->jumlah_tanggal) / $max_pesan) * 100;
                $sisa_pesanan    = $get_pesan->jumlah_tanggal - $get_pesan_past->jumlah_tanggal;

                if ($persen_pesan == 0 && $sisa_pesanan == 0) {
                    $html_pesan = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>' .
                        '<h5 class="description-header">' . $get_pesan->jumlah_tanggal . '</h5>' .
                        '<span class="description-text">TOTAL PESANAN</span>';
                } else if ($persen_pesan < 0 && $sisa_pesanan < 0) {
                    $html_pesan = '<span class="description-percentage text-red"> -' . ($sisa_pesanan * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_pesan * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . ($get_pesan->jumlah_tanggal) . '</h5>' .
                        '<span class="description-text">TOTAL PESANAN</span>';
                } else if ($persen_pesan > 0 && $sisa_pesanan > 0) {
                    $html_pesan = '<span class="description-percentage text-green">+' . $sisa_pesanan . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_pesan) . '%)</span>' .
                        '<h5 class="description-header">' . $get_pesan->jumlah_tanggal . '</h5>' .
                        '<span class="description-text">TOTAL PESANAN</span>';
                }
            }

            // DITERIMA
            if ($max_diterima == 0) {
                $html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL DITERIMA</span>';
            } else {
                $persen_diterima    = (($get_income->diterima - $get_income_past->diterima) / $max_diterima) * 100;
                $sisa_diterima        = $get_income->diterima - $get_income_past->diterima;

                if ($persen_diterima == 0 && $sisa_diterima == 0) {
                    $html_diterima = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>' .
                        '<h5 class="description-header">' . rupiah($get_income->diterima) . '</h5>' .
                        '<span class="description-text">TOTAL DITERIMA</span>';
                } else if ($persen_diterima < 0 && $sisa_diterima < 0) {
                    $html_diterima = '<span class="description-percentage text-red"> -' . rupiah($sisa_diterima * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_diterima * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->diterima) . '</h5>' .
                        '<span class="description-text">TOTAL DITERIMA</span>';
                } else if ($persen_diterima > 0 && $sisa_diterima > 0) {
                    $html_diterima = '<span class="description-percentage text-green">+' . rupiah($sisa_diterima) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_diterima) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->diterima) . '</h5>' .
                        '<span class="description-text">TOTAL DITERIMA</span>';
                }
            }

            // PENDING PAYMENT
            if ($max_pending == 0) {
                $html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL PENDING PAYMENT</span>';
            } else {
                $persen_pending    = (($get_pending->total_pending - $get_pending_past->total_pending) / $max_pending) * 100;
                $sisa_pending    = $get_pending->total_pending - $get_pending_past->total_pending;

                if ($persen_pending == 0 && $sisa_pending == 0) {
                    $html_pending = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>' .
                        '<h5 class="description-header">' . rupiah($get_pending->total_pending) . '</h5>' .
                        '<span class="description-text">TOTAL PENDING PAYMENT</span>';
                } else if ($persen_pending < 0 && $sisa_pending < 0) {
                    $html_pending = '<span class="description-percentage text-green"> -' . rupiah($sisa_pending * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_pending * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_pending->total_pending) . '</h5>' .
                        '<span class="description-text">TOTAL PENDING PAYMENT</span>';
                } else if ($persen_pending > 0 && $sisa_pending > 0) {
                    $html_pending = '<span class="description-percentage text-red">+' . rupiah($sisa_pending) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_pending) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_pending->total_pending) . '</h5>' .
                        '<span class="description-text">TOTAL PENDING PAYMENT</span>';
                }
            }

            // GROSS REVENUE
            if ($max_gross == 0) {
                $html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL GROSS REVENUE</span>';
            } else {
                $persen_gross        = (($get_income->fix - $get_income_past->fix) / $max_gross) * 100;
                $sisa_gross        = $get_income->fix - $get_income_past->fix;

                if ($persen_gross == 0 && $sisa_gross == 0) {
                    $html_gross = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->fix) . '</h5>' .
                        '<span class="description-text">TOTAL GROSS REVENUE</span>';
                } else if ($persen_gross < 0 && $sisa_gross < 0) {
                    $html_gross = '<span class="description-percentage text-red"> -' . rupiah($sisa_gross * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_gross * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->fix) . '</h5>' .
                        '<span class="description-text">TOTAL GROSS REVENUE</span>';
                } else if ($persen_gross > 0 && $sisa_gross > 0) {
                    $html_gross = '<span class="description-percentage text-green"> +' . rupiah($sisa_gross) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_gross) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->fix) . '</h5>' .
                        '<span class="description-text">TOTAL GROSS REVENUE</span>';
                }
            }

            // BRUTO
            if ($max_bruto == 0) {
                $html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL BRUTO/span>';
            } else {
                $persen_bruto        = (($get_income->bruto - $get_income_past->bruto) / $max_bruto) * 100;
                $sisa_bruto        = $get_income->bruto - $get_income_past->bruto;

                if ($persen_bruto == 0 && $sisa_bruto == 0) {
                    $html_bruto = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->bruto) . '</h5>' .
                        '<span class="description-text">TOTAL BRUTO</span>';
                } else if ($persen_bruto < 0 && $sisa_bruto < 0) {
                    $html_bruto = '<span class="description-percentage text-red"> -' . rupiah($sisa_bruto * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_bruto * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->bruto) . '</h5>' .
                        '<span class="description-text">TOTAL BRUTO</span>';
                } else if ($persen_bruto > 0 && $sisa_bruto > 0) {
                    $html_bruto = '<span class="description-percentage text-green"> +' . rupiah($sisa_bruto) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_bruto) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->bruto) . '</h5>' .
                        '<span class="description-text">TOTAL BRUTO</span>';
                }
            }

            // REVENUE
            if ($max_revenue == 0) {
                $html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL REVENUE</span>';
            } else {
                $persen_revenue    = (($get_income->total - $get_income_past->total) / $max_revenue) * 100;
                $sisa_revenue        = $get_income->total - $get_income_past->total;

                if ($persen_revenue == 0 && $sisa_revenue == 0) {
                    $html_revenue = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->total) . '</h5>' .
                        '<span class="description-text">TOTAL REVENUE</span>';
                } else if ($persen_revenue < 0 && $sisa_revenue < 0) {
                    $html_revenue = '<span class="description-percentage text-red"> -' . rupiah($sisa_revenue * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_revenue * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->total) . '</h5>' .
                        '<span class="description-text">TOTAL REVENUE</span>';
                } else if ($persen_revenue > 0 && $sisa_revenue > 0) {
                    $html_revenue = '<span class="description-percentage text-green"> +' . rupiah($sisa_revenue) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_revenue) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->total) . '</h5>' .
                        '<span class="description-text">TOTAL REVENUE</span>';
                }
            }

            // ONGKIR
            if ($max_ongkir == 0) {
                $html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL ONGKIR</span>';
            } else {
                $persen_ongkir    = (($get_income->tot_ongkir - $get_income_past->tot_ongkir) / $max_ongkir) * 100;
                $sisa_ongkir    = $get_income->tot_ongkir - $get_income_past->tot_ongkir;

                if ($persen_ongkir == 0 && $sisa_ongkir == 0) {
                    $html_ongkir = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0 %</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->tot_ongkir) . '</h5>' .
                        '<span class="description-text">TOTAL ONGKIR</span>';
                } else if ($persen_ongkir < 0 && $sisa_ongkir < 0) {
                    $html_ongkir = '<span class="description-percentage text-green"> -' . rupiah($sisa_ongkir * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_ongkir * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->tot_ongkir) . '</h5>' .
                        '<span class="description-text">TOTAL ONGKIR</span>';
                } else if ($persen_ongkir > 0 && $sisa_ongkir > 0) {
                    $html_ongkir = '<span class="description-percentage text-red"> +' . rupiah($sisa_ongkir) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_ongkir) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->tot_ongkir) . '</h5>' .
                        '<span class="description-text">TOTAL ONGKIR</span>';
                }
            }

            // MARGIN
            if ($max_margin == 0) {
                $html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL MARGIN</span>';
            } else {
                $persen_margin    = (($get_income->margin - $get_income_past->margin) / $max_margin) * 100;
                $sisa_margin        = $get_income->margin - $get_income_past->margin;

                if ($persen_margin == 0 && $sisa_margin == 0) {
                    $html_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>' .
                        '<h5 class="description-header">' . rupiah($get_income->margin) . '</h5>' .
                        '<span class="description-text">TOTAL MARGIN</span>';
                } else if ($persen_margin < 0 && $sisa_margin < 0) {
                    $html_margin = '<span class="description-percentage text-red"> -' . rupiah($sisa_margin * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_margin * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->margin) . '</h5>' .
                        '<span class="description-text">TOTAL MARGIN</span>';
                } else if ($persen_margin > 0 && $sisa_margin > 0) {
                    $html_margin = '<span class="description-percentage text-green">+' . rupiah($sisa_margin) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_margin) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->margin) . '</h5>' .
                        '<span class="description-text">TOTAL MARGIN</span>';
                }
            }

            // SELISIH MARGIN
            if ($max_selisih_margin == 0) {
                $html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL SELISIH MARGIN</span>';
            } else {
                $persen_selisih_margin    = (($get_income->selisih_margin - $get_income_past->selisih_margin) / $max_selisih_margin) * 100;
                $sisa_selisih_margin        = $get_income->selisih_margin - $get_income_past->selisih_margin;

                if ($persen_selisih_margin == 0 && $sisa_selisih_margin == 0) {
                    $html_selisih_margin = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>' .
                        '<h5 class="description-header">' . rupiah($get_income->selisih_margin) . '</h5>' .
                        '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
                } else if ($persen_selisih_margin < 0 || $sisa_selisih_margin < 0) {
                    $html_selisih_margin = '<span class="description-percentage text-red"> -' . rupiah($sisa_selisih_margin) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_selisih_margin) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->selisih_margin) . '</h5>' .
                        '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
                } else if ($persen_selisih_margin > 0 && $sisa_selisih_margin > 0) {
                    $html_selisih_margin = '<span class="description-percentage text-green">+' . rupiah($sisa_selisih_margin) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_selisih_margin) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->selisih_margin) . '</h5>' .
                        '<span class="description-text">TOTAL SELISIH  MARGIN</span>';
                }
            }

            // HPP
            if ($max_hpp == 0) {
                $html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0%</span>' .
                    '<h5 class="description-header">0</h5>' .
                    '<span class="description-text">TOTAL HPP</span>';
            } else {
                $persen_hpp    = (($get_income->hpp - $get_income_past->hpp) / $max_hpp) * 100;
                $sisa_hpp        = $get_income->hpp - $get_income_past->hpp;

                if ($persen_hpp == 0 && $sisa_hpp == 0) {
                    $html_hpp = '<span class="description-percentage text-yellow"><i class="fa fa-caret-left" style="margin-right:5px;"></i> 0% </span>' .
                        '<h5 class="description-header">' . rupiah($get_income->hpp) . '</h5>' .
                        '<span class="description-text">TOTAL HPP</span>';
                } else if ($persen_hpp < 0 && $sisa_hpp < 0) {
                    $html_hpp = '<span class="description-percentage text-red"> -' . rupiah($sisa_hpp * -1) . ' (<i class="fa fa-caret-down" style="margin-right:5px;"></i>' . round($persen_hpp * -1) . ' %)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->hpp) . '</h5>' .
                        '<span class="description-text">TOTAL HPP</span>';
                } else if ($persen_hpp > 0 && $sisa_hpp > 0) {
                    $html_hpp = '<span class="description-percentage text-green">+' . rupiah($sisa_hpp) . ' (<i class="fa fa-caret-up" style="margin-right:5px;"></i>' . round($persen_hpp) . '%)</span>' .
                        '<h5 class="description-header">' . rupiah($get_income->hpp) . '</h5>' .
                        '<span class="description-text">TOTAL HPP</span>';
                }
            }

            // // End Mencari total persen dari range angka
        }

        $result = array(
            'gross'              => $html_gross,
            'diterima'            => $html_diterima,
            'pending'            => $html_pending,
            // 'judul'   => 'Statistik Data Keuangan Tanggal: '.$start.' - '.$end.' dengan Tanggal: '.$start_past.' - '.$end_past.' ('.$fix_jarak.' Hari)', 
            // 'hpp' 	   => $hpp,
            'revenue'          => $html_revenue,
            'bruto'            => $html_bruto,
            'ongkir'              => $html_ongkir,
            'pesan'              => $html_pesan,
            'margin'            => $html_margin,
            'selisih_margin'    => $html_selisih_margin,
            'hpp'                => $html_hpp,
        );

        echo json_encode($result);
    }
}

/* End of file Keluar.php */
/* Location: ./application/controllers/admin/Keluar.php */