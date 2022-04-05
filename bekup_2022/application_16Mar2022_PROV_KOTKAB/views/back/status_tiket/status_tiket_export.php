<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Status Tiket</th>
			<th>Nama Status Tiket</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($status_tiket as $data) {
		?>
		<tr>
			<td><?php echo $data->id_status_tiket; ?></td>
			<td><?php echo $data->nama_status_tiket; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>