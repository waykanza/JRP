<?php
require_once('../../../../../../config/config.php');
$conn = conn($sess_db);
die_conn($conn);

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$field1		= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1	= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';

$query_search = '';
if ($search1 != '')
{
	$query_search .= " and $field1 LIKE '%$search1%' ";
}

/* Pagination */
$query = "
select count(a.KODE_BLOK) 
from CS_INFORMASI_DENDA a join SPP b
on a.KODE_BLOK = b.KODE_BLOK
where a.KODE_OTORISASI is null $query_search
group by a.KODE_BLOK
";
$n = 0;
$obj = $conn->execute($query);
while( ! $obj->EOF)
{
	$n++;
	$obj->movenext();
}
$total_data = $n;
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
/* End Pagination */
?>

<table id="pagging-1" class="t-control w60">
<tr>
	<td class="text-right">
		<input type="button" id="prev_page" value=" < ">
		Hal : <input type="text" name="page_num" size="5" class="page_num apply text-center" value="<?php echo $page_num; ?>">
		Dari <?php echo $total_page ?> 
		<input type="hidden" id="total_page" value="<?php echo $total_page; ?>">
		<input type="button" id="next_page" value=" > ">
	</td>
</tr>
</table>

<table class="t-data w60">
<tr>
	<th class="w10">BLOK / NOMOR</th>
	<th class="w40">NAMA PEMBELI</th>
	<th class="w40">TANGGAL</th>
	<th class="w20">NO VIRTUAL ACCOUNT</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	select a.KODE_BLOK, a.TANGGAL ,b.NAMA_PEMBELI, NOMOR_CUSTOMER = CASE WHEN b.NOMOR_CUSTOMER IS null 
	THEN '-' ELSE b.NOMOR_CUSTOMER END
	from CS_INFORMASI_DENDA a join SPP b
	on a.KODE_BLOK = b.KODE_BLOK
	where a.KODE_OTORISASI is null $query_search
	order by a.TANGGAL desc
	";
	$obj = $conn->selectlimit($query, $per_page, $page_start);

	while( ! $obj->EOF)
	{
		$id = $obj->fields['KODE_BLOK'];		
		$tanggal = $obj->fields['TANGGAL'];		

		?>
		<tr class="onclick" id="<?php echo $id.' '.$tanggal; ?>"> 
			<td><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_PEMBELI'];  ?></td>
			<td class="text-center"><?php echo fm_date(date("Y-m-d", strtotime($obj->fields['TANGGAL'])));  ?></td>
			<td class="text-center"><?php echo $obj->fields['NOMOR_CUSTOMER'];  ?></td>
			</tr>
		<?php
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control w60"></table>

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