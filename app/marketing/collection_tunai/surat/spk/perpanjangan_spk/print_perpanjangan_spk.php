<?php
	require_once('../../../../../../config/config.php');
	die_login();
	$conn = conn($sess_db);
	die_conn($conn);

	$namafile = "Daftar Pemberitahuan SPK "."(".date('d F Y').").doc";
	header("Content-Type: application/vnd.ms-word");
	header("Expires: 0");
	header("Cache-Control:  must-revalidate, post-check=0, pre-check=0");
	header("Content-disposition: attachment; filename=$namafile");
	
?>

<html>
<body>

<table class="t-data">
<tr>
	<th class="w5">BLOK/NO.</th>
	<th class="w20">NAMA</th>
	<th class="w40">ALAMAT SURAT</th>
	<th class="w10">TELEPON</th>
	<th class="w10">TANGGAL PEMBY TERAKHIR</th>

</tr>

<?php
	$query = "
	SELECT * FROM $query_pemby_terakhir LEFT JOIN SPP d
	ON C.KODE_BLOK = d.KODE_BLOK
	where 
	DATEADD(dd,0,c.TGL) = CONVERT(DATETIME,'$tgl',105) AND 
	c.KODE_BLOK NOT IN(SELECT C.KODE_BLOK FROM $query_blok_lunas)
	";
	$obj = $conn->selectlimit($query, $per_page, $page_start);

	while( !$obj->EOF)
	{
		$id = $obj->fields['KODE_BLOK'];
		$tanggal_tempo = tgltgl(date("d-m-Y", strtotime($obj->fields['TGL'])));
		$TELP_KANTOR=(trim($obj->fields["TELP_KANTOR"])!="")?trim(strtoupper($obj->fields["TELP_KANTOR"])):"";
		$TELP_LAIN=(trim($obj->fields["TELP_LAIN"])!="")?",".trim(strtoupper($obj->fields["TELP_LAIN"])):"";
		$TELP_RUMAH=(trim($obj->fields["TELP_RUMAH"])!="")?",".trim(strtoupper($obj->fields["TELP_RUMAH"])):"";
		$TELP=$TELP_KANTOR.$TELP_LAIN.$TELP_RUMAH;
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_PEMBELI']; ?></td>
			<td><?php echo $obj->fields['ALAMAT_SURAT']; ?></td>
			<td><?php echo $TELP; ?></td>
			<td class="text-center"><input type="hidden" name="tanggal_tempo" id="tanggal_tempo" value="<?php echo $tanggal_tempo; ?>"><?php echo tgltgl(date("d-m-Y", strtotime($obj->fields['TGL']))); ?></td>
		</tr>
		<?php
	}
?>
</table>
</body>
</html>
