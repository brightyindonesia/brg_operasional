<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Keyword Produk</th>
			<th>ID Produk</th>
			<th>Keyword</th>
			<th>Nama Produk</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($produk as $data) {
		?>
		<tr>
			<td><?php echo $data->id_keyword_produk; ?></td>
			<td><?php echo $data->id_produk; ?></td>
			<td>
				<?php 
					$this->lib_keyword->export_result_detail_keys_produk_by_id_produk($data->id_keyword_produk);
				?>
			</td>
			<td><?php echo $data->nama_produk; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>