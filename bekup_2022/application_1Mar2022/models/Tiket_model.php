<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tiket_model extends CI_Model {

  public $table 			= 'tiket';
  public $id    			= 'nomor_tiket';
  public $table_penjualan	= 'penjualan';
  public $id_penjualan		= 'nomor_pesanan';
  public $table_resi		= 'resi';
  public $id_resi			= 'nomor_resi';
  public $table_kurir 		= 'kurir';
  public $id_kurir 			= 'id_kurir';
  public $table_toko 		= 'toko';
  public $id_toko 			= 'id_toko';
  public $order = 'DESC';

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_by_id($id)
  {
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_cek_resi_all_by_resi($id)
  {
  	$this->db->where('penjualan.nomor_resi', $id);
  	$this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
  	$this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
  	$this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
  	return $this->db->get($this->table_penjualan)->row();
  }

  function get_cek_resi_all_by_nomor_pesanan($id)
  {
  	$this->db->where('nomor_pesanan', $id);
  	$this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
  	$this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
  	$this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
  	return $this->db->get($this->table_penjualan)->row();
  }

  function get_cek_resi_all_by_nomor_pesanan_resi($id)
  {
  	$this->db->where( array( 'nomor_pesanan OR' 			=> $id,
  							 'penjualan.nomor_resi'		=> $id		
  					));
  	$this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
  	$this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
  	$this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
  	return $this->db->get($this->table_penjualan)->row();
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

/* End of file Tiket_model.php */
/* Location: ./application/models/Tiket_model.php */