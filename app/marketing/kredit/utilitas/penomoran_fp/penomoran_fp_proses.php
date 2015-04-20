<?php
require_once('../../../../../config/config.php');

$msg 	= '';
$error	= FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$pejabat	= (isset($_REQUEST['pejabat'])) ? clean($_REQUEST['pejabat']) : '';
$jabatan	= (isset($_REQUEST['jabatan'])) ? clean($_REQUEST['jabatan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		//ex_app('');
		//ex_mod('');
		$conn = conn($sess_db);
		ex_conn($conn);

		$conn->begintrans(); 
			
		if ($act == 'Penomoran') # Proses Pembatalan
		{
			//ex_ha('', 'U');
			$query = "SELECT * FROM FAKTUR_PAJAK WHERE NO_KWITANSI = '$id' AND PEJABAT = '$pejabat' AND JABATAN = '$jabatan'";
			ex_found($conn->Execute($query)->recordcount(), "Tidak ada data yang berubah.");
			
			$query = "
			UPDATE FAKTUR_PAJAK 
			SET
				PEJABAT = '$pejabat', 
				JABATAN = '$jabatan'
			WHERE
				NO_KWITANSI = '$id'
			";			
			ex_false($conn->execute($query), $query);
			
			$msg = "Nama Pejabat berhasil disimpan.";
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
//die_app('');
//die_mod('');
$conn = conn($sess_db);
die_conn($conn);

if ($act == 'Penomoran')
{
	$query = "SELECT * FROM FAKTUR_PAJAK WHERE NO_KWITANSI = '$id'";
	$obj = $conn->execute($query);
	$no_kuitansi	= $obj->fields['NO_KWITANSI'];
	$blok_nomor = $obj->fields['KODE_BLOK'];
	$nama_pembeli = $obj->fields['NAMA'];
	$pejabat = $obj->fields['PEJABAT'];
	$jabatan = $obj->fields['JABATAN'];
	
	/*
	$query = "SELECT * FROM CS_REGISTER_CUSTOMER_SERVICE";
	$obj = $conn->execute($query);
	$reg_faktur = $obj->fields['NO_REG_FAKTUR_PAJAK']; 
	$no_faktur = $obj->fields['NO_FAKTUR_PAJAK_STANDAR']+1; */
}
?>