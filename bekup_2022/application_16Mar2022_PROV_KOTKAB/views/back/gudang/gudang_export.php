<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Gudang</th>
			<th>Nama Gudang</th>
			<th>Alamat Gudang</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($gudang as $data) {
		?>
		<tr>
			<td><?php echo $data->id_gudang; ?></td>
			<td><?php echo $data->nama_gudang; ?></td>
			<td><?php echo $data->alamat_gudang; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>