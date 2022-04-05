<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Status Transaksi</th>
			<th>Nama Status Transaksi</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($status_transaksi as $data) {
		?>
		<tr>
			<td><?php echo $data->id_status_transaksi; ?></td>
			<td><?php echo $data->nama_status_transaksi; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>