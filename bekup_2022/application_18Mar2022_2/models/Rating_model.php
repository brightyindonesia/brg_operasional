<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rating_model extends CI_Model {
  public $table           = 'rating';
  public $table_detail    = 'detail_rating';
  public $id    			    = 'id_rating';
  public $table_penjualan		= 'penjualan';
  public $id_penjualan			= 'nomor_pesanan';
  public $table_kategori_rating	= 'kategori_rating';
  public $id_kategori_rating   	= 'id_kategori_rating';
  public $table_resi		    = 'resi';
  public $id_resi			    = 'nomor_resi';
  public $table_users       	= 'users';
  public $id_users          	= 'id_users';
  public $table_kurir 			= 'kurir';
  public $id_kurir 			    = 'id_kurir';
  public $table_toko 		    = 'toko';
  public $id_toko 			  	= 'id_toko';
  public $order           		= 'DESC';

  // SPEK DATATABLES RATING
  public $column_order = array(null, 'penjualan.nomor_pesanan','nama_kategori_rating', 'tanggal_rating', 'rating'); //field yang ada di table user
  public $column_search = array('penjualan.nomor_pesanan','nama_kategori_rating', 'nama_penerima', 'nomor_resi'); //field yang diizin untuk pencarian 
  public $order_data = array('tanggal_rating' => 'desc'); // default order 

  // SPEK DATATABLES MEAN
  public $column_order_mean = array(null, 'nama_kategori_rating', 'jumlah','avg'); //field yang ada di table user
  public $column_search_mean = array('nama_kategori_rating', 'jumlah','avg'); //field yang diizin untuk pencarian 
  public $order_data_mean = array('avg' => 'desc'); // default order 

  // Datatables Server Side
  // Mean
  private function _get_datatables_query_mean()
  {
    $i = 0;
   
    foreach ($this->column_search_mean as $item) // looping awal
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

            if(count($this->column_search_mean) - 1 == $i) 
                $this->db->group_end(); 
        }
        $i++;
    }
     
    if(isset($_GET['order'])) 
    {
        $this->db->order_by($this->column_order_mean[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
    } 
    else if(isset($this->order_data_mean))
    {
        $order = $this->order_data_mean;
        $this->db->order_by(key($order), $order[key($order)]);
    }

    $start = substr($_GET['periodik'], 0, 10);
    $end = substr($_GET['periodik'], 13, 24);
    $this->db->order_by('avg', 'desc');
    $this->db->select('nama_kategori_rating');
    $this->db->select('SUM(rating) / COUNT(DISTINCT detail_rating.id_rating) AS "avg"');
    $this->db->select('COUNT(DISTINCT detail_rating.id_rating) AS "jumlah"');

    $this->db->join($this->table_kategori_rating, 'kategori_rating.id_kategori_rating = detail_rating.id_kategori_rating');
    $this->db->join($this->table, 'rating.id_rating = detail_rating.id_rating', 'left');
    $this->db->group_by('detail_rating.id_kategori_rating');
    $this->db->from($this->table_detail);

    if ($_GET['kategori'] != 'semua') {
      $this->db->where('detail_rating.id_kategori_rating', $_GET['kategori']); 
    }

    $this->db->where( array(  "date_format(tanggal_rating, '%Y-%m-%d') >="   => $start,
                              "date_format(tanggal_rating, '%Y-%m-%d') <="   => $end
    ));
  }

  function get_datatables_mean()
  {
      $this->_get_datatables_query_mean();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_mean()
  {
      $this->_get_datatables_query_mean();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_mean()
  {
      $this->db->select('nama_kategori_rating,');
      $this->db->select('SUM(rating) / COUNT(DISTINCT id_rating) AS "avg"');

      $this->db->join($this->table_kategori_rating, 'kategori_rating.id_kategori_rating = detail_rating.id_kategori_rating');
      $this->db->group_by('detail_rating.id_kategori_rating');
      $this->db->from($this->table_detail);
      return $this->db->count_all_results();
  }

  // Data Rating
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
      $this->db->order_by('tanggal_rating', 'desc');
      $this->db->select('*');
      $this->db->select('hd.name AS nama_hd');
      $this->db->select('cr.name AS nama_cr');
      $this->db->join($this->table_penjualan, 'penjualan.nomor_pesanan = rating.nomor_pesanan');
      $this->db->join('users cr', 'cr.id_users = rating.created_by');
      $this->db->join('users hd', 'hd.id_users = rating.handled_by');
      $this->db->join($this->table_detail, 'rating.id_rating = detail_rating.id_rating');
      // $this->db->join($this->table_kategori_rating, 'kategori_rating.id_kategori_rating = detail_rating.id_kategori_rating');
      $this->db->from($this->table);
      $this->db->group_by('rating.nomor_pesanan');

      if ($_GET['kategori'] != 'semua') {
        $this->db->where('detail_rating.id_kategori_rating', $_GET['kategori']); 
      }

      // $this->db->where( array(  "tgl_resi >="   => $first,
      //                           "tgl_resi <="   => $last
      //                         ));
      $this->db->where( array(  "date_format(tanggal_rating, '%Y-%m-%d') >="   => $start,
                                "date_format(tanggal_rating, '%Y-%m-%d') <="   => $end
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

  function get_dasbor_list($kategori,$first, $last)
  {
    if ($kategori != 'semua') {
    	$this->db->where('detail_rating.id_kategori_rating', $kategori); 
    }
    $this->db->join($this->table_detail, 'detail_rating.id_rating = rating.id_rating');
    $this->db->group_by('rating.nomor_pesanan');

    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tanggal_rating, '%Y-%m-%d') >="   => $first,
                              "date_format(tanggal_rating, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_kategori_all_combobox()
  {
    $this->db->order_by('nama_kategori_rating');
    $data = $this->db->get($this->table_kategori_rating);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_kategori_rating']] = $row['nama_kategori_rating'];
      }
      return $result;
    }
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

  function get_by_nomor_pesanan_nomor_resi($nomor)
  {
    $this->db->where('penjualan.nomor_pesanan', $nomor);
    $this->db->or_where('nomor_resi', $nomor);
    $this->db->join($this->table_penjualan, 'penjualan.nomor_pesanan = rating.nomor_pesanan');
    return $this->db->get($this->table)->row();
  }

  function get_detail_by_id($id)
  {
    $this->db->where($this->id, $id);
    $this->db->join($this->table_kategori_rating, 'kategori_rating.id_kategori_rating = detail_rating.id_kategori_rating');
    return $this->db->get($this->table_detail)->result();
  }

  function get_by_nomor_pesanan($id)
  {
    $this->db->where($this->id_penjualan, $id);
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
    $this->db->insert($this->table_detail, $data);
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

  function delete_detail_by_id($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table_detail);
  }

  function delete_in($id)
  {
    $this->db->where_in($this->id, explode(",", $id));
    $this->db->delete($this->table);
  }


}

/* End of file Rating_model.php */
/* Location: ./application/models/Rating_model.php */