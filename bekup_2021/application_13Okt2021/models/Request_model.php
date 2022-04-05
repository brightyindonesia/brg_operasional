<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_model extends CI_Model {

  public $table 		          = 'request';
  public $id    		          = 'no_request';
  public $table_detail 	      = 'detail_request';
  public $table_vendor 	      = 'vendor';
  public $table_sku 	        = 'sku';
  public $table_kategori      = 'kategori_po';
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

  function get_all_kategori()
  {
    $this->db->order_by('nama_kategori_po');
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
    $this->db->join($this->table_sku, 'sku.id_sku = request.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = request.id_vendor');
    return $this->db->get($this->table)->result();
  }

  function get_all_by_id_row($id)
  {
    $this->db->join($this->table_sku, 'sku.id_sku = request.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = request.id_vendor');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_all_full()
  {
    $this->db->join($this->table_sku, 'sku.id_sku = request.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = request.id_vendor');
    $this->db->join($this->table_detail, 'detail_request.no_request = request.no_request');
    return $this->db->get($this->table)->result();
  }

  function get_all_full_detail_by_id($id)
  {
    $this->db->join($this->table_sku, 'sku.id_sku = request.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = request.id_vendor');
    $this->db->join($this->table_detail, 'detail_request.no_request = request.no_request');
    $this->db->where('request.no_request', $id);
    return $this->db->get($this->table)->result();
  }

  function get_detail_by_id($id)
  {
    $this->db->join('bahan_kemas', 'bahan_kemas.id_bahan_kemas = detail_request.id_bahan_kemas');
    $this->db->join('satuan', 'satuan.id_satuan = bahan_kemas.id_satuan');
    $this->db->where($this->id, $id);
    return $this->db->get($this->table_detail)->result();
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

  public function cari_nomor($nomor)
  {
    $this->db->order_by('no_request', $this->order);
    $this->db->like('no_request', $nomor, 'BOTH');
    return $this->db->get($this->table)->row();
  }

	public function get_id_vendor($id)
  {
    $this->db->order_by('nama_bahan_kemas', 'asc');
    $this->db->join($this->table_venmas, 'venmas_data_access.id_vendor = vendor.id_vendor');
    $this->db->join($this->table_bahan_kemas, 'venmas_data_access.id_bahan_kemas = bahan_kemas.id_bahan_kemas');
    $query = $this->db->get_where($this->table_vendor, array('vendor.id_vendor' => $id));
    return $query->result_object();
  }

  public function get_id_bahan_kemas($id)
  {
    $query = $this->db->get_where($this->table_bahan_kemas, array('id_bahan_kemas' => $id));
    return $query->row_array();
  }

}

/* End of file Request_model.php */
/* Location: ./application/models/Request_model.php */