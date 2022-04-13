<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Kurir</th>
			<th>Nama Kurir</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($kurir as $data) {
		?>
		<tr>
			<td><?php echo $data->id_kurir; ?></td>
			<td><?php echo $data->nama_kurir; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>