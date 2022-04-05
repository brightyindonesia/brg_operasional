<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lib_keluar
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        $this->CI->load->model(array('Keluar_model'));
	}

	public function count_detail_penjualan($nomor)
	{
		return count($this->CI->Keluar_model->get_all_detail_by_id($nomor));
	}

}

/* End of file Lib_keluar.php */
/* Location: ./application/libraries/Lib_keluar.php */
