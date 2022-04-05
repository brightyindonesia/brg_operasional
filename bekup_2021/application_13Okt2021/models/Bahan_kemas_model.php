<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bahan_kemas_model extends CI_Model {

  public $table       = 'bahan_kemas';
  public $id          = 'id_bahan_kemas';
  public $table_satuan= 'satuan';
  public $id_satuan   = 'id_satuan';
  public $table_vendor= 'vendor';
  public $id_vendor   = 'id_vendor';
  public $order       = 'DESC';

  function get_all()
  {
    $this->db->join('satuan', 'satuan.id_satuan = bahan_kemas.id_satuan');
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
        $result[''] = '- Pilih Satuan Bahan Kemas -';
        $result[$row['id_satuan']] = $row['nama_satuan'];
      }
      return $result;
    }
  }

  function get_all_vendor()
  {
    $this->db->order_by('nama_vendor', 'asc');
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

  function get_all_kemas_by_vendor($id)
  {
    $this->db->order_by('nama_bahan_kemas');
    $this->db->join('venmas_data_access', 'venmas_data_access.id_bahan_kemas = bahan_kemas.id_bahan_kemas');
    $this->db->where('id_vendor', $id);
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Nama Bahan Kemas -';
        $result[$row['id_bahan_kemas']] = $row['kode_sku_bahan_kemas']." | ".$row['nama_bahan_kemas']." (".$row['keterangan'].")";
      }
      return $result;
    }
  }

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

}

/* End of file Bahan_kemas_model.php */
/* Location: ./application/models/Bahan_kemas_model.php */