<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keluar_sementara_model extends CI_Model {

  public $table         = 'penjualan_sementara';
  public $id            = 'nomor_pesanan';
  public $detail_table  = 'detail_penjualan_sementara';
  public $table_kurir 	= 'kurir';
  public $id_kurir 		  = 'id_kurir';
  public $table_status  = 'status_transaksi';
  public $id_status     = 'id_status_transaksi';
  public $table_toko 	  = 'toko';
  public $id_toko 		  = 'id_toko';
  public $table_produk 	= 'produk';
  public $id_produk 	  = 'id_produk';
  public $table_tokpro 	= 'tokpro_data_access';
  public $id_tokpro 	  = 'id_tokpro_access';
  public $table_sku     = 'sku';
  public $id_sku        = 'id_sku';
  public $order         = 'DESC';

  public $column_order = array(null, 'nomor_pesanan','nama_toko', 'nama_kurir', 'nomor_resi',null); //field yang ada di table user
  public $column_search = array('nomor_pesanan','nama_toko', 'nama_kurir', 'nomor_resi'); //field yang diizin untuk pencarian 
  public $order_data = array('tgl_penjualan' => 'asc'); // default order

  // Penjualan
  private function _get_datatables_query()
    {
      $i = 0;
   
      foreach ($this->column_search as $item) // looping awal
      {
          if($_GET['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
          {
               
              if($i===0) // looping awal
              {
                  $this->db->group_start(); 
                  $this->db->like($item, $_GET['search']['value']);
              }
              else
              {
                  $this->db->or_like($item, $_GET['search']['value']);
              }

              if(count($this->column_search) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order))
      {
          $order = $this->order;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->order_by('tgl_penjualan', 'desc');
      $this->db->select('*');
      $this->db->select('date_format(tgl_penjualan, "%d-%m-%Y") as tanggal');
      $this->db->join('users', 'users.id_users = penjualan_sementara.id_users');
      $this->db->join('toko', 'toko.id_toko = penjualan_sementara.id_toko');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan_sementara.id_kurir', 'left');
      $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan_sementara.id_status_transaksi');
      $this->db->from($this->table);

      if ($_GET['kurir'] != 'semua') {
        $this->db->where('penjualan_sementara.id_kurir', $_GET['kurir']); 
      }
      if ($_GET['toko'] != 'semua') {
        $this->db->where('penjualan_sementara.id_toko', $_GET['toko']); 
      }
      if ($_GET['resi'] == '' || $_GET['resi'] == NULL) {
        $this->db->where('nomor_resi', ''); 
      }
      if ($_GET['status'] != 'semua') {
        $this->db->where('penjualan_sementara.id_status_transaksi', $_GET['status']); 
      }
      // $this->db->where( array(  "tgl_penjualan >="   => $first,
      //                           "tgl_penjualan <="   => $last
                              // ));
      $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $start,
                                "date_format(created, '%Y-%m-%d') <="   => $end
      ));
  }

  function get_datatables()
  {
      $this->_get_datatables_query();
      if($_GET['length'] != -1)
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

  function get_all()
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan_sementara.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan_sementara.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan_sementara.id_kurir');
    return $this->db->get($this->table)->result();
  }

  function get_all_kurir()
  {
    $this->db->order_by('nama_kurir');
    $data = $this->db->get($this->table_kurir);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
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

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
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

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Status -';
        $result[$row['id_status_transaksi']] = $row['nama_status_transaksi'];
      }
      return $result;
    }
  }

  function get_all_by_periodik($first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan_sementara.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan_sementara.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan_sementara.id_kurir');

    $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_all_by_periodik_impor($first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan_sementara.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan_sementara.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan_sementara.id_kurir');

    $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
                              "date_format(created, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_datatable_all($status, $kurir, $toko, $resi, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan_sementara.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan_sementara.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan_sementara.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan_sementara.id_status_transaksi');
    $this->db->join('detail_penjualan_sementara', 'detail_penjualan_sementara.nomor_pesanan = penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    if ($kurir != 'semua') {
      $this->db->where('penjualan_sementara.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan_sementara.id_toko', $toko); 
    }
    if ($resi == '' || $resi == NULL) {
      $this->db->where('nomor_resi', ''); 
    }
    if ($status != 'semua') {
      $this->db->where('penjualan_sementara.id_status_transaksi', $status); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
                            // ));
    $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
                              "date_format(created, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($status, $kurir, $toko, $resi, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->select('COUNT(nomor_pesanan) as "total"');
    $this->db->select('COUNT(CASE WHEN penjualan_sementara.id_status_transaksi = 1 THEN 1 END) as "pending"');
    $this->db->select('COUNT(CASE WHEN penjualan_sementara.id_status_transaksi = 2 THEN 1 END) as "transfer"');
    $this->db->select('COUNT(CASE WHEN penjualan_sementara.id_status_transaksi = 3 THEN 1 END) as "diterima"');
    $this->db->select('COUNT(CASE WHEN penjualan_sementara.id_status_transaksi = 4 THEN 1 END) as "retur"');
    $this->db->join('users', 'users.id_users = penjualan_sementara.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan_sementara.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan_sementara.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan_sementara.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan_sementara.id_toko', $toko); 
    }
    if ($resi != 'semua') {
      $this->db->where('nomor_resi', $resi); 
    }
    if ($status != 'semua') {
      $this->db->where('penjualan_sementara.id_status_transaksi', $status); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
                              "date_format(created, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_all_by_id($id)
  {
    $this->db->join('users', 'users.id_users = penjualan_sementara.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan_sementara.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan_sementara.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan_sementara.id_status_transaksi');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_all_detail()
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_id($id)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where('detail_penjualan_sementara.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_id_in($id)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where_in('detail_penjualan_sementara.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_produk_in($produk)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where_in('detail_penjualan_sementara.id_produk', explode(",", $produk));
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_produk_periodik($produk, $first, $last)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
                              "date_format(created, '%Y-%m-%d') <="   => $last,
                              'detail_penjualan_sementara.id_produk'            => $produk
                            ));
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_periodik($first, $last)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->join('kurir', 'penjualan_sementara.id_kurir = kurir.id_kurir');
    $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_periodik_gabungin($first, $last)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->join('kurir', 'penjualan_sementara.id_kurir = kurir.id_kurir');
    $this->db->where_not_in('id_toko', array(20, 26, 30, 31));
    $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_periodik_google_contacts($first, $last)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->join('kurir', 'penjualan_sementara.id_kurir = kurir.id_kurir');
    $this->db->where_not_in('id_toko', array(20, 26, 30, 31));
    $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
                            ));
    $this->db->group_by('hp_penerima');
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_id_produk($id, $produk)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where('detail_penjualan_sementara.nomor_pesanan', $id);
    $this->db->where('detail_penjualan_sementara.id_produk', $produk);
    return $this->db->get($this->detail_table)->row();
  }

  function get_all_detail_by_id_row($id)
  {
    $this->db->join('penjualan_sementara', 'penjualan_sementara.nomor_pesanan = detail_penjualan_sementara.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where('detail_penjualan_sementara.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->row();
  }

  function get_detail_by_id($id)
  {
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where($this->id, $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_detail_by_id_in($id)
  {
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where_in($this->id, $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_detail_by_id_row($id)
  {
    $this->db->join('produk', 'produk.id_produk = detail_penjualan_sementara.id_produk');
    $this->db->where($this->id, $id);
    return $this->db->get($this->detail_table)->row();
  }

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

  function update($id,$data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->table, $data);
  }

  function update_detail($id,$produk,$data)
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

  function deleteAll()
  {
    $this->db->empty_table($this->detail_table);
    $this->db->empty_table($this->table);
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

/* End of file Keluar_sementara_model.php */
/* Location: ./application/models/Keluar_sementara_model.php */