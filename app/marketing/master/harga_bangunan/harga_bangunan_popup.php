<?php
require_once('harga_bangunan_proses.php');
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
var this_base = base_marketing + 'master/harga_bangunan/';

jQuery(function($) {
	
	$('#kode_sk').inputmask('integer', { repeat: 4 });
	$('#harga_bangunan').inputmask('numeric', { repeat: '10' });
	
	$('#tutup').on('click', function(e) {
		e.preventDefault();
		parent.loadData();
	});
	
	$('#simpan').on('click', function(e) {
		e.preventDefault();
		var url		= this_base + 'harga_bangunan_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(result) {
			
			alert(result.msg);
			if (result.error == false) {
				if (result.act == 'Tambah') {
					$('#reset').click();
					parent.loadData();
				} else if (result.act == 'Ubah') {
					parent.loadData();
				}
			}
		}, 'json');
		
		return false;
	});
});
</script>
</head>
<body class="popup">
<form name="form" id="form" method="post">
<table>
<input type="hidden" name="kode_sk" id="kode_sk" size="3" value="<?php echo $kode_sk; ?>">
<tr>
	<td>Lokasi</td><td>:</td>
	<td>
		<select name="kode_lokasi" id="kode_lokasi">
			<option value=""> -- Pilih -- </option>
			<?php
			$obj = $conn->Execute("SELECT * FROM LOKASI");
			
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_LOKASI'];
				$on = $obj->fields['LOKASI'];
				echo "<option value='$ov' " . is_selected($ov, $kode_lokasi) . "> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>

<tr>
	<td>Jenis Bangunan</td><td>:</td>
	<td>
		<select name="jenis_bangunan" id="jenis_bangunan">
		<option value=""> -- Pilih -- </option>
			<option value="1" <?php echo is_selected('1', $jenis_bangunan); ?>> STANDARD </option>
			<option value="2" <?php echo is_selected('2', $jenis_bangunan); ?>> SUDUT </option>
			<option value="3" <?php echo is_selected('3', $jenis_bangunan); ?>> KHUSUS </option>
			<option value="4" <?php echo is_selected('4', $jenis_bangunan); ?>> LAIN-LAIN </option>
		</select>
	</td>
</tr>

<tr>
	<td>Harga Bangunan</td><td>:</td>
	<td><input type="text" name="harga_bangunan" id="harga_bangunan" value="<?php echo to_money($harga_bangunan); ?>" size="20"></td>
</tr>

<tr>
	<td>Tanggal</td><td>:</td>
	<td><input type="text" name="tanggal" id="tanggal" value="<?php echo $tanggal; ?>" class="apply dd-mm-yyyy" size="12"></td>
</tr>

<tr>
	<td>Status</td><td>:</td>
	<td><input type="checkbox" name="status" id="status" value="1" <?php echo is_checked($status, '1'); ?>></td>
</tr>

<tr>
	<td colspan="3"><br>
		<input type="submit" id="simpan" value=" Simpan ">
		<input type="reset" id="reset" value=" Reset ">
		<input type="button" id="tutup" value=" Tutup ">
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php close($conn); ?>