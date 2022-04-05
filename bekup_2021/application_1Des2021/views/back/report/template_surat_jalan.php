<!DOCTYPE html>
<html>
<head>
  <title>Surat Jalan</title>
  <style type="text/css">
    table {
      border-collapse: collapse;
    }
  </style>
</head>
<body>
  <?php 
    $hariIndo = hari(date('l', strtotime($surat_jalan->tgl_surat_jalan)));
    $pathL = base_url()."assets/images/company/brighty.jpg";
    $typeL = pathinfo($pathL, PATHINFO_EXTENSION);
    $dataL = file_get_contents($pathL);
    $base64L = 'data:image/' . $typeL . ';base64,' . base64_encode($dataL);

    $TTDpathL = base_url()."assets/images/company/ttd_dickyi.jpg";
    $TTDtypeL = pathinfo($TTDpathL, PATHINFO_EXTENSION);
    $TTDdataL = file_get_contents($TTDpathL);
    $TTDbase64L = 'data:image/' . $TTDtypeL . ';base64,' . base64_encode($TTDdataL);    
  ?>
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td valign="top" width="50%">
        <img src="<?php echo $base64L; ?>" width="130px">
        <p align="left" style="font-size: 14px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 8px;padding: 0;"><?php echo $company_data->company_name ?></p>
        <p align="left" style="font-size: 12px;vertical-align: text-top;margin:0;margin-top: 5px;padding: 0;"><?php echo $company_data->company_address ?></p>
      </td>

      <td valign="top" colspan="2">
        <p align="right" style="font-size: 24px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 8px;padding: 0;">SURAT JALAN</p>
        <p align="right" style="font-size: 12px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 40px;padding: 0;">Hari, Tanggal: <?php echo $hariIndo.", ".date('d F Y', strtotime($surat_jalan->tgl_surat_jalan)) ?></p>
        <p align="right" style="font-size: 12px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;padding: 0;"><?php echo $surat_jalan->no_surat_jalan ?></p>
      </td>
    </tr>
  </table>
  <hr width="100%" height="10px">
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td style="" valign="top" width="50%">
        <p align="left" style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;">Kepada:</p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $surat_jalan->kepada_surat_jalan; ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $penerima->nama_penerima; ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $penerima->alamat_penerima ?></p>
      </td>

      <td style="" valign="top" width="50%">
        &nbsp;
      </td>
    </tr>
  </table>
  <br>
  <table cellspacing="0" cellpadding="3" border="1" width="100%">
    <tr align="center" style="background-color: #f8f8f8;font-size: 12px;">
      <th width="2%">No</th>
      <th>Kode Barang</th>
      <th>Nama Barang</th>
      <th width="8%">Jumlah</th>
      <th width="8%">Satuan</th>
      <th>Keterangan</th>
    </tr>

    <?php
      $i = 1; 
      foreach ($detail_surat_jalan as $row) {
    ?>
    <tr>
      <td align="center" style="font-size: 12px;"><?php echo $i ?></td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->kode_barang_surat_jalan ?>
      </td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->nama_barang_surat_jalan ?>
      </td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->jumlah_barang_surat_jalan ?>
      </td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->satuan_barang_surat_jalan ?>
      </td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->keterangan_barang_surat_jalan ?>
      </td>
    </tr>
    <?php
        $i++;
      }
    ?> 
  </table>
  <table cellspacing="0" style="margin-top: 5px;" cellpadding="5" border="0" width="100%">
    <tr valign="top">
      <td width="8%" align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;">
        Keterangan:
      </td>
      <td align="left" style="font-size: 11px;vertical-align: text-left;margin:0;margin-top: 5px;padding: 0;">
        <?php echo $surat_jalan->keterangan_surat_jalan ?>
      </td>
    </tr>
  </table>

  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr align="right" style="font-size: 12px;">
      <th colspan="6">
        &nbsp;
      </th>
    </tr>
    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;">
        <p align="left" style="vertical-align: text-top;margin:0;padding: 0;">Bekasi, <?php echo date("d-m-Y") ?></p>
   <!-- <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">Bekasi, <?php echo date("d-m-Y") ?></p>
        <p style="vertical-align: text-top;margin:0;padding: 0;">Hormat Kami,</p>
        <img style="vertical-align: text-top;margin:0;padding: 0;" src="<?php echo $TTDbase64L; ?>" width="100px">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">(Ibrahim Tirta)</p> -->
      </td>

      <td valign="top" align="right" rowspan="3" style="font-size: 12px;">
        Penerima,
      </td>
    </tr>

    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;">
        <p align="left" style="vertical-align: text-top;margin:0;padding: 0;">Warehouse</p>
   <!-- <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">Bekasi, <?php echo date("d-m-Y") ?></p>
        <p style="vertical-align: text-top;margin:0;padding: 0;">Hormat Kami,</p>
        <img style="vertical-align: text-top;margin:0;padding: 0;" src="<?php echo $TTDbase64L; ?>" width="100px">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">(Ibrahim Tirta)</p> -->
      </td>
    </tr>

    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;">
        <p align="left" style="vertical-align: text-top;margin:0;padding: 0;"><img style="vertical-align: text-top;margin:0;padding: 0;" src="<?php echo $TTDbase64L; ?>" width="80px"></p>
      </td>
    </tr>
    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;">
        <p align="left" style="vertical-align: text-top;margin:0;padding: 0;">(Dickyi Firmansyah)</p>
      </td>

      <td valign="top" style="font-size: 12px;">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">
          <hr align="right" width="60%" style="border: 1px black solid;">
        </p>
      </td>
    </tr>
  </table>
</body>
</html>