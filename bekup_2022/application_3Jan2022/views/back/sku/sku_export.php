<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID SKU</th>
			<th>Kode SKU</th>
			<th>Nama SKU</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($sku as $data) {
		?>
		<tr>
			<td><?php echo $data->id_sku; ?></td>
			<td><?php echo $data->kode_sku; ?></td>
			<td><?php echo $data->nama_sku; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>