<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paket_model extends CI_Model {

  public $table 		= 'paket';
  public $id    		= 'id_paket';
  public $table_pakduk	= 'pakduk';
  public $id_pakduk 	= 'id_pakduk_access';
  public $table_produk 	= 'produk';
  public $id_produk 	= 'id_produk';
  public $order 		= 'DESC';

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_all_combobox()
  {
    $this->db->order_by('nama_paket');
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Paket -';
        $result[$row['id_paket']] = $row['nama_paket'];
      }
      return $result;
    }
  }

  function get_all_produk_by_paket($id)
  {
  	$this->db->join('pakduk_data_access', 'pakduk_data_access.id_paket = paket.id_paket');
  	$this->db->join('produk', 'produk.id_produk = pakduk_data_access.id_produk');
    $this->db->where('paket.id_paket', $id);
    return $this->db->get($this->table)->result();
  }

  function get_all_produk_by_paket_ops($id)
  {
    $this->db->join('pakduk_data_access', 'pakduk_data_access.id_paket = paket.id_paket');
    $this->db->join('produk', 'produk.id_produk = pakduk_data_access.id_produk');
    $this->db->where('paket.id_paket', $id);
    return $this->db->get($this->table);
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

  public function get_id_produk($id)
  {
	$query = $this->db->get_where($this->table_produk, array('id_produk' => $id));
	return $query->row_array();
  }

  function get_all_combobox_produk()
  {
    $this->db->order_by('nama_produk', 'asc');
    $data = $this->db->get($this->table_produk);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Produk -';
        $result[$row['id_produk']] = $row['sub_sku']." | ".$row['nama_produk']." | Stok: ".$row['qty_produk'];
      }
      return $result;
    }
  }  
	

}

/* End of file Paket_model.php */
/* Location: ./application/models/Paket_model.php */