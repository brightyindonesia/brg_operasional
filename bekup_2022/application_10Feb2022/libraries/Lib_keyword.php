<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lib_keyword
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();
        $this->CI->load->model(array('Keyword_model'));
	}

	

	public function result_detail_keys_provinsi_by_id_detail_provinsi($id)
	{
		$cek_detail_kotkab = $this->CI->Keyword_model->get_keys_detail_kotkab_by_id_detail_provinsi($id);
		
    if($cek_detail_kotkab == NULL)
    {
      echo '<a href="#" class="btn btn-sm btn-danger">No Data</a>';
    }
    else
    {
      foreach($cek_detail_kotkab as $val_detail)
      {
        $string = chunk_split($val_detail->keys_kotkab, 255, "</a> ");
        echo '<a href="#" class="btn btn-sm btn-primary">'.$string;
      }
    }
	}

  public function export_result_detail_keys_provinsi_by_id_detail_provinsi($id)
  {
    $cek_detail_kotkab = $this->CI->Keyword_model->get_keys_detail_kotkab_by_id_detail_provinsi($id);
    
    if($cek_detail_kotkab == NULL)
    {
      echo '';
    }
    else
    {
      $i = 0;
      $total = count($cek_detail_kotkab) - 1;
      foreach($cek_detail_kotkab as $val_detail)
      {
        if ($i == $total) {
          echo $val_detail->keys_kotkab;
        }else{
          echo $val_detail->keys_kotkab.",";
        }

        $i++;
      }
    }
  }

	public function result_detail_provinsi_by_id_provinsi($id)
	{
		$cek_detail_provinsi = $this->CI->Keyword_model->get_detail_provinsi_by_provinsi($id);
		
        if($cek_detail_provinsi == NULL)
        {
          echo '<a href="#" class="btn btn-sm btn-danger">No Data</a>';
        }
        else
        {
          foreach($cek_detail_provinsi as $val_detail)
          {
            $string = chunk_split($val_detail->nama_kotkab, 255, "</a> ");
            echo '<a href="#" style="background-color:#ff851b;color: #fff;margin-bottom:5px;" class="btn btn-sm">'.$string;
          }
        }
	}

}

/* End of file Lib_keyword.php */
/* Location: ./application/libraries/Lib_keyword.php */
