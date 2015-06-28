<?php
require_once('pola_pembayaran_proses.php');
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
var this_base = base_marketing + 'master/pola_pembayaran/';

jQuery(function($) {
	
	$('#kode_bayar').inputmask('integer', { repeat: 3 });
	$('#jenis_bayar').inputmask('varchar', { repeat: 30 });
	
	var dua = <?php echo $non2; ?>;
	var tiga = <?php echo $non3; ?>;
	var empat = <?php echo $non4; ?>;
	var lima = <?php echo $non5; ?>;
	var jenis = <?php echo $non_jenis; ?>;
	
	if(dua == 0){
		$('#nilai2').prop('disabled', true);
		$('#kali2').prop('disabled', true);
	}
	if(tiga == 0){
		$('#nilai3').prop('disabled', true);
		$('#kali3').prop('disabled', true);
	}
	if(empat == 0){
		$('#nilai4').prop('disabled', true);
		$('#kali4').prop('disabled', true);
	}
	if(lima == 0){
		$('#nilai5').prop('disabled', true);
		$('#kali5').prop('disabled', true);
	}
	if(jenis == 0){
		$('#nilai_jenis').prop('disabled', true);
	}
	
	$('#cb2').click(function() {
        if (!$(this).is(':checked')) {
			$('#nilai2').prop('disabled', true);
			$('#kali2').prop('disabled', true);
			$('#nilai2').val(0);
			$('#kali2').val(0);
        }
		else{
			$('#nilai2').prop('disabled', false);
			$('#kali2').prop('disabled', false);
			$('#nilai2').val('');
			$('#kali2').val('');
		}
    });
	
	$('#cb3').click(function() {
        if (!$(this).is(':checked')) {
			$('#nilai3').prop('disabled', true);
			$('#kali3').prop('disabled', true);
			$('#nilai3').val(0);
			$('#kali3').val(0);
        }
		else{
			$('#nilai3').prop('disabled', false);
			$('#kali3').prop('disabled', false);
			$('#nilai3').val('');
			$('#kali3').val('');
		}
    });
	
	$('#cb4').click(function() {
        if (!$(this).is(':checked')) {
			$('#nilai4').prop('disabled', true);
			$('#kali4').prop('disabled', true);
			$('#nilai4').val(0);
			$('#kali4').val(0);
        }
		else{
			$('#nilai4').prop('disabled', false);
			$('#kali4').prop('disabled', false);
			$('#nilai4').val('');
			$('#kali4').val('');
		}
    });
	
	$('#cb5').click(function() {
        if (!$(this).is(':checked')) {
			$('#nilai5').prop('disabled', true);
			$('#kali5').prop('disabled', true);
			$('#nilai5').val(0);
			$('#kali5').val(0);
        }
		else{
			$('#nilai5').prop('disabled', false);
			$('#kali5').prop('disabled', false);
			$('#nilai5').val('');
			$('#kali5').val('');
		}
    });
	
	$('#cb_jenis').click(function() {
        if (!$(this).is(':checked')) {
			$('#nilai_jenis').prop('disabled', true);
			$('#nilai_jenis').val(0);
        }
		else{
			$('#nilai_jenis').prop('disabled', false);
			$('#nilai_jenis').val('');
		}
    });
	
	

	$('#tutup').on('click', function(e) {
		e.preventDefault();
		parent.loadData();
	});
	
	$('#simpan').on('click', function(e) {
		e.preventDefault();
		var url		= this_base + 'pola_pembayaran_proses.php',
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

<input type="hidden" name="kode_pola_bayar" id="kode_pola_bayar" size="3" value="<?php echo $kode_pola_bayar; ?>">

<table>
<tr>
	<td>Jenis Pembayaran</td><td>:</td>
	<td>
		<select name="kode_jenis" id="kode_jenis" class="wauto">
			<option value="1" <?php echo is_selected('1', $kode_jenis); ?>>KPR</option>
			<option value="2" <?php echo is_selected('2', $kode_jenis); ?>>TUNAI</option>
		</select>
	</td>
</tr>
<tr>
	<td>Nama Pola Pembayaran</td><td>:</td>
	<td><input type="text" name="nama_pola_bayar" id="nama_pola_bayar" size="30" value="<?php echo $nama_pola_bayar; ?>"></td>
</tr>
<tr>
	<td>Rumus Penggunaan</td><td>:</td>
</tr>
</table>

<table>

<tr>
	<td></td>
	<td></td>
	<td>Presentase</td>
	<td>Perkalian</td>
</tr>
<tr>
	<td></td>
	<td>1.</td>
	<td><input type="text" name="nilai1" id="nilai1" size="20" value="<?php echo $nilai1; ?>"></td>
	<td><input type="text" name="kali1" id="kali1" size="10" value="<?php echo $kali1; ?>"></td>
</tr>
<tr>
	<td><input type="checkbox" name="cb2" id="cb2"></td>
	<td>2.</td>
	<td><input type="text" name="nilai2" id="nilai2" size="20" value="<?php echo $nilai2; ?>"></td>
	<td><input type="text" name="kali2" id="kali2" size="10" value="<?php echo $kali2; ?>"></td>
</tr>
<tr>
	<td><input type="checkbox" name="cb3" id="cb3"></td>
	<td>3.</td>
	<td><input type="text" name="nilai3" id="nilai3" size="20" value="<?php echo $nilai3; ?>"></td>
	<td><input type="text" name="kali3" id="kali3" size="10" value="<?php echo $kali3; ?>"></td>
</tr>
<tr>
	<td><input type="checkbox" name="cb4" id="cb4"></td>
	<td>4.</td>
	<td><input type="text" name="nilai4" id="nilai4" size="20" value="<?php echo $nilai4; ?>"></td>
	<td><input type="text" name="kali4" id="kali4" size="10" value="<?php echo $kali4; ?>"></td>
</tr>
<tr>
	<td><input type="checkbox" name="cb5" id="cb5"></td>
	<td>5.</td>
	<td><input type="text" name="nilai5" id="nilai5" size="20" value="<?php echo $nilai5; ?>"></td>
	<td><input type="text" name="kali5" id="kali5" size="10" value="<?php echo $kali5; ?>"></td>
</tr>
<tr>
	<td><input type="checkbox" name="cb_jenis" id="cb_jenis"></td>
	<td></td>
	<td>SISA MENJADI KPR</td>
	<td><input type="text" name="nilai_jenis" id="nilai_jenis" size="10" value="<?php echo $nilai_jenis; ?>">&#37;</td>
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