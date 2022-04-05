<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_model extends CI_Model {

  public $table 			= 'retur';
  public $id    			= 'nomor_retur';
  public $table_produk_retur  = 'produk_retur';
  public $id_produk_retur      = 'id_produk_retur';
  public $table_penjualan	= 'penjualan';
  public $id_penjualan		= 'nomor_pesanan';
  public $table_resi		= 'resi';
  public $id_resi			= 'nomor_resi';
  public $table_kurir 		= 'kurir';
  public $id_kurir 			= 'id_kurir';
  public $table_status  	= 'status_transaksi';
  public $id_status     	= 'id_status_transaksi';
  public $table_toko 	 	= 'toko';
  public $id_toko 		  	= 'id_toko';
  public $table_produk 		= 'produk';
  public $id_produk 	  	= 'id_produk';
  public $table_sku     	= 'sku';
  public $id_sku        	= 'id_sku';
  public $order 			= 'DESC';

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
  function get_cek_resi_all_by_resi($id)
  {
  	$this->db->where('penjualan.nomor_resi', $id);
  	$this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
  	$this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
  	$this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
  	return $this->db->get($this->table_penjualan)->row();
  }

  function get_all_by_retur($id)
  {
    $this->db->where($this->id, $id);
    $this->db->join($this->table_penjualan, 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
    $this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
    return $this->db->get($this->table)->row();
  }

  function get_datatable($status, $kurir, $toko, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');  
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko); 
    }
    if ($status != 'semua') {
      $this->db->where('status_retur', $status); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
                            // ));
    $this->db->where( array(  "date_format(tgl_retur, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_retur, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($status, $kurir, $toko, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->select('COUNT(nomor_retur) as "total"');
    $this->db->select('COUNT(CASE WHEN status_retur = 0 THEN 1 END) as "diproses"');
    $this->db->select('COUNT(CASE WHEN status_retur = 1 THEN 1 END) as "sudah"');
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko); 
    }
    if ($status != 'semua') {
      $this->db->where('status_retur', $status); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_retur, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_retur, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
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

  function insert_produk_retur($data)
  {
    $this->db->insert($this->table_produk_retur, $data);
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

  public function cari_nomor($nomor)
  {
    $this->db->order_by('nomor_retur', $this->order);
    $this->db->like('nomor_retur', $nomor, 'BOTH');
    return $this->db->get($this->table)->row();
  }
	

}

/* End of file Retur_model.php */
/* Location: ./application/models/Retur_model.php */