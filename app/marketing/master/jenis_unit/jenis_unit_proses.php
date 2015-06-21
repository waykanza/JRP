<?php
require_once('../../../../config/config.php');

$msg = '';
$error = FALSE;

$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_unit = (isset($_REQUEST['kode_unit'])) ? to_number($_REQUEST['kode_unit']) : '';
$jenis_unit = (isset($_REQUEST['jenis_unit'])) ? clean($_REQUEST['jenis_unit']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		ex_app('A01');
		ex_mod('PM04');
		$conn = conn($sess_db);
		ex_conn($conn);

		$conn->begintrans(); 
			
		if ($act == 'Tambah') # Proses Tambah
		{
			ex_ha('PM04', 'I');
			
			ex_empty($kode_unit, 'Kode jenis unit harus diisi.');
			ex_empty($jenis_unit, 'Nama jenis unit harus diisi.');
		
			$query = "SELECT COUNT(KODE_UNIT) AS TOTAL FROM JENIS_UNIT WHERE KODE_UNIT = '$kode_unit'";
			ex_found($conn->Execute($query)->fields['TOTAL'], "Kode jenis unit \"$kode_unit\" telah terdaftar.");
			
			$query = "INSERT INTO JENIS_UNIT (KODE_UNIT, JENIS_UNIT)
			VALUES('$kode_unit', '$jenis_unit')";
			ex_false($conn->Execute($query), $query);
					
			$msg = "Jenis unit \"$jenis_unit\" berhasil ditambahkan.";
		}
		elseif ($act == 'Ubah') # Proses Ubah
		{
			ex_ha('PM04', 'U');
			
			ex_empty($kode_unit, 'Kode jenis unit harus diisi.');
			ex_empty($jenis_unit, 'Nama jenis unit harus diisi.');
			
			if ($kode_unit != $id)
			{
				$query = "SELECT COUNT(KODE_UNIT) AS TOTAL FROM JENIS_UNIT WHERE KODE_UNIT = '$kode_unit'";
				ex_found($conn->Execute($query)->fields['TOTAL'], "Kode jenis unit \"$kode_unit\" telah terdaftar.");
			}
					
			$query = "SELECT * FROM JENIS_UNIT WHERE KODE_UNIT = '$kode_unit' AND JENIS_UNIT = '$jenis_unit'";
			ex_found($conn->Execute($query)->recordcount(), "Tidak ada data yang berubah.");		
					
			$query = "
			UPDATE JENIS_UNIT 
			SET KODE_UNIT = '$kode_unit',
				JENIS_UNIT = '$jenis_unit'
			WHERE
				KODE_UNIT = '$id'
			";
			ex_false($conn->Execute($query), $query);
			
			$msg = 'Data jenis unit berhasil diubah.';
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
die_app('A01');
die_mod('PM04');
$conn = conn($sess_db);
die_conn($conn);

if ($act == 'Tambah')
{
	$obj = $conn->Execute("SELECT MAX(KODE_UNIT) AS MAX_KODE FROM JENIS_UNIT");
	$kode_unit		= 1 + $obj->fields['MAX_KODE'];
}

	
if ($act == 'Ubah')
{
	$obj = $conn->Execute("SELECT * FROM JENIS_UNIT WHERE KODE_UNIT = '$id'");
	$kode_unit	= $obj->fields['KODE_UNIT'];
	$jenis_unit	= $obj->fields['JENIS_UNIT'];
}
?>