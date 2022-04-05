<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Kategori Rating</th>
			<th>Nama Kategori Rating</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($kategori_rating as $data) {
		?>
		<tr>
			<td><?php echo $data->id_kategori_rating; ?></td>
			<td><?php echo $data->nama_kategori_rating; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>