<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timeline_bahan_model extends CI_Model {

  public $table             = 'timeline_bahan';
  public $id                = 'no_timeline_bahan';
  public $table_detail      = 'detail_timeline_bahan';
  public $id_detail         = 'id_detail_timeline_bahan';
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
  public $order             = 'DESC';

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
    $this->db->join($this->table_po, 'po.no_po = timeline_bahan.no_po');
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
      $this->db->where('status_timeline_bahan', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_timeline_bahan, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_timeline_bahan, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->result();
  }

  function get_dasbor_list($vendor, $kategori_po, $status, $first, $last)
  {
    $this->db->select('COUNT(no_timeline_bahan) as "total"');
    $this->db->select('COUNT(CASE WHEN status_timeline_bahan = 0 THEN 1 END) as "proses"');
    $this->db->select('COUNT(CASE WHEN status_timeline_bahan = 1 THEN 1 END) as "sudah"');
    $this->db->join($this->table_po, 'po.no_po = timeline_bahan.no_po');
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
      $this->db->where('status_timeline_bahan', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_timeline_bahan, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_timeline_bahan, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_detail_ajax_datatable($id){
    $this->db->select('detail_timeline_bahan.id_jenis_timeline, nama_jenis_timeline, start_date_detail_timeline_bahan, end_date_detail_timeline_bahan');
    $this->db->join($this->table_timeline, 'detail_timeline_bahan.id_jenis_timeline = jenis_timeline.id_jenis_timeline');
    $this->db->group_by('detail_timeline_bahan.id_jenis_timeline');
    return $this->db->get($this->table_detail)->result();
  }

  function get_all()
  {
    $this->db->join($this->table_po, 'po.no_po = timeline_bahan.no_po');
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
    $this->db->where_not_in('po.id_kategori_po', 6);
    $this->db->where('status_po', 0);
    return $this->db->get($this->table_po)->result();
  }

  function get_all_kategori()
  {
    $this->db->order_by('nama_kategori_po');
    $this->db->where_not_in($this->id_kategori, 6);
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
    $this->db->join($this->table_po, 'po.no_po = timeline_bahan.no_po');
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_detail_by_timeline($id)
  {
    $this->db->order_by('start_date_detail_timeline_bahan', 'asc');
    $this->db->join($this->table_timeline, 'jenis_timeline.id_jenis_timeline = detail_timeline_bahan.id_jenis_timeline');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_timeline_bahan.id_bahan_kemas');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table_detail)->result();
  }

  function get_detail_by_timeline_id_row($id, $timeline)
  {
    $this->db->order_by('start_date_detail_timeline_bahan', 'asc');
    $this->db->join($this->table, 'timeline_bahan.no_timeline_bahan = detail_timeline_bahan.no_timeline_bahan');
    $this->db->join($this->table_po, 'po.no_po = timeline_bahan.no_po');
    $this->db->join($this->table_detail_po, 'po.no_po = detail_po.no_po');
    $this->db->where('timeline_bahan.no_timeline_bahan', $timeline);
    $this->db->where($this->id_detail, $id);
    return $this->db->get($this->table_detail)->row();
  }

  function get_detail_by_timeline_row($id)
  {
    $this->db->order_by('start_date_detail_timeline_bahan', 'asc');
    $this->db->join($this->table_timeline, 'jenis_timeline.id_jenis_timeline = detail_timeline_bahan.id_jenis_timeline');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_timeline_bahan.id_bahan_kemas');
    $this->db->where($this->id_detail, $id);
    return $this->db->get($this->table_detail)->row();
  }

  function get_detail_all_by_timeline_row($id)
  {
    $this->db->select('*');
    $this->db->select('detail_timeline_bahan.id_bahan_kemas as bahan_id');
    $this->db->join($this->table, 'timeline_bahan.no_timeline_bahan = detail_timeline_bahan.no_timeline_bahan');
    $this->db->join($this->table_po, 'po.no_po = timeline_bahan.no_po');
    $this->db->join($this->table_detail_po, 'timeline_bahan.no_po = detail_po.no_po AND detail_po.id_bahan_kemas = detail_timeline_bahan.id_bahan_kemas');
    $this->db->where($this->id_detail, $id);
    return $this->db->get($this->table_detail)->row();
  }

  function get_detail_all_by_timeline_bahan_row($id)
  {
    $this->db->join($this->table, 'timeline_bahan.no_timeline_bahan = detail_timeline_bahan.no_timeline_bahan');
    $this->db->join($this->table_po, 'po.no_po = timeline_bahan.no_po');
    $this->db->join($this->table_detail_po, 'po.no_po = detail_po.no_po');
    $this->db->where('timeline_bahan.no_timeline_bahan', $id);
    return $this->db->get($this->table_detail)->row();
  }

  function get_bahan_by_po($id)
  {
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_po.id_bahan_kemas');
    $this->db->where($this->id_po, $id);
    return $this->db->get($this->table_detail_po)->result();
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

  function updatePO($id,$data)
  {
    $this->db->where($this->id_po, $id);
    $this->db->update($this->table_po, $data);
  }

  function updateTimeline($id,$data)
  {
    $this->db->where($this->id, $id);
    $this->db->update($this->table, $data);
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

  function delete_detail($id)
  {
    $this->db->where($this->id_detail, $id);
    $this->db->delete($this->table_detail);
  }

}

/* End of file Timeline_bahan_model.php */
/* Location: ./application/models/Timeline_bahan_model.php */