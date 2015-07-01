<?php
require_once('../../../../../config/config.php');
die_login();
// die_app('P');
die_mod('P11');
$conn = conn($sess_db);
die_conn($conn);

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;
$periode_awal		= (isset($_REQUEST['periode_awal'])) ? clean($_REQUEST['periode_awal']) : date('d-m-Y');
$periode_akhir		= (isset($_REQUEST['periode_akhir'])) ? clean($_REQUEST['periode_akhir']) : date('d-m-Y');


/* Pagination */
$query = "
SELECT count(*) as TOTAL
FROM SPP WHERE KODE_BLOK NOT IN 
(
SELECT DISTINCT a.KODE_BLOK FROM CS_PPJB a
JOIN SPP b on a.KODE_BLOK = b.KODE_BLOK
)
";

$total_data = $conn->execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
/* End Pagination */
?>

<table id="pagging-1" class="t-control w70">
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

<table class="t-nowrap t-data w70">
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">BLOK / NOMOR</th>
	<th rowspan="2">NAMA PEMBELI</th>
	<th rowspan="2">JENIS</th>
	<th colspan="2">SPP</th>
</tr>
<tr>
	<th colspan="1">TANGGAL</th>
	<th colspan="1">DISTRIBUSI</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT * FROM SPP WHERE KODE_BLOK NOT IN 
	(
	SELECT DISTINCT a.KODE_BLOK FROM CS_PPJB a
	JOIN SPP b on a.KODE_BLOK = b.KODE_BLOK
	)
	";

	$obj = $conn->selectlimit($query, $per_page, $page_start);
	$i = 1 + $page_start;
	while( ! $obj->EOF)
	{
		$id = $obj->fields['KODE_BLOK'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_PEMBELI']; ?></td>
			<td><?php echo sistem_pembayaran($obj->fields['STATUS_KOMPENSASI']); ?></td>
			<td><?php echo tgltgl(date("d-m-Y", strtotime($obj->fields['TANGGAL_SPP']))); ?></td>
			<td><?php echo tgltgl(date("d-m-Y", strtotime($obj->fields['TANGGAL_TANDA_JADI']))); ?></td>
		</tr>
		<?php
		$i++;
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control w70"></table>

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