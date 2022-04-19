<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Paket</th>
			<th>Nama Paket</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($paket as $data) {
		?>
		<tr>
			<td><?php echo $data->id_paket; ?></td>
			<td><?php echo $data->nama_paket; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>