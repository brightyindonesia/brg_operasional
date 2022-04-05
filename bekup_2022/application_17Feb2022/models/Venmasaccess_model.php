<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venmasaccess_model extends CI_Model {

  public $table 		    = 'venmas_data_access';
  public $table_vendor 	= 'vendor';
  public $id    		    = 'id_venmas_access';
  public $order 		    = 'DESC';

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_all_combobox()
  {
    $this->db->order_by('nama_vendor', 'asc');
    $data = $this->db->get($this->table_vendor);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[$row['id_vendor']] = $row['nama_vendor'];
      }
      return $result;
    }
  }

  function get_all_data_access_old($id)
  {
    $this->db->join('vendor', 'vendor.id_vendor = venmas_data_access.id_vendor', 'left');
    $this->db->where('venmas_data_access.id_bahan_kemas', $id);
    return $this->db->get('venmas_data_access')->result();
  }	

}

/* End of file Tokproaccess_model.php */
/* Location: ./application/models/Tokproaccess_model.php */