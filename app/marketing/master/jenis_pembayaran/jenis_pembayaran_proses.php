<?php
require_once('../../../../config/config.php');

$msg = '';
$error = FALSE;

$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_bayar = (isset($_REQUEST['kode_bayar'])) ? to_number($_REQUEST['kode_bayar']) : '';
$jenis_bayar = (isset($_REQUEST['jenis_bayar'])) ? clean($_REQUEST['jenis_bayar']) : '';


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_login();
		ex_app('A01');
		ex_mod('PM10');
		$conn = conn($sess_db);
		ex_conn($conn);

		$conn->begintrans(); 
			
		if ($act == 'Tambah') # Proses Tambah
		{
			ex_ha('PM10', 'I');
			
			ex_empty($kode_bayar, 'Kode jenis pembayaran harus diisi.');
			ex_empty($jenis_bayar, 'Nama jenis pembayaran harus diisi.');
		
			$query = "SELECT COUNT(KODE_BAYAR) AS TOTAL FROM JENIS_PEMBAYARAN WHERE KODE_BAYAR = '$kode_bayar'";
			ex_found($conn->Execute($query)->fields['TOTAL'], "Kode jenis pembayaran \"$kode_bayar\" telah terdaftar.");
			
			$query = "INSERT INTO JENIS_PEMBAYARAN (KODE_BAYAR, JENIS_BAYAR)
			VALUES('$kode_bayar', '$jenis_bayar')";
			ex_false($conn->Execute($query), $query);
					
			$msg = "Data jenis pembayaran berhasil ditambahkan.";
		}
		elseif ($act == 'Ubah') # Proses Ubah
		{
			ex_ha('PM10', 'U');
			
			ex_empty($kode_bayar, 'Kode pembayaran harus diisi.');
			ex_empty($jenis_bayar, 'Nama jenis pembayaran harus diisi.');
			
			if ($kode_bayar != $id)
			{
				$query = "SELECT COUNT(kode_bayar) AS TOTAL FROM JENIS_PEMBAYARAN WHERE KODE_BAYAR = '$kode_bayar'";
				ex_found($conn->Execute($query)->fields['TOTAL'], "Kode jenis pembayaran \"$kode_bayar\" telah terdaftar.");
			}
			
			$query = "SELECT * FROM JENIS_PEMBAYARAN WHERE KODE_BAYAR = '$kode_bayar' AND JENIS_BAYAR = '$jenis_bayar'";
			ex_found($conn->Execute($query)->recordcount(), "Tidak ada data yang berubah.");
			
			$query = "
			UPDATE JENIS_PEMBAYARAN 
			SET KODE_BAYAR = '$kode_bayar',
				JENIS_BAYAR = '$jenis_bayar'
			WHERE
				KODE_BAYAR = '$id'
			";
			ex_false($conn->Execute($query), $query);
					
			$msg = 'Data jenis pembayaran berhasil diubah.';
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
die_mod('PM10');
$conn = conn($sess_db);
die_conn($conn);
	
if ($act == 'Tambah')
{
	$obj = $conn->Execute("SELECT MAX(KODE_BAYAR) AS MAX_KODE FROM JENIS_PEMBAYARAN");
	$kode_bayar	= 1 + $obj->fields['MAX_KODE'];
}

if ($act == 'Ubah')
{
	$obj = $conn->Execute("SELECT * FROM JENIS_PEMBAYARAN WHERE KODE_BAYAR = '$id'");
	$kode_bayar	= $obj->fields['KODE_BAYAR'];
	$jenis_bayar	= $obj->fields['JENIS_BAYAR'];
}
?>