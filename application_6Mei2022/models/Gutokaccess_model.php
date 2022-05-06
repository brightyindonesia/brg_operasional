<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gutokaccess_model extends CI_Model {

  public $table 		= 'gutok_data_access';
  public $table_toko 	= 'toko';
  public $id    		= 'id_gutok_data_access';
  public $order 		= 'DESC';

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_all_combobox()
  {
    $this->db->order_by('nama_toko', 'asc');
    $data = $this->db->get($this->table_toko);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[$row['id_toko']] = $row['nama_toko'];
      }
      return $result;
    }
  }

  function get_all_data_access_old($id)
  {
    $this->db->join($this->table_toko, 'toko.id_toko = gutok_data_access.id_toko', 'left');
    $this->db->where('gutok_data_access.id_gudang', $id);
    return $this->db->get('gutok_data_access')->result();
  }		

}

/* End of file Gutokaccess_model.php */
/* Location: ./application/models/Gutokaccess_model.php */