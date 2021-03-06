<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_model extends CI_Model {

  public $table 		          = 'request';
  public $id    		          = 'no_request';
  public $table_penerima      = 'penerima';
  public $id_penerima         = 'id_penerima';
  public $table_detail 	      = 'detail_request';
  public $table_po            = 'po';
  public $id_po               = 'no_po';
  public $table_detail_po     = 'detail_po';
  public $table_vendor 	      = 'vendor';
  public $table_sku 	        = 'sku';
  public $table_kategori      = 'kategori_po';
  public $table_bahan_kemas   = 'bahan_kemas';
  public $table_venmas        = 'venmas_data_access';
  public $order               = 'DESC';

  public $column_order = array(null, 'tgl_request', 'no_request', 'nama_vendor', null); //field yang ada di table user
  public $column_search = array('no_request', 'nama_vendor', 'nama_sku'); //field yang diizin untuk pencarian 
  public $order_data = array('tgl_request' => 'asc'); // default order 

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
          $this->db->order_by($this->column_order[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
      } 
      else if(isset($this->order_data))
      {
          $order = $this->order_data;
          $this->db->order_by(key($order), $order[key($order)]);
      }

      $start = substr($_GET['periodik'], 0, 10);
      $end = substr($_GET['periodik'], 13, 24);
      $this->db->join($this->table_sku, 'sku.id_sku = request.id_sku');
      $this->db->join($this->table_vendor, 'vendor.id_vendor = request.id_vendor');
      $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = request.id_kategori_po');
      $this->db->join($this->table_penerima, 'penerima.id_penerima = request.id_penerima');
      $this->db->from($this->table);

      if ($_GET['vendor'] != 'semua') {
        $this->db->where('request.id_vendor', $_GET['vendor']); 
      }
      if ($_GET['kategori'] != 'semua') {
        $this->db->where('request.id_kategori_po', $_GET['kategori']); 
      }
      if ($_GET['status'] != 'semua') {
        $this->db->where('status_request', $_GET['status']); 
      }
      // $this->db->where( array(  "tgl_resi >="   => $first,
      //                           "tgl_resi <="   => $last
      //                         ));
      $this->db->where( array(  "date_format(tgl_request, '%Y-%m-%d') >="   => $start,
                                "date_format(tgl_request, '%Y-%m-%d') <="   => $end
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
  // End Table Server Side

  function get_dasbor_list($vendor, $kategori_po, $status, $first, $last)
  {
    $this->db->select('COUNT(no_request) as "total"');
    $this->db->select('COUNT(CASE WHEN status_request = 0 THEN 1 END) as "proses"');
    $this->db->select('COUNT(CASE WHEN status_request = 1 THEN 1 END) as "sudah"');
    $this->db->join($this->table_sku, 'sku.id_sku = request.id_sku');
    $this->db->join($this->table_vendor, 'vendor.id_vendor = request.id_vendor');
    $this->db->join($this->table_kategori, 'kategori_po.id_kategori_po = request.id_kategori_po');
    $this->db->join($this->table_penerima, 'penerima.id_penerima = request.id_penerima');
    if ($vendor != 'semua') {
      $this->db->where('request.id_vendor', $vendor); 
    }
    if ($kategori_po != 'semua') {
      $this->db->where('request.id_kategori_po', $kategori_po); 
    }
    if ($status != 'semua') {
      $this->db->where('status_request', $status); 
    }
    // $this->db->where( array(  "tgl_resi >="   => $first,
    //                           "tgl_resi <="   => $last
    //                         ));
    $this->db->where( array(  "date_format(tgl_request, '%Y-%m-%d') >="   => $first,
                              "date_format(tgl_request, '%Y-%m-%d') <="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

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

  function get_detail_po_by_id($id)
  {
    $this->db->join('bahan_kemas', 'bahan_kemas.id_bahan_kemas = detail_request.id_bahan_kemas');
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