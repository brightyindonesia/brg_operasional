<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Kategori Kasus</th>
			<th>Nama Kategori Kasus</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($kategori_kasus as $data) {
		?>
		<tr>
			<td><?php echo $data->id_kategori_kasus; ?></td>
			<td><?php echo $data->nama_kategori_kasus; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>