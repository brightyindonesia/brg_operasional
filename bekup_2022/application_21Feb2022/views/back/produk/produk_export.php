<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
?>
<table>
	<thead>
		<tr>
			<th>ID Produk</th>
			<th>ID Satuan</th>
			<th>ID SKU</th>
			<th>Sub SKU</th>
			<th>Nama Produk</th>
			<th>Qty Produk</th>
			<th>Hpp Produk</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($produk as $data) {
		?>
		<tr>
			<td><?php echo $data->id_produk; ?></td>
			<td><?php echo $data->id_satuan; ?></td>
			<td><?php echo $data->id_sku; ?></td>
			<td><?php echo $data->sub_sku; ?></td>
			<td><?php echo $data->nama_produk; ?></td>
			<td><?php echo $data->qty_produk; ?></td>
			<td><?php echo $data->hpp_produk; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>