<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Level Kasus</th>
			<th>Nama Level Kasus</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($level_kasus as $data) {
		?>
		<tr>
			<td><?php echo $data->id_level_kasus; ?></td>
			<td><?php echo $data->nama_level_kasus; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>