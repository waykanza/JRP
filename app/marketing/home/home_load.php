<?php

require_once('../../../config/config.php');
die_login();
$conn = conn($sess_db);
die_conn($conn);

//pengembalian stok yang spp lewat masa tenggang
$tgl = f_tgl (date("Y-m-d"));
$query_batas = "(SELECT BATAS_DISTRIBUSI FROM CS_PARAMETER_MARK)";
$query_tenggang = "(SELECT TENGGANG_DISTRIBUSI FROM CS_PARAMETER_MARK)";
$total_hari = $query_batas + $query_tenggang;

$query = "
	SELECT *
	FROM 
		SPP
	WHERE
	DATEADD(dd,$total_hari,TANGGAL_SPP) < CONVERT(DATETIME,'$tgl',105)
	AND STATUS_SPP = 2
	";
	$obj = $conn->execute($query);

	while( ! $obj->EOF)
	{
		$id = $obj->fields['KODE_BLOK'];
		
		$query2 = "
		UPDATE STOK
		SET STATUS_STOK = 1, TERJUAL = 0
		WHERE
			KODE_BLOK = '$id'
		";
		$obj2 = $conn->execute($query2);
		
		$obj->movenext();
	}

//penghapusan spp yang telah lewat tenggang
$conn->Execute("DELETE FROM SPP WHERE DATEADD(dd,3,TANGGAL_PROSES) < CONVERT(DATETIME,'20-06-2015',105) AND STATUS_SPP = 2");


//belum distribusi
$query = "select COUNT(STATUS_SPP) AS TOTAL_BELUM_DISTRIBUSI FROM SPP WHERE STATUS_SPP IS NULL OR STATUS_SPP != 1";
$obj = $conn->execute($query);

$belum_distribusi		= $obj->fields['TOTAL_BELUM_DISTRIBUSI'];

//belum otorisasi
$query = "select COUNT(OTORISASI) AS TOTAL_BELUM_OTORISASI FROM SPP WHERE OTORISASI != 1";
$obj = $conn->execute($query);

$belum_otorisasi		= $obj->fields['TOTAL_BELUM_OTORISASI'];

//belum identifikasi
$query = "select COUNT(SISA) AS TOTAL_BELUM_IDENTIFIKASI FROM CS_VIRTUAL_ACCOUNT WHERE SISA != 0";
$obj = $conn->execute($query);

$belum_identifikasi		= $obj->fields['TOTAL_BELUM_IDENTIFIKASI'];

//belum ppjb
$query = "SELECT count(*) as TOTAL_BELUM_PPJB
FROM SPP WHERE KODE_BLOK NOT IN 
(
SELECT DISTINCT a.KODE_BLOK FROM CS_PPJB a
JOIN SPP b on a.KODE_BLOK = b.KODE_BLOK
)";
$obj = $conn->execute($query);

$belum_ppjb		= $obj->fields['TOTAL_BELUM_PPJB'];
/* End Pagination */
?>
<script type="text/javascript">
jQuery(function($) {
	
});
</script>

<table  border="0" id="pagging-1" class="t-control w100" align="center">
<tr align="center">
	<td width="450"></td>
	<td width="250" align="left">Jumlah SPP belum Distribusi</td>
	<td align="left">
		<input type="text" name="belum_distribusi" readonly="readonly" size="3" id="per_page" class=" apply text-center" value="<?php echo $belum_distribusi; ?>">
		<input type="button" name="detail_distribusi" id="detail_distribusi" value=" Detail ">
	</td>
</tr>
<tr align="center">
	<td width="450"></td>
	<td width="250" align="left">Jumlah SPP belum PPJB</td>
	<td align="left">
		<input type="text" name="belum_ppjb" size="3" readonly="readonly" id="belum_ppjb" class=" apply text-center" value="<?php echo $belum_ppjb; ?>">
		<input type="button" name="detail_ppjb" id="detail_ppjb" value=" Detail ">
	</td>
</tr>
<tr align="center">
	<td width="450"></td>
	<td width="250" align="left">Jumlah SPP belum Otorisasi</td>
	<td align="left">
		<input type="text" name="belum_otorisasi" size="3" readonly="readonly" id="belum_otorisasi" class=" apply text-center" value="<?php echo $belum_otorisasi; ?>">
		<input type="button" name="detail_otorisasi" id="detail_otorisasi" value=" Detail ">
	</td>
</tr>
<tr align="center">
	<td width="450"></td>
	<td width="250" align="left">Jumlah SPP belum Identifikasi</td>
	<td align="left">
		<input type="text" name="belum_identifikasi" size="3" readonly="readonly" id="belum_identifikasi" class=" apply text-center" value="<?php echo $belum_identifikasi; ?>">
		<input type="button" name="detail_identifikasi" id="detail_identifikasi" value=" Detail ">
	</td>
</tr>

</table>



<?php
close($conn);
exit;
?>