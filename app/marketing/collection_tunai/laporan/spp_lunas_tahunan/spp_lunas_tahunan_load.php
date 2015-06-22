<?php
require_once('../../../../../config/config.php');
die_login();
//die_app('');
//die_mod('');
$conn = conn($sess_db);
die_conn($conn);

$nama_bulan			= (isset($_REQUEST['nama_bulan'])) ? clean($_REQUEST['nama_bulan']) : '';
$page_num			= (isset($_REQUEST['page_num'])) ? clean($_REQUEST['page_num']) : 1;

$tahun				= (isset($_REQUEST['tahun'])) ? clean($_REQUEST['tahun']) : '';

$array_bulan 		= array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember'); 
	
$query_blok_lunas = "SELECT C.KODE_BLOK, C.REMAIN, ISNULL(D.NILAI_CAIR_KPR,0) AS NILAI_KPR, (C.REMAIN - (ISNULL(D.NILAI_CAIR_KPR,0))) AS SISA FROM
( SELECT A.KODE_BLOK,B.SUMOFREALISASI,A.SUMOFPLAN,(B.SUMOFREALISASI-A.SUMOFPLAN) AS REMAIN FROM 
( SELECT SUM (A.NILAI) as SUMOFPLAN, A.KODE_BLOK from ( select A.KODE_BLOK,A.TANGGAL_TANDA_JADI AS TANGGAL,ISNULL(A.TANDA_JADI,0) 
AS NILAI from spp A where A.KODE_BLOK is not null UNION ALL SELECT A.KODE_BLOK,A.TANGGAL,ISNULL(A.NILAI,0) FROM RENCANA A WHERE A.KODE_BLOK IS NOT NULL
)a GROUP BY a.KODE_BLOK ) A LEFT JOIN (SELECT SUM(A.NILAI) AS SUMOFREALISASI,A.KODE_BLOK FROM REALISASI A GROUP BY  A.KODE_BLOK)B 
ON A.KODE_BLOK=B.KODE_BLOK where (B.SUMOFREALISASI-A.SUMOFPLAN)>=0 )C LEFT JOIN SPP D ON C.KODE_BLOK = D.KODE_BLOK
WHERE C.REMAIN - (ISNULL(D.NILAI_CAIR_KPR,0)) >= 0
";

	
$tahun_depan = $tahun + 1;

/* Pagination */

$query = "
SELECT COUNT(*) AS TOTAL
FROM 
	SPP
WHERE
	KODE_BLOK IN ($query_blok_lunas) 
	AND TANGGAL_SPP >= CONVERT(DATETIME,'$tahun',105) 
	AND TANGGAL_SPP < CONVERT(DATETIME,'$tahun_depan',105)
";

$total_data = $conn->execute($query)->fields['TOTAL'];
$total_page = 12;

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = 0;
/* End Pagination */
?>

<table id="pagging-1" class="t-control">
<tr>
	<td>
		<input type="button" id="excel" value=" Excel ">
		<input type="button" id="print" value=" Print ">
	</td>
	<td class="text-right">
		<input type="button" id="prev_page" value=" < ">
		Hal : <input type="text" name="page_num" size="5" class="page_num apply text-center" value="<?php echo $page_num; ?>">
		Dari <?php echo $total_page ?> 
		<input type="hidden" id="total_page" value="<?php echo $total_page; ?>">
		<input type="button" id="next_page" value=" > ">
	</td>
</tr>
</table>

<table class="t-data">
<tr>
	<th class="w3">NO</th>
	<th class="w5">BLOK/NO.</th>
	<th class="w20">NAMA</th>
	<th class="w5">JENIS</th>
	<th class="w10">TANGGAL SPP</th>
	<th class="w15">NILAI TRANSAKSI SEBELUM PPN</th>
	<th class="w15">PPN</th>
	<th class="w15">TOTAL TRANSAKSI</th>
	<th class="w15">TOTAL PEMBAYARAN</th>
</tr>

<?php
if ($total_data > 0)
{
	$bln		 = $page_num;
	$tahun_depan = $tahun;
	$total_harga_rekap	= 0;
	$total_ppn_rekap	= 0;
	$total_all_rekap	= 0;
	$pembayaran_rekap	= 0;
	
	?>
		<tr>
		<td></td>
		<td>Bulan</td>
		<td><?php echo $array_bulan[$bln];?></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		</tr>
	
		<?php
		$bln_depan = $bln + 1;
		if($bln == 12)
		{
			$bln_depan = 1;
			$tahun_depan = $tahun_depan + 1;
		}
		
		$query = "
		SELECT *
		FROM
			SPP a 
			LEFT JOIN STOK b ON a.KODE_BLOK = b.KODE_BLOK
			LEFT JOIN TIPE c ON b.KODE_TIPE = c.KODE_TIPE
			LEFT JOIN HARGA_TANAH d ON b.KODE_SK_TANAH = d.KODE_SK
			LEFT JOIN HARGA_BANGUNAN e ON b.KODE_SK_BANGUNAN = e.KODE_SK
			LEFT JOIN FAKTOR f ON b.KODE_FAKTOR = f.KODE_FAKTOR
			LEFT JOIN CS_VIRTUAL_ACCOUNT g ON a.NOMOR_CUSTOMER = g.NOMOR_VA	
		WHERE
			a.KODE_BLOK IN ($query_blok_lunas) 
			AND a.TANGGAL_SPP >= CONVERT(DATETIME,'01-$bln-$tahun',105) 
			AND a.TANGGAL_SPP < CONVERT(DATETIME,'01-$bln_depan-$tahun_depan',105)
		";
		
		$obj 	= $conn->execute($query);
		$i = 1 + $page_start;

		while( ! $obj->EOF)
		{
			$id 				= $obj->fields['KODE_BLOK'];
			$luas_tanah 		= $obj->fields['LUAS_TANAH'];
			$luas_bangunan 		= $obj->fields['LUAS_BANGUNAN'];
			
			$tanah 				= $luas_tanah * ($obj->fields['HARGA_TANAH']) ;
			$disc_tanah 		= round($tanah * ($obj->fields['DISC_TANAH'])/100,0) ;
			$nilai_tambah		= round(($tanah - $disc_tanah) * ($obj->fields['NILAI_TAMBAH'])/100,0) ;
			$nilai_kurang		= round(($tanah - $disc_tanah) * ($obj->fields['NILAI_KURANG'])/100,0) ;
			$faktor				= $nilai_tambah - $nilai_kurang;
			$total_tanah		= $tanah - $disc_tanah + $faktor;
			$ppn_tanah 			= round($total_tanah * ($obj->fields['PPN_TANAH'])/100,0) ;
			
			$bangunan 			= $luas_bangunan * ($obj->fields['HARGA_BANGUNAN']) ;
			$disc_bangunan 		= round($bangunan * ($obj->fields['DISC_BANGUNAN'])/100,0) ;
			$total_bangunan		= $bangunan - $disc_bangunan;
			$ppn_bangunan 		= round($total_bangunan * ($obj->fields['PPN_BANGUNAN'])/100,0) ;
			
			$total_harga 		= ($total_tanah + $total_bangunan);
			$total_ppn			= ($ppn_tanah + $ppn_bangunan);
			
			$total_harga_all	= ($total_tanah + $total_bangunan) + ($ppn_tanah + $ppn_bangunan);	
			
			$status_kompensasi	= $obj->fields['STATUS_KOMPENSASI'];
			if($status_kompensasi == 1)
				$jenis = 'KPR';
			else if($status_kompensasi == 2)
				$jenis = 'Tunai';
			else
				$jenis = '-';
				
			?>
			<tr class="onclick" id="<?php echo $id; ?>"> 
				<td class="text-center"><?php echo $i; ?></td>
				<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
				<td><?php echo $obj->fields['NAMA_PEMBELI']; ?></td>
				<td class="text-center"><?php echo $jenis; ?></td>
				<td class="text-center"><?php echo tgltgl(date("d-m-Y", strtotime($obj->fields['TANGGAL_SPP']))); ?></td>
				<td class="text-center"><?php echo to_money($total_harga); ?></td>
				<td class="text-center"><?php echo to_money($total_ppn); ?></td>
				<td class="text-center"><?php echo to_money($total_harga_all); ?></td>
				
				<?php 
				$query2 = "
				SELECT TOTAL = CASE WHEN sum(nilai) IS null 
				THEN 0 ELSE sum(nilai) END
				FROM REALISASI WHERE KODE_BLOK = '$id'
				";
				$obj2 			= $conn->execute($query2);
				$pembayaran		= $obj2->fields['TOTAL'];
				?>
		
				<td class="text-center"><?php echo to_money($pembayaran); ?></td>
				
			</tr>
			<?php
			
			$total_harga_rekap	= $total_harga_rekap + $total_harga;
			$total_ppn_rekap	= $total_ppn_rekap + $total_ppn;
			$total_all_rekap	= $total_all_rekap + $total_harga_all;
			$pembayaran_rekap	= $pembayaran_rekap + $pembayaran;
			
			$i++;
			$obj->movenext();
		}	
		
	?>
	
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>Sub Total</td>
		<td class="text-center"><?php echo to_money($total_harga_rekap); ?></td>
		<td class="text-center"><?php echo to_money($total_ppn_rekap); ?></td>
		<td class="text-center"><?php echo to_money($total_all_rekap); ?></td>
		<td class="text-center"><?php echo to_money($pembayaran_rekap); ?></td>
	</tr>
	
<?php	
	if($bln == 12)
	{
		$total_harga_rekap	= 0;
		$total_ppn_rekap	= 0;
		$total_all_rekap	= 0;
		$pembayaran_rekap	= 0;
		
		$query = "
		SELECT *
		FROM
			SPP a 
			LEFT JOIN STOK b ON a.KODE_BLOK = b.KODE_BLOK
			LEFT JOIN TIPE c ON b.KODE_TIPE = c.KODE_TIPE
			LEFT JOIN HARGA_TANAH d ON b.KODE_SK_TANAH = d.KODE_SK
			LEFT JOIN HARGA_BANGUNAN e ON b.KODE_SK_BANGUNAN = e.KODE_SK
			LEFT JOIN FAKTOR f ON b.KODE_FAKTOR = f.KODE_FAKTOR
			LEFT JOIN CS_VIRTUAL_ACCOUNT g ON a.NOMOR_CUSTOMER = g.NOMOR_VA	
		WHERE
			a.KODE_BLOK IN ($query_blok_lunas) 
			AND a.TANGGAL_SPP >= CONVERT(DATETIME,'$tahun',105) 
			AND a.TANGGAL_SPP < CONVERT(DATETIME,'$tahun_depan',105)
		";
		
		$obj 	= $conn->execute($query);

		while( ! $obj->EOF)
		{
			$id 				= $obj->fields['KODE_BLOK'];
			$luas_tanah 		= $obj->fields['LUAS_TANAH'];
			$luas_bangunan 		= $obj->fields['LUAS_BANGUNAN'];
			
			$tanah 				= $luas_tanah * ($obj->fields['HARGA_TANAH']) ;
			$disc_tanah 		= round($tanah * ($obj->fields['DISC_TANAH'])/100,0) ;
			$nilai_tambah		= round(($tanah - $disc_tanah) * ($obj->fields['NILAI_TAMBAH'])/100,0) ;
			$nilai_kurang		= round(($tanah - $disc_tanah) * ($obj->fields['NILAI_KURANG'])/100,0) ;
			$faktor				= $nilai_tambah - $nilai_kurang;
			$total_tanah		= $tanah - $disc_tanah + $faktor;
			$ppn_tanah 			= round($total_tanah * ($obj->fields['PPN_TANAH'])/100,0) ;
			
			$bangunan 			= $luas_bangunan * ($obj->fields['HARGA_BANGUNAN']) ;
			$disc_bangunan 		= round($bangunan * ($obj->fields['DISC_BANGUNAN'])/100,0) ;
			$total_bangunan		= $bangunan - $disc_bangunan;
			$ppn_bangunan 		= round($total_bangunan * ($obj->fields['PPN_BANGUNAN'])/100,0) ;
			
			$total_harga 		= ($total_tanah + $total_bangunan);
			$total_ppn			= ($ppn_tanah + $ppn_bangunan);
			
			$total_harga_all	= ($total_tanah + $total_bangunan) + ($ppn_tanah + $ppn_bangunan);	
			
			$query2 = "
			SELECT TOTAL = CASE WHEN sum(nilai) IS null 
			THEN 0 ELSE sum(nilai) END
			FROM REALISASI WHERE KODE_BLOK = '$id'
			";
			$obj2 			= $conn->execute($query2);
			$pembayaran		= $obj2->fields['TOTAL'];
			
			$total_harga_rekap	= $total_harga_rekap + $total_harga;
			$total_ppn_rekap	= $total_ppn_rekap + $total_ppn;
			$total_all_rekap	= $total_all_rekap + $total_harga_all;
			$pembayaran_rekap	= $pembayaran_rekap + $pembayaran;
			
			$i++;
			$obj->movenext();
		}
		?>
		<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>Total</td>
		<td class="text-center"><?php echo to_money($total_harga_rekap); ?></td>
		<td class="text-center"><?php echo to_money($total_ppn_rekap); ?></td>
		<td class="text-center"><?php echo to_money($total_all_rekap); ?></td>
		<td class="text-center"><?php echo to_money($pembayaran_rekap); ?></td>
	</tr>
	
	<?php
	}
		
}

$nama_bulan = $array_bulan[$page_num];
?>


</table>

<table id="pagging-2" class="t-control"></table>

<script type="text/javascript">
jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());	
	$('#total-data').html('<?php echo $total_data; ?>');
	$('#nama_bulan').val('<?php echo $nama_bulan; ?>');
	$('.page_num').inputmask('integer');
	t_strip('.t-data');
});
</script>

<?php
close($conn);
exit;
?>