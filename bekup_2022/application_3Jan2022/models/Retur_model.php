<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_model extends CI_Model {

  public $table 			= 'retur';
  public $id    			= 'nomor_retur';
  public $table_produk_retur  = 'produk_retur';
  public $id_produk_retur      = 'id_produk_retur';
  public $table_riwayat_retur  = 'riwayat_retur';
  public $id_riwayat_retur      = 'id_riwayat_retur';
  public $table_penjualan	= 'penjualan';
  public $id_penjualan		= 'nomor_pesanan';
  public $table_resi		= 'resi';
  public $id_resi			= 'nomor_resi';
  public $table_kurir 		= 'kurir';
  public $id_kurir 			= 'id_kurir';
  public $table_status  	= 'status_transaksi';
  public $id_status     	= 'id_status_transaksi';
  public $table_toko 	 	= 'toko';
  public $id_toko 		  	= 'id_toko';
  public $table_produk 		= 'produk';
  public $id_produk 	  	= 'id_produk';
  public $table_sku     	= 'sku';
  public $id_sku        	= 'id_sku';
  public $order 			= 'DESC';

  public $column_order = array(null,'nomor_retur', 'retur.nomor_pesanan', 'nomor_resi', null); //field yang ada di table user
  public $column_search = array('retur.nomor_pesanan','nomor_retur', 'nomor_resi'); //field yang diizin untuk pencarian 
  public $column_order_produk = array(null, 'retur.nomor_retur', 'nama_produk', 'qty_retur', 'keterangan_retur', null); //field yang ada di table user
  public $column_search_produk = array('retur.nomor_pesanan','retur.nomor_retur', 'nama_produk', 'keterangan_retur'); //field yang diizin untuk pencarian
  public $column_order_riwayat = array(null, 'retur.nomor_retur', 'nama_produk','updated_qty_produk', 'updated_qty_retur', null); //field yang ada di table user
  public $column_search_riwayat = array('retur.nomor_retur', 'nama_produk','updated_qty_produk', 'updated_qty_retur'); //field yang diizin untuk pencarian 
  public $order_data = array('tgl_retur' => 'asc'); // default order 

  // Table Server Side
  // Retur
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
      else if(isset($this->order_data))
      {
          $order = $this->order_data;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->order_by('tgl_penjualan', 'desc');
      $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
      $this->db->join('users', 'users.id_users = penjualan.id_users');
      $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
      $this->db->from($this->table);  

      if ($_GET['kurir'] != 'semua') {
        $this->db->where('penjualan.id_kurir', $_GET['kurir']); 
      }
      if ($_GET['toko'] != 'semua') {
        $this->db->where('penjualan.id_toko', $_GET['toko']); 
      }
      if ($_GET['status'] != 'semua') {
        $this->db->where('status_retur', $_GET['status']); 
      }
      if ($_GET['followup'] != 'semua') {
        $this->db->where('status_follow_up', $_GET['followup']); 
      }
      // $this->db->where( array(  "tgl_penjualan >="   => $first,
      //                           "tgl_penjualan <="   => $last
                              // ));
      $this->db->where( array(  "date_format(tgl_retur, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_retur, '%Y-%m-%d') <="   => $end
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

  // Produk Retur
  private function _get_datatables_query_produk()
    {
      $i = 0;
   
      foreach ($this->column_search_produk as $item) // looping awal
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

              if(count($this->column_search_produk) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order_produk[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_data))
      {
          $order = $this->order_data;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->order_by('tgl_penjualan', 'desc');
      $this->db->join('retur', 'retur.nomor_retur = produk_retur.nomor_retur');
      $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
      $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
      $this->db->join('produk', 'produk.id_produk = produk_retur.id_produk');
      $this->db->from($this->table_produk_retur);  

      if ($_GET['kurir'] != 'semua') {
        $this->db->where('penjualan.id_kurir', $_GET['kurir']); 
      }
      if ($_GET['toko'] != 'semua') {
        $this->db->where('penjualan.id_toko', $_GET['toko']); 
      }
      $this->db->where_not_in('qty_retur', 0);

      $this->db->where( array(  "date_format(tgl_retur, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_retur, '%Y-%m-%d') <="   => $end
                              ));
  }

  function get_datatables_produk()
  {
      $this->_get_datatables_query_produk();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_produk()
  {
      $this->_get_datatables_query_produk();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_produk()
  {
      $this->db->from($this->table_produk_retur);
      return $this->db->count_all_results();
  }

  // Riwayat Retur
  private function _get_datatables_query_riwayat()
    {
      $i = 0;
   
      foreach ($this->column_search_riwayat as $item) // looping awal
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

              if(count($this->column_search_riwayat) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order_riwayat[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_data))
      {
          $order = $this->order_data;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->order_by('tgl_penjualan', 'desc');
      $this->db->join('produk_retur', 'produk_retur.id_produk_retur = riwayat_retur.id_produk_retur');
      $this->db->join('retur', 'retur.nomor_retur = produk_retur.nomor_retur');
      $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
      $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
      $this->db->join('produk', 'produk.id_produk = produk_retur.id_produk');
      $this->db->from($this->table_riwayat_retur);  

      if ($_GET['kurir'] != 'semua') {
        $this->db->where('penjualan.id_kurir', $_GET['kurir']); 
      }
      if ($_GET['toko'] != 'semua') {
        $this->db->where('penjualan.id_toko', $_GET['toko']); 
      }

      $this->db->where( array(  "date_format(tgl_riwayat_retur, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_riwayat_retur, '%Y-%m-%d') <="   => $end
      ));
  }

  function get_datatables_riwayat()
  {
      $this->_get_datatables_query_riwayat();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_riwayat()
  {
      $this->_get_datatables_query_riwayat();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_riwayat()
  {
      $this->db->from($this->table_riwayat_retur);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  // function get_all_combobox()
  // {
  //   $this->db->order_by('testing_name');
  //   $data = $this->db->get($this->table);

  //   if($data->num_rows() > 0)
  //   {
  //     foreach($data->result_array() as $row)
  //     {
  //       $result[''] = '- Please Choose Testing';
  //       $result[$row['id_testing']] = $row['testing_name'];
  //     }
  //     return $result;
  //   }
  // }
  function get_cek_resi_all_by_resi($id)
  {
  	$this->db->where('penjualan.nomor_resi', $id);
  	$this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
  	$this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
  	$this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
  	return $this->db->get($this->table_penjualan)->row();
  }

  function get_all_by_retur($id)
  {
    $this->db->where($this->id, $id);
    $this->db->join($this->table_penjualan, 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
    $this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
    return $this->db->get($this->table)->row();
  }

  function get_produk_retur_by_id($id)
  {
    $this->db->where($this->id_produk_retur, $id);
    $this->db->join($this->table, 'produk_retur.nomor_retur = retur.nomor_retur');
    $this->db->join($this->table_penjualan, 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
    $this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
    $this->db->join($this->table_produk, 'produk.id_produk = produk_retur.id_produk');
    return $this->db->get($this->table_produk_retur)->row();
  }

  function get_datatable($status, $kurir, $toko, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');  
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko); 
    }
    if ($status != 'semua') {
      $this->db->where('status_retur', $status); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
                            // ));
    $this->db->where( array(  "date_format(tgl_retur, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_retur, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($status, $followup, $kurir, $toko, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->select('COUNT(nomor_retur) as "total"');
    $this->db->select('COUNT(CASE WHEN status_retur = 0 THEN 1 END) as "diproses"');
    $this->db->select('COUNT(CASE WHEN status_retur = 1 THEN 1 END) as "sudah"');
    $this->db->select('COUNT(CASE WHEN status_follow_up = 0 THEN 1 END) as "belum_fu"');
    $this->db->select('COUNT(CASE WHEN status_follow_up = 1 THEN 1 END) as "sudah_fu"');
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko); 
    }
    if ($status != 'semua') {
      $this->db->where('status_retur', $status); 
    }
    if ($followup != 'semua') {
      $this->db->where('status_follow_up', $followup); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_retur, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_retur, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_dasbor_list_produk($kurir, $toko, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->select('COUNT(id_produk_retur) as "total"');
    $this->db->select('SUM(qty_retur) as "total_produk"');
    $this->db->join('retur', 'retur.nomor_retur = produk_retur.nomor_retur');
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko); 
    }

    $this->db->where( array(  "date_format(tgl_retur, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_retur, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table_produk_retur)->row();
  }

  function get_dasbor_list_riwayat($kurir, $toko, $start, $end)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->select('COUNT(id_riwayat_retur) as "total"');
    $this->db->join('produk_retur', 'produk_retur.id_produk_retur = riwayat_retur.id_produk_retur');
    $this->db->join('retur', 'retur.nomor_retur = produk_retur.nomor_retur');
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
    $this->db->join('produk', 'produk.id_produk = produk_retur.id_produk');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko); 
    }

    $this->db->where( array(  "date_format(tgl_riwayat_retur, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_riwayat_retur, '%Y-%m-%d') <="   => $end
    ));
    return $this->db->get($this->table_riwayat_retur)->row();
  }

  function get_by_id($id)
  {
    $this->db->where($this->id, $id);
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
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

  function insert_produk_retur($data)
  {
    $this->db->insert($this->table_produk_retur, $data);
  }

  function insert_riwayat_retur($data)
  {
    $this->db->insert($this->table_riwayat_retur, $data);
  }

  function import($data)
  {
    $this->db->replace($this->table, $data);
  }

  function update($id,$data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->table, $data);
  }

  function update_stok_aktif($id,$data)
  {
    $this->db->where($this->id_produk_retur, $id);
    $this->db->update($this->table_produk_retur, $data);
  }

  function delete($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table);
  }

  public function cari_nomor($nomor)
  {
    $this->db->order_by('nomor_retur', $this->order);
    $this->db->like('nomor_retur', $nomor, 'BOTH');
    return $this->db->get($this->table)->row();
  }
	

}

/* End of file Retur_model.php */
/* Location: ./application/models/Retur_model.php */