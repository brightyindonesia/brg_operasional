<?php 
	header("Content-type:application/octet-stream/");
	header("Content-Transfer-Encoding: Binary"); 
	header("Content-Disposition:attachment; filename=$title.xlsx");
	header("Pragma: no-cache");
	header("Expires: 0");
	header("redirect_from: echo base_url('admin/laporan/crm')");
?>
<table>
	<thead>
		<tr>
			<th>no_pesanan</th>
			<th>tanggal</th>
			<th>no_resi</th>
			<th>kurir</th>
			<th>ongkir</th>
			<th>nama_penerima</th>
			<th>provinsi</th>
			<th>kota</th>
			<th>alamat</th>
			<th>hp_penerima</th>
			<th>nama_produk</th>
			<th>jumlah</th>
			<th>total_harga</th>
			<th>metode_pembayaran</th>
			<th>status</th>
			<th>total_jual</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($export as $data) {
		?>
		<tr>
			<td><?php echo $data->nomor_pesanan; ?></td>
			<td><?php echo date('d/m/Y', strtotime($data->tgl_penjualan)); ?></td>
			<td><?php echo $data->nomor_resi; ?></td>
			<td><?php echo $data->nama_kurir; ?></td>
			<td><?php echo $data->ongkir; ?></td>
			<td><?php echo $data->nama_penerima; ?></td>
			<td><?php echo $data->provinsi; ?></td>
			<td><?php echo $data->kabupaten; ?></td>
			<td><?php echo $data->alamat_penerima; ?></td>
			<td><?php echo $data->hp_penerima; ?></td>
			<td><?php echo $data->nama_produk; ?></td>
			<td><?php echo $data->qty; ?></td>
			<td><?php echo $data->harga; ?></td>
			<td>Transfer</td>
			<td>Terkirim</td>
			<td><?php echo $data->total_harga; ?></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>