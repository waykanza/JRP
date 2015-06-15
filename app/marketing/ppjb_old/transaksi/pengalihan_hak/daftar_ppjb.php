<?php
require_once('../../../../../config/config.php');
$conn = conn($sess_db);
ex_conn($conn);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	$(document).on('click', 'tr.onclick', function(e) {
		e.preventDefault();
		var kode_blok = $(this).data('kode_blok'),
			nomor = $(this).data('nomor'),
			tanggal = $(this).data('tanggal'),
			nama = $(this).data('nama'),
			no_identitas = $(this).data('no_identitas'),
			alamat = $(this).data('alamat'),
			tlp_rmh = $(this).data('tlp_rmh'),
			tlp_lain = $(this).data('tlp_lain'),
			email = $(this).data('email'),
			suami_istri = $(this).data('suami_istri'),
			fax = $(this).data('fax'),
			harga = $(this).data('harga');
		
		parent.jQuery('#kode').val(kode_blok);
		parent.jQuery('#no_ppjb_awal').val(nomor);
		parent.jQuery('#tanggal_awal').val(tanggal);
		parent.jQuery('#pihak_pertama').val(nama);
		parent.jQuery('#no_id').val(no_identitas);
		parent.jQuery('#alamat').val(alamat);
		parent.jQuery('#tlp1').val(tlp_rmh);
		parent.jQuery('#tlp3').val(tlp_lain);
		parent.jQuery('#email').val(email);
		parent.jQuery('#suami_istri').val(suami_istri);
		parent.jQuery('#no_fax').val(fax);
		parent.jQuery('#harga_awal').val(harga);
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
	<th class="w15">BLOK / NOMOR</th>
	<th class="w20">NO. PPJB</th>
	<th class="w20">NAMA PEMBELI</th>
</tr>
<?php
$query = "
		SELECT *, z.TANGGAL
		FROM
			CS_PPJB z
			JOIN SPP a ON z.KODE_BLOK = a.KODE_BLOK
			LEFT JOIN STOK b ON a.KODE_BLOK = b.KODE_BLOK
			LEFT JOIN TIPE c ON b.KODE_TIPE = c.KODE_TIPE
			LEFT JOIN HARGA_TANAH d ON b.KODE_SK_TANAH = d.KODE_SK
			LEFT JOIN HARGA_BANGUNAN e ON b.KODE_SK_BANGUNAN = e.KODE_SK
			LEFT JOIN FAKTOR f ON b.KODE_FAKTOR = f.KODE_FAKTOR
";
$obj = $conn->Execute($query);
			
while( ! $obj->EOF)
{
			$luas_tanah 		= $obj->fields['LUAS_TANAH'];
			$luas_bangunan 		= $obj->fields['LUAS_BANGUNAN'];
	
			$tanah 				= $luas_tanah * ($obj->fields['HARGA_TANAH']) ;
			$disc_tanah 		= round($tanah * ($obj->fields['DISC_TANAH'])/100,0) ;
			$nilai_tambah		= round(($tanah - $disc_tanah) * ($obj->fields['NILAI_TAMBAH'])/100,0) ;
			$nilai_kurang		= round(($tanah - $disc_tanah) * ($obj->fields['NILAI_KURANG'])/100,0) ;
			$faktor				= $nilai_tambah - $nilai_kurang;
			$total_tanah		= $tanah - $disc_tanah + $faktor;
			$ppn_tanah 			= round($total_tanah * ($obj->fields['PPN_TANAH'])/100,0) ;
	
			$bangunan 			= $luas_bangunan * ($obj->fields['HARGA_BANGUNAN']) ;
			$disc_bangunan 		= round($bangunan * ($obj->fields['DISC_BANGUNAN'])/100,0) ;
			$total_bangunan		= $bangunan - $disc_bangunan;
			$ppn_bangunan 		= round($total_bangunan * ($obj->fields['PPN_BANGUNAN'])/100,0) ;
			
			$harga	= ($total_tanah + $total_bangunan) + ($ppn_tanah + $ppn_bangunan);	
	?>
	<tr class="onclick" 
		data-kode_blok="<?php echo $obj->fields['KODE_BLOK']; ?>"
		data-nomor="<?php echo $obj->fields['NOMOR']; ?>"
		data-tanggal="<?php echo f_tgl($obj->fields['TANGGAL']); ?>"
		data-nama="<?php echo $obj->fields['NAMA_PEMBELI']; ?>"
		data-no_identitas="<?php echo $obj->fields['NO_IDENTITAS']; ?>"
		data-alamat="<?php echo $obj->fields['ALAMAT_RUMAH']; ?>"
		data-tlp_rmh="<?php echo $obj->fields['TELP_RUMAH']; ?>"
		data-tlp_lain="<?php echo $obj->fields['TELP_LAIN']; ?>"
		data-email="<?php echo $obj->fields['ALAMAT_EMAIL']; ?>"
		data-suami_istri="<?php echo $obj->fields['NAMA_SUAMI_ISTRI']; ?>"
		data-fax="<?php echo $obj->fields['NO_FAX']; ?>"
		data-harga="<?php echo to_money($harga); ?>">
		<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
		<td><?php echo $obj->fields['NOMOR']; ?></td>
		<td><?php echo $obj->fields['NAMA_PEMBELI']; ?></td>
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