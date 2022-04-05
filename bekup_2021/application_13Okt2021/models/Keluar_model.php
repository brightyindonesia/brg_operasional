<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keluar_model extends CI_Model {
  public $table         = 'penjualan';
  public $id            = 'nomor_pesanan';
  public $detail_table  = 'detail_penjualan';
  public $table_kurir 	= 'kurir';
  public $id_kurir 		  = 'id_kurir';
  public $table_status  = 'status_transaksi';
  public $id_status     = 'id_status_transaksi';
  public $table_toko 	  = 'toko';
  public $id_toko 		  = 'id_toko';
  public $table_produk 	= 'produk';
  public $id_produk 	  = 'id_produk';
  public $table_tokpro 	= 'tokpro_data_access';
  public $id_tokpro 	  = 'id_tokpro_access';
  public $table_sku     = 'sku';
  public $id_sku        = 'id_sku';
  public $order         = 'DESC';

  function get_all_kurir()
  {
    $this->db->order_by('nama_kurir');
    $data = $this->db->get($this->table_kurir);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Kurir Ekspedisi -';
        $result[$row['id_kurir']] = $row['nama_kurir'];
      }
      return $result;
    }
  }

  function get_all_toko()
  {
    $this->db->order_by('nama_toko');
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

  function get_all_status()
  {
    $this->db->order_by('id_status_transaksi', 'asc');
    $data = $this->db->get($this->table_status);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Status -';
        $result[$row['id_status_transaksi']] = $row['nama_status_transaksi'];
      }
      return $result;
    }
  }

  function get_all_kurir_list()
  {
    $this->db->order_by('nama_kurir');
    $data = $this->db->get($this->table_kurir);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[0] = 'Tidak Ada Kurir';
        $result[$row['id_kurir']] = $row['nama_kurir'];
      }
      return $result;
    }
  }

  function get_all_toko_list()
  {
    $this->db->order_by('nama_toko');
    $data = $this->db->get($this->table_toko);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_toko']] = $row['nama_toko'];
      }
      return $result;
    }
  }

  function get_all_status_list()
  {
    $this->db->order_by('id_status_transaksi', 'asc');
    $data = $this->db->get($this->table_status);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_status_transaksi']] = $row['nama_status_transaksi'];
      }
      return $result;
    }
  }

  public function get_id_barang($id_barang)
	{
		$query = $this->db->get_where('tbl_barang', array('id_barang' => $id_barang));
		return $query->row_array();
	}

  public function get_id_toko($id)
  {
    $this->db->order_by('nama_produk', 'asc');
  	$this->db->join($this->table_tokpro, 'tokpro_data_access.id_toko = toko.id_toko');
  	$this->db->join($this->table_produk, 'tokpro_data_access.id_produk = produk.id_produk');
    $this->db->join($this->table_sku, 'sku.id_sku = produk.id_sku');
	  $query = $this->db->get_where($this->table_toko, array('toko.id_toko' => $id));
	  return $query->result_object();
  }

  public function get_id_produk($id)
	{
		$query = $this->db->get_where($this->table_produk, array('id_produk' => $id));
		return $query->row_array();
	}
	
  public function cari_nomor($nomor)
  {
    $this->db->order_by('nomor_pesanan', $this->order);
    $this->db->like('nomor_pesanan', $nomor, 'BOTH');
    return $this->db->get($this->table)->row();
  }

  function get_all()
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    return $this->db->get($this->table)->result();
  }

  function get_datatable($status, $kurir, $toko, $resi, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko); 
    }
    if ($resi == '' || $resi == NULL) {
      $this->db->where('nomor_resi', ''); 
    }
    if ($status != 'semua') {
      $this->db->where('penjualan.id_status_transaksi', $status); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
                            // ));
    $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
                              "date_format(created, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($status, $kurir, $toko, $resi, $first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->select('COUNT(nomor_pesanan) as "total"');
    $this->db->select('COUNT(CASE WHEN penjualan.id_status_transaksi = 1 THEN 1 END) as "pending"');
    $this->db->select('COUNT(CASE WHEN penjualan.id_status_transaksi = 2 THEN 1 END) as "transfer"');
    $this->db->select('COUNT(CASE WHEN penjualan.id_status_transaksi = 3 THEN 1 END) as "diterima"');
    $this->db->select('COUNT(CASE WHEN penjualan.id_status_transaksi = 4 THEN 1 END) as "retur"');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($toko != 'semua') {
      $this->db->where('penjualan.id_toko', $toko); 
    }
    if ($resi != 'semua') {
      $this->db->where('nomor_resi', $resi); 
    }
    if ($status != 'semua') {
      $this->db->where('penjualan.id_status_transaksi', $status); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
                              "date_format(created, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_all_by_id($id)
  {
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_all_detail()
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_id($id)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where('detail_penjualan.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_all_detail_by_id_row($id)
  {
    $this->db->join('penjualan', 'penjualan.nomor_pesanan = detail_penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where('detail_penjualan.nomor_pesanan', $id);
    return $this->db->get($this->detail_table)->row();
  }

  function get_detail_by_id($id)
  {
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where($this->id, $id);
    return $this->db->get($this->detail_table)->result();
  }

  function get_detail_by_id_row($id)
  {
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where($this->id, $id);
    return $this->db->get($this->detail_table)->row();
  }

  // function get_all_combobox()
  // {
  //   $this->db->order_by('nama_kurir');
  //   $data = $this->db->get($this->table);

  //   if($data->num_rows() > 0)
  //   {
  //     foreach($data->result_array() as $row)
  //     {
  //       $result[''] = '- Pilih Kurir Ekspedisi -';
  //       $result[$row['id_kurir']] = $row['nama_kurir'];
  //     }
  //     return $result;
  //   }
  // }

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

  function insert_detail($data)
  {
    $this->db->insert($this->detail_table, $data);
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

  function delete_detail($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->detail_table);
  }

}

/* End of file Keluar_model.php */
/* Location: ./application/models/Keluar_model.php */