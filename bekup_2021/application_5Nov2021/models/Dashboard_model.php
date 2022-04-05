<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
	public $table_penjualan  = 'penjualan';
	public $table_detail_penjualan  = 'detail_penjualan';
	public $nomor_pesanan    = 'nomor_pesanan';
	public $table_produk  	 = 'produk';
	public $id_produk     	 = 'id_produk';
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

	function get_pakduk_produk_by_produk($id)
	{
		$this->db->select('propak_data_access.id_produk as produk_utama');
		$this->db->select('pakduk_data_access.id_produk as produk_detail');
		$this->db->select('nama_produk, qty_pakduk');
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

	public function get_pendapat_periodik($first, $last)
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
  //                             	  "created <="   => $last
  //                       ));
		$this->db->where( array(  "date_format(created, '%Y-%m-%d') >="   => $first,
	                              "date_format(created, '%Y-%m-%d') <="   => $last
	                            ));
		$this->db->group_by('tanggal');
		return $this->db->get($this->table_penjualan)->result();
	}

	public function get_pendapat_periodik_retur($first, $last)
	{
		$this->db->order_by('tgl_penjualan', 'asc');
		$this->db->select('date_format(tgl_penjualan, "%d %M %Y") as "tanggal"');
		$this->db->select('tgl_penjualan');
		$this->db->select('sum(total_jual) as total');
		$this->db->select('sum(total_hpp) as tot_hpp');
		$this->db->select('sum(jumlah_diterima) as diterima');
		$this->db->select('sum(ongkir) as tot_ongkir');
		$this->db->select('(sum(total_jual)) - (sum(total_hpp)) - (sum(ongkir)) as fix');
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
}

/* End of file Dashboard_model.php */
/* Location: ./application/models/Dashboard_model.php */