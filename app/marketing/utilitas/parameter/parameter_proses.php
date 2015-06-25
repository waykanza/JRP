<?php
require_once('../../../../config/config.php');
die_login();
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
		
		$batas_distribusi		= (isset($_REQUEST['batas_distribusi'])) ? clean($_REQUEST['batas_distribusi']) : '';
		$tenggang_distribusi	= (isset($_REQUEST['tenggang_distribusi'])) ? clean($_REQUEST['tenggang_distribusi']) : '';
		
		
		$query = "SELECT * FROM CS_PARAMETER_MARK WHERE BATAS_DISTRIBUSI= '$batas_distribusi' AND TENGGANG_DISTRIBUSI = '$tenggang_distribusi' 
				";
		ex_found($conn->Execute($query)->recordcount(), "Tidak ada data yang berubah.");
		
		$conn->Execute("DELETE FROM CS_PARAMETER_MARK");
		
		$query = "
		INSERT INTO CS_PARAMETER_MARK (
		BATAS_DISTRIBUSI, TENGGANG_DISTRIBUSI
		)
		VALUES (
		'$batas_distribusi', '$tenggang_distribusi'
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