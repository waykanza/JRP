<?php
require_once('../../../../config/config.php');
$conn = conn($sess_db);
ex_conn($conn);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	$(document).on('click', 'tr.onclick', function(e) {
		e.preventDefault();
		var kode_sk = $(this).data('kode_sk'),
			harga_bangunan = $(this).data('harga_bangunan');
		
		parent.jQuery('#kode_sk_bangunan').val(kode_sk);
		parent.jQuery('#harga_bangunan_sk').val(harga_bangunan);
		parent.window.focus();
		parent.window.popup.close();
		
		return false;
	});
	
	t_strip('.t-data');
});
</script>
</head>
<body class="popup">
<form name="form" id="form" method="post">

<table class="t-data">

<tr>
	<th>TGL SK</th>
	<th>LOKASI</th>
	<th>JENIS</th>
	<th>HARGA / m&sup2;</th>
</tr>

<?php
$query = "
SELECT
	hb.KODE_SK,
	hb.TANGGAL,
	l.LOKASI,
	hb.JENIS_BANGUNAN,
	hb.HARGA_BANGUNAN
FROM
	HARGA_BANGUNAN hb
	LEFT JOIN LOKASI l ON hb.KODE_LOKASI = l.KODE_LOKASI
WHERE hb.STATUS = '1'
ORDER BY hb.KODE_SK ASC
";

$obj = $conn->Execute($query);
while( ! $obj->EOF)
{
	?>
	<tr class="onclick" 
		data-kode_sk="<?php echo $obj->fields['KODE_SK']; ?>"
		data-harga_bangunan="<?php echo to_money($obj->fields['HARGA_BANGUNAN'],2); ?>">
		<td><?php echo date("d-m-Y", strtotime($obj->fields['TANGGAL'])); ?></td>
		<td><?php echo $obj->fields['LOKASI']; ?></td>
		<td><?php echo jenis_bangunan($obj->fields['JENIS_BANGUNAN']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['HARGA_BANGUNAN'],2); ?></td>
	</tr>
	<?php
	$obj->movenext();
}
?>
</table>

</form>

</body>
</html>
<?php close($conn); ?>