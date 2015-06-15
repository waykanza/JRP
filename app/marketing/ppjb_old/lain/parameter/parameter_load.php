<?php
require_once('../../../../../config/config.php');
die_login();
die_app('A01');
die_mod('JB12');
$conn = conn($sess_db);
die_conn($conn);

$query = "SELECT * FROM CS_PARAMETER_PPJB";
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
			<td colspan="6" class="text-center"><br><b>P E R U S A H A A N</b><hr></td>
		</tr>
		
		<tr>
			<td width="70">Nama PT</td><td width="1">:</td>
			<td><input type="text" name="nama_pt" id="nama_pt" size="40" value="<?php echo $obj->fields['NAMA_PT']; ?>"></td>
			<td width="80">Pejabat</td><td width="1">:</td>
			<td><input type="text" name="nama_pejabat" id="nama_pejabat" size="30" value="<?php echo $obj->fields['NAMA_PEJABAT']; ?>"></td></td>
		</tr>
		
		<tr>
			<td>Unit</td><td>:</td>
			<td><input type="text" name="unit" id="unit" size="30" value="<?php echo $obj->fields['UNIT']; ?>"></td>
			<td>Jabatan</td><td>:</td>
			<td><input type="text" name="nama_jabatan" id="nama_jabatan" size="30" value="<?php echo $obj->fields['NAMA_JABATAN']; ?>"></td>
		</tr>
		
		<tr>
			<td>Departemen</td><td>:</td>
			<td><input type="text" name="nama_dep" id="nama_dep" size="40" value="<?php echo $obj->fields['NAMA_DEP']; ?>"></td>
			<td>Kota</td><td>:</td>
			<td><input type="text" name="kota" id="kota" size="20" class="dd" value="<?php echo $obj->fields['KOTA']; ?>"></td>
		</tr>
		
		<tr>
			<td colspan="6" class="text-center"><br><br><b>PERJANJIAN PENGIKATAN JUAL BELI (PPJB)</b><hr></td>
		</tr>
		
		<tr>
			<td>Pejabat</td><td>:</td>
			<td><input type="text" name="pejabat_ppjb" id="pejabat_ppjb" size="30" value="<?php echo $obj->fields['PEJABAT_PPJB']; ?>"></td>
			<td>Tanggal SK</td><td>:</td>
			<td><input type="text" name="tanggal_sk" id="tanggal_sk" size="15" class="apply dd-mm-yyyy" value="<?php echo tgltgl(date("d-m-Y", strtotime($obj->fields['TANGGAL_SK']))); ?>"></td></td>
		</tr>
		
		<tr>
			<td>Jabatan</td><td>:</td>
			<td><input type="text" name="jabatan_ppjb" id="jabatan_ppjb" size="30" value="<?php echo $obj->fields['JABATAN_PPJB']; ?>"></td>
			<td>Jml. Hari Kerja</td><td>:</td>
			<td><input type="text" name="jumlah_hari" id="jumlah_hari" size="2" value="<?php echo $obj->fields['JUMLAH_HARI']; ?>"> hari</td>
		</tr>
		
		<tr>
			<td>SK No.</td><td>:</td>
			<td><input type="text" name="nomor_sk" id="nomor_sk" size="30" value="<?php echo $obj->fields['NOMOR_SK']; ?>"></td>
			<td width="100">No. PPJB Akhir</td><td>:</td>
			<td>
			<input type="text" name="nomor_ppjb" id="nomor_ppjb" size="4" value="<?php echo $obj->fields['NOMOR_PPJB']; ?>">
			<input type="text" name="reg_ppjb" id="reg_ppjb" size="20" value="<?php echo $obj->fields['REG_PPJB']; ?>">
			</td>
		</tr>
	</table>

<?php
close($conn);
exit;
?>