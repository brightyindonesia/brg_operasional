<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT+7");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT+7");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $page_title.' | '.$company_data->company_name ?></title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>font-awesome/css/font-awesome.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/template/back/') ?>dist/css/AdminLTE.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>iCheck/all.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href='<?php echo base_url('assets/plugins/') ?>sweetalert2/dist/sweetalert2.min.css' media="screen" />
  <!-- daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- highcharts -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>highcharts/css/highcharts.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>toastr/toastr.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url('assets/template/back/') ?>dist/css/skins/_all-skins.min.css">
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- Favicon -->
  <link rel="shortcut icon" href="<?php echo base_url('assets/images/company/'.$company_data->company_photo_thumb) ?>" />
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/') ?>bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <style type="text/css">
    @media (min-width: 768px) {
      .modal-xl {
        width: 90%;
       max-width:1200px;
      }
    }
    td.details-control {
        background: url(<?php echo base_url('assets/images/plus.png') ?>) no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url(<?php echo base_url('assets/images/minus.png') ?>) no-repeat center center;
    }
    
    /*body.modal-open {
      overflow: visible;
      position: absolute;
      width: 100%;
      height:100%;
    }*/
  </style>
</head>
<body class="<?php echo $skins_template->value ?> sidebar-mini <?php echo $layout_template->value ?>">
