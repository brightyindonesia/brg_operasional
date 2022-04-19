<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Changelog_model extends CI_Model{

  public $table = 'changelog_query';
  public $id    = 'id';
  public $order = 'DESC';

  public $table2 = 'changelog_app';
  public $id2    = 'id';
  public $order2 = 'DESC';

  public $column_order = array(null, 'created_at', 'content', 'created_by', 'ip_address', 'user_agent'); //field yang ada di table user
  public $column_search = array('created_at', 'content', 'created_by', 'ip_address', 'user_agent'); //field yang diizin untuk pencarian 
  public $order_data = array('created_at' => 'desc'); // default order

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

      $this->db->order_by('created_at', 'desc');
      $this->db->select('*');
      $this->db->from($this->table);

      if ($_GET['users'] != 'semua') {
        $this->db->where('created_by', $_GET['users']);
      }

      $this->db->where( array(  "date_format(created_at, '%Y-%m-%d') >="   => $start,
                                "date_format(created_at, '%Y-%m-%d') <="   => $end
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

  function get_all_users_list()
  {
    $this->db->distinct('created_by');
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['created_by']] = $row['created_by'];
      }
      return $result;
    }else{
      $result['semua'] = '- Semua Data -';
    }
  }

  function get_all_log_query()
  {
    $this->db->order_by($this->id, $this->order);
    return $this->db->get($this->table)->result();
  }

  function total_rows_log_query()
  {
    return $this->db->get($this->table)->num_rows();
  }

  function get_all_log_app()
  {
    $this->db->order_by($this->id2, $this->order2);
    return $this->db->get($this->table2)->result();
  }

  function total_rows_log_app()
  {
    return $this->db->get($this->table)->num_rows();
  }

  function insert_applog($data)
  {
    $this->db->insert($this->table2, $data);
  }

  function delete_log_query()
  {
    $this->db->empty_table($this->table);
  }


}
