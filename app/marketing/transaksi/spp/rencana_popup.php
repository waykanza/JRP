<?php
	require_once('rencana_proses.php');
	require_once('spp_proses.php');
	require_once('../../../../config/config.php');
	$conn = conn($sess_db);
	ex_conn($conn);
	$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
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
			
			var get_base = base_marketing + 'operasional/get/';
			$(function() {
				$('#keterangan').inputmask('varchar', { repeat: '30' });
				$('#tanggal').inputmask('date');
				
				$('#close').on('click', function(e) {
					e.preventDefault();
					return parent.loadData();
				});
				
				$('#save').on('click', function(e) {
					
					e.preventDefault();
					var url = base_marketing_transaksi + 'spp/rencana_proses.php',
					data	= $('#form').serialize();
					
					$.post(url, data, function(data) {
						if (data.error == true)
						{
							 window.onunload = refreshParent;
								function refreshParent() {
									window.opener.location.reload();
								}
						}
						else
						{
							if (data.act == 'Apply')
							{
								alert(data.msg);
								//parent.loadData();
								 window.onunload = refreshParent;
									function refreshParent() {
										window.opener.location.reload();
									}
								$('#reset').click();
							}
							else if (data.act == 'Hapus')
							{
								alert(data.msg);
								parent.loadData();
							}
						}
					}, '');
					return false;
				});
			});
			
			function get_kode_bayar() {
				var url = get_base + 'kode_bayar.php'; 
				setPopup('Daftar Jenis Pembayaran', url, 300, winHeight-100); 
				return false; 
			}
		</script>
	</head>
	<body class="">
		
		<form name="form" id="form" method="post">
			<table class="wauto">
				<tr>
					<td><b>Kode Blok</b></td><td>:</td>
					<td><input readonly="readonly" type="text" name="kode_blok" id="kode_blok" size="10" value="<?php echo $id; ?>"></td>
				</tr>
				<tr>
					<td><b>Harga</b></td><td>:</td>
					<td><b>Rp. <input type="text" name="total_harga" id="tanda_jadi" size="20" value="<?php echo ($r_harga_tanah + $r_harga_bangunan); ?>"></b></td>
				<tr>
				<tr>
					<td><b>Tanda Jadi</b></td><td>:</td>
					<td> <input type="text" name="tanda_jadi" id="tanda_jadi" size="20" value="<?php echo $tanda_jadi; ?>"></td>
				</tr>
				
				<tr>	
					<td>Tanggal Rencana</td><td>:</td>
					<td><input type="text" name="tanggal" id="tanggal" class="apply dd-mm-yyyy" size="15" value=""></td>
				</tr>
				<tr>
					<td>Jenis Pembayaran</td><td>:</td>
					<td>
						<input type="hidden" name="kode_bayar" id="kode_bayar" size="1" value="">
						<input type="text" name="jumlah" id="jumlah" size="15" value="" placeholder="jumlah angsuran">
						<button onclick="return get_kode_bayar()"> > </button>
						<input type="text" id="jenis_bayar" size="25" value="">
					</td>
				</tr>
				
				<tr>	
					<td>Keterangan</td><td>:</td>
					<td><input type="text" name="keterangan" id="keterangan" size="45" value=""></td>
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
					
					<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
					<input type="hidden" name="act" id="act" value="Apply">
				</form>
			</body>
		</html>
	<?php close($conn); ?>		