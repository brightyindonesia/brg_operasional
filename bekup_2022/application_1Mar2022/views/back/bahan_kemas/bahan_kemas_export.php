<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Bahan Kemas</th>
			<th>ID Satuan</th>
			<th>Kode SKU</th>
			<th>Nama Bahan Kemas</th>
			<th>Qty</th>
			<th>Keterangan</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($bahan_kemas as $data) {
		?>
		<tr>
			<td><?php echo $data->id_bahan_kemas; ?></td>
			<td><?php echo $data->id_satuan; ?></td>
			<td><?php echo $data->kode_sku_bahan_kemas; ?></td>
			<td><?php echo $data->nama_bahan_kemas; ?></td>
			<td><?php echo $data->qty_bahan_kemas; ?></td>
			<td><?php echo $data->keterangan; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>