<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lib_timeline_produksi
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        $this->CI->load->model(array('Timeline_produksi_model'));
	}

	public function check_posi_by_detail_timeline($id)
	{
		$cek_propo_posi = $this->CI->Timeline_produksi_model->get_popro_posi($id);
		if ($cek_propo_posi) {
			return 1;
		}else{
			return 0;
		}
	}

}

/* End of file Lib_timeline_produksi.php */
/* Location: ./application/libraries/Lib_timeline_produksi.php */
