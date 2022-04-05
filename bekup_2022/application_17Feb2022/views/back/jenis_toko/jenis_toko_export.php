<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Jenis</th>
			<th>Nama Jenis Toko</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($jenis_toko as $data) {
		?>
		<tr>
			<td><?php echo $data->id_jenis_toko; ?></td>
			<td><?php echo $data->nama_jenis_toko; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>