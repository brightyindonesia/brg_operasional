<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tiket_model extends CI_Model {

  public $table 			    = 'tiket';
  public $id    			    = 'nomor_tiket';
  public $table_penjualan	= 'penjualan';
  public $id_penjualan		= 'nomor_pesanan';
  public $table_kategori_kasus = 'kategori_kasus';
  public $id_kategori_kasus    = 'id_kategori_kasus';
  public $table_level_kasus = 'level_kasus';
  public $id_level_kasus    = 'id_level_kasus';
  public $table_status_tiket = 'status_tiket';
  public $id_status_tiket    = 'id_status_tiket';
  public $table_resi		    = 'resi';
  public $id_resi			      = 'nomor_resi';
  public $table_users       = 'users';
  public $id_users          = 'id_users';
  public $table_kurir 		  = 'kurir';
  public $id_kurir 			      = 'id_kurir';
  public $table_toko 		     = 'toko';
  public $id_toko 			  = 'id_toko';
  public $order           = 'DESC';

  public $column_order = array(null, 'penjualan.nomor_pesanan','judul_kasus', 'tanggal_tiket', 'id_kategori_kasus', 'id_level_kasus', 'id_status_tiket'); //field yang ada di table user
  public $column_search = array('penjualan.nomor_pesanan','judul_kasus', 'nama_penerima', 'nomor_resi'); //field yang diizin untuk pencarian 
  public $order_data = array('tanggal_tiket' => 'desc'); // default order 

  // Datatables Server Side
  private function _get_datatables_query()
    {
      $i = 0;
   
      foreach ($this->column_search as $item) // looping awal
      {
          if($_GET['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
          {
               
              if($i===0) // looping awal
              {
                  $this->db->group_start(); 
                  $this->db->like($item, $_GET['search']['value']);
              }
              else
              {
                  $this->db->or_like($item, $_GET['search']['value']);
              }

              if(count($this->column_search) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_data))
      {
          $order = $this->order_data;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->order_by('tanggal_tiket', 'desc');
      $this->db->select('*');
      $this->db->select('hd.name AS nama_hd');
      $this->db->select('cr.name AS nama_cr');
      $this->db->join($this->table_penjualan, 'penjualan.nomor_pesanan = tiket.nomor_pesanan');
      $this->db->join('users cr', 'cr.id_users = rating.created_by');
      $this->db->join('users hd', 'hd.id_users = rating.handled_by');
      $this->db->join($this->table_kategori_kasus, 'kategori_kasus.id_kategori_kasus = tiket.id_kategori_kasus');
      $this->db->join($this->table_level_kasus, 'level_kasus.id_level_kasus = tiket.id_level_kasus');
      $this->db->join($this->table_status_tiket, 'status_tiket.id_status_tiket = tiket.id_status_tiket');
      $this->db->from($this->table);

      if ($_GET['kasus'] != 'semua') {
        $this->db->where('tiket.id_kategori_kasus', $_GET['kasus']); 
      }

      if ($_GET['level'] != 'semua') {
        $this->db->where('tiket.id_level_kasus', $_GET['level']); 
      }

      if ($_GET['status'] != 'semua') {
        $this->db->where('tiket.id_status_tiket', $_GET['status']); 
      }

      // $this->db->where( array(  "tgl_resi >="   => $first,
      //                           "tgl_resi <="   => $last
      //                         ));
      $this->db->where( array(  "date_format(tanggal_tiket, '%Y-%m-%d') >="   => $start,
                                "date_format(tanggal_tiket, '%Y-%m-%d') <="   => $end
      ));
  }

  function get_datatables()
  {
      $this->_get_datatables_query();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered()
  {
      $this->_get_datatables_query();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all()
  {
      $this->db->from($this->table);
      return $this->db->count_all_results();
  }

  function get_dasbor_list($kasus, $level, $status, $first, $last)
  {
    $this->db->select('COUNT(nomor_tiket) as "total"');
    $this->db->select('COUNT(CASE WHEN id_status_tiket = 1 THEN 1 END) as "terbuka"');
    $this->db->select('COUNT(CASE WHEN id_status_tiket = 2 THEN 1 END) as "pending"');
    $this->db->select('COUNT(CASE WHEN id_status_tiket = 3 THEN 1 END) as "tertutup"');
    if ($kasus != 'semua') {
      $this->db->where('tiket.id_kategori_kasus', $kasus); 
    }

    if ($level != 'semua') {
      $this->db->where('tiket.id_level_kasus', $level); 
    }

    if ($status != 'semua') {
      $this->db->where('tiket.id_status_tiket', $status); 
    }
    

    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tanggal_tiket, '%Y-%m-%d') >="   => $first,
                              "date_format(tanggal_tiket, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_users_all_combobox()
  {
    $this->db->order_by('name');
    $this->db->where_in('usertype', array(7, 11));
    $data = $this->db->get($this->table_users);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Users -';
        $result[$row['id_users']] = $row['name'];
      }
      return $result;
    }
  }

  function get_kasus_all_combobox()
  {
    $this->db->order_by('nama_kategori_kasus');
    $data = $this->db->get($this->table_kategori_kasus);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_kategori_kasus']] = $row['nama_kategori_kasus'];
      }
      return $result;
    }
  }

  function get_level_all_combobox()
  {
    $this->db->order_by('nama_level_kasus');
    $data = $this->db->get($this->table_level_kasus);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_level_kasus']] = $row['nama_level_kasus'];
      }
      return $result;
    }
  }

  function get_status_all_combobox()
  {
    $this->db->order_by('nama_status_tiket');
    $data = $this->db->get($this->table_status_tiket);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_status_tiket']] = $row['nama_status_tiket'];
      }
      return $result;
    }
  }

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_all_by_id_in($id)
  {
    $this->db->where_in($this->id, explode(",", $id));
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
  	$this->db->where('nomor_pesanan', $id);
    $this->db->or_where('penjualan.nomor_resi', $id);
  	$this->db->join($this->table_resi, 'penjualan.nomor_resi = resi.nomor_resi');
  	$this->db->join($this->table_kurir, 'penjualan.id_kurir = kurir.id_kurir');
  	$this->db->join($this->table_toko, 'penjualan.id_toko = toko.id_toko');
  	return $this->db->get($this->table_penjualan)->row();
  }

  function cari_nomor($nomor)
  {
    $this->db->order_by('tanggal_tiket', $this->order);
    $this->db->like('nomor_tiket', $nomor, 'BOTH');
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

/* End of file Tiket_model.php */
/* Location: ./application/models/Tiket_model.php */