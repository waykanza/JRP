<?php
require_once('../../../../config/config.php');
die_login();
die_app('M');
die_mod('M26');
$conn = conn($sess_db);
die_conn($conn);

$query = "SELECT * FROM CS_PARAMETER_MARK";
$obj = $conn->Execute($query);
?>

<script type="text/javascript">
jQuery(function($) {
	$('.dd-mm-yyyy').Zebra_DatePicker({
		format: 'd-m-Y',
		readonly_element : false,
		inside: true
	});
	
	$('#nama_pt').inputmask('varchar', { repeat: '40' }); 
	$('#unit').inputmask('varchar', { repeat: '30' }); 
	$('#nama_dep').inputmask('varchar', { repeat: '40' });
	$('#nama_pejabat').inputmask('varchar', { repeat: '30' }); 
	$('#nama_jabatan').inputmask('varchar', { repeat: '30' });
	$('#kota').inputmask('varchar', { repeat: '20' }); 
	
	$('#pejabat_ppjb').inputmask('varchar', { repeat: '30' });
	$('#jabatan_ppjb').inputmask('varchar', { repeat: '30' }); 
	$('#nomor_sk').inputmask('varchar', { repeat: '25' });
	$('#jumlah_hari').inputmask('numeric', { repeat: '2' }); 
	$('#nomor_ppjb').inputmask('numeric', { repeat: '4' });
	$('#reg_ppjb').inputmask('varchar', { repeat: '20' });
});
</script>

	<table class="t-form">
		<tr><td colspan="6"><br></td></tr>
		<tr>
			<td colspan="6" class="text-center"><br><b>D I S T R I B U S I</b><hr></td>
		</tr>
		
		<tr>
			<td width="150">Batas Distribusi</td><td width="1">:</td>
			<td><input type="text" name="batas_distribusi" id="batas_distribusi" size="20" value="<?php echo $obj->fields['BATAS_DISTRIBUSI']; ?>"></td>
		</tr>
		<tr>
			<td width="150">Tenggang Distribusi</td><td width="1">:</td>
			<td><input type="text" name="tenggang_distribusi" id="tenggang_distribusi" size="20" value="<?php echo $obj->fields['TENGGANG_DISTRIBUSI']; ?>"></td></td>
		</tr>
		<tr>
			<td colspan="6" class="text-center"><br><b>V I R T U A L    A C C O U N T</b><hr></td>
		</tr>
		
		<tr>
			<td width="250">Kode Virtual Account BCA Unit</td><td width="1">:</td>
			<td><input type="text" name="va_bca" id="va_bca" size="20" value="<?php echo $obj->fields['VA_BCA_UNIT']; ?>"></td>
		</tr>
		<tr>
			<td width="250">Kode Virtual Account Mandiri Unit</td><td width="1">:</td>
			<td><input type="text" name="va_mandiri" id="va_mandiri" size="20" value="<?php echo $obj->fields['VA_MANDIRI_UNIT']; ?>"></td></td>
		</tr>
		
	</table>

<?php
close($conn);
exit;
?>