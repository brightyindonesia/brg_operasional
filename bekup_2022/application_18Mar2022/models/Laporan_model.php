<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {
  public $table = 'report';
  public $id    = 'id_report';

  public $table_users = 'users';
  public $id_users    = 'id_users';

  public $table_usertype = 'usertype';
  public $id_usertype    = 'usertype';

  public $table_toko = 'toko';
  public $id_toko = 'id_toko';
  public $order = 'DESC';

  public $column_order = array(null, 'created', 'name', 'created', 'usertype_name',null,null); //field yang ada di table user
  public $column_search = array('name','usertype_name', 'report_data', 'created'); //field yang diizin untuk pencarian 
  public $order_data = array('name' => 'asc'); // default order

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

      $this->db->order_by('created', 'desc');
      $this->db->select('*');
      $this->db->join($this->table_users, 'users.id_users = report.id_users');
      $this->db->join($this->table_usertype, 'usertype.id_usertype = report.usertype');
      $this->db->from($this->table);

      if ($_GET['usertype'] != 'semua' AND $_GET['usertype'] != '' AND $_GET['usertype'] != NULL) {
	     $this->db->where('report.usertype', $_GET['usertype']); 
	    }
      $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $start,
                                "date_format(created, '%Y-%m-%d') <="   => $end
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

  function get_all()
  {
    return $this->db->get($this->table)->result();
  }

  function get_all_crm()
  {
  	$this->db->where('usertype', 7);
    return $this->db->get($this->table)->result();
  }

  function get_all_toko_only()
  {
    $this->db->order_by('nama_toko');
    $this->db->where_not_in($this->id_toko, array(20, 26, 30, 31));
    $data = $this->db->get($this->table_toko);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result[$row['id_toko']] = $row['nama_toko'];
      }
      return $result;
    }
  }

  function get_all_toko_list()
  {
    $this->db->order_by('nama_toko');
    $this->db->where_not_in($this->id_toko, array(20, 26, 30, 31));
    $data = $this->db->get($this->table_toko);

    if($data->num_rows() > 0)
    {
      foreach($data->result_array() as $row)
      {
        $result['semua'] = '- Semua Data -';
        $result[$row['id_toko']] = $row['nama_toko'];
      }
      return $result;
    }
  }

  // function get_all_combobox()
  // {
  //   $this->db->order_by('testing_name');
  //   $data = $this->db->get($this->table);

  //   if($data->num_rows() > 0)
  //   {
  //     foreach($data->result_array() as $row)
  //     {
  //       $result[''] = '- Please Choose Testing';
  //       $result[$row['id_testing']] = $row['testing_name'];
  //     }
  //     return $result;
  //   }
  // }
  function get_by_users_periodik_row($users, $isi, $first, $last)
  {
    $this->db->join($this->table_users, 'users.id_users = report.id_users');
    $this->db->join($this->table_usertype, 'usertype.id_usertype = report.usertype');
    $this->db->where('report.id_users', $users);
    $this->db->where('report_data', $isi);
    $this->db->where( array(  "date_format(date_first, '%Y-%m-%d') ="   => $first,
                              "date_format(date_first, '%Y-%m-%d') ="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_by_users_now_row($users, $isi, $now)
  {
    $this->db->join($this->table_users, 'users.id_users = report.id_users');
    $this->db->join($this->table_usertype, 'usertype.id_usertype = report.usertype');
    $this->db->where('report.id_users', $users);
    $this->db->where('report_data', $isi);
    $this->db->where( array(  "date_format(created, '%Y-%m-%d') ="   => $now
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_by_usertype_periodik_row($usertype, $isi, $first, $last)
  {
    $this->db->join($this->table_users, 'users.id_users = report.id_users');
    $this->db->join($this->table_usertype, 'usertype.id_usertype = report.usertype');
  	$this->db->where('report.usertype', $usertype);
  	$this->db->where('report_data', $isi);
  	$this->db->where( array(  "date_format(date_first, '%Y-%m-%d') ="   => $first,
                              "date_format(date_first, '%Y-%m-%d') ="   => $last
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_by_usertype_now_row($usertype, $isi, $now)
  {
    $this->db->join($this->table_users, 'users.id_users = report.id_users');
    $this->db->join($this->table_usertype, 'usertype.id_usertype = report.usertype');
    $this->db->where('report.usertype', $usertype);
    $this->db->where('report_data', $isi);
    $this->db->where( array(  "date_format(created, '%Y-%m-%d') ="   => $now
                            ));
    return $this->db->get($this->table)->row();
  }

  function get_penjualan_by_periodik($start, $end)
  {
    $this->db->order_by('tgl_penjualan', 'desc');
    $this->db->join('users', 'users.id_users = penjualan.id_users');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
                    ));
    return $this->db->get('penjualan')->result();
  }

  function get_hpp_sku_by_periodik($start, $end)
  {
    $this->db->select('*');
    $this->db->select('SUM(qty) as sum_qty');
    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
    $this->db->group_by('detail_penjualan.id_produk');
    $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
    ));

    return $this->db->get('penjualan')->result();
  }

  public function get_pendapat_periodik_penjualan($first, $last)
  {
    $this->db->order_by('tgl_penjualan', 'asc');
    $this->db->select('date_format(tgl_penjualan, "%d %M %Y") as "tanggal"');
    $this->db->select('tgl_penjualan');
    $this->db->select('sum(total_jual) as total');
    $this->db->select('sum(total_hpp) as tot_hpp');
    $this->db->select('sum(jumlah_diterima) as diterima');
    $this->db->select('sum(ongkir) as tot_ongkir');
    $this->db->select('(sum(total_jual)) - (sum(total_hpp)) - (sum(ongkir)) as fix');
    $this->db->where_not_in('id_status_transaksi', 4);
    // $this->db->where( array(  "created >="   => $first,
  //                                "created <="   => $last
  //                       ));
    $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
                              ));
    $this->db->group_by('tanggal');
    return $this->db->get($this->table_penjualan)->result();
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

  function total_rows_dasbor($first, $last, $usertype)
  {
  	if ($usertype!='semua' AND $usertype!='' AND $usertype!=NULL) {
  		$this->db->where('usertype', $usertype);	
  	}
  	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
                              "date_format(created, '%Y-%m-%d') <="   => $last
                            ));
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

/* End of file Laporan_model.php */
/* Location: ./application/models/Laporan_model.php */