<?php
	require_once('rencana_proses.php');
	require_once('../../../../config/config.php');
	$conn = conn($sess_db);
	ex_conn($conn);
	
	$id				= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
	$act			= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
	
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
		
	</head>
	
	<script type="text/javascript">
	jQuery(function($) {
		$('#close').on('click', function(e) {
			e.preventDefault();
			return parent.loadData();
		});
		
		$('#save').on('click', function(e) {
			e.preventDefault();

			var id		= '<?php echo $id; ?>';
			var url		= base_marketing_transaksi + 'spp/rencana_proses.php',
			data		= $('#form').serialize();
			
			if (confirm("Apakah data telah terisi dengan benar ?") == false)
			{
				return false;
			}			

			$.post(url, data, function(data) {
				if (data.error == true)
				{
					alert(data.msg);
				}				
				else
				{
					
					alert(data.msg);
					parent.loadData();
				}
			}, 'json');
			return false;
		});
		
	});
	
	</script>
	<body class="popup2">
		
		<form name="form" id="form" method="POST">
			<table class="t-popup">
				<tr>
					<td><b>Kode Blok</b></td><td>:</td>
					<td><input readonly="readonly" type="text" name="kode_blok" id="kode_blok" size="10" value="<?php echo $id; ?>"></td>
				</tr>
				<tr>
					<td><b>Harga</b></td><td>:</td>
					<td><b>Rp. <input readonly="readonly" class="text-right" type="text" name="total_h" id="total_h" size="20" value="<?php echo to_money($r_harga_tanah + $r_harga_bangunan); ?>"></b></td>
					<input class="text-right" type="hidden" name="total" id="total" size="20" value="<?php echo ($r_harga_tanah + $r_harga_bangunan); ?>">
				<tr>
				<tr>
					<td><b>Tanda Jadi</b></td><td>:</td>
					<td><b>Rp. <input readonly="readonly" class="text-right" type="text" name="tanda_jadi" id="tanda_jadi" size="20" value="<?php echo to_money($tanda_jadi); ?>"></b></td>
				</tr>
				
				<tr>	
					<td>Pola Bayar</td><td>:</td>
					<td>
						<select name="pola_bayar" id="pola_bayar" required="true">
							<option value="" > --Pola Bayar-- </option>
							<?php
							$obj = $conn->execute("		
								SELECT * FROM POLA_BAYAR
								ORDER BY KODE_POLA_BAYAR 
							");
							while( ! $obj->EOF)
							{
								$ov = $obj->fields['KODE_POLA_BAYAR'];
								$oj = $obj->fields['NAMA_POLA_BAYAR'];
								echo "<option value='$ov'".is_selected($ov, $pola_bayar)."> $oj </option>";
								$obj->movenext();
							}
							?>
						</select>
					<td>
				</tr>
					
				<tr>	
					<td>Bank </td><td>:</td>					
								<td><select name="kbank" id="kbank">
										<option value="0"> -- Bank -- </option>
										<?php
											$obj = $conn->execute("
											SELECT *
											FROM 
											BANK
											");
											while( ! $obj->EOF)
											{
												$ov = $obj->fields['KODE_BANK'];
												$oj = $obj->fields['NAMA_BANK'];
												echo "<option value='$ov'".is_selected($ov, $kbank)."> $oj </option>";
												$obj->movenext();
											}
										?>
									</select>
								</td>
				</tr>
				<td width="150" class="text-right"></td>
				<tr>
					<td><input type="submit" id="save" value="Apply"> <input type="button" id="close" value=" Tutup ">
						</td>
					</tr>
					<tr>
						<td colspan="3"><br></td>
					</tr>
			</table>
				<input type="hidden" name="tgl_spp" id="tgl_spp" value="<?php echo $tgl_spp; ?>">
				<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
				<input type="hidden" name="act" id="act" value="Apply">
				<input type="hidden" name="kode_bayar" id="kode_bayar" value="4">
		</form>
	</body>
</html>
<?php close($conn); ?>