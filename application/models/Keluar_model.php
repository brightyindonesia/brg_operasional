<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Keluar_model extends CI_Model
{
  public $table         = 'penjualan';
  public $id            = 'nomor_pesanan';
  public $detail_table  = 'detail_penjualan';
  public $table_kurir   = 'kurir';
  public $id_kurir       = 'id_kurir';
  public $table_status  = 'status_transaksi';
  public $id_status     = 'id_status_transaksi';
  public $table_toko     = 'toko';
  public $id_toko       = 'id_toko';
  public $table_gudang  = 'gudang';
  public $id_gudang     = 'id_gudang';
  public $table_produk   = 'produk';
  public $id_produk     = 'id_produk';
  public $table_tokpro   = 'tokpro_data_access';
  public $id_tokpro     = 'id_tokpro_access';
  public $table_sku     = 'sku';
  public $id_sku        = 'id_sku';
  public $order         = 'DESC';

  public $column_order = array(null, 'nomor_pesanan', 'nama_toko', 'nama_kurir', 'nomor_resi', null); //field yang ada di table user
  public $column_search = array('nomor_pesanan', 'nama_toko', 'nama_penerima', 'nama_kurir', 'nomor_resi'); //field yang diizin untuk pencarian 
  public $order_data = array('tgl_penjualan' => 'asc'); // default order

  public $column_order_sku = array('nama_produk', 'sub_sku', 'sum_qty'); //field yang ada di table user
  public $column_search_sku = array('nama_produk', 'sub_sku', 'sum_qty'); //field yang diizin untuk pencarian 
  public $order_data_sku = array('tgl_penjualan' => 'asc'); // default order 

  // Table Server Side
  // SKU
  private function _get_datatables_query_sku_impor()
  {
    $i = 0;

    foreach ($this->column_search_sku as $item) // looping awal
    {
      if ($_GET['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
      {

        if ($i === 0) // looping awal
        {
          $this->db->group_start();
          $this->db->like($item, $_GET['search']['value']);
        } else {
          $this->db->or_like($item, $_GET['search']['value']);
        }

        if (count($this->column_search_sku) - 1 == $i)
          $this->db->group_end();
      }
      $i++;
    }

    if (isset($_GET['order'])) {
      $this->db->order_by($this->column_order_sku[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
    } else if (isset($this->order_data_sku)) {
      $order = $this->order_data_sku;
      $this->db->order_by(key($order), $order[key($order)]);
    }

    $start = substr($_GET['periodik'], 0, 10);
    $end = substr($_GET['periodik'], 13, 24);
    $this->db->select('*');
    $this->db->select('SUM(qty) as sum_qty');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('detail_penjualan.id_produk');
    $this->db->from($this->table);

    if (isset($_GET['toko'])) {
      $this->db->where_in('penjualan.id_toko', $_GET['toko']);
    }

    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    // ));
    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $start,
      "date_format(created, '%Y-%m-%d') <="   => $end
    ));
  }

  function get_datatables_sku_impor()
  {
    $this->_get_datatables_query_sku_impor();
    if ($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
    $query = $this->db->get();
    return $query->result();
  }

  private function _get_datatables_query_sku_gudang_impor()
  {
    $i = 0;

    foreach ($this->column_search_sku as $item) // looping awal
    {
      if ($_GET['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
      {

        if ($i === 0) // looping awal
        {
          $this->db->group_start();
          $this->db->like($item, $_GET['search']['value']);
        } else {
          $this->db->or_like($item, $_GET['search']['value']);
        }

        if (count($this->column_search_sku) - 1 == $i)
          $this->db->group_end();
      }
      $i++;
    }

    if (isset($_GET['order'])) {
      $this->db->order_by($this->column_order_sku[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
    } else if (isset($this->order_data_sku)) {
      $order = $this->order_data_sku;
      $this->db->order_by(key($order), $order[key($order)]);
    }

    $start = substr($_GET['periodik'], 0, 10);
    $end = substr($_GET['periodik'], 13, 24);
    $this->db->select('*');
    $this->db->select('SUM(qty) as sum_qty');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('gutok_data_access', 'toko.id_toko = gutok_data_access.id_toko');
    $this->db->join('gudang', 'gudang.id_gudang = gutok_data_access.id_gudang');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('detail_penjualan.id_produk');
    $this->db->from($this->table);

    if (isset($_GET['gudang'])) {
      $this->db->where_in('gudang.id_gudang', $_GET['gudang']);
    }

    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    // ));
    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $start,
      "date_format(created, '%Y-%m-%d') <="   => $end
    ));
  }

  function get_datatables_sku_gudang_impor()
  {
    $this->_get_datatables_query_sku_gudang_impor();
    if ($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
    $query = $this->db->get();
    return $query->result();
  }

  private function _get_datatables_query_sku_penjualan()
  {
    $i = 0;

    foreach ($this->column_search_sku as $item) // looping awal
    {
      if ($_GET['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
      {

        if ($i === 0) // looping awal
        {
          $this->db->group_start();
          $this->db->like($item, $_GET['search']['value']);
        } else {
          $this->db->or_like($item, $_GET['search']['value']);
        }

        if (count($this->column_search_sku) - 1 == $i)
          $this->db->group_end();
      }
      $i++;
    }

    if (isset($_GET['order'])) {
      $this->db->order_by($this->column_order_sku[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
    } else if (isset($this->order_data_sku)) {
      $order = $this->order_data_sku;
      $this->db->order_by(key($order), $order[key($order)]);
    }

    $start = substr($_GET['periodik'], 0, 10);
    $end = substr($_GET['periodik'], 13, 24);
    $this->db->select('*');
    $this->db->select('SUM(qty) as sum_qty');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('detail_penjualan.id_produk');
    $this->db->from($this->table);

    if (isset($_GET['toko'])) {
      $this->db->where_in('penjualan.id_toko', $_GET['toko']);
    }
    // if ($_GET['toko'] != 0 && $_GET['toko'] != 'semua') {
    //   $this->db->where('penjualan.id_toko', $_GET['toko']); 
    // }

    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    // ));
    $this->db->where(array(
      "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
      "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
    ));
  }

  function get_datatables_sku_penjualan()
  {
    $this->_get_datatables_query_sku_penjualan();
    if ($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
    $query = $this->db->get();
    return $query->result();
  }

  private function _get_datatables_query_sku_gudang_penjualan()
  {
    $i = 0;

    foreach ($this->column_search_sku as $item) // looping awal
    {
      if ($_GET['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
      {

        if ($i === 0) // looping awal
        {
          $this->db->group_start();
          $this->db->like($item, $_GET['search']['value']);
        } else {
          $this->db->or_like($item, $_GET['search']['value']);
        }

        if (count($this->column_search_sku) - 1 == $i)
          $this->db->group_end();
      }
      $i++;
    }

    if (isset($_GET['order'])) {
      $this->db->order_by($this->column_order_sku[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
    } else if (isset($this->order_data_sku)) {
      $order = $this->order_data_sku;
      $this->db->order_by(key($order), $order[key($order)]);
    }

    $start = substr($_GET['periodik'], 0, 10);
    $end = substr($_GET['periodik'], 13, 24);
    $this->db->select('*');
    $this->db->select('SUM(qty) as sum_qty');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('gutok_data_access', 'toko.id_toko = gutok_data_access.id_toko');
    $this->db->join('gudang', 'gudang.id_gudang = gutok_data_access.id_gudang');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('detail_penjualan.id_produk');
    $this->db->from($this->table);

    if (isset($_GET['gudang'])) {
      $this->db->where_in('gudang.id_gudang', $_GET['gudang']);
    }

    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    // ));
    $this->db->where(array(
      "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
      "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
    ));
  }

  function get_datatables_sku_gudang_penjualan()
  {
    $this->_get_datatables_query_sku_gudang_penjualan();
    if ($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
    $query = $this->db->get();
    return $query->result();
  }

  public function count_all_sku()
  {
    $this->db->from($this->detail_table);
    return $this->db->count_all_results();
  }

  // Penjualan
  private function _get_datatables_query()
  {
    $i = 0;

    foreach ($this->column_search as $item) // looping awal
    {
      if ($_GET['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
      {

        if ($i === 0) // looping awal
        {
          $this->db->group_start();
          $this->db->like($item, $_GET['search']['value']);
        } else {
          $this->db->or_like($item, $_GET['search']['value']);
        }

        if (count($this->column_search) - 1 == $i)
          $this->db->group_end();
      }
      $i++;
    }

    if (isset($_GET['order'])) {
      $this->db->order_by($this->column_order[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
    } else if (isset($this->order)) {
      $order = $this->order;
      $this->db->order_by(key($order), $order[key($order)]);
    }

    $start = substr($_GET['periodik'], 0, 10);
    $end = substr($_GET['periodik'], 13, 24);
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->select('*');
    $this->db->select('date_format(tgl_penjualan, "%d-%m-%Y") as tanggal');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
    $this->db->from($this->table);

    if ($_GET['kurir'] != 'semua') {
      $this->db->where('penjualan.id_kurir', $_GET['kurir']);
    }
    if ($_GET['toko'] != 'semua') {
      $this->db->where('penjualan.id_toko', $_GET['toko']);
    }
    if ($_GET['resi'] == '' || $_GET['resi'] == NULL) {
      $this->db->where('nomor_resi', '');
    }
    if ($_GET['status'] != 'semua') {
      $this->db->where('penjualan.id_status_transaksi', $_GET['status']);
    }

    if ($_GET['trigger'] == 'impor') {
      $this->db->where(array(
        "date_format(created, '%Y-%m-%d') >="   => $start,
        "date_format(created, '%Y-%m-%d') <="   => $end
      ));
    } else if ($_GET['trigger'] == 'penjualan') {
      $this->db->where(array(
        "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
        "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
      ));
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    // ));
  }

  function get_datatables()
  {
    $this->_get_datatables_query();
    if ($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
    $query = $this->db->get();
    return $query->result();
  }

  function count_filtered()
  {
    $this->_get_datatables_query();
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function count_all()
  {
    $this->db->from($this->table);
    return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_all_kurir()
  {
    $this->db->order_by('nama_kurir');
    $data = $this->db->get($this->table_kurir);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result[''] = '- Pilih Kurir Ekspedisi -';
        $result[$row['id_kurir']] = $row['nama_kurir'];
      }
      return $result;
    }
  }

  function get_all_toko()
  {
    $this->db->order_by('nama_toko');
    $data = $this->db->get($this->table_toko);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result[''] = '- Pilih Toko -';
        $result[$row['id_toko']] = $row['nama_toko'];
      }
      return $result;
    }
  }

  function get_all_status()
  {
    $this->db->order_by('id_status_transaksi', 'asc');
    $data = $this->db->get($this->table_status);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result[''] = '- Pilih Status -';
        $result[$row['id_status_transaksi']] = $row['nama_status_transaksi'];
      }
      return $result;
    }
  }

  function get_all_kurir_list()
  {
    $this->db->order_by('nama_kurir');
    $data = $this->db->get($this->table_kurir);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result['semua'] = '- Semua Data -';
        $result[0] = 'Tidak Ada Kurir';
        $result[$row['id_kurir']] = $row['nama_kurir'];
      }
      return $result;
    }
  }

  function get_toko_sku($start, $end)
  {
    $this->db->select('toko.id_toko');
    $this->db->select('nama_toko');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('toko.id_toko');
    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $start,
      "date_format(created, '%Y-%m-%d') <="   => $end
    ));
    return $this->db->get($this->table)->result_array();
  }

  function get_toko_sku_penjualan($start, $end)
  {
    $this->db->select('toko.id_toko');
    $this->db->select('nama_toko');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('toko.id_toko');
    $this->db->where(array(
      "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
      "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
    ));
    return $this->db->get($this->table)->result_array();
  }

  function get_gudang_sku($start, $end)
  {
    $this->db->select('gudang.id_gudang');
    $this->db->select('nama_gudang');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('gutok_data_access', 'toko.id_toko = gutok_data_access.id_toko');
    $this->db->join('gudang', 'gudang.id_gudang = gutok_data_access.id_gudang');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('gudang.id_gudang');
    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $start,
      "date_format(created, '%Y-%m-%d') <="   => $end
    ));
    return $this->db->get($this->table)->result_array();
  }

  function get_gudang_sku_penjualan($start, $end)
  {
    $this->db->select('gudang.id_gudang');
    $this->db->select('nama_gudang');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('gutok_data_access', 'toko.id_toko = gutok_data_access.id_toko');
    $this->db->join('gudang', 'gudang.id_gudang = gutok_data_access.id_gudang');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('gudang.id_gudang');
    $this->db->where(array(
      "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
      "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
    ));
    return $this->db->get($this->table)->result_array();
  }

  function get_all_toko_list()
  {
    $this->db->order_by('nama_toko');
    $data = $this->db->get($this->table_toko);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_toko']] = $row['nama_toko'];
      }
      return $result;
    }
  }

  function get_all_gudang_list()
  {
    $this->db->order_by('nama_gudang');
    $data = $this->db->get($this->table_gudang);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_gudang']] = $row['nama_gudang'];
      }
      return $result;
    }
  }

  function get_all_toko_only()
  {
    $this->db->order_by('nama_toko');
    $data = $this->db->get($this->table_toko);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result[$row['id_toko']] = $row['nama_toko'];
      }
      return $result;
    }
  }

  function get_all_gudang_only()
  {
    $this->db->order_by('nama_gudang');
    $data = $this->db->get($this->table_gudang);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result[$row['id_gudang']] = $row['nama_gudang'];
      }
      return $result;
    }
  }

  function get_all_status_list()
  {
    $this->db->order_by('id_status_transaksi', 'asc');
    $data = $this->db->get($this->table_status);

    if ($data->num_rows() > 0) {
      foreach ($data->result_array() as $row) {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_status_transaksi']] = $row['nama_status_transaksi'];
      }
      return $result;
    }
  }

  public function get_id_barang($id_barang)
  {
    $query = $this->db->get_where('tbl_barang', array('id_barang' => $id_barang));
    return $query->row_array();
  }

  public function get_id_toko($id)
  {
    $this->db->order_by('nama_produk', 'asc');
    $this->db->join($this->table_tokpro, 'tokpro_data_access.id_toko = toko.id_toko');
    $this->db->join($this->table_produk, 'tokpro_data_access.id_produk = produk.id_produk');
    $this->db->join($this->table_sku, 'sku.id_sku = produk.id_sku');
    $query = $this->db->get_where($this->table_toko, array('toko.id_toko' => $id));
    return $query->result_object();
  }

  public function get_id_produk($id)
  {
    $query = $this->db->get_where($this->table_produk, array('id_produk' => $id));
    return $query->row_array();
  }

  public function cari_nomor($nomor)
  {
    $this->db->order_by('nomor_pesanan', $this->order);
    $this->db->like('nomor_pesanan', $nomor, 'BOTH');
    return $this->db->get($this->table)->row();
  }

  function get_all()
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    return $this->db->get($this->table)->result();
  }

  function get_all_by_periodik($first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');

    $this->db->where(array(
      "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
      "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
    ));
    return $this->db->get($this->table)->result();
  }

  function get_all_by_periodik_impor($first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');

    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $first,
      "date_format(created, '%Y-%m-%d') <="   => $last
    ));
    return $this->db->get($this->table)->result();
  }

  function get_all_by_periodik_sinkron($trigger, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');

    if ($trigger == 'impor') {
      $this->db->where(array(
        "date_format(created, '%Y-%m-%d') >="   => $first,
        "date_format(created, '%Y-%m-%d') <="   => $last
      ));
    } else if ($trigger == 'penjualan') {
      $this->db->where(array(
        "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
        "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
      ));
    }
    return $this->db->get($this->table)->result();
  }

  function get_datatable($status, $kurir, $toko, $resi, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir);
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko);
    }
    if ($resi == '' || $resi == NULL) {
      $this->db->where('nomor_resi', '');
    }
    if ($status != 'semua') {
      $this->db->where('penjualan.id_status_transaksi', $status);
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    // ));
    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $first,
      "date_format(created, '%Y-%m-%d') <="   => $last
    ));
    return $this->db->get($this->table)->result();
  }

  function get_datatable_all($trigger, $status, $kurir, $toko, $resi, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir);
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko);
    }
    if ($resi == '' || $resi == NULL || $resi == 'null') {
      $this->db->where('nomor_resi', '');
    }
    if ($status != 'semua') {
      $this->db->where('penjualan.id_status_transaksi', $status);
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    // ));
    if ($trigger == 'impor') {
      $this->db->where(array(
        "date_format(created, '%Y-%m-%d') >="   => $first,
        "date_format(created, '%Y-%m-%d') <="   => $last
      ));
    } else if ($trigger == 'penjualan') {
      $this->db->where(array(
        "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
        "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
      ));
    }

    return $this->db->get($this->table)->result();
  }

  function get_datatable_customer_insight($first, $last, $provinsi, $kabupaten, $belanja_min, $belanja_max, $qty_min, $qty_max, $columnName, $columnSortOrder, $searchValue)
  {
    $this->db->select('*, SUM(QTY) as total_qty, COUNT(penjualan.nomor_pesanan) as jumlah_pesanan, SUM(harga_jual) as total_harga_jual, MAX(tgl_penjualan) as tgl_terakhir_order');
    $this->db->join('detail_penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');

    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    // ));
    if ($provinsi != '') {
      $this->db->where('penjualan.provinsi', $provinsi);
    }

    if ($kabupaten != 0 && $kabupaten != '') {
      $this->db->where('penjualan.kabupaten', $kabupaten);
    }

    if ($belanja_min != '') {
      $this->db->having('total_harga_jual >=', $belanja_min);
    }

    if ($belanja_max != '') {
      $this->db->having('total_harga_jual <=', $belanja_max);
    }

    if ($qty_min != '') {
      $this->db->having('total_qty >=', $qty_min);
    }

    if ($qty_max != '') {
      $this->db->having('total_qty <=', $qty_max);
    }

    if ($searchValue != '') {
      $this->db->like('nama_penerima', $searchValue, 'both');
      $this->db->or_like('hp_penerima', $searchValue, 'both');
    }
    $this->db->order_by($columnName, $columnSortOrder);

    $this->db->where(array(
      "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
      "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
    ));

    $start = isset($_GET['start']) && $_GET['start'] != null ? $_GET['start'] : 0;
    $length = isset($_GET['length']) && $_GET['length'] != null ? $_GET['length'] : 50;

    $this->db->group_by('nama_penerima, hp_penerima');
    $this->db->limit($length, $start);
    $this->db->order_by('total_qty', 'desc');

    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($trigger, $status, $kurir, $toko, $resi, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->select('COUNT(nomor_pesanan) as "total"');
    $this->db->select('COUNT(CASE WHEN penjualan.id_status_transaksi = 1 THEN 1 END) as "pending"');
    $this->db->select('COUNT(CASE WHEN penjualan.id_status_transaksi = 2 THEN 1 END) as "transfer"');
    $this->db->select('COUNT(CASE WHEN penjualan.id_status_transaksi = 3 THEN 1 END) as "diterima"');
    $this->db->select('COUNT(CASE WHEN penjualan.id_status_transaksi = 4 THEN 1 END) as "retur"');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir);
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko);
    }
    if ($resi != 'semua') {
      $this->db->where('nomor_resi', $resi);
    }
    if ($status != 'semua') {
      $this->db->where('penjualan.id_status_transaksi', $status);
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    //                         ));

    if ($trigger == 'impor') {
      $this->db->where(array(
        "date_format(created, '%Y-%m-%d') >="   => $first,
        "date_format(created, '%Y-%m-%d') <="   => $last
      ));
    } else if ($trigger == 'penjualan') {
      $this->db->where(array(
        "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
        "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
      ));
    }

    return $this->db->get($this->table)->row();
  }

  function get_all_by_id($id)
  {
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_all_detail()
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_id($id)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where('detail_penjualan.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_id_in($id)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where_in('detail_penjualan.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_produk_in($produk)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where_in('detail_penjualan.id_produk', explode(",", $produk));
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_produk_periodik($produk, $first, $last)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $first,
      "date_format(created, '%Y-%m-%d') <="   => $last,
      'detail_penjualan.id_produk'            => $produk
    ));
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_periodik($first, $last)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->join('kurir', 'penjualan.id_kurir = kurir.id_kurir');
    $this->db->where(array(
      "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
      "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
    ));
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_periodik_gabungin($provinsi, $kotkab, $toko, $first, $last)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->join('kurir', 'penjualan.id_kurir = kurir.id_kurir');
    if ($provinsi != 'semua') {
      $this->db->where('provinsi', $provinsi);
    }

    if ($kotkab != 'semua') {
      $this->db->where('kabupaten', $kotkab);
    }

    if ($toko != 'semua') {
      $this->db->where_in('id_toko', explode(',', $toko));
    }
    $this->db->where_not_in('id_toko', array(20, 25, 26, 30, 31));
    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $first,
      "date_format(created, '%Y-%m-%d') <="   => $last
    ));
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_resi($resi)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->join('kurir', 'penjualan.id_kurir = kurir.id_kurir');
    $this->db->where('nomor_resi', $resi);
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_periodik_google_contacts($provinsi, $kotkab, $toko, $first, $last)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->join('kurir', 'penjualan.id_kurir = kurir.id_kurir');
    if ($provinsi != 'semua') {
      $this->db->where('provinsi', $provinsi);
    }

    if ($kotkab != 'semua') {
      $this->db->where('kabupaten', $kotkab);
    }

    if ($toko != 'semua') {
      $this->db->where_in('id_toko', explode(',', $toko));
    }
    $this->db->where_not_in('id_toko', array(20, 25, 26, 30, 31));
    $this->db->where(array(
      "date_format(created, '%Y-%m-%d') >="   => $first,
      "date_format(created, '%Y-%m-%d') <="   => $last
    ));
    $this->db->group_by('hp_penerima');
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_id_produk($id, $produk)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where('detail_penjualan.nomor_pesanan', $id);
    $this->db->where('detail_penjualan.id_produk', $produk);
    return $this->db->get($this->detail_table)->row();
  }

  function get_all_detail_by_id_row($id)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where('detail_penjualan.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->row();
  }

  function get_detail_by_id($id)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where('detail_penjualan.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_detail_by_id_in($id)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where_in('detail_penjualan.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_detail_by_id_row($id)
  {
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where($this->id, $id);
    return $this->db->get($this->detail_table)->row();
  }

  function get_detail_by_cust_data($nama_penerima, $hp_penerima, $first, $last)
  {
    $this->db->select('*, SUM(QTY) as total_qty');

    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where('penjualan.nama_penerima', $nama_penerima);
    $this->db->where('penjualan.hp_penerima', $hp_penerima);
    $this->db->where(array(
      "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
      "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
    ));
    $this->db->group_by('detail_penjualan.id_produk');

    return $this->db->get($this->detail_table)->result();
  }


  // function get_all_combobox()
  // {
  //   $this->db->order_by('nama_kurir');
  //   $data = $this->db->get($this->table);

  //   if($data->num_rows() > 0)
  //   {
  //     foreach($data->result_array() as $row)
  //     {
  //       $result[''] = '- Pilih Kurir Ekspedisi -';
  //       $result[$row['id_kurir']] = $row['nama_kurir'];
  //     }
  //     return $result;
  //   }
  // }

  function get_by_id($id)
  {
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function total_rows()
  {
    return $this->db->get($this->table)->num_rows();
  }

  function insert($data)
  {
    $this->db->insert($this->table, $data);
  }

  function insert_detail($data)
  {
    $this->db->insert($this->detail_table, $data);
  }

  function update($id, $data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->table, $data);
  }

  function update_detail($id, $produk, $data)
  {
    $this->db->where($this->id, $id);
    $this->db->where($this->id_produk, $produk);
    $this->db->update($this->detail_table, $data);
  }

  function update_kosong_hpp_margin($data)
  {
    $this->db->update($this->table, $data);
  }

  function update_kosong_hpp_detail($data)
  {
    $this->db->update($this->detail_table, $data);
  }

  function update_kosong_hpp_margin_by_id($id, $data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->table, $data);
  }

  function update_kosong_hpp_detail_by_id($id, $data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->detail_table, $data);
  }

  function delete($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table);
  }

  function delete_in($id)
  {
    $this->db->where_in($this->id, $id);
    $this->db->delete($this->table);
  }

  function delete_detail($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->detail_table);
  }

  function delete_detail_in($id)
  {
    $this->db->where_in($this->id, $id);
    $this->db->delete($this->detail_table);
  }
}

/* End of file Keluar_model.php */
/* Location: ./application/models/Keluar_model.php */