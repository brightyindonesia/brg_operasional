<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Jenis Timeline</th>
			<th>Kode Jenis Timeline</th>
			<th>Nama Jenis Timeline</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($jenis_timeline as $data) {
		?>
		<tr>
			<td><?php echo $data->id_jenis_timeline; ?></td>
			<td><?php echo $data->kode_jenis_timeline; ?></td>
			<td><?php echo $data->nama_jenis_timeline; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>