<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lib_produk
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        $this->CI->load->model(array('Paket_model'));
	}

	public function get_propak_by_produk($id)
	{
		$cek_propak = $this->CI->Paket_model->get_propak_by_id_produk($id);
		if (isset($cek_propak)) {
			return 1;
		}
	}
	

}

/* End of file Lib_produk.php */
/* Location: ./application/libraries/Lib_produk.php */
