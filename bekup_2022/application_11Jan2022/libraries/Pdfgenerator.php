<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("./application/third_party/dompdf/dompdf/autoload.inc.php");
use Dompdf\Dompdf;

class Pdfgenerator
{
	public function generate($html, $filename='', $stream=TRUE, $paper = 'A4', $orientation = "portrait")
  	{
    	$dompdf = new DOMPDF();
    	$dompdf->loadHtml($html);
    	$dompdf->setPaper($paper, $orientation);
    	$dompdf->render();
    	if ($stream) {
	    	$dompdf->set_option('defaultFont', 'Calibri');
        	$dompdf->set_option('isHtml5ParserEnabled', true);
        	$dompdf->set_option('isPhpEnabled', true);
        	$dompdf->stream($filename.".pdf", array("Attachment" => FALSE));
    	}else{
        	return $dompdf->output();
    	}
  }	

}

/* End of file Generator.php */
/* Location: ./application/libraries/Generator.php */
