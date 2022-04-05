<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lib_masuk
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        $this->CI->load->model(array('Paket_model'));
	}

	

}

/* End of file Lib_masuk.php */
/* Location: ./application/libraries/Lib_masuk.php */
