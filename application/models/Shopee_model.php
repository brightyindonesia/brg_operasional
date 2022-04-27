<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shopee_model extends CI_Model {

	public $table = 'api_shopee';
	public $id    = 'id_api_shopee';
	public $order = 'DESC';

	function get_by_id($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->get($this->table)->row();
	}

	function update($id,$data)
	{
		$this->db->where($this->id, $id);
		$this->db->update($this->table, $data);
	}
}

/* End of file Shopee_model.php */
/* Location: ./application/models/Shopee_model.php */