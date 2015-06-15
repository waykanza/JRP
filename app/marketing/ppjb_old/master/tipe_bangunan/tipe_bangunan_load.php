<?php
require_once('../../../../../config/config.php');
die_login();
die_app('A01');
die_mod('JB05');
$conn = conn($sess_db);
die_conn($conn);

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$field1		= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1	= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';

$query_search = '';
if ($search1 != '')
{
	$query_search .= " WHERE $field1 LIKE '%$search1%' ";
}

# Pagination
$query = "
SELECT 
	COUNT(KODE_TIPE) AS TOTAL
FROM 
	TIPE
$query_search
";
$total_data = $conn->execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination 
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
	<th class="w5">KODE</th>
	<th class="w30">TIPE BANGUNAN</th>
	<th class="w20">DAYA LISTRIK <br> (Watt)</th>
	<th class="w20">MASA BANGUN <br> (Bulan)</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT *
	FROM 
		TIPE
	$query_search
	ORDER BY KODE_TIPE
	";
	$obj = $conn->selectlimit($query, $per_page, $page_start);

	while( ! $obj->EOF)
	{
		$id = $obj->fields['KODE_TIPE'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $id; ?></td>
			<td><?php echo $obj->fields['TIPE_BANGUNAN'];  ?></td>
			<td class="text-right"><?php echo $obj->fields['DAYA_LISTRIK']; ?></td>
			<td class="text-right"><?php echo masabangun($obj->fields['MASA_BANGUN']); ?></td>
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