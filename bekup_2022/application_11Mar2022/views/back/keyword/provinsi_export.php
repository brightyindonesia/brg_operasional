<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Kota Kabupaten</th>
			<th>ID Provinsi</th>
			<th>Nama Provinsi</th>
			<th>Nama Kota / Kabupaten</th>
			<th>Keyword</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($provinsi as $data) {
		?>
		<tr>
			<td><?php echo $data->id_detail_keyword_provinsi; ?></td>
			<td><?php echo $data->id_keyword_provinsi; ?></td>
			<td><?php echo $data->nama_provinsi; ?></td>
			<td><?php echo $data->nama_kotkab; ?></td>
			<td>
				<?php 
					$this->lib_keyword->export_result_detail_keys_provinsi_by_id_detail_provinsi($data->id_detail_keyword_provinsi);
				?>
			</td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>