<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resi_model extends CI_Model {

  public $table 			        = 'resi';
  public $id    			        = 'id_resi';
  public $table_kurir         = 'kurir';
  public $id_kurir            = 'id_kurir';
  public $table_users         = 'users';
  public $id_users            = 'id_users';
  public $table_penjualan 	  = 'penjualan';
  public $no_resi 		   	    = 'nomor_resi';
  public $table_resi_access   = 'resi_access';
  public $id_resi_access      = 'id_resi_access';
  public $order               = 'DESC';

  // Utama
  public $column_order = array(null, 'nomor_pesanan','nama_kurir', 'resi.nomor_resi'); //field yang ada di table user
  public $column_search = array('nomor_pesanan','nama_kurir', 'nama_penerima', 'resi.nomor_resi'); //field yang diizin untuk pencarian 
  public $order_data = array('status' => 'desc'); // default order 

  // Admin
  public $column_order_admin = array(null, 'tgl_resi', 'nomor_pesanan', 'resi.nomor_resi','nama_kurir', 'status', 'nama_hd'); //field yang ada di table user
  public $column_search_admin = array('nomor_pesanan','nama_kurir', 'resi.nomor_resi'); //field yang diizin untuk pencarian 
  public $order_data_admin = array('status' => 'asc'); // default order 

  // Table Server Side
  // Data Utama
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
      $this->db->order_by('status', 'desc');
      $this->db->select('*');
      $this->db->select('hd.name as nama_hd');
      $this->db->select('resi.nomor_resi as noresi');
      $this->db->select('date_format(created_resi, "%d-%m-%Y") as tanggal');
      $this->db->order_by('status', 'asc');
      $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
      $this->db->join('users rs', 'rs.id_users = resi.id_users');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
      $this->db->join($this->table_resi_access, 'resi_access.nomor_resi = resi.nomor_resi', 'left');
      $this->db->join('users hd', 'hd.id_users = resi_access.handled_by', 'left');
      $this->db->from($this->table);
      if ($_GET['pic'] != 'semua') {
        $this->db->where('resi_access.handled_by', $_GET['pic']); 
      }
      if ($_GET['kurir'] != 'semua') {
        $this->db->where('penjualan.id_kurir', $_GET['kurir']); 
      }
      if ($_GET['status'] != 'semua') {
        $this->db->where('status', $_GET['status']); 
      }
      // $this->db->where( array(  "tgl_resi >="   => $first,
      //                           "tgl_resi <="   => $last
      //                         ));
      $this->db->where( array(  "date_format(created_resi, '%Y-%m-%d') >="   => $start,
                                "date_format(created_resi, '%Y-%m-%d') <="   => $end
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

  // Data Admin
  private function _get_datatables_query_admin()
    {
      $i = 0;
   
      foreach ($this->column_search_admin as $item) // looping awal
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

              if(count($this->column_search_admin) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order_admin[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_data_admin))
      {
          $order = $this->order_data_admin;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $this->db->select('*, hd.name as nama_hd, resi.nomor_resi as noresi');
      $this->db->order_by('status', 'asc');
      $this->db->order_by('created_resi', 'desc');
      $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
      $this->db->join('users rs', 'rs.id_users = resi.id_users');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
      $this->db->join($this->table_resi_access, 'resi_access.nomor_resi = resi.nomor_resi', 'left');
      $this->db->join('users hd', 'hd.id_users = resi_access.handled_by', 'left');
      $this->db->where("date_format(created_resi, '%Y-%m-%d') =", date('Y-m-d'));
      if ($_GET['status'] != 'semua') {
        $this->db->where('status', $_GET['status']); 
      }
      // $this->db->where_not_in('status', 0);
      $this->db->from($this->table);
  }

  function get_datatables_admin()
  {
      $this->_get_datatables_query_admin();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_admin()
  {
      $this->_get_datatables_query_admin();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_admin()
  {
      $this->db->order_by('status', 'asc');
      $this->db->order_by('created_resi', 'desc');
      $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
      $this->db->join('users', 'users.id_users = resi.id_users');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
      $this->db->where("date_format(created_resi, '%Y-%m-%d') =", date('Y-m-d'));
      $this->db->where_not_in('status', 0);
      $this->db->where_not_in('status', 3);
      $this->db->from($this->table);
      return $this->db->count_all_results();
  }

  // Data Gudang
  private function _get_datatables_query_gudang()
    {
      $i = 0;
   
      foreach ($this->column_search_admin as $item) // looping awal
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

              if(count($this->column_search_admin) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order_admin[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_data_admin))
      {
          $order = $this->order_data_admin;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $this->db->select('*, hd.name as nama_hd, resi.nomor_resi as noresi');
      $this->db->order_by('status', 'asc');
      $this->db->order_by('created_resi', 'desc');
      $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
      $this->db->join('users rs', 'rs.id_users = resi.id_users');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
      $this->db->join($this->table_resi_access, 'resi_access.nomor_resi = resi.nomor_resi', 'left');
      $this->db->join('users hd', 'hd.id_users = resi_access.handled_by', 'left');
      $this->db->where("date_format(created_resi, '%Y-%m-%d') =", date('Y-m-d'));
      if ($_GET['status'] != 'semua') {
        $this->db->where('status', $_GET['status']); 
      }
      // $this->db->where_not_in('status', 0);
      // $this->db->where_not_in('status', 2);
      // $this->db->where_not_in('status', 3);
      $this->db->from($this->table);
  }

  function get_datatables_gudang()
  {
      $this->_get_datatables_query_gudang();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_gudang()
  {
      $this->_get_datatables_query_gudang();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_gudang()
  {
      $this->db->order_by('status', 'asc');
      $this->db->order_by('created_resi', 'desc');
      $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
      $this->db->join('users', 'users.id_users = resi.id_users');
      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
      $this->db->where("date_format(created_resi, '%Y-%m-%d') =", date('Y-m-d'));
      $this->db->where_not_in('status', 0);
      $this->db->where_not_in('status', 2);
      $this->db->where_not_in('status', 3);
      $this->db->from($this->table);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_all_by_harian()
  {
    $this->db->order_by('status', 'asc');
    $this->db->order_by('created_resi', 'desc');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users', 'users.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    $this->db->where("date_format(created_resi, '%Y-%m-%d') =", date('Y-m-d'));
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
    $this->db->order_by('created_resi', 'desc');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users', 'users.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    $this->db->where("date_format(created_resi, '%Y-%m-%d') =", date('Y-m-d'));
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

  function get_datatable_all($kurir, $pic, $status, $first, $last)
  {
    $this->db->order_by('status', 'asc');
    $this->db->select('*, ra.created_by as ra_created, us.created_by as us_created');
    $this->db->join('resi_access as ra', 'ra.nomor_resi = resi.nomor_resi');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('users as us', 'us.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }

    if ($pic != 'semua') {
      $this->db->where('handled_by', $pic); 
    }

    if ($status != 'semua') {
      $this->db->where('status', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(created_resi, '%Y-%m-%d') >="   => $first,
                              "date_format(created_resi, '%Y-%m-%d') <="   => $last
                            ));
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
    $this->db->where( array(  "date_format(created_resi, '%Y-%m-%d') >="   => $first,
                              "date_format(created_resi, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($pic, $kurir, $status, $first, $last)
  {
    $this->db->order_by('status', 'asc');
    $this->db->select('COUNT(id_resi) as "total"');
    $this->db->select('COUNT(CASE WHEN status = 0 THEN 1 END) as "belum"');
    $this->db->select('COUNT(CASE WHEN status = 1 THEN 1 END) as "diproses"');
    $this->db->select('COUNT(CASE WHEN status = 2 THEN 1 END) as "sudah"');
    $this->db->select('COUNT(CASE WHEN status = 3 THEN 1 END) as "gagal"');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users rs', 'rs.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    $this->db->join($this->table_resi_access, 'resi_access.nomor_resi = resi.nomor_resi', 'left');
    $this->db->join('users hd', 'hd.id_users = resi_access.handled_by', 'left');
    
    if ($pic != 'semua') {
      $this->db->where('resi_access.handled_by', $pic); 
    }

    if ($kurir != 'semua') {
      $this->db->where('penjualan.id_kurir', $kurir); 
    }
    if ($status != 'semua') {
      $this->db->where('status', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(created_resi, '%Y-%m-%d') >="   => $first,
                              "date_format(created_resi, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_dasbor_list_admin()
  {
    $this->db->order_by('status', 'asc');
    $this->db->select('COUNT(id_resi) as "total"');
    $this->db->select('COUNT(CASE WHEN status = 0 THEN 1 END) as "belum"');
    $this->db->select('COUNT(CASE WHEN status = 1 THEN 1 END) as "diproses"');
    $this->db->select('COUNT(CASE WHEN status = 2 THEN 1 END) as "sudah"');
    $this->db->select('COUNT(CASE WHEN status = 3 THEN 1 END) as "gagal"');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users', 'users.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    
    $this->db->where("date_format(created_resi, '%Y-%m-%d') =", date('Y-m-d'));
    return $this->db->get($this->table)->row();
  }

  function get_dasbor_list_gudang()
  {
    $this->db->order_by('status', 'asc');
    $this->db->select('COUNT(id_resi) as "total"');
    $this->db->select('COUNT(CASE WHEN status = 0 THEN 1 END) as "belum"');
    $this->db->select('COUNT(CASE WHEN status = 1 THEN 1 END) as "diproses"');
    $this->db->select('COUNT(CASE WHEN status = 2 THEN 1 END) as "sudah"');
    $this->db->select('COUNT(CASE WHEN status = 3 THEN 1 END) as "gagal"');
    $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
    $this->db->join('users', 'users.id_users = resi.id_users');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
    
    $this->db->where("date_format(created_resi, '%Y-%m-%d') =", date('Y-m-d'));
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

  function get_resi_access_by_id_resi_access($id)
  {
    $this->db->where($this->id_resi_access, $id);
    return $this->db->get($this->table_resi_access)->row();
  }

  function get_resi_access_by_nomor_resi($nomor)
  {
    $this->db->join($this->table_users, 'users.id_users = resi_access.handled_by');
    $this->db->where('nomor_resi', $nomor);
    return $this->db->get($this->table_resi_access)->row();
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

  function insert_resi_access($data)
  {
    $this->db->insert($this->table_resi_access, $data);
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