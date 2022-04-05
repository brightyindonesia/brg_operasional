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

  public $column_order_provinsi = array(null, 'nama_provinsi',null,null); //field yang ada di table user
  public $column_search_provinsi = array('nama_provinsi','nama_kotkab', 'keys_kotkab'); //field yang diizin untuk pencarian 
  public $order_provinsi = array('nama_provinsi' => 'asc'); // default order

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
}

/* End of file Keyword_model.php */
/* Location: ./application/models/Keyword_model.php */