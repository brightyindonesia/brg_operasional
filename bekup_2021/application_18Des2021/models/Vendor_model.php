<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_model extends CI_Model {

  public $table = 'vendor';
  public $id    = 'id_vendor';
  public $order = 'DESC';

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
  function get_all_combobox()
  {
    $this->db->order_by('nama_vendor', 'asc');
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[$row['id_vendor']] = $row['nama_vendor'];
      }
      return $result;
    }
  }

  function get_all_combobox2()
  {
    $this->db->order_by('nama_vendor', 'asc');
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Vendor -';
        $result[$row['id_vendor']] = $row['nama_vendor'];
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

/* End of file Kurir_model.php */
/* Location: ./application/models/Kurir_model.php */