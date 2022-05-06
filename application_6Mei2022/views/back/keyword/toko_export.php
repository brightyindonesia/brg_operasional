<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Keyword Toko</th>
			<th>ID Toko</th>
			<th>Keyword</th>
			<th>Nama Toko</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($toko as $data) {
		?>
		<tr>
			<td><?php echo $data->id_keyword_toko; ?></td>
			<td><?php echo $data->id_toko; ?></td>
			<td>
				<?php 
					$this->lib_keyword->export_result_detail_keys_toko_by_id_toko($data->id_keyword_toko);
				?>
			</td>
			<td><?php echo $data->nama_toko; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>