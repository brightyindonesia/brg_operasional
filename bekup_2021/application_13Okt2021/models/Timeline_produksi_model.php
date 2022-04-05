<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produksi_model extends CI_Model {

  public $table             = 'produksi';
  public $id                = 'nomor_produksi';
  public $table_po 		      = 'po';
  public $id_po    		      = 'no_po';
  public $table_detail_po      = 'detail_po';
  public $table_vendor 	      = 'vendor';
  public $table_sku 	      = 'sku';
  public $table_kategori      = 'kategori_po';
  public $id_kategori_po	  = 'id_kategori_po';
  public $table_bahan_kemas   = 'bahan_kemas';
  public $table_venmas        = 'venmas_data_access';
  public $order               = 'DESC';

  function get_all_vendor()
  {
    $this->db->order_by('nama_vendor');
    $data = $this->db->get($this->table_vendor);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Vendor -';
        $result[$row['id_vendor']] = $row['nama_vendor'];
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

  function get_all_po()
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

  function get_all_kategori()
  {
    $this->db->order_by('nama_kategori_po');
    $this->db->where_not_in('id_kategori_po', 6);
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

  function get_all()
  {
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    return $this->db->get($this->table_po)->result();
  }

  function get_all_by_produksi()
  {
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->where($this->id_kategori_po, 6);
    $this->db->where('status_po', 0);
    return $this->db->get($this->table_po)->result();
  }

  function get_all_by_id_row($id)
  {
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->where($this->id_po, $id);
    return $this->db->get($this->table_po)->row();
  }

  function get_all_full()
  {
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_detail, 'detail_po.no_po = po.no_po');
    return $this->db->get($this->table_po)->result();
  }

  function get_all_full_detail_by_id($id)
  {
    $this->db->join($this->table_sku, 'sku.id_sku = po.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = po.id_vendor');
    $this->db->join($this->table_detail, 'detail_po.no_po = po.no_po');
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_po.id_bahan_kemas');
    $this->db->where('po.no_po', $id);
    return $this->db->get($this->table_po)->result();
  }

  function get_detail_by_id($id)
  {
    $this->db->join('bahan_kemas', 'bahan_kemas.id_bahan_kemas = detail_po.id_bahan_kemas');
    $this->db->join('satuan', 'satuan.id_satuan = bahan_kemas.id_satuan');
    $this->db->where($this->id_po, $id);
    return $this->db->get($this->table_detail_po)->result();
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
    $this->db->where($this->id_po, $id);
    return $this->db->get($this->table_po)->row();
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

  function delete_detail($id)
  {
    $this->db->where($this->id, $id);
    $this->db->delete($this->table_detail);
  }

	public function get_id_kategori($id)
  {
    $this->db->order_by('no_po', 'asc');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = po.id_kategori_po');
    $this->db->where('status_po', 0);
    $query = $this->db->get_where($this->table_po, array('po.id_kategori_po' => $id));
    return $query->result_object();
  }

  public function get_id_po($id)
  {
    $this->db->join($this->table_bahan_kemas, 'bahan_kemas.id_bahan_kemas = detail_po.id_bahan_kemas');
    $query = $this->db->get_where($this->table_detail_po, array('no_po' => $id));
    return $query->result_object();
  }	

  public function get_id_bahan_kemas($id)
  {
    $this->db->join($this->table_detail_po, 'bahan_kemas.id_bahan_kemas = detail_po.id_bahan_kemas');
    $query = $this->db->get_where($this->table_bahan_kemas, array('detail_po.id_bahan_kemas' => $id));
    return $query->row_array();
  }
	

}

/* End of file Produksi_model.php */
/* Location: ./application/models/Produksi_model.php */