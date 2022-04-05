<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keyword_model extends CI_Model {
  
  // Provinsi
  public $table_provinsi = 'keyword_provinsi';
  public $id_provinsi    = 'id_keyword_provinsi';
  
  // Detail Provinsi
  public $table_detail_provinsi = 'detail_keyword_provinsi';
  public $table_detail_kotkab = 'detail_keyword_kotkab';
  public $id_detail_provinsi    = 'id_detail_keyword_provinsi';

  // Produk
  public $table_produk = 'keyword_produk';
  public $id_produk    = 'id_keyword_produk';
  
  // Detail Produk
  public $table_detail_produk = 'detail_keyword_produk';

  // Toko
  public $table_toko = 'keyword_toko';
  public $id_toko    = 'id_keyword_toko';
  
  // Detail Toko
  public $table_detail_toko = 'detail_keyword_toko';

  // Kurir
  public $table_kurir = 'keyword_kurir';
  public $id_kurir    = 'id_keyword_kurir';
  
  // Detail Kurir
  public $table_detail_kurir = 'detail_keyword_kurir';

  public $column_order_provinsi = array(null, 'nama_provinsi',null,null); //field yang ada di table user
  public $column_search_provinsi = array('nama_provinsi','nama_kotkab', 'keys_kotkab'); //field yang diizin untuk pencarian 
  public $order_provinsi = array('nama_provinsi' => 'asc'); // default order

  public $column_order_produk = array(null, 'nama_produk',null); //field yang ada di table user
  public $column_search_produk = array('nama_produk', 'keys_produk'); //field yang diizin untuk pencarian 
  public $order_produk = array('nama_produk' => 'asc'); // default order

  public $column_order_toko = array(null, 'nama_toko',null); //field yang ada di table user
  public $column_search_toko = array('nama_toko', 'keys_toko'); //field yang diizin untuk pencarian 
  public $order_toko = array('nama_toko' => 'asc'); // default order

  public $column_order_kurir = array(null, 'nama_kurir',null); //field yang ada di table user
  public $column_search_kurir = array('nama_kurir', 'keys_kurir'); //field yang diizin untuk pencarian 
  public $order_kurir = array('nama_kurir' => 'asc'); // default order

  // Provinsi
  private function _get_datatables_query_provinsi()
    {
      $i = 0;
   
      foreach ($this->column_search_provinsi as $item) // looping awal
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

              if(count($this->column_search_provinsi) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order_provinsi[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_provinsi))
      {
          $order = $this->order_provinsi;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $this->db->order_by('nama_provinsi', 'asc');
      $this->db->select('*');
      $this->db->join($this->table_detail_provinsi, 'detail_keyword_provinsi.id_keyword_provinsi = keyword_provinsi.id_keyword_provinsi');
      $this->db->join($this->table_detail_kotkab, 'detail_keyword_provinsi.id_detail_keyword_provinsi = detail_keyword_kotkab.id_detail_keyword_provinsi', 'left');
      $this->db->group_by('nama_provinsi');
      $this->db->from($this->table_provinsi);
  }

  function get_datatables_provinsi()
  {
      $this->_get_datatables_query_provinsi();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_provinsi()
  {
      $this->_get_datatables_query_provinsi();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_provinsi()
  {
      $this->db->from($this->table_provinsi);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_all_provinsi()
  {
    return $this->db->get($this->table_provinsi)->result();
  }

  function get_all_detail_provinsi_provinsi()
  {
    $this->db->join($this->table_detail_provinsi, 'detail_keyword_provinsi.id_keyword_provinsi = keyword_provinsi.id_keyword_provinsi');
    return $this->db->get($this->table_provinsi)->result();
  }

  function get_detail_provinsi_by_id_provinsi($id)
  {
  	$this->db->where($this->id_provinsi, $id);
    return $this->db->get($this->table_detail_provinsi)->result();
  }

  function get_detail_provinsi_by_id_detail_provinsi_row($id)
  {
  	$this->db->where($this->id_detail_provinsi, $id);
    return $this->db->get($this->table_detail_provinsi)->row();
  }

  function get_detail_provinsi_by_id_provinsi_in($id)
  {
  	$this->db->join($this->table_detail_provinsi, 'detail_keyword_provinsi.id_keyword_provinsi = keyword_provinsi.id_keyword_provinsi');
  	$this->db->where_in('keyword_provinsi.id_keyword_provinsi', explode(",", $id));
    return $this->db->get($this->table_provinsi)->result();
  }

  function get_keys_detail_kotkab_by_id_detail_provinsi($id)
  {
  	$this->db->select('keys_kotkab');
  	$this->db->where($this->id_detail_provinsi, $id);
    return $this->db->get($this->table_detail_kotkab)->result();
  }

  function get_keys_detail_kotkab_by_keys_kotkab($id)
  {
    $this->db->select('keys_kotkab');
    $this->db->where('keys_kotkab', $id);
    return $this->db->get($this->table_detail_kotkab)->result();
  }

  function get_keys_detail_kotkab_by_keys_kotkab_not_in($id, $keys)
  {
    $this->db->select('keys_kotkab');
    $this->db->where('keys_kotkab', $keys);
    $this->db->where_not_in('id_detail_keyword_provinsi', $id);
    return $this->db->get($this->table_detail_kotkab)->result();
  }

  function get_keys_detail_kotkab_by_keys_kotkab_row($id)
  {
    $this->db->select('keys_kotkab');
    $this->db->where('keys_kotkab', $id);
    return $this->db->get($this->table_detail_kotkab)->row();
  }

  function get_detail_provinsi_kotkab_by_id_detail_provinsi($id)
  {
  	$this->db->join($this->table_detail_provinsi, 'detail_keyword_provinsi.id_detail_keyword_provinsi = detail_keyword_kotkab.id_detail_keyword_provinsi');
  	$this->db->where('detail_keyword_provinsi.id_detail_keyword_provinsi', $id);
    return $this->db->get($this->table_detail_kotkab)->result();
  }

  function get_detail_provinsi_by_provinsi($id)
  {
  	$this->db->select('nama_kotkab');
  	$this->db->where($this->id_provinsi, $id);
    return $this->db->get($this->table_detail_provinsi)->result();
  }

  function get_detail_provinsi_last()
  {
  	$this->db->order_by($this->id_detail_provinsi, 'desc');
    return $this->db->get($this->table_detail_provinsi)->row();
  }

  function get_provinsi_by_id($id)
  {
    $this->db->where($this->id_provinsi, $id);
    return $this->db->get($this->table_provinsi)->row();
  }

  function total_rows_provinsi()
  {
    return $this->db->get($this->table_provinsi)->num_rows();
  }

  function total_rows_detail_kotkab()
  {
    return $this->db->get($this->table_detail_provinsi)->num_rows();
  }

  function total_rows_detail_provinsi()
  {
    return $this->db->get($this->table_detail_kotkab)->num_rows();
  }

  function insert_provinsi($data)
  {
    $this->db->insert($this->table_provinsi, $data);
  }

  function insert_detail_provinsi($data)
  {
    $this->db->insert($this->table_detail_provinsi, $data);
  }

  function insert_detail_kotkab($data)
  {
    $this->db->insert($this->table_detail_kotkab, $data);
  }

  function import_provinsi($data)
  {
    $this->db->replace($this->table_provinsi, $data);
  }

  function import_detail_provinsi($data)
  {
    $this->db->replace($this->table_detail_provinsi, $data);
  }

  function import_detail_kotkab($data)
  {
    $this->db->replace($this->table_detail_kotkab, $data);
  }

  function update_provinsi($id,$data)
  {
    $this->db->where($this->id_provinsi, $id);
    $this->db->update($this->table_provinsi, $data);
  }

  function update_detail_provinsi($id,$data)
  {
    $this->db->where($this->id_detail_provinsi, $id);
    $this->db->update($this->table_detail_provinsi, $data);
  }

  function delete_provinsi($id)
  {
    $this->db->where($this->id_provinsi, $id);
    $this->db->delete($this->table_provinsi);
  }

  function delete_provinsi_in($id)
  {
    $this->db->where_in($this->id_provinsi, explode(",", $id));
    $this->db->delete($this->table_provinsi);
  }

  function delete_detail_provinsi_in_by_id_provinsi($id)
  {
    $this->db->where_in($this->id_provinsi, explode(",", $id));
    $this->db->delete($this->table_detail_provinsi);
  }

  function delete_detail_provinsi_by_id_detail_provinsi($id)
  {
    $this->db->where($this->id_detail_provinsi, $id);
    $this->db->delete($this->table_detail_provinsi);
  }

  function delete_detail_provinsi_by_id_provinsi($id)
  {
    $this->db->where($this->id_provinsi, $id);
    $this->db->delete($this->table_detail_provinsi);
  }

  function delete_detail_kotkab_by_id_detail_provinsi($id)
  {
    $this->db->where($this->id_detail_provinsi, $id);
    $this->db->delete($this->table_detail_kotkab);
  }

  function deleteAll_detail_kotkab()
  {
    $this->db->empty_table($this->table_detail_kotkab);
  }

  // Produk
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
      else if(isset($this->order_produk))
      {
          $order = $this->order_produk;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $this->db->order_by('nama_produk', 'asc');
      $this->db->select('*');
      $this->db->join('produk', 'keyword_produk.id_produk = produk.id_produk');
      $this->db->group_by('nama_produk');
      $this->db->from($this->table_produk);
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
      $this->db->from($this->table_produk);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_all_produk()
  {
    return $this->db->get($this->table_produk)->result();
  }

  function get_all_detail_produk_produk()
  {
    $this->db->join($this->table_detail_produk, 'detail_keyword_produk.id_keyword_produk = keyword_produk.id_keyword_produk');
    $this->db->join('produk', 'produk.id_produk = keyword_produk.id_produk');
    $this->db->group_by('keyword_produk.id_keyword_produk');
    return $this->db->get($this->table_produk)->result();
  }

  function get_detail_produk_by_id_produk($id)
  {
    $this->db->where($this->id_produk, $id);
    return $this->db->get($this->table_detail_produk)->result();
  }

  function get_detail_produk_by_id_produk_in($id)
  {
    $this->db->join($this->table_detail_produk, 'detail_keyword_produk.id_keyword_produk = keyword_produk.id_keyword_produk');
    $this->db->where_in('keyword_produk.id_keyword_produk', explode(",", $id));
    return $this->db->get($this->table_produk)->result();
  }

  function get_keys_produk_by_id_produk($id)
  {
    $this->db->select('keys_produk');
    $this->db->where($this->id_produk, $id);
    return $this->db->get($this->table_detail_produk)->result();
  }

  function get_keys_produk_by_keys_produk($id)
  {
    $this->db->select('keys_produk');
    $this->db->where('keys_produk', $id);
    return $this->db->get($this->table_detail_produk)->result();
  }

  function get_keys_produk_by_keys_produk_not_in($id, $keys)
  {
    $this->db->select('keys_produk');
    $this->db->where('keys_produk', $keys);
    $this->db->where_not_in('id_keyword_produk', $id);
    return $this->db->get($this->table_detail_produk)->result();
  }

  function get_keys_produk_by_keys_produk_row($id)
  {
    $this->db->select('keys_produk');
    $this->db->where('keys_produk', $id);
    return $this->db->get($this->table_detail_produk)->row();
  }

  function get_produk_last()
  {
    $this->db->order_by($this->id_produk, 'desc');
    return $this->db->get($this->table_produk)->row();
  }

  function get_produk_by_id($id)
  {
    $this->db->where($this->id_produk, $id);
    return $this->db->get($this->table_produk)->row();
  }

  function total_rows_produk()
  {
    return $this->db->get($this->table_produk)->num_rows();
  }

  function total_rows_detail_produk()
  {
    return $this->db->get($this->table_detail_produk)->num_rows();
  }

  function insert_produk($data)
  {
    $this->db->insert($this->table_produk, $data);
  }

  function insert_detail_produk($data)
  {
    $this->db->insert($this->table_detail_produk, $data);
  }


  function import_produk($data)
  {
    $this->db->replace($this->table_produk, $data);
  }

  function import_detail_produk($data)
  {
    $this->db->replace($this->table_detail_produk, $data);
  }

  function update_produk($id,$data)
  {
    $this->db->where($this->id_produk, $id);
    $this->db->update($this->table_produk, $data);
  }

  function delete_produk($id)
  {
    $this->db->where($this->id_produk, $id);
    $this->db->delete($this->table_produk);
  }

  function delete_produk_in($id)
  {
    $this->db->where_in($this->id_produk, explode(",", $id));
    $this->db->delete($this->table_produk);
  }

  function delete_detail_produk_in_by_id_produk($id)
  {
    $this->db->where_in($this->id_produk, explode(",", $id));
    $this->db->delete($this->table_detail_produk);
  }

  function delete_detail_produk_by_id_produk($id)
  {
    $this->db->where($this->id_produk, $id);
    $this->db->delete($this->table_detail_produk);
  }

  function deleteAll_detail_produk()
  {
    $this->db->empty_table($this->table_detail_produk);
  }

  // Toko
  private function _get_datatables_query_toko()
    {
      $i = 0;
   
      foreach ($this->column_search_toko as $item) // looping awal
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

              if(count($this->column_search_toko) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order_toko[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_toko))
      {
          $order = $this->order_toko;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $this->db->order_by('nama_toko', 'asc');
      $this->db->select('*');
      $this->db->join('toko', 'keyword_toko.id_toko = toko.id_toko');
      $this->db->group_by('nama_toko');
      $this->db->from($this->table_toko);
  }

  function get_datatables_toko()
  {
      $this->_get_datatables_query_toko();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_toko()
  {
      $this->_get_datatables_query_toko();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_toko()
  {
      $this->db->from($this->table_toko);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_all_toko()
  {
    return $this->db->get($this->table_toko)->result();
  }

  function get_all_detail_toko_toko()
  {
    $this->db->join($this->table_detail_toko, 'detail_keyword_toko.id_keyword_toko = keyword_toko.id_keyword_toko');
    $this->db->join('toko', 'toko.id_toko = keyword_toko.id_toko');
    $this->db->group_by('keyword_toko.id_keyword_toko');
    return $this->db->get($this->table_toko)->result();
  }

  function get_detail_toko_by_id_toko($id)
  {
    $this->db->where($this->id_toko, $id);
    return $this->db->get($this->table_detail_toko)->result();
  }

  function get_detail_toko_by_id_toko_in($id)
  {
    $this->db->join($this->table_detail_toko, 'detail_keyword_toko.id_keyword_toko = keyword_toko.id_keyword_toko');
    $this->db->where_in('keyword_toko.id_keyword_toko', explode(",", $id));
    return $this->db->get($this->table_toko)->result();
  }

  function get_keys_toko_by_id_toko($id)
  {
    $this->db->select('keys_toko');
    $this->db->where($this->id_toko, $id);
    return $this->db->get($this->table_detail_toko)->result();
  }

  function get_keys_toko_by_keys_toko($id)
  {
    $this->db->select('keys_toko');
    $this->db->where('keys_toko', $id);
    return $this->db->get($this->table_detail_toko)->result();
  }

  function get_keys_toko_by_keys_toko_not_in($id, $keys)
  {
    $this->db->select('keys_toko');
    $this->db->where('keys_toko', $keys);
    $this->db->where_not_in('id_keyword_toko', $id);
    return $this->db->get($this->table_detail_toko)->result();
  }

  function get_keys_toko_by_keys_toko_row($id)
  {
    $this->db->select('keys_toko');
    $this->db->where('keys_toko', $id);
    return $this->db->get($this->table_detail_toko)->row();
  }

  function get_toko_last()
  {
    $this->db->order_by($this->id_toko, 'desc');
    return $this->db->get($this->table_toko)->row();
  }

  function get_toko_by_id($id)
  {
    $this->db->where($this->id_toko, $id);
    return $this->db->get($this->table_toko)->row();
  }

  function total_rows_toko()
  {
    return $this->db->get($this->table_toko)->num_rows();
  }

  function total_rows_detail_toko()
  {
    return $this->db->get($this->table_detail_toko)->num_rows();
  }

  function insert_toko($data)
  {
    $this->db->insert($this->table_toko, $data);
  }

  function insert_detail_toko($data)
  {
    $this->db->insert($this->table_detail_toko, $data);
  }


  function import_toko($data)
  {
    $this->db->replace($this->table_toko, $data);
  }

  function import_detail_toko($data)
  {
    $this->db->replace($this->table_detail_toko, $data);
  }

  function update_toko($id,$data)
  {
    $this->db->where($this->id_toko, $id);
    $this->db->update($this->table_toko, $data);
  }

  function delete_toko($id)
  {
    $this->db->where($this->id_toko, $id);
    $this->db->delete($this->table_toko);
  }

  function delete_toko_in($id)
  {
    $this->db->where_in($this->id_toko, explode(",", $id));
    $this->db->delete($this->table_toko);
  }

  function delete_detail_toko_in_by_id_toko($id)
  {
    $this->db->where_in($this->id_toko, explode(",", $id));
    $this->db->delete($this->table_detail_toko);
  }

  function delete_detail_toko_by_id_toko($id)
  {
    $this->db->where($this->id_toko, $id);
    $this->db->delete($this->table_detail_toko);
  }

  function deleteAll_detail_toko()
  {
    $this->db->empty_table($this->table_detail_toko);
  }

  // Kurir
  private function _get_datatables_query_kurir()
    {
      $i = 0;
   
      foreach ($this->column_search_kurir as $item) // looping awal
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

              if(count($this->column_search_kurir) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order_kurir[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_kurir))
      {
          $order = $this->order_kurir;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $this->db->order_by('nama_kurir', 'asc');
      $this->db->select('*');
      $this->db->join('kurir', 'keyword_kurir.id_kurir = kurir.id_kurir');
      $this->db->group_by('nama_kurir');
      $this->db->from($this->table_kurir);
  }

  function get_datatables_kurir()
  {
      $this->_get_datatables_query_kurir();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_kurir()
  {
      $this->_get_datatables_query_kurir();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_kurir()
  {
      $this->db->from($this->table_kurir);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_all_kurir()
  {
    return $this->db->get($this->table_kurir)->result();
  }

  function get_all_detail_kurir_kurir()
  {
    $this->db->join($this->table_detail_kurir, 'detail_keyword_kurir.id_keyword_kurir = keyword_kurir.id_keyword_kurir');
    $this->db->join('kurir', 'kurir.id_kurir = keyword_kurir.id_kurir');
    $this->db->group_by('keyword_kurir.id_keyword_kurir');
    return $this->db->get($this->table_kurir)->result();
  }

  function get_detail_kurir_by_id_kurir($id)
  {
    $this->db->where($this->id_kurir, $id);
    return $this->db->get($this->table_detail_kurir)->result();
  }

  function get_detail_kurir_by_id_kurir_in($id)
  {
    $this->db->join($this->table_detail_kurir, 'detail_keyword_kurir.id_keyword_kurir = keyword_kurir.id_keyword_kurir');
    $this->db->where_in('keyword_kurir.id_keyword_kurir', explode(",", $id));
    return $this->db->get($this->table_kurir)->result();
  }

  function get_keys_kurir_by_id_kurir($id)
  {
    $this->db->select('keys_kurir');
    $this->db->where($this->id_kurir, $id);
    return $this->db->get($this->table_detail_kurir)->result();
  }

  function get_keys_kurir_by_keys_kurir($id)
  {
    $this->db->select('keys_kurir');
    $this->db->where('keys_kurir', $id);
    return $this->db->get($this->table_detail_kurir)->result();
  }

  function get_keys_kurir_by_keys_kurir_not_in($id, $keys)
  {
    $this->db->select('keys_kurir');
    $this->db->where('keys_kurir', $keys);
    $this->db->where_not_in('id_keyword_kurir', $id);
    return $this->db->get($this->table_detail_kurir)->result();
  }

  function get_keys_kurir_by_keys_kurir_row($id)
  {
    $this->db->select('keys_kurir');
    $this->db->where('keys_kurir', $id);
    return $this->db->get($this->table_detail_kurir)->row();
  }

  function get_kurir_last()
  {
    $this->db->order_by($this->id_kurir, 'desc');
    return $this->db->get($this->table_kurir)->row();
  }

  function get_kurir_by_id($id)
  {
    $this->db->where($this->id_kurir, $id);
    return $this->db->get($this->table_kurir)->row();
  }

  function total_rows_kurir()
  {
    return $this->db->get($this->table_kurir)->num_rows();
  }

  function total_rows_detail_kurir()
  {
    return $this->db->get($this->table_detail_kurir)->num_rows();
  }

  function insert_kurir($data)
  {
    $this->db->insert($this->table_kurir, $data);
  }

  function insert_detail_kurir($data)
  {
    $this->db->insert($this->table_detail_kurir, $data);
  }


  function import_kurir($data)
  {
    $this->db->replace($this->table_kurir, $data);
  }

  function import_detail_kurir($data)
  {
    $this->db->replace($this->table_detail_kurir, $data);
  }

  function update_kurir($id,$data)
  {
    $this->db->where($this->id_kurir, $id);
    $this->db->update($this->table_kurir, $data);
  }

  function delete_kurir($id)
  {
    $this->db->where($this->id_kurir, $id);
    $this->db->delete($this->table_kurir);
  }

  function delete_kurir_in($id)
  {
    $this->db->where_in($this->id_kurir, explode(",", $id));
    $this->db->delete($this->table_kurir);
  }

  function delete_detail_kurir_in_by_id_kurir($id)
  {
    $this->db->where_in($this->id_kurir, explode(",", $id));
    $this->db->delete($this->table_detail_kurir);
  }

  function delete_detail_kurir_by_id_kurir($id)
  {
    $this->db->where($this->id_kurir, $id);
    $this->db->delete($this->table_detail_kurir);
  }

  function deleteAll_detail_kurir()
  {
    $this->db->empty_table($this->table_detail_kurir);
  }
}

/* End of file Keyword_model.php */
/* Location: ./application/models/Keyword_model.php */