<?php
require_once('../../../../config/config.php');
die_login();
die_app('C01');
die_mod('COL01');
$conn = conn($sess_db);
die_conn($conn);
$msg = '';
$error = FALSE;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		$nama_pt				= (isset($_REQUEST['nama_pt'])) ? clean($_REQUEST['nama_pt']) : '';
		$nama_dep				= (isset($_REQUEST['nama_dep'])) ? clean($_REQUEST['nama_dep']) : '';
		$nama_pejabat			= (isset($_REQUEST['nama_pejabat'])) ? clean($_REQUEST['nama_pejabat']) : '';
		$nama_jabatan			= (isset($_REQUEST['nama_jabatan'])) ? clean($_REQUEST['nama_jabatan']) : '';
		$pemb_jatuh_tempo		= (isset($_REQUEST['pemb_jatuh_tempo'])) ? clean($_REQUEST['pemb_jatuh_tempo']) : '';
		$somasi_satu			= (isset($_REQUEST['somasi_satu'])) ? clean($_REQUEST['somasi_satu']) : '';
		$somasi_dua				= (isset($_REQUEST['somasi_dua'])) ? clean($_REQUEST['somasi_dua']) : '';
		$somasi_tiga			= (isset($_REQUEST['somasi_tiga'])) ? clean($_REQUEST['somasi_tiga']) : '';
		$wanprestasi			= (isset($_REQUEST['wanprestasi'])) ? clean($_REQUEST['wanprestasi']) : '';
		$undangan_pembatalan	= (isset($_REQUEST['undangan_pembatalan'])) ? to_clean($_REQUEST['undangan_pembatalan']) : '';
		$tanggal_efektif_prog	= (isset($_REQUEST['tanggal_efektif_prog'])) ? clean($_REQUEST['tanggal_efektif_prog']) : '';
		$nilai_sisa_tagihan		= (isset($_REQUEST['nilai_sisa_tagihan'])) ? to_number($_REQUEST['nilai_sisa_tagihan']) : '';
		$masa_berlaku_denda		= (isset($_REQUEST['masa_berlaku_denda'])) ? clean($_REQUEST['masa_berlaku_denda']) : '';
		
		$conn->Execute("DELETE FROM CS_PARAMETER_COL");
		
		$query = "
		INSERT INTO CS_PARAMETER_COL (
		NAMA_PT, NAMA_DEP, NAMA_PEJABAT, NAMA_JABATAN, PEMB_JATUH_TEMPO, SOMASI_SATU, SOMASI_DUA, SOMASI_TIGA, 
		WANPRESTASI, UNDANGAN_PEMBATALAN, TANGGAL_EFEKTIF_PROG, NILAI_SISA_TAGIHAN, MASA_BERLAKU_DENDA
		)
		VALUES (
		'$nama_pt', '$nama_dep', '$nama_pejabat', '$nama_jabatan', $pemb_jatuh_tempo, $somasi_satu, $somasi_satu, 
		$somasi_tiga, $wanprestasi, $undangan_pembatalan, CONVERT(DATETIME,'$tanggal_efektif_prog',105), 
		$nilai_sisa_tagihan, $masa_berlaku_denda
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