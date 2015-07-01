<?php
require_once('../../../../../config/config.php');
die_login();
// die_app('C');
die_mod('C27');
$conn = conn($sess_db);
die_conn($conn);

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$periode_awal		= (isset($_REQUEST['periode_awal'])) ? clean($_REQUEST['periode_awal']) : '';
$periode_akhir		= (isset($_REQUEST['periode_akhir'])) ? clean($_REQUEST['periode_akhir']) : '';
$field1				= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1			= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';

$tanggal 			= f_tgl (date("Y-m-d"));
$pecah_tanggal		= explode("-",$tanggal);
$tgl 				= $pecah_tanggal[0];
$bln 				= $pecah_tanggal[1];
$thn 				= $pecah_tanggal[2];
$array_bulan 		= array(1=>'Januari','Februari','Maret', 'April', 'Mei', 'Juni','Juli','Agustus','September','Oktober', 'November','Desember'); 
	

$query_search = '';

if($field1 == 'all')
{
	if ($periode_awal <> '' || $periode_akhir <> '')
	{
		$query_search .= "WHERE b.TANGGAL >= CONVERT(DATETIME,'$periode_awal',105) AND b.TANGGAL <= CONVERT(DATETIME,'$periode_akhir',105)";
	}
}

if($field1 == 'kode_blok')
{
	$query_search .= "WHERE a.KODE_BLOK = '$search1'";

}

if($field1 == 'spp_distribusi')
{
	$query_search .= "WHERE a.TANGGAL_SPP >= CONVERT(DATETIME,'$periode_awal',105) AND a.TANGGAL_SPP <= CONVERT(DATETIME,'$periode_akhir',105) AND a.STATUS_SPP = '1'";

}

if($field1 == 'spp_belum')
{
	$query_search .= "WHERE a.TANGGAL_SPP >= CONVERT(DATETIME,'$periode_awal',105) AND a.TANGGAL_SPP <= CONVERT(DATETIME,'$periode_akhir',105) AND a.STATUS_SPP = '2'";

}



/* Pagination */

$query = "
SELECT 
	COUNT(*) AS TOTAL
FROM
	SPP a 
	LEFT JOIN RENCANA b ON a.KODE_BLOK = b.KODE_BLOK
$query_search
";

$total_data = $conn->execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
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

<table class="t-nowrap t-data wm100">
<tr>
	<th rowspan="2">BLOK / NOMOR</th>
	<th rowspan="2">NAMA PEMBELI</th>
	<th rowspan="2">TOTAL TRANSAKSI</th>
	<th colspan="2">RENCANA BAYAR</th>
	<th colspan="3">UMUR TAGIHAN SUDAH JATUH TEMPO</th>
</tr>
<tr>
	<th colspan="1">Tanggal</th>
	<th colspan="1">Nilai</th>
	<th colspan="1">Kurang dari = 45 Hari</th>
	<th colspan="1">46 - 60 Hari</th>
	<th colspan="1">Lebih dari 60 hari</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT *
	FROM
		SPP a 
		LEFT JOIN RENCANA b ON a.KODE_BLOK = b.KODE_BLOK
	$query_search
	";
	
	$obj = $conn->selectlimit($query, $per_page, $page_start);
	$i = 1 + $page_start;

	while( ! $obj->EOF)
	{
		$id 				= $obj->fields['KODE_BLOK'];
		
		//hitung harga total tanah dan bangunan
		$query2 = "
		SELECT *
		FROM
			SPP a 
			LEFT JOIN STOK b ON a.KODE_BLOK = b.KODE_BLOK
			LEFT JOIN TIPE c ON b.KODE_TIPE = c.KODE_TIPE
			LEFT JOIN HARGA_TANAH d ON b.KODE_SK_TANAH = d.KODE_SK
			LEFT JOIN HARGA_BANGUNAN e ON b.KODE_SK_BANGUNAN = e.KODE_SK
			LEFT JOIN FAKTOR f ON b.KODE_FAKTOR = f.KODE_FAKTOR
			LEFT JOIN CS_VIRTUAL_ACCOUNT g ON a.NOMOR_CUSTOMER = g.NOMOR_VA		
			WHERE a.KODE_BLOK = '$id'
		";
	
		$obj2 				= $conn->execute($query2);
		$luas_tanah 		= $obj2->fields['LUAS_TANAH'];
		$luas_bangunan 		= $obj2->fields['LUAS_BANGUNAN'];
		
		$tanah 				= $luas_tanah * ($obj2->fields['HARGA_TANAH']) ;
		$disc_tanah 		= round($tanah * ($obj2->fields['DISC_TANAH'])/100,0) ;
		$nilai_tambah		= round(($tanah - $disc_tanah) * ($obj2->fields['NILAI_TAMBAH'])/100,0) ;
		$nilai_kurang		= round(($tanah - $disc_tanah) * ($obj2->fields['NILAI_KURANG'])/100,0) ;
		$faktor				= $nilai_tambah - $nilai_kurang;
		$total_tanah		= $tanah - $disc_tanah + $faktor;
		$ppn_tanah 			= round($total_tanah * ($obj2->fields['PPN_TANAH'])/100,0) ;
		
		$bangunan 			= $luas_bangunan * ($obj2->fields['HARGA_BANGUNAN']) ;
		$disc_bangunan 		= round($bangunan * ($obj2->fields['DISC_BANGUNAN'])/100,0) ;
		$total_bangunan		= $bangunan - $disc_bangunan;
		$ppn_bangunan 		= round($total_bangunan * ($obj2->fields['PPN_BANGUNAN'])/100,0) ;
		
		$total_harga 		= to_money($total_tanah + $total_bangunan);
		$total_ppn			= to_money($ppn_tanah + $ppn_bangunan);
		
		$total_harga		= ($total_tanah + $total_bangunan) + ($ppn_tanah + $ppn_bangunan);	
		
		$sub_nilai 	= 0;
		$sub_45		= 0;
		$sub_4560	= 0;
		$sub_60		= 0;
		
		//ambil nilai total rencana pada periode yang dipilih dan tanggal terkecil
		$query2 = "
		SELECT COUNT(*) AS TOTAL,
		TGL = CASE WHEN MIN(TANGGAL) IS null 
		THEN 0 ELSE MIN(TANGGAL) END
		FROM RENCANA WHERE KODE_BLOK = '$id'  
		AND TANGGAL >= CONVERT(DATETIME,'$periode_awal',105) AND TANGGAL <= CONVERT(DATETIME,'$periode_akhir',105)
		";
		
		$obj2 		= $conn->execute($query2);
		$banyak		= $obj2->fields['TOTAL'];
		$tgl_awal	= tgltgl(date("d-m-Y", strtotime($obj2->fields['TGL'])));
		$iterasi	= 1;
		$isi_data	= 0;
		
		if($banyak > 0)
		{
		$pecah_tanggal		= explode("-",$tgl_awal);
		$tgl 				= $pecah_tanggal[0];
		$bln 				= $pecah_tanggal[1];
		$thn 				= $pecah_tanggal[2];		
		}

		//pengecekkan sebanyak rencana yang ada
		while($iterasi <= $banyak)
		{

		//pengecekkan apakah ada realisasi atau tidak
		$query4 = "
		SELECT COUNT(*) AS TOTAL
		FROM REALISASI WHERE KODE_BLOK = '$id' AND TANGGAL >= CONVERT(DATETIME,'$tgl-$bln-$thn',105) 
		AND TANGGAL <= CONVERT(DATETIME,'$periode_akhir',105) 
		";
		
		$obj4 		= $conn->execute($query4);
		$ada		= $obj4->fields['TOTAL'];
		
			//bila tidak ada realisasi
			if($ada == 0)
			{
			?>
		
			<tr class="onclick" id="<?php echo $id; ?>"> 
				<?php 
				//bila merupakan tampilan baris pertama
				if($iterasi == 1)
				{
				?>
					<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
					<td><?php echo $obj->fields['NAMA_PEMBELI']; ?></td>
					<td class="text-center"><?php echo to_money($total_harga); ?></td>
					<?php $tgl = $tgl;?>
				<?php
				}
				else
				{
				?>
					<td></td><td></td><td></td>
					<?php $tgl = 01;?>
				<?php
				}
				?>

				
				<?php 
				
				//ambil nilai dari rancana bulan tersebut
				$query3 = "
				SELECT top 1 *
				FROM RENCANA WHERE KODE_BLOK = '$id' AND TANGGAL >= CONVERT(DATETIME,'$tgl-$bln-$thn',105) AND TANGGAL <= CONVERT(DATETIME,'$periode_akhir',105) 
				 order by TANGGAL
				";
				$obj3 = $conn->execute($query3);
				$tanggal	= tgltgl(date("d-m-Y", strtotime($obj3->fields['TANGGAL'])));
				$nilai		= $obj3->fields['NILAI'];
				$sub_nilai 	= $sub_nilai + $nilai;
				?>
				
				<td class="text-center"><?php echo $tanggal; ?></td>
				<td class="text-center"><?php echo to_money($nilai); ?></td>
				
				<?php 
				$tanggal_skg 	= f_tgl (date("Y-m-d")); 
				$time1			= strtotime($tanggal);
				$time2			= strtotime($tanggal_skg);
				$selisih		=($time2-$time1)/(60*60*24);
				
				if($selisih <= 45)
				{
				?>
					<td class="text-center"><?php echo to_money($nilai); ?></td>
					<td class="text-center"><?php echo to_money(0); ?></td>
					<td class="text-center"><?php echo to_money(0); ?></td>
					<?php $sub_45 	= $sub_45 + $nilai;?>
				<?php
				}
				else if($selisih <=60 && $selisih > 45) 
				{
				?>
					<td class="text-center"><?php echo to_money(0); ?></td>
					<td class="text-center"><?php echo to_money($nilai); ?></td>
					<td class="text-center"><?php echo to_money(0); ?></td>
					<?php $sub_4560 	= $sub_4560 + $nilai;?>
				<?php
				}
				else
				{
				?>
					<td class="text-center"><?php echo to_money(0); ?></td>
					<td class="text-center"><?php echo to_money(0); ?></td>
					<td class="text-center"><?php echo to_money($nilai); ?></td>
					<?php $sub_60 	= $sub_60 + $nilai;?>
				<?php
				}
				
				$isi_data++;
			}
				?>
				
			</tr>
			<?php
			$bln++;
			$iterasi++;
			
			if($bln > 12)
			{
				$bln 	= $bln % 12;
				$thn 	= $thn + 1; 
			}
				
			
		}
		?>

		<?php
		//jika terdapat rencana
		if($isi_data > 0)
		{
		?>
			<tr>
				<td></td>
				<td class="text-right">Sub Total</td>
				<td></td>
				<td></td>
				<td class="text-center"><?php echo to_money($sub_nilai); ?></td>
				<td class="text-center"><?php echo to_money($sub_45); ?></td>
				<td class="text-center"><?php echo to_money($sub_4560); ?></td>
				<td class="text-center"><?php echo to_money($sub_60); ?></td>
			</tr>
		<?php
		}	
		?>
		
		<?php
		$i++;
		$obj->movenext();
	}	
	?>
	
<?php	
}
?>


</table>

<table id="pagging-2" class="t-control"></table>

<script type="text/javascript">
jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());	
	$('#total-data').html('<?php echo $total_data; ?>');
	$('#per_page').val('<?php echo $per_page; ?>');
	$('.page_num').inputmask('integer');
	t_strip('.t-data');
});
</script>

<?php
close($conn);
exit;
?>