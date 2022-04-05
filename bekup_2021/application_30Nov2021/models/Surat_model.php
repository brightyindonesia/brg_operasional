<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Surat_model extends CI_Model {

	public $table 			  = 'surat_jalan';
	public $id    			  = 'no_surat_jalan';

  public $table_detail  = 'detail_surat_jalan';
  public $id_detail     = 'id_detail_surat_jalan';

  public $table_packing = 'surat_packing';
  public $id_packing    = 'no_surat_packing';

  public $table_detail_packing  = 'detail_surat_packing';
  public $id_detail_packing     = 'id_detail_surat_packing';

	public $order   		  = 'DESC';


	public $column_order = array(null,'tgl_surat_jalan', 'no_surat_jalan', 'nama_surat_jalan', 'nama_penerima', null); //field yang ada di table user
	public $column_search = array('tgl_surat_jalan', 'no_surat_jalan', 'nama_surat_jalan', 'nama_penerima'); //field yang diizin untuk pencarian 
  public $order_data = array('tgl_surat_jalan' => 'asc'); // default order 

  public $column_order_packing = array(null,'tgl_surat_packing', 'no_surat_packing', 'nama_surat_packing', 'nama_penerima', null); //field yang ada di table user
  public $column_search_packing = array('tgl_surat_packing', 'no_surat_packing', 'nama_surat_packing', 'nama_penerima'); //field yang diizin untuk pencarian   
  public $order_data_packing = array('tgl_surat_packing' => 'asc'); // default order 

	// Tabel Server Side
	// Surat Jalan
	private function _get_datatables_query_surat_jalan()
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
      $this->db->order_by('created_surat_jalan', 'desc');
      $this->db->join('penerima', 'penerima.id_penerima = surat_jalan.id_penerima');
      $this->db->from($this->table);  

      if ($_GET['penerima'] != 'semua') {
        $this->db->where('penerima.id_penerima', $_GET['penerima']); 
      }
      // $this->db->where( array(  "tgl_penjualan >="   => $first,
      //                           "tgl_penjualan <="   => $last
                              // ));
      $this->db->where( array(  "date_format(tgl_surat_jalan, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_surat_jalan, '%Y-%m-%d') <="   => $end
                              ));
  }

  function get_datatables_surat_jalan()
  {
      $this->_get_datatables_query_surat_jalan();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_surat_jalan()
  {
      $this->_get_datatables_query_surat_jalan();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_surat_jalan()
  {
      $this->db->from($this->table);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_dasbor_list($penerima, $first, $last)
  {
    $this->db->order_by('tgl_surat_jalan', 'desc');
    $this->db->select('COUNT(no_surat_jalan) as "total"');
    $this->db->join('penerima', 'penerima.id_penerima = surat_jalan.id_penerima');
    if ($penerima != 'semua') {
      $this->db->where('penerima.id_penerima', $penerima); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(created_surat_jalan, '%Y-%m-%d') >="   => $first,
                              "date_format(created_surat_jalan, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_surat_jalan_by_id_row($id)
  {	
  	$this->db->where($this->id, $id);
  	return $this->db->get($this->table)->row();	
  }

  function get_detail_surat_jalan_by_nomor($id)
  {	
  	$this->db->order_by($this->id_detail, 'asc');
  	$this->db->where($this->id, $id);
  	return $this->db->get($this->table_detail)->result();	
  }

  function cari_nomor_sj($nomor)
  {
    $this->db->order_by($this->id, $this->order);
    $this->db->like('no_surat_jalan', $nomor, 'BOTH');
    return $this->db->get($this->table)->row();
  }

  function insert($data)
  {
    $this->db->insert($this->table, $data);
  }

  function insert_detail($data)
  {
    $this->db->insert($this->table_detail, $data);
  }

  function update($id,$data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->table, $data);
  }

  function update_detail($id,$nomor_surat,$data)
  {
    $this->db->where($this->id_detail, $id);
    $this->db->where($this->id, $nomor_surat);
    $this->db->update($this->table_detail, $data);
  }

  function delete_detail_by_nomor($nomor)
  {
  	$this->db->where($this->id, $nomor);
  	$this->db->delete($this->table_detail);
  }

  function delete($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table);
  }

  // Surat Packing
  private function _get_datatables_query_surat_packing()
    {
      $i = 0;
   
      foreach ($this->column_search_packing as $item) // looping awal
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

              if(count($this->column_search_packing) - 1 == $i) 
                  $this->db->group_end(); 
          }
          $i++;
      }
       
      if(isset($_GET['order'])) 
      {
          $this->db->order_by($this->column_order_packing[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_data_packing))
      {
          $order = $this->order_data_packing;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->order_by('created_surat_packing', 'desc');
      $this->db->join('penerima', 'penerima.id_penerima = surat_packing.id_penerima');
      $this->db->from($this->table_packing);  

      if ($_GET['penerima'] != 'semua') {
        $this->db->where('penerima.id_penerima', $_GET['penerima']); 
      }
      // $this->db->where( array(  "tgl_penjualan >="   => $first,
      //                           "tgl_penjualan <="   => $last
                              // ));
      $this->db->where( array(  "date_format(tgl_surat_packing, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_surat_packing, '%Y-%m-%d') <="   => $end
                              ));
  }

  function get_datatables_surat_packing()
  {
      $this->_get_datatables_query_surat_packing();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_surat_packing()
  {
      $this->_get_datatables_query_surat_packing();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_surat_packing()
  {
      $this->db->from($this->table_packing);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_dasbor_list_packing($penerima, $first, $last)
  {
    $this->db->order_by('tgl_surat_packing', 'desc');
    $this->db->select('COUNT(no_surat_packing) as "total"');
    $this->db->join('penerima', 'penerima.id_penerima = surat_packing.id_penerima');
    if ($penerima != 'semua') {
      $this->db->where('penerima.id_penerima', $penerima); 
    }
    // $this->db->where( array(  "tgl_penjualan >="   => $first,
    //                           "tgl_penjualan <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(created_surat_packing, '%Y-%m-%d') >="   => $first,
                              "date_format(created_surat_packing, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table_packing)->row();
  }

  function get_surat_packing_by_id_row_packing($id)
  { 
    $this->db->where($this->id_packing, $id);
    return $this->db->get($this->table_packing)->row(); 
  }

  function get_detail_surat_packing_by_nomor_packing($id)
  { 
    $this->db->order_by($this->id_detail_packing, 'asc');
    $this->db->where($this->id_packing, $id);
    return $this->db->get($this->table_detail_packing)->result(); 
  }

  function cari_nomor_sp($nomor)
  {
    $this->db->order_by($this->id_packing, $this->order);
    $this->db->like('no_surat_packing', $nomor, 'BOTH');
    return $this->db->get($this->table_packing)->row();
  }

  function insert_packing($data)
  {
    $this->db->insert($this->table_packing, $data);
  }

  function insert_detail_packing($data)
  {
    $this->db->insert($this->table_detail_packing, $data);
  }

  function update_packing($id,$data)
  {
    $this->db->where($this->id_packing, $id);
    $this->db->update($this->table_packing, $data);
  }

  function update_detail_packing($id,$nomor_surat,$data)
  {
    $this->db->where($this->id_detail_packing, $id);
    $this->db->where($this->id_packing, $nomor_surat);
    $this->db->update($this->table_detail_packing, $data);
  }

  function delete_detail_by_nomor_packing($nomor)
  {
    $this->db->where($this->id_packing, $nomor);
    $this->db->delete($this->table_detail_packing);
  }

  function delete_packing($id)
  {
    $this->db->where($this->id_packing, $id);
    $this->db->delete($this->table_packing);
  }
}

/* End of file Surat_model.php */
/* Location: ./application/models/Surat_model.php */