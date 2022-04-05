<?php
defined('BASEPATH') OR exit('No direct script access allowed');

  function write_log()
  {
    $CI =& get_instance();

    $data = array(
      'content'    => str_replace(';', '', $CI->db->last_query()),
      'created_by' => $CI->session->name,
      'ip_address' => $CI->input->ip_address(),
      'user_agent' => str_replace(';', '.', $CI->input->user_agent()),
    );

    $CI->db->insert('changelog_query', $data);
  }

?>
