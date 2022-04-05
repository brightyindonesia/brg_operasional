<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori_kasus_model extends CI_Model {

  public $table = 'kategori_kasus';
  public $id    = 'id_kategori_kasus';
  public $order = 'DESC';

  public $column_order = array(null, 'nama_kategori_kasus',null,null); //field yang ada di table user
  public $column_search = array('nama_kategori_kasus'); //field yang diizin untuk pencarian 
  public $order_data = array('nama_kategori_kasus' => 'asc'); // default order

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

      $this->db->order_by('nama_kategori_kasus', 'asc');
      $this->db->select('*');
      $this->db->from($this->table);
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

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_all_combobox()
  {
    $this->db->order_by('nama_kategori_kasus');
    $data = $this->db->get($this->table);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[''] = '- Pilih Kategori Kasus -';
        $result[$row['id_kategori_kasus']] = $row['nama_kategori_kasus'];
      }
      return $result;
    }
  }

  function get_all_combobox_in($id)
  {
    $this->db->order_by('nama_kategori_kasus');
    $this->db->where_in($this->id, $id);
    $data = $this->db->get($this->table);

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

  function delete_in($id)
  {
    $this->db->where_in($this->id, explode(",", $id));
    $this->db->delete($this->table);
  }

}

/* End of file Kategori_kasus_model.php */
/* Location: ./application/models/Kategori_kasus_model.php */