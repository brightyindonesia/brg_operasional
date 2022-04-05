<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timeline_produksi_model extends CI_Model {

  public $table             = 'timeline_produksi';
  public $id                = 'no_timeline_produksi';
  public $table_detail      = 'detail_timeline_produksi';
  public $id_detail         = 'id_detail_timeline_produksi';
  public $table_po          = 'po';
  public $id_po             = 'no_po';
  public $table_detail_po   = 'detail_po';
  public $table_sku         = 'sku';
  public $id_sku            = 'id_sku';
  public $table_timeline    = 'jenis_timeline';
  public $id_timeline       = 'id_jenis_timeline';
  public $table_bahan_kemas = 'bahan_kemas';
  public $id_bahan_kemas    = 'id_bahan_kemas';
  public $table_kategori    = 'kategori_po';
  public $id_kategori       = 'id_kategori_po';
  public $table_vendor      = 'vendor';
  public $id_vendor         = 'id_vendor';
  public $table_propo       = 'propo_data_access';
  public $id_propo          = 'id_propo_data_access';
  public $table_posi        = 'posi_data_access';
  public $id_posi           = 'id_posi_data_access';
  public $table_hpp         = 'hpp_produk';
  public $table_detail_hpp  = 'detail_hpp_produk';
  public $id_hpp            = 'id_hpp_produk';
  public $table_stok        = 'stok_pabrik';
  public $id_stok           = 'id_stok_pabrik';
  public $order             = 'DESC';

  public $column_order = array(null, 'no_timeline_produksi', 'nama_vendor', null, null); //field yang ada di table user
  public $column_order_hpp = array(null, 'no_timeline_produksi', 'nama_vendor', null, 'total_hpp_produk', null); //field yang ada di table user
  public $column_search = array('no_timeline_produksi', 'nama_vendor'); //field yang diizin untuk pencarian 
  public $order_data = array('tgl_timeline_produksi' => 'asc'); // default order 

  // Table Server Side
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
          $this->db->order_by($this->column_order_hpp[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order))
      {
          $order = $this->order;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
      $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
      $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
      $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
      $this->db->from($this->table);

      if ($_GET['vendor'] != 'semua') {
        $this->db->where('po.id_vendor', $_GET['vendor']); 
      }
      if ($_GET['status'] != 'semua') {
        $this->db->where('status_timeline_produksi', $_GET['status']); 
      }
      // $this->db->where( array(  "tgl_resi >="   => $first,
      //                           "tgl_resi <="   => $last
      //                         ));
      $this->db->where( array(  "date_format(tgl_timeline_produksi, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_timeline_produksi, '%Y-%m-%d') <="   => $end
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

  // HPP
  private function _get_datatables_hpp_query()
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
      else if(isset($this->order))
      {
          $order = $this->order;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
      $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
      $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
      $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
      $this->db->from($this->table);

      if ($_GET['vendor'] != 'semua') {
        $this->db->where('po.id_vendor', $_GET['vendor']); 
      }
      
      $this->db->where('status_timeline_produksi', 1); 
      // $this->db->where( array(  "tgl_resi >="   => $first,
      //                           "tgl_resi <="   => $last
      //                         ));
      $this->db->where( array(  "date_format(tgl_timeline_produksi, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_timeline_produksi, '%Y-%m-%d') <="   => $end
      ));
  }

  function get_datatables_hpp()
  {
      $this->_get_datatables_hpp_query();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_hpp()
  {
      $this->_get_datatables_hpp_query();
      $query = $this->db->get();
      return $query->num_rows();
  }

  // HPP Fix
  private function _get_datatables_hpp_fix_query()
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
      else if(isset($this->order))
      {
          $order = $this->order;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->join($this->table, 'timeline_produksi.no_timeline_produksi = hpp_produk.no_timeline_produksi');
      $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
      $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
      $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
      $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
      $this->db->from($this->table_hpp);

      if ($_GET['vendor'] != 'semua') {
        $this->db->where('po.id_vendor', $_GET['vendor']); 
      }
      
      $this->db->where( array(  "date_format(tgl_hpp_produk, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_hpp_produk, '%Y-%m-%d') <="   => $end
      ));
  }

  function get_datatables_hpp_fix()
  {
      $this->_get_datatables_hpp_fix_query();
      if($_GET['length'] != -1)
      $this->db->limit($_GET['length'], $_GET['start']);
      $query = $this->db->get();
      return $query->result();
  }

  function count_filtered_hpp_fix()
  {
      $this->_get_datatables_hpp_fix_query();
      $query = $this->db->get();
      return $query->num_rows();
  }

  public function count_all_hpp_fix()
  {
      $this->db->from($this->table_hpp);
      return $this->db->count_all_results();
  }
  // End Table Server Side

  function get_all_vendor_list()
  {
    $this->db->order_by('nama_vendor');
    $data = $this->db->get($this->table_vendor);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_vendor']] = $row['nama_vendor'];
      }
      return $result;
    }
  }

  function get_po_list()
  {
    $this->db->order_by('no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    $this->db->where_not_in('po.id_kategori_po', 6);
    $this->db->where('status_po', 2);
    $this->db->where('total_selisih_po_produksi >', 0);
    $data = $this->db->get($this->table_po);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Data -';
        $result[$row['no_po']] = $row['no_po']." - ".$row['nama_kategori_po']." (".$row['nama_vendor'].")";
      }
      return $result;
    }else{
      $result[''] = '- Tidak Ada Data -';

      return $result;
    }
  }

  function get_po_stok_by_id_list($id)
  {
    $this->db->order_by('po.no_po');
    $this->db->join($this->table_po, 'po.no_po = stok_pabrik.no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    $this->db->where('fix_sisa_stok_pabrik >', 0);
    $this->db->where($this->id, $id);
    $data = $this->db->get($this->table_stok);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Data -';
        $result[$row['no_po']] = $row['no_po']." - ".$row['nama_kategori_po']." (".$row['nama_vendor'].")";
      }
      return $result;
    }else{
      $result[''] = '- Tidak Ada Data -';

      return $result;
    }
  }

  function get_all_kategori_po_list()
  {
    $this->db->order_by('nama_kategori_po');
    $this->db->where_not_in('id_kategori_po', 6);
    $data = $this->db->get($this->table_kategori);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_kategori_po']] = $row['nama_kategori_po'];
      }
      return $result;
    }
  }

  function get_datatable($vendor, $kategori_po, $status, $first, $last)
  {
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    if ($vendor != 'semua') {
      $this->db->where('po.id_vendor', $vendor); 
    }
    if ($kategori_po != 'semua') {
      $this->db->where('po.id_kategori_po', $kategori_po); 
    }
    if ($status != 'semua') {
      $this->db->where('status_timeline_produksi', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_timeline_produksi, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_timeline_produksi, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($vendor, $status, $first, $last)
  {
    $this->db->select('COUNT(no_timeline_produksi) as "total"');
    $this->db->select('COUNT(CASE WHEN status_timeline_produksi = 0 THEN 1 END) as "proses"');
    $this->db->select('COUNT(CASE WHEN status_timeline_produksi = 1 THEN 1 END) as "sudah"');
    $this->db->select('COUNT(CASE WHEN status_timeline_produksi = 2 THEN 1 END) as "hpp"');
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    if ($vendor != 'semua') {
      $this->db->where('po.id_vendor', $vendor); 
    }
    if ($status != 'semua') {
      $this->db->where('status_timeline_produksi', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_timeline_produksi, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_timeline_produksi, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_dasbor_list_hpp($vendor, $first, $last)
  {
    $this->db->select('COUNT(no_timeline_produksi) as "total"');
    $this->db->select('COUNT(CASE WHEN status_timeline_produksi = 0 THEN 1 END) as "proses"');
    $this->db->select('COUNT(CASE WHEN status_timeline_produksi = 1 THEN 1 END) as "sudah"');
    $this->db->select('COUNT(CASE WHEN status_timeline_produksi = 2 THEN 1 END) as "hpp"');
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    if ($vendor != 'semua') {
      $this->db->where('po.id_vendor', $vendor); 
    }

    $this->db->where('status_timeline_produksi', 1); 
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_timeline_produksi, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_timeline_produksi, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_dasbor_list_hpp_fix($vendor, $first, $last)
  {
    $this->db->select('COUNT(id_hpp_produk) as "total"');
    $this->db->join($this->table, 'timeline_produksi.no_timeline_produksi = hpp_produk.no_timeline_produksi');
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    if ($vendor != 'semua') {
      $this->db->where('po.id_vendor', $vendor); 
    }

    $this->db->where('status_timeline_produksi', 2); 
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_hpp_produk, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_hpp_produk, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table_hpp)->row();
  }

  function get_detail_ajax_datatable($id){
    $this->db->select('detail_timeline_produksi.id_jenis_timeline, nama_jenis_timeline, start_date_detail_timeline_produksi, end_date_detail_timeline_produksi');
    $this->db->join($this->table_timeline, 'detail_timeline_produksi.id_jenis_timeline = jenis_timeline.id_jenis_timeline');
    $this->db->where('no_timeline_produksi', $id);
    return $this->db->get($this->table_detail)->result();
  }

  function get_detail_ajax_datatable_hpp($id){
    $this->db->select('no_po, nama_bahan_kemas, harga_hpp_produk');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_hpp_produk.id_bahan_kemas');
    $this->db->where('id_hpp_produk', $id);
    return $this->db->get($this->table_detail_hpp)->result();
  }

  function get_all()
  {
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    return $this->db->get($this->table)->result();
  }

  function get_data_po()
  {
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    $this->db->where_not_in('po.id_kategori_po', 1);
    $this->db->where_not_in('po.id_kategori_po', 2);
    $this->db->where_not_in('po.id_kategori_po', 3);
    $this->db->where('status_po', 0);
    return $this->db->get($this->table_po)->result();
  }

  function get_all_detail_po_by_po_bahan_row($id, $bahan)
  {
    $this->db->join($this->table_detail_po, 'detail_po.no_po = po.no_po');
    $this->db->where('detail_po.no_po', $id);
    $this->db->where('id_bahan_kemas', $bahan);
    $this->db->where_not_in('po.id_kategori_po', 6);
    return $this->db->get($this->table_po)->row();
  }

  function get_all_detail_po_by_po_bahan($id)
  {
    $this->db->join($this->table_po, 'detail_timeline_produksi.no_po = po.no_po');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_timeline_produksi.id_bahan_kemas');
    $this->db->where('no_timeline_produksi', $id);
    $this->db->where('id_jenis_timeline', 2);
    $this->db->where_not_in('po.id_kategori_po', 6);
    return $this->db->get($this->table_detail)->result();
  }

  function get_all_kategori()
  {
    $this->db->order_by('nama_kategori_po');
    $this->db->where_not_in($this->id_kategori, 1);
    $this->db->where_not_in($this->id_kategori, 2);
    $this->db->where_not_in($this->id_kategori, 3);
    $data = $this->db->get($this->table_kategori);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Kategori -';
        $result[$row['id_kategori_po']] = $row['kode_kategori_po']." - ".$row['nama_kategori_po'];
      }
      return $result;
    }
  }

  function get_all_sku()
  {
    $this->db->order_by('nama_sku');
    $data = $this->db->get($this->table_sku);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih SKU -';
        $result[$row['id_sku']] = $row['nama_sku'];
      }
      return $result;
    }
  }

  function get_all_by_id_row($id)
  {
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->where($this->id_po, $id);
    return $this->db->get($this->table_po)->row();
  }

  function get_all_by_timeline_row($id)
  {
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_detail_by_timeline($id)
  {
    $this->db->order_by('start_date_detail_timeline_produksi', 'asc');
    $this->db->join($this->table_timeline, 'jenis_timeline.id_jenis_timeline = detail_timeline_produksi.id_jenis_timeline');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_timeline_produksi.id_bahan_kemas');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table_detail)->result();
  }

  function get_detail_by_timeline_id_row($id, $timeline)
  {
    $this->db->order_by('start_date_detail_timeline_produksi', 'asc');
    $this->db->join($this->table, 'timeline_produksi.no_timeline_produksi = detail_timeline_produksi.no_timeline_produksi');
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_detail_po, 'po.no_po = detail_po.no_po');
    $this->db->where('timeline_produksi.no_timeline_produksi', $timeline);
    $this->db->where($this->id_detail, $id);
    return $this->db->get($this->table_detail)->row();
  }

  function get_detail_by_timeline_row($id)
  {
    $this->db->order_by('start_date_detail_timeline_produksi', 'asc');
    $this->db->join($this->table_timeline, 'jenis_timeline.id_jenis_timeline = detail_timeline_produksi.id_jenis_timeline');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_timeline_produksi.id_bahan_kemas');
    $this->db->where($this->id_detail, $id);
    return $this->db->get($this->table_detail)->row();
  }

  function get_detail_all_by_timeline_row($id)
  {
    $this->db->select('*');
    $this->db->select('detail_timeline_produksi.id_bahan_kemas as bahan_id');
    $this->db->select('detail_timeline_produksi.no_po as po_bahan');
    $this->db->join($this->table, 'timeline_produksi.no_timeline_produksi = detail_timeline_produksi.no_timeline_produksi');
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_detail_po, 'timeline_produksi.no_po = detail_po.no_po AND detail_po.id_bahan_kemas = detail_timeline_produksi.id_bahan_kemas');
    $this->db->where($this->id_detail, $id);
    return $this->db->get($this->table_detail)->row();
  }

  function get_detail_all_by_timeline_produksi_row($id)
  {
    $this->db->join($this->table, 'timeline_produksi.no_timeline_produksi = detail_timeline_produksi.no_timeline_produksi');
    $this->db->join($this->table_po, 'po.no_po = timeline_produksi.no_po');
    $this->db->join($this->table_detail_po, 'po.no_po = detail_po.no_po');
    $this->db->where('timeline_produksi.no_timeline_produksi', $id);
    return $this->db->get($this->table_detail)->row();
  }

  function get_popro_posi($id)
  {
    $this->db->join($this->table_posi, 'propo_data_access.id_detail_timeline_produksi = posi_data_access.id_detail_timeline_produksi');
    $this->db->where('posi_data_access.id_detail_timeline_produksi', $id);
    return $this->db->get($this->table_propo)->result();
  }

  function get_bahan_posi_by_bahan($id)
  {
    $this->db->join($this->table_posi, 'propo_data_access.id_detail_timeline_produksi = posi_data_access.id_detail_timeline_produksi');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = posi_data_access.id_bahan_kemas');
    $this->db->where('id_jenis_timeline', 2);
    $this->db->where('propo_data_access.no_timeline_produksi', $id);
    return $this->db->get($this->table_propo)->result();
  }

  function get_bahan_posi_by_detail($id)
  {
    $this->db->join($this->table_posi, 'propo_data_access.id_detail_timeline_produksi = posi_data_access.id_detail_timeline_produksi');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = posi_data_access.id_bahan_kemas');
    $this->db->where('posi_data_access.id_detail_timeline_produksi', $id);
    return $this->db->get($this->table_propo)->result();
  }

  function get_bahan_posi_by_bahan_id_detail($id, $detail, $bahan)
  {
    $this->db->join($this->table_posi, 'propo_data_access.id_detail_timeline_produksi = posi_data_access.id_detail_timeline_produksi');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = posi_data_access.id_bahan_kemas');
    $this->db->where('id_jenis_timeline', 2);
    $this->db->where('propo_data_access.no_timeline_produksi', $id);
    $this->db->where('posi_data_access.id_bahan_kemas', $bahan);
    $this->db->where('posi_data_access.id_detail_timeline_produksi', $detail);
    return $this->db->get($this->table_propo)->row();
  }

  function get_bahan_posi_by_bahan_id($id, $bahan)
  {
    $this->db->join($this->table_posi, 'propo_data_access.id_detail_timeline_produksi = posi_data_access.id_detail_timeline_produksi');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = posi_data_access.id_bahan_kemas');
    $this->db->where('id_jenis_timeline', 2);
    $this->db->where('propo_data_access.no_timeline_produksi', $id);
    $this->db->where('posi_data_access.id_bahan_kemas', $bahan);
    return $this->db->get($this->table_propo)->row();
  }

  function get_bahan_by_po($id)
  {
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_po.id_bahan_kemas');
    $this->db->where($this->id_po, $id);
    return $this->db->get($this->table_detail_po)->result();
  }

  function get_all_po_by_po_bahan_row($id, $bahan)
  {
    $this->db->join($this->table_po, 'po.no_po = detail_po.no_po');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_po.id_bahan_kemas');
    $this->db->where('po.no_po', $id);
    $this->db->where('detail_po.id_bahan_kemas', $bahan);
    return $this->db->get($this->table_detail_po)->row();
  }

  function get_posi_by_po_bahan_produksi($po, $bahan, $produksi)
  {
    $this->db->where($this->id_po, $po);
    $this->db->where($this->id_bahan_kemas, $bahan);
    $this->db->where($this->id, $produksi);
    // $this->db->where('id_jenis_timeline', 2);
    return $this->db->get($this->table_posi)->row();
  }

   function get_posi_by_po_bahan_produksi_detail($po, $bahan, $produksi, $posi)
  {
    $this->db->where($this->id_po, $po);
    $this->db->where($this->id_bahan_kemas, $bahan);
    $this->db->where($this->id, $produksi);
    $this->db->where($this->id_posi, $posi);
    // $this->db->where('id_jenis_timeline', 2);
    return $this->db->get($this->table_posi)->row();
  }

  function get_stok_by_po_bahan_produksi($po, $bahan, $produksi)
  {
    $this->db->where($this->id_po, $po);
    $this->db->where($this->id_bahan_kemas, $bahan);
    $this->db->where($this->id, $produksi);
    return $this->db->get($this->table_stok)->row();
  }

  function get_last_detail_timeline($jenis_timeline, $nomor_produksi)
  {
    $this->db->order_by('id_detail_timeline_produksi', 'desc');
    $this->db->where($this->id, $nomor_produksi);
    $this->db->where('id_jenis_timeline', $jenis_timeline);
    return $this->db->get($this->table_detail)->row();
  }

  function get_last_hpp_produk()
  {
    $this->db->order_by($this->id_hpp, 'desc');
    return $this->db->get($this->table_hpp)->row();
  }

  function get_all_hpp_by_id($id)
  {
    $this->db->join($this->table_detail_hpp, 'detail_hpp_produk.id_hpp_produk = hpp_produk.id_hpp_produk');
    $this->db->where('hpp_produk.id_hpp_produk', $id);
    return $this->db->get($this->table_hpp)->row();
  }

  function get_all_hpp_by_timeline($timeline)
  {
    $this->db->join($this->table_detail_hpp, 'detail_hpp_produk.id_hpp_produk = hpp_produk.id_hpp_produk');
    $this->db->where('no_timeline_produksi', $timeline);
    return $this->db->get($this->table_hpp)->row();
  }

  function get_posi_jenis_timeline_by_id($jenis_timeline, $nomor_produksi)
  {
    $this->db->where('id_jenis_timeline', $jenis_timeline);
    $this->db->where($this->id, $nomor_produksi);
    return $this->db->get($this->table_posi)->result();
  }

  function get_posi_bahan_jenis_timeline_by_id($jenis_timeline, $nomor_produksi)
  {
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = posi_data_access.id_bahan_kemas');
    $this->db->where('id_jenis_timeline', $jenis_timeline);
    $this->db->where($this->id, $nomor_produksi);
    return $this->db->get($this->table_posi)->result();
  }

  function get_stok_bahan_by_id($nomor_produksi)
  {
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = stok_pabrik.id_bahan_kemas');
    $this->db->where($this->id, $nomor_produksi);
    return $this->db->get($this->table_stok)->result();
  }

  function get_stok_bahan_by_id_stok_row($id)
  {
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = stok_pabrik.id_bahan_kemas');
    $this->db->where($this->id_stok, $id);
    return $this->db->get($this->table_stok)->row();
  }

  function get_posi_po_bahan_jenis_timeline_by_id($jenis_timeline, $nomor_produksi)
  {
    $this->db->join($this->table_detail_po, 'detail_po.no_po = posi_data_access.no_po AND detail_po.id_bahan_kemas = posi_data_access.id_bahan_kemas');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = posi_data_access.id_bahan_kemas');
    $this->db->where('id_jenis_timeline', $jenis_timeline);
    $this->db->where($this->id, $nomor_produksi);
    return $this->db->get($this->table_posi)->result();
  }

  function check_posi_jenis_timeline($jenis_timeline)
  {
    $this->db->where('id_jenis_timeline', $jenis_timeline);
    return $this->db->get($this->table_posi)->result();
  }

  function check_detail_timeline_jenis_timeline($jenis_timeline)
  {
    $this->db->where('id_jenis_timeline', $jenis_timeline);
    return $this->db->get($this->table_detail)->result();
  }

  function get_bahan_by_id($id)
  {
    $this->db->where($this->id_bahan_kemas, $id);
    return $this->db->get($this->table_bahan_kemas)->row();
  }

  function get_detail_po_by_po_bahan_row($id,$bahan)
  {
    $this->db->where($this->id_po, $id);
    $this->db->where($this->id_bahan_kemas, $bahan);
    return $this->db->get($this->table_detail_po)->row();
  }

  function get_by_id($id)
  {
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_all_timeline_produksi_by_id_detail($id)
  {
    $this->db->join($this->table, 'timeline_produksi.no_timeline_produksi = detail_timeline_produksi.no_timeline_produksi');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_timeline_produksi.id_bahan_kemas');
    $this->db->where('id_detail_timeline_produksi', $id);
    return $this->db->get($this->table_detail)->row();
  }

  public function get_no_po($id)
  {
    $this->db->join($this->table_detail_po, 'detail_po.no_po = po.no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_po.id_bahan_kemas');
    $this->db->where_not_in('po.id_kategori_po', 6);
    $this->db->where('status_po', 2);
    $query = $this->db->get_where($this->table_po, array('detail_po.no_po' => $id));
    return $query->result_object();
  }

  public function get_no_po_stok($id)
  {
    $this->db->join($this->table_po, 'po.no_po = stok_pabrik.no_po');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = stok_pabrik.id_bahan_kemas');
    $query = $this->db->get_where($this->table_stok, array('stok_pabrik.no_po' => $id));
    return $query->result_object();
  }

  public function get_id_bahan($id, $no_po)
  {
    $this->db->join($this->table_detail_po, 'detail_po.id_bahan_kemas = bahan_kemas.id_bahan_kemas');
    $query = $this->db->get_where($this->table_bahan_kemas, array('detail_po.id_bahan_kemas' => $id, 'no_po' => $no_po));
    return $query->row_array();
  }

  public function get_id_bahan_stok($id, $no_po)
  {
    $this->db->join($this->table_po, 'po.no_po = stok_pabrik.no_po');
    $this->db->join($this->table_detail_po, 'detail_po.no_po = po.no_po');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = stok_pabrik.id_bahan_kemas');
    $query = $this->db->get_where($this->table_stok, array('stok_pabrik.id_bahan_kemas' => $id, 'stok_pabrik.no_po' => $no_po));
    return $query->row_array();
  }

  function insert($data)
  {
    $this->db->insert($this->table, $data);
  }

  function insert_detail($data)
  {
    $this->db->insert($this->table_detail, $data);
  }

  function insert_hpp($data)
  {
    $this->db->insert($this->table_hpp, $data);
  }

  function insert_detail_hpp($data)
  {
    $this->db->insert($this->table_detail_hpp, $data);
  }

  function insert_propo($data)
  {
    $this->db->insert($this->table_propo, $data);
  }

  function insert_posi($data)
  {
    $this->db->insert($this->table_posi, $data);
  }

  function insert_stok($data)
  {
    $this->db->insert($this->table_stok, $data);
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

  function update_posi($id,$data)
  {
    $this->db->where($this->id_posi, $id);
    $this->db->update($this->table_posi, $data);
  }

  function update_stok($id,$data)
  {
    $this->db->where($this->id_stok, $id);
    $this->db->update($this->table_stok, $data);
  }

  function update_posi_by_po_bahan_produksi($po, $bahan, $produksi)
  {
    $this->db->where($this->id_po, $po);
    $this->db->where($this->id_bahan_kemas, $bahan);
    $this->db->where($this->id, $produksi);
    $this->db->update($this->table_posi, $data);
  }

  function updatePO($id,$data)
  {
    $this->db->where($this->id_po, $id);
    $this->db->update($this->table_po, $data);
  }

  function update_detailPO($id,$data)
  {
    $this->db->where($this->id_po, $id);
    $this->db->update($this->table_detail_po, $data);
  }

  function updateTimeline($id,$data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->table, $data);
  }

  function updateDetailTimeline($id,$data)
  {
    $this->db->where($this->id_detail, $id);
    $this->db->update($this->table_detail, $data);
  }

  function update_detailPO_by_bahan($id,$bahan,$data)
  {
    $this->db->where($this->id_po, $id);
    $this->db->where($this->id_bahan_kemas, $bahan);
    $this->db->update($this->table_detail_po, $data);
  }

  function updated_time($id)
  {
    date_default_timezone_set("Asia/Jakarta");
    $now = date('Y-m-d H:i:s');

    $this->db->where($this->id, $id);
    $this->db->set('updated_time', $now);
    $this->db->update($this->table);
  }

  function update_produk($id,$data)
  {
    $this->db->where($this->id_bahan_kemas, $id);
    $this->db->update($this->table_bahan_kemas, $data);
  }

  function delete($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table);
  }

  function delete_stok($id)
  {
    $this->db->where($this->id_stok, $id);
    $this->db->delete($this->table_stok);
  }

  function delete_hpp($id)
  {
    $this->db->where($this->id_hpp, $id);
    $this->db->delete($this->table_hpp);
  }

  function delete_detail_hpp($id)
  {
    $this->db->where($this->id_hpp, $id);
    $this->db->delete($this->table_detail_hpp);
  }

  function delete_posi_by_detail_timeline($id)
  {
    $this->db->where($this->id_detail, $id);
    $this->db->delete($this->table_posi);
  }

  function delete_propo_by_detail_timeline($id)
  {
    $this->db->where($this->id_detail, $id);
    $this->db->delete($this->table_propo);
  }

  function delete_posi_by_id($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table_posi);
  }

  function delete_propo_by_id($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table_propo);
  }

  function delete_detail($id)
  {
    $this->db->where($this->id_detail, $id);
    $this->db->delete($this->table_detail);
  }

  function delete_detail_by_timeline($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table_detail);
  }

}

/* End of file Timeline_produksi_model.php */
/* Location: ./application/models/Timeline_produksi_model.php */