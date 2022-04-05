<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paket_model extends CI_Model {

  public $table 		= 'paket';
  public $id    		= 'id_paket';
  public $table_pakduk	= 'pakduk_data_access';
  public $id_pakduk   = 'id_pakduk_access';
  public $table_propak  = 'propak_data_access';
  public $id_propak   = 'id_propak_access';
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

  function  get_propak_by_id_produk($id)
  {
    $this->db->select('*');
    $this->db->where($this->id_produk, $id);
    return $this->db->get($this->table_propak)->row();
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

  function get_pakduk_produk_by_produk($id)
  {
    $this->db->select('propak_data_access.id_produk as produk_utama');
    $this->db->select('pakduk_data_access.id_produk as produk_detail');
    $this->db->select('nama_produk, hpp_produk, qty_pakduk, sub_sku');
    $this->db->join($this->table_pakduk, 'pakduk_data_access.id_paket = propak_data_access.id_paket');
    $this->db->join($this->table_produk, 'produk.id_produk = pakduk_data_access.id_produk');
    $this->db->where('propak_data_access.id_produk', $id);
    return $this->db->get($this->table_propak)->result();
  }

  function get_pakduk_produk_by_produk_in($id)
  {
    $this->db->select('propak_data_access.id_produk as produk_utama');
    $this->db->select('pakduk_data_access.id_produk as produk_detail');
    $this->db->select('nama_produk, hpp_produk, qty_pakduk, sub_sku');
    $this->db->join($this->table_pakduk, 'pakduk_data_access.id_paket = propak_data_access.id_paket');
    $this->db->join($this->table_produk, 'produk.id_produk = pakduk_data_access.id_produk');
    $this->db->where_in('propak_data_access.id_produk', explode(",", $id));
    return $this->db->get($this->table_propak)->result();
  }
	

}

/* End of file Paket_model.php */
/* Location: ./application/models/Paket_model.php */