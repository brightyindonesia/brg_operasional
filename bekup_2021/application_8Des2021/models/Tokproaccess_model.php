<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tokproaccess_model extends CI_Model {

  public $table 		= 'tokpro_data_access';
  public $table_toko 	= 'toko';
  public $id    		= 'id_tokpro_access';
  public $order 		= 'DESC';

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_all_combobox()
  {
    $this->db->order_by('nama_toko');
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
    $this->db->join('toko', 'toko.id_toko = tokpro_data_access.id_toko', 'left');
    $this->db->where('tokpro_data_access.id_produk', $id);
    return $this->db->get('tokpro_data_access')->result();
  }	

}

/* End of file Tokproaccess_model.php */
/* Location: ./application/models/Tokproaccess_model.php */