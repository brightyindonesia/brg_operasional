<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Disposition:attachment; filename=$title.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	$i = 1;
?>
<table>
	<thead>
		<tr>
			<th>No</th>
			<th>Nomor Pesanan</th>
			<th>Nomor Resi</th>
			<th>Tanggal</th>
			<th>Kurir</th>
			<th>Toko</th>
			<th>Nama Penerima</th>
			<th>Nomor Handphone</th>
			<th>Alamat Penerima</th>
			<th>Kabupaten</th>
			<th>Provinsi</th>
			<th>Tanggal Diterima</th>
			<th>Jumlah Diterima</th>
			<th>Total Harga</th>
			<th>ID Produk</th>
			<th>Sub SKU</th>
			<th>Nama Produk</th>
			<th>Qty</th>
			<th>Harga Jual</th>
			<th>Tanggal Impor</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($penjualan as $data) {
		?>
		<tr>
			<td><?php echo $i; ?></td>
			<td><?php echo $data->nomor_pesanan; ?></td>
			<?php 
				if (substr($data->nomor_resi, 0, 1) != '0') {
			?>
					<td><?php echo $data->nomor_resi; ?></td>
			<?php
				}else{
			?>
					<td><?php echo "'".$data->nomor_resi; ?></td>
			<?php 
				}
			?>
			<td><?php echo $data->tgl_penjualan; ?></td>
			<td><?php echo $data->nama_kurir; ?></td>
			<td><?php echo $data->nama_toko; ?></td>
			<td><?php echo $data->nama_penerima; ?></td>
			<?php 
				if (substr($data->hp_penerima, 0, 1) == 0) {
			?>
					<td><?php echo "'".$data->hp_penerima; ?></td>
			<?php
				}else{
			?>
					<td><?php echo '=TEXT('.$data->hp_penerima.';"0")'; ?></td>
			<?php 
				}
			?>
			<td><?php echo $data->alamat_penerima; ?></td>
			<td><?php echo $data->kabupaten; ?></td>
			<td><?php echo $data->provinsi; ?></td>
			<td><?php echo $data->tgl_diterima; ?></td>
			<td><?php echo $data->jumlah_diterima; ?></td>
			<td><?php echo $data->total_harga; ?></td>
			<td><?php echo $data->id_produk; ?></td>
			<td><?php echo $data->sub_sku; ?></td>
			<td><?php echo $data->nama_produk; ?></td>
			<td><?php echo $data->qty; ?></td>
			<td><?php echo $data->harga; ?></td>
			<td><?php echo $data->created; ?></td>
		</tr>
		<?php
			$i++;
			}
		?>
	</tbody>
</table>