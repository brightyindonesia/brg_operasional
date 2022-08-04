<!DOCTYPE html>
<html>

<head>
  <title>Surat Terima Barang</title>
  <style type="text/css">
    table {
      border-collapse: collapse;
    }
  </style>
</head>

<body>
  <?php
  $pathL = base_url() . "assets/images/company/brighty.jpg";
  $typeL = pathinfo($pathL, PATHINFO_EXTENSION);
  $dataL = file_get_contents($pathL);
  $base64L = 'data:image/' . $typeL . ';base64,' . base64_encode($dataL);


  ?>
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td valign="top" width="50%">
        <img src="<?php echo $base64L; ?>" width="130px">
        <p align="left" style="font-size: 14px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 8px;padding: 0;"><?php echo $company_data->company_name ?></p>
        <p align="left" style="font-size: 12px;vertical-align: text-top;margin:0;margin-top: 5px;padding: 0;"><?php echo $company_data->company_address ?></p>
      </td>

      <td valign="top" colspan="2">
        <p align="right" style="font-size: 12px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 40px;padding: 0;">Nomor Dokumen: TE-001-01-<?= strlen($surat_terima_barang->id_surat_terima_barang) > 1 ? $surat_terima_barang->id_surat_terima_barang : "0".$surat_terima_barang->id_surat_terima_barang ?></p>
        <p align="right" style="font-size: 12px;font-weight: bold;vertical-align: text-top;margin:0;margin-top:5px;padding: 0;">Tanggal Berlaku: <?= date("d/m/Y", strtotime(date('Y-m-d') . ' + 5 days')) ?></p>
      </td>
    </tr>
  </table>
  <!-- <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td valign="top" width="35%">
        <img src="<?php echo $base64L; ?>" width="130px">
        <p align="left" style="font-size: 14px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 8px;padding: 0;"><?php echo $company_data->company_name ?></p>
        <p align="left" style="font-size: 12px;vertical-align: text-top;margin:0;margin-top: 5px;padding: 0;"><?php echo $company_data->company_address ?></p>
      </td>

      <td valign="top" colspan="3">
        &nbsp;
      </td>
    </tr>
  </table> -->
  <table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top:20px">
    <tr>
      <td style="" valign="top" width="33%">
        <p align="center" style="font-size: 22px;font-weight: bold;vertical-align: text-top;margin-bottom:20px;margin-top: 8px;padding: 0;">BUKTI PENERIMAAN BARANG</p>
      </td>
    </tr>
  </table>
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td style="" valign="top" width="50%">
        <div class="row">
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0; width:40%; display: inline-block;">Nama Penerima</span>
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-left: 5px;padding: 0; display: inline-block;">: <?php echo $surat_terima_barang->nama_penerima_surat_terima_barang ?></span> <!-- DI MENU DIBUAT DROPDOWN -->
        </div>
        <div class="row">
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0; width:40%; display: inline-block;">Tanggal Kirim</span>
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-left: 5px;padding: 0; display: inline-block;">: <?php echo $surat_terima_barang->tgl_kirim_surat_terima_barang ?></span> <!-- DI MENU DIBUAT DROPDOWN -->
        </div>
        <div class="row">
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0; width:40%; display: inline-block;">Tanggal Terima</span>
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-left: 5px;padding: 0; display: inline-block;">: <?php echo $surat_terima_barang->tgl_terima_surat_terima_barang ?></span> <!-- DI MENU DIBUAT DROPDOWN -->
        </div>
        <!-- <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo '' ///$penerima->nama_penerima; 
                                                                                                                                    ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo '' //$penerima->alamat_penerima 
                                                                                                                                ?></p> -->
      </td>

      <td style="padding-left: 20%" valign="top" width="50%">
        <div class="row">
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 0px;padding: 0; width:30%;display: inline-block; ">Nama PIC QC</span>
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-left: 0px;padding: 0; display: inline-block;">: <?php echo $pic_qc ?></span> <!-- DI MENU DIBUAT DROPDOWN -->
        </div>
        <div class="row">
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 0px;padding: 0; width:30%;display: inline-block;">Kode PO</span>
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-left: 0px;padding: 0;display: inline-block;">: <?php echo $surat_terima_barang->no_po ?></span> <!-- DI MENU DIBUAT DROPDOWN -->
        </div>
        <div class="row">
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 0px;padding: 0; width:30%; display: inline-block;">Kode SJ</span>
          <span style="font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-left: 0px;padding: 0;display: inline-block;">: <?php echo $surat_terima_barang->no_surat_jalan ?></span> <!-- DI MENU DIBUAT DROPDOWN -->
        </div>
      </td>
    </tr>
  </table>
  <p style="font-size: 13px;">Telah diterima dengan baik barang-barang sebagai berikut:
  <table cellspacing="0" style="margin-top: 20px;" cellpadding="3" border="1" width="100%">
    <tr align="center" style="background-color: #f8f8f8;font-size: 12px;">
      <th width="2%">No</th>
      <th>Nama Barang</th>
      <th>No Batch</th>
      <th width="8%">Jumlah</th>
      <th>Exp Date</th>
      <th>Keterangan</th>
    </tr>

    <?php
    $i = 1;
    foreach ($detail_surat_terima_barang as $row) {
    ?>
      <tr>
        <td align="center" style="font-size: 12px;"><?php echo $i ?></td>
        <td align="center" style="font-size: 12px;">
          <?php echo $row->nama_sku
          ?>
        </td>
        <td align="center" style="font-size: 12px;">
          <?php echo $row->no_batch_surat_terima_barang
          ?>
        </td>
        <td align="center" style="font-size: 12px;">
          <?php echo $row->qty_barang_surat_terima_barang
          ?>
        </td>
        <td align="center" style="font-size: 12px;">
          <?php echo $row->exp_date_surat_terima_barang
          ?>
        </td>
        <td align="center" style="font-size: 12px;">
          <?php echo $row->keterangan_barang_surat_terima_barang
          ?>
        </td>
      </tr>
    <?php
      $i++;
    }
    ?>
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

      </td>

      <td valign="top" align="right" rowspan="3" style="font-size: 12px;">
        Warehouse,
      </td>
    </tr>

    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;">
        <p align="left" style="vertical-align: text-top;margin:0;padding: 0;">Dibuat</p>

      </td>
    </tr>

    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;padding-bottom: 50px">
        &nbsp;
      </td>
    </tr>
    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;">
        <p align="left" style="vertical-align: text-top;margin:0;padding: 0;">(Yuki Indah Selistiadi)</p>
      </td>
      <td valign="top" colspan="1" style="font-size: 12px;">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">(<?php echo $warehouse ?>)</p>
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


      <td valign="top" align="left" rowspan="3" style="font-size: 12px;">
        Pengirim,
      </td>
    </tr>

    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">PIC QC</p>

      </td>
    </tr>

    <tr>
      <td valign="top" colspan="1" style="font-size: 12px;padding-bottom: 50px">
        &nbsp;
      </td>
    </tr>
    <tr>
      <td valign="top" colspan="5" style="font-size: 12px;">
        <p align="left" style="vertical-align: text-top;margin:0;padding: 0;">(<?php echo $surat_terima_barang->nama_penerima_surat_terima_barang ?>)</p>
      </td>
      <td valign="top" colspan="1" style="font-size: 12px;">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">(<?php echo $pic_qc ?>)</p>
      </td>
    </tr>
  </table>
</body>

</html>