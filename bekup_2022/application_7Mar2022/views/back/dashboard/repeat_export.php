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
			<th>Nama Penerima</th>
			<th>Provinsi</th>
			<th>Kabupaten</th>
			<th>Nomor Handphone</th>
			<th>Alamat</th>
			<th>Jumlah Repeat</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($repeat as $data) {
		?>
		<tr>
			<td><?php echo $i; ?></td>
			<td><?php echo $data->nama_penerima; ?></td>
			<td><?php echo $data->provinsi; ?></td>
			<td><?php echo $data->kabupaten; ?></td>
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
			<td><?php echo $data->jumlah_penerima." Pesanan"; ?></td>
		</tr>
		<?php
			$i++;
			}
		?>
	</tbody>
</table>