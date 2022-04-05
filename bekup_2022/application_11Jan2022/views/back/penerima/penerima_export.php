<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Penerima</th>
			<th>Nama Penerima</th>
			<th>Alamat Penerima</th>
			<th>No. HP Penerima</th>
			<th>No. Telepon Penerima</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($penerima as $data) {
		?>
		<tr>
			<td><?php echo $data->id_penerima; ?></td>
			<td><?php echo $data->nama_penerima; ?></td>
			<td><?php echo $data->alamat_penerima; ?></td>
			<td><?php echo $data->no_hp_penerima; ?></td>
			<td><?php echo $data->no_telpon_penerima; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>