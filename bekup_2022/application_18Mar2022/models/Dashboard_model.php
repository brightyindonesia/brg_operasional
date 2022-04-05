<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
	public $table_penjualan  = 'penjualan';
	public $table_detail_penjualan  = 'detail_penjualan';
	public $nomor_pesanan    = 'nomor_pesanan';
	public $table_produk  	 = 'produk';
	public $id_produk     	 = 'id_produk';
	public $table_bahan    	 = 'bahan_kemas';
 	public $id_bahan       	 = 'id_bahan_kemas';
	public $table_toko  	 = 'toko';
	public $id_toko     	 = 'id_toko';
	public $table_kurir  	 = 'kurir';
	public $id_kurir     	 = 'id_kurir';
	public $table_jenis_toko = 'jenis_toko';
	public $id_jenis_toko    = 'id_jenis_toko';
	public $table_tokpro	 = 'tokpro_data_access';
	public $id_tokpro    	 = 'id_tokpro_access';
	public $table_propak	 = 'propak_data_access';
	public $table_pakduk	 = 'pakduk_data_access';
	public $table_sku		 = 'sku';
	public $id_sku   		 = 'id_sku';
	public $table_resi		 = 'resi';
	public $id_resi   		 = 'id_resi';
	public $table_retur		 = 'retur';
	public $id_retur   		 = 'nomor_retur';

	// ServerSide Repeat Impor
	  public $column_order_bahan_status = array('kode_sku_bahan_kemas', 'nama_bahan_kemas', null, 'qty_bahan_kemas'); //field yang ada di table user
	  public $column_search_bahan_status = array('kode_sku_bahan_kemas', 'nama_bahan_kemas', null, 'qty_bahan_kemas'); //field yang diizin untuk pencarian 
	  public $order_data_bahan_status = array('qty_bahan_kemas' => 'asc'); // default order 


	  public $column_order_produk_status = array('nama_sku','sub_sku', 'nama_produk', null, 'qty_produk'); //field yang ada di table user
	  public $column_search_produk_status = array('nama_sku','sub_sku', 'nama_produk', null, 'qty_produk'); //field yang diizin untuk pencarian 
	  public $order_data_produk_status = array('qty_produk' => 'asc'); // default order 


	  public $column_order = array(null, 'nama_penerima','provinsi', 'kabupaten', 'hp_penerima', 'alamat_penerima', 'jumlah_penerima'); //field yang ada di table user
	  public $column_search = array('nama_penerima','provinsi', 'kabupaten', 'hp_penerima', 'alamat_penerima', 'jumlah_penerima'); //field yang diizin untuk pencarian 
	  public $order_data = array('tgl_penjualan' => 'asc'); // default order 

	  // Count Penjualan
	  public $column_order_count_penjualan = array(null, 'nomor_pesanan','nama_toko', 'nama_kurir', 'nomor_resi'); //field yang ada di table user
	  public $column_search_count_penjualan = array('nomor_pesanan','nama_toko', 'nama_kurir', 'nomor_resi'); //field yang diizin untuk pencarian 
	  public $order_data_count_penjualan = array('tgl_penjualan' => 'asc'); // default order 

	  // Count Resi
	  public $column_order_count_resi = array(null, 'nomor_pesanan','nama_kurir', 'resi.nomor_resi'); //field yang ada di table user
	  public $column_search_count_resi = array('nomor_pesanan','nama_kurir', 'resi.nomor_resi'); //field yang diizin untuk pencarian 
	  public $order_data_count_resi = array('status' => 'asc'); // default order 

	  // Count Retur
	  public $column_order_count_retur = array(null,'nomor_retur', 'retur.nomor_pesanan', 'nomor_resi', null); //field yang ada di table user
	  public $column_search_count_retur = array('retur.nomor_pesanan','nomor_retur', 'nomor_resi'); //field yang diizin untuk pencarian 
	  public $order_data_count_retur = array('tgl_retur' => 'asc'); // default order 

	  // Count Repeat
	  public $column_order_count_repeat = array(null, 'nomor_pesanan','nama_toko', 'nama_kurir', 'nomor_resi'); //field yang ada di table user
	  public $column_search_count_repeat = array('nomor_pesanan','nama_toko', 'nama_kurir', 'nomor_resi'); //field yang diizin untuk pencarian 
	  public $order_data_count_repeat = array('tgl_penjualan' => 'asc'); // default order 


	  // Table Server Side
	  // Total Penjualan
	  private function _get_datatables_query_count_penjualan()
	    {
	      $i = 0;
	   
	      foreach ($this->column_search_count_penjualan as $item) // looping awal
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

	              if(count($this->column_search_count_penjualan) - 1 == $i) 
	                  $this->db->group_end(); 
	          }
	          $i++;
	      }
	       
	      if(isset($_GET['order'])) 
	      {
	          $this->db->order_by($this->column_order_count_penjualan[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
	      } 
	      else if(isset($this->order_data_count_penjualan))
	      {
	          $order = $this->order_data_count_penjualan;
	          $this->db->order_by(key($order), $order[key($order)]);
	      }

	      $start = substr($_GET['periodik'], 0, 10);
	      $end = substr($_GET['periodik'], 13, 24);
	      $this->db->order_by('tgl_penjualan', 'desc');
	      $this->db->select('*');
	      $this->db->select('date_format(tgl_penjualan, "%d-%m-%Y") as tanggal');
	      $this->db->join('users', 'users.id_users = penjualan.id_users');
	      $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
	      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
	      $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
	      $this->db->from($this->table_penjualan);

	      if ($_GET['status'] != 'semua') {
	        $this->db->where('penjualan.id_status_transaksi', $_GET['status']); 
	      }
	      // $this->db->where( array(  "tgl_penjualan >="   => $first,
	      //                           "tgl_penjualan <="   => $last
	                              // ));

	      if ($_GET['trigger'] == 'impor') {
	      	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $start,
		                                "date_format(created, '%Y-%m-%d') <="   => $end
		      ));
	      }elseif ($_GET['trigger'] == 'penjualan') {
	      	$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
		                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
		      ));
	      }
	      
	  }

	  function get_datatables_count_penjualan()
	  {
	      $this->_get_datatables_query_count_penjualan();
	      if($_GET['length'] != -1)
	      $this->db->limit($_GET['length'], $_GET['start']);
	      $query = $this->db->get();
	      return $query->result();
	  }

	  function count_filtered_count_penjualan()
	  {
	      $this->_get_datatables_query_count_penjualan();
	      $query = $this->db->get();
	      return $query->num_rows();
	  }

	  public function count_all_count_penjualan()
	  {
	      $this->db->from($this->table_penjualan);
	      return $this->db->count_all_results();
	  }

	  // Total Resi
	  private function _get_datatables_query_count_resi()
	    {
	      $i = 0;
	   
	      foreach ($this->column_search_count_resi as $item) // looping awal
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

	              if(count($this->column_search_count_resi) - 1 == $i) 
	                  $this->db->group_end(); 
	          }
	          $i++;
	      }
	       
	      if(isset($_GET['order'])) 
	      {
	          $this->db->order_by($this->column_order_count_resi[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
	      } 
	      else if(isset($this->order_data_count_resi))
	      {
	          $order = $this->order_data_count_resi;
	          $this->db->order_by(key($order), $order[key($order)]);
	      }

	      $start = substr($_GET['periodik'], 0, 10);
	      $end = substr($_GET['periodik'], 13, 24);
	      $this->db->order_by('status', 'desc');
	      $this->db->select('*');
	      $this->db->select('date_format(created_resi, "%d-%m-%Y") as tanggal');
	      $this->db->order_by('status', 'asc');
	      $this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
	      $this->db->join('users', 'users.id_users = resi.id_users');
	      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
	      $this->db->from($this->table_resi);

	      if ($_GET['status'] != 'semua') {
	        $this->db->where('status', $_GET['status']); 
	      }
	      // $this->db->where( array(  "tgl_resi >="   => $first,
	      //                           "tgl_resi <="   => $last
	      //                         ));

	      if ($_GET['trigger'] == 'impor') {
	      	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $start,
		                                "date_format(created, '%Y-%m-%d') <="   => $end
		      ));
	      }elseif ($_GET['trigger'] == 'penjualan') {
	      	$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
		                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
		      ));
	      }
	  }

	  function get_datatables_count_resi()
	  {
	      $this->_get_datatables_query_count_resi();
	      if($_GET['length'] != -1)
	      $this->db->limit($_GET['length'], $_GET['start']);
	      $query = $this->db->get();
	      return $query->result();
	  }

	  function count_filtered_count_resi()
	  {
	      $this->_get_datatables_query_count_resi();
	      $query = $this->db->get();
	      return $query->num_rows();
	  }

	  public function count_all_count_resi()
	  {
	      $this->db->from($this->table_resi);
	      return $this->db->count_all_results();
	  }

	  // Retur
	  private function _get_datatables_query_count_retur()
	    {
	      $i = 0;
	   
	      foreach ($this->column_search_count_retur as $item) // looping awal
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

	              if(count($this->column_search_count_retur) - 1 == $i) 
	                  $this->db->group_end(); 
	          }
	          $i++;
	      }
	       
	      if(isset($_GET['order'])) 
	      {
	          $this->db->order_by($this->column_order_count_retur[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
	      } 
	      else if(isset($this->order_data_count_retur))
	      {
	          $order = $this->order_data_count_retur;
	          $this->db->order_by(key($order), $order[key($order)]);
	      }

	      $start = substr($_GET['periodik'], 0, 10);
	      $end = substr($_GET['periodik'], 13, 24);
	      $this->db->order_by('tgl_penjualan', 'desc');
	      $this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
	      $this->db->join('users', 'users.id_users = penjualan.id_users');
	      $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
	      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
	      $this->db->from($this->table_retur);  

	      if ($_GET['status'] != 'semua') {
	        $this->db->where('status_retur', $_GET['status']); 
	      }
	      // $this->db->where( array(  "tgl_penjualan >="   => $first,
	      //                           "tgl_penjualan <="   => $last
	                              // ));

	      if ($_GET['follup'] != null) {
	        $this->db->where('status_follow_up', $_GET['follup']); 
	      }

	      if ($_GET['trigger'] == 'impor') {
	      	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $start,
		                                "date_format(created, '%Y-%m-%d') <="   => $end
		      ));
	      }elseif ($_GET['trigger'] == 'penjualan') {
	      	$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
		                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
		      ));
	      }
	  }

	  function get_datatables_count_retur()
	  {
	      $this->_get_datatables_query_count_retur();
	      if($_GET['length'] != -1)
	      $this->db->limit($_GET['length'], $_GET['start']);
	      $query = $this->db->get();
	      return $query->result();
	  }

	  function count_filtered_count_retur()
	  {
	      $this->_get_datatables_query_count_retur();
	      $query = $this->db->get();
	      return $query->num_rows();
	  }

	  public function count_all_count_retur()
	  {
	      $this->db->from($this->table_retur);
	      return $this->db->count_all_results();
	  }

	  // Total Repeat
	  private function _get_datatables_query_count_repeat()
	    {
	      $i = 0;
	   
	      foreach ($this->column_search_count_repeat as $item) // looping awal
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

	              if(count($this->column_search_count_repeat) - 1 == $i) 
	                  $this->db->group_end(); 
	          }
	          $i++;
	      }
	       
	      if(isset($_GET['order'])) 
	      {
	          $this->db->order_by($this->column_order_count_repeat[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
	      } 
	      else if(isset($this->order_data_count_repeat))
	      {
	          $order = $this->order_data_count_repeat;
	          $this->db->order_by(key($order), $order[key($order)]);
	      }

	      $start = $_GET['first'];
	      $end = $_GET['last'];
	      $this->db->order_by('tgl_penjualan', 'desc');
	      $this->db->select('*');
	      $this->db->select('date_format(tgl_penjualan, "%d-%m-%Y") as tanggal');
	      $this->db->join('users', 'users.id_users = penjualan.id_users');
	      $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
	      $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
	      $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
	      $this->db->from($this->table_penjualan);

	      
	      $this->db->where('hp_penerima', $_GET['hp']); 
	      // $this->db->where( array(  "tgl_penjualan >="   => $first,
	      //                           "tgl_penjualan <="   => $last
	                              // ));

	      if ($_GET['trigger'] == 'impor') {
	      	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $start,
		                                "date_format(created, '%Y-%m-%d') <="   => $end
		      ));
	      }elseif ($_GET['trigger'] == 'penjualan') {
	      	$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $start,
		                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $end
		      ));
	      }
	  }

	  function get_datatables_count_repeat()
	  {
	      $this->_get_datatables_query_count_repeat();
	      if($_GET['length'] != -1)
	      $this->db->limit($_GET['length'], $_GET['start']);
	      $query = $this->db->get();
	      return $query->result();
	  }

	  function count_filtered_count_repeat()
	  {
	      $this->_get_datatables_query_count_repeat();
	      $query = $this->db->get();
	      return $query->num_rows();
	  }

	  public function count_all_count_repeat()
	  {
	      $this->db->from($this->table_penjualan);
	      return $this->db->count_all_results();
	  }
	  // End Table Server Side

	  // DASBOR COUNT IMPOR
	  function get_dasbor_list_penjualan($first, $last)
	  {
		$this->db->select('COUNT(nomor_pesanan) as "total"');
		$this->db->select('COUNT(CASE WHEN id_status_transaksi = 1 THEN 1 END) as "pending"');
		$this->db->select('COUNT(CASE WHEN id_status_transaksi = 2 THEN 1 END) as "transfer"');
		$this->db->select('COUNT(CASE WHEN id_status_transaksi = 3 THEN 1 END) as "diterima"');
		$this->db->select('COUNT(CASE WHEN id_status_transaksi = 4 THEN 1 END) as "retur"');
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
		                              "date_format(created, '%Y-%m-%d') <="   => $last
		                          ));

		return $this->db->get($this->table_penjualan)->row();
	  }

	  function get_dasbor_list_resi($first, $last)
	  {
	  	$this->db->join($this->table_penjualan, 'resi.nomor_resi = penjualan.nomor_resi');
		$this->db->select('COUNT(nomor_pesanan) as "total"');
		$this->db->select('COUNT(CASE WHEN status = 0 THEN 1 END) as "belum"');
		$this->db->select('COUNT(CASE WHEN status = 1 THEN 1 END) as "sedang"');
		$this->db->select('COUNT(CASE WHEN status = 2 THEN 1 END) as "sudah"');
		$this->db->select('COUNT(CASE WHEN status = 3 THEN 1 END) as "retur"');

		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
		                             "date_format(created, '%Y-%m-%d') <="   => $last
		                          ));
		return $this->db->get($this->table_resi)->row();
	  }

	  function get_dasbor_list_retur($first, $last)
	  {
	  	$this->db->join($this->table_penjualan, 'retur.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->select('COUNT(nomor_retur) as "total"');
		$this->db->select('COUNT(CASE WHEN status_retur = 0 THEN 1 END) as "diproses"');
	    $this->db->select('COUNT(CASE WHEN status_retur = 1 THEN 1 END) as "sudah"');
	    $this->db->select('COUNT(CASE WHEN status_follow_up = 0 THEN 1 END) as "belum_fu"');
	    $this->db->select('COUNT(CASE WHEN status_follow_up = 1 THEN 1 END) as "sudah_fu"');
	    $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
		                             "date_format(created, '%Y-%m-%d') <="   => $last
		                          ));

		return $this->db->get($this->table_retur)->row();
	  }

	  // DASBOR COUNT REAL
	  function get_dasbor_list_penjualan_real($first, $last)
	  {
		$this->db->select('COUNT(nomor_pesanan) as "total"');
		$this->db->select('COUNT(CASE WHEN id_status_transaksi = 1 THEN 1 END) as "pending"');
		$this->db->select('COUNT(CASE WHEN id_status_transaksi = 2 THEN 1 END) as "transfer"');
		$this->db->select('COUNT(CASE WHEN id_status_transaksi = 3 THEN 1 END) as "diterima"');
		$this->db->select('COUNT(CASE WHEN id_status_transaksi = 4 THEN 1 END) as "retur"');
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
		                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
		                          ));

		return $this->db->get($this->table_penjualan)->row();
	  }

	  function get_dasbor_list_resi_real($first, $last)
	  {
	  	$this->db->join($this->table_penjualan, 'resi.nomor_resi = penjualan.nomor_resi');
		$this->db->select('COUNT(nomor_pesanan) as "total"');
		$this->db->select('COUNT(CASE WHEN status = 0 THEN 1 END) as "belum"');
		$this->db->select('COUNT(CASE WHEN status = 1 THEN 1 END) as "sedang"');
		$this->db->select('COUNT(CASE WHEN status = 2 THEN 1 END) as "sudah"');
		$this->db->select('COUNT(CASE WHEN status = 3 THEN 1 END) as "retur"');

		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
		                             "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
		                          ));
		return $this->db->get($this->table_resi)->row();
	  }

	  function get_dasbor_list_retur_real($first, $last)
	  {
	  	$this->db->join($this->table_penjualan, 'retur.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->select('COUNT(nomor_retur) as "total"');
		$this->db->select('COUNT(CASE WHEN status_retur = 0 THEN 1 END) as "diproses"');
	    $this->db->select('COUNT(CASE WHEN status_retur = 1 THEN 1 END) as "sudah"');
	    $this->db->select('COUNT(CASE WHEN status_follow_up = 0 THEN 1 END) as "belum_fu"');
	    $this->db->select('COUNT(CASE WHEN status_follow_up = 1 THEN 1 END) as "sudah_fu"');
	    $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
		                             "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
		                          ));

		return $this->db->get($this->table_retur)->row();
	  }

	  // Table Server Side

	  // Produk Status

	  private function _get_datatables_query_produk_status()
	    {
	      $i = 0;
	   
	      foreach ($this->column_search_produk_status as $item) // looping awal
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

	              if(count($this->column_search_produk_status) - 1 == $i) 
	                  $this->db->group_end(); 
	          }
	          $i++;
	      }
	       
	      if(isset($_GET['order'])) 
	      {
	          $this->db->order_by($this->column_order_produk_status[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
	      } 
	      else if(isset($this->order_data_produk_status))
	      {
	          $order = $this->order_data_produk_status;
	          $this->db->order_by(key($order), $order[key($order)]);
	      }

		  $this->db->order_by('qty_produk','asc');
		  $this->db->select('*');
		  $this->db->join($this->table_sku, 'sku.id_sku = produk.id_sku');
		  $this->db->limit(5);
	      $this->db->from($this->table_produk);
	  }

	  function get_datatables_produk_status()
	  {
	      $this->_get_datatables_query_produk_status();
	      if($_GET['length'] != -1)
	      $this->db->limit($_GET['length'], $_GET['start']);
	      $query = $this->db->get();
	      return $query->result();
	  }

	  function count_filtered_produk_status()
	  {
	      $this->_get_datatables_query_produk_status();
	      $query = $this->db->get();
	      return $query->num_rows();
	  }

	  public function count_all_produk_status()
	  {
	      $this->db->limit(5);
	      $this->db->from($this->table_produk);
	      return $this->db->count_all_results();
	  }

	  // Bahan Status

	  private function _get_datatables_query_bahan_status()
	    {
	      $i = 0;
	   
	      foreach ($this->column_search_bahan_status as $item) // looping awal
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

	              if(count($this->column_search_bahan_status) - 1 == $i) 
	                  $this->db->group_end(); 
	          }
	          $i++;
	      }
	       
	      if(isset($_GET['order'])) 
	      {
	          $this->db->order_by($this->column_order_bahan_status[$_GET['order']['0']['column']], $_GET['order']['0']['dir']);
	      } 
	      else if(isset($this->order_data_bahan_status))
	      {
	          $order = $this->order_data_bahan_status;
	          $this->db->order_by(key($order), $order[key($order)]);
	      }

		  $this->db->order_by('qty_bahan_kemas','asc');
		  $this->db->select('*');
		  $this->db->limit(5);
	      $this->db->from($this->table_bahan);
	  }

	  function get_datatables_bahan_status()
	  {
	      $this->_get_datatables_query_bahan_status();
	      if($_GET['length'] != -1)
	      $this->db->limit($_GET['length'], $_GET['start']);
	      $query = $this->db->get();
	      return $query->result();
	  }

	  function count_filtered_bahan_status()
	  {
	      $this->_get_datatables_query_bahan_status();
	      $query = $this->db->get();
	      return $query->num_rows();
	  }

	  public function count_all_bahan_status()
	  {
	      $this->db->limit(5);
	      $this->db->from($this->table_bahan);
	      return $this->db->count_all_results();
	  }

	  // Impor

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
	      else if(isset($this->order))
	      {
	          $order = $this->order;
	          $this->db->order_by(key($order), $order[key($order)]);
	      }

	      $first = substr($_GET['periodik'], 0, 10);
	      $last = substr($_GET['periodik'], 13, 24);
	      $this->db->order_by('jumlah_penerima', 'desc');
		  $this->db->select('COUNT(hp_penerima) as jumlah_penerima');
		  $this->db->select('nama_penerima, hp_penerima, provinsi, kabupaten, alamat_penerima');
		  
		  $this->db->where_not_in('id_status_transaksi', 4);
		  $this->db->group_by('hp_penerima');
		  $this->db->having('jumlah_penerima >', 1);
	      $this->db->from($this->table_penjualan);

	      $this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
		                              "date_format(created, '%Y-%m-%d') <="   => $last
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

	  // Penjualan

	  private function _get_datatables_query_penjualan()
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

	      $first = substr($_GET['periodik'], 0, 10);
	      $last = substr($_GET['periodik'], 13, 24);
	      $this->db->order_by('jumlah_penerima', 'desc');
		  $this->db->select('COUNT(hp_penerima) as jumlah_penerima');
		  $this->db->select('nama_penerima, hp_penerima, provinsi, kabupaten, alamat_penerima');
		  
		  $this->db->where_not_in('id_status_transaksi', 4);
		  $this->db->group_by('hp_penerima');
		  $this->db->having('jumlah_penerima >', 1);
	      $this->db->from($this->table_penjualan);

	      $this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
		                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
		                          ));
	  }

	  function get_datatables_penjualan()
	  {
	      $this->_get_datatables_query_penjualan();
	      if($_GET['length'] != -1)
	      $this->db->limit($_GET['length'], $_GET['start']);
	      $query = $this->db->get();
	      return $query->result();
	  }

	  function count_filtered_penjualan()
	  {
	      $this->_get_datatables_query_penjualan();
	      $query = $this->db->get();
	      return $query->num_rows();
	  }

	  public function count_all()
	  {
	      $this->db->from($this->table_penjualan);
	      return $this->db->count_all_results();
	  }
	// End ServerSide Repeat Impor

	public function get_produk_toko_periodik($first, $last)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('count(produk.id_produk) as jumlah_id');
		$this->db->select('sum(qty) as jumlah_produk');
		$this->db->select('produk.id_produk as produk_id');
		$this->db->select('nama_produk');
		$this->db->select('nama_toko');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('nama_produk');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_produk_toko_periodik_penjualan($first, $last)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('count(produk.id_produk) as jumlah_id');
		$this->db->select('sum(qty) as jumlah_produk');
		$this->db->select('produk.id_produk as produk_id');
		$this->db->select('nama_produk');
		$this->db->select('nama_toko');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('nama_produk');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_produk_toko_periodik_retur($first, $last)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('count(produk.id_produk) as jumlah_id');
		$this->db->select('sum(qty) as jumlah_produk');
		$this->db->select('produk.id_produk as produk_id');
		$this->db->select('nama_produk');
		$this->db->select('nama_toko');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('nama_produk');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_produk_toko_periodik_retur_penjualan($first, $last)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('count(produk.id_produk) as jumlah_id');
		$this->db->select('sum(qty) as jumlah_produk');
		$this->db->select('produk.id_produk as produk_id');
		$this->db->select('nama_produk');
		$this->db->select('nama_toko');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('nama_produk');
		return $this->db->get($this->table_penjualan)->result();
	}

	function get_pakduk_produk_by_produk($id)
	{
		$this->db->select('propak_data_access.id_produk as produk_utama');
		$this->db->select('pakduk_data_access.id_produk as produk_detail');
		$this->db->select('nama_produk, qty_pakduk, sub_sku');
		$this->db->join($this->table_pakduk, 'pakduk_data_access.id_paket = propak_data_access.id_paket');
		$this->db->join($this->table_produk, 'produk.id_produk = pakduk_data_access.id_produk');
		$this->db->where('propak_data_access.id_produk', $id);
		return $this->db->get($this->table_propak)->result();
	}

	public function get_produk_kurir_periodik($first, $last)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('count(produk.id_produk) as jumlah_produk');
		$this->db->select('nama_produk');
		$this->db->select('nama_kurir');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_kurir, 'kurir.id_kurir = penjualan.id_kurir');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_produk');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_toko($first, $last)
	{
		$this->db->order_by('nama_toko', 'desc');
		$this->db->select('count(penjualan.id_toko) as jumlah_toko');
		$this->db->select('nama_toko');
		$this->db->select('penjualan.id_toko as toko_id');
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_toko');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_toko_penjualan($first, $last)
	{
		$this->db->order_by('nama_toko', 'desc');
		$this->db->select('count(penjualan.id_toko) as jumlah_toko');
		$this->db->select('nama_toko');
		$this->db->select('penjualan.id_toko as toko_id');
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_toko');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_jenis_toko($first, $last)
	{
		$this->db->order_by('nama_jenis_toko', 'desc');
		$this->db->select('count(nama_jenis_toko) as jumlah_jenis');
		$this->db->select('nama_jenis_toko');
		$this->db->select('toko.id_jenis_toko as jenis_toko_id');
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_jenis_toko, 'toko.id_jenis_toko = jenis_toko.id_jenis_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_jenis_toko');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_jenis_toko_penjualan($first, $last)
	{
		$this->db->order_by('nama_jenis_toko', 'desc');
		$this->db->select('count(nama_jenis_toko) as jumlah_jenis');
		$this->db->select('nama_jenis_toko');
		$this->db->select('toko.id_jenis_toko as jenis_toko_id');
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_jenis_toko, 'toko.id_jenis_toko = jenis_toko.id_jenis_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_jenis_toko');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_jenis_toko_retur($first, $last)
	{
		$this->db->order_by('nama_jenis_toko', 'desc');
		$this->db->select('count(nama_jenis_toko) as jumlah_jenis');
		$this->db->select('nama_jenis_toko');
		$this->db->select('toko.id_jenis_toko as jenis_toko_id');
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_jenis_toko, 'toko.id_jenis_toko = jenis_toko.id_jenis_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_jenis_toko');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_jenis_toko_retur_penjualan($first, $last)
	{
		$this->db->order_by('nama_jenis_toko', 'desc');
		$this->db->select('count(nama_jenis_toko) as jumlah_jenis');
		$this->db->select('nama_jenis_toko');
		$this->db->select('toko.id_jenis_toko as jenis_toko_id');
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_jenis_toko, 'toko.id_jenis_toko = jenis_toko.id_jenis_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_jenis_toko');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_toko_by_jenis($first, $last, $jenis)
	{
		$this->db->order_by('nama_toko', 'desc');
		$this->db->select('count(CASE WHEN toko.id_jenis_toko = '.$jenis.' THEN 1 END) as jumlah_toko');
		$this->db->select('nama_toko');
		$this->db->select('toko.id_jenis_toko');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_jenis_toko, 'toko.id_jenis_toko = jenis_toko.id_jenis_toko');
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_toko');
		// $this->db->having('penjualan.id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_toko_by_jenis_penjualan($first, $last, $jenis)
	{
		$this->db->order_by('nama_toko', 'desc');
		$this->db->select('count(CASE WHEN toko.id_jenis_toko = '.$jenis.' THEN 1 END) as jumlah_toko');
		$this->db->select('nama_toko');
		$this->db->select('toko.id_jenis_toko');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_jenis_toko, 'toko.id_jenis_toko = jenis_toko.id_jenis_toko');
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_toko');
		// $this->db->having('penjualan.id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_toko_by_jenis_retur($first, $last, $jenis)
	{
		$this->db->order_by('nama_toko', 'desc');
		$this->db->select('count(CASE WHEN toko.id_jenis_toko = '.$jenis.' THEN 1 END) as jumlah_toko');
		$this->db->select('nama_toko');
		$this->db->select('toko.id_jenis_toko');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_jenis_toko, 'toko.id_jenis_toko = jenis_toko.id_jenis_toko');
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_toko');
		// $this->db->having('penjualan.id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_toko_by_jenis_retur_penjualan($first, $last, $jenis)
	{
		$this->db->order_by('nama_toko', 'desc');
		$this->db->select('count(CASE WHEN toko.id_jenis_toko = '.$jenis.' THEN 1 END) as jumlah_toko');
		$this->db->select('nama_toko');
		$this->db->select('toko.id_jenis_toko');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_jenis_toko, 'toko.id_jenis_toko = jenis_toko.id_jenis_toko');
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_toko');
		// $this->db->having('penjualan.id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_sku($first, $last)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('SUM(qty) as jumlah_sku');
		$this->db->select('produk.id_sku as sku_id');
		// $this->db->select('SUM(detail_penjualan.id_produk) as jumlah_produk');
		$this->db->select('nama_sku');
		$this->db->select('sku.id_sku');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_sku, 'sku.id_sku = produk.id_sku');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_sku');
		// $this->db->having('penjualan.id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_sku_penjualan($first, $last)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('SUM(qty) as jumlah_sku');
		$this->db->select('produk.id_sku as sku_id');
		// $this->db->select('SUM(detail_penjualan.id_produk) as jumlah_produk');
		$this->db->select('nama_sku');
		$this->db->select('sku.id_sku');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_sku, 'sku.id_sku = produk.id_sku');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_sku');
		// $this->db->having('penjualan.id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_produk_by_sku($first, $last, $sku)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('SUM(qty) as jumlah_produk');
		$this->db->select('detail_penjualan.id_produk as produk_id');
		// $this->db->select('SUM(detail_penjualan.id_produk) as jumlah_produk');
		$this->db->select('id_sku');
		$this->db->select('nama_produk');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where('id_sku', $sku);
		$this->db->group_by('nama_produk');
		// $this->db->having('id_sku', $sku);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_produk_by_sku_penjualan($first, $last, $sku)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('SUM(qty) as jumlah_produk');
		$this->db->select('detail_penjualan.id_produk as produk_id');
		// $this->db->select('SUM(detail_penjualan.id_produk) as jumlah_produk');
		$this->db->select('id_sku');
		$this->db->select('nama_produk');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where('id_sku', $sku);
		$this->db->group_by('nama_produk');
		// $this->db->having('id_sku', $sku);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_produk_by_toko($first, $last, $toko)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('count(CASE WHEN penjualan.id_toko = '.$toko.' THEN 1 END) as jumlah_toko');
		$this->db->select('SUM(qty) as jumlah_produk');
		$this->db->select('detail_penjualan.id_produk as produk_id');
		// $this->db->select('SUM(detail_penjualan.id_produk) as jumlah_produk');
		$this->db->select('nama_produk');
		$this->db->select('penjualan.id_toko');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		$this->db->join($this->table_sku, 'sku.id_sku = produk.id_sku');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where('penjualan.id_toko', $toko);
		$this->db->group_by('nama_produk');
		// $this->db->having('penjualan.id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_produk_by_toko_penjualan($first, $last, $toko)
	{
		$this->db->order_by('nama_produk', 'desc');
		$this->db->select('count(CASE WHEN penjualan.id_toko = '.$toko.' THEN 1 END) as jumlah_toko');
		$this->db->select('SUM(qty) as jumlah_produk');
		$this->db->select('detail_penjualan.id_produk as produk_id');
		// $this->db->select('SUM(detail_penjualan.id_produk) as jumlah_produk');
		$this->db->select('nama_produk');
		$this->db->select('penjualan.id_toko');
		$this->db->join($this->table_detail_penjualan, 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
		$this->db->join($this->table_produk, 'produk.id_produk = detail_penjualan.id_produk');
		$this->db->join($this->table_toko, 'toko.id_toko = penjualan.id_toko');
		// $this->db->where( array(  "created >="   => $first,
	 //                              "created <="   => $last
	 //                            ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where('penjualan.id_toko', $toko);
		$this->db->group_by('nama_produk');
		// $this->db->having('penjualan.id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_kurir_by_periodik($first, $last)
	{
		$this->db->order_by('nama_kurir', 'desc');
		$this->db->select('count(penjualan.id_kurir) as jumlah_kurir');
		$this->db->select('nama_kurir');
		$this->db->select('penjualan.id_kurir');
		$this->db->join($this->table_kurir, 'kurir.id_kurir = penjualan.id_kurir');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('nama_kurir');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_kurir_by_periodik_penjualan($first, $last)
	{
		$this->db->order_by('nama_kurir', 'desc');
		$this->db->select('count(penjualan.id_kurir) as jumlah_kurir');
		$this->db->select('nama_kurir');
		$this->db->select('penjualan.id_kurir');
		$this->db->join($this->table_kurir, 'kurir.id_kurir = penjualan.id_kurir');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('nama_kurir');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_kurir_by_periodik_retur($first, $last)
	{
		$this->db->order_by('nama_kurir', 'desc');
		$this->db->select('count(penjualan.id_kurir) as jumlah_kurir');
		$this->db->select('nama_kurir');
		$this->db->select('penjualan.id_kurir');
		$this->db->join($this->table_kurir, 'kurir.id_kurir = penjualan.id_kurir');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('nama_kurir');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_kurir_by_periodik_retur_penjualan($first, $last)
	{
		$this->db->order_by('nama_kurir', 'desc');
		$this->db->select('count(penjualan.id_kurir) as jumlah_kurir');
		$this->db->select('nama_kurir');
		$this->db->select('penjualan.id_kurir');
		$this->db->join($this->table_kurir, 'kurir.id_kurir = penjualan.id_kurir');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('nama_kurir');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_total_pesanan_by_periodik($first, $last)
	{
		$this->db->select('COUNT(nomor_pesanan) as jumlah_tanggal');
		// $this->db->where( array(  "tgl_penjualan >="   => $first,
  //                             "tgl_penjualan <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->row();
	}

	public function get_total_pesanan_by_periodik_penjualan($first, $last)
	{
		$this->db->select('COUNT(nomor_pesanan) as jumlah_tanggal');
		// $this->db->where( array(  "tgl_penjualan >="   => $first,
  //                             "tgl_penjualan <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->row();
	}

	public function get_pesanan_by_periodik($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as tanggal');
		$this->db->select('COUNT(date_format(tgl_penjualan, "%d %M %Y")) as jumlah_tanggal');
		// $this->db->where( array(  "tgl_penjualan >="   => $first,
  //                             "tgl_penjualan <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('tanggal');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_pesanan_by_periodik_penjualan($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as tanggal');
		$this->db->select('COUNT(date_format(tgl_penjualan, "%d %M %Y")) as jumlah_tanggal');
		// $this->db->where( array(  "tgl_penjualan >="   => $first,
  //                             "tgl_penjualan <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('tanggal');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_pesanan_by_periodik_retur($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as tanggal');
		$this->db->select('COUNT(date_format(tgl_penjualan, "%d %M %Y")) as jumlah_tanggal');
		// $this->db->where( array(  "tgl_penjualan >="   => $first,
  //                             "tgl_penjualan <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('tanggal');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_pesanan_by_periodik_retur_penjualan($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as tanggal');
		$this->db->select('COUNT(date_format(tgl_penjualan, "%d %M %Y")) as jumlah_tanggal');
		// $this->db->where( array(  "tgl_penjualan >="   => $first,
  //                             "tgl_penjualan <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('tanggal');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_kurir_by_toko($first, $last, $toko)
	{
		$this->db->order_by('nama_kurir', 'desc');
		$this->db->select('count(CASE WHEN id_toko = '.$toko.' THEN 1 END) as jumlah_kurir');
		$this->db->select('nama_kurir');
		$this->db->select('penjualan.id_kurir');
		$this->db->join($this->table_kurir, 'kurir.id_kurir = penjualan.id_kurir');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_kurir');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_kurir_by_toko_penjualan($first, $last, $toko)
	{
		$this->db->order_by('nama_kurir', 'desc');
		$this->db->select('count(CASE WHEN id_toko = '.$toko.' THEN 1 END) as jumlah_kurir');
		$this->db->select('nama_kurir');
		$this->db->select('penjualan.id_kurir');
		$this->db->join($this->table_kurir, 'kurir.id_kurir = penjualan.id_kurir');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('nama_kurir');
		// $this->db->having('id_toko', $toko);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_penjualan($status, $tanggal, $first, $last)
	{
		$this->db->order_by('created', 'asc');
		$this->db->select('*');
		$this->db->join('users', 'users.id_users = penjualan.id_users');
	    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
	    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
	    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
	    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
	    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		if ($status != 'semua') {
	        $this->db->where('penjualan.id_status_transaksi', $status); 
	    }
	      // $this->db->where( array(  "tgl_penjualan >="   => $first,
	      //                           "tgl_penjualan <="   => $last
	                              // ));

	    if ($tanggal == 'impor') {
	      	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
		                                "date_format(created, '%Y-%m-%d') <="   => $last
		      ));
	    }elseif ($tanggal == 'penjualan') {
	      	$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
		                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
		      ));
	    }
		
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_resi($status, $tanggal, $first, $last)
	{
		$this->db->order_by('created', 'asc');
		$this->db->select('*');
		$this->db->join('penjualan', 'penjualan.nomor_resi = resi.nomor_resi');
	    $this->db->join('users', 'users.id_users = resi.id_users');
	    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
	    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
	    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
	    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
	    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		if ($status != 'semua') {
	        $this->db->where('status', $status); 
	    }
	      // $this->db->where( array(  "tgl_penjualan >="   => $first,
	      //                           "tgl_penjualan <="   => $last
	                              // ));

	    if ($tanggal == 'impor') {
	      	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
		                                "date_format(created, '%Y-%m-%d') <="   => $last
		      ));
	    }elseif ($tanggal == 'penjualan') {
	      	$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
		                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
		      ));
	    }
		
		return $this->db->get($this->table_resi)->result();
	}

	public function get_retur($status, $tanggal, $follup, $first, $last)
	{
		$this->db->order_by('created', 'asc');
		$this->db->select('*');
		$this->db->join('penjualan', 'penjualan.nomor_pesanan = retur.nomor_pesanan');
	    $this->db->join('users', 'users.id_users = penjualan.id_users');
	    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
	    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir');
	    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
	    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
	    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		if ($status != 'semua') {
	        $this->db->where('status_retur', $status); 
	    }
	      // $this->db->where( array(  "tgl_penjualan >="   => $first,
	      //                           "tgl_penjualan <="   => $last
	                              // ));

	    if ($follup != 'null') {
	        $this->db->where('status_follow_up', $follup); 
	    }

	    if ($tanggal == 'impor') {
	      	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
		                                "date_format(created, '%Y-%m-%d') <="   => $last
		      ));
	    }elseif ($tanggal == 'penjualan') {
	      	$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
		                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
		      ));
	    }
		
		return $this->db->get($this->table_retur)->result();
	}

	public function get_repeat($hp, $first, $last, $trigger)
	{
		$this->db->order_by('created', 'asc');
		$this->db->select('*');
		$this->db->join('users', 'users.id_users = penjualan.id_users');
	    $this->db->join('toko', 'toko.id_toko = penjualan.id_toko');
	    $this->db->join('kurir', 'kurir.id_kurir = penjualan.id_kurir', 'left');
	    $this->db->join('status_transaksi', 'status_transaksi.id_status_transaksi = penjualan.id_status_transaksi');
	    $this->db->join('detail_penjualan', 'detail_penjualan.nomor_pesanan = penjualan.nomor_pesanan');
	    $this->db->join('produk', 'produk.id_produk = detail_penjualan.id_produk');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
	    if ($hp == 'kosong') {
	    	$this->db->where('hp_penerima', ''); 
	    }else{
	    	$this->db->where('hp_penerima', $hp); 
	    }
	      // $this->db->where( array(  "tgl_penjualan >="   => $first,
	      //                           "tgl_penjualan <="   => $last
	                              // ));

	    if ($trigger == 'impor') {
	      	$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
		                                "date_format(created, '%Y-%m-%d') <="   => $last
		      ));
	    }elseif ($trigger == 'penjualan') {
	      	$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
		                                "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
		      ));
	    }
		
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_repeat($first, $last)
	{
		$this->db->order_by('jumlah_penerima', 'desc');
		$this->db->select('COUNT(hp_penerima) as jumlah_penerima');
		$this->db->select('nama_penerima, hp_penerima, provinsi, kabupaten, alamat_penerima');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('hp_penerima');
		$this->db->having('jumlah_penerima >', 1);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_repeat_penjualan($first, $last)
	{
		$this->db->order_by('jumlah_penerima', 'desc');
		$this->db->select('COUNT(hp_penerima) as jumlah_penerima');
		$this->db->select('nama_penerima, hp_penerima, provinsi, kabupaten, alamat_penerima');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('hp_penerima');
		$this->db->having('jumlah_penerima >', 1);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_pending_payment($first, $last)
	{
		$this->db->select('jumlah_diterima');
		$this->db->select('sum(total_jual) as total_pending');
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->where( array(  "created >="   => $first,
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last,
	                              'jumlah_diterima'	=> 0
	                            ));
		return $this->db->get($this->table_penjualan)->row();
	}

	public function get_pending_payment_penjualan($first, $last)
	{
		$this->db->select('jumlah_diterima');
		$this->db->select('sum(total_jual) as total_pending');
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->where( array(  "created >="   => $first,
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last,
	                              'jumlah_diterima'	=> 0
	                            ));
		return $this->db->get($this->table_penjualan)->row();
	}

	public function get_pendapat_dasbor($first, $last)
	{
		$this->db->select('sum(total_harga) as total');
		$this->db->select('sum(total_jual) as bruto');
		$this->db->select('sum(total_hpp) as tot_hpp');
		// $this->db->select('(CASE
		// 			            WHEN jumlah_diterima = 0 THEN sum(total_jual)
		// 			        END) as total_pending'
		// 			    );
		$this->db->select('sum(jumlah_diterima) as diterima');
		$this->db->select('sum(ongkir) as tot_ongkir');
		// $this->db->select('(sum(total_jual)) - (sum(total_hpp)) - (sum(ongkir)) as fix');
		$this->db->select('(sum(total_harga)) - (sum(total_hpp)) as fix');
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->where( array(  "created >="   => $first,
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		return $this->db->get($this->table_penjualan)->row();
	}

	public function get_pendapat_dasbor_penjualan($first, $last)
	{
		$this->db->select('sum(total_harga) as total');
		$this->db->select('sum(total_jual) as bruto');
		$this->db->select('sum(total_hpp) as tot_hpp');
		// $this->db->select('(CASE
		// 			            WHEN jumlah_diterima = 0 THEN sum(total_jual)
		// 			        END) as total_pending'
		// 			    );
		$this->db->select('sum(jumlah_diterima) as diterima');
		$this->db->select('sum(ongkir) as tot_ongkir');
		// $this->db->select('(sum(total_jual)) - (sum(total_hpp)) - (sum(ongkir)) as fix');
		$this->db->select('(sum(total_harga)) - (sum(total_hpp)) as fix');
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->where( array(  "created >="   => $first,
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		return $this->db->get($this->table_penjualan)->row();
	}

	public function get_pendapat_periodik($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as "tanggal"');
		$this->db->select('tgl_penjualan');
		$this->db->select('sum(total_harga) as total');
		$this->db->select('sum(total_jual) as bruto');
		$this->db->select('sum(total_hpp) as tot_hpp');
		$this->db->select('sum(jumlah_diterima) as diterima');
		$this->db->select('sum(ongkir) as tot_ongkir');
		// $this->db->select('(sum(total_jual)) - (sum(total_hpp)) - (sum(ongkir)) as fix');
		$this->db->select('(sum(total_harga)) - (sum(total_hpp)) as fix');
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->where( array(  "created >="   => $first,
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('tanggal');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_pendapat_periodik_penjualan($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as "tanggal"');
		$this->db->select('tgl_penjualan');
		$this->db->select('sum(total_harga) as total');
		$this->db->select('sum(total_jual) as bruto');
		$this->db->select('sum(total_hpp) as tot_hpp');
		$this->db->select('sum(jumlah_diterima) as diterima');
		$this->db->select('sum(ongkir) as tot_ongkir');
		// $this->db->select('(sum(total_jual)) - (sum(total_hpp)) - (sum(ongkir)) as fix');
		$this->db->select('(sum(total_harga)) - (sum(total_hpp)) as fix');
		$this->db->where_not_in('id_status_transaksi', 4);
		// $this->db->where( array(  "created >="   => $first,
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('tanggal');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_pendapat_periodik_retur($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as "tanggal"');
		$this->db->select('tgl_penjualan');
		$this->db->select('sum(total_harga) as total');
		$this->db->select('sum(total_jual) as bruto');
		$this->db->select('sum(total_hpp) as tot_hpp');
		$this->db->select('sum(jumlah_diterima) as diterima');
		$this->db->select('sum(ongkir) as tot_ongkir');
		// $this->db->select('(sum(total_jual)) - (sum(total_hpp)) - (sum(ongkir)) as fix');
		$this->db->select('(sum(total_harga)) - (sum(total_hpp)) as fix');
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		// $this->db->where( array(  "created >="   => $first,
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('tanggal');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_pendapat_periodik_retur_penjualan($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as "tanggal"');
		$this->db->select('tgl_penjualan');
		$this->db->select('sum(total_harga) as total');
		$this->db->select('sum(total_jual) as bruto');
		$this->db->select('sum(total_hpp) as tot_hpp');
		$this->db->select('sum(jumlah_diterima) as diterima');
		$this->db->select('sum(ongkir) as tot_ongkir');
		// $this->db->select('(sum(total_jual)) - (sum(total_hpp)) - (sum(ongkir)) as fix');
		$this->db->select('(sum(total_harga)) - (sum(total_hpp)) as fix');
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		// $this->db->where( array(  "created >="   => $first,
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('tanggal');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_provinsi($first, $last)
	{
		$this->db->order_by('provinsi', 'asc');
		$this->db->select('COUNT(provinsi) as jumlah_provinsi');
		$this->db->select('provinsi');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('provinsi');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_provinsi_penjualan($first, $last)
	{
		$this->db->order_by('provinsi', 'asc');
		$this->db->select('COUNT(provinsi) as jumlah_provinsi');
		$this->db->select('provinsi');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('provinsi');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_kabupaten($first, $last, $provinsi)
	{
		$this->db->order_by('kabupaten', 'asc');
		$this->db->select('COUNT(kabupaten) as jumlah_kabupaten');
		$this->db->select('kabupaten, provinsi');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('kabupaten');
		$this->db->having('provinsi', $provinsi);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_kabupaten_penjualan($first, $last, $provinsi)
	{
		$this->db->order_by('kabupaten', 'asc');
		$this->db->select('COUNT(kabupaten) as jumlah_kabupaten');
		$this->db->select('kabupaten, provinsi');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 4);
		$this->db->group_by('kabupaten');
		$this->db->having('provinsi', $provinsi);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_provinsi_retur($first, $last)
	{
		$this->db->order_by('provinsi', 'asc');
		$this->db->select('COUNT(provinsi) as jumlah_provinsi');
		$this->db->select('provinsi');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('provinsi');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_provinsi_retur_penjualan($first, $last)
	{
		$this->db->order_by('provinsi', 'asc');
		$this->db->select('COUNT(provinsi) as jumlah_provinsi');
		$this->db->select('provinsi');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('provinsi');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_kabupaten_retur($first, $last, $provinsi)
	{
		$this->db->order_by('kabupaten', 'asc');
		$this->db->select('COUNT(kabupaten) as jumlah_kabupaten');
		$this->db->select('kabupaten, provinsi');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('kabupaten');
		$this->db->having('provinsi', $provinsi);
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_customer_kabupaten_retur_penjualan($first, $last, $provinsi)
	{
		$this->db->order_by('kabupaten', 'asc');
		$this->db->select('COUNT(kabupaten) as jumlah_kabupaten');
		$this->db->select('kabupaten, provinsi');
		// $this->db->where( array(  "created >="   => $first,
  //                             "created <="   => $last
  //                           ));
		$this->db->where( array(  "date_format(tgl_penjualan, '%Y-%m-%d') >="   => $first,
	                              "date_format(tgl_penjualan, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->where_not_in('id_status_transaksi', 1);
		$this->db->where_not_in('id_status_transaksi', 2);
		$this->db->where_not_in('id_status_transaksi', 3);
		$this->db->group_by('kabupaten');
		$this->db->having('provinsi', $provinsi);
		return $this->db->get($this->table_penjualan)->result();
	}
}

/* End of file Dashboard_model.php */
/* Location: ./application/models/Dashboard_model.php */