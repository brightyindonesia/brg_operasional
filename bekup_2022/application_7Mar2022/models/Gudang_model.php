<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang_model extends CI_Model {
  public $table       = 'gudang';
  public $id          = 'id_gudang';
  public $table_toko  = 'toko';
  public $id_toko     = 'id_toko';
  public $order       = 'DESC';

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_all_toko()
  {
    $this->db->order_by('nama_toko', 'asc');
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

  function import($data)
  {
    $this->db->replace($this->table, $data);
  }
  
  function update($id,$data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->table, $data);
  }

  function delete($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table);
  }

  function delete_in($id)
  {
    $this->db->where_in($this->id, explode(",", $id));
    $this->db->delete($this->table);
  }
	

}

/* End of file Gudang_model.php */
/* Location: ./application/models/Gudang_model.php */