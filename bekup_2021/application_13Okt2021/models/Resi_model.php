<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resi_model extends CI_Model {


  public $table 			        = 'resi';
  public $id    			        = 'id_resi';
  public $table_kurir         = 'kurir';
  public $id_kurir            = 'id_kurir';
  public $table_penjualan 	  = 'penjualan';
  public $no_resi 		   	    = 'nomor_resi';
  public $order               = 'DESC';

  function get_all_by_harian()
  {
    $this->db->order_by('status', 'asc');
    $this->db->order_by('tgl_resi', 'desc');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users', 'users.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    $this->db->where("date_format(tgl_resi, '%Y-%m-%d') =", date('Y-m-d'));
    $this->db->where_not_in('status', 0);
    // $this->db->where("status", 1);
    // $this->db->where('status', 2);
    return $this->db->get($this->table)->result();
  }

  function get_all_kurir()
  {
    $this->db->order_by('nama_kurir');
    $data = $this->db->get($this->table_kurir);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_kurir']] = $row['nama_kurir'];
      }
      return $result;
    }
  }

  function get_all_by_harian_gudang()
  {
    $this->db->order_by('status', 'asc');
    $this->db->order_by('tgl_resi', 'desc');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users', 'users.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    $this->db->where("date_format(tgl_resi, '%Y-%m-%d') =", date('Y-m-d'));
    $this->db->where_not_in('status', 0);
    $this->db->where_not_in('status', 2);
    // $this->db->where("status", 1);
    // $this->db->where('status', 2);
    return $this->db->get($this->table)->result();
  }

  function get_all()
  {
    $this->db->order_by('status', 'asc');
  	$this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
  	$this->db->join('users', 'users.id_users = resi.id_users');
  	$this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    return $this->db->get($this->table)->result();
  }

  function get_datatable($kurir, $status, $first, $last)
  {
    $this->db->order_by('status', 'asc');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users', 'users.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($status != 'semua') {
      $this->db->where('status', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_resi, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_resi, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($kurir, $status, $first, $last)
  {
    $this->db->order_by('status', 'asc');
    $this->db->select('COUNT(id_resi) as "total"');
    $this->db->select('COUNT(CASE WHEN status = 0 THEN 1 END) as "belum"');
    $this->db->select('COUNT(CASE WHEN status = 1 THEN 1 END) as "diproses"');
    $this->db->select('COUNT(CASE WHEN status = 2 THEN 1 END) as "sudah"');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users', 'users.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($status != 'semua') {
      $this->db->where('status', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_resi, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_resi, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_penjualan_by_resi($id)
  {
  	$this->db->where($this->no_resi, $id);
    return $this->db->get($this->table_penjualan)->row();
  }

  function get_data_cek()
  {
  	$this->db->select('name, resi.nomor_resi, nama_kurir, COUNT(penjualan.id_kurir) AS "ekspedisi", status');
  	$this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
  	$this->db->join('users', 'users.id_users = resi.id_users');
  	$this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
  	$this->db->group_by('penjualan.id_kurir');
  	$this->db->where('status', 1);
  	// $this->db->having('status', 1);
    return $this->db->get($this->table)->result();
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

  function get_by_resi($id)
  {
    $this->db->where($this->no_resi, $id);
    return $this->db->get($this->table)->row();
  }

  function get_by_resi_belum($id)
  {
    $this->db->where($this->no_resi, $id);
    $this->db->where($this->no_resi, $id);
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

  function update_by_resi($id,$data)
  {
    $this->db->where($this->no_resi, $id);
    $this->db->update($this->table, $data);
  }

  function delete($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table);
  }

  function delete_by_resi($id)
  {
    $this->db->where($this->no_resi, $id);
    $this->db->delete($this->table);
  }
	

}

/* End of file Resi_model.php */
/* Location: ./application/models/Resi_model.php */