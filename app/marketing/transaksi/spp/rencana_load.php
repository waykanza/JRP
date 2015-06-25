<?php
require_once('../../../../config/config.php');
require_once('spp_proses.php');
die_login();
$conn = conn($sess_db);
die_conn($conn);
?>

<table class="t-data w100">
<tr>
	<th class="w5">NO.</th>
	<th class="w15">KODE BLOK</th>
	<th class="w15">TANGGAL</th>
	<th class="w15">JENIS PEMBAYARAN</th>
	<th class="w15">NILAI (RP)</th>
	<th class="">KETERANGAN</th>
</tr>

<?php
	$query = "
	SELECT *
	FROM 
		RENCANA a
	LEFT JOIN JENIS_PEMBAYARAN b ON a.KODE_BAYAR = b.KODE_BAYAR
	WHERE a.KODE_BLOK = '$id'
	ORDER BY a.TANGGAL
	";
	$obj = $conn->execute($query);
	$i = 1;

	while( ! $obj->EOF)
	{
		$id = $obj->fields['KODE_BLOK'];
		?>
		<tr>
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK'];  ?></td>
			<td><?php echo tgltgl(f_tgl($obj->fields['TANGGAL'])); ?></td>
			<td><?php echo $obj->fields['JENIS_BAYAR'];  ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI']);  ?></td>
			<td><?php echo $obj->fields['KETERANGAN'];  ?></td>
		</tr>
		<?php
		$i++;
		$obj->movenext();
	}
	
?>
<input type="hidden" name="kode_blok" id="kode_blok" value="<?php echo $id ?>">
</table>
<script type="text/javascript">
jQuery(function($) {
	t_strip('.t-data');
});
</script>
<?php
close($conn);
exit;
?>