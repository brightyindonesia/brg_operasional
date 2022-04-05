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
    $hariIndo = hari(date('l', strtotime($surat_packing->tgl_surat_packing)));
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
        <p align="right" style="font-size: 24px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 8px;padding: 0;">SURAT PACKING LIST</p>
        <p align="right" style="font-size: 12px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 40px;padding: 0;">Hari, Tanggal: <?php echo $hariIndo.", ".date('d F Y', strtotime($surat_packing->tgl_surat_packing)) ?></p>
        <p align="right" style="font-size: 12px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;padding: 0;"><?php echo $surat_packing->no_surat_packing ?></p>
      </td>
    </tr>
  </table>
  <hr width="100%" height="10px">
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td style="" valign="top" width="50%">
        <p align="left" style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;">Kepada:</p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $surat_packing->kepada_surat_packing; ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $penerima->nama_penerima; ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $penerima->alamat_penerima ?></p>

        <?php 
          if ($penerima->no_hp_penerima != NULL) {
        ?>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $penerima->no_hp_penerima ?></p>
        <?php
          }
        ?>

        <?php 
          if ($penerima->no_telpon_penerima != NULL) {
        ?>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $penerima->no_telpon_penerima ?></p>
        <?php
          }
        ?>        
      </td>

      <td style="" valign="top" width="50%">
        &nbsp;
      </td>
    </tr>
  </table>
  <br>
  <table cellspacing="0" cellpadding="3" border="1" width="100%">
    <tr align="center" style="background-color: #f8f8f8;font-size: 12px;">
      <th width="10%">No</th>
      <th>Kode Barang</th>
      <th>Nama Barang</th>
      <th width="8%">Jumlah</th>
      <th width="8%">Satuan</th>
      <th>Keterangan</th>
    </tr>

    <?php
      $i = 1; 
      foreach ($detail_surat_packing as $row) {
    ?>
    <tr>
      <td align="center" style="font-size: 12px;"><?php echo $i ?> dari <?php echo $total ?></td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->kode_barang_surat_packing ?>
      </td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->nama_barang_surat_packing ?>
      </td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->jumlah_barang_surat_packing ?>
      </td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->satuan_barang_surat_packing ?>
      </td>
      <td align="center" style="font-size: 12px;">
        <?php echo $row->keterangan_barang_surat_packing ?>
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
        <?php echo $surat_packing->keterangan_surat_packing ?>
      </td>
    </tr>
  </table>
</body>
</html>