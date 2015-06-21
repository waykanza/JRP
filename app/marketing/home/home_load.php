<?php

include('../../../config/config.php');

$conn = conn($sess_db);
die_conn($conn);

$query = "select COUNT(STATUS_SPP) AS TOTAL_BELUM_DISTRIBUSI FROM SPP WHERE STATUS_SPP IS NULL OR STATUS_SPP != 1";
$obj = $conn->execute($query);

$belum_distribusi		= $obj->fields['TOTAL_BELUM_DISTRIBUSI'];

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

<table class="t-control wauto">
<tr>
	<td>Jumlah SPP belum distribusi</td><td>:</td>
	<td>
		<input type="text" name="belum_distribusi" size="3" id="per_page" class=" apply text-center" value="<?php echo $belum_distribusi; ?>">
		<input type="button" name="detail_distribusi" id="detail_distribusi" value=" Detail ">
	</td>
</tr>
<tr>
	<td>Jumlah SPP belum PPJB</td><td>:</td>
	<td>
		<input type="text" name="belum_ppjb" size="3" id="belum_ppjb" class=" apply text-center" value="<?php echo $belum_ppjb; ?>">
		<input type="button" name="detail_ppjb" id="detail_ppjb" value=" Detail ">
	</td>
</tr>

</table>

<script type="text/javascript">
jQuery(function($) {
	
});
</script>

<?php
close($conn);
exit;
?>