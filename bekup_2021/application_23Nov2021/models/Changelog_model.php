<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Changelog_model extends CI_Model{

  public $table = 'changelog_query';
  public $id    = 'id';
  public $order = 'DESC';

  public $table2 = 'changelog_app';
  public $id2    = 'id';
  public $order2 = 'DESC';

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


}
