<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Vendor</th>
			<th>Nama Vendor</th>
			<th>Alamat Vendor</th>
			<th>No. HP Vendor</th>
			<th>No. Telepon Vendor</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($vendor as $data) {
		?>
		<tr>
			<td><?php echo $data->id_vendor; ?></td>
			<td><?php echo $data->nama_vendor; ?></td>
			<td><?php echo $data->alamat_vendor; ?></td>
			<td><?php echo $data->no_hp_vendor; ?></td>
			<td><?php echo $data->no_telpon_vendor; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>