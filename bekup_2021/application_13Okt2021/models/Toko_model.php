<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Toko_model extends CI_Model {

  public $table       = 'toko';
  public $id          = 'id_toko';
  public $table_jenis = 'jenis_toko';
  public $id_jenis    = 'id_jenis_toko';
  public $order       = 'DESC';

  function get_all()
  {
    $this->db->join($this->table_jenis, 'jenis_toko.id_jenis_toko = toko.id_jenis_toko');
    return $this->db->get($this->table)->result();
  }

  function get_all_jenis()
  {
    $this->db->order_by('nama_jenis_toko');
    $data = $this->db->get($this->table_jenis);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Jenis Toko -';
        $result[$row['id_jenis_toko']] = $row['nama_jenis_toko'];
      }
      return $result;
    }
  }

  function get_all_combobox()
  {
    $this->db->order_by('nama_toko');
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[$row['id_toko']] = $row['nama_toko'];
      }
      return $result;
    }
  }

  function get_by_id($id)
  {
    $this->db->join($this->table_jenis, 'jenis_toko.id_jenis_toko = toko.id_jenis_toko');
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

}

/* End of file Jenis_toko_model.php */
/* Location: ./application/models/Jenis_toko_model.php */