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
    $i = 1; 
    foreach ($detail_surat_packing as $row) {
      if ($i % 8 == 0) {
        // echo "<div style='margin-top:90px'></div>";
      }
  ?>
    <table cellspacing="0" cellpadding="3" border="0" width="100%" style="font-size: 16px;font-weight: bold;vertical-align: text-top;margin-top:20px;margin-bottom:20px;padding: 0;">
      <tr>
        <td valign="top" width="18%" style="border-top: 1px solid black;border-left: 1px solid black;">
          Nama Penerima
        </td>
        
        <td valign="top" width="1%" style="border-top: 1px solid black;">
          :
        </td>

        <td valign="top" style="border-top: 1px solid black;border-right: 1px solid black;">
          <?php echo $penerima->nama_penerima ?> (<?php echo $surat_packing->kepada_surat_packing ?> - <?php echo $penerima->no_hp_penerima ?>)
        </td>
      </tr>

      <tr>
        <td valign="top" width="5%" style="border-left: 1px solid black;">
          Alamat
        </td>
        
        <td valign="top" width="1%">
          :
        </td>

        <td valign="top" style="border-right: 1px solid black;">
          <?php echo $penerima->alamat_penerima ?>
        </td>
      </tr>

      <tr>
        <td valign="top" width="5%" style="border-left: 1px solid black;">
          Isi
        </td>
        
        <td valign="top" width="1%">
          :
        </td>

        <td valign="top" style="border-right: 1px solid black;">
          <?php echo $row->nama_barang_surat_packing." (".$i."/".$total." Koli)"; ?>
        </td>
      </tr>

      <tr>
        <td valign="top" width="5%" style="border-left: 1px solid black;border-bottom: 1px solid black;">
          Nama Pengirim
        </td>
        
        <td valign="top" width="1%" style="border-bottom: 1px solid black;">
          :
        </td>

        <td valign="top" style="border-right: 1px solid black;border-bottom: 1px solid black;">
          <?php echo $company_data->company_name ?> (<?php echo $company_data->company_phone ?>)
        </td>
      </tr>
    </table>
  <?php
      $i++;
    }
  ?>
</body>
</html>