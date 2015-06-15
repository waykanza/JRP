<?php
require_once('../../../../../config/config.php');
die_login();
die_app('A01');
die_mod('JB12');
$conn = conn($sess_db);
die_conn($conn);
$msg = '';
$error = FALSE;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		ex_app('A01');
		ex_mod('JB12');
		$conn = conn($sess_db);
		ex_conn($conn);
		
		ex_ha('JB12', 'U');
		
		$conn->begintrans();
		
		$nama_pt		= (isset($_REQUEST['nama_pt'])) ? clean($_REQUEST['nama_pt']) : '';
		$unit			= (isset($_REQUEST['unit'])) ? clean($_REQUEST['unit']) : '';
		$nama_dep		= (isset($_REQUEST['nama_dep'])) ? clean($_REQUEST['nama_dep']) : '';
		$nama_pejabat	= (isset($_REQUEST['nama_pejabat'])) ? clean($_REQUEST['nama_pejabat']) : '';
		$nama_jabatan	= (isset($_REQUEST['nama_jabatan'])) ? clean($_REQUEST['nama_jabatan']) : '';
		$kota			= (isset($_REQUEST['kota'])) ? clean($_REQUEST['kota']) : '';
		$pejabat_ppjb	= (isset($_REQUEST['pejabat_ppjb'])) ? clean($_REQUEST['pejabat_ppjb']) : '';
		$jabatan_ppjb	= (isset($_REQUEST['jabatan_ppjb'])) ? clean($_REQUEST['jabatan_ppjb']) : '';
		$nomor_sk		= (isset($_REQUEST['nomor_sk'])) ? clean($_REQUEST['nomor_sk']) : '';
		$jumlah_hari	= (isset($_REQUEST['jumlah_hari'])) ? clean($_REQUEST['jumlah_hari']) : '';
		$nomor_ppjb		= (isset($_REQUEST['nomor_ppjb'])) ? to_number($_REQUEST['nomor_ppjb']) : '';
		$reg_ppjb		= (isset($_REQUEST['reg_ppjb'])) ? clean($_REQUEST['reg_ppjb']) : '';
		$tanggal_sk		= (isset($_REQUEST['tanggal_sk'])) ? clean($_REQUEST['tanggal_sk']) : '';
		
		$query = "SELECT * FROM CS_PARAMETER_PPJB WHERE NAMA_PT = '$nama_pt' AND NAMA_DEP = '$nama_dep' AND NAMA_PEJABAT = '$nama_pejabat' AND
				NAMA_JABATAN = '$nama_jabatan' AND PEJABAT_PPJB = '$pejabat_ppjb' AND JABATAN_PPJB = '$jabatan_ppjb' AND NOMOR_SK = '$nomor_sk' AND 
				TANGGAL_SK = CONVERT(DATETIME,'$tanggal_sk',105) AND NOMOR_PPJB = '$nomor_ppjb' AND REG_PPJB = '$reg_ppjb' AND 
				JUMLAH_HARI = '$jumlah_hari' AND UNIT = '$unit' AND KOTA = '$kota'
				";
		ex_found($conn->Execute($query)->recordcount(), "Tidak ada data yang berubah.");
		
		$conn->Execute("DELETE FROM CS_PARAMETER_PPJB");
		
		$query = "
		INSERT INTO CS_PARAMETER_PPJB (
		NAMA_PT, NAMA_DEP, NAMA_PEJABAT, NAMA_JABATAN, PEJABAT_PPJB, JABATAN_PPJB, NOMOR_SK, TANGGAL_SK, NOMOR_PPJB, REG_PPJB, JUMLAH_HARI, UNIT, KOTA
		)
		VALUES (
		'$nama_pt', '$nama_dep', '$nama_pejabat', '$nama_jabatan', '$pejabat_ppjb', '$jabatan_ppjb', '$nomor_sk', CONVERT(DATETIME,'$tanggal_sk',105), '$nomor_ppjb', '$reg_ppjb', '$jumlah_hari', '$unit', '$kota'
		)
		";
		
		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
		
		$msg = 'Parameter berhasil diubah.';
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$error = TRUE;
		$conn->rollbacktrans();
	}

	close($conn);
	$json = array('msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}
?>