<!DOCTYPE html>
<html>
<head>
  <title>Laporan PO</title>
  <style type="text/css">
    table {
      border-collapse: collapse;
    }
  </style>
</head>
<body>
  <?php 
    $hariIndo = hari(date('l', strtotime($purchase->tgl_po)));
    $pathL = base_url()."assets/images/company/brighty.jpg";
    $typeL = pathinfo($pathL, PATHINFO_EXTENSION);
    $dataL = file_get_contents($pathL);
    $base64L = 'data:image/' . $typeL . ';base64,' . base64_encode($dataL);

    $TTDpathL = base_url()."assets/images/company/ttd_ibrahim.jpg";
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
        <p align="right" style="font-size: 24px;color:#009eae;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 8px;padding: 0;">PURCHASE ORDER</p>
        <p align="right" style="font-size: 12px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 40px;padding: 0;">Hari, Tanggal: <?php echo $hariIndo.", ".date('d F Y', strtotime($purchase->tgl_po)) ?></p>
        <p align="right" style="font-size: 12px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;padding: 0;"><?php echo $purchase->no_po ?></p>
      </td>
    </tr>
  </table>
  <hr width="100%" height="10px">
  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td style="" valign="top" width="50%">
        <p align="left" style="background-color:#9beef1;width:60%;font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;">Vendor</p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $purchase->nama_vendor ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $purchase->alamat_vendor ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;">No. Telephone: <?php echo ($purchase->no_telpon_vendor == '') ? " - " : $purchase->no_telpon_vendor ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;">No. Handphone: <?php echo ($purchase->no_hp_vendor == '') ? " - " : $purchase->no_hp_vendor ?></p>
      </td>

      <td style="" valign="top" width="30%">
        <p align="left" style="background-color:#9beef1;width:100%;font-size: 13px;font-weight: bold;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;">Pengiriman Ke:</p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $penerima->nama_penerima; ?></p>
        <p align="left" style="font-size: 11px;vertical-align: text-top;margin:0;margin-top: 5px;margin-left: 5px;padding: 0;"><?php echo $penerima->alamat_penerima ?></p>
      </td>
    </tr>
  </table>
  <br>
  <table cellspacing="0" cellpadding="3" border="0" width="100%">
    <tr align="center" style="background-color: #f8f8f8;font-size: 12px;">
      <th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;">No</th>
      <th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;" width="35%">Produk</th>
      <th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;">Kuantitas</th>
      <th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;">Satuan</th>
      <th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;" width="13%">Harga</th>
      <th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;">Diskon</th>
      <th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;">Pajak</th>
      <th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;" width="18%">Total</th>
    </tr>

    <!-- Nama SKU nya -->
    <tr>
      <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;border-bottom:0;font-size: 12px;">A.</td>
      <td align="left" style="border-right: 1px solid black;border-left: 1px solid black;border-bottom:0;font-size: 12px;">
        <?php echo $purchase->nama_sku ?>
      </td>
      <td style="border-right: 1px solid black;border-left: 1px solid black;border-bottom:0;">
        &nbsp;
      </td>
      <td style="border-right: 1px solid black;border-left: 1px solid black;border-bottom:0;">
        &nbsp;
      </td>
      <td style="border-right: 1px solid black;border-left: 1px solid black;border-bottom:0;">
        &nbsp;
      </td>
      <td style="border-right: 1px solid black;border-left: 1px solid black;border-bottom:0;">
        &nbsp;
      </td>
      <td style="border-right: 1px solid black;border-left: 1px solid black;border-bottom:0;">
        &nbsp;
      </td>
      <td style="border-right: 1px solid black;border-left: 1px solid black;border-bottom:0;">
        &nbsp;
      </td>
    </tr>


    <?php
      $i = 1; 
      foreach ($daftar_bahan_kemas as $row) {
    ?>
    <tr>
      <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;border-top:0;border-bottom:0;font-size: 12px;"><?php echo $i ?></td>
      <td align="left" style="border-right: 1px solid black;border-left: 1px solid black;border-top:0;border-bottom:0;font-size: 12px;">
        <?php echo $row->nama_bahan_kemas ?>
      </td>
      <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;border-top:0;border-bottom:0;font-size: 12px;">
        <?php echo $row->kuantitas_po ?>
      </td>
      <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;border-top:0;border-bottom:0;font-size: 12px;">
        <?php echo $row->nama_satuan ?>
      </td>
      <td align="right" style="border-right: 1px solid black;border-left: 1px solid black;border-top:0;border-bottom:0;font-size: 12px;">
        <?php echo rupiah($row->harga_po) ?>
      </td>
      <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;border-top:0;border-bottom:0;font-size: 12px;">
        <?php echo $row->diskon_po." %" ?>
      </td>
      <td align="center" style="border-right: 1px solid black;border-left: 1px solid black;border-top:0;border-bottom:0;font-size: 12px;">
        <?php echo $row->pajak_po." %" ?>
      </td>
      <td align="right" style="border-right: 1px solid black;border-left: 1px solid black;border-top:0;border-bottom:0;font-size: 12px;">
        <?php echo rupiah($row->harga_po * $row->kuantitas_po) ?>
      </td>
    </tr>
    <?php
        $i++;
      }
    ?>

    <tr>
      <td align="left" colspan="5" style="border-top: 1px solid black;font-size: 12px;">
        Terbilang: <?php echo penyebut(($purchase->total_harga_po + $purchase->total_pajak_po) - $purchase->total_diskon_po); ?> Rupiah
      </td>

      <td align="right" colspan="2" style="border-top: 1px solid black;border-bottom:0;border-left:0;font-size: 12px;">
        Total Harga
      </td>
      <td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;font-size: 12px;font-size: 12px;">
        <?php echo rupiah($purchase->total_harga_po) ?>
      </td>
    </tr>
    <tr>
      <td align="right" colspan="7" style="border-bottom:0;border-left:0;font-size: 12px;">
        Total Diskon
      </td>
      <td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;font-size: 12px;">
        <?php echo rupiah($purchase->total_diskon_po) ?>
      </td>
    </tr>
    <tr>
      <td align="right" colspan="7" style="border-bottom:0;border-top:0;border-left:0;font-size: 12px;">
        Total Pajak
      </td>
      <td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;font-size: 12px;">
        <?php echo rupiah($purchase->total_pajak_po) ?>
      </td>
    </tr>
    <tr>
      <td align="right" colspan="7" style="border-bottom:0;border-top:0;border-left:0;font-size: 12px;">
        Ongkos Kirim
      </td>
      <td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;font-size: 12px;">
        <?php echo rupiah($purchase->ongkir_po) ?>
      </td>
    </tr>
    <tr>
      <td align="right" colspan="7" style="border-bottom:0;border-top:0;border-left:0;font-size: 12px;">
        Total
      </td>
      <td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;font-size: 12px;">
        <?php echo rupiah(($purchase->total_harga_po + $purchase->total_pajak_po) - $purchase->total_diskon_po) ?>
      </td>
    </tr>    
  </table>

  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr align="left" style="font-size: 12px;">
      <th colspan="3">Remarks:</th>
    </tr>
    <tr>
      <td valign="top" rowspan="3" style="font-size: 12px;">
        <?php echo $purchase->remarks_po ?>
      </td>

      <td valign="top" colspan="2" style="font-size: 12px;">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">Bekasi, <?php echo date("d-m-Y") ?></p>
   <!-- <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">Bekasi, <?php echo date("d-m-Y") ?></p>
        <p style="vertical-align: text-top;margin:0;padding: 0;">Hormat Kami,</p>
        <img style="vertical-align: text-top;margin:0;padding: 0;" src="<?php echo $TTDbase64L; ?>" width="100px">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">(Ibrahim Tirta)</p> -->
      </td>
    </tr>
    <tr>
      <td valign="top" colspan="2" style="font-size: 12px;">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;"><img style="vertical-align: text-top;margin:0;padding: 0;" src="<?php echo $TTDbase64L; ?>" width="80px"></p>
      </td>
    </tr>
    <tr>
      <td valign="top" colspan="2" style="font-size: 12px;">
        <p align="right" style="vertical-align: text-top;margin:0;padding: 0;">(Ibrahim Tirta)</p>
      </td>
    </tr>
  </table>
</body>
</html>