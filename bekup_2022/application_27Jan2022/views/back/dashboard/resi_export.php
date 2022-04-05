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
			<th>Tanggal Pesanan</th>
			<th>Tanggal Impor</th>
			<th>Tanggal Resi</th>
			<th>Nomor Pesanan</th>
			<th>Nama Toko</th>
			<th>Kurir</th>
			<th>Nomor Resi</th>
			<th>Status Transaksi</th>
			<th>Status Resi</th>
			<th>Total Harga</th>
			<th>Nama Penerima</th>
			<th>Provinsi</th>
			<th>Kabupaten</th>
			<th>Alamat Penerima</th>
			<th>Hp Penerima</th>
			<th>SKU</th>
			<th>Nama Produk</th>
			<th>Qty</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($penjualan as $data) {
				if ($data->status == 0) {
					$status_resi = "Belum Diproses";
				}else if($data->status == 1){
					$status_resi = "Sedang Diproses";
				}else if($data->status == 2){
					$status_resi = "Sudah Diproses";
				}else if($data->status == 3){
					$status_resi = "Retur";
				}

		?>
		<tr>
			<td><?php echo $i; ?></td>
			<td><?php echo $data->tgl_penjualan; ?></td>
			<td><?php echo $data->created; ?></td>
			<td><?php echo $data->tgl_resi; ?></td>
			<td><?php echo $data->nomor_pesanan; ?></td>
			<td><?php echo $data->nama_toko; ?></td>
			<td><?php echo $data->nama_kurir; ?></td>
			<td><?php echo "'".$data->nomor_resi; ?></td>
			<td><?php echo $data->nama_status_transaksi; ?></td>
			<td><?php echo $status_resi; ?></td>
			<td><?php echo $data->total_harga; ?></td>
			<td><?php echo $data->nama_penerima; ?></td>
			<td><?php echo $data->provinsi; ?></td>
			<td><?php echo $data->kabupaten; ?></td>
			<td><?php echo $data->alamat_penerima; ?></td>
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
			<td><?php echo $data->sub_sku; ?></td>
			<td><?php echo $data->nama_produk; ?></td>
			<td><?php echo $data->qty; ?></td>
		</tr>
		<?php
			$i++;
			}
		?>
	</tbody>
</table>