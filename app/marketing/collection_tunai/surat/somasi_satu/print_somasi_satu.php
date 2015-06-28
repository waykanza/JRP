<?php
	require_once('../../../../../config/config.php');
	die_login();
	$conn = conn($sess_db);
	die_conn($conn);

	$namafile = "Daftar Somasi Satu "."(".date('d F Y').").doc";
	header("Content-Type: application/vnd.ms-word");
	header("Expires: 0");
	header("Cache-Control:  must-revalidate, post-check=0, pre-check=0");
	header("Content-disposition: attachment; filename=$namafile");
	
?>

<html>
<body>

<table border="1">
<tr>
	<th class="w5">BLOK/NO.</th>
	<th class="w20">NAMA</th>
	<th class="w40">ALAMAT SURAT</th>
	<th class="w10">TELEPON</th>
	<th class="w10">TANGGAL JATUH TEMPO</th>
	<th class="w10">NILAI JATUH TEMPO</th>
</tr>

<?php
$tgl 		= f_tgl (date("Y-m-d"));

$tanggal_bulan		= $tgl;
$pecah				= explode("-",$tanggal_bulan);
$sekarang_tgl		= $pecah[0];
$sekarang_bln 		= $pecah[1];
$sekarang_thn 		= $pecah[2];

//bulan kemarin
$kemarin_bln	= $sekarang_bln - 1;
$kemarin_thn	= $sekarang_thn;
if($sekarang_bln == 1)
{
	$kemarin_bln	= 12;
	$kemarin_thn	= $sekarang_thn - 1;
}

$query_blok_lunas_bayar = '';
$query_pemb_jt = '';
$query_tglmerah = '';
$query_libur = '';
$query_blok_lunas_bayar = "SELECT C.KODE_BLOK FROM ( SELECT A.KODE_BLOK,B.SUMOFREALISASI,A.SUMOFPLAN,(B.SUMOFREALISASI-A.SUMOFPLAN) AS REMAIN FROM 
( SELECT SUM (A.NILAI) as SUMOFPLAN, A.KODE_BLOK from ( select A.KODE_BLOK,A.TANGGAL_TANDA_JADI AS TANGGAL,ISNULL(A.TANDA_JADI,0) 
AS NILAI from spp A where A.KODE_BLOK is not null UNION ALL SELECT A.KODE_BLOK,A.TANGGAL,ISNULL(A.NILAI,0) FROM RENCANA A WHERE A.KODE_BLOK IS NOT NULL
)a GROUP BY a.KODE_BLOK ) A LEFT JOIN (SELECT SUM(A.NILAI) AS SUMOFREALISASI,A.KODE_BLOK FROM REALISASI A GROUP BY  A.KODE_BLOK)B 
ON A.KODE_BLOK=B.KODE_BLOK where (B.SUMOFREALISASI-A.SUMOFPLAN)>=0 )C LEFT JOIN SPP D ON C.KODE_BLOK = D.KODE_BLOK
WHERE C.REMAIN - (ISNULL(D.NILAI_CAIR_KPR,0)) >= 0 UNION ALL SELECT KODE_BLOK FROM KWITANSI WHERE TANGGAL >= CONVERT(DATETIME,'01-$kemarin_bln-$kemarin_thn',105)
AND TANGGAL < CONVERT(DATETIME,'01-$sekarang_bln-$sekarang_thn',105)
";

$query_pemb_jt .= "(SELECT SOMASI_SATU FROM CS_PARAMETER_COL)";


$query = "
SELECT 
	A.KODE_BLOK,A.NAMA_PEMBELI,A.ALAMAT_SURAT,A.TELP_KANTOR,A.TELP_LAIN,A.TELP_RUMAH,B.TANGGAL,B.NILAI
FROM 
	SPP A JOIN RENCANA B ON A.KODE_BLOK = B.KODE_BLOK
WHERE
	(select dbo.tambah_tgl(B.TANGGAL,$query_pemb_jt)) = CONVERT(DATETIME,'$tgl',105)AND 
b.KODE_BLOK NOT IN($query_blok_lunas_bayar)
ORDER BY A.KODE_BLOK
";
$obj = $conn->selectlimit($query, $per_page, $page_start);

while( !$obj->EOF)
{
	$id = $obj->fields['KODE_BLOK'];
	$tanggal_tempo = $obj->fields['TANGGAL'];
	$TELP_KANTOR=(trim($obj->fields["TELP_KANTOR"])!="")?trim(strtoupper($obj->fields["TELP_KANTOR"])):"";
	$TELP_LAIN=(trim($obj->fields["TELP_LAIN"])!="")?",".trim(strtoupper($obj->fields["TELP_LAIN"])):"";
	$TELP_RUMAH=(trim($obj->fields["TELP_RUMAH"])!="")?",".trim(strtoupper($obj->fields["TELP_RUMAH"])):"";
	$TELP=$TELP_KANTOR.$TELP_LAIN.$TELP_RUMAH;
	?>
	<tr class="onclick" id="<?php echo $id; ?>"> 
		<td width="30" class="notclick text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
		<td class="text-center"><?php echo $id; ?></td>
		<td><?php echo $obj->fields['NAMA_PEMBELI']; ?></td>
		<td><?php echo $obj->fields['ALAMAT_SURAT']; ?></td>
		<td><?php echo $TELP; ?></td>
		<td class="text-center"><input type="hidden" name="tanggal_tempo" id="tanggal_tempo" value="<?php echo $tanggal_tempo; ?>"><?php echo tgltgl(date("d-m-Y", strtotime($obj->fields['TANGGAL']))); ?></td>
		<td><?php echo $obj->fields['NILAI']; ?></td>		
	</tr>
	<?php
	$obj->movenext();
}
?>
</table>

</body>
</html>
