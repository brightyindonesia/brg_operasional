<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Satuan</th>
			<th>Nama Satuan</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($satuan as $data) {
		?>
		<tr>
			<td><?php echo $data->id_satuan; ?></td>
			<td><?php echo $data->nama_satuan; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>