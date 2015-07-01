<?php
require_once('../../../../../config/config.php');

$msg = '';
$error = FALSE;

$act 		= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id 		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
$user_id 	= (isset($_REQUEST['user_id'])) ? clean($_REQUEST['user_id']) : '';
$app_id 	= (isset($_REQUEST['app_id'])) ? clean($_REQUEST['app_id']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		//ex_app('A');
		ex_mod('A04');
		$conn = conn($sess_db);
		ex_conn($conn);

		$conn->begintrans(); 
			
		if ($act == 'Simpan') # Proses Tambah
		{
			ex_ha('A04', 'I');

			$act = array();
			$cb_data = $_REQUEST['cb_data'];	
			
			foreach ($cb_data as $id)
			{			
				$obj = $conn->Execute("SELECT COUNT(MODUL_ID) AS TOTAL FROM APPLICATION_RIGHTS WHERE MODUL_ID = '$id' AND USER_ID = '$user_id'");
				$total	= $obj->fields['TOTAL'];
				if($total == 0)
				{
					if ($conn->Execute("INSERT INTO APPLICATION_RIGHTS(USER_ID, MODUL_ID, R_RONLY, R_EDIT, R_INSERT, R_DELETE)
						VALUES('$user_id','$id',NULL,NULL,NULL,NULL)")) {
						$act[] = $id;
					} else {
						$error = TRUE;
					}
				}
			}
			
			$msg = ($error) ? 'Sebagian data gagal disimpan.' : 'Data berhasil disimpan.';
		}
				
		$conn->committrans(); 
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$error = TRUE;
		if ($conn) { $conn->rollbacktrans(); } 
	}

	close($conn);
	$json = array('act' => $act, 'error'=> $error, 'msg' => $msg);
	echo json_encode($json);
	exit;
}

die_login();
//die_app('A');
die_mod('A04');
$conn = conn($sess_db);
die_conn($conn);

?>