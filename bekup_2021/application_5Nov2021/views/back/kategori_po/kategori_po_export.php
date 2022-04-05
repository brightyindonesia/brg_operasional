<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Kategori PO</th>
			<th>Kode Kategori PO</th>
			<th>Nama Kategori PO</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($kategori_po as $data) {
		?>
		<tr>
			<td><?php echo $data->id_kategori_po; ?></td>
			<td><?php echo $data->kode_kategori_po; ?></td>
			<td><?php echo $data->nama_kategori_po; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>