<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk_model extends CI_Model {

  public $table = 'produk';
  public $id    = 'id_produk';
  public $table_satuan= 'satuan';
  public $id_satuan   = 'id_satuan';
  public $table_sku= 'sku';
  public $id_sku   = 'id_sku';
  public $order = 'DESC';

  function get_all()
  {
    $this->db->join('sku', 'sku.id_sku = produk.id_sku');
    $this->db->join('satuan', 'satuan.id_satuan = produk.id_satuan');
    return $this->db->get($this->table)->result();
  }

  function get_all_satuan()
  {
    $this->db->order_by('nama_satuan');
    $data = $this->db->get($this->table_satuan);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Satuan Produk -';
        $result[$row['id_satuan']] = $row['nama_satuan'];
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
        $result[$row['id_sku']] = $row['kode_sku']." - ".$row['nama_sku'];
      }
      return $result;
    }
  }

  function get_all_combobox()
  {
    $this->db->order_by('nama_produk', 'asc');
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Produk -';
        $result[$row['id_produk']] = $row['nama_produk'];
      }
      return $result;
    }
  }

  function get_all_combobox_where_not_in($id)
  {
    $this->db->order_by('nama_produk', 'asc');
    $this->db->where_not_in($this->id, $id);
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Produk -';
        $result[$row['id_produk']] = $row['nama_produk'];
      }
      return $result;
    }
  }

  function get_all_produk_by_sku($id)
  {
    $this->db->order_by('nama_produk', 'asc');
    $this->db->join('sku', 'sku.id_sku = produk.id_sku');
    if ($id != 'semua') {
      $this->db->where('produk.id_sku', $id);  
    }
    return $this->db->get($this->table)->result();
  }

  function get_all_produk_by_sku_in($id)
  {
    $this->db->order_by('nama_produk', 'asc');
    $this->db->join('sku', 'sku.id_sku = produk.id_sku');
    $this->db->where_in('produk.id_sku', $id);  
    return $this->db->get($this->table)->result();
  }

  function get_all_produk_by_toko($id)
  {
    $this->db->order_by('nama_produk', 'asc');
    $this->db->join('tokpro_data_access', 'tokpro_data_access.id_produk = produk.id_produk');
    $this->db->join('sku', 'sku.id_sku = produk.id_sku');
    $this->db->where('id_toko', $id);
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Nama Produk -';
        $result[$row['id_produk']] = $row['kode_sku']." | ".$row['sub_sku']." | ".$row['nama_produk']." | Stok: ".$row['qty_produk'];
      }
      return $result;
    }
  }

  function get_by_id($id)
  {
    $this->db->where($this->id, $id);
    return $this->db->get($this->table)->row();
  }

  function get_all_by_id($id)
  {
    $this->db->select('*, propak_data_access.id_paket as paket_utama');
    $this->db->join('propak_data_access', 'propak_data_access.id_produk = produk.id_produk');
    $this->db->join('paket', 'paket.id_paket = propak_data_access.id_paket');
    $this->db->join('pakduk_data_access', 'pakduk_data_access.id_paket = paket.id_paket');
    $this->db->where('produk.id_produk', $id);
    return $this->db->get($this->table)->row();
  }

  function get_all_by_id_result($id)
  {
    $this->db->join('propak_data_access', 'propak_data_access.id_produk = produk.id_produk');
    $this->db->join('paket', 'paket.id_paket = propak_data_access.id_paket');
    $this->db->join('pakduk_data_access', 'pakduk_data_access.id_paket = paket.id_paket');
    $this->db->where('produk.id_produk', $id);
    return $this->db->get($this->table)->result();
  }

  function get_produk_paket_by_id($id)
  {
    $this->db->select('*');
    $this->db->select('pakduk_data_access.id_produk as produk_id');
    $this->db->join('pakduk_data_access', 'pakduk_data_access.id_paket = propak_data_access.id_paket');
    $this->db->join('produk', 'produk.id_produk = pakduk_data_access.id_produk');
    $this->db->where('propak_data_access.id_produk', $id);
    return $this->db->get('propak_data_access')->result();
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

  function update_in($id,$data)
  {
    $this->db->where_in($this->id, explode(",", $id));
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

/* End of file Produk_model.php */
/* Location: ./application/models/Produk_model.php */